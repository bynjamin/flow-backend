<?php
namespace App\GraphQL\Mutation\User;

use GraphQL;
use App\Tenant\User;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Mutation;
use Illuminate\Support\Facades\Validator;

class CreateUserMutation extends Mutation {

    protected $attributes = [
        'name' => 'NewUser',
        'description' => 'Create new user and create user profile.'
    ];

	public function type(): \GraphQL\Type\Definition\Type
	{
        return Type::nonNull(GraphQL::type('User'));
	}

	public function args(): array
	{
		return [
            'mail' => [
				'name' => 'mail',
				'type' => Type::nonNull(Type::string())
			],
            'password' => [
				'name' => 'password',
				'type' => Type::nonNull(Type::string())
			],
            'activate' => [
				'name' => 'activate',
				'type' => Type::boolean()
			]
        ];
	}

	public function resolve($root, $args)
	{
        if (!(User::checkAction('User', 'create', 'global', false))) { // first check global rights
            User::checkAction('User', 'create');
        }

        if (empty($args['mail'])) {
            throw new \GraphQL\Error\Error('Email is required.');
        }

        $validator = Validator::make($args, [
           'mail' => 'email:rfc,dns',
        ]);

        if ($validator->fails()) {
            throw new \GraphQL\Error\Error('Email is not in valid format.');
        }

        $findUser = User::findByMail($args['mail']);

        if ($findUser->count()) {
            throw new \GraphQL\Error\Error('Email is already used.');
        }

        if (empty($args['password'])) {
            throw new \GraphQL\Error\Error('Password is required.');
        }

        if (strlen(trim($args['password'])) < MINIMAL_PASSWORD_LENGTH) {
            throw new \GraphQL\Error\Error('Password minimal length character is ' . MINIMAL_PASSWORD_LENGTH . '.');
        }

        $activate = (!empty($args['activate'])) ? $args['activate'] : false;
        
		return User::createUser($args['mail'], $args['password'], $activate);

	}


}
