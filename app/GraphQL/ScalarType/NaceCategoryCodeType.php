<?php
namespace App\GraphQL\ScalarType;

use GraphQL\Error\Error;
use GraphQL\Error\InvariantViolation;
use GraphQL\Language\AST\StringValueNode;
use GraphQL\Type\Definition\ScalarType;
use GraphQL\Utils\Utils;

class NaceCategoryCodeType extends ScalarType
{
    // Note: name can be omitted. In this case it will be inferred from class name 
    // (suffix "Type" will be dropped)
    public $name = 'NACECategoryCode';
    public $description = 'String in NACE format: ^[A-Z]{1}[0-9.]+$';


    /**
     * Serializes an internal value to include in a response.
     *
     * @param string $value
     * @return string
     */
    public function serialize($value)
    {
        // Assuming internal representation of email is always correct:
        return $value;
    }

    /**
     * Parses an externally provided value (query variable) to use as an input
     *
     * @param mixed $value
     * @return mixed
     */
    public function parseValue($value)
    {
        return $value;
    }

    /**
     * Parses an externally provided literal value (hardcoded in GraphQL query) to use as an input.
     * 
     * E.g. 
     * {
     *   user(email: "user@example.com") 
     * }
     *
     * @param \GraphQL\Language\AST\Node $valueNode
     * @return string
     * @throws Error
     */
    public function parseLiteral($valueNode, ?array $variables = null)
    {
        if (!$valueNode instanceof StringValueNode) {
            throw new \GraphQL\Error\Error('Query error: Can only parse strings got: ' . $valueNode->kind, [$valueNode]);
        }

        preg_match("/^[A-Z]{1}[0-9.]*$/", $valueNode->value, $matches);
        if ( empty($matches) ) {	
            throw new \GraphQL\Error\Error('Invalid NACE format. Valid format is ^[A-Z]{1}[0-9.]*$ Got: ' . $valueNode->value, [$valueNode]);
        }

        return $valueNode->value;
    }
}