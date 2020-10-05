<?php declare(strict_types=1);
namespace PackageFactory\Neos\CodeGenerator\Pattern\Eel;

/*
 * This file is part of the PackageFactory.Neos.CodeGenerator package
 */

use Neos\Flow\Annotations as Flow;
use PackageFactory\Neos\CodeGenerator\Domain\Code\Common\Signature\SignatureInterface;
use PackageFactory\Neos\CodeGenerator\Domain\Code\Php\Import\Import;
use PackageFactory\Neos\CodeGenerator\Domain\Code\Php\PhpClass\PhpClassName;
use PackageFactory\Neos\CodeGenerator\Domain\Code\Php\PhpFile;
use PackageFactory\Neos\CodeGenerator\Domain\Code\Php\PhpFileBuilder;
use PackageFactory\Neos\CodeGenerator\Domain\Code\YamlFile;
use PackageFactory\Neos\CodeGenerator\Domain\Flow\DistributionPackageInterface;

/**
 * @Flow\Proxy(false)
 */
final class Helper
{
    /**
     * @var DistributionPackageInterface
     */
    private $distributionPackage;

    /**
     * @var string
     */
    private $name;

    /**
     * @var SignatureInterface
     */
    private $signature;

    /**
     * @var string
     */
    private $defaultContextIdentifier;

    /**
     * @param DistributionPackageInterface $distributionPackage
     * @param string $name
     * @param SignatureInterface $signature
     * @param string $defaultContextIdentifier
     */
    public function __construct(
        DistributionPackageInterface $distributionPackage,
        string $name,
        SignatureInterface $signature,
        string $defaultContextIdentifier
    ) {
        $this->distributionPackage = $distributionPackage;
        $this->name = $name;
        $this->signature = $signature;
        $this->defaultContextIdentifier = $defaultContextIdentifier;
    }

    /**
     * @return PhpClassName
     */
    public function getPhpClassName(): PhpClassName
    {
        return $this->distributionPackage->getPackageKey()->asPhpNamespace()
            ->append('Application\\Eel')
            ->append($this->name . 'Helper')
            ->asClassName();
    }

    /**
     * @return PhpFile
     */
    public function asPhpClassFile(): PhpFile
    {
        $builder = new PhpFileBuilder();

        $builder->setPath($this->distributionPackage->getPhpFilePathForClassName($this->getPhpClassName()));
        $builder->setNamespaceFromClassName($this->getPhpClassName());
        $builder->setSignature($this->signature);
        $builder->getImportCollectionBuilder()->addImport(new Import('Neos\\Flow\\Annotations', 'Flow'));
        $builder->getImportCollectionBuilder()->addImport(new Import('Neos\\Eel\\ProtectedContextAwareInterface', null));

        $code = [];

        $code[] = '/**';
        $code[] = ' * @Flow\Scope("singleton")';
        $code[] = ' */';
        $code[] = 'final class ' . $this->getPhpClassName()->asDeclarationNameString() . ' implements ProtectedContextAwareInterface';
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
        $settingsFile = YamlFile::fromConfigurationInDistributionPackage($this->distributionPackage, 'Settings.Eel.Helpers.yaml');

        $settings = $settingsFile->getData();
        $settings['Neos']['Fusion']['defaultContext'][$this->defaultContextIdentifier] = $this->getPhpClassName()->asNamespace()->asString();

        return $settingsFile->withData($settings);
    }
}
