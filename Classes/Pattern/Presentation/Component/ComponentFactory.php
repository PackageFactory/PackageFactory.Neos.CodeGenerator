<?php declare(strict_types=1);
namespace PackageFactory\Neos\CodeGenerator\Pattern\Presentation\Component;

/*
 * This file is part of the PackageFactory.Neos.CodeGenerator package
 */

use Neos\Flow\Annotations as Flow;
use PackageFactory\Neos\CodeGenerator\Domain\Code\Common\Signature\SignatureFactoryInterface;
use PackageFactory\Neos\CodeGenerator\Domain\Input\Query;
use PackageFactory\Neos\CodeGenerator\Domain\Flow\DistributionPackageResolverInterface;
use PackageFactory\Neos\CodeGenerator\Framework\Util\StringUtil;
use PackageFactory\Neos\CodeGenerator\Pattern\Presentation\Component\Prop\Prop;
use PackageFactory\Neos\CodeGenerator\Pattern\Presentation\Component\PropType\PropTypeFactory;
use PackageFactory\Neos\CodeGenerator\Pattern\Presentation\Presentation;

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
     * @var PropTypeFactory
     */
    protected $propTypeFactory;

    /**
     * @param Query $query
     * @return Component
     */
    public function fromQuery(Query $query): Component
    {
        $distributionPackage = $this->distributionPackageResolver->resolve($query->optional('package')->string());
        $presentation = Presentation::fromDistributionPackage($distributionPackage);
        $name = str_replace('\\', '.', $query->required('name')->type()->asString());
        $signature = $this->signatureFactory->forDistributionPackage($distributionPackage);

        $title = $query->optional('title')->string() ?? StringUtil::tail('.', $name);

        $props = [];
        foreach ($query->optional('props')->dictionary() as $propName => $typeDescription) {
            $typeDescription = $typeDescription->type()->withTemplate($presentation);
            $props[] = new Prop($propName, $this->propTypeFactory->fromString($typeDescription->asString()));
        }

        return new Component($presentation, $name, $signature, $title, $props);
    }
}
