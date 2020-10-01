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
use PackageFactory\Neos\CodeGenerator\Domain\Code\Php\Type\ClassType;
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
     * @return Value
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

        foreach ($query->optional('properties')->dictionary() as $propertyName => $typeName) {
            $typeName = str_replace('/', '\\', $typeName->string());
            $property = $this->propertyFactory->fromKeyValuePair([$propertyName, $typeName]);
            $type = $property->getType();

            if ($type instanceof ClassType && $import = Import::fromClassType($type, $presentationNamespace)) {
                $import = $importCollectionBuilder->addImport($import);
                $property = $property->withType($type->withNativeName($import->getName()));
            }

            $properties[] = $property;
        }

        $imports = $importCollectionBuilder->build();

        return new Value($flowPackage, $className, $signature, $imports, $properties);
    }
}
