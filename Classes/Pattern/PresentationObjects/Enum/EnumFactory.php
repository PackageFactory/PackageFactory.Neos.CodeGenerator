<?php declare(strict_types=1);
namespace PackageFactory\Neos\CodeGenerator\Pattern\PresentationObjects\Enum;

/*
 * This file is part of the PackageFactory.Neos.CodeGenerator package
 */

use Neos\Flow\Annotations as Flow;
use PackageFactory\Neos\CodeGenerator\Domain\Code\Common\Signature\SignatureFactoryInterface;
use PackageFactory\Neos\CodeGenerator\Domain\Input\Query;
use PackageFactory\Neos\CodeGenerator\Domain\Flow\DistributionPackageResolverInterface;
use PackageFactory\Neos\CodeGenerator\Pattern\PresentationObjects\Presentation;

/**
 * @Flow\Scope("singleton")
 */
final class EnumFactory
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
     * @param Query $query
     * @return Enum
     */
    public function fromQuery(Query $query): Enum
    {
        $distributionPackage = $this->distributionPackageResolver->resolve($query->optional('package')->string());
        $presentation = Presentation::fromDistributionPackage($distributionPackage);
        $name = $query->required('name')->type()->asString();
        $signature = $this->signatureFactory->forDistributionPackage($distributionPackage);

        $values = [];
        foreach ($query->required('values')->list() as $value) {
            $values[] = $value->string();
        }

        return new Enum($presentation, $name, $signature, $values);
    }
}
