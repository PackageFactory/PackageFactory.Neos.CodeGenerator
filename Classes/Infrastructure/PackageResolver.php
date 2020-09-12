<?php declare(strict_types=1);
namespace PackageFactory\Neos\CodeGenerator\Infrastructure;

/*
 * This file is part of the PackageFactory.Neos.CodeGenerator package
 */

use Neos\Eel\Helper\StringHelper;
use Neos\Flow\Annotations as Flow;
use Neos\Flow\Cli\ConsoleOutput;
use Neos\Flow\Package\FlowPackageInterface;
use Neos\Flow\Package\PackageInterface;
use Neos\Flow\Package\PackageManager;

/**
 * @Flow\Scope("singleton")
 */
final class PackageResolver
{
    /**
     * @Flow\Inject
     * @var ConsoleOutput
     */
    protected $output;

    /**
     * @Flow\Inject
     * @var PackageManager
     */
    protected $packageManager;

    /**
     * @Flow\InjectConfiguration(path="packageResolution.defaultPackageKey")
     * @var string
     */
    protected $defaultPackageKey;

    /**
     * @Flow\Inject
     * @var StringHelper
     */
    protected $stringHelper;

    /**
     * @param string $input
     * @return Pattern
     */
    public function resolve(string $input): FlowPackageInterface
    {
        if ($input === '-') {
            return $this->resolveFromDefault();
        } elseif ($input === '.') {
            return $this->resolveFromPrompt();
        } else {
            return $this->resolveFromPackageKey($input);
        }
    }

    /**
     * @return FlowPackageInterface
     */
    public function resolveFromDefault(): FlowPackageInterface
    {
        if ($this->defaultPackageKey) {
            return $this->resolveFromPackageKey($this->defaultPackageKey);
        } else {
            foreach ($this->packageManager->getAvailablePackages() as $availablePackage) {
                /** @var PackageInterface $availablePackage */
                if ($availablePackage->getComposerManifest('type') === 'neos-site') {
                    return $availablePackage;
                }
            }
        }

        throw new \InvalidArgumentException('Could not resolve default package key. In order to proceed, please configure a default package key in your Settings.yaml under "PackageFactory.Neos.CodeGenerator.packageResolution.defaultPackageKey".');
    }

    /**
     * @return FlowPackageInterface
     */
    public function resolveFromPrompt(): FlowPackageInterface
    {
        $availablePackageKeys = [];
        foreach ($this->packageManager->getAvailablePackages() as $availablePackageKey => $availablePackage) {
            $packagePath = rtrim($availablePackage->getPackagePath(), DIRECTORY_SEPARATOR);
            if (is_link($packagePath)) {
                $packagePath = realpath($packagePath);
            }

            if ($this->stringHelper->startsWith($packagePath, FLOW_PATH_ROOT . 'DistributionPackages')) {
                $availablePackageKeys[] = $availablePackageKey;
            }
        }

        return $this->resolveFromPackageKey(
            $this->output->select(
                'In which package?',
                $availablePackageKeys
            )
        );
    }

    /**
     * @param string $packageKey
     * @return FlowPackageInterface
     */
    public function resolveFromPackageKey(string $packageKey): FlowPackageInterface
    {
        return $this->packageManager->getPackage($packageKey);
    }
}
