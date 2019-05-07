<?php
/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2018 Heimrich & Hannot GmbH
 *
 * @author  Thomas Körner <t.koerner@heimrich-hannot.de>
 * @license http://www.gnu.org/licences/lgpl-3.0.html LGPL
 */


namespace HeimrichHannot\ContaoExporterBundle\Manager;


use HeimrichHannot\ContaoExporterBundle\Exporter\ExporterInterface;
use Psr\Container\ContainerInterface;
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
     * @var ContainerInterface
     */
    private $container;
    private $isFileTypesInitialized = false;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * Add an exporter to the registry.
     *
     * @param ExporterInterface $exporter
     */
    public function addExporter(ExporterInterface $exporter, string $className)
    {
        $this->exporter[$className] = $exporter;
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
     */
    protected function initializeFileTypeLists(bool $useCache)
    {
        if ($this->isFileTypesInitialized)
        {
            return;
        }

        foreach ($this->exporter as $exporterName => $exporter)
        {
            foreach ($exporter->getSupportedFileTypes() as $fileType)
            {
                $this->exporterFileTypes[$fileType][] = $exporterName;
            }
        }
        $this->isFileTypesInitialized = true;
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