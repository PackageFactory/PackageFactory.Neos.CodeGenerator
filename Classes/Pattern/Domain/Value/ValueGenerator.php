<?php declare(strict_types=1);
namespace PackageFactory\Neos\CodeGenerator\Pattern\Domain\Value;

/*
 * This file is part of the PackageFactory.Neos.CodeGenerator package
 */

use Neos\Flow\Annotations as Flow;
use PackageFactory\Neos\CodeGenerator\Domain\Files\FileWriterInterface;
use PackageFactory\Neos\CodeGenerator\Domain\Pattern\GeneratorInterface;
use PackageFactory\Neos\CodeGenerator\Domain\Input\Query;

/**
 * @Flow\Scope("singleton")
 */
final class ValueGenerator implements GeneratorInterface
{
    /**
     * @Flow\Inject
     * @var ValueRepository
     */
    protected $valueRepository;

    /**
     * @Flow\Inject
     * @var ValueFactory
     */
    protected $valueFactory;

    /**
     * @Flow\Inject
     * @var FileWriterInterface
     */
    protected $fileWriter;

    /**
     * @param Query $query
     * @return void
     */
    public function generate(Query $query): void
    {
        $value = $this->valueFactory->fromQuery($query);

        $this->fileWriter->write($value->asPhpClassFile());

        $this->valueRepository->add($value);
    }
}
