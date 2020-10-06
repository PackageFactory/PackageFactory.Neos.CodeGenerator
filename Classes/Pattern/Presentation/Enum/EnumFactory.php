<?php declare(strict_types=1);
namespace PackageFactory\Neos\CodeGenerator\Pattern\Presentation\Enum;

/*
 * This file is part of the PackageFactory.Neos.CodeGenerator package
 */

use Neos\Flow\Annotations as Flow;
use PackageFactory\Neos\CodeGenerator\Domain\Code\Common\Signature\SignatureFactoryInterface;
use PackageFactory\Neos\CodeGenerator\Domain\Input\Query;
use PackageFactory\Neos\CodeGenerator\Domain\Flow\DistributionPackageResolverInterface;
use PackageFactory\Neos\CodeGenerator\Pattern\Presentation\Presentation;

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
        $timestamp = $query->now()->getTimestamp();
        $type = EnumType::fromString($query->required('type')->type()->asString());
        $signature = $this->signatureFactory->forDistributionPackage($distributionPackage);

        $values = [];
        foreach ($query->required('values')->array() as $key => $value) {
            if ($type->isInteger()) {
                if (is_string($key) && is_int($value)) {
                    $values[] = new EnumValue($key, (string) $value);
                } elseif (is_int($key) && is_string($value)) {
                    $values[] = new EnumValue($value, (string) ($key + 1));
                } else {
                    throw new \InvalidArgumentException(
                        'Could not build Enum value for key-value pair: ' . json_encode([$key => $value], JSON_PRETTY_PRINT),
                        1602014635
                    );
                }
            } elseif ($type->isString()) {
                $values[] = new EnumValue(
                    is_string($key) ? $key : (string) $value,
                    (string) $value
                );
            }
        }

        return new Enum($presentation, $name, $timestamp, $type, $signature, $values);
    }
}
