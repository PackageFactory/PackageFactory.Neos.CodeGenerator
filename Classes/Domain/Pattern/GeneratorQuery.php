<?php declare(strict_types=1);
namespace PackageFactory\Neos\CodeGenerator\Domain\Pattern;

/*
 * This file is part of the PackageFactory.Neos.CodeGenerator package
 */

use Neos\Flow\Annotations as Flow;
use Neos\Flow\Cli\Request as CliRequest;
use Neos\Flow\Package\FlowPackageInterface;

/**
 * @Flow\Proxy(false)
 */
final class GeneratorQuery
{
    /**
     * @var FlowPackageInterface
     */
    private $flowPackage;

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
     * @param FlowPackageInterface $flowPackage
     * @param array<int, string> $arguments
     * @param array<string, string> $options
     */
    private function __construct(
        FlowPackageInterface $flowPackage,
        array $arguments,
        array $options
    ) {
        $this->flowPackage = $flowPackage;
        $this->arguments = $arguments;
        $this->options = $options;
    }

    /**
     * @param FlowPackageInterface $flowPackage
     * @param CliRequest $cliRequest
     * @return self
     */
    public static function fromFlowPackageAndCliRequest(FlowPackageInterface $flowPackage, CliRequest $cliRequest): self
    {
        /** @phpstan-var array<int, string> $arguments */
        $arguments = $cliRequest->getExceedingArguments();
        /** @phpstan-var array<string, string> $options */
        $options = $cliRequest->getArguments();

        return new self($flowPackage, $arguments, $options);
    }

    /**
     * @return FlowPackageInterface
     */
    public function getFlowPackage(): FlowPackageInterface
    {
        return $this->flowPackage;
    }

    /**
     * @return array<int, string>
     */
    public function getArguments(): array
    {
        return $this->arguments;
    }

    /**
     * @return array<string, string>
     */
    public function getOptions(): array
    {
        return $this->options;
    }
}
