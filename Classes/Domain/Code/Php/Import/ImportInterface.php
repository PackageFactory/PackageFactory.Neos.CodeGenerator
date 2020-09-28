<?php declare(strict_types=1);
namespace PackageFactory\Neos\CodeGenerator\Domain\Code\Php\Import;

/*
 * This file is part of the PackageFactory.Neos.CodeGenerator package
 */

interface ImportInterface
{
    /**
     * @return string
     */
    public function getFullyQualifiedName(): string;

    /**
     * @return null|string
     */
    public function getAlias(): ?string;

    /**
     * @return string
     */
    public function getName(): string;

    /**
     * @param string $alias
     * @return ImportInterface
     */
    public function withAlias(string $alias): ImportInterface;
}
