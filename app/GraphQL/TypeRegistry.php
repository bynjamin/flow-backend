<?php
namespace App\GraphQL;

use App\GraphQL\ScalarType\DateType;
use App\GraphQL\ScalarType\Base64Type;
//use App\GraphQL\ScalarType\CountryCodeType;
//use App\GraphQL\ScalarType\NaceCategoryCodeType;
//use App\GraphQL\ScalarType\KeywordType;

class TypeRegistry
{
    private static $dateType;
    private static $base64Type;
    // private static $countryCodeType;
    // private static $naceCategoryCodeType;
    // private static $keywordType;


    public static function dateType()
    {
        return self::$dateType ?: (self::$dateType = new DateType());
    }

    public static function base64Type()
    {
        return self::$base64Type ?: (self::$base64Type = new Base64Type());
    }

    // public static function countryCodeType()
    // {
    //     return self::$countryCodeType ?: (self::$countryCodeType = new CountryCodeType());
    // }
    //
    // public static function naceCategoryCodeType()
    // {
    //     return self::$naceCategoryCodeType ?: (self::$naceCategoryCodeType = new NaceCategoryCodeType());
    // }
    //
    // public static function keywordType()
    // {
    //     return self::$keywordType ?: (self::$keywordType = new KeywordType());
    // }

}
