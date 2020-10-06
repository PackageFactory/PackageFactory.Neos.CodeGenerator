<?php declare(strict_types=1);
namespace PackageFactory\Neos\CodeGenerator\Infrastructure;

/*
 * This file is part of the PackageFactory.Neos.CodeGenerator package
 */

use Neos\Flow\Annotations as Flow;
use Neos\Utility\Files;
use PackageFactory\Neos\CodeGenerator\Domain\Files\FileInterface;
use PackageFactory\Neos\CodeGenerator\Domain\Files\FileWriterInterface;
use PackageFactory\Neos\CodeGenerator\Framework\IO\ConsoleIO;

/**
 * @Flow\Scope("singleton")
 */
final class FileWriter implements FileWriterInterface
{
    /**
     * @Flow\Inject(lazy=false)
     * @var ConsoleIO
     */
    protected $io;

    public function write(FileInterface $file): void
    {
        $directoryPath = $file->getPath()->getParentDirectoryPath()->asString();
        if (!file_exists($directoryPath)) {
            Files::createDirectoryRecursively($directoryPath);
        }

        if ($this->isWriteOperationPermitted($file)) {
            file_put_contents($file->getPath()->asString(), $file->getContents());

            $cwd = getcwd();
            $pathAsString = $file->getPath()->asString();

            if ($cwd && strpos($pathAsString, $cwd) === 0) {
                $pathAsString = '.' . substr($pathAsString, strlen($cwd));
            }

            $this->io->out(sprintf('Wrote file %s', $pathAsString));
        } else {
            $this->io->yellow()->out(sprintf('File %s was not written', $file->getPath()->asString()));
        }
    }

    public function isWriteOperationPermitted(FileInterface $file): bool
    {
        if (file_exists($file->getPath()->asString())) {
            if (is_writeable($file->getPath()->asString())) {
                $this->io->out(sprintf('File <bold>%s</bold> already exists.', $file->getPath()->asString()));

                if ($this->io->arguments->get('overwrite')) {
                    $this->io->yellow()->out('Flag --overwrite was set.');
                    return true;
                } else {
                    return $this->io->confirm('Should it be overwritten?')->defaultTo('N')->confirmed();
                }
            } else {
                $this->io->red()->out(sprintf('File %s is not writable!', $file->getPath()->asString()));
                return false;
            }
        } elseif (is_writable($file->getPath()->getParentDirectoryPath()->asString())) {
            return true;
        } else {
            $this->io->red()->out(sprintf('Directory %s is not writable!', $file->getPath()->getParentDirectoryPath()->asString()));
            return false;
        }
    }
}
