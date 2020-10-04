<?php declare(strict_types=1);
namespace PackageFactory\Neos\CodeGenerator\Domain\Flow;

use PackageFactory\Neos\CodeGenerator\Domain\Code\Php\PhpClass\PhpClassName;
use PackageFactory\Neos\CodeGenerator\Domain\Files\Path;

/*
 * This file is part of the PackageFactory.Neos.CodeGenerator package
 */

interface DistributionPackageInterface
{
    /**
     * @return PackageKey
     */
    public function getPackageKey(): PackageKey;

    /**
     * @return Path
     */
    public function getPackagePath(): Path;

    /**
     * @return Path
     */
    public function getPhpFilePathForClassName(PhpClassName $className): Path;
}
