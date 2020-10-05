<?php declare(strict_types=1);
namespace PackageFactory\Neos\CodeGenerator\Pattern\Presentation\Enum;

/*
 * This file is part of the PackageFactory.Neos.CodeGenerator package
 */

use Neos\Flow\Annotations as Flow;

/**
 * @Flow\Proxy(false)
 */
final class EnumTypeIsInvalid extends \DomainException
{
    /**
     * @param string $attemptedValue
     * @return self
     */
    public static function becauseItMustBeOneOfTheDefinedConstants(string $attemptedValue): self
    {
        return new self('The given value "' . $attemptedValue . '" is no valid EnumType, because it must be one of "' . join('", "', EnumType::getValues()) . '".', 1601909839);
    }
}
