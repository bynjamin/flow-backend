<?php
namespace App\GraphQL\Type;

use App\GraphQL\TypeRegistry;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Type as GraphQLType;

class UserGroupPaginationType extends GraphQLType {

    protected $attributes = [
        'name'          => 'UserGroupPagination',
        'description'   => 'User group pagination model',
        'model'         => \App\Tenant\UserGroup::class,
    ];

    public function fields(): array
    {
        return [
            'count' => [
                'type'        => Type::nonNull(Type::Int()),
                'description' => 'Total user group count',
            ],            
            'items' => [
                'type' => Type::nonNull(Type::listOf(Type::nonNull(GraphQL::type('UserGroup')))),
                'description' => 'List of users groups'
            ]
        ];
    }

}