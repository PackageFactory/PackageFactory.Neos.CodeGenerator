<?php declare(strict_types=1);
namespace PackageFactory\Neos\CodeGenerator\Tests\Unit\Pattern\Eel;

use PackageFactory\Neos\CodeGenerator\Domain\Input\Query;
use PackageFactory\Neos\CodeGenerator\Pattern\Eel\HelperFactory;
use PackageFactory\Neos\CodeGenerator\Pattern\Eel\HelperGenerator;
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

    protected function setUp(): void
    {
        parent::setUp();

        $this->helperFactory = new HelperFactory();
        $this->helperGenerator = new HelperGenerator();

        $this->inject($this->helperFactory, 'distributionPackageResolver', $this->distributionPackageResolver);
        $this->inject($this->helperFactory, 'signatureFactory', $this->signatureFactory);

        $this->inject($this->helperGenerator, 'helperFactory', $this->helperFactory);
        $this->inject($this->helperGenerator, 'fileWriter', $this->fileWriter);
    }

    /**
     * @test
     * @return void
     */
    public function createsEelHelperInDefaultPackage(): void
    {
        $query = Query::fromArray(['name' => 'Essentials']);

        $this->helperGenerator->generate($query);

        $this->assertFileWasWritten('Vendor.Default/Classes/Application/Eel/EssentialsHelper.php');
        $this->assertFileWasWritten('Vendor.Default/Configuration/Settings.Eel.Helpers.yaml');
    }
}
