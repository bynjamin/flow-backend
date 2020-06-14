<?php
namespace App\GraphQL\Mutation\Task;

use GraphQL;
use App\Tenant\User;
use App\Tenant\Task;
use App\Tenant\Project;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Mutation;

class UpdateTaskMutation extends Mutation {

    protected $attributes = [
        'name' => 'UpdateTask',
        'description' => 'Update task.'
    ];

	public function type(): \GraphQL\Type\Definition\Type
	{
        return Type::nonNull(GraphQL::type('Task'));
	}

	public function args(): array
	{
		return [
            'taskId' => [
				'name' => 'taskId',
				'type' => Type::nonNull(Type::int())
			],
            'name' => [
				'name' => 'name',
				'type' => Type::nonNull(Type::string())
			],
            'description' => [
				'name' => 'description',
				'type' => Type::nonNull(Type::string())
			],
            'deadline' => [
				'name' => 'deadline',
				'type' => Type::nonNull(Type::string())
            ],
            'status' => [
				'name' => 'status',
				'type' => Type::nonNull(GraphQL::type('TaskState'))
            ],
            'assigneeIds' => [
				'name' => 'assigneeIds',
				'type' => Type::nonNull(Type::listOf(Type::nonNull(Type::int())))
			],
            'projectId' => [
				'name' => 'projectId',
				'type' => Type::nonNull(Type::int())
			]            
        ];
	}

	public function resolve($root, $args)
	{
        User::checkAction('Task', 'update');

        if (empty($args['taskId'])) {
            throw new \GraphQL\Error\Error('Task ID is required.');
        }

        $task = Task::find($args['taskId']);

        if (!$task) {
            throw new \GraphQL\Error\Error('Task not exists.');
        }

        if (empty($args['name'])) {
            throw new \GraphQL\Error\Error('Name is required.');
        }

        if (empty($args['description'])) {
            throw new \GraphQL\Error\Error('Description is required.');
        }

        if (empty($args['deadline'])) {
            throw new \GraphQL\Error\Error('Deadline is required.');
        }

        if (empty($args['status'])) {
            throw new \GraphQL\Error\Error('Status is required.');
        }

        if (empty($args['assigneeIds'])) {
            throw new \GraphQL\Error\Error('Assignee Ids is required.');
        }

        if (empty($args['projectId'])) {
            throw new \GraphQL\Error\Error('Project Id is required.');
        }

        $project = Project::find($args['projectId']);

        if (!$project) {
            throw new \GraphQL\Error\Error('Project not exists.');
        }

        if (!(User::checkAction('Task', 'update', 'global', false))) { // first check global rights

            $user = auth()->user();

            // skontrolujeme ci ma asociaciu na projekt (ci je owner alebo manager)
            if (
                (!$project->userHasAssociation($user->id, true)) &&
                ($task->owner_id != auth()->user()->id)
            ) {
                throw new \GraphQL\Error\Error('User dont have any association with project.');
            }

        }

		return Task::updateTask(
            $args['taskId'], 
            $args['name'], 
            $args['description'], 
            $args['deadline'], 
            $args['status'], 
            $args['assigneeIds'], 
            $args['projectId']
        );
	}


}
