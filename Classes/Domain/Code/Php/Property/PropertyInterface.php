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

    /**
     * @return string
     */
    public function asClassPropertyDeclaration(): string;

    /**
     * @return string
     */
    public function asFunctionParameterDeclaration(): string;

    /**
     * @return string
     */
    public function asDocBlockString(): string;

    /**
     * @return string
     */
    public function asConstructorAssignment(): string;

    /**
     * @return string
     */
    public function asGetterSignature(): string;

    /**
     * @return string
     */
    public function asGetterImplementation(): string;
}
