<?php declare(strict_types=1);
namespace PackageFactory\Neos\CodeGenerator\Pattern\Presentation\Prop;

/*
 * This file is part of the PackageFactory.Neos.CodeGenerator package
 */

use Neos\Flow\Annotations as Flow;

/**
 * @Flow\Proxy(false)
 */
final class PropType
{
    public const NAMES = ['bool', 'boolean', 'int', 'integer', 'float', 'string', 'unknown'];

    /**
     * @var string
     */
    private $styleguideExample;

    /**
     * @var string
     */
    private $afxExample;

    /**
     * @param string $styleguideExample
     * @param string $afxExample
     */
    public function __construct(string $styleguideExample, string $afxExample)
    {
        $this->styleguideExample = $styleguideExample;
        $this->afxExample = $afxExample;
    }

    /**
     * @return string
     */
    public function getStyleguideExample(): string
    {
        return $this->styleguideExample;
    }

    /**
     * @param array<string,string> $replaceMap
     * @return string
     */
    public function getAfxExample(array $replaceMap): string
    {
        return str_replace(
            array_map(function (string $key) { return '{' . $key . '}'; }, array_keys($replaceMap)),
            array_values($replaceMap),
            $this->afxExample
        );
    }
}
