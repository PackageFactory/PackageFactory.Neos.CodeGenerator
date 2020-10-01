<?php declare(strict_types=1);
namespace PackageFactory\Neos\CodeGenerator\Domain\Code\Php\Import;

/*
 * This file is part of the PackageFactory.Neos.CodeGenerator package
 */

use Neos\Flow\Annotations as Flow;
use PackageFactory\Neos\CodeGenerator\Domain\Code\Php\Identifier\PhpNamespace;
use PackageFactory\Neos\CodeGenerator\Domain\Code\Php\Type\ClassType;
use PackageFactory\Neos\CodeGenerator\Domain\Code\Php\Type\TypeInterface;

/**
 * @Flow\Proxy(false)
 */
final class Import implements ImportInterface
{
    /**
     * @var string
     */
    private $fullyQualifiedName;

    /**
     * @var null|string
     */
    private $alias;

    /**
     * @var string
     */
    private $name;

    /**
     * @param string $fullyQualifiedName
     * @param string|null $alias
     */
    public function __construct(
        string $fullyQualifiedName,
        ?string $alias
    ) {
        $this->fullyQualifiedName = $fullyQualifiedName;
        $this->alias = $alias;

        if ($this->alias) {
            $this->name = $this->alias;
        } else {
            $this->name = substr($this->fullyQualifiedName, strrpos($this->fullyQualifiedName, '\\') + 1);
        }
    }

    /**
     * @param ClassType $type
     * @param null|PhpNamespace $referenceNamespace
     * @return self|null
     */
    public static function fromClassType(ClassType $type, ?PhpNamespace $referenceNamespace = null): ?self
    {
        $typeName = $type->getNativeName();

        if (strpos($typeName, '\\') !== false) {
            if ($typeName[0] === '\\') {
                return new Import(substr($typeName, 1), null);
            } elseif ($referenceNamespace) {
                return new Import($referenceNamespace->append($typeName)->asString(), null);
            }
        }

        return null;
    }

    /**
     * @return string
     */
    public function getFullyQualifiedName(): string
    {
        return $this->fullyQualifiedName;
    }

    /**
     * @return null|string
     */
    public function getAlias(): ?string
    {
        return $this->alias;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function asPhpUseStatement(): string
    {
        $result = 'use ' . $this->fullyQualifiedName;

        if ($this->alias !== null) {
            $result .= ' as ' . $this->alias;
        }

        return $result . ';';
    }

    /**
     * @param string $alias
     * @return ImportInterface
     */
    public function withAlias(string $alias): ImportInterface
    {
        return new self($this->fullyQualifiedName, $alias);
    }
}
