<?php
/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2018 Heimrich & Hannot GmbH
 *
 * @author  Thomas Körner <t.koerner@heimrich-hannot.de>
 * @license http://www.gnu.org/licences/lgpl-3.0.html LGPL
 */


namespace HeimrichHannot\ContaoExporterBundle\Exporter;


use Contao\CoreBundle\Framework\ContaoFrameworkInterface;
use Contao\Database;
use Contao\FilesModel;
use Contao\FrontendUser;
use Contao\Input;
use Contao\Model;
use HeimrichHannot\ContaoExporterBundle\Event\BeforeExportEvent;
use HeimrichHannot\ContaoExporterBundle\Event\BeforeBuildQueryEvent;
use HeimrichHannot\ContaoExporterBundle\Model\ExporterModel;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\EventDispatcher\EventDispatcher;

abstract class AbstractExporter implements ExporterInterface
{
    const TARGET_DOWNLOAD = 'download';
    const TARGET_FILE     = 'file';

    const TYPE_ITEM = 'item';
    const TYPE_LIST = 'list';

    const EXPORTER_RAW_FIELD_SUFFIX = 'ERawE';

    /**
     * @var string
     */
    protected $tempFolderPath = 'system/tmp';

    /**
     * @var ExporterModel
     */
    protected $config;
    /**
     * @var ContaoFrameworkInterface
     */
    protected $framework;
    /**
     * @var EventDispatcher
     */
    protected $dispatcher;
    /**
     * @var ContainerInterface
     */
    protected $container;

    public function __construct(ContainerInterface $container, ContaoFrameworkInterface $framework, EventDispatcher $dispatcher)
    {
        $this->container     = $container;
        $this->framework     = $framework;
        $this->dispatcher    = $dispatcher;
    }

    abstract public static function getName(): string;

    abstract public static function getSupportedFileTypes(): array;

    abstract public static function getSupportedExportTypes(): array;

    /**
     * @param Model|null $entity
     * @param ExporterModel|null $config
     * @param array $fields
     * @return bool
     * @throws \Exception
     */
    public function export(ExporterModel $config = null, $entity = null, array $fields = []): bool
    {
        if ($config)
        {
            $this->setConfig($config);
        }
        if (!$this->config)
        {
            throw new \Exception("No configuration found for current export action.");
        }

        if (!$this->hasType($this->config->type))
        {
            if ($this->container->getParameter('kernel.environment') != 'prod') {
                throw new \Exception("Export type ".$this->config->type." is not supported by export class ".static::class
                    .' for export configuration '.$this->config->title.' (ID: '.$this->config->id.')');
            }
            return false;
        }

        $fileName = $this->buildFileName($entity);

        if ($this->config->target == static::TARGET_FILE)
        {
            $fileDir = $this->buildFileDir($entity);
        }

        $event = $this->dispatcher->dispatch(BeforeExportEvent::NAME, new BeforeExportEvent($entity, $fields, $fileDir, $fileName, $config, $this));

        $result = $this->doExport($event->getEntity(), $event->getFields());

        switch ($this->config->target)
        {
            case static::TARGET_FILE:
                return $this->exportToFile($result, $event->getFileDir(), $event->getFileName());
            case static::TARGET_DOWNLOAD:
                return $this->exportToDownload($result, $event->getFileDir(), $event->getFileName());
        }
    }

    /**
     * Concrete exporter implementation
     *
     * @param $entity
     * @param $fields
     * @param $target
     * @return mixed
     */
    abstract protected function doExport($entity, array $fields);

    abstract public function exportToDownload($result, string $fileDir, string $fileName);

    abstract public function exportToFile($result, string $fileDir, string $fileName);

    /**
     *
     * Return the file dir
     *
     * @return string
     * @throws \Exception
     */
    protected function buildFileDir(Model $entity)
    {
        if ($this->config->fileDir && $objFolder = $this->framework->getAdapter(FilesModel::class)->findByUuid($this->fileDir))
        {
            $objMember = FrontendUser::getInstance();
            $strDir    = $objFolder->path;

            if ($this->config->useHomeDir && FE_USER_LOGGED_IN && $objMember->assignDir && $objMember->homeDir)
            {
                $strDir = $this->container->get('huh.utils.file')->getPathFromUuid($objMember->homeDir);
            }

            if ($this->container->get('huh.utils.container')->isBundleActive('protected_homedirs'))
            {
                if ($this->config->useProtectedHomeDir && $objMember->assignProtectedDir && $objMember->protectedHomeDir)
                {
                    $strDir = $this->container->get('huh.utils.file')->getPathFromUuid($objMember->protectedHomeDir);
                }
            }

            if ($this->config->fileSubDirName)
            {
                $strDir .= '/' . $this->config->fileSubDirName;
            }

            return $strDir;
        }

        throw new \Exception('No exporter fileDir defined!');
    }

    /**
     * Creates the file name
     *
     * @param Model $entity
     * @return string
     */
    protected function buildFileName(Model $entity)
    {
        $fileName = $this->config->fileName ?: 'export';

        if ($this->config->fileNameAddDatime)
        {
            $fileName = date($this->config->fileNameAddDatimeFormat ?: 'Y-m-d') . '_' . $fileName;
        }

        return $fileName . '.' . $this->config->fileType;
    }

    /**
     * Returns the entites for list export
     *
     * @return Database\Result
     */
    public function getEntities()
    {
        $exportFields = [];
        $dca          = $GLOBALS['TL_DCA'][$this->config->linkedTable];

        foreach (deserialize($this->config->tableFieldsForExport, true) as $field)
        {
            if (strpos($field, static::EXPORTER_RAW_FIELD_SUFFIX) !== false)
            {
                $exportFields[] = str_replace(EXPORTER_RAW_FIELD_SUFFIX, '', $field) . ' AS "' . $field . '"';
            } else
            {
                $exportFields[] = $field . ' AS "' . $field . '"';
            }
        }

        // JOIN
        $joinTables = null;
        if ($this->config->addJoinTables)
        {
            if (($joinExportConfig = ExporterModel::findByPk($this->config->id)) && $joinExportConfig->addJoinTables)
            {
                $joinTables = deserialize($joinExportConfig->joinTables, true);
            }
        }

        // WHERE
        $wheres = [];
        if ($this->config->whereClause)
        {
            $wheres[] = html_entity_decode($this->config->whereClause);
        }

        // limit to archive
        if (TL_MODE == 'BE' && ($this->config->type == static::TYPE_LIST || !$this->config->type))
        {
            $strAct = Input::get('act');
            $intPid = Input::get('id');

            if ($intPid && !$strAct && is_array($dca['fields']) && $dca['config']['ptable'])
            {
                $wheres[] = 'pid = ' . $intPid;
            }
        }


        $event = $this->dispatcher->dispatch(
            BeforeBuildQueryEvent::NAME,
            new BeforeBuildQueryEvent($this->config, $exportFields, $joinTables, $wheres, $this->config->orderBy, $this)
        );


        $query = 'SELECT ' . implode(',', $event->getExportFields()) . ' FROM ' . $this->config->linkedTable;

        if (is_array($event->getJoinTables()))
        {
            foreach ($joinTables as $joinT)
            {
                $query .= ' INNER JOIN ' . $joinT['joinTable'] . ' ON ' . $joinT['joinCondition'];
            }
        }

        if (is_array($wheres = $event->getWheres()))
        {
            $query .= ' WHERE ' . implode(
                    ' AND ',
                    array_map(
                        function ($val) {
                            return '(' . $val . ')';
                        },
                        $wheres
                    )
                );
        }

        // ORDER BY
        if ($event->getOrderBy())
        {
            $query .= ' ORDER BY ' . $event->getOrderBy();
        }

        return $this->framework->createInstance(Database::class)->prepare($query)->execute();
    }

    /**
     * @return ExporterModel
     */
    public function getConfig(): ExporterModel
    {
        return $this->config;
    }

    /**
     * @param ExporterModel $config
     */
    public function setConfig(ExporterModel $config): void
    {
        $this->config = $config;
    }

    public function hasType(string $type): bool
    {
        switch ($type)
        {
            case static::TYPE_LIST:
                return $this instanceof ExportTypeListInterface;
            case static::TYPE_ITEM:
                return $this instanceof ExportTypeItemInterface;
        }
        return false;
    }

    /**
     * @return string
     */
    public function getTempFolderPath(): string
    {
        return $this->tempFolderPath;
    }

    /**
     * @param string $tempFolderPath
     */
    public function setTempFolderPath(string $tempFolderPath): void
    {
        $this->tempFolderPath = $tempFolderPath;
    }
}