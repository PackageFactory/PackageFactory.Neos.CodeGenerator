<?php declare(strict_types=1);
namespace PackageFactory\Neos\CodeGenerator\Pattern\PresentationObjects;

/*
 * This file is part of the PackageFactory.Neos.CodeGenerator package
 */

use Neos\Flow\Annotations as Flow;
use Neos\Flow\Package\FlowPackageInterface;
use PackageFactory\Neos\CodeGenerator\Domain\Code\PhpFile;
use PackageFactory\Neos\CodeGenerator\Domain\Code\PhpNamespace;
use PackageFactory\Neos\CodeGenerator\Domain\Pattern\GeneratorQuery;

/**
 * @Flow\Proxy(false)
 */
final class Value
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

        $domesticNamespace = PhpNamespace::fromFlowPackage($flowPackage)
            ->appendString('Presentation')
            ->append($subNamespace);

        $properties = [];
        foreach ($query->getRemainingArguments(2) as $argument) {
            foreach (explode(',', $argument) as $descriptor) {
                $properties[] = Property::fromDescriptor(trim($descriptor), $flowPackage, $domesticNamespace);
            }
        }

        return new self($flowPackage, $subNamespace, $className, $properties);
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
     * @return array|Property[]
     */
    public function getProperties(): array
    {
        return $this->properties;
    }

    /**
     * @return PhpFile
     */
    public function asPhpClassFile(): PhpFile
    {
        $body = [];

        $body[] = 'use Neos\Flow\Annotations as Flow;';
        if ($this->properties) {
            $body[] = trim(join(PHP_EOL, array_unique(
                array_filter(
                    array_map(function (Property $property) {
                        return $property->getType()->asImportStatement();
                    }, $this->properties)
                )
            )));
        } else {
            $body[] = '';
        }

        $body[] = '/**';
        $body[] = ' * @Flow\Proxy(false)';
        $body[] = ' */';
        $body[] = 'final class ' . $this->className;
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
            $body[] = '    public function __construct(';
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
                return $property->getGetterImplementation();
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
}
