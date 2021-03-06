<?php declare(strict_types=1);
namespace PackageFactory\Neos\CodeGenerator\Pattern\Presentation\Prop;

/*
 * This file is part of the PackageFactory.Neos.CodeGenerator package
 */

use Neos\Flow\Annotations as Flow;
use PackageFactory\Neos\CodeGenerator\Domain\Code\Php\PhpClass\PhpClassName;
use PackageFactory\Neos\CodeGenerator\Framework\Util\StringUtil;
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
     * @phpstan-var array<string, array{type: string, example: array{presentation: array{styleguide: string, afx: string}}}>
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
        } else {
            $className = null;
            if (PhpClassName::isValid($string)) {
                $className = PhpClassName::fromString($string);
            } elseif(PhpClassName::isValid('\\' . $string)) {
                $className = PhpClassName::fromString('\\' . $string);
            }

            if ($className) {
                return $this->fromClassName($className);
            }
        }

        // @phpstan-ignore-next-line
        return new PropType('{' . PHP_EOL . '}', '{{prop}}');
    }

    /**
     * @param mixed $styleguideExample
     * @return PropType
     */
    public function fromExistingStyleguideExample($styleguideExample): PropType
    {
        if (is_array($styleguideExample)) {
            if (isset($styleguideExample['__objectType'])) {
                return new PropType(
                    join(PHP_EOL, [
                        $styleguideExample['__objectType'] . ' {',
                        join(PHP_EOL, array_filter(array_map(function (string $key) use ($styleguideExample) {
                            if (substr($key, 0, 2) === '__') {
                                return null;
                            } else {
                                return StringUtil::indent($key . ' = ' . $this->fromExistingStyleguideExample($styleguideExample[$key])->getStyleguideExample(), '    ');
                            }
                        }, array_keys($styleguideExample)))),
                        '}',
                    ]),
                    '{{prop}}'
                );
            } elseif (isset($styleguideExample['__eelExpression'])) {
                throw new \Exception('@TODO: Implement __eelExpression case');
            } elseif (isset($styleguideExample['__value'])) {
                throw new \Exception('@TODO: Implement __value case');
            }
            return new PropType(
                join(PHP_EOL, [
                    '{',
                    join(PHP_EOL, array_filter(array_map(function (string $key) use ($styleguideExample) {
                        if (substr($key, 0, 2) === '__') {
                            return null;
                        } else {
                            return StringUtil::indent($key . ' = ' . $this->fromExistingStyleguideExample($styleguideExample[$key])->getStyleguideExample(), '    ');
                        }
                    }, array_keys($styleguideExample)))),
                    '}',
                ]),
                '{{prop}}'
            );
        } elseif (is_string($styleguideExample)) {
            return new PropType('\'' . addcslashes(StringUtil::unindent($styleguideExample), '\'') . '\'', '{{prop}}');
        } elseif (is_int($styleguideExample) || is_float($styleguideExample)) {
            return new PropType((string) $styleguideExample, '{{prop}}');
        } elseif (is_bool($styleguideExample)) {
            return new PropType($styleguideExample ? 'true' : 'false', '{{prop}}');
        } elseif (is_null($styleguideExample)) {
            return new PropType('null', '{{prop}}');
        }

        return new PropType('{' . PHP_EOL . '}', '{{prop}}');
    }

    /**
     * @param PhpClassName $className
     * @return PropType
     */
    public function fromClassName(PhpClassName $className): PropType
    {
        $modelClassName = $className->asNamespace()->append($className->asDeclarationNameString())->asClassName();

        if ($component = $this->componentRepository->findOneByPhpClassName($className)) {
            return $this->fromComponent($component);
        } elseif ($component = $this->componentRepository->findOneByPhpClassName($modelClassName)) {
            return $this->fromComponent($component);
        } elseif ($model = $this->modelRepository->findOneByPhpClassName($className)) {
            return $this->fromModel($model);
        } elseif ($model = $this->modelRepository->findOneByPhpClassName($modelClassName)) {
            return $this->fromModel($model);
        } elseif ($value = $this->valueRepository->findOneByPhpClassName($className)) {
            return $this->fromValue($value);
        } elseif ($enum = $this->enumRepository->findOneByPhpClassName($className)) {
            return $this->fromEnum($enum);
        }

        return new PropType('{' . PHP_EOL . '}', '{{prop}}');
    }

    /**
     * @param Component $component
     * @return PropType
     */
    public function fromComponent(Component $component): PropType
    {
        return new PropType(
            join(PHP_EOL, [
                '{',
                join(PHP_EOL, array_map(function (Prop $prop) {
                    return StringUtil::indent($prop->getStyleguideExample(), '    ');
                }, $component->getProps())),
                '}',
            ]),
            join(PHP_EOL, [
                '<' . $component->getFusionPrototypeName()->asString(),
                '    presentationObject={{prop}}',
                '    />',
            ])
        );
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
        $value = $enum->getValues()[0];

        return new PropType(
            '${{ ' . $value->asComparatorMethodName() . ': true, value: ' . $enum->getType()->quoteValue($value) . ' }}',
            '{{prop}.value}'
        );
    }
}
