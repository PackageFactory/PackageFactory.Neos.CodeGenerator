<?php declare(strict_types=1);
namespace PackageFactory\Neos\CodeGenerator\Pattern\Presentation\Component;

/*
 * This file is part of the PackageFactory.Neos.CodeGenerator package
 */

use Neos\Flow\Annotations as Flow;
use PackageFactory\Neos\CodeGenerator\Domain\Code\Common\Signature\SignatureFactoryInterface;
use PackageFactory\Neos\CodeGenerator\Domain\Code\Fusion\Prototype\PrototypeInterface;
use PackageFactory\Neos\CodeGenerator\Domain\Input\Query;
use PackageFactory\Neos\CodeGenerator\Domain\Flow\DistributionPackageResolverInterface;
use PackageFactory\Neos\CodeGenerator\Framework\Util\StringUtil;
use PackageFactory\Neos\CodeGenerator\Pattern\Presentation\Model\ModelFactory;
use PackageFactory\Neos\CodeGenerator\Pattern\Presentation\Presentation;
use PackageFactory\Neos\CodeGenerator\Pattern\Presentation\Prop\Prop;
use PackageFactory\Neos\CodeGenerator\Pattern\Presentation\Prop\PropTypeFactory;

/**
 * @Flow\Scope("singleton")
 */
final class ComponentFactory
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
     * @var ModelFactory
     */
    protected $modelFactory;

    /**
     * @Flow\Inject
     * @var PropTypeFactory
     */
    protected $propTypeFactory;

    /**
     * @param Query $query
     * @param PropTypeFactory $propTypeFactory
     * @return Component
     */
    public function fromQuery(Query $query, PropTypeFactory $propTypeFactory): Component
    {
        $model = $this->modelFactory->fromQuery($query);
        $distributionPackage = $this->distributionPackageResolver->resolve($query->optional('package')->string());
        $presentation = Presentation::fromDistributionPackage($distributionPackage);
        $name = str_replace('\\', '.', $query->required('name')->type()->asString());
        $signature = $this->signatureFactory->forDistributionPackage($distributionPackage);

        $title = $query->optional('title')->string() ?? StringUtil::tail('.', $name);

        $props = [];
        foreach ($query->optional('props')->dictionary() as $propName => $type) {
            $type = $type->type()->substitute([
                'foreignNamespace' => '\\{package}\\Presentation\\{namespace}',
                'domesticNamespace' => '\\' . $presentation->getPhpNamespace()->asString() . '\\{namespace}',
                'localNamespace' => StringUtil::tail('\\', $model->getPhpClassNameForValueObject()->asFullyQualifiedNameString()) . '\\{className}'
            ]);

            $props[] = new Prop($propName, $propTypeFactory->fromString($type->asAtomicString()));
        }

        return new Component($presentation, $name, $model, $signature, $title, $props);
    }

    /**
     * @param \ReflectionClass<object> $modelReflection
     * @param PrototypeInterface $prototype
     * @return Component
     */
    public function fromExisitingComponent(\ReflectionClass $modelReflection, PrototypeInterface $prototype): Component
    {
        $model = $this->modelFactory->fromExistingModel($modelReflection);
        $distributionPackage = $this->distributionPackageResolver->resolve($prototype->getName()->getPackageKey());
        $presentation = Presentation::fromDistributionPackage($distributionPackage);
        $name = StringUtil::truncateStart(StringUtil::dropHead(':', $prototype->getName()->asString()), 'Component.');

        $signature = $this->signatureFactory->forDistributionPackage($distributionPackage);
        $title = $prototype->getMeta()['styleguide']['title'] ?? 'Unknown';

        $props = [];
        foreach ($model->getProperties() as $property) {
            $props[] = new Prop(
                $property->getName(),
                $this->propTypeFactory->fromExistingStyleguideExample(
                    $prototype->getMeta()['styleguide']['props'][$property->getName()] ?? null
                )
            );
        }

        return new Component($presentation, $name, $model, $signature, $title, $props);
    }
}
