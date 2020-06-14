<?php

namespace App\GraphQL\Query;

use GraphQL;
use App\Tenant\Task;
use App\Tenant\User;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Query;

use Hyn\Tenancy\Environment;

class TaskQuery extends Query {

    protected $attributes = [
        'name' => 'Task query',
        'description' => 'Get task with given id'
    ];

    public function type(): \GraphQL\Type\Definition\Type
    {
        return GraphQL::type('Task');
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
        if (!(User::checkAction('Task', 'read', 'global', false))) { // first check global rights
            User::checkAction('Task', 'read'); // check basic rights
        }

        if (empty($args['id'])) {
            throw new \GraphQL\Error\Error('Task not exists.');
        }

        $task = Task::find($args['id']);

        if (!$task) {
            throw new \GraphQL\Error\Error('Task not exists.');
        }

        $user = auth()->user();

        if (!(User::checkAction('Task', 'read', 'global', false))) { // first check global rights

            // check if user have some association with task            
            if (!(
                ($task->project->owner_id == $user->id) || // ak je owner projektu
                ($task->project->managers->contains($user->id)) || // ak je manager projektu
                ($task->assignees->contains($user->id)) || // ak je asignee
                ($task->owner_id == $user->id)
            )) {
                throw new \GraphQL\Error\Error('Cant see this task. You dont have association with this task.');
            }

        }

        return $task;
    }

}
