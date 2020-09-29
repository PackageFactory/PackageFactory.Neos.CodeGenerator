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
     * @var TypeInterface[]
     */
    private $parameterTypes;

    /**
     * @var boolean
     */
    private $nullable;

    /**
     * @param string $name
     * @param TypeInterface[] $parameterTypes
     * @param boolean $nullable
     */
    public function __construct(string $name, array $parameterTypes, bool $nullable)
    {
        $this->name = $name;
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
    public function getNativeName(): string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getPhpDocName(): string
    {
        if (count($this->parameterTypes) > 0) {
            return sprintf(
                '%s<%s>',
                $this->name,
                join(', ', array_map(
                    static function (TypeInterface $type) { return $type->getPhpDocName(); },
                    $this->parameterTypes
                ))
            );
        } else {
            return $this->name;
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
        return new self($this->name, $this->parameterTypes, true);
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
        return $other instanceof ClassType && $this->getPhpDocName() === $other->getPhpDocName();
    }
}
