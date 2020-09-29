<?php declare(strict_types=1);
namespace PackageFactory\Neos\CodeGenerator\Domain\Files;

/*
 * This file is part of the PackageFactory.Neos.CodeGenerator package
 */

use Neos\Flow\Annotations as Flow;
use Neos\Flow\Package\FlowPackageInterface;

/**
 * @Flow\Proxy(false)
 */
final class Path
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
        $this->value = rtrim($value, '/\\');
        $this->value = str_replace('/', DIRECTORY_SEPARATOR, $this->value);
        $this->value = str_replace('\\', DIRECTORY_SEPARATOR, $this->value);
    }

    /**
     * @deprecated
     * @param FlowPackageInterface $flowPackage
     * @return self
     */
    public static function fromFlowPackage(FlowPackageInterface $flowPackage): self
    {
        return new self($flowPackage->getPackagePath());
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
     * @deprecated
     * @return string
     */
    public function getValue(): string
    {
        return $this->value;
    }

    /**
     * @return string
     */
    public function asString(): string
    {
        return $this->value;
    }

    /**
     * @return Path
     */
    public function getParentDirectoryPath(): self
    {
        return new self(dirname($this->value));
    }

    /**
     * @param Path $other
     * @return Path
     */
    public function append(Path $other): self
    {
        return $this->appendString($other->asString());
    }

    /**
     * @param string $string
     * @return Path
     */
    public function appendString(string $string): self
    {
        return new self($this->value . DIRECTORY_SEPARATOR . ltrim($string, DIRECTORY_SEPARATOR));
    }

    /**
     * @param string $extension
     * @return self
     */
    public function withExtension(string $extension): self
    {
        if ($extension[0] === '.' || strpos($extension, DIRECTORY_SEPARATOR) !== false) {
            throw new \DomainException('"' . $extension . '" is not a valid extension.');
        }

        return new self(substr($this->value, 0, strlen($this->value) - strrpos($this->value, '0')) . '.' . $extension);
    }
}
