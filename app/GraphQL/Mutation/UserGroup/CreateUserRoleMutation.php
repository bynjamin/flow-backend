<?php
namespace App\GraphQL\Mutation\UserGroup;

use GraphQL;
use App\Tenant\UserGroup;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Mutation;
use Illuminate\Support\Facades\Validator;

class CreateUserRoleMutation extends Mutation {

    protected $attributes = [
        'name' => 'NewRole',
        'description' => 'Create new user role.'
    ];

	public function type(): \GraphQL\Type\Definition\Type
	{
        return Type::nonNull(GraphQL::type('UserRole'));
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
			]
        ];
	}

	public function resolve($root, $args)
	{
		if (!(User::checkAction('UserGroup', 'create', 'global', false))) { // first check global rights
            User::checkAction('UserGroup', 'create');
		}
		
        if (empty($args['name'])) {
            throw new \GraphQL\Error\Error('Name is required.');
        }

        if (empty($args['description'])) {
            throw new \GraphQL\Error\Error('Description is required.');
        }

		return UserGroup::createNewRole($args['name'], $args['description']);
	}


}
