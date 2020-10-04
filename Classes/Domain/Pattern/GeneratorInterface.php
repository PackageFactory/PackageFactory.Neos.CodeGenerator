<?php declare(strict_types=1);
namespace PackageFactory\Neos\CodeGenerator\Domain\Pattern;

/*
 * This file is part of the PackageFactory.Neos.CodeGenerator package
 */

use PackageFactory\Neos\CodeGenerator\Domain\Input\Query;

interface GeneratorInterface
{
    /**
     * @param Query $query
     * @return void
     */
    public function generate(Query $query): void;
}
