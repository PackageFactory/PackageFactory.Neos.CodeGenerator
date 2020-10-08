<?php declare(strict_types=1);
namespace PackageFactory\Neos\CodeGenerator\Tests\Util;

use PackageFactory\Neos\CodeGenerator\Domain\Files\FileInterface;
use PackageFactory\Neos\CodeGenerator\Domain\Files\FileWriterInterface;
use Spatie\Snapshots\MatchesSnapshots;

trait FileWriterTrait
{
    use MatchesSnapshots;

    /**
     * @var FileWriterInterface
     */
    protected $fileWriter;

    /**
     * @var null|string
     */
    protected $currentlyAssertedFilePath;

    /**
     * @before
     */
    public function setupFileWriterTrait(): void
    {
        $this->fileWriter = new class implements FileWriterInterface {
            /**
             * @var FileInterface[]
             */
            private $files = [];

            /**
             * @param FileInterface $file
             * @return void
             */
            public function write(FileInterface $file): void
            {
                $this->files[$file->getPath()->asString()] = $file;
            }

            /**
             * @param string $filePath
             * @return boolean
             */
            public function has(string $filePath): bool
            {
                return array_key_exists($filePath, $this->files);
            }

            /**
             * @param string $filePath
             * @return FileInterface
             */
            public function get(string $filePath): FileInterface
            {
                if (array_key_exists($filePath, $this->files)) {
                    return $this->files[$filePath];
                }

                throw new \LogicException($filePath . ' is not registered');
            }
        };

        $this->currentlyAssertedFilePath = null;
    }

    /**
     * @param string $filePath
     * @return void
     */
    protected function assertFileWasWritten(string $filePath): void
    {
        /** @var mixed $fileWriter */
        $fileWriter = $this->fileWriter;

        $this->currentlyAssertedFilePath = $filePath;
        $this->assertTrue($fileWriter->has($filePath), 'File "' . $filePath . '" was not written.');
        $this->assertMatchesSnapshot($fileWriter->get($filePath)->getContents());
    }

    /*
     * Overwrite snapshot directory for better structure
     */
    protected function getSnapshotDirectory(): string
    {
        return dirname((new \ReflectionClass($this))->getFileName())
            . DIRECTORY_SEPARATOR . '__snapshots__'
            . DIRECTORY_SEPARATOR . (new \ReflectionClass($this))->getShortName()
            . DIRECTORY_SEPARATOR . $this->getName();
    }

    /*
     * Overwrite snapshot ID to resemble the tested file
     */
    protected function getSnapshotId(): string
    {
        if ($this->currentlyAssertedFilePath) {
            $id = basename($this->currentlyAssertedFilePath);
            $this->currentlyAssertedFilePath = null;
        } else {
            $id = 'unnamed__' . $this->snapshotIncrementor;
        }

        return $id;
    }
}
