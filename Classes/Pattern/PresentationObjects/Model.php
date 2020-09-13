<?php declare(strict_types=1);
namespace PackageFactory\Neos\CodeGenerator\Pattern\PresentationObjects;

/*
 * This file is part of the PackageFactory.AtomicFusion.PresentationObjects package
 */

use Neos\Eel\Helper\StringHelper;
use Neos\Flow\Annotations as Flow;
use Neos\Flow\Package\FlowPackageInterface;
use PackageFactory\Neos\CodeGenerator\Domain\Code\PhpFile;
use PackageFactory\Neos\CodeGenerator\Domain\Code\PhpNamespace;
use PackageFactory\Neos\CodeGenerator\Domain\Pattern\GeneratorQuery;
use PackageFactory\Neos\CodeGenerator\Infrastructure\PackageResolver;

/**
 * @Flow\Proxy(false)
 */
final class Model
{
    /**
     * @var FlowPackageInterface
     */
    private $flowPackage;

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
     * @param FlowPackageInterface $flowPackage
     * @param PhpNamespace $subNamespace
     * @param string $className
     * @param array|Property[] $properties
     */
    public function __construct(
        FlowPackageInterface $flowPackage,
        PhpNamespace $subNamespace,
        string $className,
        array $properties
    ) {
        $this->flowPackage = $flowPackage;
        $this->subNamespace = $subNamespace;
        $this->className = $className;
        $this->properties = $properties;
    }

    /**
     * @param GeneratorQuery $query
     * @param FlowPackageInterface $flowPackage
     * @return self
     */
    public static function fromQuery(GeneratorQuery $query, FlowPackageInterface $flowPackage): self
    {
        $subNamespace = PhpNamespace::fromString($query->getArgument(0, 'No sub-namespace was given!'));
        $className = $query->getArgument(1, 'No class name was given!');
        $properties = [];

        foreach ($query->getRemainingArguments(2) as $argument) {
            foreach (explode(',', $argument) as $descriptor) {
                $properties[] = Property::fromDescriptor(trim($descriptor), $flowPackage);
            }
        }

        return new self($flowPackage, $subNamespace, $className, $properties);
    }

    /**
     * @param class-string $className
     * @param PackageResolver $packageResolver
     * @return null|self
     */
    public static function fromClassName(string $className, PackageResolver $packageResolver): ?self
    {
        $parts = explode('\\Presentation\\', $className);
        if (count($parts) === 2) {
            $packageKey = PhpNamespace::fromString($parts[0])->asKey();

            if ($flowPackage = $packageResolver->gracefullyResolveFromPackageKey($packageKey)) {
                $stringHelper = new StringHelper();
                $reflectionClass = new \ReflectionClass($className);
                $subNamespace = PhpNamespace::fromString($parts[0]);
                $className = $subNamespace->getImportName();
                $subNamespace = $subNamespace->getParentNamespace();

                $properties = [];
                foreach ($reflectionClass->getMethods(\ReflectionMethod::IS_PUBLIC) as $method) {
                    if ($stringHelper->startsWith($method->getName(), 'get')) {
                        if ($property = Property::fromGetter($method)) {
                            $properties[] = $property;
                        }
                    }
                }

                return new self($flowPackage, $subNamespace, $className, $properties);
            }
        }

        return null;
    }

    /**
     * @return FlowPackageInterface
     */
    public function getFlowPackage(): FlowPackageInterface
    {
        return $this->flowPackage;
    }

    /**
     * @return PhpNamespace
     */
    public function getPackageNamespace(): PhpNamespace
    {
        return PhpNamespace::fromFlowPackage($this->flowPackage);
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
        return $this->getPackageNamespace()->appendString('Presentation')->append($this->subNamespace);
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
     * @param PackageResolver $packageResolver
     * @param string $indentation
     * @return string
     */
    public function asSampleForFusionStyleguide(PackageResolver $packageResolver, string $indentation): string
    {
        return join(PHP_EOL, array_map(function (Property $property) use ($packageResolver, $indentation) {
            return $property->asSampleForFusionStyleguide($packageResolver, $indentation);
        }, $this->properties));
    }

    /**
     * @return PhpFile
     */
    public function asPhpClassFile(): PhpFile
    {
        $body = [];

        $body[] = 'use Neos\Flow\Annotations as Flow;';
        if ($this->properties) {
            $imports = trim(join(PHP_EOL, array_unique(
                array_filter(
                    array_map(function (Property $property) {
                        return $property->getType()->asImportStatement();
                    }, $this->properties)
                )
            )));

            if ($imports) {
                $body[] = $imports;
                $body[] = '';
            }
        } else {
            $body[] = '';
        }

        $body[] = '/**';
        $body[] = ' * @Flow\Proxy(false)';
        $body[] = ' */';
        $body[] = 'final class ' . $this->className . ' implements ' . $this->getInterfaceName();
        $body[] = '{';

        if ($this->properties) {
            $body[] = join(PHP_EOL . PHP_EOL, array_map(function (Property $property) {
                return $property->getDeclaration();
            }, $this->properties));
        }

        if ($this->properties) {
            $body[] = '';
            $body[] = '    /**';
            $body[] = join(PHP_EOL, array_map(function (Property $property) {
                return '     * @param ' . $property->asDocBlockString();
            }, $this->properties));
            $body[] = '     */';
            $body[] = '    public function __constructor(';
            $body[] = join(',' . PHP_EOL, array_map(function (Property $property) {
                return '        ' . $property->asParameter();
            }, $this->properties));
            $body[] = '    ) {';
            $body[] = join(PHP_EOL, array_map(function (Property $property) {
                return  '        ' . $property->getConstructorAssignment();
            }, $this->properties));
            $body[] = '    }';
        }

        if ($this->properties) {
            $body[] = '';
            $body[] = join(PHP_EOL . PHP_EOL, array_map(function (Property $property) {
                return $property->getGetterImplementation() . PHP_EOL . PHP_EOL . $property->getSetterImplementationForModel($this);
            }, $this->properties));
        }

        $body[] = '}';

        return PhpFile::fromFlowPackageAndNamespace(
            $this->flowPackage,
            $this->getNamespace(),
            $this->getClassName(),
            join(PHP_EOL, $body)
        );
    }

    /**
     * @return PhpFile
     */
    public function asPhpInterfaceFile(): PhpFile
    {
        $body = [];

        if ($this->properties) {
            $body[] = join(PHP_EOL, array_unique(
                array_filter(
                    array_map(function (Property $property) {
                        return $property->getType()->asImportStatement();
                    }, $this->properties)
                )
            ));

            if (trim($body[0])) {
                $body[] = '';
            } else {
                $body = [];
            }
        }

        $body[] = 'interface ' . $this->getInterfaceName();
        $body[] = '{';
        if ($this->properties) {
            $body[] = join(PHP_EOL . PHP_EOL, array_map(function (Property $property) {
                return $property->getGetterSignature() . PHP_EOL . PHP_EOL . $property->getSetterSignatureForModel($this);
            }, $this->properties));
        }
        $body[] = '}';

        return PhpFile::fromFlowPackageAndNamespace(
            $this->flowPackage,
            $this->getNamespace(),
            $this->getInterfaceName(),
            join(PHP_EOL, $body)
        );
    }
}
