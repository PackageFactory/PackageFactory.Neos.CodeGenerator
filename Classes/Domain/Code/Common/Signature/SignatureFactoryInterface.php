<?php declare(strict_types=1);
namespace PackageFactory\Neos\CodeGenerator\Domain\Code\Common\Signature;

/*
 * This file is part of the PackageFactory.Neos.CodeGenerator package
 */

use PackageFactory\Neos\CodeGenerator\Domain\Flow\DistributionPackageInterface;

interface SignatureFactoryInterface
{
    /**
     * @param DistributionPackageInterface $distributionPackage
     * @return SignatureInterface
     */
    public function forDistributionPackage(DistributionPackageInterface $distributionPackage): SignatureInterface;
}
