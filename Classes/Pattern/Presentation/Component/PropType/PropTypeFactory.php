<?php declare(strict_types=1);
namespace PackageFactory\Neos\CodeGenerator\Pattern\Presentation\Component\PropType;

/*
 * This file is part of the PackageFactory.Neos.CodeGenerator package
 */

use Neos\Flow\Annotations as Flow;
use PackageFactory\Neos\CodeGenerator\Domain\Code\Fusion\Prototype\PrototypeRepositoryInterface;
use PackageFactory\Neos\CodeGenerator\Domain\Code\Php\PhpClass\PhpClassName;
use PackageFactory\Neos\CodeGenerator\Domain\Code\Php\PhpClass\PhpClassRepositoryInterface;
use PackageFactory\Neos\CodeGenerator\Pattern\Presentation\Enum\Enum;

/**
 * @Flow\Scope("singleton")
 */
final class PropTypeFactory
{
    /**
     * @Flow\InjectConfiguration(path="shorthands")
     * @var array<string, array{type: string, example: string}>
     */
    protected $shorthands;

    /**
     * @Flow\Inject
     * @var PhpClassRepositoryInterface
     */
    protected $phpClassRepository;

    /**
     * @Flow\Inject
     * @var PrototypeRepositoryInterface
     */
    protected $prototypeRepository;

    /**
     * @param string $string
     * @return PropTypeInterface
     */
    public function fromString(string $string): PropTypeInterface
    {
        if (isset($this->shorthands[$string])) {
            return new LeafPropType($this->shorthands[$string]['example']);
        } elseif (PhpClassName::isValid($string)) {
            $className = PhpClassName::fromString($string);

            if ($phpClass = $this->phpClassRepository->findOneByClassName($className)) {
                if ($phpClass instanceof Enum) {
                    return new LeafPropType('\'' . $phpClass->getValues()[0] . '\'');
                }
            }
        }

        return new LeafPropType('null');
    }
}
