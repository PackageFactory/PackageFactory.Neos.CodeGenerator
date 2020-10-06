<?php declare(strict_types=1);
namespace PackageFactory\Neos\CodeGenerator\Domain\Flow;

/*
 * This file is part of the PackageFactory.Neos.CodeGenerator package
 */

use Neos\Flow\Annotations as Flow;
use Neos\Utility\Files;
use PackageFactory\Neos\CodeGenerator\Domain\Files\Path;

/**
 * @Flow\Scope("singleton")
 */
final class DistributionPackageFactory implements DistributionPackageFactoryInterface
{
    /**
     * @Flow\InjectConfiguration(path="packageResolution.pathToDistributionPackages")
     * @var string
     */
    protected $pathToDistributionPackages;

    /**
     * @param string $packageKeyString
     * @return null|DistributionPackageInterface
     */
    public function fromPackageKeyString(string $packageKeyString): ?DistributionPackageInterface
    {
        if (PackageKey::isValid($packageKeyString)) {
            return $this->fromPackageKey(PackageKey::fromString($packageKeyString));
        }

        return null;
    }

    /**
     * @param PackageKey $packageKey
     * @return null|DistributionPackageInterface
     */
    public function fromPackageKey(PackageKey $packageKey): ?DistributionPackageInterface
    {
        $pathToComposerJson = Files::concatenatePaths([
            $this->pathToDistributionPackages,
            $packageKey->asString(),
            'composer.json'
        ]);

        if (is_file($pathToComposerJson) && is_readable($pathToComposerJson)) {
            if ($composerJsonAsString = file_get_contents($pathToComposerJson)) {
                if ($composerJson = json_decode($composerJsonAsString, true)) {
                    return new DistributionPackage(
                        $packageKey,
                        Path::fromString(dirname($pathToComposerJson)),
                        $composerJson
                    );
                }
            }
        }

        return null;
    }
}
