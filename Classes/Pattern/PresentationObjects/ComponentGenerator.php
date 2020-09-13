<?php declare(strict_types=1);
namespace PackageFactory\Neos\CodeGenerator\Pattern\PresentationObjects;

/*
 * This file is part of the PackageFactory.Neos.CodeGenerator package
 */

use Neos\Flow\Annotations as Flow;
use PackageFactory\Neos\CodeGenerator\Domain\Code\FusionFile;
use PackageFactory\Neos\CodeGenerator\Domain\Pattern\GeneratorInterface;
use PackageFactory\Neos\CodeGenerator\Domain\Pattern\GeneratorQuery;
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
     * @param GeneratorQuery $query
     * @return void
     */
    public function generate(GeneratorQuery $query): void
    {
        $model = Model::fromGeneratorQuery($query);
        $component = Component::fromModel($model);

        $fusionFileForComponent = FusionFile::fromFlowPackage($query->getFlowPackage(), $component->getLocation(), $model->getClassName())
        ->withBody($component->getBody());

        $this->modelGenerator->generate($query);
        $this->fileWriter->write($fusionFileForComponent);
    }
}
