<?php declare(strict_types=1);
namespace PackageFactory\Neos\CodeGenerator\Domain\Code\Php\Type;

/*
 * This file is part of the PackageFactory.Neos.CodeGenerator package
 */

use Neos\Flow\Annotations as Flow;
use PHPStan\PhpDocParser\Ast\Type\ArrayTypeNode;
use PHPStan\PhpDocParser\Ast\Type\GenericTypeNode;
use PHPStan\PhpDocParser\Ast\Type\IdentifierTypeNode;
use PHPStan\PhpDocParser\Ast\Type\TypeNode;
use PHPStan\PhpDocParser\Ast\Type\UnionTypeNode;
use PHPStan\PhpDocParser\Lexer\Lexer;
use PHPStan\PhpDocParser\Parser\ConstExprParser;
use PHPStan\PhpDocParser\Parser\PhpDocParser;
use PHPStan\PhpDocParser\Parser\TokenIterator;
use PHPStan\PhpDocParser\Parser\TypeParser;

/**
 * @Flow\Scope("singleton")
 */
final class TypeFactory
{
    /**
     * @Flow\InjectConfiguration(path="shorthands")
     * @phpstan-var array<string, array{type: string, example: array{presentation: {styleguide: string, afx: string}}}>
     * @var array
     */
    protected $shorthands;

    /**
     * @var Lexer
     */
    private $lexer;

    /**
     * @var ConstExprParser
     */
    private $constExprParser;

    /**
     * @var TypeParser
     */
    private $typeParser;

    /**
     * @var PhpDocParser
     */
    private $phpDocParser;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->lexer = new Lexer();
        $this->constExprParser = new ConstExprParser();
        $this->typeParser = new TypeParser($this->constExprParser);
        $this->phpDocParser = new PhpDocParser(new TypeParser($this->constExprParser), $this->constExprParser);
    }

    /**
     * @param string $string
     * @return TypeInterface
     */
    public function fromString(string $string): TypeInterface
    {
        $string = trim($string);
        if ($string[0] === '?') {
            $nullable = true;
            $string = trim(substr($string, 1));
        } else {
            $nullable = false;
            $string = $string;
        }

        if (isset($this->shorthands[$string]['type'])) {
            $string = $this->shorthands[$string]['type'];
        }

        $type = $this->fromPhpDocTypeNode(
            $this->typeParser->parse(
                new TokenIterator($this->lexer->tokenize($string))
            )
        );

        if ($nullable) {
            return $type->asNullable();
        } else {
            return $type;
        }
    }

    /**
     * @param \ReflectionType $reflectionType
     * @return TypeInterface
     */
    public function fromReflectionType(\ReflectionType $reflectionType): TypeInterface
    {
        throw new \Exception('not implemented');
    }

    /**
     * @param \ReflectionProperty $reflectionProperty
     * @return TypeInterface
     */
    public function fromReflectionProperty(\ReflectionProperty $reflectionProperty): TypeInterface
    {
        if ($docComment = $reflectionProperty->getDocComment()) {
            return $this->fromPropertyDocComment($docComment);
        } elseif ($reflectionType = $reflectionProperty->getType()) {
            return $this->fromReflectionType($reflectionType);
        } else {
            throw new \Exception('@TODO: Could not infer type from reflection property');
        }
    }

    /**
     * @param string $docComment
     * @return TypeInterface
     */
    public function fromPropertyDocComment(string $docComment): TypeInterface
    {
        $tokens = new TokenIterator($this->lexer->tokenize($docComment));
        $actualPhpDocNode = $this->phpDocParser->parse($tokens);

        foreach ($actualPhpDocNode->getVarTagValues() as $varTagvalue) {
            return $this->fromPhpDocTypeNode($varTagvalue->type);
        }

        throw new \InvalidArgumentException('@TODO: Invalid doc comment');
    }

    /**
     * @param TypeNode $node
     * @return TypeInterface
     */
    public function fromPhpDocTypeNode(TypeNode $node): TypeInterface
    {
        switch (true) {
            case $node instanceof IdentifierTypeNode:
                return $this->fromPhpDocIdentifierTypeNode($node);
            case $node instanceof ArrayTypeNode:
                return $this->fromPhpDocArrayTypeNode($node);
            case $node instanceof UnionTypeNode:
                return $this->fromPhpDocUnionTypeNode($node);
            case $node instanceof GenericTypeNode:
                return $this->fromPhpDocGenericTypeNode($node);
            default:
                throw new \Exception('@TODO: Unknown doc comment node: ' . get_class($node));
        }
    }

    /**
     * @param IdentifierTypeNode $node
     * @return TypeInterface
     */
    public function fromPhpDocIdentifierTypeNode(IdentifierTypeNode $node): TypeInterface
    {
        if (ScalarType::isValidName($node->name)) {
            return new ScalarType($node->name, false);
        } elseif (ArrayType::isValidName($node->name)) {
            return new ArrayType(ScalarType::int(), new MixedType, false);
        } elseif (IterableType::isValidName($node->name)) {
            return new IterableType(ScalarType::int(), new MixedType, false);
        } else {
            return new ClassType($node->name, [], false);
        }
    }

    /**
     * @param ArrayTypeNode $node
     * @return TypeInterface
     */
    public function fromPhpDocArrayTypeNode(ArrayTypeNode $node): TypeInterface
    {
        return new ArrayType(ScalarType::int(), $this->fromPhpDocTypeNode($node->type), false);
    }

    /**
     * @param UnionTypeNode $node
     * @return TypeInterface
     */
    public function fromPhpDocUnionTypeNode(UnionTypeNode $node): TypeInterface
    {
        $numberOfTypes = count($node->types);

        if ($numberOfTypes === 1) {
            return $this->fromPhpDocTypeNode($node->types[0]);
        } elseif ($numberOfTypes === 2) {
            $nullable = array_reduce(
                $node->types,
                static function (bool $carry, TypeNode $node) { return $carry || ((string) $node === 'null'); },
                false
            );

            if ($nullable) {
                foreach ($node->types as $typeNode) {
                    if ((string) $typeNode !== 'null') {
                        return $this->fromPhpDocTypeNode($typeNode)->asNullable();
                    }
                }
            }
        }

        throw new \Exception('@TODO: Union types are not supported yet.');
    }

    /**
     * @param GenericTypeNode $node
     * @return TypeInterface
     */
    public function fromPhpDocGenericTypeNode(GenericTypeNode $node): TypeInterface
    {
        if ($node->type->name === 'array') {
            $numberOfParameters = count($node->genericTypes);

            if ($numberOfParameters === 0) {
                return new ArrayType(ScalarType::int(), new MixedType, false);
            } elseif ($numberOfParameters === 1) {
                return new ArrayType(ScalarType::int(), $this->fromPhpDocTypeNode($node->genericTypes[0]), false);
            } else {
                return new ArrayType(
                    $this->fromPhpDocTypeNode($node->genericTypes[0]),
                    $this->fromPhpDocTypeNode($node->genericTypes[1]),
                    false
                );
            }
        } elseif ($node->type->name === 'iterable') {
            $numberOfParameters = count($node->genericTypes);

            if ($numberOfParameters === 0) {
                return new IterableType(ScalarType::int(), new MixedType, false);
            } elseif ($numberOfParameters === 1) {
                return new IterableType(ScalarType::int(), $this->fromPhpDocTypeNode($node->genericTypes[0]), false);
            } else {
                return new IterableType(
                    $this->fromPhpDocTypeNode($node->genericTypes[0]),
                    $this->fromPhpDocTypeNode($node->genericTypes[1]),
                    false
                );
            }
        } else {
            return new ClassType(
                $node->type->name,
                array_map(
                    function (TypeNode $node) { return $this->fromPhpDocTypeNode($node); },
                    $node->genericTypes
                ),
                false
            );
        }
    }
}
