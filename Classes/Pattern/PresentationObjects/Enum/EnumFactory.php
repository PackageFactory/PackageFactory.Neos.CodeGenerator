<?php declare(strict_types=1);
namespace PackageFactory\Neos\CodeGenerator\Pattern\PresentationObjects\Enum;

/*
 * This file is part of the PackageFactory.Neos.CodeGenerator package
 */

use Neos\Flow\Annotations as Flow;
use PackageFactory\Neos\CodeGenerator\Domain\Code\Php\Identifier\PhpNamespace;
use PackageFactory\Neos\CodeGenerator\Domain\Pattern\GeneratorQuery;
use PackageFactory\Neos\CodeGenerator\Infrastructure\PackageResolver;
use PackageFactory\Neos\CodeGenerator\Infrastructure\SignatureFactory;

/**
 * @Flow\Scope("singleton")
 */
final class EnumFactory
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
     * @param GeneratorQuery $query
     * @return Enum
     */
    public function fromGeneratorQuery(GeneratorQuery $query): Enum
    {
        $flowPackage = $this->packageResolver->resolve($query->optional('package')->string());
        $presentationNamespace = PhpNamespace::fromFlowPackage($flowPackage)->append('Presentation');

        $className = $presentationNamespace
            ->append(ucfirst(str_replace('/', '\\', $query->required('name')->string())))
            ->asClassName();
        $signature = $this->signatureFactory->forFlowPackage($flowPackage);

        $values = [];
        foreach ($query->required('values')->list() as $value) {
            $values[] = $value->string();
        }

        return new Enum($flowPackage, $className, $signature, $values);
    }
}
