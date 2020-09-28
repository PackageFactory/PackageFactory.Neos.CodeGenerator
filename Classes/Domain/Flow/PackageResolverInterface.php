<?php declare(strict_types=1);
namespace PackageFactory\Neos\CodeGenerator\Domain\Flow;

use Neos\Flow\Package\FlowPackageInterface;

/*
 * This file is part of the PackageFactory.Neos.CodeGenerator package
 */

interface PackageResolverInterface
{
    /**
     * @param null|string $input
     * @return FlowPackageInterface
     */
    public function resolve(?string $input): FlowPackageInterface;
}
