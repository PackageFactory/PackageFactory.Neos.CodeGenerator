<?php declare(strict_types=1);
namespace PackageFactory\Neos\CodeGenerator\Pattern\PresentationObjects\Enum;

/*
 * This file is part of the PackageFactory.Neos.CodeGenerator package
 */

use Neos\Flow\Annotations as Flow;
use Neos\Flow\Package\FlowPackageInterface;
use PackageFactory\Neos\CodeGenerator\Domain\Code\PhpFile;
use PackageFactory\Neos\CodeGenerator\Domain\Code\PhpNamespace;
use PackageFactory\Neos\CodeGenerator\Domain\Files\FileWriter;
use PackageFactory\Neos\CodeGenerator\Domain\Pattern\GeneratorInterface;

/**
 * The value generator domain service
 *
 * @Flow\Scope("singleton")
 */
final class EnumGenerator implements GeneratorInterface
{
    /**
     * @Flow\Inject
     * @var FileWriter
     */
    protected $fileWriter;

    /**
     * @param FlowPackageInterface $flowPackage
     * @param array $arguments
     * @return void
     */
    public function generate(FlowPackageInterface $flowPackage, array $arguments): void
    {
        $namespace = PhpNamespace::fromPath(array_shift($arguments));
        $enum = Enum::fromArguments($arguments);
        $phpFile = PhpFile::fromFlowPackageAndNamespace(
            $flowPackage,
            $namespace->prependString('Presentation'),
            $enum->getName()
        )->withBody($enum->getBody());

        $this->fileWriter->write($phpFile);
    }
}
