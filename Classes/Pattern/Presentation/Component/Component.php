<?php declare(strict_types=1);
namespace PackageFactory\Neos\CodeGenerator\Pattern\Presentation\Component;

/*
 * This file is part of the PackageFactory.Neos.CodeGenerator package
 */

use Neos\Flow\Annotations as Flow;
use PackageFactory\Neos\CodeGenerator\Domain\Code\Common\Signature\SignatureInterface;
use PackageFactory\Neos\CodeGenerator\Domain\Code\Fusion\FusionFile;
use PackageFactory\Neos\CodeGenerator\Domain\Code\Fusion\FusionFileBuilder;
use PackageFactory\Neos\CodeGenerator\Domain\Code\Fusion\Identifier\PrototypeName;
use PackageFactory\Neos\CodeGenerator\Framework\Util\StringUtil;
use PackageFactory\Neos\CodeGenerator\Pattern\Presentation\Model\Model;
use PackageFactory\Neos\CodeGenerator\Pattern\Presentation\Prop\Prop;
use PackageFactory\Neos\CodeGenerator\Pattern\Presentation\Presentation;

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
     * @var Model
     */
    private $model;

    /**
     * @var SignatureInterface
     */
    private $signature;

    /**
     * @var string
     */
    private $title;

    /**
     * @var Prop[]
     */
    private $props;

    /**
     * @param Presentation $presentation
     * @param string $name
     * @param Model $model
     * @param SignatureInterface $signature
     * @param string $title
     * @param Prop[] $props
     */
    public function __construct(
        Presentation $presentation,
        string $name,
        Model $model,
        SignatureInterface $signature,
        string $title,
        array $props
    ) {
        $this->presentation = $presentation;
        $this->name = $name;
        $this->model = $model;
        $this->signature = $signature;
        $this->title = $title;
        $this->props = $props;
    }

    /**
     * @return Model
     */
    public function getModel(): Model
    {
        return $this->model;
    }

    /**
     * @return PrototypeName
     */
    public function getFusionPrototypeName(): PrototypeName
    {
        return PrototypeName::fromString($this->presentation->getFusionPrototypeNamePrefix() . $this->name);
    }

    /**
     * @return Prop[]
     */
    public function getProps(): array
    {
        return $this->props;
    }

    /**
     * @return FusionFile
     */
    public function asFusionPrototypeFile(): FusionFile
    {
        $builder = new FusionFileBuilder();

        $builder->setPath($this->presentation->getFusionFilePathForComponentName($this->name));

        $code = [];

        $code[] = 'prototype(' . $this->getFusionPrototypeName()->asString() . ') < prototype(PackageFactory.AtomicFusion.PresentationObjects:PresentationObjectComponent) {';
        $code[] = '    @presentationObjectInterface = \'' . str_replace('\\', '\\\\', $this->model->getPhpInterfaceName()->asNamespace()->asString()) . '\'';
        $code[] = '';
        $code[] = '    @styleguide {';
        $code[] = '        title = \'' . $this->title . '\'';
        $code[] = '';
        $code[] = '        props {';
        foreach ($this->props as $prop) {
            $code[] = StringUtil::indent($prop->getStyleguideExample(), '            ');
        }
        $code[] = '        }';
        $code[] = '    }';
        $code[] = '';
        $code[] = '    renderer = afx`';
        $code[] = '        <figure>';
        $code[] = '            <figcaption>';
        $code[] = '                ' . $this->title . ' (' . $this->getFusionPrototypeName()->asString() . ')';
        $code[] = '            </figcaption>';
        $code[] = '            <dl>';
        foreach ($this->props as $prop) {
            $code[] = StringUtil::indent($prop->getAfxExample(), '                ');
        }
        $code[] = '            </dl>';
        $code[] = '        </figure>';
        $code[] = '    `';
        $code[] = '}';
        $code[] = '';

        $builder->setCode(join(PHP_EOL, $code));

        return $builder->build();
    }
}
