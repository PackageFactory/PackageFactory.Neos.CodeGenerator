<?php declare(strict_types=1);
namespace PackageFactory\Neos\CodeGenerator\Pattern\PresentationObjects\Component\Prop;

/*
 * This file is part of the PackageFactory.Neos.CodeGenerator package
 */

use Neos\Flow\Annotations as Flow;
use PackageFactory\Neos\CodeGenerator\Pattern\PresentationObjects\Component\PropType\PropTypeInterface;
use PackageFactory\Neos\CodeGenerator\Framework\Util\StringUtil;

/**
 * @Flow\Proxy(false)
 */
final class Prop implements PropInterface
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var PropTypeInterface
     */
    private $type;

    /**
     * @param string $name
     * @param PropTypeInterface $type
     */
    public function __construct(
        string $name,
        PropTypeInterface $type
    ) {
        $this->name = $name;
        $this->type = $type;
    }

    /**
     * @return string
     */
    public function asExampleValueAssignment(): string
    {
        return $this->name . ' = ' . $this->type->asExampleValue();
    }

    /**
     * @return string
     */
    public function asDummyAfxMarkup(): string
    {
        $template = $this->type->asDummyAfxMarkup();

        $code = [];

        $code[] = '<dd>' . $this->name . ':</dd>';

        if (strpos($template, PHP_EOL) === false) {
            $code[] = '<dt>' . sprintf($template, 'presentationObject.' . $this->name) . '</dt>';
        } else {
            $code[] = '<dt>';
            $code[] = StringUtil::indent(sprintf($template, 'presentationObject.' . $this->name), '    ');
            $code[] = '</dt>';
        }

        return join(PHP_EOL, $code);
    }
}
