<?php declare(strict_types=1);
namespace PackageFactory\Neos\CodeGenerator\Pattern\Eel;

/*
 * This file is part of the PackageFactory.Neos.CodeGenerator package
 */

use Neos\Flow\Annotations as Flow;
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
     * @param GeneratorQuery $query
     * @return Helper
     */
    public function fromGeneratorQuery(GeneratorQuery $query): Helper
    {
        $flowPackage = $this->packageResolver->resolve($query->optional('package')->string());
        $name = $query->required('name')->string();

        return new Helper($flowPackage, $name);
    }
}
