<?php

namespace App\GraphQL\Query;

use GraphQL;
use App\Tenant\User;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Query;

class CanPerformActionQuery extends Query {

    protected $attributes = [
        'name' => 'Can perfrom action',
        'description' => 'Check if user can perform action'
    ];

    public function type(): \GraphQL\Type\Definition\Type
    {
        return Type::nonNull(Type::Boolean());
    }

    public function args(): array
    {
        return [
            'action' => [
                'type' => Type::nonNull(Type::string())
            ],
            'model' => [
                'type' => Type::nonNull(Type::string())
            ],
            'model_id' => [
                'type' => Type::int()
            ],
        ];
    }

    public function resolve($root, $args)
    {
        $model_id = (empty($args['model_id'])) ? 0 : $args['model_id'];

        //return User::checkAction($args['model'], $args['action'], $model_id);
    }

}
