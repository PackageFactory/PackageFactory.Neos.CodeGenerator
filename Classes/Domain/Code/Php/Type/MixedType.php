<?php declare(strict_types=1);
namespace PackageFactory\Neos\CodeGenerator\Domain\Code\Php\Type;

/*
 * This file is part of the PackageFactory.Neos.CodeGenerator package
 */

use Neos\Flow\Annotations as Flow;

/**
 * @Flow\Proxy(false)
 */
final class MixedType implements TypeInterface
{
    public function getName(): string
    {
        throw new \BadMethodCallException();
    }

    public function getNativeName(): string
    {
        throw new \BadMethodCallException();
    }

    /**
     * @param string $nativeName
     * @return TypeInterface
     */
    public function withNativeName(string $nativeName): TypeInterface
    {
        return $this;
    }

    /**
     * @param string $alias
     * @return TypeInterface
     */
    public function withAlias(string $alias): TypeInterface
    {
        return $this;
    }

    public function getPhpDocName(): string
    {
        return 'mixed';
    }

    public function isNullable(): bool
    {
        throw new \BadMethodCallException();
    }

    public function asNullable(): TypeInterface
    {
        throw new \BadMethodCallException();
    }

    public function equals(TypeInterface $other): bool
    {
        return $other instanceof MixedType;
    }

    /**
     * @return string
     */
    public function asDocBlockString(): string
    {
        return $this->getPhpDocName();
    }

    /**
     * @return string
     */
    public function asPhpTypeHint(): string
    {
        throw new \BadMethodCallException();
    }
}
