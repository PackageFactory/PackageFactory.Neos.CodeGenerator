<?php declare(strict_types=1);
namespace PackageFactory\Neos\CodeGenerator\Tests\Unit\Pattern\Eel;

use PackageFactory\Neos\CodeGenerator\Domain\Code\Php\PhpClass\PhpClassName;
use PackageFactory\Neos\CodeGenerator\Domain\Input\Query;
use PackageFactory\Neos\CodeGenerator\Pattern\Eel\Helper;
use PackageFactory\Neos\CodeGenerator\Pattern\Eel\HelperFactory;
use PackageFactory\Neos\CodeGenerator\Pattern\Eel\HelperGenerator;
use PackageFactory\Neos\CodeGenerator\Pattern\Eel\HelperRepository;
use PackageFactory\Neos\CodeGenerator\Tests\Unit\Pattern\PatternTestCase;

final class HelperTest extends PatternTestCase
{
    /**
     * @var HelperGenerator
     */
    private $helperGenerator;

    /**
     * @var HelperFactory
     */
    private $helperFactory;

    /**
     * @Flow\Inject
     * @var HelperRepository
     */
    protected $helperRepository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->helperRepository = new HelperRepository();
        $this->helperFactory = new HelperFactory();
        $this->helperGenerator = new HelperGenerator();

        $this->inject($this->helperFactory, 'distributionPackageResolver', $this->distributionPackageResolver);
        $this->inject($this->helperFactory, 'signatureFactory', $this->signatureFactory);

        $this->inject($this->helperGenerator, 'helperRepository', $this->helperRepository);
        $this->inject($this->helperGenerator, 'helperFactory', $this->helperFactory);
        $this->inject($this->helperGenerator, 'fileWriter', $this->fileWriter);
    }

    /**
     * @test
     * @return void
     */
    public function createsEelHelperInDefaultPackage(): void
    {
        $query = Query::fromArrayAtSpecificPointInTime([
            'name' => 'Essentials'
        ], new \DateTimeImmutable('2006-03-24 22:22:00', new \DateTimeZone('Europe/Berlin')));

        $this->helperGenerator->generate($query);

        $this->assertFileWasWritten('Vendor.Default/Classes/Application/Eel/EssentialsHelper.php');
        $this->assertFileWasWritten('Vendor.Default/Configuration/Settings.Eel.Helpers.yaml');

        $helper = $this->helperRepository->findOneByPhpClassName(
            PhpClassName::fromString('\\Vendor\\Default\\Application\\Eel\\EssentialsHelper')
        );

        $this->assertNotNull($helper);
        $this->assertInstanceOf(Helper::class, $helper);
    }
}
