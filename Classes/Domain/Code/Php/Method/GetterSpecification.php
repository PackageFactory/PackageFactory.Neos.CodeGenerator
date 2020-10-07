<?php declare(strict_types=1);
namespace PackageFactory\Neos\CodeGenerator\Domain\Code\Php\Method;

/*
 * This file is part of the PackageFactory.Neos.CodeGenerator package
 */

use Neos\Flow\Annotations as Flow;

/**
 * @Flow\Proxy(false)
 */
final class GetterSpecification
{
    /**
     * @param \ReflectionMethod $reflectionMethod
     * @return boolean
     */
    public static function isSatisfiedByReflectionMethod(\ReflectionMethod $reflectionMethod): bool
    {
        return self::isSatisfiedByMethodName($reflectionMethod->getName());
    }

    /**
     * @param string $methodName
     * @return boolean
     */
    public static function isSatisfiedByMethodName(string $methodName): bool
    {
        return strlen($methodName) >= 4 && substr($methodName, 0, 3) === 'get' && ctype_upper($methodName[3]);
    }
}
