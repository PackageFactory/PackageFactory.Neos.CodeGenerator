<?php declare(strict_types=1);
namespace PackageFactory\Neos\CodeGenerator\Pattern\PresentationObjects;

/*
 * This file is part of the PackageFactory.Neos.CodeGenerator package
 */

use Neos\Eel\Helper\StringHelper;
use Neos\Flow\Annotations as Flow;
use Neos\Flow\Package\FlowPackageInterface;
use PackageFactory\Neos\CodeGenerator\Domain\Code\PhpFile;
use PackageFactory\Neos\CodeGenerator\Domain\Code\PhpNamespace;
use PackageFactory\Neos\CodeGenerator\Domain\Code\YamlFile;
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
    public function getValueObjectClassName(): string
    {
        return $this->className;
    }

    /**
     * @return string
     */
    public function getFullyQualifiedValueObjectClassName(): string
    {
        return $this->getNamespace()->appendString($this->getValueObjectClassName())->getValue();
    }

    /**
     * @return string
     */
    public function getFactoryClassName(): string
    {
        return $this->className . 'Factory';
    }

    /**
     * @return string
     */
    public function getFactoryEelHelperName(): string
    {
        return $this->getPackageNamespace()->asKey() . '.' . $this->getValueObjectClassName();
    }

    /**
     * @return string
     */
    public function getFullyQualifiedFactoryClassName(): string
    {
        return $this->getNamespace()->appendString($this->getFactoryClassName())->getValue();
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
    public function asPhpClassFileForValueObject(): PhpFile
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
        $body[] = 'final class ' . $this->getValueObjectClassName() . ' implements ' . $this->getInterfaceName();
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
            $this->getValueObjectClassName(),
            join(PHP_EOL, $body)
        );
    }

    /**
     * @return PhpFile
     */
    public function asPhpClassFileForFactory(): PhpFile
    {
        $body = [];

        $body[] = 'use Neos\Flow\Annotations as Flow;';
        $body[] = 'use PackageFactory\AtomicFusion\PresentationObjects\Fusion\AbstractComponentPresentationObjectFactory;';
        $body[] = '';

        $body[] = '/**';
        $body[] = ' * @Flow\Scope("singleton")';
        $body[] = ' */';
        $body[] = 'final class ' . $this->getFactoryClassName() . ' extends AbstractComponentPresentationObjectFactory';
        $body[] = '{';
        $body[] = '}';

        return PhpFile::fromFlowPackageAndNamespace(
            $this->flowPackage,
            $this->getNamespace(),
            $this->getFactoryClassName(),
            join(PHP_EOL, $body)
        );
    }

    /**
     * @param YamlFile $settingsFile
     * @return YamlFile
     */
    public function asAppendedSettingForFusionDefaultContext(YamlFile $settingsFile): YamlFile
    {
        $settings = $settingsFile->getData();
        $settings['Neos']['Fusion']['defaultContext'][$this->getFactoryEelHelperName()] = $this->getFullyQualifiedFactoryClassName();

        return $settingsFile->withData($settings);
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
