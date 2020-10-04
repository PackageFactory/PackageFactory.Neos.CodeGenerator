<?php declare(strict_types=1);
namespace PackageFactory\Neos\CodeGenerator\Tests\Unit\Pattern;

use Neos\Flow\Tests\UnitTestCase;
use PackageFactory\Neos\CodeGenerator\Domain\Code\Common\Signature\SignatureFactoryInterface;
use PackageFactory\Neos\CodeGenerator\Domain\Code\Php\PhpClass\PhpClassInterface;
use PackageFactory\Neos\CodeGenerator\Domain\Code\Php\PhpClass\PhpClassName;
use PackageFactory\Neos\CodeGenerator\Domain\Code\Php\PhpClass\PhpClassRepositoryInterface;
use PackageFactory\Neos\CodeGenerator\Domain\Code\Php\Property\PropertyFactory;
use PackageFactory\Neos\CodeGenerator\Domain\Code\Php\Type\TypeFactory;
use PackageFactory\Neos\CodeGenerator\Domain\Files\FileInterface;
use PackageFactory\Neos\CodeGenerator\Domain\Files\FileWriterInterface;
use PackageFactory\Neos\CodeGenerator\Domain\Files\Path;
use PackageFactory\Neos\CodeGenerator\Domain\Flow\DistributionPackageInterface;
use PackageFactory\Neos\CodeGenerator\Domain\Flow\DistributionPackageResolverInterface;
use PackageFactory\Neos\CodeGenerator\Domain\Flow\PackageKey;
use PackageFactory\Neos\CodeGenerator\Infrastructure\SignatureFactory;
use Spatie\Snapshots\MatchesSnapshots;

abstract class PatternTestCase extends UnitTestCase
{
    use MatchesSnapshots;

    /**
     * @var FileWriterInterface
     */
    protected $fileWriter;

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
     * @var PhpClassRepositoryInterface
     */
    protected $phpClassRepository;

    /**
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->fileWriter = new class implements FileWriterInterface {
            /** @var FileInterface[] */
            private $files = [];

            public function write(FileInterface $file): void
            {
                $this->files[$file->getPath()->asString()] = $file;
            }

            public function has(string $filePath): bool
            {
                return array_key_exists($filePath, $this->files);
            }

            public function get(string $filePath): FileInterface
            {
                if (array_key_exists($filePath, $this->files)) {
                    return $this->files[$filePath];
                }

                throw new \LogicException($filePath . ' is not registered');
            }
        };

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
        $this->propertyFactory = new PropertyFactory();
        $this->inject($this->propertyFactory, 'typeFactory', $this->typeFactory);

        $this->phpClassRepository = new class implements PhpClassRepositoryInterface {
            /** @var PhpClassInterface[] */
            private $classes = [];

            /**
             * @param PhpClassName $className
             * @return null|PhpClassInterface
             */
            public function findOneByClassName(PhpClassName $className): ?PhpClassInterface
            {
                if (isset($this->classes[$className->asFullyQualifiedNameString()])) {
                    return $this->classes[$className->asFullyQualifiedNameString()];
                }

                return null;
            }

            /**
             * @param PhpClassInterface $phpClass
             * @return void
             */
            public function add(PhpClassInterface $phpClass): void
            {
                $this->classes[$phpClass->getClassName()->asFullyQualifiedNameString()] = $phpClass;
            }

            /**
             * @param string $className
             * @return boolean
             */
            public function has(string $className): bool
            {
                return isset($this->classes[$className]);
            }
        };
    }

    /**
     * @param string $filePath
     * @return void
     */
    protected function assertFileWasWritten(string $filePath): void
    {
        /** @var mixed $fileWriter */
        $fileWriter = $this->fileWriter;

        $this->assertTrue($fileWriter->has($filePath), 'File "' . $filePath . '" was not written.');
        $this->assertMatchesSnapshot($fileWriter->get($filePath)->getContents());
    }

    /**
     * @param string $className
     * @return void
     */
    protected function assertPhpClassWasRegistered(string $className): void
    {
        /** @var mixed $phpClassRepository */
        $phpClassRepository = $this->phpClassRepository;

        $this->assertTrue($phpClassRepository->has($className), 'Class "' . $className . '" was not registered.');
    }
}
