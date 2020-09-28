<?php declare(strict_types=1);
namespace PackageFactory\Neos\CodeGenerator\Domain\Code\Php\Import;

/*
 * This file is part of the PackageFactory.Neos.CodeGenerator package
 */

interface ImportCollectionInterface
{
    /**
     * @template T
     * @param callable(ImportInterface):T $mapFn
     * @return T[]
     */
    public function map(callable $mapFn): array;
}
