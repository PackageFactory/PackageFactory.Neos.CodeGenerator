<?php declare(strict_types=1);
namespace PackageFactory\Neos\CodeGenerator\Domain\Flow;

/*
 * This file is part of the PackageFactory.Neos.CodeGenerator package
 */

interface DistributionPackageResolverInterface
{
    /**
     * @param null|string $input
     * @return DistributionPackageInterface
     */
    public function resolve(?string $input): DistributionPackageInterface;
}
