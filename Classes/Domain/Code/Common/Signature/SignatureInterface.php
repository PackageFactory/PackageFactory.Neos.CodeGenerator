<?php declare(strict_types=1);
namespace PackageFactory\Neos\CodeGenerator\Domain\Code\Common\Signature;

/*
 * This file is part of the PackageFactory.Neos.CodeGenerator package
 */

interface SignatureInterface
{
    /**
     * @return string
     */
    public function asPhpCode(): string;
}
