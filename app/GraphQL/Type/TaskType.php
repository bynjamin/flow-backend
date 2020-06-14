<?php
namespace App\GraphQL\Type;

use App\GraphQL\TypeRegistry;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Type as GraphQLType;

class TaskType extends GraphQLType {

    protected $attributes = [
        'name'          => 'Task',
        'description'   => 'Task model',
        'model'         => \App\Tenant\Task::class,
    ];

    public function fields(): array
    {
        return [   
            'id' => [
                'type'        => Type::nonNull(Type::string()),
                'description' => 'ID of task',
            ],   
            'name' => [
                'type'        => Type::nonNull(Type::string()),
                'description' => 'Name of task',
            ],  
            'description' => [
                'type'        => Type::nonNull(Type::string()),
                'description' => 'Description of task',
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
                'description' => 'Creator of task',
                'resolve' => function($model) {

                    return $model->owner;

                }
            ],
            'deadline' => [
                'type'        => Type::nonNull(Type::string()),
                'description' => 'Deadline of task',
            ], 
            'status' => [
                'type'        => Type::nonNull(GraphQL::type('TaskState')),
                'description' => 'Status of task',
                'resolve' => function($model) {

                    return $model->statusText();

                }
            ], 
            'assignees' => [
                'type'        => Type::nonNull(Type::listOf(Type::nonNull(GraphQL::type('User')))),
                'description' => 'Assignees of task',
                'resolve' => function($model) {

                    return $model->assignees;

                }
            ],
            'project' => [
                'type'        => Type::nonNull(GraphQL::type('Project')),
                'description' => 'Project for this task',
                'resolve' => function($model) {

                    return $model->project;

                }
            ],    
            'collaborators' => [
                'type' => Type::nonNull(GraphQL::type('Collaborators')),
                'description' => 'All users and user groups that has permissions to C,R,U,D',
                'resolve' => function($model) {

                    return $model->getCollaborators();
                    
                },
            ]      
        ];
    }

}