<?php declare(strict_types=1);
namespace PackageFactory\Neos\CodeGenerator\Pattern\Presentation\Component\Prop;

/*
 * This file is part of the PackageFactory.Neos.CodeGenerator package
 */

interface PropInterface
{
    /**
     * @return string
     */
    public function asExampleValueAssignment(): string;

    /**
     * @return string
     */
    public function asDummyAfxMarkup(): string;
}
