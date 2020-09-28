<?php declare(strict_types=1);
namespace PackageFactory\Neos\CodeGenerator\Domain\Code\Php\Property;

/*
 * This file is part of the PackageFactory.Neos.CodeGenerator package
 */

use PackageFactory\Neos\CodeGenerator\Domain\Code\Php\Type\TypeInterface;

interface PropertyInterface
{
    /**
     * @return TypeInterface
     */
    public function getType(): TypeInterface;

    /**
     * @return string
     */
    public function getName(): string;
}
