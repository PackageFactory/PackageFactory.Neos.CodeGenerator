<?php declare(strict_types=1);
namespace PackageFactory\Neos\CodeGenerator\Domain\Code\Fusion\Identifier;

/*
 * This file is part of the PackageFactory.Neos.CodeGenerator package
 */

use Neos\Flow\Annotations as Flow;

/**
 * @Flow\Proxy(false)
 */
final class PrototypeName
{
    public const PATTERN = '/^[a-zA-Z0-9:.]+$/';

    /**
     * @var string
     */
    private $value;

    /**
     * @param string $value
     */
    private function __construct(string $value)
    {
        if (!preg_match(self::PATTERN, $value)) {
            throw new \DomainException('Prototype name must match pattern ' . self::PATTERN . '.');
        }

        $this->value = $value;
    }

    /**
     * @param string $value
     * @return self
     */
    public static function fromString(string $value): self
    {
        return new self($value);
    }

    /**
     * @return null|string
     */
    public function getPackageKey(): ?string
    {
        $segments = explode(':', $this->value);

        if (count($segments) === 2) {
            return $segments[0];
        } else {
            return null;
        }
    }

    /**
     * @return string
     */
    public function asString(): string
    {
        return $this->value;
    }
}
