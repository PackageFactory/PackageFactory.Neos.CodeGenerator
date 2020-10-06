<?php declare(strict_types=1);
namespace PackageFactory\Neos\CodeGenerator\Domain\Input;

/*
 * This file is part of the PackageFactory.Neos.CodeGenerator package
 */

use Neos\Flow\Annotations as Flow;
use Neos\Utility\ObjectAccess;
use PackageFactory\Neos\CodeGenerator\Domain\Input\TypeTemplate\TypeTemplate;

/**
 * @Flow\Proxy(false)
 */
final class Required
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var mixed
     */
    private $value;

    /**
     * @param string $name
     * @param mixed $value
     */
    public function __construct(string $name, $value)
    {
        if ($value === null) {
            throw new \InvalidArgumentException('"' . $name . '" is required but was null.');
        }

        $this->name = $name;
        $this->value = $value;
    }

    /**
     * @param string $path
     * @return Required
     */
    public function required(string $path): Required
    {
        return new self($this->name . '.' . $path, ObjectAccess::getPropertyPath($this->value, $path));
    }

    /**
     * @param string $path
     * @return Optional
     */
    public function optional(string $path): Optional
    {
        return new Optional($this->name . '.' . $path, ObjectAccess::getPropertyPath($this->value, $path));
    }

    /**
     * @return string
     */
    public function string(): string
    {
        if (is_string($this->value)) {
            return $this->value;
        }

        throw new \InvalidArgumentException('"' . $this->name . '" was expected to be string but was ' . gettype($this->value) . '.');
    }

    /**
     * @param string $pattern
     * @return string
     */
    public function regex(string $pattern): string
    {
        if (is_string($this->value) && preg_match($pattern, $this->value)) {
            return $this->value;
        }

        throw new \InvalidArgumentException('"' . $this->name . '" was expected to match pattern "' . $pattern . '".');
    }

    /**
     * @return TypeTemplate
     */
    public function type(): TypeTemplate
    {
        return TypeTemplate::fromString($this->string());
    }

    /**
     * @return integer
     */
    public function integer(): int
    {
        if (is_int($this->value)) {
            return $this->value;
        }

        throw new \InvalidArgumentException('"' . $this->name . '" was expected to be integer but was ' . gettype($this->value) . '.');
    }

    /**
     * @return float
     */
    public function float(): float
    {
        if (is_float($this->value)) {
            return $this->value;
        }

        throw new \InvalidArgumentException('"' . $this->name . '" was expected to be float but was ' . gettype($this->value) . '.');
    }

    /**
     * @return boolean
     */
    public function boolean(): bool
    {
        if (is_bool($this->value)) {
            return $this->value;
        }

        throw new \InvalidArgumentException('"' . $this->name . '" was expected to be boolean but was ' . gettype($this->value) . '.');
    }

    /**
     * @return \Iterator<string, Required>
     */
    public function dictionary(): \Iterator
    {
        if (is_iterable($this->value)) {
            foreach ($this->value as $key => $child) {
                if (!is_string($key)) {
                    throw new \InvalidArgumentException('"' . $this->name . '" was expected to be dictionary but key of type ' . gettype($key) . ' was found.');
                }

                yield $key => new self($this->name . '.' . $key, $child);
            }
        } else {
            throw new \InvalidArgumentException('"' . $this->name . '" was expected to be dictionary but was ' . gettype($this->value) . '.');
        }
    }

    /**
     * @return \Iterator<int, Required>
     */
    public function list(): \Iterator
    {
        if (is_iterable($this->value)) {
            foreach ($this->value as $key => $child) {
                if (!is_int($key)) {
                    throw new \InvalidArgumentException('"' . $this->name . '" was expected to be list but key of type ' . gettype($key) . ' was found.');
                }

                yield new self($this->name . '.' . $key, $child);
            }
        } else {
            throw new \InvalidArgumentException('"' . $this->name . '" was expected to be list but was ' . gettype($this->value) . '.');
        }
    }

    /**
     * @return array<mixed>
     */
    public function array(): array
    {
        if (is_array($this->value)) {
            return $this->value;
        } else {
            throw new \InvalidArgumentException('"' . $this->name . '" was expected to be array but was ' . gettype($this->value) . '.');
        }
    }
}
