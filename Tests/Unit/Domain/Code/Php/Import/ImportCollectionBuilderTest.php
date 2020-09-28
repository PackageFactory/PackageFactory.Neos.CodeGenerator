<?php declare(strict_types=1);
namespace PackageFactory\Neos\CodeGenerator\Tests\Unit\Domain\Code\Php\Import;

use Neos\Flow\Tests\UnitTestCase;
use PackageFactory\Neos\CodeGenerator\Domain\Code\Php\Import\Import;
use PackageFactory\Neos\CodeGenerator\Domain\Code\Php\Import\ImportCollectionBuilder;

final class ImportCollectionBuilderTest extends UnitTestCase
{
    /**
     * @test
     * @return void
     */
    public function resolvesPotentialNamingConflicts(): void
    {
        $import1 = new Import('Vendor\\Toast\\ToastModelInterface', null);
        $import2 = new Import('Vendor\\Site\\Bread\\ModelInterface', null);
        $import3 = new Import('Vendor\\Site\\Milk\\ModelInterface', null);
        $import4 = new Import('Vendor\\Shared\\Toast\\ModelInterface', null);

        $builder = new ImportCollectionBuilder();

        $builder->addImport($import1);
        $builder->addImport($import2);

        $this->assertEquals('MilkModelInterface', $builder->resolvePotentialNamingConflictForImport($import3)->getName());
        $this->assertEquals('SharedToastModelInterface', $builder->resolvePotentialNamingConflictForImport($import4)->getName());
    }
}
