<?php declare(strict_types=1);
namespace PackageFactory\Neos\CodeGenerator\Pattern\PresentationObjects;

/*
 * This file is part of the PackageFactory.Neos.CodeGenerator package
 */

use Neos\Flow\Annotations as Flow;
use Neos\Flow\Package\FlowPackageInterface;
use PackageFactory\Neos\CodeGenerator\Domain\Code\FusionFile;
use PackageFactory\Neos\CodeGenerator\Domain\Code\PhpNamespace;
use PackageFactory\Neos\CodeGenerator\Domain\Pattern\GeneratorInterface;
use PackageFactory\Neos\CodeGenerator\Infrastructure\FileWriter;

/**
 * @Flow\Scope("singleton")
 */
final class ComponentGenerator implements GeneratorInterface
{
    /**
     * @Flow\Inject
     * @var ModelGenerator
     */
    protected $modelGenerator;

    /**
     * @Flow\Inject
     * @var FileWriter
     */
    protected $fileWriter;

    /**
     * @param FlowPackageInterface $flowPackage
     * @param array<string> $arguments
     * @return void
     */
    public function generate(FlowPackageInterface $flowPackage, array $arguments): void
    {
        $model = Model::fromArguments($arguments, $flowPackage);
        $component = Component::fromModel($model);

        $fusionFileForComponent = FusionFile::fromFlowPackage($flowPackage, $component->getLocation(), $model->getClassName())
        ->withBody($component->getBody());

        $this->modelGenerator->generate($flowPackage, $arguments);
        $this->fileWriter->write($fusionFileForComponent);
    }
}
