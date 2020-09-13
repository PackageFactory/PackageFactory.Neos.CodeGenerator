<?php declare(strict_types=1);
namespace PackageFactory\Neos\CodeGenerator\Pattern\PresentationObjects;

/*
 * This file is part of the PackageFactory.Neos.CodeGenerator package
 */

use Neos\Flow\Annotations as Flow;
use Neos\Flow\Package\FlowPackageInterface;
use PackageFactory\Neos\CodeGenerator\Domain\Code\PhpFile;
use PackageFactory\Neos\CodeGenerator\Domain\Code\PhpNamespace;
use PackageFactory\Neos\CodeGenerator\Domain\Pattern\GeneratorInterface;
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
     * @param FlowPackageInterface $flowPackage
     * @param array<string> $arguments
     * @return void
     */
    public function generate(FlowPackageInterface $flowPackage, array $arguments): void
    {
        $model = Model::fromArguments($arguments, $flowPackage);

        $phpFileForModel = PhpFile::fromFlowPackageAndNamespace($flowPackage, $model->getNamespace(), $model->getClassName())
            ->withBody($model->getBody());
        $phpFileForModelInterface = PhpFile::fromFlowPackageAndNamespace($flowPackage, $model->getNamespace(), $model->getInterfaceName())
            ->withBody($model->getInterfaceBody());

        $this->fileWriter->write($phpFileForModel);
        $this->fileWriter->write($phpFileForModelInterface);
    }
}
