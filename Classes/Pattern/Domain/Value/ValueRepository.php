<?php declare(strict_types=1);
namespace PackageFactory\Neos\CodeGenerator\Pattern\Domain\Value;

/*
 * This file is part of the PackageFactory.Neos.CodeGenerator package
 */

use Neos\Flow\Annotations as Flow;
use PackageFactory\Neos\CodeGenerator\Domain\Code\Php\PhpClass\PhpClassName;

/**
 * @Flow\Scope("singleton")
 */
final class ValueRepository
{
    /**
     * @var array<string,Value>
     */
    private $storage = [];

    /**
     * @param PhpClassName $className
     * @return null|Value
     */
    public function findOneByPhpClassName(PhpClassName $className): ?Value
    {
        if (isset($this->storage[$className->asFullyQualifiedNameString()])) {
            return $this->storage[$className->asFullyQualifiedNameString()];
        }
        return null;
    }

    /**
     * @param Value $value
     * @return void
     */
    public function add(Value $value): void
    {
        $this->storage[$value->getPhpClassName()->asFullyQualifiedNameString()] = $value;
    }
}
