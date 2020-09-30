<?php declare(strict_types=1);
namespace PackageFactory\Neos\CodeGenerator\Pattern\PresentationObjects\Value;

/*
 * This file is part of the PackageFactory.Neos.CodeGenerator package
 */

use Neos\Flow\Annotations as Flow;
use PackageFactory\Neos\CodeGenerator\Domain\Code\Php\Identifier\PhpNamespace;
use PackageFactory\Neos\CodeGenerator\Domain\Code\Php\Import\Import;
use PackageFactory\Neos\CodeGenerator\Domain\Code\Php\Import\ImportCollectionBuilder;
use PackageFactory\Neos\CodeGenerator\Domain\Code\Php\Property\PropertyFactory;
use PackageFactory\Neos\CodeGenerator\Domain\Pattern\GeneratorQuery;
use PackageFactory\Neos\CodeGenerator\Infrastructure\PackageResolver;
use PackageFactory\Neos\CodeGenerator\Infrastructure\SignatureFactory;

/**
 * @Flow\Scope("singleton")
 */
final class ValueFactory
{
    /**
     * @Flow\Inject
     * @var PackageResolver
     */
    protected $packageResolver;

    /**
     * @Flow\Inject
     * @var SignatureFactory
     */
    protected $signatureFactory;

    /**
     * @Flow\Inject
     * @var PropertyFactory
     */
    protected $propertyFactory;

    /**
     * @param GeneratorQuery $query
     * @return void
     */
    public function fromGeneratorQuery(GeneratorQuery $query): Value
    {
        $flowPackage = $this->packageResolver->resolve($query->optional('package')->string());
        $presentationNamespace = PhpNamespace::fromFlowPackage($flowPackage)->append('Presentation');

        $className = $presentationNamespace
            ->append(ucfirst(str_replace('/', '\\', $query->required('name')->string())))
            ->asClassName();
        $signature = $this->signatureFactory->forFlowPackage($flowPackage);

        $importCollectionBuilder = new ImportCollectionBuilder();
        $properties = [];

        foreach ($query->optional('properties')->dictionary() as $propertyName => $propertyType) {
            $propertyType = str_replace('/', '\\', $propertyType->string());

            if (strpos($propertyType, '\\') !== false) {
                if ($propertyType[0] === '\\') {
                    $import = new Import(substr($propertyType, 1), null);
                } else {
                    $import = new Import($presentationNamespace->append($propertyType)->asString(), null);
                }

                $import = $importCollectionBuilder->resolvePotentialNamingConflictForImport($import);
                $importCollectionBuilder->addImport($import);

                $property = $this->propertyFactory->fromKeyValuePair([$propertyName, $import->getName()]);
            } else {
                $property = $this->propertyFactory->fromKeyValuePair([$propertyName, $propertyType]);
            }

            $properties[] = $property;
        }

        $imports = $importCollectionBuilder->build();

        return new Value($flowPackage, $className, $signature, $imports, $properties);
    }
}
