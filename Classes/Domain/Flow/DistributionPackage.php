<?php declare(strict_types=1);
namespace PackageFactory\Neos\CodeGenerator\Domain\Flow;

/*
 * This file is part of the PackageFactory.Neos.CodeGenerator package
 */

use Neos\Flow\Annotations as Flow;
use PackageFactory\Neos\CodeGenerator\Domain\Code\Php\PhpClass\PhpClassName;
use PackageFactory\Neos\CodeGenerator\Domain\Code\Php\PhpNamespace\PhpNamespace;
use PackageFactory\Neos\CodeGenerator\Domain\Files\Path;

/**
 * @Flow\Proxy(false)
 */
final class DistributionPackage implements DistributionPackageInterface
{
    /**
     * @var PackageKey
     */
    private $packageKey;

    /**
     * @var Path
     */
    private $path;

    /**
     * @var array<mixed>
     */
    private $composerJson;

    /**
     * @param PackageKey $packageKey
     * @param Path $path
     * @param array<mixed> $composerJson
     */
    public function __construct(
        PackageKey $packageKey,
        Path $path,
        array $composerJson
    ) {
        $this->packageKey = $packageKey;
        $this->path = $path;
        $this->composerJson = $composerJson;
    }

    /**
     * @return PackageKey
     */
    public function getPackageKey(): PackageKey
    {
        return $this->packageKey;
    }

    /**
     * @return Path
     */
    public function getPackagePath(): Path
    {
        return $this->path;
    }

    /**
     * @return Path
     */
    public function getPhpFilePathForClassName(PhpClassName $className): Path
    {
        if (isset($this->composerJson['autoload']['psr-4'])) {
            foreach ($this->composerJson['autoload']['psr-4'] as $namespaceAsString => $pathAsString) {
                $psr4Namespace = PhpNamespace::fromString(rtrim($namespaceAsString, '\\'));

                if ($className->asNamespace()->isDescendantOf($psr4Namespace)) {
                    return $this->path
                        ->appendString($pathAsString)
                        ->append($className->asNamespace()->truncateAscendant($psr4Namespace)->asPath())
                        ->withExtension('php');
                }
            }
        }

        throw new \DomainException('Could not find a suitable autload configuration for package "' . $this->packageKey->asString() . '".');
    }
}
