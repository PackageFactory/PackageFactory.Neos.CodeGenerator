<?php declare(strict_types=1);
namespace PackageFactory\Neos\CodeGenerator\Domain\Code\Php;

/*
 * This file is part of the PackageFactory.Neos.CodeGenerator package
 */

use Neos\Flow\Annotations as Flow;
use PackageFactory\Neos\CodeGenerator\Domain\Code\Common\Signature\SignatureInterface;
use PackageFactory\Neos\CodeGenerator\Domain\Code\Php\PhpNamespace\PhpNamespace;
use PackageFactory\Neos\CodeGenerator\Domain\Code\Php\Import\ImportCollectionInterface;
use PackageFactory\Neos\CodeGenerator\Domain\Code\Php\Import\ImportInterface;
use PackageFactory\Neos\CodeGenerator\Domain\Files\FileInterface;
use PackageFactory\Neos\CodeGenerator\Domain\Files\Path;

/**
 * @Flow\Proxy(false)
 */
final class PhpFile implements FileInterface
{
    /**
     * @var Path
     */
    private $path;

    /**
     * @var null|PhpNamespace
     */
    private $namespace;

    /**
     * @var null|SignatureInterface
     */
    private $signature;

    /**
     * @var ImportCollectionInterface
     */
    private $imports;

    /**
     * @var null|string
     */
    private $code;

    /**
     * @param Path $path
     * @param null|PhpNamespace $namespace
     * @param null|SignatureInterface $signature
     * @param ImportCollectionInterface $imports
     * @param null|string $code
     */
    public function __construct(
        Path $path,
        ?PhpNamespace $namespace,
        ?SignatureInterface $signature,
        ImportCollectionInterface $imports,
        ?string $code
    ) {
        $this->path = $path;
        $this->namespace = $namespace;
        $this->signature = $signature;
        $this->imports = $imports;
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

        $result[] = '<?php declare(strict_types=1);';

        if ($this->namespace) {
            $result[] = 'namespace ' . $this->namespace->asString() . ';';
        }

        if ($this->signature) {
            $result[] = '';
            $result[] = $this->signature->asPhpCode();
        }

        if (count($this->imports) > 0) {
            $result[] = '';

            /** @var ImportInterface $import */
            foreach ($this->imports as $import) {
                $result[] = $import->asPhpUseStatement();
            }
        }

        if ($this->code) {
            $result[] = '';
            $result[] = trim($this->code);
        }

        $result[] = '';

        return join(PHP_EOL, $result);
    }
}
