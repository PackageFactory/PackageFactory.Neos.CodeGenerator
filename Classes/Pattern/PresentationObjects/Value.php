<?php declare(strict_types=1);
namespace PackageFactory\Neos\CodeGenerator\Pattern\PresentationObjects;

/*
 * This file is part of the PackageFactory.AtomicFusion.PresentationObjects package
 */

use Neos\Flow\Annotations as Flow;
use Neos\Flow\Package\FlowPackageInterface;
use PackageFactory\Neos\CodeGenerator\Domain\Code\PhpNamespace;

/**
 * @Flow\Proxy(false)
 */
final class Value
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
     * @param array<string> $arguments
     * @return self
     */
    public static function fromArguments(array $arguments, FlowPackageInterface $flowPackage): self
    {
        assert(isset($arguments[0]), new \InvalidArgumentException('No sub-namespace was given'));
        assert(isset($arguments[1]), new \InvalidArgumentException('No class name was given!'));

        $packageNamespace = PhpNamespace::fromFlowPackage($flowPackage);
        $subNamespace = PhpNamespace::fromString($arguments[0]);
        $className = $arguments[1];
        $properties = [];

        foreach (array_slice($arguments, 2) as $argument) {
            foreach (explode(',', $argument) as $descriptor) {
                $properties[] = Property::fromDescriptor(trim($descriptor), $flowPackage);
            }
        }

        return new self($packageNamespace, $subNamespace, $className, $properties);
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
     * @return array|Property[]
     */
    public function getProperties(): array
    {
        return $this->properties;
    }

    /**
     * @return string
     */
    public function getBody(): string
    {
        $result = [];

        $result[] = 'use Neos\Flow\Annotations as Flow;';
        if ($this->properties) {
            $result[] = trim(join(PHP_EOL, array_unique(
                array_filter(
                    array_map(function (Property $property) {
                        return $property->getType()->asImportStatement();
                    }, $this->properties)
                )
            )));
        } else {
            $result[] = '';
        }

        $result[] = '/**';
        $result[] = ' * @Flow\Proxy(false)';
        $result[] = ' */';
        $result[] = 'final class ' . $this->className;
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
                return $property->getGetterImplementation();
            }, $this->properties));
        }

        $result[] = '}';

        return join(PHP_EOL, $result);
    }
}
