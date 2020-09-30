<?php declare(strict_types=1);
namespace PackageFactory\Neos\CodeGenerator\Pattern\PresentationObjects\Value;

/*
 * This file is part of the PackageFactory.Neos.CodeGenerator package
 */

use Neos\Flow\Annotations as Flow;
use PackageFactory\Neos\CodeGenerator\Domain\Pattern\GeneratorInterface;
use PackageFactory\Neos\CodeGenerator\Domain\Pattern\GeneratorQuery;
use PackageFactory\Neos\CodeGenerator\Infrastructure\FileWriter;

/**
 * @Flow\Scope("singleton")
 */
final class ValueGenerator implements GeneratorInterface
{
    /**
     * @Flow\Inject
     * @var ValueFactory
     */
    protected $valueFactory;

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
        $value = $this->valueFactory->fromGeneratorQuery($query);

        $this->fileWriter->write($value->asPhpClassFile());
    }
}
