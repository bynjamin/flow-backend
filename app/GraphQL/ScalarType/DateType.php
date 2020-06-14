<?php
namespace App\GraphQL\ScalarType;

use GraphQL\Error\Error;
use GraphQL\Error\InvariantViolation;
use GraphQL\Language\AST\StringValueNode;
use GraphQL\Type\Definition\ScalarType;
use GraphQL\Utils\Utils;
use Carbon\Carbon;

class DateType extends ScalarType
{
    // Note: name can be omitted. In this case it will be inferred from class name 
    // (suffix "Type" will be dropped)
    public $name = 'Date';
    public $description = 'String in format YYYY-MM-DD';


    /**
     * Serializes an internal value to include in a response.
     *
     * @param string $value
     * @return string
     */
    public function serialize($value)
    {
        if (is_string($value)) {
            $value = new Carbon($value);
        }
        // Assuming internal representation of email is always correct:
        return $value->format('Y-m-d');
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

        preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/", $valueNode->value, $matches);
        if ( empty($matches) ) {	
            throw new \GraphQL\Error\Error('Date is in bad format. Valid format is YYYY-MM-DD. Got: ' . $valueNode->value, [$valueNode]);
        }

        // check if year is at least 1900
        if ( date($valueNode->value) < date('1900')  ) {
            throw new \GraphQL\Error\Error('Date value must be at least year 1900. Got: ' . $valueNode->value, [$valueNode]);
        }
        return $valueNode->value;
    }
}