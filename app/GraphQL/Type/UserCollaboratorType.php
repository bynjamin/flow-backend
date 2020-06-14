<?php
namespace App\GraphQL\Type;

use App\GraphQL\TypeRegistry;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Type as GraphQLType;

class UserCollaboratorType extends GraphQLType {

    protected $attributes = [
        'name'          => 'UserCollaborator',
        'description'   => 'User collaborators type'
    ];

    public function fields(): array
    {
        return [
            'user' => [
                'type'        => Type::nonNull(GraphQL::type('User')),
                'description' => 'User',
            ],
            'rules' => [
                'type'        => Type::nonNull(GraphQL::type('Crud')),
                'description' => 'CRUD',
            ],           
        ];
    }

}
