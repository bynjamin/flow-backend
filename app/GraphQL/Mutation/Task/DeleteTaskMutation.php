<?php
namespace App\GraphQL\Mutation\Task;

use GraphQL;
use App\Tenant\User;
use App\Tenant\Task;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Mutation;

class DeleteTaskMutation extends Mutation {

    protected $attributes = [
        'name' => 'DeleteTask',
        'description' => 'Delete task.'
    ];

	public function type(): \GraphQL\Type\Definition\Type
	{
        return Type::nonNull(Type::int());
	}
    
	public function args(): array
	{
		return [
            'taskId' => [
				'name' => 'taskId',
				'type' => Type::nonNull(Type::int())
			],
        ];
	}

	public function resolve($root, $args)
	{        
        User::checkAction('Task', 'delete');

        if (empty($args['taskId'])) {
            throw new \GraphQL\Error\Error('Task ID is required.');
        }

        $task = Task::find($args['taskId']);

        if (!$task) {
            throw new \GraphQL\Error\Error('Task not existed.');
        }

        $project = $task->project;

        if (!(User::checkAction('Task', 'delete', 'global', false))) { // first check global rights

            $user = auth()->user();

            // skontrolujeme ci ma asociaciu na projekt (ci je owner alebo manager)
            if (!$project->userHasAssociation($user->id)) {
                throw new \GraphQL\Error\Error('User dont have any association with project.');
            }

        }
        
        $task->deleted = 1;
        $task->save();
        
        return $args['taskId'];
	}

}
