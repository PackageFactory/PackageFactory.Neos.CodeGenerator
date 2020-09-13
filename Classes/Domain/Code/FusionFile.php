<?php declare(strict_types=1);
namespace PackageFactory\Neos\CodeGenerator\Domain\Code;

/*
 * This file is part of the PackageFactory.Neos.CodeGenerator package
 */

use Neos\Flow\Annotations as Flow;
use Neos\Flow\Package\FlowPackageInterface;
use PackageFactory\Neos\CodeGenerator\Domain\Files\FileInterface;
use PackageFactory\Neos\CodeGenerator\Domain\Files\Path;

/**
 * @Flow\Proxy(false)
 */
final class FusionFile implements FileInterface
{
    /**
     * @var Path
     */
    private $path;

    /**
     * @var string
     */
    private $head;

    /**
     * @var string
     */
    private $body;

    /**
     * @param Path $path
     * @param string $head
     * @param string $body
     */
    private function __construct(
        Path $path,
        string $head,
        string $body
    ) {
        $this->path = $path;
        $this->head = $head;
        $this->body = $body;
    }

    /**
     * @param FlowPackageInterface $flowPackage
     * @param string $location
     * @param string $name
     * @param string $body
     * @return self
     */
    public static function fromFlowPackage(FlowPackageInterface $flowPackage, string $location, string $name, string $body): self
    {
        $path = Path::fromFlowPackage($flowPackage)
            ->appendString('Resources/Private/Fusion')
            ->appendString($location)
            ->appendString($name . '.fusion');

        return new self(
            $path,
            join(PHP_EOL, [
                '/*',
                ' * This file is part of the ' . $flowPackage->getPackageKey() . ' package',
                ' */'
            ]),
            $body
        );
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
        return join(PHP_EOL . PHP_EOL, [$this->head, $this->body]) . PHP_EOL;
    }
}
