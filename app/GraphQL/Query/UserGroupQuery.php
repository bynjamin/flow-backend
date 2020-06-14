<?php

namespace App\GraphQL\Query;

use GraphQL;
use App\Tenant\User;
use App\Tenant\UserGroup;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Query;

class UserGroupQuery extends Query {

    protected $attributes = [
        'name' => 'Get user group',
        'description' => 'Get user group'
    ];

    public function type(): \GraphQL\Type\Definition\Type
    {
        return Type::nonNull(GraphQL::type('UserGroup'));
    }

    public function args(): array
    {
        return [
            'groupId' => [
                'name'    => 'groupId',
                'type'    => Type::nonNull(Type::int())
            ],
        ];
    }

    public function resolve($root, $args)
    {
        User::checkAction('UserGroup', 'read');

        if (empty($args['groupId'])) {
            throw new \GraphQL\Error\Error('User group does not exist.');
        }

        $group = UserGroup::group()->find($args['groupId']);

        if (!$group) {
            throw new \GraphQL\Error\Error('User group does not exist.');
        }

        return $group;
    }

}
