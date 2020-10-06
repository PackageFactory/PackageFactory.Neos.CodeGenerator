<?php declare(strict_types=1);
namespace PackageFactory\Neos\CodeGenerator\Tests\Unit\Domain\Code\Php\Import;

use Neos\Flow\Tests\UnitTestCase;
use PackageFactory\Neos\CodeGenerator\Domain\Input\TypeTemplate\TypeTemplate;

final class TypeTemplateTest extends UnitTestCase
{
    /**
     * @return array<string,array{string,array<mixed>}>
     */
    public function validTypeTemplates(): array
    {
        return [
            'string' => [
                'string',
                [
                    'asString' => 'string',
                    'asAtomicString' => 'string',
                    'substitute' => 'string',
                ],
            ],
            'mixed' => [
                'mixed',
                [
                    'asString' => 'mixed',
                    'asAtomicString' => 'mixed',
                    'substitute' => 'mixed',
                ],
            ],
            'custom:descriptor' => [
                'custom:descriptor',
                [
                    'asString' => 'custom:descriptor',
                    'asAtomicString' => 'custom:descriptor',
                    'substitute' => 'custom:descriptor',
                ],
            ],
            'even.more:custom.descriptor' => [
                'even.more:custom.descriptor',
                [
                    'asString' => 'even.more:custom.descriptor',
                    'asAtomicString' => 'even.more:custom.descriptor',
                    'substitute' => 'even.more:custom.descriptor',
                ],
            ],
            'alternative-custom-descriptor' => [
                'alternative-custom-descriptor',
                [
                    'asString' => 'alternative-custom-descriptor',
                    'asAtomicString' => 'alternative-custom-descriptor',
                    'substitute' => 'alternative-custom-descriptor',
                ],
            ],
            '\\DateTimeInterface' => [
                '\\DateTimeInterface',
                [
                    'asString' => '\\DateTimeInterface',
                    'asAtomicString' => '\\DateTimeInterface',
                    'substitute' => '\\DateTimeInterface',
                ],
            ],
            '\\Iterator<mixed, string>' => [
                '\\Iterator<mixed, string>',
                [
                    'asString' => '\\Iterator<mixed, string>',
                    'asAtomicString' => '\\Iterator',
                    'substitute' => '\\Iterator<mixed, string>',
                ],
            ],
            '/Neos/Eel/Helper/StringHelper' => [
                '/Neos/Eel/Helper/StringHelper',
                [
                    'asString' => '\\Neos\\Eel\\Helper\\StringHelper',
                    'asAtomicString' => '\\Neos\\Eel\\Helper\\StringHelper',
                    'substitute' => '\\Neos\\Eel\\Helper\\StringHelper',
                ],
            ],
            'Vendor.Site/Application' => [
                'Vendor.Site/Application',
                [
                    'asString' => '\\Vendor\\Site\\Application',
                    'asAtomicString' => '\\Vendor\\Site\\Application',
                    'substitute' => '\\Vendor\\Site\\Domain\\Service\\Application',
                ],
            ],
            'Vendor.Site/Foo<Vendor.Shared/Bar>' => [
                'Vendor.Site/Foo<Vendor.Shared/Bar>',
                [
                    'asString' => '\\Vendor\\Site\\Foo<\\Vendor\\Shared\\Bar>',
                    'asAtomicString' => '\\Vendor\\Site\\Foo',
                    'substitute' => '\\Vendor\\Site\\Domain\\Service\\Foo<\\Vendor\\Shared\\Domain\\Service\\Bar>',
                ],
            ],
            'Alignment/Horizontal' => [
                'Alignment/Horizontal',
                [
                    'asString' => 'Alignment\\Horizontal',
                    'asAtomicString' => 'Alignment\\Horizontal',
                    'substitute' => '\\Vendor\\Adopted\\Domain\\Service\\Alignment\\Horizontal',
                ],
            ],
            '?Alignment/Horizontal' => [
                '?Alignment/Horizontal',
                [
                    'asString' => '?Alignment\\Horizontal',
                    'asAtomicString' => 'Alignment\\Horizontal',
                    'substitute' => '?\\Vendor\\Adopted\\Domain\\Service\\Alignment\\Horizontal',
                ],
            ]
        ];
    }

    /**
     * @test
     * @dataProvider validTypeTemplates
     * @param string $input
     * @param array<string,string> $expectations
     * @return void
     */
    public function acceptsValidTypeTemplates(string $input, array $expectations): void
    {
        $TypeTemplate = TypeTemplate::fromString($input);

        $this->assertEquals($expectations['asString'], $TypeTemplate->asString());
    }

    /**
     * @test
     * @dataProvider validTypeTemplates
     * @param string $input
     * @param array<string,string> $expectations
     * @return void
     */
    public function aomtizesValidTypeTemplates(string $input, array $expectations): void
    {
        $TypeTemplate = TypeTemplate::fromString($input);

        $this->assertEquals($expectations['asAtomicString'], $TypeTemplate->asAtomicString());
    }

    /**
     * @test
     * @group isolated
     * @dataProvider validTypeTemplates
     * @param string $input
     * @param array<string,string> $expectations
     * @return void
     */
    public function adoptsDistributionPackageNamespace(string $input, array $expectations): void
    {
        $typeTemplate = TypeTemplate::fromString($input)->substitute([
            'foreignNamespace' => '\\{package}\\Domain\\Service\\{namespace}',
            'domesticNamespace' => '\\Vendor\\Adopted\\Domain\\Service\\{namespace}',
            'localNamespace' => '\\Vendor\\Adopted\\Domain\\Service\\TelephoneService',
        ]);

        $this->assertEquals($expectations['substitute'], $typeTemplate->asString());
    }
}
