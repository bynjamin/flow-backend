<?php
namespace App\GraphQL\Type;

use App\GraphQL\TypeRegistry;
use App\Tenant\UserGroup;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Type as GraphQLType;

class UserGroupType extends GraphQLType {

    protected $attributes = [
        'name'          => 'UserGroup',
        'description'   => 'User group model',
        'model'         => \App\Tenant\UserGroup::class,
    ];

    public function fields(): array
    {
        return [
            'id' => [
                'type'        => Type::nonNull(Type::int()),
                'description' => 'User group ID',
            ],
            'name' => [
                'type' => Type::nonNull(Type::string()),
                'description' => 'User group name',
            ],            
            'description' => [
                'type' => Type::nonNull(Type::string()),
                'description' => 'User group description',
            ],
            'permissions' => [
                'type'        => Type::nonNull(Type::listOf(Type::nonNull(GraphQL::type('Module')))),
                'description' => 'User group access',
                'resolve' => function($model) {

                    return $model->getModulesAccess();

                },
            ],
            'members' => [
                'type' => Type::nonNull(Type::listOf(Type::nonNull(GraphQL::type('User')))),
                'description' => 'User group users',
                'resolve' => function($model) {

                    return $model->users()->where('deleted', 0)->get();
                    
                },
            ],   
            'memberCount' => [
                'type' => Type::nonNull(Type::Int()),
                'description' => 'User group users count',
                'resolve' => function($model) {

                    return $model->users()->where('deleted', 0)->count();
                    
                },
            ],    
            'collaborators' => [
                'type' => Type::nonNull(GraphQL::type('Collaborators')),
                'description' => 'All users and user groups that has permissions to C,R,U,D',
                'resolve' => function($model) {

                    return $model->getCollaborators();
                    
                },
            ]
        ];
    }

}