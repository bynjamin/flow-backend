<?php

namespace App\GraphQL\Query;

use GraphQL;
use App\Tenant;
use App\Tenant\User;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Query;

use Hyn\Tenancy\Environment;

class UserQuery extends Query {

    protected $attributes = [
        'name' => 'User query',
        'description' => 'Get actual user data. if set id then return data for user with id = id'
    ];

    public function type(): \GraphQL\Type\Definition\Type
    {
        return GraphQL::type('User');
    }

    public function args(): array
    {
        return [
            'id' => [
                'type' => Type::int()
            ]
        ];
    }

    public function resolve($root, $args)
    {
        if (!empty($args['id'])) {

            if ($args['id'] != auth()->user()->id) {
                User::checkAction('User', 'read');
            }
            
            $user = User::find($args['id']);

            if (!$user) {
                throw new \GraphQL\Error\Error('User not existed.');
            }

            if ($user->deleted == 1) {
                throw new \GraphQL\Error\Error('User was deleted.');
            }

            return $user;

        }

        return auth()->user();
    }

}
