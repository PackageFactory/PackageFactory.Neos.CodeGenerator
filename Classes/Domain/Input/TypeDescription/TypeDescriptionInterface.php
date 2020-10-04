<?php declare(strict_types=1);
namespace PackageFactory\Neos\CodeGenerator\Domain\Input\TypeDescription;

/*
 * This file is part of the PackageFactory.Neos.CodeGenerator package
 */

use PackageFactory\Neos\CodeGenerator\Domain\Flow\DistributionPackageNamespace;

interface TypeDescriptionInterface
{
    /**
     * @return string
     */
    public function asString(): string;

    /**
     * @param TypeDescriptionTemplateInterface $template
     * @return TypeDescriptionInterface
     */
    public function withTemplate(TypeDescriptionTemplateInterface $template): TypeDescriptionInterface;
}
