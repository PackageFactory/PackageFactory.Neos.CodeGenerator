<?php declare(strict_types=1);
namespace PackageFactory\Neos\CodeGenerator\Tests\Unit\Pattern\Presentation\Model;

use PackageFactory\Neos\CodeGenerator\Domain\Code\Php\PhpClass\PhpClassName;
use PackageFactory\Neos\CodeGenerator\Domain\Input\Query;
use PackageFactory\Neos\CodeGenerator\Pattern\Presentation\Model\Model;
use PackageFactory\Neos\CodeGenerator\Pattern\Presentation\Model\ModelFactory;
use PackageFactory\Neos\CodeGenerator\Pattern\Presentation\Model\ModelGenerator;
use PackageFactory\Neos\CodeGenerator\Pattern\Presentation\Model\ModelRepository;
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

    /**
     * @var ModelRepository
     */
    private $modelRepository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->modelRepository = new ModelRepository();
        $this->modelFactory = new ModelFactory();
        $this->modelGenerator = new ModelGenerator();

        $this->inject($this->modelFactory, 'distributionPackageResolver', $this->distributionPackageResolver);
        $this->inject($this->modelFactory, 'signatureFactory', $this->signatureFactory);
        $this->inject($this->modelFactory, 'propertyFactory', $this->propertyFactory);

        $this->inject($this->modelGenerator, 'modelRepository', $this->modelRepository);
        $this->inject($this->modelGenerator, 'modelFactory', $this->modelFactory);
        $this->inject($this->modelGenerator, 'fileWriter', $this->fileWriter);
    }

    /**
     * @test
     * @return void
     */
    public function createsValueObjectFactoryAndInterfaceInDefaultPackage(): void
    {
        $query = Query::fromArrayAtSpecificPointInTime([
            'name' => 'Button',
            'props' => [
                'type' => 'ButtonType',
                'look' => 'ButtonLook',
                'label' => 'string',
                'horizontalAlignment' => '?Alignment/HorizontalAlignment'
            ]
        ], new \DateTimeImmutable('2006-03-24 22:22:00', new \DateTimeZone('Europe/Berlin')));

        $this->modelGenerator->generate($query);

        $this->assertFileWasWritten('Vendor.Default/Classes/Presentation/Button/Button.php');
        $this->assertFileWasWritten('Vendor.Default/Classes/Presentation/Button/ButtonInterface.php');
        $this->assertFileWasWritten('Vendor.Default/Classes/Presentation/Button/ButtonFactory.php');

        $model = $this->modelRepository->findOneByPhpClassName(
            PhpClassName::fromString('\\Vendor\\Default\\Presentation\\Button\\Button')
        );

        $this->assertNotNull($model);
        $this->assertInstanceOf(Model::class, $model);
    }
}
