<?php declare(strict_types=1);
namespace PackageFactory\Neos\CodeGenerator\Domain\Code\Php\Property;

/*
 * This file is part of the PackageFactory.Neos.CodeGenerator package
 */

use Neos\Flow\Annotations as Flow;
use PackageFactory\Neos\CodeGenerator\Domain\Code\Php\Type\TypeFactory;

/**
 * @Flow\Scope("singleton")
 */
final class PropertyFactory
{
    /**
     * @Flow\Inject
     * @var TypeFactory
     */
    protected $typeFactory;

    /**
     * @param array{string, string} $keyValuePair
     * @return PropertyInterface
     */
    public function fromKeyValuePair(array $keyValuePair): PropertyInterface
    {
        return new Property($this->typeFactory->fromString($keyValuePair[1]), $keyValuePair[0]);
    }

    /**
     * @param \ReflectionProperty $reflectionProperty
     * @return PropertyInterface
     */
    public function fromReflectionProperty(\ReflectionProperty $reflectionProperty): PropertyInterface
    {
        return new Property(
            $this->typeFactory->fromReflectionProperty($reflectionProperty),
            $reflectionProperty->getName()
        );
    }
}
