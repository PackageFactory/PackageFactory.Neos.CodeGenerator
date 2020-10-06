<?php declare(strict_types=1);
namespace PackageFactory\Neos\CodeGenerator\Framework\Util;

/*
 * This file is part of the PackageFactory.Neos.CodeGenerator package
 */

final class StringUtil
{
    /**
     * @param string $string
     * @return string
     */
    public static function unindent(string $string): string
    {
        $nonEmptyLines = array_filter(explode(PHP_EOL, $string), static function($part) {
            return trim($part);
        });

        $indentSize = min(array_map(static function($line) {
            preg_match('/^[\s\t]*/', $line, $matches);
            return strlen($matches[0]);
        }, $nonEmptyLines));

        return join(PHP_EOL, array_map(static function($part) use ($indentSize) {
            return substr($part, $indentSize);
        }, $nonEmptyLines));
    }

    /**
     * @param string $string
     * @param string $indentationString
     * @return string
     */
    public static function indent(string $string, string $indentationString): string
    {
        return implode(PHP_EOL, array_map(
            static function ($part) use ($indentationString) {
                return $indentationString . $part;
            },
            explode(PHP_EOL, self::unindent($string)))
        );
    }

    /**
     * @param string $delimiter
     * @param string $string
     * @return string
     */
    public static function tail(string $delimiter, string $string): string
    {
        $segments = explode($delimiter, $string);
        assert(is_array($segments));

        $tail = array_pop($segments);
        assert(is_string($tail));

        return $tail;
    }

    /**
     * @param string $string
     * @return string
     */
    public static function camelCaseToSnakeCase(string $string): string
    {
        return preg_replace('/(?<!^)[A-Z]/', '_$0', $string) ?? $string;
    }

    /**
     * @param string $string
     * @return string
     */
    public static function kebabCase(string $string): string
    {
        $string = preg_replace('/\s+/', '-', ucwords($string)) ?? $string;
        $string = str_replace(['_', '.', '\\', '/'], '-', $string);
        $string = preg_replace('~(?<=\\w)([A-Z])~', '-$1', $string) ?? $string;

        return $string;
    }
}
