<?php declare(strict_types=1);
namespace PackageFactory\Neos\CodeGenerator\Pattern\Presentation\Component;

/*
 * This file is part of the PackageFactory.Neos.CodeGenerator package
 */

use Neos\Flow\Annotations as Flow;
use PackageFactory\Neos\CodeGenerator\Domain\Code\Fusion\Identifier\PrototypeName;
use PackageFactory\Neos\CodeGenerator\Domain\Code\Fusion\Prototype\PrototypeInterface;
use PackageFactory\Neos\CodeGenerator\Domain\Code\Php\PhpClass\PhpClassName;

/**
 * @Flow\Proxy(false)
 */
final class ComponentSpecification
{
    const COMPONENT_MODEL_BASE_CLASS = 'PackageFactory\\AtomicFusion\\PresentationObjects\\Fusion\\AbstractComponentPresentationObject';
    const COMPONENT_MODEL_INTERFACE = 'PackageFactory\\AtomicFusion\\PresentationObjects\\Fusion\\ComponentPresentationObjectInterface';
    const COMPONENT_BASE_PROTOYPE = 'PackageFactory.AtomicFusion.PresentationObjects:PresentationObjectComponent';

    /**
     * @param PhpClassName $className
     * @return boolean
     */
    public static function isSatisfiedByClassName(PhpClassName $className): bool
    {
        $classNameAsString = $className->asFullyQualifiedNameString();

        return class_exists($classNameAsString) && (bool) preg_match('/.+\\\\Presentation\\\\.+/', $classNameAsString);
    }

    /**
     * @param \ReflectionClass<object> $reflectionClass
     * @return boolean
     */
    public static function isSatisfiedByReflectionClass(\ReflectionClass $reflectionClass): bool
    {
        if ($reflectionClass->isSubclassOf(self::COMPONENT_MODEL_BASE_CLASS)) {
            return true;
        }

        if ($reflectionClass->implementsInterface(self::COMPONENT_MODEL_INTERFACE)) {
            return true;
        }

        $companionInterfaceName = $reflectionClass->getName() . 'Interface';
        if (interface_exists($companionInterfaceName) && $reflectionClass->implementsInterface($companionInterfaceName)) {
            return true;
        }

        return false;
    }

    /**
     * @param PrototypeInterface $prototype
     * @return boolean
     */
    public static function isSatisfiedByFusionPrototype(PrototypeInterface $prototype): bool
    {
        return $prototype->extends(PrototypeName::fromString(self::COMPONENT_BASE_PROTOYPE));
    }
}
