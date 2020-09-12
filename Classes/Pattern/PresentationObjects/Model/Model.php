<?php declare(strict_types=1);
namespace PackageFactory\Neos\CodeGenerator\Pattern\PresentationObjects\Model;

/*
 * This file is part of the PackageFactory.AtomicFusion.PresentationObjects package
 */

use Neos\Flow\Annotations as Flow;
use Neos\Flow\Package\FlowPackageInterface;

/**
 * @Flow\Proxy(false)
 */
final class Model
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var array
     */
    private $properties;

    /**
     * @param string $name
     * @param array $properties
     */
    public function __construct(
        string $name,
        array $properties
    ) {
        $this->name = $name;
        $this->properties = $properties;
    }

    /**
     * @param array $arguments
     * @return self
     */
    public static function fromArguments(array $arguments, FlowPackageInterface $flowPackage): self
    {
        $name = array_shift($arguments);
        $properties = [];

        foreach ($arguments as $argument) {
            foreach (explode(',', $argument) as $descriptor) {
                $properties[] = Property::fromDescriptor(trim($descriptor), $flowPackage);
            }
        }

        return new self($name, $properties);
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getInterfaceName(): string
    {
        return $this->name . 'Interface';
    }

    /**
     * @return string
     */
    public function getBody(): string
    {
        $result = [];

        $result[] = 'use Neos\Flow\Annotations as Flow;';
        $result[] = join(PHP_EOL, array_unique(
            array_filter(
                array_map(function (Property $property) {
                    return $property->getType()->asImportStatement();
                }, $this->properties)
            )
        ));

        $result[] = '';
        $result[] = '/**';
        $result[] = ' * @Flow\Proxy(false)';
        $result[] = ' */';
        $result[] = 'final class ' . $this->name . ' implements ' . $this->getInterfaceName();
        $result[] = '{';

        $result[] = join(PHP_EOL . PHP_EOL, array_map(function (Property $property) {
            return $property->getDeclaration();
        }, $this->properties));

        $result[] = '';
        $result[] = '    /**';
        $result[] = join(PHP_EOL, array_map(function (Property $property) {
            return '     * @param ' . $property->getConstructorParameter();
        }, $this->properties));
        $result[] = '     */';
        $result[] = '    public function __constructor(';
        $result[] = join(',' . PHP_EOL, array_map(function (Property $property) {
            return '        ' . $property->getConstructorParameter();
        }, $this->properties));
        $result[] = '    ) {';
        $result[] = join(PHP_EOL, array_map(function (Property $property) {
            return  '        ' . $property->getConstructorAssignment();
        }, $this->properties));
        $result[] = '    }';

        $result[] = '';
        $result[] = join(PHP_EOL . PHP_EOL, array_map(function (Property $property) {
            return $property->getGetterImplementation() . PHP_EOL . PHP_EOL . $property->getSetterImplementationForModel($this);
        }, $this->properties));

        $result[] = '}';

        return join(PHP_EOL, $result);
    }

    /**
     * @return string
     */
    public function getInterfaceBody(): string
    {
        $result = [];

        $result[] = join(PHP_EOL, array_unique(
            array_filter(
                array_map(function (Property $property) {
                    return $property->getType()->asImportStatement();
                }, $this->properties)
            )
        ));

        if (count($result)) {
            $result[] = '';
        }

        $result[] = 'interface ' . $this->getInterfaceName();
        $result[] = '{';
        $result[] = join(PHP_EOL . PHP_EOL, array_map(function (Property $property) {
            return $property->getGetterSignature() . PHP_EOL . PHP_EOL . $property->getSetterSignatureForModel($this);
        }, $this->properties));
        $result[] = '}';

        return join(PHP_EOL, $result);
    }
}
