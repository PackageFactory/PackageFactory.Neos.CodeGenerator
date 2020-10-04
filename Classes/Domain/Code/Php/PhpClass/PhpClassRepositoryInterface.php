<?php declare(strict_types=1);
namespace PackageFactory\Neos\CodeGenerator\Domain\Code\Php\PhpClass;

/*
 * This file is part of the PackageFactory.Neos.CodeGenerator package
 */

interface PhpClassRepositoryInterface
{
    /**
     * @param PhpClassName $className
     * @return null|PhpClassInterface
     */
    public function findOneByClassName(PhpClassName $className): ?PhpClassInterface;

    /**
     * @param PhpClassInterface $phpClass
     * @return void
     */
    public function add(PhpClassInterface $phpClass): void;
}
