<?php declare(strict_types=1);
namespace PackageFactory\Neos\CodeGenerator\Pattern\Domain;

/*
 * This file is part of the PackageFactory.Neos.CodeGenerator package
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
     * @param \ReflectionMethod $getter
     * @return null|self
     */
    public static function fromGetter(\ReflectionMethod $getter): ?self
    {
        $propertyName = lcfirst(\mb_substr($getter->getName(), 3));
        if ($type = Type::fromReflectionType($getter->getReturnType())) {
            return new self($propertyName, $type);
        } else {
            return null;
        }
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
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
    public function getGetterImplementation(): string
    {
        return join(PHP_EOL, [
            '    /**',
            '     * @return ' . $this->type->asDocBlockString(),
            '     */',
            '    public function get' . ucfirst($this->name) . '(): ' . $this->type,
            '    {',
            '        return $this->' . $this->name . ';',
            '    }'
        ]);
    }
}
