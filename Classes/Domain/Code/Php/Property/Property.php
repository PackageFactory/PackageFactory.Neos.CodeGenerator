<?php declare(strict_types=1);
namespace PackageFactory\Neos\CodeGenerator\Domain\Code\Php\Property;

/*
 * This file is part of the PackageFactory.Neos.CodeGenerator package
 */

use Neos\Flow\Annotations as Flow;
use PackageFactory\Neos\CodeGenerator\Domain\Code\Php\Type\TypeInterface;

/**
 * @Flow\Proxy(false)
 */
final class Property implements PropertyInterface
{
    /**
     * @var TypeInterface
     */
    private $type;

    /**
     * @var string
     */
    private $name;

    /**
     * @param TypeInterface $type
     * @param string $name
     */
    public function __construct(
        TypeInterface $type,
        string $name
    ) {
        $this->type = $type;
        $this->name = $name;
    }

    /**
     * @return TypeInterface
     */
    public function getType(): TypeInterface
    {
        return $this->type;
    }

    /**
     * @param TypeInterface $type
     * @return self
     */
    public function withType(TypeInterface $type): self
    {
        return new self($type, $this->name);
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
    public function asClassPropertyDeclaration(): string
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
    public function asFunctionParameterDeclaration(): string
    {
        return $this->type->asPhpTypeHint() . ' $' . $this->name;
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
    public function asConstructorAssignment(): string
    {
        return '$this->' . $this->name . ' = $' . $this->name . ';';
    }

    /**
     * @return string
     */
    public function asGetterSignature(): string
    {
        return join(PHP_EOL, [
            '    /**',
            '     * @return ' . $this->type->asDocBlockString(),
            '     */',
            '    public function get' . ucfirst($this->name) . '(): ' . $this->type->asPhpTypeHint() . ';'
        ]);
    }

    /**
     * @return string
     */
    public function asGetterImplementation(): string
    {
        return join(PHP_EOL, [
            '    /**',
            '     * @return ' . $this->type->asDocBlockString(),
            '     */',
            '    public function get' . ucfirst($this->name) . '(): ' . $this->type->asPhpTypeHint(),
            '    {',
            '        return $this->' . $this->name . ';',
            '    }'
        ]);
    }

    /**
     * @param TypeInterface $returnType
     * @return string
     */
    public function asSetterSignature(TypeInterface $returnType): string
    {
        return join(PHP_EOL, [
            '    /**',
            '     * @param ' . $this->asDocBlockString(),
            '     * @return ' . $returnType->asDocBlockString(),
            '     */',
            '    public function with' . ucfirst($this->name) . '(' . $this->asFunctionParameterDeclaration() . '): ' . $returnType->asPhpTypeHint() . ';'
        ]);
    }

    /**
     * @param TypeInterface $returnType
     * @return string
     */
    public function getSetterImplementationForModel(TypeInterface $returnType): string
    {
        return join(PHP_EOL, [
            '    /**',
            '     * @param ' . $this->asDocBlockString(),
            '     * @return ' . $returnType->asDocBlockString(),
            '     */',
            '    public function with' . ucfirst($this->name) . '(' . $this->asFunctionParameterDeclaration() . '): ' . $returnType->asPhpTypeHint(),
            '    {',
            '         $next = clone $this;',
            '         $next->' . $this->name . ' = $' . $this->name . ';',
            '',
            '         return $next;',
            '    }'
        ]);
    }
}
