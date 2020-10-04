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
final class PrototypeRepository implements PrototypeRepositoryInterface
{
    /**
     * @param PrototypeName $prototypeName
     * @return null|PrototypeInterface
     */
    public function findOneByPrototypeName(PrototypeName $prototypeName): ?PrototypeInterface
    {
        throw new \Exception('Not implemented');
    }
}
