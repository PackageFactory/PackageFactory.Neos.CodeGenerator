<?php declare(strict_types=1);
namespace PackageFactory\Neos\CodeGenerator\Tests\Unit\Pattern\Presentation\Component;

use PackageFactory\Neos\CodeGenerator\Domain\Code\Php\PhpClass\PhpClassName;
use PackageFactory\Neos\CodeGenerator\Domain\Input\Query;
use PackageFactory\Neos\CodeGenerator\Pattern\Presentation\Component\Component;
use PackageFactory\Neos\CodeGenerator\Pattern\Presentation\Component\ComponentFactory;
use PackageFactory\Neos\CodeGenerator\Pattern\Presentation\Component\ComponentGenerator;
use PackageFactory\Neos\CodeGenerator\Pattern\Presentation\Component\ComponentRepository;
use PackageFactory\Neos\CodeGenerator\Pattern\Presentation\Model\ModelFactory;
use PackageFactory\Neos\CodeGenerator\Pattern\Presentation\Prop\PropTypeFactory;
use PackageFactory\Neos\CodeGenerator\Tests\Unit\Pattern\PatternTestCase;

final class ComponentTest extends PatternTestCase
{
    /**
     * @var ComponentGenerator
     */
    private $componentGenerator;

    /**
     * @var ComponentFactory
     */
    private $componentFactory;

    /**
     * @var ComponentRepository
     */
    private $componentRepository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->propTypeFactory = new PropTypeFactory();
        $this->modelFactory = new ModelFactory();

        $this->componentRepository = new ComponentRepository();
        $this->componentFactory = new ComponentFactory();
        $this->componentGenerator = new ComponentGenerator();

        $this->inject($this->propTypeFactory, 'shorthands', $this->shorthands);

        $this->inject($this->modelFactory, 'distributionPackageResolver', $this->distributionPackageResolver);
        $this->inject($this->modelFactory, 'signatureFactory', $this->signatureFactory);
        $this->inject($this->modelFactory, 'propertyFactory', $this->propertyFactory);

        $this->inject($this->componentFactory, 'distributionPackageResolver', $this->distributionPackageResolver);
        $this->inject($this->componentFactory, 'signatureFactory', $this->signatureFactory);
        $this->inject($this->componentFactory, 'modelFactory', $this->modelFactory);

        $this->inject($this->componentGenerator, 'propTypeFactory', $this->propTypeFactory);
        $this->inject($this->componentGenerator, 'componentRepository', $this->componentRepository);
        $this->inject($this->componentGenerator, 'componentFactory', $this->componentFactory);
        $this->inject($this->componentGenerator, 'fileWriter', $this->fileWriter);
    }

    /**
     * @test
     * @return void
     */
    public function createsValueObjectFactoryAndInterfaceInDefaultPackage(): void
    {
        $query = Query::fromArrayAtSpecificPointInTime([
            'name' => 'Image',
            'props' => [
                'src' => 'imagesource',
                'alt' => 'string',
                'title' => 'string',
            ]
        ], new \DateTimeImmutable('2006-03-24 22:22:00', new \DateTimeZone('Europe/Berlin')));

        $this->componentGenerator->generate($query);

        $this->assertFileWasWritten('Vendor.Default/Classes/Presentation/Image/Image.php');
        $this->assertFileWasWritten('Vendor.Default/Classes/Presentation/Image/ImageInterface.php');
        $this->assertFileWasWritten('Vendor.Default/Classes/Presentation/Image/ImageFactory.php');
        $this->assertFileWasWritten('Vendor.Default/Resources/Private/Fusion/Presentation/Component/Image/Image.fusion');

        $component = $this->componentRepository->findOneByPhpClassName(
            PhpClassName::fromString('\\Vendor\\Default\\Presentation\\Image\\Image')
        );

        $this->assertNotNull($component);
        $this->assertInstanceOf(Component::class, $component);
    }
}
