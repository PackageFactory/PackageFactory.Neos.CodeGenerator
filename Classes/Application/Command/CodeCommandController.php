<?php declare(strict_types=1);
namespace PackageFactory\Neos\CodeGenerator\Application\Command;

/*
 * This file is part of the PackageFactory.Neos.CodeGenerator package
 */

use Neos\Flow\Annotations as Flow;
use Neos\Flow\Cli\CommandController;
use PackageFactory\Neos\CodeGenerator\Domain\Pattern\GeneratorQuery;
use PackageFactory\Neos\CodeGenerator\Domain\Pattern\PatternRepository;
use PackageFactory\Neos\CodeGenerator\Infrastructure\GeneratorResolver;
use PackageFactory\Neos\CodeGenerator\Infrastructure\PackageResolver;
use PackageFactory\Neos\CodeGenerator\Infrastructure\PatternResolver;

/**
 * Command controller for code generation
 */
class CodeCommandController extends CommandController
{
    /**
     * @Flow\Inject
     * @var PatternResolver
     */
    protected $patternResolver;

    /**
     * @Flow\Inject
     * @var PatternRepository
     */
    protected $patternRepository;

    /**
     * @Flow\Inject
     * @var PackageResolver
     */
    protected $packageResolver;

    /**
     * @Flow\Inject
     * @var GeneratorResolver
     */
    protected $generatorResolver;

    /**
     * Generates code with the given pattern in the given package
     *
     * @param string $patternKey A pattern key (see ./flow code:listpatterns) or "?". The latter will result in prompt in which a pattern can be manually selected.
     * @param string $packageKey A package key, "." or "?". "." will automatically choose a default package, if available. "?" will result in a prompt in which a package can be manually selected.
     * @return void
     */
    public function generateCommand(string $patternKey, string $packageKey): void
    {
        $pattern = $this->patternResolver->resolve($patternKey);
        $generator = $this->generatorResolver->resolve($pattern);

        $package = $this->packageResolver->resolve($packageKey);
        $query = GeneratorQuery::fromFlowPackageAndCliRequest($package, $this->request);

        $generator->generate($query);

        $this->outputLine();
        $this->outputLine('<success>Done!</success>');
    }

    /**
     * Shows a list of all available patterns as configured in Settings path "PackageFactory.Neos.CodeGenerator.patterns".
     *
     * @return void
     */
    public function listPatternsCommand(): void
    {
        $this->outputLine();
        $this->outputLine('List of all available code patterns');
        $this->outputLine('===================================');
        $this->outputLine();

        $headers = ['Pattern Key', 'Short Description'];
        $rows = [];
        foreach ($this->patternRepository->findAll() as $pattern) {
            $rows[] = [$pattern->getKey(), $pattern->getShortDescription()];
        }

        $this->output->outputTable($rows, $headers);
        $this->outputLine();
        $this->outputLine('Usage:');
        $this->outputFormatted('<b>./flow code:generate <em>{Pattern Key}</em> <em>{Package Key}</em> ...</b>');
    }

    /**
     * Show some helpful documentation for the given pattern
     *
     * @param string $patternKey A pattern key (see ./flow code:listpatterns) or "?". The latter will result in prompt in which a pattern can be manually selected.
     * @return void
     */
    public function describePatternCommand(string $patternKey): void
    {
        $pattern = $this->patternResolver->resolve($patternKey);

        $this->outputLine();
        $this->outputFormatted('<em> %s - Summary </em>', [$pattern->getKey()]);
        $this->outputLine();

        $this->outputFormatted('  <b>Short description</b>');
        $this->outputFormatted($pattern->getShortDescription(), [], 4);
        $this->outputLine();

        $this->outputFormatted('  <b>Description</b>');
        $this->outputFormatted($pattern->getDescription(), [], 4);
        $this->outputLine();

        $this->outputFormatted('  <b>Arguments</b>');
        foreach ($pattern->getArguments() as $key => $description) {
            $this->outputFormatted('#' . $key . ' - ' . $description, [], 4);
        }
        $this->outputLine();

        $this->outputFormatted('  <b>Usage Example</b>');
        $this->outputFormatted($pattern->getUsageExample(), [], 4);
        $this->outputLine();
    }
}
