<?php declare(strict_types=1);
namespace PackageFactory\Neos\CodeGenerator\Domain\Input\TypeDescription;

/*
 * This file is part of the PackageFactory.Neos.CodeGenerator package
 */

use Neos\Flow\Annotations as Flow;

/**
 * @Flow\Proxy(false)
 */
final class TypeDescription implements TypeDescriptionInterface
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
     * @param TypeDescriptionTemplateInterface $template
     * @return TypeDescriptionInterface
     */
    public function withTemplate(TypeDescriptionTemplateInterface $template): TypeDescriptionInterface
    {
        $string = $this->value;
        $string = str_replace('/', '\\', $this->value);

        $string = preg_replace_callback(
            '/([A-Z][_a-zA-Z0-9]+(\.[A-Z][_a-zA-Z0-9]+)+)(\\\\[A-Z][_a-zA-Z0-9]+)+/',
            static function (array $matches) use($template): string {
                return $template->resolvePackageReference(
                    str_replace('.', '\\', $matches[1]),
                    ltrim($matches[3], '\\')
                );
            },
            $string
        );

        $string = preg_replace_callback(
            '/(^|[\?\<\{\s])([A-Z][_a-zA-Z0-9]+(\\\\[A-Z][_a-zA-Z0-9]+)+)/',
            static function (array $matches) use($template): string {
                return $matches[1] . $template->resolveRelativeNamespace(ltrim($matches[2], '\\'));
            },
            $string
        );

        return new self($string);
    }
}
