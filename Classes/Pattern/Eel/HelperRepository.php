<?php declare(strict_types=1);
namespace PackageFactory\Neos\CodeGenerator\Pattern\Eel;

/*
 * This file is part of the PackageFactory.Neos.CodeGenerator package
 */

use Neos\Flow\Annotations as Flow;
use PackageFactory\Neos\CodeGenerator\Domain\Code\Php\PhpClass\PhpClassName;

/**
 * @Flow\Scope("singleton")
 */
final class HelperRepository
{
    /**
     * @var array<string,Helper>
     */
    private $storage;

    /**
     * @param PhpClassName $className
     * @return null|Helper
     */
    public function findOneByPhpClassName(PhpClassName $className): ?Helper
    {
        if (isset($this->storage[$className->asFullyQualifiedNameString()])) {
            return $this->storage[$className->asFullyQualifiedNameString()];
        }
        return null;
    }

    /**
     * @param Helper $value
     * @return void
     */
    public function add(Helper $helper): void
    {
        $this->storage[$helper->getPhpClassName()->asFullyQualifiedNameString()] = $helper;
    }
}
