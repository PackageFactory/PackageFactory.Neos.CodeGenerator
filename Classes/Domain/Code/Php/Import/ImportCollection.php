<?php declare(strict_types=1);
namespace PackageFactory\Neos\CodeGenerator\Domain\Code\Php\Import;

/*
 * This file is part of the PackageFactory.Neos.CodeGenerator package
 */

use Neos\Flow\Annotations as Flow;

/**
 * @Flow\Proxy(false)
 */
final class ImportCollection implements ImportCollectionInterface
{
    /**
     * @var ImportInterface[]
     */
    private $imports;

    /**
     * @param ImportInterface[] $imports
     */
    public function __construct(array $imports)
    {
        $this->imports = $imports;
    }

    /**
     * @template T
     * @param callable(ImportInterface):T $mapFn
     * @return T[]
     */
    public function map(callable $mapFn): array
    {
        return array_map($mapFn, $this->imports);
    }
}
