<?php
namespace App\GraphQL\Mutation\Project;

use GraphQL;
use App\Tenant\User;
use App\Tenant\Project;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Mutation;

class CreateProjectMutation extends Mutation {

    protected $attributes = [
        'name' => 'CreateProject',
        'description' => 'Create project.'
    ];

	public function type(): \GraphQL\Type\Definition\Type
	{
        return Type::nonNull(GraphQL::type('Project'));
	}

	public function args(): array
	{
		return [
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
        if (!(User::checkAction('Project', 'create', 'global', false))) { // first check global rights
            User::checkAction('Project', 'create'); // check basic rights
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

		return Project::createProject($args['name'], $args['description'], $args['managersId']);
	}


}
