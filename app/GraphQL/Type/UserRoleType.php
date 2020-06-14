<?php
namespace App\GraphQL\Type;

use App\GraphQL\TypeRegistry;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Type as GraphQLType;

class UserRoleType extends GraphQLType {

    protected $attributes = [
        'name'          => 'UserRole',
        'description'   => 'User role model',
        'model'         => \App\Tenant\UserGroup::class,
    ];

    public function fields(): array
    {
        return [
            'id' => [
                'type'        => Type::nonNull(Type::int()),
                'description' => 'User role ID',
            ],
            'name' => [
                'type' => Type::nonNull(Type::string()),
                'description' => 'User role name',
            ],            
            'description' => [
                'type' => Type::nonNull(Type::string()),
                'description' => 'User role description',
            ],
            'permissions' => [
                'type'        => Type::nonNull(Type::listOf(Type::nonNull(GraphQL::type('Module')))),
                'description' => 'User role access',
                'resolve'     => function($model) {

                    return $model->getModulesAccess();

                },
            ], 
            'members' => [
                'type' => Type::nonNull(Type::listOf(Type::nonNull(GraphQL::type('User')))),
                'description' => 'User role users',
                'resolve' => function($model) {

                    return $model->users()->where('deleted', 0)->get();
                    
                },
            ],     
            'memberCount' => [
                'type' => Type::nonNull(Type::Int()),
                'description' => 'User role users count',
                'resolve' => function($model) {

                    return $model->users()->where('deleted', 0)->count();
                    
                },
            ],    
            'level' => [
                'type' => Type::nonNull(Type::Int()),
                'description' => 'User role level',
                'resolve' => function($model) {

                    return $model->id;
                    
                },
            ],      
        ];
    }

}