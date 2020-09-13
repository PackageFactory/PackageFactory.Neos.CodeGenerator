<?php declare(strict_types=1);
namespace PackageFactory\Neos\CodeGenerator\Pattern\PresentationObjects;

/*
 * This file is part of the PackageFactory.AtomicFusion.PresentationObjects package
 */

use Neos\Flow\Annotations as Flow;

/**
 * @Flow\Proxy(false)
 */
final class Enum
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var array
     */
    private $values;

    /**
     * @param string $name
     * @param array $values
     */
    public function __construct(
        string $name,
        array $values
    ) {
        $this->name = $name;
        $this->values = $values;
    }

    /**
     * @param array $arguments
     * @return self
     */
    public static function fromArguments(array $arguments): self
    {
        $name = array_shift($arguments);
        $values = [];

        foreach ($arguments as $argument) {
            foreach (explode(',', $argument) as $value) {
                $values[] = trim($value);
            }
        }

        return new self($name, $values);
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
    public function getBody(): string
    {
        $result = [];

        $result[] = 'use Neos\Flow\Annotations as Flow;';
        $result[] = 'use PackageFactory\AtomicFusion\PresentationObjects\Framework\Type\Enum;';
        $result[] = '';
        $result[] = '/**';

        foreach ($this->values as $value) {
            $result[] = ' * @method static self ' . $value . '()';
        }

        $result[] = ' *';
        $result[] = ' * @Flow\Proxy(false)';
        $result[] = ' */';
        $result[] = 'final class ' . $this->name . ' extends Enum';
        $result[] = '{';
        $result[] = '}';

        return join(PHP_EOL, $result);
    }
}
