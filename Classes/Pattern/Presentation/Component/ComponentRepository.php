<?php declare(strict_types=1);
namespace PackageFactory\Neos\CodeGenerator\Pattern\Presentation\Component;

/*
 * This file is part of the PackageFactory.Neos.CodeGenerator package
 */

use Neos\Flow\Annotations as Flow;
use PackageFactory\Neos\CodeGenerator\Domain\Code\Fusion\Identifier\PrototypeName;
use PackageFactory\Neos\CodeGenerator\Domain\Code\Fusion\Prototype\PrototypeRepositoryInterface;
use PackageFactory\Neos\CodeGenerator\Domain\Code\Php\PhpClass\PhpClassName;
use PackageFactory\Neos\CodeGenerator\Framework\Util\StringUtil;

/**
 * @Flow\Scope("singleton")
 */
final class ComponentRepository
{
    /**
     * @var array<string,Component>
     */
    private $storage = [];

    /**
     * @Flow\Inject
     * @var PrototypeRepositoryInterface
     */
    protected $prototypeRepository;

    /**
     * @Flow\Inject
     * @var ComponentFactory
     */
    protected $componentFactory;

    /**
     * @param PhpClassName $className
     * @return null|Component
     */
    public function findOneByPhpClassName(PhpClassName $className): ?Component
    {
        if (isset($this->storage[$className->asFullyQualifiedNameString()])) {
            return $this->storage[$className->asFullyQualifiedNameString()];
        }

        if (ComponentSpecification::isSatisfiedByClassName($className)) {
            $classNameAsString = $className->asFullyQualifiedNameString();
            $reflectionClass = new \ReflectionClass($classNameAsString);

            if (ComponentSpecification::isSatisfiedByReflectionClass($reflectionClass)) {
                list($packageKey, $componentName) = explode('\\Presentation\\', $classNameAsString);

                $packageKey = ltrim($packageKey, '\\');
                $packageKey = str_replace('\\', '.', $packageKey);

                $componentName = str_replace('\\', '.', $componentName);
                $componentName = StringUtil::dropTail('.', $componentName);

                $prototypeName = PrototypeName::fromString($packageKey . ':Component.' . $componentName);
                if ($prototype = $this->prototypeRepository->findOneByPrototypeName($prototypeName)) {
                    if (ComponentSpecification::isSatisfiedByFusionPrototype($prototype)) {
                        return $this->componentFactory->fromExisitingComponent($reflectionClass, $prototype);
                    }
                }
            }
        }

        return null;
    }

    /**
     * @param Component $component
     * @return void
     */
    public function add(Component $component): void
    {
        $this->storage[$component->getModel()->getPhpClassNameForValueObject()->asFullyQualifiedNameString()] = $component;
    }
}
