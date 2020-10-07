<?php declare(strict_types=1);
namespace PackageFactory\Neos\CodeGenerator\Domain\Code\Php\Type;

/*
 * This file is part of the PackageFactory.Neos.CodeGenerator package
 */

interface TypeInterface
{
    /**
     * @return string
     */
    public function getName(): string;

    /**
     * @return string
     */
    public function getNativeName(): string;

    /**
     * @param string $nativeName
     * @return TypeInterface
     */
    public function withNativeName(string $nativeName): TypeInterface;

    /**
     * @param string $alias
     * @return TypeInterface
     */
    public function withAlias(string $alias): TypeInterface;

    /**
     * @return string
     */
    public function getPhpDocName(): string;

    /**
     * @return boolean
     */
    public function isNullable(): bool;

    /**
     * @return TypeInterface
     */
    public function asNullable(): TypeInterface;

    /**
     * @return string
     */
    public function asDocBlockString(): string;

    /**
     * @return string
     */
    public function asPhpTypeHint(): string;

    /**
     * @param TypeInterface $other
     * @return boolean
     */
    public function equals(TypeInterface $other): bool;
}
