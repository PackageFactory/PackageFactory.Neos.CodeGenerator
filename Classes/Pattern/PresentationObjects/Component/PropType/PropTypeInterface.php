<?php declare(strict_types=1);
namespace PackageFactory\Neos\CodeGenerator\Pattern\PresentationObjects\Component\PropType;

/*
 * This file is part of the PackageFactory.Neos.CodeGenerator package
 */

interface PropTypeInterface
{
    /**
     * @return string
     */
    public function asExampleValue(): string;

    /**
     * @return string
     */
    public function asDummyAfxMarkup(): string;
}
