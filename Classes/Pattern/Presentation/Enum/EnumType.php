<?php declare(strict_types=1);
namespace PackageFactory\Neos\CodeGenerator\Pattern\Presentation\Enum;

/*
 * This file is part of the PackageFactory.Neos.CodeGenerator package
 */

use Neos\Flow\Annotations as Flow;

/**
 * @Flow\Proxy(false)
 */
final class EnumType
{
    public const ENUM_TYPE_STRING = 'string';
    public const ENUM_TYPE_INTEGER = 'int';

    /**
     * @var string
     */
    private $value;

    /**
     * @param string $value
     */
    private function __construct(string $value)
    {
        $this->value = $value;
    }

    /**
     * @param string $string
     * @return self
     */
    public static function fromString(string $string): self
    {
        if (!in_array($string, self::getValues())) {
            throw EnumTypeIsInvalid::becauseItMustBeOneOfTheDefinedConstants($string);
        }

        return new self($string);
    }

    /**
     * @return array|string[]
     */
    public static function getValues(): array
    {
        return [
            self::ENUM_TYPE_STRING,
            self::ENUM_TYPE_INTEGER,
        ];
    }

    /**
     * @return boolean
     */
    public function isInteger(): bool
    {
        return $this->value === self::ENUM_TYPE_INTEGER;
    }

    /**
     * @return boolean
     */
    public function isString(): bool
    {
        return $this->value === self::ENUM_TYPE_STRING;
    }

    /**
     * @return string
     */
    public function asString(): string
    {
        return $this->value;
    }

    /**
     * @return string
     */
    public function asVariableName(): string
    {
        switch ($this->value) {
            case self::ENUM_TYPE_INTEGER:
                return 'integer';
            default:
                return $this->value;
        }
    }

    /**
     * @return string
     */
    public function asStaticFactoryMethodName(): string
    {
        switch ($this->value) {
            case self::ENUM_TYPE_INTEGER:
                return 'fromInteger';
            default:
                return 'from' . ucfirst($this->value);
        }
    }

    /**
     * @return string
     */
    public function asPhpDocType(): string
    {
        switch ($this->value) {
            case self::ENUM_TYPE_INTEGER:
                return 'integer';
            default:
                return $this->value;
        }
    }

    /**
     * @return string
     */
    public function asPhpTypeHint(): string
    {
        switch ($this->value) {
            case self::ENUM_TYPE_INTEGER:
                return 'int';
            default:
                return $this->value;
        }
    }

    /**
     * @param EnumValue $value
     * @return string
     */
    public function quoteValue(EnumValue $value): string
    {
        switch ($this->value) {
            case self::ENUM_TYPE_STRING:
                return '\'' . $value->getValue() . '\'';
            default:
                return $value->getValue();
        }
    }

    /**
     * @param string $variableName
     * @return string
     */
    public function castVariableToString(string $variableName): string
    {
        switch ($this->value) {
            case self::ENUM_TYPE_STRING:
                return '$' . $variableName;
            default:
                return '(string) $' . $variableName;
        }
    }
}
