<?php declare(strict_types=1);
namespace PackageFactory\Neos\CodeGenerator\Pattern\PresentationObjects;

/*
 * This file is part of the PackageFactory.Neos.CodeGenerator package
 */

use Neos\Flow\Annotations as Flow;
use PackageFactory\Neos\CodeGenerator\Domain\Code\PhpFile;
use PackageFactory\Neos\CodeGenerator\Domain\Pattern\GeneratorInterface;
use PackageFactory\Neos\CodeGenerator\Domain\Pattern\GeneratorQuery;
use PackageFactory\Neos\CodeGenerator\Infrastructure\FileWriter;

/**
 * The value generator domain service
 *
 * @Flow\Scope("singleton")
 */
final class EnumGenerator implements GeneratorInterface
{
    /**
     * @Flow\Inject
     * @var FileWriter
     */
    protected $fileWriter;

    /**
     * @param GeneratorQuery $query
     * @return void
     */
    public function generate(GeneratorQuery $query): void
    {
        $enum = Enum::fromGeneratorQuery($query);
        $phpFile = PhpFile::fromFlowPackageAndNamespace($query->getFlowPackage(), $enum->getNamespace(), $enum->getClassName())
            ->withBody($enum->getBody());

        $this->fileWriter->write($phpFile);
    }
}
