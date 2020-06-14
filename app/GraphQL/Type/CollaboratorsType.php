<?php
namespace App\GraphQL\Type;

use App\GraphQL\TypeRegistry;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Type as GraphQLType;

class CollaboratorsType extends GraphQLType {

    protected $attributes = [
        'name'          => 'Collaborators',
        'description'   => 'Collaborators type'
    ];

    public function fields(): array
    {
        return [
            'users' => [
                'type'        => Type::nonNull(Type::listOf(GraphQL::type('UserCollaborator'))),
                'description' => 'Users',
            ],
            'userGroups' => [
                'type'        => Type::nonNull(Type::listOf(GraphQL::type('UserGroupCollaborator'))),
                'description' => 'User groups',
            ],           
        ];
    }

}
