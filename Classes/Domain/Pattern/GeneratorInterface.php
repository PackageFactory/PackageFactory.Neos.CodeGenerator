<?php declare(strict_types=1);
namespace PackageFactory\Neos\CodeGenerator\Domain\Pattern;

/*
 * This file is part of the PackageFactory.Neos.CodeGenerator package
 */

use Neos\Flow\Package\FlowPackageInterface;

interface GeneratorInterface
{
    /**
     * @param FlowPackageInterface $flowPackage
     * @param array<string> $arguments
     * @return void
     */
    public function generate(FlowPackageInterface $flowPackage, array $arguments): void;
}
