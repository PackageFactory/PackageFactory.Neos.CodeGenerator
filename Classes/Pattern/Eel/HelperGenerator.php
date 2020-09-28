<?php declare(strict_types=1);
namespace PackageFactory\Neos\CodeGenerator\Pattern\Eel;

/*
 * This file is part of the PackageFactory.Neos.CodeGenerator package
 */

use Neos\Flow\Annotations as Flow;
use PackageFactory\Neos\CodeGenerator\Domain\Files\FileWriterInterface;
use PackageFactory\Neos\CodeGenerator\Domain\Pattern\GeneratorInterface;
use PackageFactory\Neos\CodeGenerator\Domain\Pattern\GeneratorQuery;

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
     * @param GeneratorQuery $query
     * @return void
     */
    public function generate(GeneratorQuery $query): void
    {
        $helper = $this->helperFactory->fromGeneratorQuery($query);

        $this->fileWriter->write($helper->asPhpClassFile());
        $this->fileWriter->write($helper->asAppendedSettingForFusionDefaultContext());
    }
}
