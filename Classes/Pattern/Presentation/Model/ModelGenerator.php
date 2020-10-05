<?php declare(strict_types=1);
namespace PackageFactory\Neos\CodeGenerator\Pattern\Presentation\Model;

/*
 * This file is part of the PackageFactory.Neos.CodeGenerator package
 */

use Neos\Flow\Annotations as Flow;
use PackageFactory\Neos\CodeGenerator\Domain\Files\FileWriterInterface;
use PackageFactory\Neos\CodeGenerator\Domain\Pattern\GeneratorInterface;
use PackageFactory\Neos\CodeGenerator\Domain\Input\Query;

/**
 * @Flow\Scope("singleton")
 */
final class ModelGenerator implements GeneratorInterface
{
    /**
     * @var ModelRepository
     */
    private $modelRepository;

    /**
     * @Flow\Inject
     * @var ModelFactory
     */
    protected $modelFactory;

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

        $this->fileWriter->write($model->asPhpClassFileForValueObject());
        $this->fileWriter->write($model->asPhpInterfaceFile());
        $this->fileWriter->write($model->asPhpClassFileForFactory());

        $this->modelRepository->add($model);
    }
}
