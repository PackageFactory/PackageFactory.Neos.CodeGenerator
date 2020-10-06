<?php declare(strict_types=1);
namespace PackageFactory\Neos\CodeGenerator\Pattern\Presentation\Enum;

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
final class EnumGenerator implements GeneratorInterface
{
    /**
     * @Flow\Inject
     * @var EnumRepository
     */
    protected $enumRepository;

    /**
     * @Flow\Inject
     * @var EnumFactory
     */
    protected $enumFactory;

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
        $enum = $this->enumFactory->fromQuery($query);

        $this->fileWriter->write($enum->asPhpClassFileForValueObject());
        $this->fileWriter->write($enum->asPhpClassFileForException());
        $this->fileWriter->write($enum->asPhpClassFileForDataSource());

        $this->enumRepository->add($enum);
    }
}
