<?php declare(strict_types=1);
namespace PackageFactory\Neos\CodeGenerator\Tests\Unit\Pattern\Domain\Value;

use PackageFactory\Neos\CodeGenerator\Domain\Input\Query;
use PackageFactory\Neos\CodeGenerator\Pattern\Domain\Value\ValueFactory;
use PackageFactory\Neos\CodeGenerator\Pattern\Domain\Value\ValueGenerator;
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

        $this->inject($this->valueFactory, 'distributionPackageResolver', $this->distributionPackageResolver);
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
        $query = Query::fromArrayAtSpecificPointInTime([
            'name' => 'Order/PostalAddress',
            'properties' => [
                'streetAddress' => 'string',
                'addressLocality' => 'string',
                'postalCode' => 'PostalCode',
            ]
        ], new \DateTimeImmutable('2006-03-24 22:22:00', new \DateTimeZone('Europe/Berlin')));

        $this->valueGenerator->generate($query);

        $this->assertFileWasWritten('Vendor.Default/Classes/Domain/Order/PostalAddress.php');
    }

    /**
     * @test
     * @return void
     */
    public function createsValueObjectInDefaultPackageWithDependenciesInDomain(): void
    {
        $query = Query::fromArrayAtSpecificPointInTime([
            'name' => 'Cinema/Movie',
            'properties' => [
                'actors' => 'Actor[]',
                'musicBy' => 'Music/MusicGroup'
            ]
        ], new \DateTimeImmutable('2006-03-24 22:22:00', new \DateTimeZone('Europe/Berlin')));

        $this->valueGenerator->generate($query);

        $this->assertFileWasWritten('Vendor.Default/Classes/Domain/Cinema/Movie.php');
    }

    /**
     * @test
     * @return void
     */
    public function createsValueObjectInDefaultPackageWithDependenciesInOtherPackage(): void
    {
        $query = Query::fromArrayAtSpecificPointInTime([
            'name' => 'Archive/Image/Photograph',
            'properties' => [
                'uri' => '/Vendor/Shared/Domain/Uri',
                'abstract' => 'string',
                'author' => '/Vendor/Shared/Domain/Person'
            ]
        ], new \DateTimeImmutable('2006-03-24 22:22:00', new \DateTimeZone('Europe/Berlin')));

        $this->valueGenerator->generate($query);

        $this->assertFileWasWritten('Vendor.Default/Classes/Domain/Archive/Image/Photograph.php');
    }
}
