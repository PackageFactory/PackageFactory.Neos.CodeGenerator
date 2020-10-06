<?php declare(strict_types=1);
namespace PackageFactory\Neos\CodeGenerator\Tests\Unit\Pattern\Presentation\Component;

use PackageFactory\Neos\CodeGenerator\Domain\Code\Php\PhpClass\PhpClassName;
use PackageFactory\Neos\CodeGenerator\Domain\Input\Query;
use PackageFactory\Neos\CodeGenerator\Pattern\Presentation\Component\Component;
use PackageFactory\Neos\CodeGenerator\Pattern\Presentation\Component\ComponentFactory;
use PackageFactory\Neos\CodeGenerator\Pattern\Presentation\Component\ComponentGenerator;
use PackageFactory\Neos\CodeGenerator\Pattern\Presentation\Component\ComponentRepository;
use PackageFactory\Neos\CodeGenerator\Pattern\Presentation\Enum\EnumRepository;
use PackageFactory\Neos\CodeGenerator\Pattern\Presentation\Model\ModelFactory;
use PackageFactory\Neos\CodeGenerator\Pattern\Presentation\Model\ModelRepository;
use PackageFactory\Neos\CodeGenerator\Pattern\Presentation\Prop\PropTypeFactory;
use PackageFactory\Neos\CodeGenerator\Pattern\Presentation\Value\ValueRepository;
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

    /**
     * @var ModelRepository
     */
    private $modelRepository;

    /**
     * @var ValueRepository
     */
    private $valueRepository;

    /**
     * @var EnumRepository
     */
    private $enumRepository;

    /**
     * @var PropTypeFactory
     */
    private $propTypeFactory;

    /**
     * @var ModelFactory
     */
    private $modelFactory;

    protected function setUp(): void
    {
        parent::setUp();

        $this->componentRepository = new ComponentRepository();
        $this->modelRepository = new ModelRepository();
        $this->valueRepository = new ValueRepository();
        $this->enumRepository = new EnumRepository();

        $this->propTypeFactory = new PropTypeFactory();
        $this->modelFactory = new ModelFactory();

        $this->componentFactory = new ComponentFactory();
        $this->componentGenerator = new ComponentGenerator();

        $this->inject($this->propTypeFactory, 'shorthands', $this->shorthands);
        $this->inject($this->propTypeFactory, 'componentRepository', $this->componentRepository);
        $this->inject($this->propTypeFactory, 'modelRepository', $this->modelRepository);
        $this->inject($this->propTypeFactory, 'valueRepository', $this->valueRepository);
        $this->inject($this->propTypeFactory, 'enumRepository', $this->enumRepository);

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
                'title' => '?string',
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

    /**
     * @test
     * @return void
     */
    public function resolvesStyleguideExamplesRecursively(): void
    {
        $now = new \DateTimeImmutable('2006-03-24 22:22:00', new \DateTimeZone('Europe/Berlin'));

        $this->componentGenerator->generate(
            Query::fromArrayAtSpecificPointInTime([
                'name' => 'Element/Image',
                'props' => [
                    'src' => 'string',
                    'alt' => 'string',
                    'title' => 'string',
                ]
            ], $now)
        );

        $this->componentGenerator->generate(
            Query::fromArrayAtSpecificPointInTime([
                'name' => 'Element/Text',
                'props' => [
                    'content' => 'string',
                ]
            ], $now)
        );

        $this->componentGenerator->generate(
            Query::fromArrayAtSpecificPointInTime([
                'name' => 'Element/Link',
                'props' => [
                    'href' => 'string',
                ]
            ], $now)
        );

        $query = Query::fromArrayAtSpecificPointInTime([
            'name' => 'Block/Card',
            'props' => [
                'image' => '?Element/Image',
                'text' => 'Element/Text',
                'link' => 'Element/Link',
            ]
        ], $now);

        $this->componentGenerator->generate($query);

        $this->assertFileWasWritten('Vendor.Default/Classes/Presentation/Block/Card/Card.php');
        $this->assertFileWasWritten('Vendor.Default/Classes/Presentation/Block/Card/CardInterface.php');
        $this->assertFileWasWritten('Vendor.Default/Classes/Presentation/Block/Card/CardFactory.php');
        $this->assertFileWasWritten('Vendor.Default/Resources/Private/Fusion/Presentation/Component/Block/Card/Card.fusion');
    }
}
