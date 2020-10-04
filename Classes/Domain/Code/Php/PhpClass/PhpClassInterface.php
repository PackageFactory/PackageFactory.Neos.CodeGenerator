<?php declare(strict_types=1);
namespace PackageFactory\Neos\CodeGenerator\Domain\Code\Php\PhpClass;

/*
 * This file is part of the PackageFactory.Neos.CodeGenerator package
 */

interface PhpClassInterface
{
    /**
     * @return PhpClassName
     */
    public function getClassName(): PhpClassName;
}
