<?php declare(strict_types=1);
namespace PackageFactory\Neos\CodeGenerator\Pattern\PresentationObjects\Component;

/*
 * This file is part of the PackageFactory.Neos.CodeGenerator package
 */

use Neos\Flow\Annotations as Flow;
use PackageFactory\Neos\CodeGenerator\Domain\Code\Common\Signature\SignatureInterface;
use PackageFactory\Neos\CodeGenerator\Domain\Code\Fusion\FusionFile;
use PackageFactory\Neos\CodeGenerator\Domain\Code\Fusion\FusionFileBuilder;
use PackageFactory\Neos\CodeGenerator\Pattern\PresentationObjects\Component\Prop\PropInterface;
use PackageFactory\Neos\CodeGenerator\Framework\Util\StringUtil;
use PackageFactory\Neos\CodeGenerator\Pattern\PresentationObjects\Presentation;

/**
 * @Flow\Proxy(false)
 */
final class Component
{
    /**
     * @var Presentation
     */
    private $presentation;

    /**
     * @var string
     */
    private $name;

    /**
     * @var SignatureInterface
     */
    private $signature;

    /**
     * @var string
     */
    private $title;

    /**
     * @var PropInterface[]
     */
    private $props;

    /**
     * @param Presentation $presentation
     * @param string $name
     * @param SignatureInterface $signature
     * @param string $title
     * @param PropInterface[] $props
     */
    public function __construct(
        Presentation $presentation,
        string $name,
        SignatureInterface $signature,
        string $title,
        array $props
    ) {
        $this->presentation = $presentation;
        $this->name = $name;
        $this->signature = $signature;
        $this->title = $title;
        $this->props = $props;
    }

    /**
     * @return FusionFile
     */
    public function asFusionPrototypeFile(): FusionFile
    {
        $builder = new FusionFileBuilder();

        $builder->setPath($this->presentation->getFusionFilePathForComponentName($this->name));

        $code = [];

        $code[] = 'prototype(' . $this->presentation->getFusionPrototypeNamePrefix() . $this->name . ') < prototype(PackageFactory.Neos.CodeGenerator:PresentationObjectComponent) {';
        $code[] = '    @presentationObjectInterface = \'' . str_replace('\\', '\\\\', $this->name->asPhpInterfaceName()->asNamespace()->asString()) . '\'';
        $code[] = '';
        $code[] = '    @styleguide {';
        $code[] = '        title = \'' . $this->title . '\'';
        $code[] = '';
        $code[] = '        props {';
        foreach ($this->props as $prop) {
            $code[] = StringUtil::indent($prop->asExampleValueAssignment(), '            ');
        }
        $code[] = '        }';
        $code[] = '    }';
        $code[] = '';
        $code[] = '    renderer = afx`';
        $code[] = '        <dl>';
        foreach ($this->props as $prop) {
            $code[] = StringUtil::indent($prop->asDummyAfxMarkup(), '            ');
        }
        $code[] = '        </dl>';
        $code[] = '    `';
        $code[] = '}';
        $code[] = '';

        $builder->setCode(join(PHP_EOL, $code));

        return $builder->build();
    }
}
