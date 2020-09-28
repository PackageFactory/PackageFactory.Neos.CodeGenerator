<?php declare(strict_types=1);
namespace PackageFactory\Neos\CodeGenerator\Infrastructure;

/*
 * This file is part of the PackageFactory.Neos.CodeGenerator package
 */

use Neos\Flow\Annotations as Flow;
use Neos\Flow\Cli\ConsoleOutput;
use Neos\Utility\Files;
use PackageFactory\Neos\CodeGenerator\Domain\Files\FileInterface;
use PackageFactory\Neos\CodeGenerator\Domain\Files\FileWriterInterface;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;

/**
 * @Flow\Scope("singleton")
 */
final class FileWriter implements FileWriterInterface
{
    /**
     * @Flow\Inject
     * @var ConsoleOutput
     */
    protected $output;

    public function write(FileInterface $file): void
    {
        $this->output->getOutput()->getFormatter()->setStyle('warning', new OutputFormatterStyle('yellow'));

        $directoryPath = $file->getPath()->getParentDirectoryPath()->getValue();
        if (!file_exists($directoryPath)) {
            Files::createDirectoryRecursively($directoryPath);
        }

        if ($this->isWriteOperationPermitted($file)) {
            file_put_contents($file->getPath()->getValue(), $file->getContents());
            $this->output->outputLine('Wrote file %s', [$file->getPath()->getValue()]);
        } else {
            $this->output->outputLine('<warning>File %s was not written</warning>', [$file->getPath()->getValue()]);
        }
    }

    public function isWriteOperationPermitted(FileInterface $file): bool
    {
        if (file_exists($file->getPath()->getValue())) {
            if (is_writeable($file->getPath()->getValue())) {
                $this->output->outputLine('File <b>%s</b> already exists.', [$file->getPath()->getValue()]);
                return $this->output->askConfirmation('Should it be overwritten? (y/N) ', false);
            } else {
                $this->output->outputLine('<error>File %s is not writable!</error>', [$file->getPath()->getValue()]);
                return false;
            }
        } elseif (is_writable($file->getPath()->getParentDirectoryPath()->getValue())) {
            return true;
        } else {
            $this->output->outputLine('<error>Directory %s is not writable!</error>', [$file->getPath()->getParentDirectoryPath()->getValue()]);
            return false;
        }
    }
}
