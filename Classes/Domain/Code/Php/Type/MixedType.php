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
    public function getNativeName(): string
    {
        throw new \BadMethodCallException();
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
}
