<?php declare(strict_types=1);
namespace PackageFactory\Neos\CodeGenerator\Infrastructure;

/*
 * This file is part of the PackageFactory.Neos.CodeGenerator package
 */

use Neos\Flow\Annotations as Flow;
use PackageFactory\Neos\CodeGenerator\Domain\Code\Common\Signature\Signature;
use PackageFactory\Neos\CodeGenerator\Domain\Code\Common\Signature\SignatureFactoryInterface;
use PackageFactory\Neos\CodeGenerator\Domain\Code\Common\Signature\SignatureInterface;
use PackageFactory\Neos\CodeGenerator\Domain\Flow\DistributionPackageInterface;

/**
 * @Flow\Scope("singleton")
 */
final class SignatureFactory implements SignatureFactoryInterface
{
    /**
     * @param DistributionPackageInterface $distributionPackage
     * @return SignatureInterface
     */
    public function forDistributionPackage(DistributionPackageInterface $distributionPackage): SignatureInterface
    {
        return new Signature($distributionPackage->getPackageKey()->asString());
    }
}
