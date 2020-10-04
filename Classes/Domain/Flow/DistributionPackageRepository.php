<?php declare(strict_types=1);
namespace PackageFactory\Neos\CodeGenerator\Domain\Flow;

/*
 * This file is part of the PackageFactory.Neos.CodeGenerator package
 */

use Neos\Flow\Annotations as Flow;
use Neos\Utility\Files;

/**
 * @Flow\Scope("singleton")
 */
final class DistributionPackageRepository implements DistributionPackageRepositoryInterface
{
    /**
     * @Flow\Inject
     * @var DistributionPackageFactoryInterface
     */
    protected $distributionPackageFactory;

    /**
     * @Flow\InjectConfiguration(path="packageResolution.defaultPackageKey")
     * @var string
     */
    protected $defaultPackageKey;

    /**
     * @Flow\InjectConfiguration(path="packageResolution.pathToDistributionPackages")
     * @var string
     */
    protected $pathToDistributionPackages;

    /**
     * @var array<string,DistributionPackageInterface>
     */
    protected $storage = [];

    /**
     * @return \Iterator<string, DistributionPackageInterface>
     */
    public function findAll(): \Iterator
    {
        foreach ($this->storage as $distributionPackage) {
            yield $distributionPackage->getPackageKey()->asString() => $distributionPackage;
        }

        $directoryIterator = new \DirectoryIterator($this->pathToDistributionPackages);
        foreach ($directoryIterator as $directoryIteratorItem) {
            if (array_key_exists($directoryIteratorItem->getBasename(), $this->storage)) {
                continue;
            }

            if ($distributionPackage = $this->distributionPackageFactory->fromPackageKeyString($directoryIteratorItem->getBasename())) {
                yield $distributionPackage->getPackageKey()->asString() => $distributionPackage;
            }
        }
    }

    /**
     * @param PackageKey $packageKey
     * @return null|DistributionPackageInterface
     */
    public function findOneByPackageKey(PackageKey $packageKey): ?DistributionPackageInterface
    {
        if (isset($this->storage[$packageKey->asString()])) {
            return $this->storage[$packageKey->asString()];
        } else {
            return $this->distributionPackageFactory->fromPackageKey($packageKey);
        }
    }

    /**
     * @return null|DistributionPackageInterface
     */
    public function findDefault(): ?DistributionPackageInterface
    {
        if ($this->defaultPackageKey) {
            return $this->findOneByPackageKey(PackageKey::fromString($this->defaultPackageKey));
        }

        return null;
    }

    /**
     * @return null|DistributionPackageInterface
     */
    public function findFirstAvailable(): ?DistributionPackageInterface
    {
        $directoryIterator = new \DirectoryIterator($this->pathToDistributionPackages);

        foreach ($directoryIterator as $directoryIteratorItem) {
            if (array_key_exists($directoryIteratorItem->getBasename(), $this->storage)) {
                return $this->storage[$directoryIteratorItem->getBasename()];
            }

            if ($distributionPackage = $this->distributionPackageFactory->fromPackageKeyString($directoryIteratorItem->getBasename())) {
                return $distributionPackage;
            }
        }

        foreach ($this->storage as $distributionPackage) {
            return $distributionPackage;
        }

        return null;
    }

    /**
     * @param DistributionPackageInterface $distributionPackage
     * @return void
     */
    public function add(DistributionPackageInterface $distributionPackage): void
    {
        $this->storage[$distributionPackage->getPackageKey()->asString()] = $distributionPackage;
    }
}
