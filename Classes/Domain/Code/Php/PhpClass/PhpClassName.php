<?php declare(strict_types=1);
namespace PackageFactory\Neos\CodeGenerator\Domain\Code\Php\PhpClass;

/*
 * This file is part of the PackageFactory.Neos.CodeGenerator package
 */

use Neos\Flow\Annotations as Flow;
use PackageFactory\Neos\CodeGenerator\Domain\Code\Php\PhpNamespace\PhpNamespace;
use PackageFactory\Neos\CodeGenerator\Framework\Util\StringUtil;

/**
 * @Flow\Proxy(false)
 */
final class PhpClassName
{
    /**
     * @phpstan-var class-string
     * @var string
     */
    private $value;

    /**
     * @param string $value
     */
    private function __construct(string $value)
    {
        if (!self::isValid($value)) {
            throw new \InvalidArgumentException('Invalid PhpClassName "' . $value . '".');
        }

        // @phpstan-ignore-next-line
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
            '/^[\\\\][a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff\\\\]*[a-zA-Z0-9_\x7f-\xff]$/',
            $string
        );
    }

    /**
     * @phpstan-return class-string
     * @return string
     */
    public function asFullyQualifiedNameString(): string
    {
        return $this->value;
    }

    /**
     * @return string
     */
    public function asDeclarationNameString(): string
    {
        $segments = explode('\\', $this->value);
        $lastSegment = array_pop($segments);

        assert($lastSegment !== null);

        return StringUtil::tail('\\', $lastSegment);
    }

    /**
     * @return PhpNamespace
     */
    public function asNamespace(): PhpNamespace
    {
        return PhpNamespace::fromString(ltrim($this->value, '\\'));
    }

    /**
     * @param string $suffix
     * @return PhpClassName
     */
    public function append(string $suffix): PhpClassName
    {
        return new self($this->value . $suffix);
    }

    /**
     * @param string $declarationName
     * @return self
     */
    public function replaceDeclarationNameWith(string $declarationName): self
    {
        $segments = explode('\\', $this->value);
        array_pop($segments);
        $segments[] = $declarationName;

        return new self(join('\\', $segments));
    }
}
