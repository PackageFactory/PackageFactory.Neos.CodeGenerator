<?php declare(strict_types=1);
namespace PackageFactory\Neos\CodeGenerator\Domain\Code\Common\Signature;

/*
 * This file is part of the PackageFactory.Neos.CodeGenerator package
 */

final class Signature implements SignatureInterface
{
    /**
     * @var string
     */
    private $packageName;

    /**
     * @param string $packageName
     */
    public function __construct(string $packageName)
    {
        $this->packageName = $packageName;
    }

    public function asPhpCode(): string
    {
        return join(PHP_EOL, [
            '/*',
            ' * This file is part of the ' . $this->packageName . ' package',
            ' */'
        ]);
    }
}
