<?php declare(strict_types=1);
namespace PackageFactory\Neos\CodeGenerator\Pattern\PresentationObjects;

/*
 * This file is part of the PackageFactory.Neos.CodeGenerator package
 */

use Neos\Flow\Annotations as Flow;
use PackageFactory\Neos\CodeGenerator\Domain\Code\FusionFile;
use PackageFactory\Neos\CodeGenerator\Infrastructure\PackageResolver;

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

        if ($this->model->getSubNamespace()->getImportName() === $this->model->getValueObjectClassName()) {
            return $packageKey . ':' . $prefix;
        } else {
            return $packageKey . ':' . $prefix . '.' . $this->model->getValueObjectClassName();
        }
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->model->getValueObjectClassName();
    }

    /**
     * @return string
     */
    public function getLocation(): string
    {
        return 'Presentation/' . $this->model->getSubNamespace()->asPath()->getValue();
    }

    /**
     * @return FusionFile
     */
    public function asFusionPrototypeFile(PackageResolver $packageResolver): FusionFile
    {
        $body = [];

        $body[] = 'prototype(' . $this->getPrototypeName() . ') < prototype(PackageFactory.Neos.CodeGenerator:PresentationObjectComponent) {';
        $body[] = '    @presentationObjectInterface = \'' . str_replace('\\', '\\\\', $this->model->getFullyQualifiedInterfaceName()) . '\'';
        $body[] = '';
        $body[] = '    @styleguide {';
        $body[] = '        title = \'' . $this->getTitle() . '\'';
        $body[] = '';
        $body[] = '        props {';
        $body[] = $this->model->asSampleForFusionStyleguide($packageResolver, '            ');
        $body[] = '        }';
        $body[] = '    }';
        $body[] = '';
        $body[] = '    renderer = afx`';
        $body[] = '        <dl>';
        $body[] = join(PHP_EOL, array_map(function (Property $property) use ($packageResolver) {
            return $property->asMarkupForFusionStyleguide($packageResolver);
        }, $this->model->getProperties()));
        $body[] = '        </dl>';
        $body[] = '    `';
        $body[] = '}';

        return FusionFile::fromFlowPackage(
            $this->model->getFlowPackage(),
            $this->getLocation(),
            $this->model->getValueObjectClassName(),
            join(PHP_EOL, $body)
        );
    }
}
