<?php declare(strict_types=1);
namespace PackageFactory\Neos\CodeGenerator\Pattern\Presentation\Component;

/*
 * This file is part of the PackageFactory.Neos.CodeGenerator package
 */

use Neos\Flow\Annotations as Flow;
use PackageFactory\Neos\CodeGenerator\Domain\Files\FileWriterInterface;
use PackageFactory\Neos\CodeGenerator\Domain\Pattern\GeneratorInterface;
use PackageFactory\Neos\CodeGenerator\Domain\Input\Query;
use PackageFactory\Neos\CodeGenerator\Pattern\Presentation\Prop\PropTypeFactory;

/**
 * @Flow\Scope("singleton")
 */
final class ComponentGenerator implements GeneratorInterface
{
    /**
     * @Flow\Inject(lazy=false)
     * @var PropTypeFactory
     */
    protected $propTypeFactory;

    /**
     * @Flow\Inject
     * @var ComponentFactory
     */
    protected $componentFactory;

    /**
     * @Flow\Inject
     * @var ComponentRepository
     */
    protected $componentRepository;

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
        $component = $this->componentFactory->fromQuery($query, $this->propTypeFactory);

        $this->fileWriter->write($component->getModel()->asPhpClassFileForValueObject());
        $this->fileWriter->write($component->getModel()->asPhpInterfaceFile());
        $this->fileWriter->write($component->getModel()->asPhpClassFileForFactory());
        $this->fileWriter->write($component->asFusionPrototypeFile());

        $this->componentRepository->add($component);
    }
}
