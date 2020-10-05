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
     * @var \DateTimeImmutable
     */
    private $dateTime;

    /**
     * @param array<mixed> $data
     */
    private function __construct(array $data, \DateTimeImmutable $dateTime)
    {
        $this->data = $data;
        $this->dateTime = $dateTime;
    }

    /**
     * @param array<mixed> $array
     * @return self
     */
    public static function fromArray(array $array): self
    {
        return new self($array, new \DateTimeImmutable());
    }

    /**
     * @param array<mixed> $array
     * @param \DateTimeImmutable $dateTime
     * @return self
     */
    public static function fromArrayAtSpecificPointInTime(array $array, \DateTimeImmutable $dateTime): self
    {
        return new self($array, $dateTime);
    }



    /**
     * @return \DateTimeImmutable
     */
    public function now(): \DateTimeImmutable
    {
        return $this->dateTime;
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
