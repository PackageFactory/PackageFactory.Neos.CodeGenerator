<?php declare(strict_types=1);
namespace PackageFactory\Neos\CodeGenerator\Pattern\PresentationObjects;

/*
 * This file is part of the PackageFactory.Neos.CodeGenerator package
 */

use Neos\Flow\Annotations as Flow;
use PackageFactory\Neos\CodeGenerator\Domain\Code\PhpFile;
use PackageFactory\Neos\CodeGenerator\Domain\Pattern\GeneratorInterface;
use PackageFactory\Neos\CodeGenerator\Domain\Pattern\GeneratorQuery;
use PackageFactory\Neos\CodeGenerator\Infrastructure\FileWriter;

/**
 * @Flow\Scope("singleton")
 */
final class ModelGenerator implements GeneratorInterface
{
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

        $phpFileForModel = PhpFile::fromFlowPackageAndNamespace($query->getFlowPackage(), $model->getNamespace(), $model->getClassName())
            ->withBody($model->getBody());
        $phpFileForModelInterface = PhpFile::fromFlowPackageAndNamespace($query->getFlowPackage(), $model->getNamespace(), $model->getInterfaceName())
            ->withBody($model->getInterfaceBody());

        $this->fileWriter->write($phpFileForModel);
        $this->fileWriter->write($phpFileForModelInterface);
    }
}
