<?php declare(strict_types=1);
namespace PackageFactory\Neos\CodeGenerator\Pattern\PresentationObjects\Value;

/*
 * This file is part of the PackageFactory.Neos.CodeGenerator package
 */

use Neos\Flow\Annotations as Flow;
use Neos\Flow\Package\FlowPackageInterface;
use PackageFactory\Neos\CodeGenerator\Domain\Code\Common\Signature\SignatureInterface;
use PackageFactory\Neos\CodeGenerator\Domain\Code\Php\Identifier\PhpClassName;
use PackageFactory\Neos\CodeGenerator\Domain\Code\Php\Import\Import;
use PackageFactory\Neos\CodeGenerator\Domain\Code\Php\Import\ImportCollectionInterface;
use PackageFactory\Neos\CodeGenerator\Domain\Code\Php\PhpFile;
use PackageFactory\Neos\CodeGenerator\Domain\Code\Php\PhpFileBuilder;
use PackageFactory\Neos\CodeGenerator\Domain\Code\Php\Property\PropertyInterface;

/**
 * @Flow\Proxy(false)
 */
final class Value
{
    /**
     * @var FlowPackageInterface
     */
    private $flowPackage;

    /**
     * @var PhpClassName
     */
    private $className;

    /**
     * @var SignatureInterface
     */
    private $signature;

    /**
     * @var ImportCollectionInterface
     */
    private $imports;

    /**
     * @var PropertyInterface[]
     */
    private $properties;

    /**
     * @param FlowPackageInterface $flowPackage
     * @param PhpClassName $className
     * @param SignatureInterface $signature
     * @param ImportCollectionInterface $imports
     * @param PropertyInterface[] $properties
     */
    public function __construct(
        FlowPackageInterface $flowPackage,
        PhpClassName $className,
        SignatureInterface $signature,
        ImportCollectionInterface $imports,
        array $properties
    ) {
        $this->flowPackage = $flowPackage;
        $this->className = $className;
        $this->signature = $signature;
        $this->imports = $imports;
        $this->properties = $properties;
    }

    /**
     * @return PhpFile
     */
    public function asPhpClassFile(): PhpFile
    {
        $builder = new PhpFileBuilder();

        $builder->setPath($this->className->asClassFilePathInFlowPackage($this->flowPackage));
        $builder->setNamespace($this->className->asNamespace()->getParentNamespace());
        $builder->setSignature($this->signature);
        $builder->getImportCollectionBuilder()
            ->addImport(new Import('Neos\\Flow\\Annotations', 'Flow'))
            ->addImportCollection($this->imports);

        $code = [];

        $code[] = '/**';
        $code[] = ' * @Flow\Proxy(false)';
        $code[] = ' */';
        $code[] = 'final class ' . $this->className->asDeclarationNameString();
        $code[] = '{';

        if ($this->properties) {
            $code[] = join(PHP_EOL . PHP_EOL, array_map(function (PropertyInterface $property) {
                return $property->asClassPropertyDeclaration();
            }, $this->properties));
        }

        if ($this->properties) {
            $code[] = '';
            $code[] = '    /**';
            $code[] = join(PHP_EOL, array_map(function (PropertyInterface $property) {
                return '     * @param ' . $property->asDocBlockString();
            }, $this->properties));
            $code[] = '     */';
            $code[] = '    public function __construct(';
            $code[] = join(',' . PHP_EOL, array_map(function (PropertyInterface $property) {
                return '        ' . $property->asFunctionParameterDeclaration();
            }, $this->properties));
            $code[] = '    ) {';
            $code[] = join(PHP_EOL, array_map(function (PropertyInterface $property) {
                return  '        ' . $property->asConstructorAssignment();
            }, $this->properties));
            $code[] = '    }';
        }

        if ($this->properties) {
            $code[] = '';
            $code[] = join(PHP_EOL . PHP_EOL, array_map(function (PropertyInterface $property) {
                return $property->asGetterImplementation();
            }, $this->properties));
        }

        $code[] = '}';
        $code[] = '';

        $builder->setCode(join(PHP_EOL, $code));

        return $builder->build();
    }
}
