<?php declare(strict_types=1);
namespace PackageFactory\Neos\CodeGenerator\Pattern\Domain;

/*
 * This file is part of the PackageFactory.Neos.CodeGenerator package
 */

use Neos\Flow\Annotations as Flow;
use PackageFactory\Neos\CodeGenerator\Domain\Pattern\GeneratorInterface;
use PackageFactory\Neos\CodeGenerator\Domain\Pattern\GeneratorQuery;
use PackageFactory\Neos\CodeGenerator\Infrastructure\FileWriter;
use PackageFactory\Neos\CodeGenerator\Infrastructure\PackageResolver;

/**
 * @Flow\Scope("singleton")
 */
final class ValueGenerator implements GeneratorInterface
{
    /**
     * @Flow\Inject
     * @var PackageResolver
     */
    protected $packageResolver;

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
        $flowPackage = $this->packageResolver->resolve($query->getArgument(0, 'No package key was given!'));

        $value = Value::fromQuery($query->shiftArgument(), $flowPackage);

        $this->fileWriter->write($value->asPhpClassFile());
    }
}
