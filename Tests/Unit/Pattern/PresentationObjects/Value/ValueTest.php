<?php declare(strict_types=1);
namespace PackageFactory\Neos\CodeGenerator\Tests\Unit\Pattern\PresentationObjects\Value;

use PackageFactory\Neos\CodeGenerator\Domain\Pattern\GeneratorQuery;
use PackageFactory\Neos\CodeGenerator\Pattern\PresentationObjects\Value\ValueFactory;
use PackageFactory\Neos\CodeGenerator\Pattern\PresentationObjects\Value\ValueGenerator;
use PackageFactory\Neos\CodeGenerator\Tests\Unit\Pattern\PatternTestCase;

final class ValueTest extends PatternTestCase
{
    /**
     * @var ValueGenerator
     */
    private $valueGenerator;

    /**
     * @var ValueFactory
     */
    private $valueFactory;

    protected function setUp(): void
    {
        parent::setUp();

        $this->valueFactory = new ValueFactory();
        $this->valueGenerator = new ValueGenerator();

        $this->inject($this->valueFactory, 'packageResolver', $this->packageResolver);
        $this->inject($this->valueFactory, 'signatureFactory', $this->signatureFactory);
        $this->inject($this->valueFactory, 'propertyFactory', $this->propertyFactory);

        $this->inject($this->valueGenerator, 'valueFactory', $this->valueFactory);
        $this->inject($this->valueGenerator, 'fileWriter', $this->fileWriter);
    }

    /**
     * @test
     * @return void
     */
    public function createsValueObjectInDefaultPackage(): void
    {
        $query = GeneratorQuery::fromArray([
            'name' => 'Alignment/Alignment',
            'properties' => [
                'horizontal' => 'HorizontalAlignment',
                'vertical' => 'VerticalAlignment'
            ]
        ]);

        $this->valueGenerator->generate($query);

        $this->assertFileWasWritten('Vendor.Default/Classes/Presentation/Alignment/Alignment.php');
    }
}
