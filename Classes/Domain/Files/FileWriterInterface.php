<?php declare(strict_types=1);
namespace PackageFactory\Neos\CodeGenerator\Domain\Files;

/*
 * This file is part of the PackageFactory.Neos.CodeGenerator package
 */

interface FileWriterInterface
{
    /**
     * @param FileInterface $file
     * @return void
     */
    public function write(FileInterface $file): void;
}
