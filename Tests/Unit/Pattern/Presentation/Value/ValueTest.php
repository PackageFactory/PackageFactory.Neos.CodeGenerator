<?php declare(strict_types=1);
namespace PackageFactory\Neos\CodeGenerator\Tests\Unit\Pattern\Presentation\Value;

use PackageFactory\Neos\CodeGenerator\Domain\Code\Php\PhpClass\PhpClassName;
use PackageFactory\Neos\CodeGenerator\Domain\Input\Query;
use PackageFactory\Neos\CodeGenerator\Pattern\Presentation\Value\Value;
use PackageFactory\Neos\CodeGenerator\Pattern\Presentation\Value\ValueFactory;
use PackageFactory\Neos\CodeGenerator\Pattern\Presentation\Value\ValueGenerator;
use PackageFactory\Neos\CodeGenerator\Pattern\Presentation\Value\ValueRepository;
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

    /**
     * @var ValueRepository
     */
    private $valueRepository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->valueRepository = new ValueRepository();
        $this->valueFactory = new ValueFactory();
        $this->valueGenerator = new ValueGenerator();

        $this->inject($this->valueFactory, 'distributionPackageResolver', $this->distributionPackageResolver);
        $this->inject($this->valueFactory, 'signatureFactory', $this->signatureFactory);
        $this->inject($this->valueFactory, 'propertyFactory', $this->propertyFactory);

        $this->inject($this->valueGenerator, 'valueRepository', $this->valueRepository);
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
            'name' => 'Alignment/Alignment',
            'props' => [
                'horizontal' => 'HorizontalAlignment',
                'vertical' => 'VerticalAlignment'
            ]
        ], new \DateTimeImmutable('2006-03-24 22:22:00', new \DateTimeZone('Europe/Berlin')));

        $this->valueGenerator->generate($query);

        $this->assertFileWasWritten('Vendor.Default/Classes/Presentation/Alignment/Alignment.php');

        $value = $this->valueRepository->findOneByPhpClassName(
            PhpClassName::fromString('\\Vendor\\Default\\Presentation\\Alignment\\Alignment')
        );

        $this->assertNotNull($value);
        $this->assertInstanceOf(Value::class, $value);
    }
}
