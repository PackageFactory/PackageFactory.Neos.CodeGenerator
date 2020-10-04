<?php declare(strict_types=1);
namespace PackageFactory\Neos\CodeGenerator\Domain\Flow;

/*
 * This file is part of the PackageFactory.Neos.CodeGenerator package
 */

use Neos\Eel\Helper\StringHelper;
use Neos\Flow\Annotations as Flow;
use PackageFactory\Neos\CodeGenerator\Framework\IO\ConsoleIO;

/**
 * @Flow\Scope("singleton")
 */
final class DistributionPackageResolver implements DistributionPackageResolverInterface
{
    /**
     * @Flow\Inject
     * @var ConsoleIO
     */
    protected $io;

    /**
     * @Flow\Inject
     * @var DistributionPackageRepositoryInterface
     */
    protected $distributionPackageRepository;

    /**
     * @Flow\Inject
     * @var StringHelper
     */
    protected $stringHelper;

    /**
     * @var null|FlowPackageInterface
     */
    protected $cachedPromptResult = null;

    /**
     * @param null|string $input
     * @return FlowPackageInterface
     */
    public function resolve(?string $input): DistributionPackageInterface
    {
        if ($input === null) {
            if ($distributionPackage = $this->distributionPackageRepository->findDefault()) {
                return $distributionPackage;
            } elseif ($distributionPackage = $this->distributionPackageRepository->findFirstAvailable()) {
                return $distributionPackage;
            } else {
                throw new \Exception('No distribution packages were found.');
            }
        } elseif ($input === '.') {
            return $this->resolveFromPrompt();
        } elseif ($distributionPackage = $this->distributionPackageRepository->findOneByPackageKey(PackageKey::fromString($input))) {
            return $distributionPackage;
        } else {
            throw new \Exception('Could not resolve package "' . $input . '".');
        }
    }

    /**
     * @return DistributionPackageInterface
     */
    public function resolveFromPrompt(): DistributionPackageInterface
    {
        $options  = [];
        foreach ($this->distributionPackageRepository->findAll() as $distributionPackage) {
            $options[] = $distributionPackage->getPackageKey()->asString();
        }

        $input = $this->io->radio('Please choose a package:', $options);
        $chosenPackageKey = PackageKey::fromString($input->prompt());

        if ($distributionPackage = $this->distributionPackageRepository->findOneByPackageKey($chosenPackageKey)) {
            return $distributionPackage;
        }

        throw new \Exception('Could not find package "' . $chosenPackageKey->asString() . '".');
    }
}
