<?php declare(strict_types=1);
namespace PackageFactory\Neos\CodeGenerator\Application\Command;

/*
 * This file is part of the PackageFactory.Neos.CodeGenerator package
 */

use Neos\Flow\Annotations as Flow;
use Neos\Flow\Cli\CommandController;
use PackageFactory\Neos\CodeGenerator\Domain\Input\Query;
use PackageFactory\Neos\CodeGenerator\Domain\Pattern\PatternRepository;
use PackageFactory\Neos\CodeGenerator\Framework\IO\ConsoleIO;
use PackageFactory\Neos\CodeGenerator\Infrastructure\GeneratorResolver;
use PackageFactory\Neos\CodeGenerator\Infrastructure\PatternResolver;
use Symfony\Component\Yaml;

/**
 * Command controller for code generation
 */
class PatternCommandController extends CommandController
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
     * @var GeneratorResolver
     */
    protected $generatorResolver;

    /**
     * @Flow\Inject
     * @var ConsoleIO
     */
    protected $io;

    /**
     * Generates code with the given pattern in the given package
     *
     * @param string $fileName
     * @return void
     */
    public function generateCommand(string $fileName): void
    {
        $input = file_get_contents($fileName);

        if ($input = file_get_contents($fileName)) {
            $parser = new Yaml\Parser();
            $data = $parser->parse($input);

            foreach ($data as $item) {
                foreach ($item as $patternKey => $options) {
                    $pattern = $this->patternResolver->resolve($patternKey);
                    $generator = $this->generatorResolver->resolve($pattern);
                    $query = Query::fromArray($options);

                    $this->outputLine();
                    $this->outputLine('<em> Running %s... </em>', [$pattern->getKey()]);
                    $this->outputLine();

                    $generator->generate($query);

                    $this->outputLine();
                }
            }

            $this->outputLine('<success> Done! </success>');
        } else {
            $this->outputLine();
            $this->outputLine('<error> No Input given for %s. </error>');
            $this->outputLine('<em> See ./flow pattern:list a list of patterns. </em>');
            $this->outputLine();
            $this->quit(-1);
        }
    }

    /**
     * Shows a list of all available patterns as configured in Settings path "PackageFactory.Neos.CodeGenerator.patterns".
     *
     * @return void
     */
    public function listCommand(): void
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
        $this->outputFormatted('<b>./flow pattern:generate <em>{Pattern Key}</em> <em>{Package Key}</em> ...</b>');
    }

    /**
     * Show some helpful documentation for the given pattern
     *
     * @param string $patternKey A pattern key (see ./flow pattern:list) or "?". The latter will result in prompt in which a pattern can be manually selected.
     * @return void
     */
    public function describeCommand(string $patternKey): void
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

    /**
     * @return void
     */
    public function configtestCommand(): void
    {
        throw new \Exception('@TODO: configtestCommand');
    }
}
