<?php
namespace App\GraphQL\Type;

use App\GraphQL\TypeRegistry;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Type as GraphQLType;

class ProjectType extends GraphQLType {

    protected $attributes = [
        'name'          => 'Project',
        'description'   => 'Project model',
        'model'         => \App\Tenant\Project::class,
    ];

    public function fields(): array
    {
        return [   
            'id' => [
                'type'        => Type::nonNull(Type::string()),
                'description' => 'ID of Project',
            ],   
            'name' => [
                'type'        => Type::nonNull(Type::string()),
                'description' => 'Name of module',
            ],  
            'description' => [
                'type'        => Type::nonNull(Type::string()),
                'description' => 'Description'
            ],      
            'deleted' => [
                'type'        => Type::nonNull(Type::boolean()),
                'description' => 'Deleted',
                'resolve' => function($model) {

                    return ($model->deleted == 1) ? true : false;

                }
            ],     
            'createdBy' => [
                'type'        => Type::nonNull(GraphQL::type('User')),
                'description' => 'Creator of project',
                'resolve' => function($model) {

                    return $model->owner;

                },
            ],          
            'managers' => [
                'type'        => Type::nonNull(Type::listOf(Type::nonNull(GraphQL::type('User')))),
                'description' => 'Managers of project',
                'resolve' => function($model) {

                    return $model->managers;

                },
            ],          
            'tasks' => [
                'type'        => Type::nonNull(Type::listOf(Type::nonNull(GraphQL::type('Task')))),
                'description' => 'Tasks of project',
                'resolve' => function($model) {

                    return $model->tasks()->where('deleted', 0)->get();

                },
            ],          
            'assignees' => [
                'type'        => Type::nonNull(Type::listOf(Type::nonNull(GraphQL::type('User')))),
                'description' => 'Tasks of project',
                'resolve' => function($model) {

                    return $model->getAssignees();

                },
            ]
        ];
    }

}