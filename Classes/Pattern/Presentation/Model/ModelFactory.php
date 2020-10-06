<?php declare(strict_types=1);
namespace PackageFactory\Neos\CodeGenerator\Pattern\Presentation\Model;

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
use PackageFactory\Neos\CodeGenerator\Framework\Util\StringUtil;
use PackageFactory\Neos\CodeGenerator\Pattern\Presentation\Presentation;

/**
 * @Flow\Scope("singleton")
 */
final class ModelFactory
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
     * @return Model
     */
    public function fromQuery(Query $query): Model
    {
        $distributionPackage = $this->distributionPackageResolver->resolve($query->optional('package')->string());
        $presentation = Presentation::fromDistributionPackage($distributionPackage);

        $name = str_replace('/', '\\', $query->required('name')->type()->asString());
        $name = $name . '\\' . StringUtil::tail('\\', $name);

        $signature = $this->signatureFactory->forDistributionPackage($distributionPackage);

        $importCollectionBuilder = new ImportCollectionBuilder();
        $properties = [];

        foreach ($query->optional('props')->dictionary() as $propertyName => $typeDescription) {
            $typeDescription = $typeDescription->type()->withTemplate($presentation);
            $property = $this->propertyFactory->fromKeyValuePair([$propertyName, $typeDescription->asString()]);
            $type = $property->getType();

            if ($type instanceof ClassType && $import = Import::fromType($type)) {
                $import = $importCollectionBuilder->addImport($import);
                $property = $property->withType($type->withNativeName($import->getName()));
            }

            $properties[] = $property;
        }

        $imports = $importCollectionBuilder->build();

        return new Model($presentation, $name, $signature, $imports, $properties);
    }
}
