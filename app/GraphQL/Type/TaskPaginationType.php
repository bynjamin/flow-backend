<?php
namespace App\GraphQL\Type;

use App\GraphQL\TypeRegistry;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Type as GraphQLType;

class TaskPaginationType extends GraphQLType {

    protected $attributes = [
        'name'          => 'TaskPagination',
        'description'   => 'Task pagination model',
        'model'         => \App\Tenant\Task::class,
    ];

    public function fields(): array
    {
        return [
            'count' => [
                'type'        => Type::nonNull(Type::Int()),
                'description' => 'Total task count',
            ],            
            'items' => [
                'type' => Type::nonNull(Type::listOf(Type::nonNull(GraphQL::type('Task')))),
                'description' => 'List of tasks'
            ]
        ];
    }

}