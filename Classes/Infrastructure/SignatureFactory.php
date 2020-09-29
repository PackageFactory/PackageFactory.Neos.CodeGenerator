<?php declare(strict_types=1);
namespace PackageFactory\Neos\CodeGenerator\Infrastructure;

/*
 * This file is part of the PackageFactory.Neos.CodeGenerator package
 */

use Neos\Flow\Annotations as Flow;
use Neos\Flow\Package\FlowPackageInterface;
use PackageFactory\Neos\CodeGenerator\Domain\Code\Common\Signature\Signature;
use PackageFactory\Neos\CodeGenerator\Domain\Code\Common\Signature\SignatureFactoryInterface;
use PackageFactory\Neos\CodeGenerator\Domain\Code\Common\Signature\SignatureInterface;

/**
 * @Flow\Scope("singleton")
 */
final class SignatureFactory implements SignatureFactoryInterface
{
    /**
     * @param FlowPackageInterface $flowPackage
     * @return SignatureInterface
     */
    public function forFlowPackage(FlowPackageInterface $flowPackage): SignatureInterface
    {
        return new Signature($flowPackage->getPackageKey());
    }
}
