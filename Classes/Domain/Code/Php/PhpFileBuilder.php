<?php declare(strict_types=1);
namespace PackageFactory\Neos\CodeGenerator\Domain\Code\Php;

/*
 * This file is part of the PackageFactory.Neos.CodeGenerator package
 */

use Neos\Flow\Annotations as Flow;
use PackageFactory\Neos\CodeGenerator\Domain\Code\Common\Signature\SignatureInterface;
use PackageFactory\Neos\CodeGenerator\Domain\Code\Php\PhpNamespace\PhpNamespace;
use PackageFactory\Neos\CodeGenerator\Domain\Code\Php\Import\ImportCollectionBuilder;
use PackageFactory\Neos\CodeGenerator\Domain\Code\Php\PhpClass\PhpClassName;
use PackageFactory\Neos\CodeGenerator\Domain\Files\Path;

/**
 * @Flow\Proxy(false)
 */
final class PhpFileBuilder
{
    /**
     * @var null|Path
     */
    private $path;

    /**
     * @var null|PhpNamespace
     */
    private $namespace;

    /**
     * @var null|SignatureInterface
     */
    private $signature;

    /**
     * @var ImportCollectionBuilder
     */
    private $importCollectionBuilder;

    /**
     * @var null|string
     */
    private $code;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->importCollectionBuilder = new ImportCollectionBuilder();
    }

    /**
     * @param Path $path
     * @return self
     */
    public function setPath(Path $path): self
    {
        $this->path = $path;
        return $this;
    }

    /**
     * @param PhpNamespace $namespace
     * @return self
     */
    public function setNamespace(PhpNamespace $namespace): self
    {
        $this->namespace = $namespace;
        return $this;
    }

    /**
     * @param PhpClassName $className
     * @return self
     */
    public function setNamespaceFromClassName(PhpClassName $className): self
    {
        if ($namespace = $className->asNamespace()->getParentNamespace()) {
            $this->namespace = $namespace;
        }

        return $this;
    }

    /**
     * @param SignatureInterface $signature
     * @return self
     */
    public function setSignature(SignatureInterface $signature): self
    {
        $this->signature = $signature;
        return $this;
    }

    /**
     * @return ImportCollectionBuilder
     */
    public function getImportCollectionBuilder(): ImportCollectionBuilder
    {
        return $this->importCollectionBuilder;
    }

    /**
     * @param string $code
     * @return self
     */
    public function setCode(string $code): self
    {
        $this->code = $code;
        return $this;
    }

    /**
     * @return PhpFile
     */
    public function build(): PhpFile
    {
        if (!$this->path) {
            throw new \LogicException('Could not build PhpFile, because of missing path.');
        }

        return new PhpFile(
            $this->path,
            $this->namespace,
            $this->signature,
            $this->importCollectionBuilder->build(),
            $this->code
        );
    }
}
