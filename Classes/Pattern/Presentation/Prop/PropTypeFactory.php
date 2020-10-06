<?php declare(strict_types=1);
namespace PackageFactory\Neos\CodeGenerator\Pattern\Presentation\Prop;

/*
 * This file is part of the PackageFactory.Neos.CodeGenerator package
 */

use Neos\Flow\Annotations as Flow;
use PackageFactory\Neos\CodeGenerator\Domain\Code\Php\PhpClass\PhpClassName;
use PackageFactory\Neos\CodeGenerator\Pattern\Presentation\Component\Component;
use PackageFactory\Neos\CodeGenerator\Pattern\Presentation\Component\ComponentRepository;
use PackageFactory\Neos\CodeGenerator\Pattern\Presentation\Enum\Enum;
use PackageFactory\Neos\CodeGenerator\Pattern\Presentation\Enum\EnumRepository;
use PackageFactory\Neos\CodeGenerator\Pattern\Presentation\Model\Model;
use PackageFactory\Neos\CodeGenerator\Pattern\Presentation\Value\ValueRepository;
use PackageFactory\Neos\CodeGenerator\Pattern\Presentation\Model\ModelRepository;
use PackageFactory\Neos\CodeGenerator\Pattern\Presentation\Value\Value;

/**
 * @Flow\Scope("singleton")
 */
final class PropTypeFactory
{
    /**
     * @Flow\InjectConfiguration(path="shorthands")
     * @phpstan-var array<string, array{type: string, example: array{presentation: {styleguide: string, afx: string}}}>
     * @var array
     */
    protected $shorthands;

    /**
     * @Flow\Inject
     * @var ComponentRepository
     */
    protected $componentRepository;

    /**
     * @Flow\Inject
     * @var ModelRepository
     */
    protected $modelRepository;

    /**
     * @Flow\Inject
     * @var ValueRepository
     */
    protected $valueRepository;

    /**
     * @Flow\Inject
     * @var EnumRepository
     */
    protected $enumRepository;

    /**
     * @param string $string
     * @return PropType
     */
    public function fromString(string $string): PropType
    {
        if (isset($this->shorthands[$string]['example']['presentation'])) {
            $examples = $this->shorthands[$string]['example']['presentation'];

            return new PropType(
                $examples['styleguide'] ?? 'null',
                $examples['afx'] ?? '{{prop}}'
            );
        } elseif (PhpClassName::isValid($string)) {
            $className = PhpClassName::fromString($string);

            if ($component = $this->componentRepository->findOneByPhpClassName($className)) {
                return $this->fromComponent($component);
            } elseif ($model = $this->modelRepository->findOneByPhpClassName($className)) {
                return $this->fromModel($model);
            } elseif ($value = $this->valueRepository->findOneByPhpClassName($className)) {
                return $this->fromValue($value);
            } elseif ($enum = $this->enumRepository->findOneByPhpClassName($className)) {
                return $this->fromEnum($enum);
            }
        }

        return new PropType('{' . PHP_EOL . '}', '{{prop}}');
    }

    /**
     * @param Component $component
     * @return PropType
     */
    public function fromComponent(Component $component): PropType
    {
        throw new \Exception('@TODO: Create examples for component');
    }

    /**
     * @param Model $model
     * @return PropType
     */
    public function fromModel(Model $model): PropType
    {
        throw new \Exception('@TODO: Create examples for model');
    }

    /**
     * @param Value $value
     * @return PropType
     */
    public function fromValue(Value $value): PropType
    {
        throw new \Exception('@TODO: Create examples for value');
    }

    /**
     * @param Enum $enum
     * @return PropType
     */
    public function fromEnum(Enum $enum): PropType
    {
        return new PropType($enum->getType()->quoteValue($enum->getValues()[0]), '{{prop}.value}');
    }
}
