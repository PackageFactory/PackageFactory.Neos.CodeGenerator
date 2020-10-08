<?php declare(strict_types=1);
namespace PackageFactory\Neos\CodeGenerator\Tests\Unit\Pattern;

use Neos\Flow\Tests\UnitTestCase;
use PackageFactory\Neos\CodeGenerator\Domain\Code\Common\Signature\SignatureFactoryInterface;
use PackageFactory\Neos\CodeGenerator\Domain\Code\Php\PhpClass\PhpClassName;
use PackageFactory\Neos\CodeGenerator\Domain\Code\Php\Property\PropertyFactory;
use PackageFactory\Neos\CodeGenerator\Domain\Code\Php\Type\TypeFactory;
use PackageFactory\Neos\CodeGenerator\Domain\Files\Path;
use PackageFactory\Neos\CodeGenerator\Domain\Flow\DistributionPackageInterface;
use PackageFactory\Neos\CodeGenerator\Domain\Flow\DistributionPackageResolverInterface;
use PackageFactory\Neos\CodeGenerator\Domain\Flow\PackageKey;
use PackageFactory\Neos\CodeGenerator\Infrastructure\SignatureFactory;
use PackageFactory\Neos\CodeGenerator\Tests\Util\FileWriterTrait;
use Symfony\Component\Yaml;

abstract class PatternTestCase extends UnitTestCase
{
    use FileWriterTrait;

    /**
     * @phpstan-var array<string, array{type: string, example: array{presentation: array{styleguide: string, afx: string}}}>
     * @var array
     */
    protected $shorthands;

    /**
     * @var DistributionPackageResolverInterface
     */
    protected $distributionPackageResolver;

    /**
     * @var SignatureFactoryInterface
     */
    protected $signatureFactory;

    /**
     * @var TypeFactory
     */
    protected $typeFactory;

    /**
     * @var PropertyFactory
     */
    protected $propertyFactory;

    /**
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        $parser = new Yaml\Parser();
        $settings = $parser->parseFile($_SERVER['FLOW_ROOTPATH'] . 'Configuration/Settings.Shorthands.yaml');
        $this->shorthands = $settings['PackageFactory']['Neos']['CodeGenerator']['shorthands'] ?? [];

        $this->distributionPackageResolver = new class implements DistributionPackageResolverInterface {
            public function resolve(?string $input): DistributionPackageInterface
            {
                return new class($input) implements DistributionPackageInterface {
                    /** @var PackageKey */
                    private $packageKey;

                    public function __construct(?string $input)
                    {
                        $this->packageKey = PackageKey::fromString($input ?? 'Vendor.Default');
                    }

                    public function getPackageKey(): PackageKey
                    {
                        return $this->packageKey;
                    }

                    public function getPackagePath(): Path
                    {
                        return Path::fromString($this->packageKey->asString());
                    }

                    public function getPhpFilePathForClassName(PhpClassName $className): Path
                    {
                        return $this->getPackagePath()
                            ->appendString('Classes')
                            ->append(
                                $className
                                    ->asNamespace()
                                    ->truncateAscendant($this->packageKey->asPhpNamespace())
                                    ->asPath()
                            )
                            ->withExtension('php');
                    }
                };
            }
        };

        $this->signatureFactory = new SignatureFactory();

        $this->typeFactory = new TypeFactory();
        $this->inject($this->typeFactory, 'shorthands', $this->shorthands);

        $this->propertyFactory = new PropertyFactory();
        $this->inject($this->propertyFactory, 'typeFactory', $this->typeFactory);
    }
}
