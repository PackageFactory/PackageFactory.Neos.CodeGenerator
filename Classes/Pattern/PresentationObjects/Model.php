<?php declare(strict_types=1);
namespace PackageFactory\Neos\CodeGenerator\Pattern\PresentationObjects;

/*
 * This file is part of the PackageFactory.AtomicFusion.PresentationObjects package
 */

use Neos\Eel\Helper\StringHelper;
use Neos\Flow\Annotations as Flow;
use PackageFactory\Neos\CodeGenerator\Domain\Code\PhpNamespace;
use PackageFactory\Neos\CodeGenerator\Domain\Pattern\GeneratorQuery;

/**
 * @Flow\Proxy(false)
 */
final class Model
{
    /**
     * @var PhpNamespace
     */
    private $packageNamespace;

    /**
     * @var PhpNamespace
     */
    private $subNamespace;

    /**
     * @var string
     */
    private $className;

    /**
     * @var array|Property[]
     */
    private $properties;

    /**
     * @param PhpNamespace $packageNamespace
     * @param PhpNamespace $subNamespace
     * @param string $className
     * @param array|Property[] $properties
     */
    public function __construct(
        PhpNamespace $packageNamespace,
        PhpNamespace $subNamespace,
        string $className,
        array $properties
    ) {
        $this->packageNamespace = $packageNamespace;
        $this->subNamespace = $subNamespace;
        $this->className = $className;
        $this->properties = $properties;
    }

    /**
     * @param GeneratorQuery $query
     * @return self
     */
    public static function fromGeneratorQuery(GeneratorQuery $query): self
    {
        $arguments = $query->getArguments();

        assert(isset($arguments[0]), new \InvalidArgumentException('No sub-namespace was given'));
        assert(isset($arguments[1]), new \InvalidArgumentException('No class name was given!'));

        $packageNamespace = PhpNamespace::fromFlowPackage($query->getFlowPackage());
        $subNamespace = PhpNamespace::fromString($arguments[0]);
        $className = $arguments[1];
        $properties = [];

        foreach (array_slice($arguments, 2) as $argument) {
            foreach (explode(',', $argument) as $descriptor) {
                $properties[] = Property::fromDescriptor(trim($descriptor), $query->getFlowPackage());
            }
        }

        return new self($packageNamespace, $subNamespace, $className, $properties);
    }

    /**
     * @param class-string $className
     * @param PhpNamespace $packageNamespace
     * @return self
     */
    public static function fromClassName(string $className, PhpNamespace $packageNamespace): self
    {
        $stringHelper = new StringHelper();
        $reflectionClass = new \ReflectionClass($className);
        $namespace = PhpNamespace::fromString($className);

        if ($namespace->isSubNamespaceOf($packageNamespace)) {
            $subNamespace = $namespace->relativeTo($packageNamespace);
        } else {
            $packageNamespace = PhpNamespace::empty();
            $subNamespace = PhpNamespace::fromString($className)->relativeTo($packageNamespace);
        }

        $properties = [];
        foreach ($reflectionClass->getMethods(\ReflectionMethod::IS_PUBLIC) as $method) {
            if ($stringHelper->startsWith($method->getName(), 'get')) {
                if ($property = Property::fromGetter($method)) {
                    $properties[] = $property;
                }
            }
        }

        return new self($packageNamespace, $subNamespace->getParentNamespace(), $subNamespace->getImportName(), $properties);
    }

    /**
     * @return PhpNamespace
     */
    public function getPackageNamespace(): PhpNamespace
    {
        return $this->packageNamespace;
    }

    /**
     * @return PhpNamespace
     */
    public function getSubNamespace(): PhpNamespace
    {
        return $this->subNamespace;
    }

    /**
     * @return PhpNamespace
     */
    public function getNamespace(): PhpNamespace
    {
        return $this->packageNamespace->appendString('Presentation')->append($this->subNamespace);
    }

    /**
     * @return string
     */
    public function getClassName(): string
    {
        return $this->className;
    }

    /**
     * @return string
     */
    public function getFullyQualifiedClassName(): string
    {
        return $this->getNamespace()->appendString($this->className)->getValue();
    }

    /**
     * @return string
     */
    public function getInterfaceName(): string
    {
        return $this->className . 'Interface';
    }

    /**
     * @return string
     */
    public function getFullyQualifiedInterfaceName(): string
    {
        return $this->getNamespace()->appendString($this->getInterfaceName())->getValue();
    }

    /**
     * @return array|Property[]
     */
    public function getProperties(): array
    {
        return $this->properties;
    }

    /**
     * @return string
     */
    public function asSampleForFusionStyleguide(string $indentation): string
    {
        return join(PHP_EOL, array_map(function (Property $property) use ($indentation) {
            return $property->asSampleForFusionStyleguide($this->packageNamespace, $indentation);
        }, $this->properties));
    }

    /**
     * @return string
     */
    public function getBody(): string
    {
        $result = [];

        $result[] = 'use Neos\Flow\Annotations as Flow;';
        if ($this->properties) {
            $imports = trim(join(PHP_EOL, array_unique(
                array_filter(
                    array_map(function (Property $property) {
                        return $property->getType()->asImportStatement();
                    }, $this->properties)
                )
            )));

            if ($imports) {
                $result[] = $imports;
                $result[] = '';
            }
        } else {
            $result[] = '';
        }

        $result[] = '/**';
        $result[] = ' * @Flow\Proxy(false)';
        $result[] = ' */';
        $result[] = 'final class ' . $this->className . ' implements ' . $this->getInterfaceName();
        $result[] = '{';

        if ($this->properties) {
            $result[] = join(PHP_EOL . PHP_EOL, array_map(function (Property $property) {
                return $property->getDeclaration();
            }, $this->properties));
        }

        if ($this->properties) {
            $result[] = '';
            $result[] = '    /**';
            $result[] = join(PHP_EOL, array_map(function (Property $property) {
                return '     * @param ' . $property->asDocBlockString();
            }, $this->properties));
            $result[] = '     */';
            $result[] = '    public function __constructor(';
            $result[] = join(',' . PHP_EOL, array_map(function (Property $property) {
                return '        ' . $property->asParameter();
            }, $this->properties));
            $result[] = '    ) {';
            $result[] = join(PHP_EOL, array_map(function (Property $property) {
                return  '        ' . $property->getConstructorAssignment();
            }, $this->properties));
            $result[] = '    }';
        }

        if ($this->properties) {
            $result[] = '';
            $result[] = join(PHP_EOL . PHP_EOL, array_map(function (Property $property) {
                return $property->getGetterImplementation() . PHP_EOL . PHP_EOL . $property->getSetterImplementationForModel($this);
            }, $this->properties));
        }

        $result[] = '}';

        return join(PHP_EOL, $result);
    }

    /**
     * @return string
     */
    public function getInterfaceBody(): string
    {
        $result = [];

        if ($this->properties) {
            $result[] = join(PHP_EOL, array_unique(
                array_filter(
                    array_map(function (Property $property) {
                        return $property->getType()->asImportStatement();
                    }, $this->properties)
                )
            ));

            if (trim($result[0])) {
                $result[] = '';
            } else {
                $result = [];
            }
        }

        $result[] = 'interface ' . $this->getInterfaceName();
        $result[] = '{';
        if ($this->properties) {
            $result[] = join(PHP_EOL . PHP_EOL, array_map(function (Property $property) {
                return $property->getGetterSignature() . PHP_EOL . PHP_EOL . $property->getSetterSignatureForModel($this);
            }, $this->properties));
        }
        $result[] = '}';

        return join(PHP_EOL, $result);
    }
}
