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
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }
}
