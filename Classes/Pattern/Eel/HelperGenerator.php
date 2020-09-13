<?php declare(strict_types=1);
namespace PackageFactory\Neos\CodeGenerator\Pattern\Eel;

/*
 * This file is part of the PackageFactory.Neos.CodeGenerator package
 */

use Neos\Flow\Annotations as Flow;
use PackageFactory\Neos\CodeGenerator\Domain\Code\YamlFile;
use PackageFactory\Neos\CodeGenerator\Domain\Pattern\GeneratorInterface;
use PackageFactory\Neos\CodeGenerator\Domain\Pattern\GeneratorQuery;
use PackageFactory\Neos\CodeGenerator\Infrastructure\FileWriter;
use PackageFactory\Neos\CodeGenerator\Infrastructure\PackageResolver;

/**
 * @Flow\Scope("singleton")
 */
final class HelperGenerator implements GeneratorInterface
{
    /**
     * @Flow\Inject
     * @var PackageResolver
     */
    protected $packageResolver;

    /**
     * @Flow\Inject
     * @var FileWriter
     */
    protected $fileWriter;

    /**
     * @param GeneratorQuery $query
     * @return void
     */
    public function generate(GeneratorQuery $query): void
    {
        $flowPackage = $this->packageResolver->resolve($query->getArgument(0, 'No package key was given!'));

        $helper = Helper::fromQuery($query->shiftArgument(), $flowPackage);
        $settingsFile = YamlFile::fromConfigurationInFlowPackage($flowPackage, 'Settings.Eel.Helpers.yaml');

        $this->fileWriter->write($helper->asPhpClassFile());
        $this->fileWriter->write($helper->asAppendedSettingForFusionDefaultContext($settingsFile));
    }
}
