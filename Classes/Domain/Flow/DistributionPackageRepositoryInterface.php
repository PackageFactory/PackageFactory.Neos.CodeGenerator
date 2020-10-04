<?php declare(strict_types=1);
namespace PackageFactory\Neos\CodeGenerator\Domain\Flow;

/*
 * This file is part of the PackageFactory.Neos.CodeGenerator package
 */

interface DistributionPackageRepositoryInterface
{
    /**
     * @return \Iterator<string, DistributionPackageInterface>
     */
    public function findAll(): \Iterator;

    /**
     * @param PackageKey $packageKey
     * @return null|DistributionPackageInterface
     */
    public function findOneByPackageKey(PackageKey $packageKey): ?DistributionPackageInterface;

    /**
     * @return null|DistributionPackageInterface
     */
    public function findDefault(): ?DistributionPackageInterface;

    /**
     * @return null|DistributionPackageInterface
     */
    public function findFirstAvailable(): ?DistributionPackageInterface;

    /**
     * @param DistributionPackageInterface $distributionPackage
     * @return void
     */
    public function add(DistributionPackageInterface $distributionPackage): void;
}
