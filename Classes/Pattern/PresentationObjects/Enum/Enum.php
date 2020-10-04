<?php declare(strict_types=1);
namespace PackageFactory\Neos\CodeGenerator\Pattern\PresentationObjects\Enum;

/*
 * This file is part of the PackageFactory.Neos.CodeGenerator package
 */

use Neos\Flow\Annotations as Flow;
use PackageFactory\Neos\CodeGenerator\Domain\Code\Common\Signature\SignatureInterface;
use PackageFactory\Neos\CodeGenerator\Domain\Code\Php\PhpClass\PhpClassName;
use PackageFactory\Neos\CodeGenerator\Domain\Code\Php\Import\Import;
use PackageFactory\Neos\CodeGenerator\Domain\Code\Php\PhpClass\PhpClassInterface;
use PackageFactory\Neos\CodeGenerator\Domain\Code\Php\PhpFile;
use PackageFactory\Neos\CodeGenerator\Domain\Code\Php\PhpFileBuilder;
use PackageFactory\Neos\CodeGenerator\Pattern\PresentationObjects\Presentation;

/**
 * @Flow\Proxy(false)
 */
final class Enum implements PhpClassInterface
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
     * @var string[]
     */
    private $values;

    /**
     * @param Presentation $presentation
     * @param string $name
     * @param SignatureInterface $signature
     * @param string[] $values
     */
    public function __construct(
        Presentation $presentation,
        string $name,
        SignatureInterface $signature,
        array $values
    ) {
        $this->presentation = $presentation;
        $this->name = $name;
        $this->signature = $signature;
        $this->values = $values;
    }

    /**
     * @return PhpClassName
     */
    public function getClassName(): PhpClassName
    {
        return $this->presentation->getPhpNamespace()->append($this->name)->asClassName();
    }

    /**
     * @return string[]
     */
    public function getValues(): array
    {
        return $this->values;
    }

    /**
     * @return PhpFile
     */
    public function asPhpClassFile(): PhpFile
    {
        $builder = new PhpFileBuilder();
        $className = $this->getClassName();

        $builder->setPath($this->presentation->getPhpFilePathForClassName($className));
        $builder->setNamespaceFromClassName($className);
        $builder->setSignature($this->signature);
        $builder->getImportCollectionBuilder()
            ->addImport(new Import('PackageFactory\\AtomicFusion\\PresentationObjects\\Framework\\Type\\Enum', null));

        $code = [];

        $code[] = '';
        $code[] = '/**';

        foreach ($this->values as $value) {
            $code[] = ' * @method static self ' . $value . '()';
        }

        $code[] = ' *';
        $code[] = ' * @Flow\Proxy(false)';
        $code[] = ' */';
        $code[] = 'final class ' . $className->asDeclarationNameString() . ' extends Enum';
        $code[] = '{';
        $code[] = '}';
        $code[] = '';

        $builder->setCode(join(PHP_EOL, $code));

        return $builder->build();
    }
}
