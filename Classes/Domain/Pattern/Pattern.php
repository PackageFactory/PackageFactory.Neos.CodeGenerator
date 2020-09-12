<?php declare(strict_types=1);
namespace PackageFactory\Neos\CodeGenerator\Domain\Pattern;

/*
 * This file is part of the PackageFactory.Neos.CodeGenerator package
 */

use Neos\Flow\Annotations as Flow;

/**
 * @Flow\Proxy(false)
 */
final class Pattern
{
    /**
     * @var string
     */
    private $key;

    /**
     * @var string
     */
    private $shortDescription;

    /**
     * @var string
     */
    private $description;

    /**
     * @var array
     */
    private $arguments;

    /**
     * @var string
     */
    private $usageExample;

    /**
     * @var string
     */
    private $generatorClassName;

    /**
     * @param string $key
     * @param string $shortDescription
     * @param string $description
     * @param array $arguments
     * @param string $usageExample
     * @param string $generatorClassName
     */
    private function __construct(
        string $key,
        string $shortDescription,
        string $description,
        array $arguments,
        string $usageExample,
        string $generatorClassName
    ) {
        $this->key = $key;
        $this->shortDescription = $shortDescription;
        $this->description = $description;
        $this->arguments = $arguments;
        $this->usageExample = $usageExample;
        $this->generatorClassName = $generatorClassName;
    }

    /**
     * @param array $configuration
     * @return self
     */
    public static function fromConfiguration(string $key, array $configuration): self
    {
        if (!isset($configuration['generatorClassName']) || empty($configuration['generatorClassName'])) {
            throw PatternConfigurationIsInvalid::
                becauseItDoesNotProvideAGeneratorClassName($key);
        }

        $generatorClassName = trim($configuration['generatorClassName']);

        if (!class_exists($generatorClassName)) {
            throw PatternConfigurationIsInvalid::
                becauseItsGeneratorClassDoesNotExist($key, $generatorClassName);
        }

        if (!is_subclass_of($generatorClassName, GeneratorInterface::class)) {
            throw PatternConfigurationIsInvalid::
                becauseItsGeneratorClassDoesNotImplementGeneratorInterface($key, $generatorClassName);
        }

        $shortDescription = trim($configuration['shortDescription'] ?? $configuration['description'] ?? '- No description available -');
        $description = trim($configuration['description'] ?? '- No description available -');
        $arguments = $configuration['arguments'] ?? [];
        $usageExample = trim($configuration['usageExample'] ?? '- No usage example available -');

        return new self($key, $shortDescription, $description, $arguments, $usageExample, $generatorClassName);
    }

    /**
     * @return string
     */
    public function getKey(): string
    {
        return $this->key;
    }

    /**
     * @return string
     */
    public function getShortDescription(): string
    {
        return $this->shortDescription;
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @return array
     */
    public function getArguments(): array
    {
        return $this->arguments;
    }

    /**
     * @return string
     */
    public function getUsageExample(): string
    {
        return $this->usageExample;
    }

    /**
     * @return string
     */
    public function getGeneratorClassName(): string
    {
        return $this->generatorClassName;
    }
}
