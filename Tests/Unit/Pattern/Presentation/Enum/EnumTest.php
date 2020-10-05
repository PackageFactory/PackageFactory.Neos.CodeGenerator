<?php declare(strict_types=1);
namespace PackageFactory\Neos\CodeGenerator\Tests\Unit\Pattern\Presentation\Enum;

use PackageFactory\Neos\CodeGenerator\Domain\Code\Php\PhpClass\PhpClassName;
use PackageFactory\Neos\CodeGenerator\Domain\Input\Query;
use PackageFactory\Neos\CodeGenerator\Pattern\Presentation\Enum\Enum;
use PackageFactory\Neos\CodeGenerator\Pattern\Presentation\Enum\EnumFactory;
use PackageFactory\Neos\CodeGenerator\Pattern\Presentation\Enum\EnumGenerator;
use PackageFactory\Neos\CodeGenerator\Pattern\Presentation\Enum\EnumRepository;
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

    /**
     * @var EnumRepository
     */
    private $enumRepository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->enumRepository = new EnumRepository();
        $this->enumFactory = new EnumFactory();
        $this->enumGenerator = new EnumGenerator();

        $this->inject($this->enumFactory, 'distributionPackageResolver', $this->distributionPackageResolver);
        $this->inject($this->enumFactory, 'signatureFactory', $this->signatureFactory);

        $this->inject($this->enumGenerator, 'enumRepository', $this->enumRepository);
        $this->inject($this->enumGenerator, 'enumFactory', $this->enumFactory);
        $this->inject($this->enumGenerator, 'fileWriter', $this->fileWriter);
    }

    /**
     * @test
     * @return void
     */
    public function createsStringEnumInDefaultPackage(): void
    {
        $query = Query::fromArrayAtSpecificPointInTime([
            'name' => 'Button/ButtonType',
            'type' => 'string',
            'values' => ['link', 'button', 'submit']
        ], new \DateTimeImmutable('2006-03-24 22:22:00', new \DateTimeZone('Europe/Berlin')));

        $this->enumGenerator->generate($query);

        $this->assertFileWasWritten('Vendor.Default/Classes/Presentation/Button/ButtonType.php');
        $this->assertFileWasWritten('Vendor.Default/Classes/Presentation/Button/ButtonTypeIsInvalid.php');
        $this->assertFileWasWritten('Vendor.Default/Classes/Application/DataSource/ButtonTypeProvider.php');

        $enum = $this->enumRepository->findOneByPhpClassName(
            PhpClassName::fromString('\\Vendor\\Default\\Presentation\\Button\\ButtonType')
        );

        $this->assertNotNull($enum);
        $this->assertInstanceOf(Enum::class, $enum);
    }

    /**
     * @test
     * @return void
     */
    public function createsIntegerEnumInDefaultPackage(): void
    {
        $query = Query::fromArrayAtSpecificPointInTime([
            'name' => 'Alert/Severity',
            'type' => 'int',
            'values' => ['info', 'warning', 'error', 'fatal']
        ], new \DateTimeImmutable('2006-03-24 22:22:00', new \DateTimeZone('Europe/Berlin')));

        $this->enumGenerator->generate($query);

        $this->assertFileWasWritten('Vendor.Default/Classes/Presentation/Alert/Severity.php');
        $this->assertFileWasWritten('Vendor.Default/Classes/Presentation/Alert/SeverityIsInvalid.php');
        $this->assertFileWasWritten('Vendor.Default/Classes/Application/DataSource/SeverityProvider.php');

        $enum = $this->enumRepository->findOneByPhpClassName(
            PhpClassName::fromString('\\Vendor\\Default\\Presentation\\Alert\\Severity')
        );

        $this->assertNotNull($enum);
        $this->assertInstanceOf(Enum::class, $enum);
    }
}
