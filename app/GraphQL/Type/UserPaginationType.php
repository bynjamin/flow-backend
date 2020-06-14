<?php
namespace App\GraphQL\Type;

use App\GraphQL\TypeRegistry;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Type as GraphQLType;

class UserPaginationType extends GraphQLType {

    protected $attributes = [
        'name'          => 'UserPagination',
        'description'   => 'User pagination model',
        'model'         => \App\Tenant\User::class,
    ];

    public function fields(): array
    {
        return [
            'count' => [
                'type'        => Type::nonNull(Type::Int()),
                'description' => 'Total user count',
            ],            
            'items' => [
                'type' => Type::nonNull(Type::listOf(Type::nonNull(GraphQL::type('User')))),
                'description' => 'List of users'
            ]
        ];
    }

}