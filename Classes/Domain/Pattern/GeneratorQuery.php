<?php declare(strict_types=1);
namespace PackageFactory\Neos\CodeGenerator\Domain\Pattern;

/*
 * This file is part of the PackageFactory.Neos.CodeGenerator package
 */

use Neos\Flow\Annotations as Flow;
use Neos\Flow\Cli\Request as CliRequest;

/**
 * @Flow\Proxy(false)
 */
final class GeneratorQuery
{
    /**
     * @phpstan-var array<int, string>
     * @var array
     */
    private $arguments;

    /**
     * @phpstan-var array<string, string>
     * @var array
     */
    private $options;

    /**
     * @param array<int, string> $arguments
     * @param array<string, string> $options
     */
    private function __construct(
        array $arguments,
        array $options
    ) {
        $this->arguments = $arguments;
        $this->options = $options;
    }

    /**
     * @param CliRequest $cliRequest
     * @return self
     */
    public static function fromCliRequest(CliRequest $cliRequest): self
    {
        /** @phpstan-var array<int, string> $arguments */
        $arguments = $cliRequest->getExceedingArguments();
        /** @phpstan-var array<string, string> $options */
        $options = $cliRequest->getArguments();

        return new self($arguments, $options);
    }

    /**
     * @param integer $index
     * @param string $errorMessage
     * @return string
     */
    public function getArgument(int $index, string $errorMessage): string
    {
        if (isset($this->arguments[$index])) {
            return $this->arguments[$index];
        }

        throw new \InvalidArgumentException($errorMessage);
    }

    /**
     * @return self
     */
    public function shiftArgument(): self
    {
        return new self(array_slice($this->arguments, 1), $this->options);
    }

    /**
     * @param integer $offset
     * @return array<int, string>
     */
    public function getRemainingArguments(int $offset): array
    {
        return array_slice($this->arguments, $offset);
    }

    /**
     * @param string $key
     * @param string $default
     * @return string
     */
    public function getOption(string $key, string $default): string
    {
        if (isset($this->options[$key])) {
            return $this->options[$key];
        } else {
            return $default;
        }
    }
}
