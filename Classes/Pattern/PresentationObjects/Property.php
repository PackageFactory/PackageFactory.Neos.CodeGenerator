<?php declare(strict_types=1);
namespace PackageFactory\Neos\CodeGenerator\Pattern\PresentationObjects;

/*
 * This file is part of the PackageFactory.Neos.CodeGenerator package
 */

use Neos\Flow\Annotations as Flow;
use Neos\Flow\Package\FlowPackageInterface;
use PackageFactory\Neos\CodeGenerator\Domain\Code\PhpNamespace;
use PackageFactory\Neos\CodeGenerator\Infrastructure\PackageResolver;

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
     * @param PackageResolver $packageResolver
     * @param string $indentation
     * @return string
     */
    public function asSampleForFusionStyleguide(PackageResolver $packageResolver, string $indentation): string
    {
        return $indentation . $this->name . ' ' . $this->type->asSampleForFusionStyleguide($packageResolver, $indentation);
    }

    /**
     * @param PackageResolver $packageResolver
     * @return string
     */
    public function asMarkupForFusionStyleguide(PackageResolver $packageResolver): string
    {
        if ($this->type->isBuiltIn()) {
            return join(PHP_EOL, [
                '            <dt>' . $this->name . '</dt>',
                '            <dd>{presentationObject.' . $this->name . '}</dd>',
            ]);
        } elseif ($this->type->refersToPresentationModel()) {
            /** @phpstan-var class-string $fullyQualifiedName */
            $fullyQualifiedName = $this->type->getFullyQualifiedName();

            if ($model = Model::fromClassName($fullyQualifiedName, $packageResolver)) {
                $component = Component::fromModel($model);

                return join(PHP_EOL, [
                    '            <dt>' . $this->name . '</dt>',
                    '            <dd><' . $component->getPrototypeName() . ' presentationObject={presentationObject.' . $this->name . '}/></dd>',
                ]);
            }
        }

        if ($this->type->refersToExistingClass()) {
            if (method_exists($this->type->getFullyQualifiedName(), '__toString')) {
                return join(PHP_EOL, [
                    '            <dt>' . $this->name . '</dt>',
                    '            <dd>{presentationObject.' . $this->name . '}</dd>',
                ]);
            } elseif (is_subclass_of($this->type->getFullyQualifiedName(), \JsonSerializable::class)) {
                return join(PHP_EOL, [
                    '            <dt>' . $this->name . '</dt>',
                    '            <dd><pre>{Json.stringify(presentationObject.' . $this->name . ', [\'JSON_PRETTY_PRINT\'])}</pre></dd>',
                ]);
            } else {
                return join(PHP_EOL, [
                    '            <dt>' . $this->name . '</dt>',
                    '            <dd><pre>Not renderable: presentationObject.' . $this->name . ' (' . $this->type->getFullyQualifiedName() . ')</pre></dd>',
                ]);
            }
        } else {
            return join(PHP_EOL, [
                '            <dt>' . $this->name . '</dt>',
                '            <dd><pre>Unknown type: presentationObject.' . $this->name . ' (' . $this->type->getFullyQualifiedName() . ')</pre></dd>',
            ]);
        }
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
