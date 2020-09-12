<?php declare(strict_types=1);
namespace PackageFactory\Neos\CodeGenerator\Domain\Files;

/*
 * This file is part of the PackageFactory.Neos.CodeGenerator package
 */

interface FileInterface
{
    /**
     * @return Path
     */
    public function getPath(): Path;

    /**
     * @return string
     */
    public function getContents(): string;
}
