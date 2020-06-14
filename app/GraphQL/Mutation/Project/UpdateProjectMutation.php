<?php
namespace App\GraphQL\Mutation\Project;

use GraphQL;
use App\Tenant\User;
use App\Tenant\Project;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Mutation;

class UpdateProjectMutation extends Mutation {

    protected $attributes = [
        'name' => 'UpdateProject',
        'description' => 'Update project.'
    ];

	public function type(): \GraphQL\Type\Definition\Type
	{
        return Type::nonNull(GraphQL::type('Project'));
	}

	public function args(): array
	{
		return [
            'projectId' => [
				'name' => 'projectId',
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
            'managersId' => [
				'name' => 'managersId',
				'type' => Type::nonNull(Type::listOf(Type::nonNull(Type::int())))
			]
        ];
	}

	public function resolve($root, $args)
	{
        // najprv pozrieme ci ma basic pravo na update
        User::checkAction('Project', 'update');

        if (empty($args['projectId'])) {
            throw new \GraphQL\Error\Error('Project ID is required.');
        }

        if (empty($args['name'])) {
            throw new \GraphQL\Error\Error('Name is required.');
        }

        if (empty($args['description'])) {
            throw new \GraphQL\Error\Error('Description is required.');
        }

        if (empty($args['managersId'])) {
            throw new \GraphQL\Error\Error('Managers IDs is required.');
        }
        
        $project = Project::find($args['projectId']);

        if (!$project) {
            throw new \GraphQL\Error\Error('Project not exists.');
        }

        // ak ma global pravo tak preskocime kontrolu asociacie usera na projekt
        if (!(User::checkAction('Project', 'update', 'global', false))) {

            $user = auth()->user();
            
            // skontrolujeme ci ma asociaciu na projekt (ci je owner alebo manager)
            if (!$project->userHasAssociation($user->id)) {
                throw new \GraphQL\Error\Error('User dont have any association with this project.');
            }

        }
        
		return Project::updateProject($args['projectId'], $args['name'], $args['description'], $args['managersId']);
	}


}
