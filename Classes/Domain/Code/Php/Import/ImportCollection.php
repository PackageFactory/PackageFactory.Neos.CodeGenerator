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
     * @return ImportInterface[]
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->imports);
    }

    /**
     * @return integer
     */
    public function count()
    {
        return count($this->imports);
    }
}
