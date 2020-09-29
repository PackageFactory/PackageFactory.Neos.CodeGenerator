<?php declare(strict_types=1);
namespace PackageFactory\Neos\CodeGenerator\Domain\Code\Php\Import;

/*
 * This file is part of the PackageFactory.Neos.CodeGenerator package
 */

/**
 * @extends \IteratorAggregate<string, ImportInterface>
 */
interface ImportCollectionInterface extends \Countable, \IteratorAggregate
{
}
