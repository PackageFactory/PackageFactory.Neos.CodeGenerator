<?php declare(strict_types=1);
namespace PackageFactory\Neos\CodeGenerator\Tests\Unit\Pattern\PresentationObjects\Enum;

use PackageFactory\Neos\CodeGenerator\Domain\Pattern\GeneratorQuery;
use PackageFactory\Neos\CodeGenerator\Pattern\PresentationObjects\Enum\EnumFactory;
use PackageFactory\Neos\CodeGenerator\Pattern\PresentationObjects\Enum\EnumGenerator;
use PackageFactory\Neos\CodeGenerator\Tests\Unit\Pattern\PatternTestCase;

final class EnumTest extends PatternTestCase
{
    /**
     * @var EnumGenerator
     */
    private $enumGenerator;

    /**
     * @var EnumFactory
     */
    private $enumFactory;

    protected function setUp(): void
    {
        parent::setUp();

        $this->enumFactory = new EnumFactory();
        $this->enumGenerator = new EnumGenerator();

        $this->inject($this->enumFactory, 'packageResolver', $this->packageResolver);
        $this->inject($this->enumFactory, 'signatureFactory', $this->signatureFactory);

        $this->inject($this->enumGenerator, 'enumFactory', $this->enumFactory);
        $this->inject($this->enumGenerator, 'fileWriter', $this->fileWriter);
    }

    /**
     * @test
     * @return void
     */
    public function createsEnumInDefaultPackage(): void
    {
        $query = GeneratorQuery::fromArray([
            'name' => 'Button/ButtonType',
            'values' => ['link', 'button', 'submit']
        ]);

        $this->enumGenerator->generate($query);

        $this->assertFileWasWritten('Vendor.Default/Classes/Presentation/Button/ButtonType.php');
    }
}
