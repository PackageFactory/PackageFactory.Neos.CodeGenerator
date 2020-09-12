<?php declare(strict_types=1);
namespace PackageFactory\Neos\CodeGenerator\Pattern\PresentationObjects\Model;

/*
 * This file is part of the PackageFactory.AtomicFusion.PresentationObjects package
 */

use Neos\Eel\Helper\StringHelper;
use Neos\Flow\Annotations as Flow;
use Neos\Flow\Package\FlowPackageInterface;
use PackageFactory\Neos\CodeGenerator\Domain\Code\PhpNamespace;

/**
 * @Flow\Proxy(false)
 */
final class Type
{
    public const SCALAR_TYPE_NAMES = ['bool', 'boolean', 'int', 'integer', 'float', 'string', 'array', 'iterable', 'object', 'callable'];

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $simpleName;

    /**
     * @var bool
     */
    private $nullable;

    /**
     * @param string $name
     * @param boolean $nullable
     */
    private function __construct(
        string $name,
        ?string $importName,
        bool $nullable
    ) {
        if ($name === 'boolean') {
            $name = 'bool';
        } elseif ($name === 'integer') {
            $name = 'int';
        }

        $this->name = $name;
        $this->importName = $importName;
        $this->nullable = $nullable;
    }

    /**
     * @param string $descriptor
     * @param FlowPackageInterface $flowPackage
     * @return self
     */
    public static function fromDescriptor(string $descriptor, FlowPackageInterface $flowPackage): self
    {
        $nullable = \mb_substr($descriptor, 0, 1) === '?';
        if ($nullable) {
            $descriptor = \mb_substr($descriptor, 1);
        }

        if (in_array($descriptor, self::SCALAR_TYPE_NAMES)) {
            return new self($descriptor, null, $nullable);
        }

        $stringHelper = new StringHelper();

        if ($stringHelper->startsWith($descriptor, '/') || $stringHelper->startsWith($descriptor, '\\')) {
            return new self($descriptor, null, $nullable);
        }

        $targetNamespace = PhpNamespace::fromFlowPackage($flowPackage)
            ->appendString('Presentation')
            ->appendString($descriptor);

        return new self($targetNamespace->getValue(), $targetNamespace->getImportName(), $nullable);
    }

    /**
     * @return string|null
     */
    public function asImportStatement(): ?string
    {
        if ($this->importName) {
            $stringHelper = new StringHelper();

            if ($this->name === $this->importName || $stringHelper->endsWith($this->name, '\\' . $this->importName)) {
                return 'use ' . $this->name . ';';
            } else {
                return 'use ' . $this->name . ' as ' . $this->importName . ';';
            }
        }

        return null;
    }

    /**
     * @return string
     */
    public function asDocBlockString(): string
    {
        $name = $this->importName ?? $this->name;

        if ($this->nullable) {
            return 'null|' . $name;
        } else {
            return $name;
        }
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        $name = $this->importName ?? $this->name;

        if ($this->nullable) {
            return '?' . $name;
        } else {
            return $name;
        }
    }
}
