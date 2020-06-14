<?php

namespace App\GraphQL\Query;

use GraphQL;
use App\Tenant\User;
use App\Tenant\UserGroup;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Query;

class UserRolesQuery extends Query {

    protected $attributes = [
        'name' => 'Get user roles',
        'description' => 'Get user roles'
    ];

    public function type(): \GraphQL\Type\Definition\Type
    {
        return Type::nonNull(Type::listOf(Type::nonNull(GraphQL::type('UserRole'))));
    }

    public function args(): array
    {
        return [];
    }

    public function resolve($root, $args)
    {
        User::checkAction('UserGroup', 'read');

        return UserGroup::role()->orderBy('id', 'ASC')->get();
        
    }

}
