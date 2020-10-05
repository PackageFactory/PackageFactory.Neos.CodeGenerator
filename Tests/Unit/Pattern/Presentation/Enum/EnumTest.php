<?php declare(strict_types=1);
namespace PackageFactory\Neos\CodeGenerator\Tests\Unit\Pattern\Presentation\Enum;

use PackageFactory\Neos\CodeGenerator\Domain\Input\Query;
use PackageFactory\Neos\CodeGenerator\Pattern\Presentation\Enum\EnumFactory;
use PackageFactory\Neos\CodeGenerator\Pattern\Presentation\Enum\EnumGenerator;
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

        $this->inject($this->enumFactory, 'distributionPackageResolver', $this->distributionPackageResolver);
        $this->inject($this->enumFactory, 'signatureFactory', $this->signatureFactory);

        $this->inject($this->enumGenerator, 'phpClassRepository', $this->phpClassRepository);
        $this->inject($this->enumGenerator, 'enumFactory', $this->enumFactory);
        $this->inject($this->enumGenerator, 'fileWriter', $this->fileWriter);
    }

    /**
     * @test
     * @return void
     */
    public function createsEnumInDefaultPackage(): void
    {
        $query = Query::fromArray([
            'name' => 'Button/ButtonType',
            'values' => ['link', 'button', 'submit']
        ]);

        $this->enumGenerator->generate($query);

        $this->assertFileWasWritten('Vendor.Default/Classes/Presentation/Button/ButtonType.php');
        $this->assertPhpClassWasRegistered('\\Vendor\\Default\\Presentation\\Button\\ButtonType');
    }
}
