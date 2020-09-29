<?php declare(strict_types=1);
namespace PackageFactory\Neos\CodeGenerator\Pattern\Eel;

/*
 * This file is part of the PackageFactory.Neos.CodeGenerator package
 */

use Neos\Flow\Annotations as Flow;
use PackageFactory\Neos\CodeGenerator\Domain\Code\Common\Signature\SignatureFactoryInterface;
use PackageFactory\Neos\CodeGenerator\Domain\Code\Php\Identifier\PhpNamespace;
use PackageFactory\Neos\CodeGenerator\Domain\Flow\PackageResolverInterface;
use PackageFactory\Neos\CodeGenerator\Domain\Pattern\GeneratorQuery;

/**
 * @Flow\Scope("singleton")
 */
final class HelperFactory
{
    /**
     * @Flow\Inject
     * @var PackageResolverInterface
     */
    protected $packageResolver;

    /**
     * @Flow\Inject
     * @var SignatureFactoryInterface
     */
    protected $signatureFactory;

    /**
     * @param GeneratorQuery $query
     * @return Helper
     */
    public function fromGeneratorQuery(GeneratorQuery $query): Helper
    {
        $flowPackage = $this->packageResolver->resolve($query->optional('package')->string());
        $name = ucfirst($query->required('name')->string());

        $className = PhpNamespace::fromFlowPackage($flowPackage)
            ->append('Application\\Eel')
            ->append($name . 'Helper')
            ->asClassName();
        $signature = $this->signatureFactory->forFlowPackage($flowPackage);
        $defaultContextIdentifier = $flowPackage->getPackageKey() . '.' . $name;

        return new Helper($flowPackage, $className, $signature, $defaultContextIdentifier);
    }
}
