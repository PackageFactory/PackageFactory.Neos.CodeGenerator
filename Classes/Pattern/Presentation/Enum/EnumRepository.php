<?php declare(strict_types=1);
namespace PackageFactory\Neos\CodeGenerator\Pattern\Presentation\Enum;

/*
 * This file is part of the PackageFactory.Neos.CodeGenerator package
 */

use Neos\Flow\Annotations as Flow;
use PackageFactory\Neos\CodeGenerator\Domain\Code\Php\PhpClass\PhpClassName;

/**
 * @Flow\Scope("singleton")
 */
final class EnumRepository
{
    /**
     * @var array<string,Enum>
     */
    private $storage = [];

    /**
     * @param PhpClassName $className
     * @return null|Enum
     */
    public function findOneByPhpClassName(PhpClassName $className): ?Enum
    {
        if (isset($this->storage[$className->asFullyQualifiedNameString()])) {
            return $this->storage[$className->asFullyQualifiedNameString()];
        }
        return null;
    }

    /**
     * @param Enum $enum
     * @return void
     */
    public function add(Enum $enum): void
    {
        $this->storage[$enum->getPhpClassNameForValueObject()->asFullyQualifiedNameString()] = $enum;
    }
}
