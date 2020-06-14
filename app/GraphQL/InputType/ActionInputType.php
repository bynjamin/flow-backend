<?php

namespace App\GraphQL\InputType;
    
use App\GraphQL\TypeRegistry;

use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Type as GraphQLType;

class ActionInputType extends GraphQLType {

    protected $inputObject = true;
    
    protected $attributes = [
        'name'          => 'ActionInput',
        'description'   => 'Action input type',
    ];

    public function fields() : array
    {
        return [
            'create' => [
                'type' => Type::nonNull(Type::boolean()), 
                'description' => 'Create',
            ],
            'read' => [
                'type' => Type::nonNull(Type::boolean()), 
                'description' => 'Read',
            ],
            'update' => [
                'type' => Type::nonNull(Type::boolean()), 
                'description' => 'Update',
            ],
            'delete' => [
                'type' => Type::nonNull(Type::boolean()), 
                'description' => 'Delete',
            ],
        ];
    }
    
}
