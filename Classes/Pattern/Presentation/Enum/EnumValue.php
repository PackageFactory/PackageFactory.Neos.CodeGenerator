<?php declare(strict_types=1);
namespace PackageFactory\Neos\CodeGenerator\Pattern\Presentation\Enum;

/*
 * This file is part of the PackageFactory.Neos.CodeGenerator package
 */

use Neos\Flow\Annotations as Flow;
use PackageFactory\Neos\CodeGenerator\Framework\Util\StringUtil;

/**
 * @Flow\Proxy(false)
 */
final class EnumValue
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $value;

    /**
     * @param string $name
     * @param string $value
     */
    public function __construct(string $name, string $value)
    {
        $this->name = $name;
        $this->value = $value;
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
    public function getValue(): string
    {
        return $this->value;
    }

    /**
     * @param string $enumName
     * @return string
     */
    public function asConstantName(string $enumName): string
    {
        return strtoupper(StringUtil::camelCaseToSnakeCase($enumName)) .  '_' .  strtoupper($this->name);
    }

    /**
     * @return string
     */
    public function asStaticFactoryMethodName(): string
    {
        return lcfirst($this->name);
    }

    /**
     * @return string
     */
    public function asComparatorMethodName(): string
    {
        return 'is' . ucfirst($this->name);
    }
}
