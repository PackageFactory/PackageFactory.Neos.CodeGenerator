<?php declare(strict_types=1);
namespace PackageFactory\Neos\CodeGenerator\Domain\Pattern;

/*
 * This file is part of the PackageFactory.Neos.CodeGenerator package
 */

use Neos\Flow\Annotations as Flow;
use Neos\Utility\ObjectAccess;

/**
 * @Flow\Proxy(false)
 */
final class Optional
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var mixed
     */
    private $value;

    public function __construct(string $name, $value)
    {
        $this->name = $name;
        $this->value = $value;
    }

    /**
     * @param string $path
     * @return Required
     */
    public function required(string $path): Required
    {
        return new Required($this->name . '.' . $path, ObjectAccess::getPropertyPath($this->value, $path));
    }

    /**
     * @param string $path
     * @return Optional
     */
    public function optional(string $path): Optional
    {
        return new self($this->name . '.' . $path, ObjectAccess::getPropertyPath($this->value, $path));
    }

    /**
     * @return null|string
     */
    public function string(): ?string
    {
        if (is_string($this->value)) {
            return $this->value;
        } elseif ($this->value === null) {
            return null;
        } else {
            throw new \InvalidArgumentException('"' . $this->name . '" was expected to be string or null but was ' . gettype($this->value) . '.');
        }
    }

    /**
     * @param string $pattern
     * @return null|string
     */
    public function regex(string $pattern): ?string
    {
        if (is_string($this->value)) {
            if (preg_match($pattern, $this->value)) {
                return $this->value;
            }

            throw new \InvalidArgumentException('"' . $this->name . '" was expected to match pattern "' . $pattern . '".');
        } elseif ($this->value === null) {
            return null;
        } else {
            throw new \InvalidArgumentException('"' . $this->name . '" was expected to be string or null but was ' . gettype($this->value) . '.');
        }
    }

    /**
     * @return null|integer
     */
    public function integer(): ?int
    {
        if (is_int($this->value)) {
            return $this->value;
        } elseif ($this->value === null) {
            return null;
        } else {
            throw new \InvalidArgumentException('"' . $this->name . '" was expected to be integer or null but was ' . gettype($this->value) . '.');
        }
    }

    /**
     * @return null|float
     */
    public function float(): ?float
    {
        if (is_float($this->value)) {
            return $this->value;
        } elseif ($this->value === null) {
            return null;
        } else {
            throw new \InvalidArgumentException('"' . $this->name . '" was expected to be float or null but was ' . gettype($this->value) . '.');
        }
    }

    /**
     * @return boolean
     */
    public function boolean(): bool
    {
        if (is_bool($this->value)) {
            return $this->value;
        } elseif ($this->value === null) {
            return null;
        } else {
            throw new \InvalidArgumentException('"' . $this->name . '" was expected to be boolean or null but was ' . gettype($this->value) . '.');
        }
    }

    /**
     * @return iterable<string, Required>
     */
    public function dictionary(): iterable
    {
        if (is_iterable($this->value)) {
            foreach ($this->value as $key => $child) {
                if (!is_string($key)) {
                    throw new \InvalidArgumentException('"' . $this->name . '" was expected to be dictionary or null but key of type ' . gettype($key) . ' was found.');
                }

                yield $key => new self($this->name . '.' . $key, $child);
            }
        } elseif ($this->value === null) {
            return null;
        } else {
            throw new \InvalidArgumentException('"' . $this->name . '" was expected to be dictionary or null but was ' . gettype($this->value) . '.');
        }
    }

    /**
     * @return iterable<int, Required>
     */
    public function list(): iterable
    {
        if (is_iterable($this->value)) {
            foreach ($this->value as $key => $child) {
                if (!is_int($key)) {
                    throw new \InvalidArgumentException('"' . $this->name . '" was expected to be list or null but key of type ' . gettype($key) . ' was found.');
                }

                yield new self($this->name . '.' . $key, $child);
            }
        } elseif ($this->value === null) {
            return null;
        } else {
            throw new \InvalidArgumentException('"' . $this->name . '" was expected to be list or null but was ' . gettype($this->value) . '.');
        }
    }
}
