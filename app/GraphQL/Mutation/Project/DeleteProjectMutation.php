<?php
namespace App\GraphQL\Mutation\Project;

use GraphQL;
use App\Tenant\User;
use App\Tenant\Project;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Mutation;

class DeleteProjectMutation extends Mutation {

    protected $attributes = [
        'name' => 'DeleteProject',
        'description' => 'Delete project.'
    ];

	public function type(): \GraphQL\Type\Definition\Type
	{
        return Type::nonNull(Type::int());
	}
    
	public function args(): array
	{
		return [
            'projectId' => [
				'name' => 'projectId',
				'type' => Type::nonNull(Type::int())
            ],
            'deleteTasks' => [
				'name' => 'deleteTasks',
				'type' => Type::boolean()
            ]
        ];
	}

	public function resolve($root, $args)
	{   
        if (empty($args['projectId'])) {
            throw new \GraphQL\Error\Error('Project ID is required.');
        }

        // najprv pozrieme ci ma basic pravo na delete
        User::checkAction('Project', 'delete');

        $project = Project::find($args['projectId']);

        if (!$project) {
            throw new \GraphQL\Error\Error('Project not existed.');
        }

        // ak ma global pravo tak preskocime kontrolu asociacie usera na projekt
        if (!(User::checkAction('Project', 'delete', 'global', false))) {

            $user = auth()->user();
            
            // skontrolujeme ci ma asociaciu na projekt (ci je owner alebo manager)
            if (!$project->userHasAssociation($user->id)) {
                throw new \GraphQL\Error\Error('User dont have any association with this project.');
            }

        }

        $deleteTasks = (!empty($args['deleteTasks'])) ? $args['deleteTasks'] : false;

        if ($deleteTasks) {

            foreach ($project->tasks AS $task) {

                $task->deleted = 1;
                $task->save();

            }

        }
        
        $project->deleted = 1;
        $project->save();
        
        return $args['projectId'];
	}

}
