<?php declare(strict_types=1);
namespace PackageFactory\Neos\CodeGenerator\Domain\Code\Common\Signature;

use Neos\Flow\Package\FlowPackageInterface;

/*
 * This file is part of the PackageFactory.Neos.CodeGenerator package
 */

interface SignatureFactoryInterface
{
    public function forFlowPackage(FlowPackageInterface $flowPackage): SignatureInterface;
}
