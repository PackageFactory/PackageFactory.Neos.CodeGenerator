<?php declare(strict_types=1);
namespace PackageFactory\Neos\CodeGenerator\Pattern\Presentation\Prop;

/*
 * This file is part of the PackageFactory.Neos.CodeGenerator package
 */

use Neos\Flow\Annotations as Flow;
use PackageFactory\Neos\CodeGenerator\Framework\Util\StringUtil;

/**
 * @Flow\Proxy(false)
 */
final class Prop
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var PropType
     */
    private $type;

    /**
     * @param string $name
     * @param PropType $type
     */
    public function __construct(
        string $name,
        PropType $type
    ) {
        $this->name = $name;
        $this->type = $type;
    }

    /**
     * @return string
     */
    public function getStyleguideExample(): string
    {
        $example = $this->type->getStyleguideExample();

        if ($example[0] === '{') {
            return $this->name . ' ' . $example;
        } else {
            return $this->name . ' = ' . $example;
        }
    }

    /**
     * @return string
     */
    public function getAfxExample(): string
    {
        $example = $this->type->getAfxExample(['prop' => 'presentationObject.' . $this->name]);
        $example = trim($example, PHP_EOL);

        $code = [];

        $code[] = '<dt>' . $this->name . ':</dt>';
        $code[] = '<dd>';
        $code[] = StringUtil::indent($example, '    ');
        $code[] = '</dd>';

        return join(PHP_EOL, $code);
    }
}
