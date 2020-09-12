<?php declare(strict_types=1);
namespace PackageFactory\Neos\CodeGenerator\Domain\Code;

/*
 * This file is part of the PackageFactory.Neos.CodeGenerator package
 */

use Neos\Flow\Annotations as Flow;
use Neos\Flow\Package\FlowPackageInterface;
use PackageFactory\Neos\CodeGenerator\Domain\Files\Path;

/**
 * @Flow\Proxy(false)
 */
final class PhpNamespace
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
        if (!preg_match(
            '/^[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff\\\\]*[a-zA-Z0-9_\x7f-\xff]$/',
            $value
        )) {
            throw new \DomainException('@TODO: Invalid namespace');
        }

        $this->value = ltrim($value, '\\');
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
     * @return self
     */
    public static function fromPath(string $path): self
    {
        return new self(str_replace(DIRECTORY_SEPARATOR, '\\', $path));
    }

    /**
     * @param FlowPackageInterface $flowPackage
     * @return self
     */
    public static function fromFlowPackage(FlowPackageInterface $flowPackage): self
    {
        return new self(str_replace('.', '\\', $flowPackage->getPackageKey()));
    }

    /**
     * @return string
     */
    public function getValue(): string
    {
        return $this->value;
    }

    /**
     * @return string
     */
    public function asPath(): Path
    {
        return Path::fromString(str_replace('\\', DIRECTORY_SEPARATOR, $this->value));
    }

    /**
     * @param PhpNamespace $other
     * @return boolean
     */
    public function isSubNamespaceOf(PhpNamespace $other): bool
    {
        $otherValue = $other->getValue();

        return \mb_substr($this->value, 0, \mb_strlen($otherValue)) === $otherValue;
    }

    /**
     * @param FlowPackageInterface $flowPackage
     * @return boolean
     */
    public function belongsToFlowPackage(FlowPackageInterface $flowPackage): bool
    {
        return $this->isSubNamespaceOf(self::fromFlowPackage($flowPackage));
    }

    /**
     * @param PhpNamespace $other
     * @return self
     */
    public function prepend(PhpNamespace $other): self
    {
        return $this->prependString($other->getValue());
    }

    /**
     * @param string $string
     * @return self
     */
    public function prependString(string $string): self
    {
        return new self($string . '\\' . $this->value);
    }

    /**
     * @param PhpNamespace $other
     * @return self
     */
    public function append(PhpNamespace $other): self
    {
        return $this->appendString($other->getValue());
    }

    /**
     * @param string $string
     * @return self
     */
    public function appendString(string $string): self
    {
        return new self($this->value . '\\' . $string);
    }
}
