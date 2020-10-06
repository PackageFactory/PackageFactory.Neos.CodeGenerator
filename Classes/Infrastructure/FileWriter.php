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

        $directoryPath = $file->getPath()->getParentDirectoryPath()->asString();
        if (!file_exists($directoryPath)) {
            Files::createDirectoryRecursively($directoryPath);
        }

        if ($this->isWriteOperationPermitted($file)) {
            file_put_contents($file->getPath()->asString(), $file->getContents());

            $cwd = getcwd();
            $pathAsString = $file->getPath()->asString();

            if (strpos($pathAsString, $cwd) === 0) {
                $pathAsString = '.' . substr($pathAsString, strlen($cwd));
            }

            $this->output->outputLine('Wrote file %s', [$pathAsString]);
        } else {
            $this->output->outputLine('<warning>File %s was not written</warning>', [$file->getPath()->asString()]);
        }
    }

    public function isWriteOperationPermitted(FileInterface $file): bool
    {
        if (file_exists($file->getPath()->asString())) {
            if (is_writeable($file->getPath()->asString())) {
                $this->output->outputLine('File <b>%s</b> already exists.', [$file->getPath()->asString()]);
                return $this->output->askConfirmation('Should it be overwritten? (y/N) ', false);
            } else {
                $this->output->outputLine('<error>File %s is not writable!</error>', [$file->getPath()->asString()]);
                return false;
            }
        } elseif (is_writable($file->getPath()->getParentDirectoryPath()->asString())) {
            return true;
        } else {
            $this->output->outputLine('<error>Directory %s is not writable!</error>', [$file->getPath()->getParentDirectoryPath()->getValue()]);
            return false;
        }
    }
}
