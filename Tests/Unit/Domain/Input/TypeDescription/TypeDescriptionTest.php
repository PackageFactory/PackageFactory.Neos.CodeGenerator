<?php declare(strict_types=1);
namespace PackageFactory\Neos\CodeGenerator\Tests\Unit\Domain\Code\Php\Import;

use Neos\Flow\Tests\UnitTestCase;
use PackageFactory\Neos\CodeGenerator\Domain\Code\Php\PhpNamespace\PhpNamespace;
use PackageFactory\Neos\CodeGenerator\Domain\Flow\DistributionPackageNamespace;
use PackageFactory\Neos\CodeGenerator\Domain\Flow\PackageKey;
use PackageFactory\Neos\CodeGenerator\Domain\Input\TypeDescription\TypeDescription;
use PackageFactory\Neos\CodeGenerator\Domain\Input\TypeDescription\TypeDescriptionTemplateInterface;

final class TypeDescriptionTest extends UnitTestCase
{
    /**
     * @return array<string,array{string,array<mixed>}>
     */
    public function validTypeDescriptions(): array
    {
        return [
            'string' => [
                'string',
                [
                    'asString' => 'string',
                    'with adopted namespace' => 'string',
                ],
            ],
            'mixed' => [
                'mixed',
                [
                    'asString' => 'mixed',
                    'with adopted namespace' => 'mixed',
                ],
            ],
            'custom:descriptor' => [
                'custom:descriptor',
                [
                    'asString' => 'custom:descriptor',
                    'with adopted namespace' => 'custom:descriptor',
                ],
            ],
            'even.more:custom.descriptor' => [
                'even.more:custom.descriptor',
                [
                    'asString' => 'even.more:custom.descriptor',
                    'with adopted namespace' => 'even.more:custom.descriptor',
                ],
            ],
            'alternative-custom-descriptor' => [
                'alternative-custom-descriptor',
                [
                    'asString' => 'alternative-custom-descriptor',
                    'with adopted namespace' => 'alternative-custom-descriptor',
                ],
            ],
            '\\DateTimeInterface' => [
                '\\DateTimeInterface',
                [
                    'asString' => '\\DateTimeInterface',
                    'with adopted namespace' => '\\DateTimeInterface',
                ],
            ],
            '\\Iterator<mixed, string>' => [
                '\\Iterator<mixed, string>',
                [
                    'asString' => '\\Iterator<mixed, string>',
                    'with adopted namespace' => '\\Iterator<mixed, string>',
                ],
            ],
            '/Neos/Eel/Helper/StringHelper' => [
                '/Neos/Eel/Helper/StringHelper',
                [
                    'asString' => '\\Neos\\Eel\\Helper\\StringHelper',
                    'with adopted namespace' => '\\Neos\\Eel\\Helper\\StringHelper',
                ],
            ],
            'Vendor.Site/Application' => [
                'Vendor.Site/Application',
                [
                    'asString' => '\\Vendor\\Site\\Application',
                    'with adopted namespace' => '\\Vendor\\Site\\Domain\\Service\\Application',
                ],
            ],
            'Vendor.Site/Foo<Vendor.Shared/Bar>' => [
                'Vendor.Site/Foo<Vendor.Shared/Bar>',
                [
                    'asString' => '\\Vendor\\Site\\Foo<\\Vendor\\Shared\\Bar>',
                    'with adopted namespace' => '\\Vendor\\Site\\Domain\\Service\\Foo<\\Vendor\\Shared\\Domain\\Service\\Bar>',
                ],
            ],
            'Alignment/Horizontal' => [
                'Alignment/Horizontal',
                [
                    'asString' => 'Alignment\\Horizontal',
                    'with adopted namespace' => '\\Vendor\\Adopted\\Domain\\Service\\Alignment\\Horizontal',
                ],
            ],
            '?Alignment/Horizontal' => [
                '?Alignment/Horizontal',
                [
                    'asString' => '?Alignment\\Horizontal',
                    'with adopted namespace' => '?\\Vendor\\Adopted\\Domain\\Service\\Alignment\\Horizontal',
                ],
            ]
        ];
    }

    /**
     * @test
     * @dataProvider validTypeDescriptions
     * @param string $input
     * @param array<string,string> $expectations
     * @return void
     */
    public function acceptsValidTypeDescriptions(string $input, array $expectations): void
    {
        $typeDescription = TypeDescription::fromString($input);

        $this->assertEquals($expectations['asString'], $typeDescription->asString());
    }

    /**
     * @test
     * @dataProvider validTypeDescriptions
     * @param string $input
     * @param array<string,string> $expectations
     * @return void
     */
    public function adoptsDistributionPackageNamespace(string $input, array $expectations): void
    {
        $template = new class implements TypeDescriptionTemplateInterface {
            public function resolvePackageReference(string $package, string $namespace): string
            {
                return '\\' . $package . '\\Domain\\Service\\' . $namespace;
            }

            public function resolveRelativeNamespace(string $namespace): string
            {
                return '\\Vendor\\Adopted\\Domain\\Service\\' . $namespace;
            }
        };
        $typeDescription = TypeDescription::fromString($input)->withTemplate($template);

        $this->assertEquals($expectations['with adopted namespace'], $typeDescription->asString());
    }
}
