<?php declare(strict_types=1);
namespace PackageFactory\Neos\CodeGenerator\Domain\Code\Fusion\Prototype;

/*
 * This file is part of the PackageFactory.Neos.CodeGenerator package
 */

use Neos\Flow\Annotations as Flow;
use PackageFactory\Neos\CodeGenerator\Domain\Code\Fusion\Identifier\PrototypeName;

/**
 * @Flow\Proxy(false)
 */
final class Prototype implements PrototypeInterface
{
    /**
     * @var PrototypeName
     */
    private $name;

    /**
     * @var array<mixed>
     */
    private $ast;

    /**
     * @param PrototypeName $name
     * @param array<mixed> $ast
     */
    public function __construct(PrototypeName $name, array $ast)
    {
        $this->name = $name;
        $this->ast = $ast;
    }

    /**
     * @return PrototypeName
     */
    public function getName(): PrototypeName
    {
        return $this->name;
    }

    /**
     * @param PrototypeName $otherPrototypeName
     * @return boolean
     */
    public function extends(PrototypeName $otherPrototypeName): bool
    {
        if (isset($this->ast['__prototypeObjectName']) && $this->ast['__prototypeObjectName'] === $otherPrototypeName->asString()) {
            return true;
        }

        if (isset($this->ast['__prototypeChain']) && in_array($otherPrototypeName->asString(), $this->ast['__prototypeChain'])) {
            return true;
        }

        return false;
    }

    /**
     * @return array<mixed>
     */
    public function getMeta(): array
    {
        return $this->ast['__meta'] ?? [];
    }
}
