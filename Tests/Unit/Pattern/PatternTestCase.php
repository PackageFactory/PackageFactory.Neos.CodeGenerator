<?php declare(strict_types=1);
namespace PackageFactory\Neos\CodeGenerator\Tests\Unit\Pattern;

use Neos\Flow\Package\FlowPackageInterface;
use Neos\Flow\Tests\UnitTestCase;
use PackageFactory\Neos\CodeGenerator\Domain\Code\Common\Signature\SignatureFactoryInterface;
use PackageFactory\Neos\CodeGenerator\Domain\Files\FileInterface;
use PackageFactory\Neos\CodeGenerator\Domain\Files\FileWriterInterface;
use PackageFactory\Neos\CodeGenerator\Domain\Flow\PackageResolverInterface;
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
     * @var PackageResolverInterface
     */
    protected $packageResolver;

    /**
     * @var SignatureFactoryInterface
     */
    protected $signatureFactory;

    protected function setUp(): void
    {
        parent::setUp();
        $this->fileWriter = new class implements FileWriterInterface {
            /** @var FileInterface[] */
            private $files = [];

            public function write(FileInterface $file): void
            {
                $this->files[$file->getPath()->getValue()] = $file;
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

                throw new \LogicException($filePath . ' is not registered!');
            }
        };

        $this->packageResolver = new class implements PackageResolverInterface {
            public function resolve(?string $input): FlowPackageInterface
            {
                return new class($input) implements FlowPackageInterface {
                    /** @var null|string */
                    private $input;

                    public function __construct(?string $input) { $this->input = $input; }
                    public function getClassFiles() { throw new \BadMethodCallException('Not implemented.'); }
                    public function getComposerName() { throw new \BadMethodCallException('Not implemented.'); }
                    public function getNamespaces() { throw new \BadMethodCallException('Not implemented.'); }
                    public function getPackagePath() { return $this->input ?? 'Vendor.Default'; }
                    public function getInstalledVersion() { throw new \BadMethodCallException('Not implemented.'); }
                    public function getComposerManifest($key = null) { return json_decode(file_get_contents(__DIR__ . '/Fixtures/sample-composer.json'), true); }
                    public function getPackageKey() { return $this->input ?? 'Vendor.Default'; }
                    public function getResourcesPath() { throw new \BadMethodCallException('Not implemented.'); }
                    public function getConfigurationPath() { throw new \BadMethodCallException('Not implemented.'); }
                    public function getFunctionalTestsClassFiles() { throw new \BadMethodCallException('Not implemented.'); }
                    public function getFunctionalTestsPath() { throw new \BadMethodCallException('Not implemented.'); }
                };
            }
        };

        $this->signatureFactory = new SignatureFactory();
    }

    protected function assertFileWasWritten(string $filePath): void
    {
        /** @var mixed $fileWriter */
        $fileWriter = $this->fileWriter;

        $this->assertTrue($fileWriter->has($filePath), 'File "' . $filePath . '" was not written.');
        $this->assertMatchesSnapshot($fileWriter->get($filePath)->getContents());
    }
}
