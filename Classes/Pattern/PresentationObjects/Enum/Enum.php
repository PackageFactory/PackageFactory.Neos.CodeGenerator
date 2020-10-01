<?php declare(strict_types=1);
namespace PackageFactory\Neos\CodeGenerator\Pattern\PresentationObjects\Enum;

/*
 * This file is part of the PackageFactory.Neos.CodeGenerator package
 */

use Neos\Flow\Annotations as Flow;
use Neos\Flow\Package\FlowPackageInterface;
use PackageFactory\Neos\CodeGenerator\Domain\Code\Common\Signature\SignatureInterface;
use PackageFactory\Neos\CodeGenerator\Domain\Code\Php\Identifier\PhpClassName;
use PackageFactory\Neos\CodeGenerator\Domain\Code\Php\Import\Import;
use PackageFactory\Neos\CodeGenerator\Domain\Code\Php\PhpFile;
use PackageFactory\Neos\CodeGenerator\Domain\Code\Php\PhpFileBuilder;

/**
 * @Flow\Proxy(false)
 */
final class Enum
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
     * @var string[]
     */
    private $values;

    /**
     * @param FlowPackageInterface $flowPackage
     * @param PhpClassName $className
     * @param SignatureInterface $signature
     * @param string[] $values
     */
    public function __construct(
        FlowPackageInterface $flowPackage,
        PhpClassName $className,
        SignatureInterface $signature,
        array $values
    ) {
        $this->flowPackage = $flowPackage;
        $this->className = $className;
        $this->signature = $signature;
        $this->values = $values;
    }

    /**
     * @return PhpFile
     */
    public function asPhpClassFile(): PhpFile
    {
        $builder = new PhpFileBuilder();

        $builder->setPath($this->className->asClassFilePathInFlowPackage($this->flowPackage));

        $namespace = $this->className->asNamespace()->getParentNamespace();
        assert($namespace !== null);
        $builder->setNamespace($namespace);

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
        $code[] = 'final class ' . $this->className->asDeclarationNameString() . ' extends Enum';
        $code[] = '{';
        $code[] = '}';
        $code[] = '';

        $builder->setCode(join(PHP_EOL, $code));

        return $builder->build();
    }
}
