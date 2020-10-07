<?php declare(strict_types=1);
namespace PackageFactory\Neos\CodeGenerator\Domain\Code\Fusion\Prototype;

use PackageFactory\Neos\CodeGenerator\Domain\Code\Fusion\Identifier\PrototypeName;

/*
 * This file is part of the PackageFactory.Neos.CodeGenerator package
 */

interface PrototypeInterface
{
    /**
     * @return PrototypeName
     */
    public function getName(): PrototypeName;

    /**
     * @param PrototypeName $other
     * @return boolean
     */
    public function extends(PrototypeName $other): bool;

    /**
     * @return array<mixed>
     */
    public function getMeta(): array;
}
