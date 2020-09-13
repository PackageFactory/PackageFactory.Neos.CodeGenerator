<?php declare(strict_types=1);
namespace PackageFactory\Neos\CodeGenerator\Pattern\Domain;

/*
 * This file is part of the PackageFactory.Neos.CodeGenerator package
 */

use Neos\Eel\Helper\StringHelper;
use Neos\Flow\Annotations as Flow;
use Neos\Flow\Package\FlowPackageInterface;
use PackageFactory\Neos\CodeGenerator\Domain\Code\PhpNamespace;

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
     * @return self
     */
    public static function fromDescriptor(string $descriptor, FlowPackageInterface $flowPackage): self
    {
        $nullable = \mb_substr($descriptor, 0, 1) === '?';
        if ($nullable) {
            $descriptor = \mb_substr($descriptor, 1);
        }

        if (in_array($descriptor, self::BUILTIN_TYPE_NAMES)) {
            return new self($descriptor, null, $nullable);
        }

        $stringHelper = new StringHelper();

        if ($stringHelper->startsWith($descriptor, '/') || $stringHelper->startsWith($descriptor, '\\')) {
            return new self(str_replace('/', '\\', $descriptor), null, $nullable);
        }

        $targetNamespace = PhpNamespace::fromFlowPackage($flowPackage)
            ->appendString('Domain')
            ->appendString($descriptor);

        $fullyQualifiedName = $targetNamespace->asAbsoluteNamespaceString();
        $aliasName = $targetNamespace->getImportName();
        if (interface_exists($fullyQualifiedName . 'Interface')) {
            $fullyQualifiedName .= 'Interface';
            $aliasName .= 'Interface';
        }

        return new self($fullyQualifiedName, $aliasName, $nullable);
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