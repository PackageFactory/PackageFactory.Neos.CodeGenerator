<?php declare(strict_types=1);
namespace PackageFactory\Neos\CodeGenerator\Pattern\Presentation\Component\PropType;

/*
 * This file is part of the PackageFactory.Neos.CodeGenerator package
 */

use Neos\Flow\Annotations as Flow;

/**
 * @Flow\Proxy(false)
 */
final class LeafPropType implements PropTypeInterface
{
    public const NAMES = ['bool', 'boolean', 'int', 'integer', 'float', 'string', 'unknown'];

    /**
     * @var string
     */
    private $exampleValue;

    /**
     * @param string $exampleValue
     */
    public function __construct(string $exampleValue)
    {
        $this->exampleValue = $exampleValue;
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
     * @return string
     */
    public function asExampleValue(): string
    {
        return $this->exampleValue;
    }

    /**
     * @return string
     */
    public function asDummyAfxMarkup(): string
    {
        return '{%s}';
    }
}
