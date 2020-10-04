<?php declare(strict_types=1);
namespace PackageFactory\Neos\CodeGenerator\Domain\Code\Fusion;

/*
 * This file is part of the PackageFactory.Neos.CodeGenerator package
 */

use Neos\Flow\Annotations as Flow;
use PackageFactory\Neos\CodeGenerator\Domain\Code\Common\Signature\SignatureInterface;
use PackageFactory\Neos\CodeGenerator\Domain\Files\Path;

/**
 * @Flow\Proxy(false)
 */
final class FusionFileBuilder
{
    /**
     * @var null|Path
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
     * @return self
     */
    public function setPath(Path $path): self
    {
        $this->path = $path;
        return $this;
    }

    /**
     * @param SignatureInterface $signature
     * @return self
     */
    public function setSignature(SignatureInterface $signature): self
    {
        $this->signature = $signature;
        return $this;
    }

    /**
     * @param string $code
     * @return self
     */
    public function setCode(string $code): self
    {
        $this->code = $code;
        return $this;
    }

    /**
     * @return FusionFile
     */
    public function build(): FusionFile
    {
        if (!$this->path) {
            throw new \LogicException('Could not build FusionFile, because of missing path.');
        }

        return new FusionFile($this->path, $this->signature, $this->code);
    }
}
