<?php

namespace App\GraphQL\Query;

use GraphQL;
use App\Tenant;
use App\Tenant\User;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Query;

use Hyn\Tenancy\Environment;

class MeQuery extends Query {

    protected $attributes = [
        'name' => 'Me query',
        'description' => 'Get actual user data'
    ];

    public function type(): \GraphQL\Type\Definition\Type
    {
        return GraphQL::type('User');
    }

    public function args(): array
    {
        return [];
    }

    public function resolve($root, $args)
    {       
        if (!auth()->user()) {
            throw new \GraphQL\Error\Error('Not authorized!');
        }

        return auth()->user();
    }

}
