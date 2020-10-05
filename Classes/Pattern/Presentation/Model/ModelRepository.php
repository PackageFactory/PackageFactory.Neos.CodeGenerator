<?php declare(strict_types=1);
namespace PackageFactory\Neos\CodeGenerator\Pattern\Presentation\Model;

/*
 * This file is part of the PackageFactory.Neos.CodeGenerator package
 */

use Neos\Flow\Annotations as Flow;
use PackageFactory\Neos\CodeGenerator\Domain\Code\Php\PhpClass\PhpClassName;

/**
 * @Flow\Scope("singleton")
 */
final class ModelRepository
{
    /**
     * @var array<string,Model>
     */
    private $storage;

    /**
     * @param PhpClassName $className
     * @return null|Model
     */
    public function findOneByPhpClassName(PhpClassName $className): ?Model
    {
        if (isset($this->storage[$className->asFullyQualifiedNameString()])) {
            return $this->storage[$className->asFullyQualifiedNameString()];
        }
        return null;
    }

    /**
     * @param Model $value
     * @return void
     */
    public function add(Model $model): void
    {
        $this->storage[$model->getPhpClassNameForValueObject()->asFullyQualifiedNameString()] = $model;
    }
}
