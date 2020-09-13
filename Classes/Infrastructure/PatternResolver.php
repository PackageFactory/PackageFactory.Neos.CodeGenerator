<?php declare(strict_types=1);
namespace PackageFactory\Neos\CodeGenerator\Infrastructure;

/*
 * This file is part of the PackageFactory.Neos.CodeGenerator package
 */

use Neos\Flow\Annotations as Flow;
use Neos\Flow\Cli\ConsoleOutput;
use PackageFactory\Neos\CodeGenerator\Domain\Pattern\Pattern;
use PackageFactory\Neos\CodeGenerator\Domain\Pattern\PatternRepository;

/**
 * @Flow\Scope("singleton")
 */
final class PatternResolver
{
    /**
     * @Flow\Inject
     * @var ConsoleOutput
     */
    protected $output;

    /**
     * @Flow\Inject
     * @var PatternRepository
     */
    protected $patternRepository;

    /**
     * @param string $input
     * @return Pattern
     */
    public function resolve(string $input): Pattern
    {
        if ($input === '.') {
            return $this->resolveFromPrompt();
        } else {
            return $this->resolveFromPatternKey($input);
        }
    }

    /**
     * @return Pattern
     */
    public function resolveFromPrompt(): Pattern
    {
        /** @var string $patternKey */
        $patternKey = $this->output->select(
            'Which code pattern shall be used?',
            $this->patternRepository->findAllKeys()
        );

        return $this->resolveFromPatternKey($patternKey);
    }

    /**
     * @param string $patternKey
     * @return Pattern
     */
    public function resolveFromPatternKey(string $patternKey): Pattern
    {
        if ($pattern = $this->patternRepository->findOneByPatternKey($patternKey)) {
            return $pattern;
        }

        throw new \InvalidArgumentException('No code pattern with key "' . $patternKey . '" could be found.');
    }
}
