<?php
/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2018 Heimrich & Hannot GmbH
 *
 * @author  Thomas Körner <t.koerner@heimrich-hannot.de>
 * @license http://www.gnu.org/licences/lgpl-3.0.html LGPL
 */


namespace HeimrichHannot\ContaoExporterBundle\ContaoManager;


use HeimrichHannot\ContaoExporterBundle\Exporter\ExporterInterface;
use Symfony\Component\Cache\Simple\FilesystemCache;

class ExporterManager
{
    /**
     * @var ExporterInterface[]|array
     */
    protected $exporter = [];
    protected $exporterFileTypes = [];
    protected $exporterClassesFileTypes = [];

    /**
     * Add an exporter to the registry.
     *
     * @param ExporterInterface $exporter
     */
    public function addExporter(ExporterInterface $exporter) {
        $this->exporter[get_class($exporter)] = $exporter;
    }

    /**
     * Returns a list of exporter classes (full qualified namespaces).
     *
     * @param string $fileType
     * @param bool $useCache
     * @return array|string[]
     */
    public function getExporterByFileType(string $fileType, bool $useCache = true)
    {
        $this->initializeFileTypeLists($useCache);
        return $this->exporterFileTypes[$fileType] ?: [];
    }

    /**
     * Get exporter by class name
     *
     * @param string $className
     * @return bool|ExporterInterface|mixed
     */
    public function getExporterByClassName(string $className) {
        if (array_key_exists($className, $this->exporter))
        {
            return $this->exporter[$className];
        }
        return false;
    }

    /**
     * @param bool $useCache
     * @return string
     */
    protected function initializeFileTypeLists(bool $useCache)
    {
        $cacheKey = 'exporter.filetypes';
        if (empty($this->exporterFileTypes))
        {
            if ($useCache)
            {
                $cache = new FilesystemCache('huh.exporter');
            }

            if ($useCache && $cache->has($cacheKey))
            {
                $this->exporterFileTypes = $cache->get($cacheKey);
            } else
            {
                foreach ($this->exporter as $exporter)
                {
                    foreach ($exporter->getSupportedFileTypes() as $fileType)
                    {
                        $this->exporterFileTypes[$fileType][] = get_class($exporter);
                    }
                }
                if ($useCache)
                {
                    $cache->set($cacheKey, $this->exporterFileTypes);
                }
            }
        }
    }

    /**
     * @return ExporterInterface[]|array
     */
    public function getExporter(): array
    {
        return $this->exporter;
    }

    /**
     * Returns the list of file types with supporting exporter classes (full qualified namespaces)
     * Structure: ['fileType' => ['Bundle\Namespace\ExporterClass1', 'Bundle\Namespace\ExporterClass2'], ...]
     *
     * @param bool $useCache
     * @return array
     */
    public function getExporterFileTypes(bool $useCache = true): array
    {
        $this->initializeFileTypeLists($useCache);
        return $this->exporterFileTypes;
    }
}