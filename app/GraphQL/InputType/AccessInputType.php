<?php

namespace App\GraphQL\InputType;
    
use App\GraphQL\TypeRegistry;

use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Type as GraphQLType;

class AccessInputType extends GraphQLType {

    protected $inputObject = true;
    
    protected $attributes = [
        'name'          => 'AccessInput',
        'description'   => 'Access input type',
    ];

    public function fields() : array
    {
        return [
            'basic' => [
                'type' => Type::nonNull(GraphQL::type('ActionInput')), 
                'description' => 'Basic access',
            ],
            'global' => [
                'type' => Type::nonNull(GraphQL::type('ActionInput')), 
                'description' => 'Global access',
            ]            
        ];
    }
    
}
