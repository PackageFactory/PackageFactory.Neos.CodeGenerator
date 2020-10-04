<?php declare(strict_types=1);
namespace PackageFactory\Neos\CodeGenerator\Pattern\Eel;

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
final class HelperGenerator implements GeneratorInterface
{
    /**
     * @Flow\Inject
     * @var HelperFactory
     */
    protected $helperFactory;

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
        $helper = $this->helperFactory->fromQuery($query);

        $this->fileWriter->write($helper->asPhpClassFile());
        $this->fileWriter->write($helper->asAppendedSettingForFusionDefaultContext());
    }
}
