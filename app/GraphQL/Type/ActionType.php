<?php
namespace App\GraphQL\Type;

use App\GraphQL\TypeRegistry;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Type as GraphQLType;

class ActionType extends GraphQLType {

    protected $attributes = [
        'name'          => 'Action',
        'description'   => 'Action type'
    ];

    public function fields(): array
    {
        return [
            'create' => [
                'type'        => Type::boolean(),
                'description' => 'Create',
            ],
            'update' => [
                'type'        => Type::boolean(),
                'description' => 'Update',
            ],
            'read' => [
                'type'        => Type::boolean(),
                'description' => 'Read',
            ],
            'delete' => [
                'type'        => Type::boolean(),
                'description' => 'Delete',
            ],
        ];
    }

}
