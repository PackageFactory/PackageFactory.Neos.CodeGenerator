<?php declare(strict_types=1);
namespace PackageFactory\Neos\CodeGenerator\Domain\Code\Fusion\Prototype;

/*
 * This file is part of the PackageFactory.Neos.CodeGenerator package
 */

use Neos\Flow\Annotations as Flow;
use Neos\Utility\Files;
use Neos\Fusion\Core\Parser as FusionParser;
use PackageFactory\Neos\CodeGenerator\Domain\Code\Fusion\Identifier\PrototypeName;

/**
 * @Flow\Scope("singleton")
 */
final class PrototypeRepository implements PrototypeRepositoryInterface
{
    const ROOT_FUSION_PATTERN = 'resource://%s/Private/Fusion/Root.fusion';

    /**
     * @Flow\Inject
     * @var FusionParser
     */
    protected $fusionParser;

    /**
     * @param PrototypeName $prototypeName
     * @return null|PrototypeInterface
     */
    public function findOneByPrototypeName(PrototypeName $prototypeName): ?PrototypeInterface
    {
        $pathToRootFusion = sprintf(self::ROOT_FUSION_PATTERN, $prototypeName->getPackageKey());

        if (is_file($pathToRootFusion) && is_readable($pathToRootFusion)) {
            $rootFusionContents = Files::getFileContents($pathToRootFusion);
            $ast = $this->fusionParser->parse($rootFusionContents, $pathToRootFusion);

            if (isset($ast['__prototypes'][$prototypeName->asString()])) {
                return new Prototype($prototypeName, $ast['__prototypes'][$prototypeName->asString()]);
            }
        }

        return null;
    }
}
