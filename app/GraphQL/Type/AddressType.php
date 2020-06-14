<?php
namespace App\GraphQL\Type;

use App\GraphQL\TypeRegistry;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Type as GraphQLType;

class AddressType extends GraphQLType {

    protected $attributes = [
        'name'          => 'Address',
        'description'   => 'Address model',
        'model'         => \App\Tenant\Address::class,
    ];

    public function fields(): array
    {
        return [
            'street' => [
                'type'        => Type::string(),
                'description' => 'Street'
            ],
            'zip' => [
                'type'        => Type::string(),
                'description' => 'Zip',
            ],
            'city' => [
                'type'        => Type::string(),
                'description' => 'City'
            ],
            'country' => [
                'type'        => Type::string(),
                'description' => 'Country'               
            ]          
        ];
    }

}