<?php declare(strict_types=1);
namespace PackageFactory\Neos\CodeGenerator\Domain\Input;

/*
 * This file is part of the PackageFactory.Neos.CodeGenerator package
 */

use Neos\Flow\Annotations as Flow;
use Neos\Utility\ObjectAccess;

/**
 * @Flow\Proxy(false)
 */
final class Query
{
    /**
     * @var array<mixed>
     */
    private $data;

    /**
     * @param array<mixed> $data
     */
    private function __construct(array $data)
    {
        $this->data = $data;
    }

    /**
     * @param array<mixed> $array
     * @return self
     */
    public static function fromArray(array $array): self
    {
        return new self($array);
    }

    /**
     * @param string $path
     * @return Required
     */
    public function required(string $path): Required
    {
        return new Required($path, ObjectAccess::getPropertyPath($this->data, $path));
    }

    /**
     * @param string $path
     * @return Optional
     */
    public function optional(string $path): Optional
    {
        return new Optional($path, ObjectAccess::getPropertyPath($this->data, $path));
    }
}
