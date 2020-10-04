<?php declare(strict_types=1);
namespace PackageFactory\Neos\CodeGenerator\Domain\Flow;

/*
 * This file is part of the PackageFactory.Neos.CodeGenerator package
 */

interface DistributionPackageFactoryInterface
{
    /**
     * @param string $packageKeyString
     * @return null|DistributionPackageInterface
     */
    public function fromPackageKeyString(string $packageKeyString): ?DistributionPackageInterface;

    /**
     * @param PackageKey $packagekey
     * @return null|DistributionPackageInterface
     */
    public function fromPackageKey(PackageKey $packagekey): ?DistributionPackageInterface;
}
