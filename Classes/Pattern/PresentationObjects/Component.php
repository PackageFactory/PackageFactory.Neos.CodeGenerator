<?php declare(strict_types=1);
namespace PackageFactory\Neos\CodeGenerator\Pattern\PresentationObjects;

/*
 * This file is part of the PackageFactory.AtomicFusion.PresentationObjects package
 */

use Neos\Flow\Annotations as Flow;

/**
 * @Flow\Proxy(false)
 */
final class Component
{
    /**
     * @var Model
     */
    private $model;

    /**
     * @param Model $model
     */
    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    /**
     * @param Model $model
     * @return self
     */
    public static function fromModel(Model $model): self
    {
        return new self($model);
    }

    /**
     * @return string
     */
    public function getPrototypeName(): string
    {
        $packageKey = $this->model->getPackageNamespace()->asKey();
        $prefix = $this->model->getSubNamespace()->asKey();

        if ($this->model->getSubNamespace()->getImportName() === $this->model->getClassName()) {
            return $packageKey . ':' . $prefix;
        } else {
            return $packageKey . ':' . $prefix . '.' . $this->model->getClassName();
        }
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->model->getClassName();
    }

    /**
     * @return string
     */
    public function getLocation(): string
    {
        return 'Presentation/' . $this->model->getSubNamespace()->asPath()->getValue();
    }

    /**
     * @return string
     */
    public function getBody(): string
    {
        $result = [];

        $result[] = 'prototype(' . $this->getPrototypeName() . ') < prototype(PackageFactory.AtomicFusion.PresentationObjects:PresentationObjectComponent) {';
        $result[] = '    @presentationObjectInterface = \'' . str_replace('\\', '\\\\', $this->model->getFullyQualifiedInterfaceName()) . '\'';
        $result[] = '';
        $result[] = '    @styleguide {';
        $result[] = '        title = \'' . $this->getTitle() . '\'';
        $result[] = '';
        $result[] = '        props {';
        $result[] = $this->model->asSampleForFusionStyleguide('            ');
        $result[] = '        }';
        $result[] = '    }';
        $result[] = '';
        $result[] = '    renderer = afx`';
        $result[] = '        <dl>';
        $result[] = join(PHP_EOL, array_map(function (Property $property) {
            return $property->asMarkupForFusionStyleguide($this->model->getPackageNamespace());
        }, $this->model->getProperties()));
        $result[] = '        </dl>';
        $result[] = '    `';
        $result[] = '}';

        return join(PHP_EOL, $result);
    }
}
