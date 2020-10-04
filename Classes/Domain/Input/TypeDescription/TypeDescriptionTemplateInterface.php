<?php declare(strict_types=1);
namespace PackageFactory\Neos\CodeGenerator\Domain\Input\TypeDescription;

/*
 * This file is part of the PackageFactory.Neos.CodeGenerator package
 */

interface TypeDescriptionTemplateInterface
{
    /**
     * @param string $package
     * @param string $namespace
     * @return string
     */
    public function resolvePackageReference(string $package, string $namespace): string;

    /**
     * @param string $namespace
     * @return string
     */
    public function resolveRelativeNamespace(string $namespace): string;
}
