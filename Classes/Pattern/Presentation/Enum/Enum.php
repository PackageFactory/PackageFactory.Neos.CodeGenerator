<?php declare(strict_types=1);
namespace PackageFactory\Neos\CodeGenerator\Pattern\Presentation\Enum;

/*
 * This file is part of the PackageFactory.Neos.CodeGenerator package
 */

use Neos\Flow\Annotations as Flow;
use PackageFactory\Neos\CodeGenerator\Domain\Code\Common\Signature\SignatureInterface;
use PackageFactory\Neos\CodeGenerator\Domain\Code\Php\PhpClass\PhpClassName;
use PackageFactory\Neos\CodeGenerator\Domain\Code\Php\Import\Import;
use PackageFactory\Neos\CodeGenerator\Domain\Code\Php\PhpFile;
use PackageFactory\Neos\CodeGenerator\Domain\Code\Php\PhpFileBuilder;
use PackageFactory\Neos\CodeGenerator\Framework\Util\StringUtil;
use PackageFactory\Neos\CodeGenerator\Pattern\Presentation\Presentation;

/**
 * @Flow\Proxy(false)
 */
final class Enum
{
    /**
     * @var Presentation
     */
    private $presentation;

    /**
     * @var string
     */
    private $name;

    /**
     * @var integer
     */
    private $timestamp;

    /**
     * @var EnumType
     */
    private $type;

    /**
     * @var SignatureInterface
     */
    private $signature;

    /**
     * @var EnumValue[]
     */
    private $values;

    /**
     * @param Presentation $presentation
     * @param string $name
     * @param int $timestamp
     * @param EnumType $type
     * @param SignatureInterface $signature
     * @param EnumValue[] $values
     */
    public function __construct(
        Presentation $presentation,
        string $name,
        int $timestamp,
        EnumType $type,
        SignatureInterface $signature,
        array $values
    ) {
        $this->presentation = $presentation;
        $this->name = $name;
        $this->timestamp = $timestamp;
        $this->type = $type;
        $this->signature = $signature;
        $this->values = $values;
    }

    /**
     * @return EnumType
     */
    public function getType(): EnumType
    {
        return $this->type;
    }

    /**
     * @return EnumValue[]
     */
    public function getValues(): array
    {
        return $this->values;
    }

    /**
     * @return PhpClassName
     */
    public function getPhpClassNameForValueObject(): PhpClassName
    {
        return $this->presentation->getPhpNamespace()->append($this->name)->asClassName();
    }

    /**
     * @return PhpFile
     */
    public function asPhpClassFileForValueObject(): PhpFile
    {
        $builder = new PhpFileBuilder();
        $className = $this->getPhpClassNameForValueObject();

        $builder->setPath($this->presentation->getPhpFilePathForClassName($className));
        $builder->setNamespaceFromClassName($className);
        $builder->setSignature($this->signature);
        $builder->getImportCollectionBuilder()
            ->addImport(new Import('Neos\\Flow\\Annotations', 'Flow'));

        $code = [];

        $code[] = '';
        $code[] = '/**';
        $code[] = ' * @Flow\Proxy(false)';
        $code[] = ' */';
        $code[] = 'final class ' . $className->asDeclarationNameString();
        $code[] = '{';
        foreach ($this->values as $value) {
            $code[] = '    public const ' . $value->asConstantName($className->asDeclarationNameString()) . ' = ' . $this->type->quoteValue($value) . ';';
        }
        $code[] = '';
        $code[] = '    /**';
        $code[] = '     * @var ' . $this->type->asPhpDocType();
        $code[] = '     */';
        $code[] = '    private $value;';
        $code[] = '';
        $code[] = '    /**';
        $code[] = '     * @param ' . $this->type->asPhpDocType() . ' $value';
        $code[] = '     */';
        $code[] = '    private function __construct(' . $this->type->asPhpTypeHint() . ' $value)';
        $code[] = '    {';
        $code[] = '        $this->value = $value;';
        $code[] = '    }';
        $code[] = '';
        $code[] = '    /**';
        $code[] = '     * @param ' . $this->type->asPhpDocType() . ' $' . $this->type->asVariableName();
        $code[] = '     * @return self';
        $code[] = '     */';
        $code[] = '    public static function ' . $this->type->asStaticFactoryMethodName() . '(' . $this->type->asPhpTypeHint() . ' $' . $this->type->asVariableName() . '): self';
        $code[] = '    {';
        $code[] = '        if (!in_array($' . $this->type->asVariableName() . ', self::getValues())) {';
        $code[] = '            throw ' . $className->asDeclarationNameString() . 'IsInvalid::becauseItMustBeOneOfTheDefinedConstants($' . $this->type->asVariableName() . ');';
        $code[] = '        }';
        $code[] = '';
        $code[] = '        return new self($' . $this->type->asVariableName() . ');';
        $code[] = '    }';
        foreach ($this->values as $value) {
            $code[] = '';
            $code[] = '    /**';
            $code[] = '     * @return self';
            $code[] = '     */';
            $code[] = '    public static function ' . $value->asStaticFactoryMethodName() . '(): self';
            $code[] = '    {';
            $code[] = '        return new self(self::' . $value->asConstantName($className->asDeclarationNameString()) . ');';
            $code[] = '    }';
        }
        foreach ($this->values as $value) {
            $code[] = '';
            $code[] = '    /**';
            $code[] = '     * @return boolean';
            $code[] = '     */';
            $code[] = '    public function ' . $value->asComparatorMethodName() . '(): bool';
            $code[] = '    {';
            $code[] = '        return $this->value === self::' . $value->asConstantName($className->asDeclarationNameString()) . ';';
            $code[] = '    }';
        }
        $code[] = '';
        $code[] = '    /**';
        $code[] = '     * @return array|' . $this->type->asPhpDocType() . '[]';
        $code[] = '     */';
        $code[] = '    public static function getValues(): array';
        $code[] = '    {';
        $code[] = '        return [';
        foreach ($this->values as $value) {
            $code[] = '            self::' . $value->asConstantName($className->asDeclarationNameString()) . ',';
        }
        $code[] = '        ];';
        $code[] = '    }';
        $code[] = '';
        $code[] = '    /**';
        $code[] = '     * @return ' . $this->type->asPhpDocType();
        $code[] = '     */';
        $code[] = '    public function getValue(): ' . $this->type->asPhpTypeHint();
        $code[] = '    {';
        $code[] = '        return $this->value;';
        $code[] = '    }';
        $code[] = '';
        $code[] = '    /**';
        $code[] = '     * @return string';
        $code[] = '     */';
        $code[] = '    public function __toString(): string';
        $code[] = '    {';
        $code[] = '        return ' . $this->type->castVariableToString('this->value') . ';';
        $code[] = '    }';
        $code[] = '}';
        $code[] = '';

        $builder->setCode(join(PHP_EOL, $code));

        return $builder->build();
    }

    /**
     * @return PhpClassName
     */
    public function getPhpClassNameForException(): PhpClassName
    {
        return $this->getPhpClassNameForValueObject()->append('IsInvalid');
    }

    /**
     * @return PhpFile
     */
    public function asPhpClassFileForException(): PhpFile
    {
        $builder = new PhpFileBuilder();

        $builder->setPath($this->presentation->getPhpFilePathForClassName($this->getPhpClassNameForException()));
        $builder->setNamespaceFromClassName($this->getPhpClassNameForException());
        $builder->setSignature($this->signature);
        $builder->getImportCollectionBuilder()
            ->addImport(new Import('Neos\\Flow\\Annotations', 'Flow'));

        $code = [];

        $code[] = '';
        $code[] = '/**';
        $code[] = ' * @Flow\Proxy(false)';
        $code[] = ' */';
        $code[] = 'final class ' . $this->getPhpClassNameForException()->asDeclarationNameString() . ' extends \DomainException';
        $code[] = '{';
        $code[] = '    /**';
        $code[] = '     * @param ' . $this->type->asPhpDocType() . ' $attemptedValue';
        $code[] = '     * @return self';
        $code[] = '     */';
        $code[] = '    public static function becauseItMustBeOneOfTheDefinedConstants(' . $this->type->asPhpTypeHint() . ' $attemptedValue): self';
        $code[] = '    {';
        $code[] = '        return new self(\'The given value "\' . $attemptedValue . \'" is no valid ' . $this->getPhpClassNameForValueObject()->asDeclarationNameString() . ', must be one of the defined constants. \', ' . $this->timestamp . ');';
        $code[] = '    }';
        $code[] = '}';
        $code[] = '';

        $builder->setCode(join(PHP_EOL, $code));

        return $builder->build();
    }

    /**
     * @return PhpFile
     */
    public function asPhpClassFileForDataSource(): PhpFile
    {
        $builder = new PhpFileBuilder();

        $package = $this->presentation->getDistributionPackage();
        $packageKey = $package->getPackageKey();
        $enumName = $this->getPhpClassNameForValueObject()->asDeclarationNameString();

        $namespace = $packageKey->asPhpNamespace()->append('Application\DataSource');
        $className = $namespace->append($enumName)->asClassName()->append('Provider');

        $dataSourceIdentifier = $packageKey->asString() . '-' . $this->getPhpClassNameForValueObject()->asNamespace()->truncateAscendant($this->presentation->getPhpNamespace())->asString();
        $dataSourceIdentifier = StringUtil::kebabCase($dataSourceIdentifier);
        $dataSourceIdentifier = strtolower($dataSourceIdentifier);

        $translationSourceName = $this->getPhpClassNameForValueObject()->asNamespace()->truncateAscendant($this->presentation->getPhpNamespace())->asString();
        $translationSourceName = str_replace('\\', '.', $translationSourceName);

        $builder->setPath($package->getPhpFilePathForClassName($className));
        $builder->setNamespace($namespace);
        $builder->setSignature($this->signature);

        $importBuilder = $builder->getImportCollectionBuilder();
        $importBuilder->addImport(new Import('Neos\\Flow\\Annotations', 'Flow'));
        $importBuilder->addImport(new Import('Neos\\ContentRepository\\Domain\\Model\\NodeInterface', null));
        $importBuilder->addImport(new Import('Neos\\Flow\\I18n\\Translator', null));
        $importBuilder->addImport(new Import('Neos\\Neos\\Service\\DataSource\\AbstractDataSource', null));
        $importBuilder->addImport(new Import('Neos\\Eel\\ProtectedContextAwareInterface', null));
        $importBuilder->addImport(new Import($this->getPhpClassNameForValueObject()->asNamespace()->asString(), null));

        $code = [];

        $code[] = '';
        $code[] = '/**';
        $code[] = ' * @Flow\Scope("singleton")';
        $code[] = ' */';
        $code[] = 'class ' . $className->asDeclarationNameString() . ' extends AbstractDataSource implements ProtectedContextAwareInterface';
        $code[] = '{';
        $code[] = '    /**';
        $code[] = '     * @Flow\Inject';
        $code[] = '     * @var Translator';
        $code[] = '     */';
        $code[] = '    protected $translator;';
        $code[] = '';
        $code[] = '    /**';
        $code[] = '     * @var string';
        $code[] = '     */';
        $code[] = '    protected static $identifier = \'' . $dataSourceIdentifier . '\';';
        $code[] = '';
        $code[] = '    /**';
        $code[] = '     * @param null|NodeInterface $node';
        $code[] = '     * @param array<mixed> $arguments';
        $code[] = '     * @return array<mixed>';
        $code[] = '     */';
        $code[] = '    public function getData(NodeInterface $node = null, array $arguments = []): array';
        $code[] = '    {';
        $code[] = '        $result = [];';
        $code[] = '        foreach (' . $enumName . '::getValues() as $value) {';
        $code[] = '            $result[$value][\'label\'] = $this->translator->translateById(\'' . lcfirst($enumName) . '.\' . $value, [], null, null, \'' . $translationSourceName . '\', \'' . $packageKey->asString() . '\') ?? $value;';
        $code[] = '        }';
        $code[] = '        return $result;';
        $code[] = '    }';
        $code[] = '';
        $code[] = '    /**';
        $code[] = '     * @return array|' . $this->type->asPhpDocType() . '[]';
        $code[] = '     */';
        $code[] = '    public function getValues(): array';
        $code[] = '    {';
        $code[] = '        return ' . $enumName . '::getValues();';
        $code[] = '    }';
        $code[] = '';
        $code[] = '    /**';
        $code[] = '     * @param string $methodName';
        $code[] = '     * @return boolean';
        $code[] = '     */';
        $code[] = '    public function allowsCallOfMethod($methodName): bool';
        $code[] = '    {';
        $code[] = '        return true;';
        $code[] = '    }';
        $code[] = '}';
        $code[] = '';

        $builder->setCode(join(PHP_EOL, $code));

        return $builder->build();
    }
}
