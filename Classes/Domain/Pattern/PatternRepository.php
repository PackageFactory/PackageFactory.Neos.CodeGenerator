<?php declare(strict_types=1);
namespace PackageFactory\Neos\CodeGenerator\Domain\Pattern;

/*
 * This file is part of the PackageFactory.Neos.CodeGenerator package
 */

use Neos\Flow\Annotations as Flow;

/**
 * @Flow\Scope("singleton")
 */
class PatternRepository
{
    /**
     * @Flow\InjectConfiguration(path="patterns")
     * @var array<mixed>
     */
    protected $patternConfigurations;

    /**
     * @return array<string>
     */
    public function findAllKeys(): array
    {
        return array_keys($this->patternConfigurations);
    }

    /**
     * @return \Iterator<int, Pattern>
     */
    public function findAll(): \Iterator
    {
        foreach ($this->patternConfigurations as $patternKey => $patternConfiguration) {
            yield Pattern::fromConfiguration($patternKey, $patternConfiguration);
        }
    }

    /**
     * @param string $patternKey
     * @return Pattern|null
     */
    public function findOneByPatternKey(string $patternKey): ?Pattern
    {
        if (isset($this->patternConfigurations[$patternKey])) {
            return Pattern::fromConfiguration($patternKey, $this->patternConfigurations[$patternKey]);
        }

        return null;
    }
}
