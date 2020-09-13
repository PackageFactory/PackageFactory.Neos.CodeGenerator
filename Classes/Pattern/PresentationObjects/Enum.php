<?php declare(strict_types=1);
namespace PackageFactory\Neos\CodeGenerator\Pattern\PresentationObjects;

/*
 * This file is part of the PackageFactory.Neos.CodeGenerator package
 */

use Neos\Flow\Annotations as Flow;
use Neos\Flow\Package\FlowPackageInterface;
use PackageFactory\Neos\CodeGenerator\Domain\Code\PhpFile;
use PackageFactory\Neos\CodeGenerator\Domain\Code\PhpNamespace;
use PackageFactory\Neos\CodeGenerator\Domain\Pattern\GeneratorQuery;

/**
 * @Flow\Proxy(false)
 */
final class Enum
{
    /**
     * @var FlowPackageInterface
     */
    private $flowPackage;

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
     * @param FlowPackageInterface $flowPackage
     * @param PhpNamespace $subNamespace
     * @param string $className
     * @param array<string> $values
     */
    public function __construct(
        FlowPackageInterface $flowPackage,
        PhpNamespace $subNamespace,
        string $className,
        array $values
    ) {
        $this->flowPackage = $flowPackage;
        $this->subNamespace = $subNamespace;
        $this->className = $className;
        $this->values = $values;
    }

    /**
     * @param GeneratorQuery $query
     * @param FlowPackageInterface $flowPackage
     * @return self
     */
    public static function fromQuery(GeneratorQuery $query, FlowPackageInterface $flowPackage): self
    {
        $subNamespace = PhpNamespace::fromString($query->getArgument(0, 'No sub-namespace was given!'));
        $className = $query->getArgument(1, 'No class name was given!');
        $values = [];

        foreach ($query->getRemainingArguments(2) as $argument) {
            foreach (explode(',', $argument) as $value) {
                $values[] = trim($value);
            }
        }

        return new self($flowPackage, $subNamespace, $className, $values);
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
    public function getSubNamespace(): PhpNamespace
    {
        return $this->subNamespace;
    }

    /**
     * @return PhpNamespace
     */
    public function getNamespace(): PhpNamespace
    {
        return $this->getPackageNamespace()->appendString('Presentation')->append($this->subNamespace);
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
     * @return PhpFile
     */
    public function asPhpClassFile(): PhpFile
    {
        $body = [];

        $body[] = 'use Neos\Flow\Annotations as Flow;';
        $body[] = 'use PackageFactory\AtomicFusion\PresentationObjects\Framework\Type\Enum;';
        $body[] = '';
        $body[] = '/**';

        foreach ($this->values as $value) {
            $body[] = ' * @method static self ' . $value . '()';
        }

        $body[] = ' *';
        $body[] = ' * @Flow\Proxy(false)';
        $body[] = ' */';
        $body[] = 'final class ' . $this->className . ' extends Enum';
        $body[] = '{';
        $body[] = '}';

        return PhpFile::fromFlowPackageAndNamespace(
            $this->flowPackage,
            $this->getNamespace(),
            $this->getClassName(),
            join(PHP_EOL, $body)
        );
    }
}
