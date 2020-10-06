<?php declare(strict_types=1);
namespace PackageFactory\Neos\CodeGenerator\Pattern\Presentation\Component;

/*
 * This file is part of the PackageFactory.Neos.CodeGenerator package
 */

use Neos\Flow\Annotations as Flow;
use PackageFactory\Neos\CodeGenerator\Domain\Code\Php\PhpClass\PhpClassName;

/**
 * @Flow\Scope("singleton")
 */
final class ComponentRepository
{
    /**
     * @var array<string,Component>
     */
    private $storage;

    /**
     * @param PhpClassName $className
     * @return null|Component
     */
    public function findOneByPhpClassName(PhpClassName $className): ?Component
    {
        if (isset($this->storage[$className->asFullyQualifiedNameString()])) {
            return $this->storage[$className->asFullyQualifiedNameString()];
        }
        return null;
    }

    /**
     * @param Component $value
     * @return void
     */
    public function add(Component $component): void
    {
        $this->storage[$component->getModel()->getPhpClassNameForValueObject()->asFullyQualifiedNameString()] = $component;
    }
}
