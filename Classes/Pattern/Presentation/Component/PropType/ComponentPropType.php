<?php declare(strict_types=1);
namespace PackageFactory\Neos\CodeGenerator\Pattern\Presentation\Component\PropType;

/*
 * This file is part of the PackageFactory.Neos.CodeGenerator package
 */

use Neos\Flow\Annotations as Flow;
use PackageFactory\Neos\CodeGenerator\Domain\Code\Fusion\Identifier\PrototypeName;
use PackageFactory\Neos\CodeGenerator\Pattern\Presentation\Component\Prop\Prop;
use PackageFactory\Neos\CodeGenerator\Framework\Util\StringUtil;

/**
 * @Flow\Proxy(false)
 */
final class ComponentPropType implements PropTypeInterface
{
    /**
     * @var PrototypeName
     */
    private $prototypeName;

    /**
     * @var Prop[]
     */
    private $props;

    /**
     * @param PrototypeName $prototypeName
     * @param Prop[] $props
     */
    public function __construct(
        PrototypeName $prototypeName,
        array $props
    ) {
        $this->prototypeName = $prototypeName;
        $this->props = $props;
    }

    /**
     * @return string
     */
    public function asExampleValue(): string
    {
        $code = [];

        $code[] = '{';
        foreach ($this->props as $prop) {
            $code[] = StringUtil::indent($prop->asExampleValueAssignment(), '    ');
        }
        $code[] = '}';

        return join(PHP_EOL, $code);
    }

    /**
     * @return string
     */
    public function asDummyAfxMarkup(): string
    {
        $code = [];
        $code[] = '<' . $this->prototypeName->asString();
        $code[] = '    presentationObject={%s}';
        $code[] = '    />';

        return join(PHP_EOL, $code);
    }
}
