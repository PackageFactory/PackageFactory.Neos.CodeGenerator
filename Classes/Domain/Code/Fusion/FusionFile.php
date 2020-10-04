<?php declare(strict_types=1);
namespace PackageFactory\Neos\CodeGenerator\Domain\Code\Fusion;

/*
 * This file is part of the PackageFactory.Neos.CodeGenerator package
 */

use Neos\Flow\Annotations as Flow;
use PackageFactory\Neos\CodeGenerator\Domain\Code\Common\Signature\SignatureInterface;
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
     * @var null|SignatureInterface
     */
    private $signature;

    /**
     * @var null|string
     */
    private $code;

    /**
     * @param Path $path
     * @param null|SignatureInterface $signature
     * @param null|string $code
     */
    public function __construct(
        Path $path,
        ?SignatureInterface $signature,
        ?string $code
    ) {
        $this->path = $path;
        $this->signature = $signature;
        $this->code = $code;
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
        $result = [];

        if ($this->code) {
            $result[] = trim($this->code);
        }

        $result[] = '';

        return join(PHP_EOL, $result);
    }
}
