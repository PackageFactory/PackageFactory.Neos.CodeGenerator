<?php declare(strict_types=1);
namespace PackageFactory\Neos\CodeGenerator\Pattern\PresentationObjects;

/*
 * This file is part of the PackageFactory.AtomicFusion.PresentationObjects package
 */

use Neos\Flow\Annotations as Flow;
use Neos\Flow\Package\FlowPackageInterface;
use PackageFactory\Neos\CodeGenerator\Domain\Code\PhpNamespace;

/**
 * @Flow\Proxy(false)
 */
final class Enum
{
    /**
     * @var PhpNamespace
     */
    private $packageNamespace;

    /**
     * @var PhpNamespace
     */
    private $subNamespace;

    /**
     * @var string
     */
    private $className;

    /**
     * @var array<string>
     */
    private $values;

    /**
     * @param PhpNamespace $packageNamespace
     * @param PhpNamespace $subNamespace
     * @param string $className
     * @param array<string> $values
     */
    public function __construct(
        PhpNamespace $packageNamespace,
        PhpNamespace $subNamespace,
        string $className,
        array $values
    ) {
        $this->packageNamespace = $packageNamespace;
        $this->subNamespace = $subNamespace;
        $this->className = $className;
        $this->values = $values;
    }

    /**
     * @param array<string> $arguments
     * @param FlowPackageInterface $flowPackage
     * @return self
     */
    public static function fromArguments(array $arguments, FlowPackageInterface $flowPackage): self
    {
        assert(isset($arguments[0]), new \InvalidArgumentException('No sub-namespace was given'));
        assert(isset($arguments[1]), new \InvalidArgumentException('No class name was given!'));

        $packageNamespace = PhpNamespace::fromFlowPackage($flowPackage);
        $subNamespace = PhpNamespace::fromString($arguments[0]);
        $className = $arguments[1];
        $values = [];

        foreach (array_slice($arguments, 2) as $argument) {
            foreach (explode(',', $argument) as $value) {
                $values[] = trim($value);
            }
        }

        return new self($packageNamespace, $subNamespace, $className, $values);
    }

    /**
     * @return PhpNamespace
     */
    public function getPackageNamespace(): PhpNamespace
    {
        return $this->packageNamespace;
    }

    /**
     * @return PhpNamespace
     */
    public function getSubNamespace(): PhpNamespace
    {
        return $this->subNamespace;
    }

    /**
     * @return PhpNamespace
     */
    public function getNamespace(): PhpNamespace
    {
        return $this->packageNamespace->appendString('Presentation')->append($this->subNamespace);
    }

    /**
     * @return string
     */
    public function getClassName(): string
    {
        return $this->className;
    }

    /**
     * @return string
     */
    public function getFullyQualifiedClassName(): string
    {
        return $this->getNamespace()->appendString($this->className)->getValue();
    }

    /**
     * @return array<string>
     */
    public function getValues(): array
    {
        return $this->values;
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
        $result[] = 'final class ' . $this->className . ' extends Enum';
        $result[] = '{';
        $result[] = '}';

        return join(PHP_EOL, $result);
    }
}
