<?php
namespace App\GraphQL\Type;

use App\GraphQL\TypeRegistry;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Type as GraphQLType;

class CrudType extends GraphQLType {

    protected $attributes = [
        'name'          => 'Crud',
        'description'   => 'Crud type'
    ];

    public function fields(): array
    {
        return [
            'create' => [
                'type'        => Type::nonNull(Type::boolean()),
                'description' => 'Create',
            ],
            'update' => [
                'type'        => Type::nonNull(Type::boolean()),
                'description' => 'Update',
            ],
            'read' => [
                'type'        => Type::nonNull(Type::boolean()),
                'description' => 'Read',
            ],
            'delete' => [
                'type'        => Type::nonNull(Type::boolean()),
                'description' => 'Delete',
            ],
        ];
    }

}
