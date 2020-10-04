<?php declare(strict_types=1);
namespace PackageFactory\Neos\CodeGenerator\Domain\Code\Php\PhpClass;

/*
 * This file is part of the PackageFactory.Neos.CodeGenerator package
 */

use Neos\Flow\Annotations as Flow;

/**
 * @Flow\Scope("singleton")
 */
final class PhpClassRepository implements PhpClassRepositoryInterface
{
    /**
     * @var array<string, PhpClassInterface>
     */
    private $storage;

    /**
     * @param PhpClassName $className
     * @return null|PhpClassInterface
     */
    public function findOneByClassName(PhpClassName $className): ?PhpClassInterface
    {
        if (isset($this->storage[$className->asFullyQualifiedNameString()])) {
            return $this->storage[$className->asFullyQualifiedNameString()];
        }

        return null;
    }

    /**
     * @param PhpClassInterface $phpClass
     * @return void
     */
    public function add(PhpClassInterface $phpClass): void
    {
        $this->storage[$phpClass->getClassName()->asFullyQualifiedNameString()] = $phpClass;
    }
}
