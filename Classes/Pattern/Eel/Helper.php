<?php declare(strict_types=1);
namespace PackageFactory\Neos\CodeGenerator\Pattern\Eel;

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
use PackageFactory\Neos\CodeGenerator\Domain\Code\YamlFile;

/**
 * @Flow\Proxy(false)
 */
final class Helper
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
     * @var string
     */
    private $defaultContextIdentifier;

    /**
     * @param FlowPackageInterface $flowPackage
     * @param PhpClassName $className
     * @param SignatureInterface $signature
     * @param string $defaultContextIdentifier
     */
    public function __construct(
        FlowPackageInterface $flowPackage,
        PhpClassName $className,
        SignatureInterface $signature,
        string $defaultContextIdentifier
    ) {
        $this->flowPackage = $flowPackage;
        $this->className = $className;
        $this->signature = $signature;
        $this->defaultContextIdentifier = $defaultContextIdentifier;
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
        $builder->getImportCollectionBuilder()->addImport(new Import('Neos\\Flow\\Annotations', 'Flow'));
        $builder->getImportCollectionBuilder()->addImport(new Import('Neos\\Eel\\ProtectedContextAwareInterface', null));

        $code = [];

        $code[] = '/**';
        $code[] = ' * @Flow\Scope("singleton")';
        $code[] = ' */';
        $code[] = 'final class ' . $this->className->asDeclarationNameString() . ' implements ProtectedContextAwareInterface';
        $code[] = '{';
        $code[] = '    /**';
        $code[] = '     * All methods are considered safe';
        $code[] = '     *';
        $code[] = '     * @return boolean';
        $code[] = '     */';
        $code[] = '    public function allowsCallOfMethod($methodName)';
        $code[] = '    {';
        $code[] = '        return true;';
        $code[] = '    }';
        $code[] = '}';

        $builder->setCode(join(PHP_EOL, $code));

        return $builder->build();
    }

    /**
     * @return YamlFile
     */
    public function asAppendedSettingForFusionDefaultContext(): YamlFile
    {
        $settingsFile = YamlFile::fromConfigurationInFlowPackage($this->flowPackage, 'Settings.Eel.Helpers.yaml');

        $settings = $settingsFile->getData();
        $settings['Neos']['Fusion']['defaultContext'][$this->defaultContextIdentifier] = $this->className->asNamespace()->asString();

        return $settingsFile->withData($settings);
    }
}
