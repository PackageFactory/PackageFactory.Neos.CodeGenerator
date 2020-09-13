<?php declare(strict_types=1);
namespace PackageFactory\Neos\CodeGenerator\Pattern\Eel;

/*
 * This file is part of the PackageFactory.Neos.CodeGenerator package
 */

use Neos\Eel\Helper\StringHelper;
use Neos\Flow\Annotations as Flow;
use Neos\Flow\Package\FlowPackageInterface;
use PackageFactory\Neos\CodeGenerator\Domain\Code\PhpFile;
use PackageFactory\Neos\CodeGenerator\Domain\Code\PhpNamespace;
use PackageFactory\Neos\CodeGenerator\Domain\Code\YamlFile;
use PackageFactory\Neos\CodeGenerator\Domain\Pattern\GeneratorQuery;
use PackageFactory\Neos\CodeGenerator\Infrastructure\PackageResolver;

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
     * @var string
     */
    private $shortName;

    /**
     * @param FlowPackageInterface $flowPackage
     * @param string $shortName
     */
    public function __construct(
        FlowPackageInterface $flowPackage,
        string $shortName
    ) {
        $this->flowPackage = $flowPackage;
        $this->shortName = $shortName;
    }

    /**
     * @param GeneratorQuery $query
     * @param FlowPackageInterface $flowPackage
     * @return self
     */
    public static function fromQuery(GeneratorQuery $query, FlowPackageInterface $flowPackage): self
    {
        $shortName = $query->getArgument(0, 'No short name was given!');

        return new self($flowPackage, $shortName);
    }

    /**
     * @return PhpNamespace
     */
    public function getPackageNamespace(): PhpNamespace
    {
        return PhpNamespace::fromFlowPackage($this->flowPackage);
    }

    /**
     * @return PhpNamespace
     */
    public function getNamespace(): PhpNamespace
    {
        return $this->getPackageNamespace()->appendString('Application')->appendString('Eel');
    }

    /**
     * @return string
     */
    public function getShortName(): string
    {
        return $this->shortName;
    }

    /**
     * @return string
     */
    public function getClassName(): string
    {
        return $this->getShortName() . 'Helper';
    }

    /**
     * @return string
     */
    public function getFullyQualifiedClassName(): string
    {
        return $this->getNamespace()->appendString($this->getClassName())->getValue();
    }

    /**
     * @return string
     */
    public function getEelName(): string
    {
        return $this->getPackageNamespace()->asKey() . '.' . $this->getShortName();
    }

    /**
     * @return PhpFile
     */
    public function asPhpClassFile(): PhpFile
    {
        $body = [];

        $body[] = 'use Neos\Flow\Annotations as Flow;';
        $body[] = 'use Neos\Eel\ProtectedContextAwareInterface;';
        $body[] = '';

        $body[] = '/**';
        $body[] = ' * @Flow\Scope("singleton")';
        $body[] = ' */';
        $body[] = 'final class ' . $this->getClassName() . ' implements ProtectedContextAwareInterface';
        $body[] = '{';
        $body[] = '    /**';
        $body[] = '     * All methods are considered safe';
        $body[] = '     *';
        $body[] = '     * @return boolean';
        $body[] = '     */';
        $body[] = '    public function allowsCallOfMethod($methodName)';
        $body[] = '    {';
        $body[] = '        return true;';
        $body[] = '    }';
        $body[] = '}';

        return PhpFile::fromFlowPackageAndNamespace(
            $this->flowPackage,
            $this->getNamespace(),
            $this->getClassName(),
            join(PHP_EOL, $body)
        );
    }

    /**
     * @param YamlFile $settingsFile
     * @return YamlFile
     */
    public function asAppendedSettingForFusionDefaultContext(YamlFile $settingsFile): YamlFile
    {
        $settings = $settingsFile->getData();
        $settings['Neos']['Fusion']['defaultContext'][$this->getEelName()] = $this->getFullyQualifiedClassName();

        return $settingsFile->withData($settings);
    }
}
