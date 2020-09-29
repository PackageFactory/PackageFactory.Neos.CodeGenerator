<?php declare(strict_types=1);
namespace PackageFactory\Neos\CodeGenerator\Domain\Code\Php\Identifier;

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
    public function __construct(string $value)
    {
        if (!preg_match(
            '/^[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff\\\\]*[a-zA-Z0-9_\x7f-\xff]$/',
            $value
        )) {
            throw new \InvalidArgumentException('Invalid namespace "' . $value . '".');
        }

        $this->value = $value;
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
    public function asString(): string
    {
        return $this->value;
    }

    /**
     * @return PhpClassName
     */
    public function asClassName(): PhpClassName
    {
        return new PhpClassName('\\' . $this->value);
    }

    /**
     * @return Path
     */
    public function asPath(): Path
    {
        return Path::fromString(str_replace('\\', DIRECTORY_SEPARATOR, $this->value));
    }

    /**
     * @param string $value
     * @return self
     */
    public function append(string $value): self
    {
        return new self($this->value . '\\' . $value);
    }

    /**
     * @return self
     */
    public function truncateAscendant(PhpNamespace $ascendant): self
    {
        if (!$ascendant->isAscendantOf($this)) {
            throw new \DomainException('Cannot truncate non-ascendant namespace "' . $ascendant->asString() . '" from "' . $this->value . '".');
        }

        return new self(trim(substr($this->value, strlen($ascendant->asString())), '\\'));
    }

    /**
     * @param PhpNamespace $other
     * @return boolean
     */
    public function equals(PhpNamespace $other): bool
    {
        return $this->value === $other->asString();
    }

    /**
     * @param PhpNamespace $other
     * @return boolean
     */
    public function isAscendantOf(PhpNamespace $other): bool
    {
        return $other->isDescendantOf($this);
    }

    /**
     * @param PhpNamespace $other
     * @return boolean
     */
    public function isDescendantOf(PhpNamespace $other): bool
    {
        $otherAsString = $other->asString();
        return substr($this->value, 0, strlen($otherAsString)) === $otherAsString;
    }

    /**
     * @return null|PhpNamespace
     */
    public function getParentNamespace(): ?PhpNamespace
    {
        $segments = explode('\\', $this->value);
        array_pop($segments);

        if (count($segments) > 0) {
            return new self(join('\\', $segments));
        } else {
            return null;
        }
    }
}
