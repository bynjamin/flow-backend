<?php
namespace App\GraphQL\ScalarType;

use GraphQL\Error\Error;
use GraphQL\Error\InvariantViolation;
use GraphQL\Language\AST\StringValueNode;
use GraphQL\Type\Definition\ScalarType;
use GraphQL\Utils\Utils;

class Base64Type extends ScalarType
{
    // Note: name can be omitted. In this case it will be inferred from class name 
    // (suffix "Type" will be dropped)
    public $name = 'Base64';
    public $description = 'Base64 format string';


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
                
        // check if it is string:
        if ( gettype($value) != "string") {
            throw new \GraphQL\Error\Error('Query error: Can only parse strings. Got: '.gettype($value));
        }
  
        $base64 = $this->processBase64($value);
        // if it is not base64, throw error
        if ( $base64['notBase64'] ) {	
            throw new \GraphQL\Error\Error('Bad data format. Must be valid Base64.');
        }

        return $base64;
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
     * @return mixed [ "notBase64" => boolean, "fileType" => string, "fileExtension" => string, "base64string" => string ]
     * @throws Error
     */
    public function parseLiteral($valueNode)
    {
        // check if it is string:
        if (!$valueNode instanceof StringValueNode) {
            throw new \GraphQL\Error\Error('Query error: Can only parse strings got: ' . $valueNode->kind, [$valueNode]);
        }
  
        $base64 = $this->processBase64($valueNode->value);

        // if it is not base64, throw error
        if ( $base64['notBase64'] ) {	
            throw new \GraphQL\Error\Error('Bad data format. Must be valid Base64.');
        }

        return $base64;
    }


    private function processBase64($base64Data) 
    {
        
        $data = explode( ',', $base64Data );
        // $data[ 0 ] == "data:image/png;base64"    (head)
        // $data[ 1 ] == <actual base64 string>     (body)

        // check if string is valid BASE64 format
        if ( $this->notBase64($data[0]) ) {
            $processedData['notBase64'] = true;
            // if not, stop processing and return data
            return $processedData;
        }

        $fileData = $this->getFileData($data[0]);

        $processedData['notBase64'] = false;
        $processedData['fileType'] = $fileData['type'];
        $processedData['fileExtension'] = $fileData['extension'];
        $processedData['base64string'] = $data[1];

        return $processedData;
    }

    private function getFileData($base64Head) 
    {
        if ($base64Head == 'data:') {
            $fileData['type'] = 'unknown';
            $fileData['extension'] = 'unknown';     
        } else {

            // remove 'data:' + ';base64' from string
            $base64Head = str_replace("data:","",$base64Head);
            $base64Head = str_replace(";base64","",$base64Head);

            $data = explode( '/', $base64Head );
            $fileData['type'] = $data[0];
            $fileData['extension'] = $data[1];
        }
  
        return $fileData;
    }


    /**
     * This function resolve if provided data are base64. 
     * Return TRUE if data are not base64.
     * 
     * @param string $base64head 
     * @return boolean 
     */
    private function notBase64($base64head) 
    {

        //check if string include 'base64'
        preg_match('/(base64+)/', $base64head, $matches);

        if ( empty($matches) ) {	
            return true;
        }

        return false;
    }
}