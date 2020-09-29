<?php declare(strict_types=1);
namespace PackageFactory\Neos\CodeGenerator\Domain\Code\Php\Type;

/*
 * This file is part of the PackageFactory.Neos.CodeGenerator package
 */

use Neos\Flow\Annotations as Flow;

/**
 * @Flow\Proxy(false)
 */
final class ArrayType implements TypeInterface
{
    /**
     * @var TypeInterface
     */
    private $keyType;

    /**
     * @var TypeInterface
     */
    private $valueType;

    /**
     * @var bool
     */
    private $nullable;

    /**
     * @param TypeInterface $keyType
     * @param TypeInterface $valueType
     * @param boolean $nullable
     */
    public function __construct(
        TypeInterface $keyType,
        TypeInterface $valueType,
        bool $nullable
    ) {
        $this->keyType = $keyType;
        $this->valueType = $valueType;
        $this->nullable = $nullable;
    }

    /**
     * @param string $name
     * @return boolean
     */
    public static function isValidName(string $name): bool
    {
        $name = trim($name);
        return (substr($name, 0, 5) === 'array') || (substr($name, -2) === '[]');
    }

    /**
     * @return string
     */
    public function getNativeName(): string
    {
        return 'array';
    }

    /**
     * @return string
     */
    public function getPhpDocName(): string
    {
        if ($this->keyType->equals(ScalarType::int())) {
            if ($this->valueType->equals(new MixedType)) {
                return 'array<mixed>';
            } else {
                return $this->valueType->getPhpDocName() . '[]';
            }
        } else {
            return 'array<' . $this->keyType->getPhpDocName() . ', ' . $this->valueType->getPhpDocName() . '>';
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
        return new self($this->keyType, $this->valueType, true);
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
        return ($this->nullable ? '?' : '') . $this->getNativeName();
    }

    /**
     * @param TypeInterface $other
     * @return boolean
     */
    public function equals(TypeInterface $other): bool
    {
        return $other instanceof ArrayType && $this->getPhpDocName() === $other->getPhpDocName();
    }
}
