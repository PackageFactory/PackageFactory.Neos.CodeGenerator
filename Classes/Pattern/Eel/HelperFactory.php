<?php declare(strict_types=1);
namespace PackageFactory\Neos\CodeGenerator\Pattern\Eel;

/*
 * This file is part of the PackageFactory.Neos.CodeGenerator package
 */

use Neos\Flow\Annotations as Flow;
use PackageFactory\Neos\CodeGenerator\Domain\Code\Common\Signature\SignatureFactoryInterface;
use PackageFactory\Neos\CodeGenerator\Domain\Code\Php\PhpNamespace\PhpNamespace;
use PackageFactory\Neos\CodeGenerator\Domain\Flow\DistributionPackageResolverInterface;
use PackageFactory\Neos\CodeGenerator\Domain\Input\Query;

/**
 * @Flow\Scope("singleton")
 */
final class HelperFactory
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
     * @return Helper
     */
    public function fromQuery(Query $query): Helper
    {
        $distributionPackage = $this->distributionPackageResolver->resolve($query->optional('package')->string());
        $name = ucfirst($query->required('name')->string());

        $className = $distributionPackage->getPackageKey()->asPhpNamespace()
            ->append('Application\\Eel')
            ->append($name . 'Helper')
            ->asClassName();
        $signature = $this->signatureFactory->forDistributionPackage($distributionPackage);
        $defaultContextIdentifier = $distributionPackage->getPackageKey()->asString() . '.' . $name;

        return new Helper($distributionPackage, $name, $signature, $defaultContextIdentifier);
    }
}
