<?php declare(strict_types=1);
namespace PackageFactory\Neos\CodeGenerator\Domain\Input\TypeTemplate;

/*
 * This file is part of the PackageFactory.Neos.CodeGenerator package
 */

use Neos\Flow\Annotations as Flow;

/**
 * @Flow\Proxy(false)
 */
final class TypeTemplate
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
     * @return string
     */
    public function asString(): string
    {
        $string = $this->value;
        $string = str_replace('/', '\\', $this->value);

        $string = preg_replace_callback(
            '/([A-Z][_a-zA-Z0-9]+(\.[A-Z][_a-zA-Z0-9]+)+)(\\\\[A-Z][_a-zA-Z0-9]+)+/',
            static function (array $matches): string {
                return '\\' . str_replace('.', '\\', $matches[0]);
            },
            $string
        );

        return (string) $string;
    }

    /**
     * @return string
     */
    public function asAtomicString(): string
    {
        $string = $this->asString();

        preg_match('/[-_\.\:a-zA-Z0-9\/\\\\]+/', $string, $matches);
        $string = $matches[0];

        return $string;
    }

    /**
     * @phpstan-param array{foreignNamespace?: string, domesticNamespace?: string, localNamespace?: string} $values
     * @param array $values
     * @return TypeTemplate
     */
    public function substitute(array $values): TypeTemplate
    {
        $string = $this->value;
        $string = str_replace('/', '\\', $this->value);

        if (isset($values['foreignNamespace'])) {
            $string = preg_replace_callback(
                '/([A-Z][_a-zA-Z0-9]+(\.[A-Z][_a-zA-Z0-9]+)+)(\\\\[A-Z][_a-zA-Z0-9]+)+/',
                static function (array $matches) use($values): string {
                    return str_replace(
                        ['{package}', '{namespace}'],
                        [str_replace('.', '\\', $matches[1]), ltrim($matches[3], '\\')],
                        $values['foreignNamespace']
                    );
                },
                $string
            ) ?? $string;
        }

        if (isset($values['domesticNamespace'])) {
            $string = preg_replace_callback(
                '/(^|[\?\<\{\s])([A-Z][_a-zA-Z0-9]+(\\\\[A-Z][_a-zA-Z0-9]+)+)/',
                static function (array $matches) use($values): string {
                    return $matches[1] . str_replace('{namespace}', ltrim($matches[2], '\\'), $values['domesticNamespace']);
                },
                $string
            ) ?? $string;
        }

        if (isset($values['localNamespace'])) {
            $string = preg_replace_callback(
                '/(^|[\?\<\{\s])([A-Z][_a-zA-Z0-9]+)($|[\>\}\[\s])/',
                static function (array $matches) use($values): string {
                    return $matches[1] . str_replace('{namespace}', ltrim($matches[2], '\\'), $values['localNamespace']);
                },
                $string
            ) ?? $string;
        }

        return new self($string);
    }
}
