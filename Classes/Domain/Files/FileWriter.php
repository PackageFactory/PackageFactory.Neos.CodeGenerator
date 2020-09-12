<?php declare(strict_types=1);
namespace PackageFactory\Neos\CodeGenerator\Domain\Files;

/*
 * This file is part of the PackageFactory.Neos.CodeGenerator package
 */

use Neos\Flow\Annotations as Flow;
use Neos\Flow\Cli\ConsoleOutput;
use Neos\Utility\Files;

/**
 * @Flow\Scope("singleton")
 */
final class FileWriter
{
    /**
     * @Flow\Inject
     * @var ConsoleOutput
     */
    protected $output;

    public function write(FileInterface $file): void
    {
        $directoryPath = $file->getPath()->getParentDirectoryPath()->getValue();
        if (!file_exists($directoryPath)) {
            Files::createDirectoryRecursively($directoryPath);
        }

        file_put_contents($file->getPath()->getValue(), $file->getContents());

        $this->output->outputLine('Wrote file %s', [$file->getPath()->getValue()]);
    }
}
