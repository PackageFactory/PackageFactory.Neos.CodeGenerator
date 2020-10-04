<?php declare(strict_types=1);
namespace PackageFactory\Neos\CodeGenerator\Domain\Code\Fusion\Prototype;

use PackageFactory\Neos\CodeGenerator\Domain\Code\Fusion\Identifier\PrototypeName;

/*
 * This file is part of the PackageFactory.Neos.CodeGenerator package
 */

interface PrototypeRepositoryInterface
{
    /**
     * @param PrototypeName $prototypeName
     * @return null|PrototypeInterface
     */
    public function findOneByPrototypeName(PrototypeName $prototypeName): ?PrototypeInterface;
}
