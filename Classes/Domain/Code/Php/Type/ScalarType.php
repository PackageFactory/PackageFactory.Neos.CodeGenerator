<?php declare(strict_types=1);
namespace PackageFactory\Neos\CodeGenerator\Domain\Code\Php\Type;

/*
 * This file is part of the PackageFactory.Neos.CodeGenerator package
 */

use Neos\Flow\Annotations as Flow;

/**
 * @Flow\Proxy(false)
 */
final class ScalarType implements TypeInterface
{
    public const NAMES = ['bool', 'boolean', 'int', 'integer', 'float', 'string'];
    public const ALIASES = ['boolean' => 'bool', 'integer' => 'int'];

    /**
     * @var string
     */
    private $nativeName;

    /**
     * @var string
     */
    private $phpDocName;

    /**
     * @var boolean
     */
    private $nullable;

    /**
     * @param string $name
     * @param boolean $nullable
     */
    public function __construct(string $name, bool $nullable)
    {
        if (array_key_exists($name, self::ALIASES)) {
            $this->nativeName = self::ALIASES[$name];
            $this->phpDocName = $name;
        } elseif (($phpDocName = array_search($name, self::ALIASES)) && is_string($phpDocName)) {
            $this->nativeName = $name;
            $this->phpDocName = $phpDocName;
        } elseif (in_array($name, self::NAMES)) {
            $this->nativeName = $name;
            $this->phpDocName = $name;
        } else {
            throw new \DomainException('"' . $name . '" is not a valid scalar type name.');
        }

        $this->nullable = $nullable;
    }

    /**
     * @param string $name
     * @return boolean
     */
    public static function isValidName(string $name): bool
    {
        return in_array($name, self::NAMES);
    }

    /**
     * @param boolean $nullable
     * @return self
     */
    public static function int(bool $nullable = false): self
    {
        return new self('int', $nullable);
    }

    /**
     * @return string
     */
    public function getNativeName(): string
    {
        return $this->nativeName;
    }

    /**
     * @return string
     */
    public function getPhpDocName(): string
    {
        return $this->phpDocName;
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
        return new self($this->nativeName, true);
    }

    /**
     * @param TypeInterface $other
     * @return boolean
     */
    public function equals(TypeInterface $other): bool
    {
        return $other instanceof ScalarType && $this->nativeName === $other->getNativeName();
    }
}
