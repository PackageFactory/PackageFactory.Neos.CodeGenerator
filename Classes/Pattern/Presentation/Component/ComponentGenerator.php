<?php declare(strict_types=1);
namespace PackageFactory\Neos\CodeGenerator\Pattern\Presentation\Component;

/*
 * This file is part of the PackageFactory.Neos.CodeGenerator package
 */

use Neos\Flow\Annotations as Flow;
use PackageFactory\Neos\CodeGenerator\Domain\Files\FileWriterInterface;
use PackageFactory\Neos\CodeGenerator\Domain\Pattern\GeneratorInterface;
use PackageFactory\Neos\CodeGenerator\Domain\Input\Query;
use PackageFactory\Neos\CodeGenerator\Pattern\Presentation\Model\ModelFactory;

/**
 * @Flow\Scope("singleton")
 */
final class ComponentGenerator implements GeneratorInterface
{
    /**
     * @Flow\Inject
     * @var ModelFactory
     */
    protected $modelFactory;

    /**
     * @Flow\Inject
     * @var ComponentFactory
     */
    protected $componentFactory;

    /**
     * @Flow\Inject
     * @var FileWriterInterface
     */
    protected $fileWriter;

    /**
     * @param Query $query
     * @return void
     */
    public function generate(Query $query): void
    {
        $model = $this->modelFactory->fromQuery($query);
        $component = $this->componentFactory->fromQuery($query);

        $this->fileWriter->write($model->asPhpClassFileForValueObject());
        $this->fileWriter->write($model->asPhpInterfaceFile());
        $this->fileWriter->write($model->asPhpClassFileForFactory());
        $this->fileWriter->write($component->asFusionPrototypeFile());
    }
}
