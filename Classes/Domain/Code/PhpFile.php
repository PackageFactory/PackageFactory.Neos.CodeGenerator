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
final class PhpFile implements FileInterface
{
    private const DEFAULT_HEAD = '<?php declare(strict_types=1);';

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
     * @param PhpNamespace $namespace
     * @param string $name
     * @return self
     */
    public static function fromFlowPackageAndNamespace(FlowPackageInterface $flowPackage, PhpNamespace $namespace, string $name): self
    {
        $path = Path::fromFlowPackage($flowPackage)
            ->appendString('Classes')
            ->append($namespace->asPath())
            ->appendString($name . '.php');
        $fullyQualifiedNamespace = $namespace->belongsToFlowPackage($flowPackage)
            ? $namespace
            : PhpNamespace::fromFlowPackage($flowPackage)->append($namespace);

        return new self(
            $path,
            join(PHP_EOL, [
                '<?php declare(strict_types=1);',
                'namespace ' . $fullyQualifiedNamespace->getValue() . ';',
                '',
                '/*',
                ' * This file is part of the ' . $flowPackage->getPackageKey() . ' package',
                ' */'
            ]),
            ''
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

    /**
     * @param PhpNamespace $namespace
     * @return self
     */
    public function inNamespace(PhpNamespace $namespace): self
    {
        $next = clone $this;
        $next->head = self::DEFAULT_HEAD . PHP_EOL . 'namespace ' . $namespace->getValue() . ';';

        return $next;
    }

    /**
     * @param string $packageKey
     * @return self
     */
    public function inPackage(string $packageKey): self
    {
        $next = clone $this;
        $next->comment = join(PHP_EOL, [
            '/*',
            ' * This file is part of the ' . $packageKey . ' package',
            ' */'
        ]);

        return $next;
    }

    /**
     * @param string $body
     * @return self
     */
    public function withBody(string $body): self
    {
        $next = clone $this;
        $next->body = $body;

        return $next;
    }
}
