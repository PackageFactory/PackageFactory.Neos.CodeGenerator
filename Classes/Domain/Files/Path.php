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
     * @return string
     */
    public function getValue(): string
    {
        return $this->value;
    }

    /**
     * @return Path
     */
    public function getParentDirectoryPath(): Path
    {
        return new self(dirname($this->value));
    }

    /**
     * @param Path $other
     * @return Path
     */
    public function append(Path $other): Path
    {
        return $this->appendString($other->getValue());
    }

    /**
     * @param string $string
     * @return Path
     */
    public function appendString(string $string): Path
    {
        return new self($this->value . DIRECTORY_SEPARATOR . ltrim($string, DIRECTORY_SEPARATOR));
    }
}
