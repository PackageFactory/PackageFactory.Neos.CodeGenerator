<?php declare(strict_types=1);
namespace PackageFactory\Neos\CodeGenerator\Domain\Code;

/*
 * This file is part of the PackageFactory.Neos.CodeGenerator package
 */

use Neos\Flow\Annotations as Flow;
use Neos\Flow\Package\FlowPackageInterface;
use PackageFactory\Neos\CodeGenerator\Domain\Files\FileInterface;
use PackageFactory\Neos\CodeGenerator\Domain\Files\Path;
use Symfony\Component\Yaml;

/**
 * @Flow\Proxy(false)
 */
final class YamlFile implements FileInterface
{
    /**
     * @var Path
     */
    private $path;

    /**
     * @var array<mixed>
     */
    private $data;

    /**
     * @param Path $path
     * @param array<mixed> $data
     * @return void
     */
    private function __construct(Path $path, array $data)
    {
        $this->path = $path;
        $this->data = $data;
    }

    /**
     * @param FlowPackageInterface $flowPackage
     * @param string $configurationFilePath
     * @return self
     */
    public static function fromConfigurationInFlowPackage(FlowPackageInterface $flowPackage, string $configurationFilePath): self
    {
        $path = Path::fromFlowPackage($flowPackage)
            ->appendString(FlowPackageInterface::DIRECTORY_CONFIGURATION)
            ->appendString($configurationFilePath);

        if (file_exists($path->getValue())) {
            $parser = new Yaml\Parser();
            return new self($path, $parser->parseFile($path->getValue()) ?? []);
        } else {
            return new self($path, []);
        }
    }

    /**
     * @return Path
     */
    public function getPath(): Path
    {
        return $this->path;
    }

    /**
     * @return string
     */
    public function getContents(): string
    {
        $dumper = new Yaml\Dumper(2);
        return $dumper->dump($this->data, 999);
    }

    /**
     * @return array<mixed>
     */
    public function getData(): array
    {
        return $this->data;
    }

    /**
     * @param array<mixed> $data
     * @return self
     */
    public function withData(array $data): self
    {
        return new self($this->path, $data);
    }
}
