<?php declare(strict_types=1);
namespace PackageFactory\Neos\CodeGenerator\Domain\Pattern;

/*
 * This file is part of the PackageFactory.Neos.CodeGenerator package
 */

use Neos\Flow\Annotations as Flow;

/**
 * @Flow\Proxy(false)
 */
final class PatternConfigurationIsInvalid extends \DomainException
{
    /**
     * @param string $message
     */
    private function __construct(string $key, string $message)
    {
        parent::__construct('Pattern configuration for "' . $key . '" is invalid: ' . $message);
    }

    /**
     * @param string $key
     * @return self
     */
    public static function becauseItDoesNotProvideAGeneratorClassName(string $key): self
    {
        throw new self($key, 'generatorClassName is missing.');
    }

    /**
     * @param string $key
     * @param string $className
     * @return self
     */
    public static function becauseItsGeneratorClassDoesNotExist(string $key, string $className): self
    {
        throw new self($key, 'The class provided for generatorClassName "' . $className . '" does not exist.');
    }

    /**
     * @param string $key
     * @param string $className
     * @return self
     */
    public static function becauseItsGeneratorClassDoesNotImplementGeneratorInterface(string $key, string $className): self
    {
        throw new self($key, 'The class provided for generatorClassName "' . $className . '" does not implement "' . GeneratorInterface::class . '".');
    }
}
