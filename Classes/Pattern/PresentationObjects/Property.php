<?php declare(strict_types=1);
namespace PackageFactory\Neos\CodeGenerator\Pattern\PresentationObjects;

/*
 * This file is part of the PackageFactory.AtomicFusion.PresentationObjects package
 */

use Neos\Flow\Annotations as Flow;
use Neos\Flow\Package\FlowPackageInterface;

/**
 * @Flow\Proxy(false)
 */
final class Property
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var Type
     */
    private $type;

    /**
     * @param string $name
     * @param Type $type
     */
    private function __construct(string $name, Type $type)
    {
        $this->name = $name;
        $this->type = $type;
    }

    /**
     * @param string $descriptor
     * @param FlowPackageInterface $flowPackage
     * @return self
     */
    public static function fromDescriptor(string $descriptor, FlowPackageInterface $flowPackage): self
    {
        $parts = explode(':', $descriptor);
        if (count($parts) !== 2) {
            throw new \InvalidArgumentException('Descriptor "' . $descriptor . '" has an invalid format. The expected format is: "propertyName:typeName".');
        }

        return new self($parts[0], Type::fromDescriptor($parts[1], $flowPackage));
    }

    /**
     * @return Type
     */
    public function getType(): Type
    {
        return $this->type;
    }

    /**
     * @return string
     */
    public function asDocBlockString(): string
    {
        return $this->type->asDocBlockString() . ' $' . $this->name;
    }

    /**
     * @return string
     */
    public function asParameter(): string
    {
        return $this->type . ' $' . $this->name;
    }

    /**
     * @return string
     */
    public function getDeclaration(): string
    {
        return join(PHP_EOL, [
            '    /**',
            '     * @var ' . $this->type->asDocBlockString(),
            '     */',
            '    private $' . $this->name . ';',
        ]);
    }

    /**
     * @return string
     */
    public function getConstructorAssignment(): string
    {
        return '$this->' . $this->name . ' = $' . $this->name . ';';
    }

    /**
     * @return string
     */
    public function getGetterSignature(): string
    {
        return join(PHP_EOL, [
            '    /**',
            '     * @return ' . $this->type->asDocBlockString(),
            '     */',
            '    public function get' . ucfirst($this->name) . '(): ' . $this->type . ';'
        ]);
    }

    /**
     * @return string
     */
    public function getGetterImplementation(): string
    {
        return join(PHP_EOL, [
            '    /**',
            '     * @return ' . $this->type->asDocBlockString(),
            '     */',
            '    public function get' . ucfirst($this->name) . '(): ' . $this->type,
            '    {',
            '         return $this->' . $this->name . ';',
            '    }'
        ]);
    }

    /**
     * @param Model $model
     * @return string
     */
    public function getSetterSignatureForModel(Model $model): string
    {
        return join(PHP_EOL, [
            '    /**',
            '     * @param ' . $this->asDocBlockString(),
            '     * @return ' . $model->getInterfaceName(),
            '     */',
            '    public function with' . ucfirst($this->name) . '(' . $this->asParameter() . '): ' . $model->getInterfaceName() . ';'
        ]);
    }

    /**
     * @param Model $model
     * @return string
     */
    public function getSetterImplementationForModel(Model $model): string
    {
        return join(PHP_EOL, [
            '    /**',
            '     * @param ' . $this->asDocBlockString(),
            '     * @return ' . $model->getInterfaceName(),
            '     */',
            '    public function with' . ucfirst($this->name) . '(' . $this->asParameter() . '): ' . $model->getInterfaceName(),
            '    {',
            '         $next = clone $this;',
            '         $next->' . $this->name . ' = $' . $this->name . ';',
            '',
            '         return $next;',
            '    }'
        ]);
    }
}
