<?php declare(strict_types=1);
namespace PackageFactory\Neos\CodeGenerator\Pattern\PresentationObjects;

/*
 * This file is part of the PackageFactory.Neos.CodeGenerator package
 */

use Neos\Eel\Helper\StringHelper;
use Neos\Flow\Annotations as Flow;
use Neos\Flow\Package\FlowPackageInterface;
use PackageFactory\Neos\CodeGenerator\Domain\Code\PhpNamespace;
use PackageFactory\Neos\CodeGenerator\Infrastructure\PackageResolver;
use Spatie\Enum\Enum;

/**
 * @Flow\Proxy(false)
 */
final class Type
{
    public const BUILTIN_TYPE_NAMES = ['bool', 'boolean', 'int', 'integer', 'float', 'string', 'array', 'iterable', 'object', 'callable'];

    /**
     * @var string
     */
    private $fullyQualifiedName;

    /**
     * @var null|string
     */
    private $aliasName;

    /**
     * @var bool
     */
    private $nullable;

    /**
     * @param string $fullyQualifiedName
     * @param null|string $aliasName
     * @param boolean $nullable
     */
    private function __construct(
        string $fullyQualifiedName,
        ?string $aliasName,
        bool $nullable
    ) {
        if ($fullyQualifiedName === 'boolean') {
            $fullyQualifiedName = 'bool';
        } elseif ($fullyQualifiedName === 'integer') {
            $fullyQualifiedName = 'int';
        }

        $this->fullyQualifiedName = $fullyQualifiedName;
        $this->aliasName = $aliasName;
        $this->nullable = $nullable;
    }

    /**
     * @param string $descriptor
     * @param FlowPackageInterface $flowPackage
     * @param PhpNamespace $domesticNameSpace
     * @return self
     */
    public static function fromDescriptor(
        string $descriptor,
        FlowPackageInterface $flowPackage,
        PhpNamespace $domesticNameSpace
    ): self {
        $nullable = \mb_substr($descriptor, 0, 1) === '?';
        if ($nullable) {
            $descriptor = \mb_substr($descriptor, 1);
        }

        if (in_array($descriptor, self::BUILTIN_TYPE_NAMES)) {
            return new self($descriptor, null, $nullable);
        }

        $stringHelper = new StringHelper();

        if ($stringHelper->startsWith($descriptor, '/') || $stringHelper->startsWith($descriptor, '\\')) {
            $targetNamespace = PhpNamespace::fromString($descriptor);
            $fullyQualifiedName = $targetNamespace->asAbsoluteNamespaceString();
            $aliasName = $targetNamespace->getImportName();

            return new self($fullyQualifiedName, $aliasName, $nullable);
        }

        $targetNamespace = $domesticNameSpace->appendString($descriptor);
        $fullyQualifiedName = $targetNamespace->asAbsoluteNamespaceString();
        $aliasName = $targetNamespace->getImportName();

        if (class_exists($fullyQualifiedName)) {
            return new self($aliasName, null, $nullable);
        } elseif (interface_exists($fullyQualifiedName . 'Interface')) {
            $aliasName .= 'Interface';
            return new self($aliasName, null, $nullable);
        } else {
            $targetNamespace = PhpNamespace::fromFlowPackage($flowPackage)
                ->appendString('Presentation')
                ->appendString($descriptor);

            $fullyQualifiedName = $targetNamespace->asAbsoluteNamespaceString();
            $aliasName = $targetNamespace->getImportName();
            if (interface_exists($fullyQualifiedName . 'Interface')) {
                $fullyQualifiedName .= 'Interface';
                $aliasName .= 'Interface';
            }

            return new self($fullyQualifiedName, $aliasName, $nullable);
        }

    }

    /**
     * @param \ReflectionType|null $type
     * @return self|null
     */
    public static function fromReflectionType(?\ReflectionType $type): ?self
    {
        if (!$type || !($type instanceof \ReflectionNamedType) || $type->getName() === 'void') {
            return null;
        } else if ($type->isBuiltin()) {
            return new self($type->getName(), null, $type->allowsNull());
        } else {
            $stringHelper = new StringHelper();

            if ($stringHelper->startsWith($type->getName(), '/') || $stringHelper->startsWith($type->getName(), '\\')) {
                return new self($type->getName(), null, $type->allowsNull());
            }

            $targetNamespace = PhpNamespace::fromString($type->getName());
            return new self($targetNamespace->asAbsoluteNamespaceString(), $targetNamespace->getImportName(), $type->allowsNull());
        }
    }

    /**
     * @return string
     */
    public function getFullyQualifiedName(): string
    {
        return $this->fullyQualifiedName;
    }

    /**
     * @return string
     */
    public function getAliasName(): string
    {
        return $this->aliasName ?? $this->fullyQualifiedName;
    }

    /**
     * @return boolean
     */
    public function isBuiltIn(): bool
    {
        return in_array($this->fullyQualifiedName, self::BUILTIN_TYPE_NAMES);
    }

    /**
     * @return boolean
     */
    public function refersToExistingClass(): bool
    {
        return class_exists($this->fullyQualifiedName);
    }

    /**
     * @return boolean
     */
    public function refersToPresentationModel(): bool
    {
        return \mb_strpos($this->fullyQualifiedName, '\\Presentation\\') !== false;
    }

    /**
     * @return string|null
     */
    public function asImportStatement(): ?string
    {
        if ($this->aliasName) {
            $stringHelper = new StringHelper();

            if ($this->fullyQualifiedName === $this->aliasName || $stringHelper->endsWith($this->fullyQualifiedName, '\\' . $this->aliasName)) {
                return 'use ' . ltrim($this->fullyQualifiedName, '\\') . ';';
            } else {
                return 'use ' . ltrim($this->fullyQualifiedName, '\\') . ' as ' . $this->aliasName . ';';
            }
        }

        return null;
    }

    /**
     * @return string
     */
    public function asDocBlockString(): string
    {
        $name = $this->aliasName ?? $this->fullyQualifiedName;

        if ($this->nullable) {
            return 'null|' . $name;
        } else {
            return $name;
        }
    }

    /**
     * @param PackageResolver $packageResolver
     * @param string $indentation
     * @return string
     */
    public function asSampleForFusionStyleguide(PackageResolver $packageResolver, string $indentation): string
    {
        switch ($this->fullyQualifiedName) {
            case 'bool':
                return '= true';
            case 'int':
                return '= 42';
            case 'float':
                return '= 12.3';
            case 'string':
                return '= \'Lorem ipsum...\'';
            case 'array':
            case 'iterable':
                return '= ${[]}';
            case 'object':
                return '= ${{}}';
            case 'callable':
                return '= ${param => param}';
            default:
                /** @phpstan-var class-string $fullyQualifiedName */
                $fullyQualifiedName = $this->fullyQualifiedName;

                if (is_subclass_of($fullyQualifiedName, Enum::class)) {
                    $class = $fullyQualifiedName;
                    if ($class::getValues()) {
                        return '= \'' . $class::getValues()[0] . '\'';
                    } else {
                        return '= null';
                    }
                } elseif (\mb_strlen($indentation) < 16 && $model = Model::fromClassName($fullyQualifiedName, $packageResolver)) {
                    return join(PHP_EOL, [
                        '{',
                        $model->asSampleForFusionStyleguide($packageResolver, $indentation . '    '),
                        $indentation . '}'
                    ]);
                } else {
                    return '= null';
                }
        }
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        $name = $this->aliasName ?? $this->fullyQualifiedName;

        if ($this->nullable) {
            return '?' . $name;
        } else {
            return $name;
        }
    }
}
