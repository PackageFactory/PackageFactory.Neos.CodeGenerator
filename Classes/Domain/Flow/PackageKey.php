<?php declare(strict_types=1);
namespace PackageFactory\Neos\CodeGenerator\Domain\Flow;

/*
 * This file is part of the PackageFactory.Neos.CodeGenerator package
 */

use Neos\Flow\Annotations as Flow;
use PackageFactory\Neos\CodeGenerator\Domain\Code\Php\PhpNamespace\PhpNamespace;

/**
 * @Flow\Proxy(false)
 */
final class PackageKey
{
    /**
     * @var string
     */
    private $value;

    /**
     * @param string $value
     */
    private function __construct(string $value)
    {
        if (!self::isValid($value)) {
            throw new \InvalidArgumentException('Invalid PackageKey "' . $value . '".');
        }

        $this->value = $value;
    }

    /**
     * @param string $string
     * @return self
     */
    public static function fromString(string $string): self
    {
        return new self($string);
    }

    /**
     * @param string $string
     * @return boolean
     */
    public static function isValid(string $string): bool
    {
        return (bool) preg_match(
            '/^[a-zA-Z][a-zA-Z0-9]+(\.[a-zA-Z][a-zA-Z0-9]+)+$/',
            $string
        );
    }

    /**
     * @return string
     */
    public function asString(): string
    {
        return $this->value;
    }

    /**
     * @return PhpNamespace
     */
    public function asPhpNamespace(): PhpNamespace
    {
        return PhpNamespace::fromString(str_replace('.', '\\', $this->value));
    }
}
