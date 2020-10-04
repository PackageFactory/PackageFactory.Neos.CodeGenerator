<?php declare(strict_types=1);
namespace PackageFactory\Neos\CodeGenerator\Pattern\Domain;

/*
 * This file is part of the PackageFactory.Neos.CodeGenerator package
 */

use Neos\Flow\Annotations as Flow;
use PackageFactory\Neos\CodeGenerator\Domain\Code\Php\PhpClass\PhpClassName;
use PackageFactory\Neos\CodeGenerator\Domain\Code\Php\PhpNamespace\PhpNamespace;
use PackageFactory\Neos\CodeGenerator\Domain\Files\Path;
use PackageFactory\Neos\CodeGenerator\Domain\Flow\DistributionPackageInterface;
use PackageFactory\Neos\CodeGenerator\Domain\Input\TypeDescription\TypeDescriptionTemplateInterface;

/**
 * @Flow\Proxy(false)
 */
final class Domain implements TypeDescriptionTemplateInterface
{
    /**
     * @var DistributionPackageInterface
     */
    private $distributionPackage;

    /**
     * @param DistributionPackageInterface $distributionPackage
     */
    private function __construct(DistributionPackageInterface $distributionPackage)
    {
        $this->distributionPackage = $distributionPackage;
    }

    /**
     * @param DistributionPackageInterface $distributionPackage
     * @return self
     */
    public static function fromDistributionPackage(DistributionPackageInterface $distributionPackage): self
    {
        return new self($distributionPackage);
    }

    /**
     * @return PhpNamespace
     */
    public function getPhpNamespace(): PhpNamespace
    {
        return $this->distributionPackage->getPackageKey()->asPhpNamespace()->append('Domain');
    }

    /**
     * @param PhpClassName $className
     * @return Path
     */
    public function getPhpFilePathForClassName(PhpClassName $className): Path
    {
        return $this->distributionPackage->getPhpFilePathForClassName($className);
    }

    /**
     * @param string $package
     * @param string $namespace
     * @return string
     */
    public function resolvePackageReference(string $package, string $namespace): string
    {
        return '\\' . $package . '\\Domain\\' . $namespace;
    }

    /**
     * @param string $namespace
     * @return string
     */
    public function resolveRelativeNamespace(string $namespace): string
    {
        return $this->getPhpNamespace()->append($namespace)->asString();
    }
}
