<?php declare(strict_types=1);
namespace PackageFactory\Neos\CodeGenerator\Pattern\Presentation\Model;

/*
 * This file is part of the PackageFactory.Neos.CodeGenerator package
 */

use Neos\Flow\Annotations as Flow;
use PackageFactory\Neos\CodeGenerator\Domain\Code\Common\Signature\SignatureInterface;
use PackageFactory\Neos\CodeGenerator\Domain\Code\Php\Import\Import;
use PackageFactory\Neos\CodeGenerator\Domain\Code\Php\Import\ImportCollectionInterface;
use PackageFactory\Neos\CodeGenerator\Domain\Code\Php\PhpClass\PhpClassName;
use PackageFactory\Neos\CodeGenerator\Domain\Code\Php\PhpFile;
use PackageFactory\Neos\CodeGenerator\Domain\Code\Php\PhpFileBuilder;
use PackageFactory\Neos\CodeGenerator\Domain\Code\Php\Property\PropertyInterface;
use PackageFactory\Neos\CodeGenerator\Pattern\Presentation\Presentation;

/**
 * @Flow\Proxy(false)
 */
final class Model
{
    /**
     * @var Presentation
     */
    private $presentation;

    /**
     * @var string
     */
    private $name;

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
     * @param Presentation $presentation
     * @param string $name
     * @param SignatureInterface $signature
     * @param ImportCollectionInterface $imports
     * @param PropertyInterface[] $properties
     */
    public function __construct(
        Presentation $presentation,
        string $name,
        SignatureInterface $signature,
        ImportCollectionInterface $imports,
        array $properties
    ) {
        $this->presentation = $presentation;
        $this->name = $name;
        $this->signature = $signature;
        $this->imports = $imports;
        $this->properties = $properties;
    }

    /**
     * @return ImportCollectionInterface
     */
    public function getImports(): ImportCollectionInterface
    {
        return $this->imports;
    }

    /**
     * @return PropertyInterface[]
     */
    public function getProperties(): array
    {
        return $this->properties;
    }

    /**
     * @return PhpClassName
     */
    public function getPhpClassNameForValueObject(): PhpClassName
    {
        return $this->presentation->getPhpNamespace()->append($this->name)->asClassName();
    }

    /**
     * @return PhpFile
     */
    public function asPhpClassFileForValueObject(): PhpFile
    {
        $builder = new PhpFileBuilder();

        $builder->setPath($this->presentation->getPhpFilePathForClassName($this->getPhpClassNameForValueObject()));
        $builder->setNamespaceFromClassName($this->getPhpClassNameForValueObject());
        $builder->setSignature($this->signature);
        $builder->getImportCollectionBuilder()->addImport(new Import('Neos\\Flow\\Annotations', 'Flow'));
        $builder->getImportCollectionBuilder()->addImport(new Import('PackageFactory\\AtomicFusion\\PresentationObjects\\Fusion\\AbstractComponentPresentationObject', null));
        $builder->getImportCollectionBuilder()->addImportCollection($this->imports);

        $code = [];

        $code[] = '/**';
        $code[] = ' * @Flow\Proxy(false)';
        $code[] = ' */';
        $code[] = 'final class ' . $this->getPhpClassNameForValueObject()->asDeclarationNameString() . ' extends AbstractComponentPresentationObject implements ' . $this->getPhpInterfaceName()->asDeclarationNameString();
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

    /**
     * @return PhpClassName
     */
    public function getPhpInterfaceName(): PhpClassName
    {
        return $this->presentation->getPhpNamespace()->append($this->name)->asClassName()->append('Interface');
    }

    /**
     * @return PhpFile
     */
    public function asPhpInterfaceFile(): PhpFile
    {
        $builder = new PhpFileBuilder();

        $builder->setPath($this->presentation->getPhpFilePathForClassName($this->getPhpInterfaceName()));
        $builder->setNamespaceFromClassName($this->getPhpInterfaceName());
        $builder->setSignature($this->signature);
        $builder->getImportCollectionBuilder()->addImport(new Import('PackageFactory\\AtomicFusion\\PresentationObjects\\Fusion\\ComponentPresentationObjectInterface', null));
        $builder->getImportCollectionBuilder()->addImportCollection($this->imports);

        $code = [];

        $code[] = 'interface ' . $this->getPhpInterfaceName()->asDeclarationNameString() . ' extends ComponentPresentationObjectInterface';
        $code[] = '{';
        if ($this->properties) {
            $code[] = join(PHP_EOL . PHP_EOL, array_map(function (PropertyInterface $property) {
                return $property->asGetterSignature();
            }, $this->properties));
        }
        $code[] = '}';
        $code[] = '';

        $builder->setCode(join(PHP_EOL, $code));

        return $builder->build();
    }

    /**
     * @return PhpClassName
     */
    public function getPhpClassNameForFactory(): PhpClassName
    {
        return $this->presentation->getPhpNamespace()->append($this->name)->asClassName()->append('Factory');
    }

    /**
     * @return PhpFile
     */
    public function asPhpClassFileForFactory(): PhpFile
    {
        $builder = new PhpFileBuilder();

        $builder->setPath($this->presentation->getPhpFilePathForClassName($this->getPhpClassNameForFactory()));
        $builder->setNamespaceFromClassName($this->getPhpClassNameForFactory());
        $builder->setSignature($this->signature);
        $builder->getImportCollectionBuilder()->addImport(new Import('Neos\\Flow\\Annotations', 'Flow'));
        $builder->getImportCollectionBuilder()->addImport(new Import('PackageFactory\\AtomicFusion\\PresentationObjects\\Fusion\\AbstractComponentPresentationObjectFactory', null));

        $code = [];

        $code[] = '/**';
        $code[] = ' * @Flow\Scope("singleton")';
        $code[] = ' */';
        $code[] = 'final class ' . $this->getPhpClassNameForFactory()->asDeclarationNameString() . ' extends AbstractComponentPresentationObjectFactory';
        $code[] = '{';
        $code[] = '}';
        $code[] = '';

        $builder->setCode(join(PHP_EOL, $code));

        return $builder->build();
    }
}
