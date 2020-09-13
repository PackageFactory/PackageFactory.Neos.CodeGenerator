<?php declare(strict_types=1);
namespace PackageFactory\Neos\CodeGenerator\Infrastructure;

/*
 * This file is part of the PackageFactory.Neos.CodeGenerator package
 */

use Neos\Flow\Annotations as Flow;
use Neos\Flow\ObjectManagement\ObjectManagerInterface;
use PackageFactory\Neos\CodeGenerator\Domain\Pattern\GeneratorInterface;
use PackageFactory\Neos\CodeGenerator\Domain\Pattern\Pattern;

/**
 * @Flow\Scope("singleton")
 */
final class GeneratorResolver
{
    /**
     * @Flow\Inject
     * @var ObjectManagerInterface
     */
    protected $objectManager;

    /**
     * @param Pattern $pattern
     * @return GeneratorInterface
     */
    public function resolve(Pattern $pattern): GeneratorInterface
    {
        $generator = $this->objectManager->get($pattern->getGeneratorClassName());
        assert($generator instanceof GeneratorInterface);

        return $generator;
    }
}
