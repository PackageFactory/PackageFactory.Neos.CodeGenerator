<?php declare(strict_types=1);
namespace PackageFactory\Neos\CodeGenerator\Tests\Unit\Pattern\Presentation\Model;

use PackageFactory\Neos\CodeGenerator\Domain\Input\Query;
use PackageFactory\Neos\CodeGenerator\Pattern\Presentation\Model\ModelFactory;
use PackageFactory\Neos\CodeGenerator\Pattern\Presentation\Model\ModelGenerator;
use PackageFactory\Neos\CodeGenerator\Tests\Unit\Pattern\PatternTestCase;

final class ModelTest extends PatternTestCase
{
    /**
     * @var ModelGenerator
     */
    private $modelGenerator;

    /**
     * @var ModelFactory
     */
    private $modelFactory;

    protected function setUp(): void
    {
        parent::setUp();

        $this->modelFactory = new ModelFactory();
        $this->modelGenerator = new ModelGenerator();

        $this->inject($this->modelFactory, 'distributionPackageResolver', $this->distributionPackageResolver);
        $this->inject($this->modelFactory, 'signatureFactory', $this->signatureFactory);
        $this->inject($this->modelFactory, 'propertyFactory', $this->propertyFactory);

        $this->inject($this->modelGenerator, 'modelFactory', $this->modelFactory);
        $this->inject($this->modelGenerator, 'fileWriter', $this->fileWriter);
    }

    /**
     * @test
     * @return void
     */
    public function createsValueObjectFactoryAndInterfaceInDefaultPackage(): void
    {
        $query = Query::fromArray([
            'name' => 'Button/Button',
            'props' => [
                'type' => 'ButtonType',
                'look' => 'ButtonLook',
                'label' => 'string',
                'horizontalAlignment' => '?Alignment/HorizontalAlignment'
            ]
        ]);

        $this->modelGenerator->generate($query);

        $this->assertFileWasWritten('Vendor.Default/Classes/Presentation/Button/Button.php');
        $this->assertFileWasWritten('Vendor.Default/Classes/Presentation/Button/ButtonInterface.php');
        $this->assertFileWasWritten('Vendor.Default/Classes/Presentation/Button/ButtonFactory.php');
    }
}
