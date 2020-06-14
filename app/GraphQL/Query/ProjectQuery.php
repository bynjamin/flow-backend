<?php

namespace App\GraphQL\Query;

use GraphQL;
use App\Tenant\User;
use App\Tenant\Project;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Query;

use Hyn\Tenancy\Environment;

class ProjectQuery extends Query {

    protected $attributes = [
        'name' => 'Project query',
        'description' => 'Get project with given id'
    ];

    public function type(): \GraphQL\Type\Definition\Type
    {
        return GraphQL::type('Project');
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
        if (!(User::checkAction('Project', 'read', 'global', false))) { // first check global rights
            User::checkAction('Project', 'read'); // check basic rights
        }
        
        if (empty($args['id'])) {
            throw new \GraphQL\Error\Error('Project not exists.');
        }

        $project = Project::find($args['id']);

        if (!$project) {
            throw new \GraphQL\Error\Error('Project not exists.');
        }

        $user = auth()->user();

        if (!(User::checkAction('Project', 'read', 'global', false))) {

            if (!(
                ($project->owner_id == $user->id) || // ak je owner
                ($project->managers->contains($user->id)) || // ak je manager
                ($project->isAssignee($user->id)) // ak je asignee
            )) {
                throw new \GraphQL\Error\Error('Cant see this project. You dont have association with this project.');
            }

        }

        return $project;
    }

}
