<?php
namespace App\GraphQL\Type;

use App\GraphQL\TypeRegistry;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Type as GraphQLType;

class ProjectPaginationType extends GraphQLType {

    protected $attributes = [
        'name'          => 'ProjectPagination',
        'description'   => 'Project pagination model',
        'model'         => \App\Tenant\Project::class,
    ];

    public function fields(): array
    {
        return [
            'count' => [
                'type'        => Type::nonNull(Type::Int()),
                'description' => 'Total project count',
            ],            
            'items' => [
                'type' => Type::nonNull(Type::listOf(Type::nonNull(GraphQL::type('Project')))),
                'description' => 'List of projects'
            ]
        ];
    }

}