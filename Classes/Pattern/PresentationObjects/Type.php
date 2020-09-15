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
    public const WELL_KNOWN_TYPES = [
        'slot' => 'PackageFactory\\AtomicFusion\\PresentationObjects\\Presentation\\Slot\\SlotInterface'
    ];

    /**
     * @var string
     */
    private $name;

    /**
     * @var bool
     */
    private $nullable;

    /**
     * @var bool
     */
    private $builtin;

    /**
     * @param string $name
     * @param boolean $nullable
     * @param boolean $builtin
     */
    private function __construct(
        string $name,
        bool $nullable,
        bool $builtin
    ) {
        if ($name === 'boolean') {
            $name = 'bool';
        } elseif ($name === 'integer') {
            $name = 'int';
        }

        $this->name = $name;
        $this->nullable = $nullable;
        $this->builtin = $builtin;
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
            return new self($descriptor, $nullable, true);
        }

        if (array_key_exists($descriptor, self::WELL_KNOWN_TYPES)) {
            return self::fromPhpNamespace(
                PhpNamespace::fromString(self::WELL_KNOWN_TYPES[$descriptor]),
                $nullable
            );
        }

        $stringHelper = new StringHelper();

        if ($stringHelper->startsWith($descriptor, '/') || $stringHelper->startsWith($descriptor, '\\')) {
            return self::fromPhpNamespace(
                PhpNamespace::fromString($descriptor),
                $nullable
            );
        }

        $targetNamespace = $domesticNameSpace->appendString($descriptor);

        if ($targetNamespace->asInterface()->exists()) {
            return self::fromPhpNamespace($targetNamespace->asInterface(), $nullable);
        } elseif ($targetNamespace->exists()) {
            return self::fromPhpNamespace($targetNamespace, $nullable);
        } else {
            $targetNamespace = PhpNamespace::fromFlowPackage($flowPackage)
                ->appendString('Presentation')
                ->appendString($descriptor);

            if ($targetNamespace->asInterface()->exists()) {
                return self::fromPhpNamespace($targetNamespace->asInterface(), $nullable);
            } else {
                return self::fromPhpNamespace($targetNamespace, $nullable);
            }
        }
    }

    /**
     * @param PhpNamespace $phpNamespace
     * @param boolean $nullable
     * @return self
     */
    public static function fromPhpNamespace(PhpNamespace $phpNamespace, bool $nullable): self
    {
        return new self($phpNamespace->asAbsoluteNamespaceString(), $nullable, false);
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
            return new self($type->getName(), $type->allowsNull(), $type->isBuiltin());
        } else {
            return self::fromPhpNamespace(
                PhpNamespace::fromString($type->getName()),
                $type->allowsNull()
            );
        }
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return boolean
     */
    public function isBuiltIn(): bool
    {
        return $this->builtin;
    }

    /**
     * @return boolean
     */
    public function refersToExistingClassOrInterface(): bool
    {
        if ($namespace = $this->asPhpNamespace()) {
            return $namespace->exists();
        } else {
            return false;
        }
    }

    /**
     * @return boolean
     */
    public function refersToPresentationModel(): bool
    {
        return is_a($this->name, '\\PackageFactory\\AtomicFusion\\PresentationObjects\\Fusion\\ComponentPresentationObjectInterface', true);
    }

    /**
     * @return boolean
     */
    public function refersToSlot(): bool
    {
        return is_a($this->name, '\\PackageFactory\\AtomicFusion\\PresentationObjects\\Presentation\\Slot\\SlotInterface', true);
    }

    /**
     * @return null|PhpNamespace
     */
    public function asPhpNamespace(): ?PhpNamespace
    {
        if ($this->builtin) {
            return null;
        } else {
            return PhpNamespace::fromString($this->name);
        }
    }

    /**
     * @return null|string
     */
    public function asImportStatement(PhpNamespace $domesticNameSpace): ?string
    {
        if ($namespace = $this->asPhpNamespace()) {
            if (!$namespace->getParentNamespace()->equals($domesticNameSpace)) {
                return 'use ' . ltrim($this->name, '\\') . ';';
            }
        }

        return null;
    }

    /**
     * @return string
     */
    public function asDocBlockString(): string
    {
        if ($namespace = $this->asPhpNamespace()) {
            $name = $namespace->getImportName();
        } elseif ($this->name === 'int') {
            $name = 'integer';
        } elseif ($this->name === 'bool') {
            $name = 'boolean';
        } else {
            $name = $this->name;
        }

        if ($this->nullable) {
            return 'null|' . $name;
        } else {
            return $name;
        }
    }

    /**
     * @return string
     */
    public function asTypeHint(): string
    {
        if ($namespace = $this->asPhpNamespace()) {
            $name = $namespace->getImportName();
        } else {
            $name = $this->name;
        }

        if ($this->nullable) {
            return '?' . $name;
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
        switch ($this->name) {
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
                $fullyQualifiedName = $this->name;
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
}
