<?php declare(strict_types=1);
namespace PackageFactory\Neos\CodeGenerator\Pattern\Presentation\Model;

/*
 * This file is part of the PackageFactory.Neos.CodeGenerator package
 */

use Neos\Flow\Annotations as Flow;
use PackageFactory\Neos\CodeGenerator\Domain\Code\Common\Signature\SignatureFactoryInterface;
use PackageFactory\Neos\CodeGenerator\Domain\Code\Php\Import\Import;
use PackageFactory\Neos\CodeGenerator\Domain\Code\Php\Import\ImportCollectionBuilder;
use PackageFactory\Neos\CodeGenerator\Domain\Code\Php\Method\GetterSpecification;
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

        return new Model($presentation, $name, $signature, $imports, $properties);
    }

    /**
     * @param \ReflectionClass<object> $modelReflection
     * @return Model
     */
    public function fromExistingModel(\ReflectionClass $modelReflection): Model
    {
        list($packageKey, $name) = explode('\\Presentation\\', $modelReflection->getName());

        $packageKey = ltrim($packageKey, '\\');
        $packageKey = str_replace('\\', '.', $packageKey);

        $distributionPackage = $this->distributionPackageResolver->resolve($packageKey);
        $presentation = Presentation::fromDistributionPackage($distributionPackage);
        $signature = $this->signatureFactory->forDistributionPackage($distributionPackage);

        $importCollectionBuilder = new ImportCollectionBuilder();
        $properties = [];

        foreach ($modelReflection->getMethods(\ReflectionMethod::IS_PUBLIC) as $methodReflection) {
            if (GetterSpecification::isSatisfiedByReflectionMethod($methodReflection)) {
                $property = $this->propertyFactory->fromReflectionGetterMethod($methodReflection);
                $fullyQualifiedTypeName = (string) $methodReflection->getReturnType();

                if (class_exists($fullyQualifiedTypeName) || interface_exists($fullyQualifiedTypeName)) {
                    $import = new Import($fullyQualifiedTypeName, $property->getType()->getName());
                    $import = $importCollectionBuilder->addImport($import);
                    $property = $property->withType(
                        $property->getType()
                            ->withNativeName('\\' . $import->getFullyQualifiedName())
                            ->withAlias($import->getName())
                    );
                }

                $properties[] = $property;
            }
        }


        $imports = $importCollectionBuilder->build();

        return new Model($presentation, $name, $signature, $imports, $properties);
    }
}
