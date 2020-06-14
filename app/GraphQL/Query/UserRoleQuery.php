<?php

namespace App\GraphQL\Query;

use GraphQL;
use App\Tenant\User;
use App\Tenant\UserGroup;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Query;

class UserRoleQuery extends Query {

    protected $attributes = [
        'name' => 'Get user role',
        'description' => 'Get user role'
    ];

    public function type(): \GraphQL\Type\Definition\Type
    {
        return Type::nonNull(GraphQL::type('UserRole'));
    }

    public function args(): array
    {
        return [
            'roleId' => [
                'name'    => 'roleId',
                'type'    => Type::nonNull(Type::int())
            ]
        ];
    }

    public function resolve($root, $args)
    {
        User::checkAction('UserGroup', 'read');

        if (empty($args['roleId'])) {
            throw new \GraphQL\Error\Error('User role does not exist.');
        }
        
        $role = UserGroup::role()->find($args['roleId']);

        if (!$role) {
            throw new \GraphQL\Error\Error('User role does not exist.');
        }

        return $role;
    }

}
