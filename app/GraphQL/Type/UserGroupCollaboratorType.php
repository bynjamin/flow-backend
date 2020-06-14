<?php
namespace App\GraphQL\Type;

use App\GraphQL\TypeRegistry;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Type as GraphQLType;

class UserGroupCollaboratorType extends GraphQLType {

    protected $attributes = [
        'name'          => 'UserGroupCollaborator',
        'description'   => 'User group collaborators type'
    ];

    public function fields(): array
    {
        return [
            'userGroup' => [
                'type'        => Type::nonNull(GraphQL::type('UserGroup')),
                'description' => 'User group',
            ],
            'rules' => [
                'type'        => Type::nonNull(GraphQL::type('Crud')),
                'description' => 'CRUD',
            ],           
        ];
    }

}
