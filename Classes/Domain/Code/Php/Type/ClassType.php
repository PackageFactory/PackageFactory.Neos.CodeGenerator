<?php declare(strict_types=1);
namespace PackageFactory\Neos\CodeGenerator\Domain\Code\Php\Type;

/*
 * This file is part of the PackageFactory.Neos.CodeGenerator package
 */

use Neos\Flow\Annotations as Flow;

/**
 * @Flow\Proxy(false)
 */
final class ClassType implements TypeInterface
{
    public const NAME_PATTERN = '/^[a-zA-Z0-9_\x7f-\xff\\\\]*[a-zA-Z0-9_\x7f-\xff]$/';

    /**
     * @var string
     */
    private $name;

    /**
     * @var null|string
     */
    private $alias;

    /**
     * @var TypeInterface[]
     */
    private $parameterTypes;

    /**
     * @var boolean
     */
    private $nullable;

    /**
     * @param string $name
     * @param null|string $alias
     * @param TypeInterface[] $parameterTypes
     * @param boolean $nullable
     */
    public function __construct(string $name, ?string $alias, array $parameterTypes, bool $nullable)
    {
        $this->name = $name;
        $this->alias = $alias;
        $this->parameterTypes = $parameterTypes;
        $this->nullable = $nullable;
    }

    /**
     * @param string $name
     * @return boolean
     */
    public static function isValidName(string $name): bool
    {
        return (bool) preg_match(self::NAME_PATTERN, $name);
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->alias ?? $this->name;
    }

    /**
     * @return string
     */
    public function getNativeName(): string
    {
        return $this->name;
    }

    /**
     * @param string $nativeName
     * @return TypeInterface
     */
    public function withNativeName(string $nativeName): TypeInterface
    {
        return new self($nativeName, $this->alias, $this->parameterTypes, $this->nullable);
    }

    /**
     * @param string $alias
     * @return TypeInterface
     */
    public function withAlias(string $alias): TypeInterface
    {
        return new self($this->name, $alias, $this->parameterTypes, $this->nullable);
    }

    /**
     * @return string
     */
    public function getPhpDocName(): string
    {
        if (count($this->parameterTypes) > 0) {
            return sprintf(
                '%s<%s>',
                $this->alias ?? $this->name,
                join(', ', array_map(
                    static function (TypeInterface $type) { return $type->getPhpDocName(); },
                    $this->parameterTypes
                ))
            );
        } else {
            return $this->alias ?? $this->name;
        }
    }

    /**
     * @return boolean
     */
    public function isNullable(): bool
    {
        return $this->nullable;
    }

    /**
     * @return TypeInterface
     */
    public function asNullable(): TypeInterface
    {
        return new self($this->name, $this->alias, $this->parameterTypes, true);
    }

    /**
     * @return string
     */
    public function asDocBlockString(): string
    {
        return ($this->nullable ? 'null|' : '') . $this->getPhpDocName();
    }

    /**
     * @return string
     */
    public function asPhpTypeHint(): string
    {
        return ($this->nullable ? '?' : '') . ($this->alias ?? $this->name);
    }

    /**
     * @param TypeInterface $other
     * @return boolean
     */
    public function equals(TypeInterface $other): bool
    {
        return $other instanceof ClassType && $this->getPhpDocName() === $other->getPhpDocName();
    }
}
