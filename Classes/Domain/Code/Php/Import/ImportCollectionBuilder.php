<?php declare(strict_types=1);
namespace PackageFactory\Neos\CodeGenerator\Domain\Code\Php\Import;

/*
 * This file is part of the PackageFactory.Neos.CodeGenerator package
 */

use Neos\Flow\Annotations as Flow;

/**
 * @Flow\Proxy(false)
 */
final class ImportCollectionBuilder
{
    /**
     * @var array<string, ImportInterface>
     */
    private $importsByFullyQualifiedName = [];

    /**
     * @var array<string, ImportInterface>
     */
    private $importsByName = [];

    /**
     * @param ImportInterface $import
     * @return ImportInterface
     */
    public function resolvePotentialNamingConflictForImport(ImportInterface $import): ImportInterface
    {
        if (array_key_exists($import->getFullyQualifiedName(), $this->importsByFullyQualifiedName)) {
            throw new \DomainException('Duplicate import: ' . $import->getFullyQualifiedName());
        } elseif ($import->getAlias() !== null) {
            throw new \DomainException('Unable to resolve naming conflict for import with alias: ' . $import->getAlias());
        } elseif (array_key_exists($import->getName(), $this->importsByName)) {
            $fullyQualifiedName = $import->getFullyQualifiedName();
            $segments = explode('\\', $fullyQualifiedName);

            if (count($segments) > 1) {
                $alias = '';
                while ($lastSegment = array_pop($segments)) {
                    $alias = $lastSegment . $alias;

                    if (!array_key_exists($alias, $this->importsByName)) {
                        return $import->withAlias($alias);
                    }
                }
            }

            throw new \DomainException('Naming conflict cannot be resolved: ' . $import->getFullyQualifiedName());
        } else {
            return $import;
        }
    }

    /**
     * @param ImportInterface $import
     * @return self
     */
    public function addImport(ImportInterface $import): self
    {
        if (array_key_exists($import->getFullyQualifiedName(), $this->importsByFullyQualifiedName)) {
            throw new \DomainException('Duplicate import: ' . $import->getFullyQualifiedName());
        }

        if (array_key_exists($import->getName(), $this->importsByName)) {
            throw new \DomainException('Import naming conflict: ' . $import->getName());
        }

        $this->importsByFullyQualifiedName[$import->getFullyQualifiedName()] = $import;
        $this->importsByName[$import->getName()] = $import;

        return $this;
    }

    /**
     * @param ImportCollectionInterface $importCollection
     * @return self
     */
    public function addImportCollection(ImportCollectionInterface $importCollection): self
    {
        foreach ($importCollection as $import) {
            $this->addImport($this->resolvePotentialNamingConflictForImport($import));
        }

        return $this;
    }

    /**
     * @return ImportCollectionInterface
     */
    public function build(): ImportCollectionInterface
    {
        return new ImportCollection(array_values($this->importsByName));
    }
}
