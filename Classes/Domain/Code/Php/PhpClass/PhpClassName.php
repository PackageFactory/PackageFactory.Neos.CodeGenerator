<?php declare(strict_types=1);
namespace PackageFactory\Neos\CodeGenerator\Domain\Code\Php\PhpClass;

/*
 * This file is part of the PackageFactory.Neos.CodeGenerator package
 */

use Neos\Flow\Annotations as Flow;
use Neos\Flow\Package\FlowPackageInterface;
use PackageFactory\Neos\CodeGenerator\Domain\Code\Php\PhpNamespace\PhpNamespace;
use PackageFactory\Neos\CodeGenerator\Domain\Files\Path;

/**
 * @Flow\Proxy(false)
 */
final class PhpClassName
{
    /**
     * @var string
     */
    private $value;

    /**
     * @param string $value
     */
    private function __construct(string $value)
    {
        if (!self::isValid($value)) {
            throw new \InvalidArgumentException('Invalid PhpClassName "' . $value . '".');
        }

        $this->value = $value;
    }

    /**
     * @param string $string
     * @return self
     */
    public static function fromString(string $string): self
    {
        return new self($string);
    }

    /**
     * @param string $string
     * @return boolean
     */
    public static function isValid(string $string): bool
    {
        return (bool) preg_match(
            '/^[\\\\][a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff\\\\]*[a-zA-Z0-9_\x7f-\xff]$/',
            $string
        );
    }

    /**
     * @return string
     */
    public function asFullyQualifiedNameString(): string
    {
        return $this->value;
    }

    /**
     * @return string
     */
    public function asDeclarationNameString(): string
    {
        $segments = explode('\\', $this->value);
        $lastSegment = array_pop($segments);

        assert($lastSegment !== null);

        return $lastSegment;
    }

    /**
     * @return PhpNamespace
     */
    public function asNamespace(): PhpNamespace
    {
        return PhpNamespace::fromString(ltrim($this->value, '\\'));
    }

    /**
     * @param string $suffix
     * @return PhpClassName
     */
    public function append(string $suffix): PhpClassName
    {
        return new self($this->value . $suffix);
    }

    /**
     * @param FlowPackageInterface $flowPackage
     * @return Path
     */
    public function asClassFilePathInFlowPackage(FlowPackageInterface $flowPackage): Path
    {
        $composerManifest = $flowPackage->getComposerManifest();

        if (isset($composerManifest['autoload']['psr-4'])) {
            foreach ($composerManifest['autoload']['psr-4'] as $namespaceAsString => $pathAsString) {
                $psr4Namespace = PhpNamespace::fromString(rtrim($namespaceAsString, '\\'));

                if ($this->asNamespace()->isDescendantOf($psr4Namespace)) {
                    return Path::fromString($flowPackage->getPackagePath())
                        ->appendString($pathAsString)
                        ->append($this->asNamespace()->truncateAscendant($psr4Namespace)->asPath())
                        ->withExtension('php');
                }
            }
        }

        throw new \DomainException('Could not find a suitable autload configuration for package "' . $flowPackage->getPackageKey() . '".');
    }
}
