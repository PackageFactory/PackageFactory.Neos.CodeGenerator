<?php declare(strict_types=1);
namespace PackageFactory\Neos\CodeGenerator\Pattern\Presentation\Value;

/*
 * This file is part of the PackageFactory.Neos.CodeGenerator package
 */

use Neos\Flow\Annotations as Flow;
use PackageFactory\Neos\CodeGenerator\Domain\Code\Common\Signature\SignatureFactoryInterface;
use PackageFactory\Neos\CodeGenerator\Domain\Code\Php\Import\Import;
use PackageFactory\Neos\CodeGenerator\Domain\Code\Php\Import\ImportCollectionBuilder;
use PackageFactory\Neos\CodeGenerator\Domain\Code\Php\Property\PropertyFactory;
use PackageFactory\Neos\CodeGenerator\Domain\Code\Php\Type\ClassType;
use PackageFactory\Neos\CodeGenerator\Domain\Input\Query;
use PackageFactory\Neos\CodeGenerator\Domain\Flow\DistributionPackageResolverInterface;
use PackageFactory\Neos\CodeGenerator\Pattern\Presentation\Presentation;

/**
 * @Flow\Scope("singleton")
 */
final class ValueFactory
{
    /**
     * @Flow\Inject
     * @var DistributionPackageResolverInterface
     */
    protected $distributionPackageResolver;

    /**
     * @Flow\Inject
     * @var SignatureFactoryInterface
     */
    protected $signatureFactory;

    /**
     * @Flow\Inject
     * @var PropertyFactory
     */
    protected $propertyFactory;

    /**
     * @param Query $query
     * @return Value
     */
    public function fromQuery(Query $query): Value
    {
        $distributionPackage = $this->distributionPackageResolver->resolve($query->optional('package')->string());
        $presentation = Presentation::fromDistributionPackage($distributionPackage);
        $name = $query->required('name')->type()->asString();
        $signature = $this->signatureFactory->forDistributionPackage($distributionPackage);

        $importCollectionBuilder = new ImportCollectionBuilder();
        $properties = [];

        foreach ($query->optional('props')->dictionary() as $propertyName => $type) {
            $type = $type->type()->substitute([
                'foreignNamespace' => '\\{package}\\Presentation\\{namespace}',
                'domesticNamespace' => '\\' . $presentation->getPhpNamespace()->asString() . '\\{namespace}'
            ]);

            $property = $this->propertyFactory->fromKeyValuePair([$propertyName, $type->asString()]);
            $type = $property->getType();

            if ($type instanceof ClassType && $import = Import::fromType($type)) {
                $import = $importCollectionBuilder->addImport($import);
                $property = $property->withType($type->withAlias($import->getName()));
            }

            $properties[] = $property;
        }

        $imports = $importCollectionBuilder->build();

        return new Value($presentation, $name, $signature, $imports, $properties);
    }
}
