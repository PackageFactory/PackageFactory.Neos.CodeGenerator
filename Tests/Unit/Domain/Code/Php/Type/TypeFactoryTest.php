<?php declare(strict_types=1);
namespace PackageFactory\Neos\CodeGenerator\Tests\Unit\Domain\Code\Php\Type;

use Neos\Flow\Tests\UnitTestCase;
use PackageFactory\Neos\CodeGenerator\Domain\Code\Php\Type\TypeFactory;

final class TypeFactoryTest extends UnitTestCase
{
    /**
     * @var TypeFactory
     */
    private $typeFactory;

    /**
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->typeFactory = new TypeFactory();
    }

    /**
     * @return array<string, array{string, array{string, string, boolean}}>
     */
    public function typeStrings(): array
    {
        return [
            // Scalar / Array of Scalar
            'string' => ['string', ['string', 'string', false]],
            '?string' => ['?string', ['string', 'string', true]],
            'string[]' => ['string[]', ['array', 'string[]', false]],
            '?string[]' => ['?string[]', ['array', 'string[]', true]],

            'int' => ['int', ['int', 'integer', false]],
            '?int' => ['?int', ['int', 'integer', true]],
            'int[]' => ['int[]', ['array', 'integer[]', false]],
            '?int[]' => ['?int[]', ['array', 'integer[]', true]],

            'integer' => ['int', ['int', 'integer', false]],
            '?integer' => ['?int', ['int', 'integer', true]],
            'integer[]' => ['integer[]', ['array', 'integer[]', false]],
            '?integer[]' => ['?integer[]', ['array', 'integer[]', true]],

            'float' => ['float', ['float', 'float', false]],
            '?float' => ['?float', ['float', 'float', true]],
            'float[]' => ['float[]', ['array', 'float[]', false]],
            '?float[]' => ['?float[]', ['array', 'float[]', true]],

            'bool' => ['bool', ['bool', 'boolean', false]],
            '?bool' => ['?bool', ['bool', 'boolean', true]],
            'bool[]' => ['bool[]', ['array', 'boolean[]', false]],
            '?bool[]' => ['?bool[]', ['array', 'boolean[]', true]],

            'boolean' => ['boolean', ['bool', 'boolean', false]],
            '?boolean' => ['?boolean', ['bool', 'boolean', true]],
            'boolean[]' => ['boolean[]', ['array', 'boolean[]', false]],
            '?boolean[]' => ['?boolean[]', ['array', 'boolean[]', true]],

            // Plain Array / Associative Array / Nested Array
            'array' => ['array', ['array', 'array<mixed>', false]],
            '?array' => ['?array', ['array', 'array<mixed>', true]],

            'array<string, string>' => ['array<string, string>', ['array', 'array<string, string>', false]],
            '?array<string, string>' => ['?array<string, string>', ['array', 'array<string, string>', true]],

            'array<string, string[]>' => ['array<string, string[]>', ['array', 'array<string, string[]>', false]],
            '?array<string, string[]>' => ['?array<string, string[]>', ['array', 'array<string, string[]>', true]],

            'array<string, array<string, string>>' => ['array<string, array<string, string>>', ['array', 'array<string, array<string, string>>', false]],
            '?array<string, array<string, string>>' => ['?array<string, array<string, string>>', ['array', 'array<string, array<string, string>>', true]],

            // Plain Iterable / Associative Iterable / Nested Iterable
            'iterable' => ['iterable', ['iterable', 'iterable<integer, mixed>', false]],
            '?iterable' => ['?iterable', ['iterable', 'iterable<integer, mixed>', true]],

            'iterable<string, string>' => ['iterable<string, string>', ['iterable', 'iterable<string, string>', false]],
            '?iterable<string, string>' => ['?iterable<string, string>', ['iterable', 'iterable<string, string>', true]],

            'iterable<string, string[]>' => ['iterable<string, string[]>', ['iterable', 'iterable<string, string[]>', false]],
            '?iterable<string, string[]>' => ['?iterable<string, string[]>', ['iterable', 'iterable<string, string[]>', true]],

            'iterable<string, iterable<string, string>>' => ['iterable<string, iterable<string, string>>', ['iterable', 'iterable<string, iterable<string, string>>', false]],
            '?iterable<string, iterable<string, string>>' => ['?iterable<string, iterable<string, string>>', ['iterable', 'iterable<string, iterable<string, string>>', true]],

            // Built-in classes
            '\\stdClass' => ['\\stdClass', ['\\stdClass', '\\stdClass', false]],
            '?\\stdClass' => ['?\\stdClass', ['\\stdClass', '\\stdClass', true]],
            '\\stdClass[]' => ['\\stdClass[]', ['array', '\\stdClass[]', false]],
            '?\\stdClass[]' => ['?\\stdClass[]', ['array', '\\stdClass[]', true]],

            '\\DateTimeInterface' => ['\\DateTimeInterface', ['\\DateTimeInterface', '\\DateTimeInterface', false]],
            '?\\DateTimeInterface' => ['?\\DateTimeInterface', ['\\DateTimeInterface', '\\DateTimeInterface', true]],
            '\\DateTimeInterface[]' => ['\\DateTimeInterface[]', ['array', '\\DateTimeInterface[]', false]],
            '?\\DateTimeInterface[]' => ['?\\DateTimeInterface[]', ['array', '\\DateTimeInterface[]', true]],

            '\\Iterator' => ['\\Iterator', ['\\Iterator', '\\Iterator', false]],
            '?\\Iterator' => ['?\\Iterator', ['\\Iterator', '\\Iterator', true]],
            '\\Iterator[]' => ['\\Iterator[]', ['array', '\\Iterator[]', false]],
            '?\\Iterator[]' => ['?\\Iterator[]', ['array', '\\Iterator[]', true]],

            '\\Iterator<string, \\stdClass>' => ['\\Iterator<string, \\stdClass>', ['\\Iterator', '\\Iterator<string, \\stdClass>', false]],
            '?\\Iterator<string, \\stdClass>' => ['?\\Iterator<string, \\stdClass>', ['\\Iterator', '\\Iterator<string, \\stdClass>', true]],
            '\\Iterator<string, \\stdClass>[]' => ['\\Iterator<string, \\stdClass>[]', ['array', '\\Iterator<string, \\stdClass>[]', false]],
            '?\\Iterator<string, \\stdClass>[]' => ['?\\Iterator<string, \\stdClass>[]', ['array', '\\Iterator<string, \\stdClass>[]', true]],

            // Arbitrary classes
            'MyClass' => ['MyClass', ['MyClass', 'MyClass', false]],
            '?MyClass' => ['?MyClass', ['MyClass', 'MyClass', true]],

            'MyClass<float>' => ['MyClass<float>', ['MyClass', 'MyClass<float>', false]],
            '?MyClass<float>' => ['?MyClass<float>', ['MyClass', 'MyClass<float>', true]],

            'MyClass<MyOtherClass>' => ['MyClass<MyOtherClass>', ['MyClass', 'MyClass<MyOtherClass>', false]],
            '?MyClass<MyOtherClass>' => ['?MyClass<MyOtherClass>', ['MyClass', 'MyClass<MyOtherClass>', true]],

            'MyVendor\MyClass' => ['MyVendor\MyClass', ['MyVendor\MyClass', 'MyVendor\MyClass', false]],
            '?MyVendor\MyClass' => ['?MyVendor\MyClass', ['MyVendor\MyClass', 'MyVendor\MyClass', true]],

            'MyVendor\MyClass<float>' => ['MyVendor\MyClass<float>', ['MyVendor\MyClass', 'MyVendor\MyClass<float>', false]],
            '?MyVendor\MyClass<float>' => ['?MyVendor\MyClass<float>', ['MyVendor\MyClass', 'MyVendor\MyClass<float>', true]],

            'MyVendor\MyClass<MyOtherVendor\MyOtherClass>' => ['MyVendor\MyClass<MyOtherVendor\MyOtherClass>', ['MyVendor\MyClass', 'MyVendor\MyClass<MyOtherVendor\MyOtherClass>', false]],
            '?MyVendor\MyClass<MyOtherVendor\MyOtherClass>' => ['?MyVendor\MyClass<MyOtherVendor\MyOtherClass>', ['MyVendor\MyClass', 'MyVendor\MyClass<MyOtherVendor\MyOtherClass>', true]],
        ];
    }

    /**
     * @return array<string, array{object, array{string, string, boolean}}>
     */
    public function properties(): array
    {
        return [
            // Scalar
            'string' => [new class { /** @var string */ private $property; }, ['string', 'string', false]],
            '?string' => [new class { /** @var null|string */ private $property; }, ['string', 'string', true]],

            'int' => [new class { /** @var int */ private $property; }, ['int', 'integer', false]],
            '?int' => [new class { /** @var null|int */ private $property; }, ['int', 'integer', true]],

            'integer' => [new class { /** @var integer */ private $property; }, ['int', 'integer', false]],
            '?integer' => [new class { /** @var null|integer */ private $property; }, ['int', 'integer', true]],

            'float' => [new class { /** @var float */ private $property; }, ['float', 'float', false]],
            '?float' => [new class { /** @var null|float */ private $property; }, ['float', 'float', true]],

            'bool' => [new class { /** @var bool */ private $property; }, ['bool', 'boolean', false]],
            '?bool' => [new class { /** @var null|bool */ private $property; }, ['bool', 'boolean', true]],

            'boolean' => [new class { /** @var boolean */ private $property; }, ['bool', 'boolean', false]],
            '?boolean' => [new class { /** @var null|boolean */ private $property; }, ['bool', 'boolean', true]],
        ];
    }

    /**
     * @test
     * @dataProvider typeStrings
     * @param string $input
     * @param array{string, string, boolean} $expectations
     * @return void
     */
    public function createsTypesFromString(string $input, array $expectations): void
    {
        $type = $this->typeFactory->fromString($input);
        $this->assertEquals($expectations[0], $type->getNativeName());
        $this->assertEquals($expectations[1], $type->getPhpDocName());
        $this->assertEquals($expectations[2], $type->isNullable());
    }

    /**
     * @test
     * @dataProvider properties
     * @param object $object
     * @param array{string, string, boolean} $expectations
     * @return void
     */
    public function createsTypesFromReflectionProperties(object $object, array $expectations): void
    {
        $reflectionObject = new \ReflectionObject($object);
        $reflectionProperty = $reflectionObject->getProperty('property');
        $type = $this->typeFactory->fromReflectionProperty($reflectionProperty);

        $this->assertEquals($expectations[0], $type->getNativeName());
        $this->assertEquals($expectations[1], $type->getPhpDocName());
        $this->assertEquals($expectations[2], $type->isNullable());
    }
}
