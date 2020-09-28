<?php declare(strict_types=1);
namespace PackageFactory\Neos\CodeGenerator\Domain\Code\Php\Import;

/*
 * This file is part of the PackageFactory.Neos.CodeGenerator package
 */

use Neos\Flow\Annotations as Flow;

/**
 * @Flow\Proxy(false)
 */
final class Import implements ImportInterface
{
    /**
     * @var string
     */
    private $fullyQualifiedName;

    /**
     * @var null|string
     */
    private $alias;

    /**
     * @var string
     */
    private $name;

    /**
     * @param string $fullyQualifiedName
     * @param string|null $alias
     */
    public function __construct(
        string $fullyQualifiedName,
        ?string $alias
    ) {
        $this->fullyQualifiedName = $fullyQualifiedName;
        $this->alias = $alias;

        if ($this->alias) {
            $this->name = $this->alias;
        } else {
            $this->name = substr($this->fullyQualifiedName, strrpos($this->fullyQualifiedName, '\\') + 1);
        }
    }

    /**
     * @return string
     */
    public function getFullyQualifiedName(): string
    {
        return $this->fullyQualifiedName;
    }

    /**
     * @return null|string
     */
    public function getAlias(): ?string
    {
        return $this->alias;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $alias
     * @return ImportInterface
     */
    public function withAlias(string $alias): ImportInterface
    {
        return new self($this->fullyQualifiedName, $alias);
    }
}
