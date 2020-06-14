<?php
namespace App\GraphQL\Mutation\User;

use GraphQL;
use App\Tenant\User;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Mutation;
use Illuminate\Support\Facades\Validator;

class UpdatePasswordMutation extends Mutation {

    protected $attributes = [
        'name' => 'UpdatePassword',
        'description' => 'Update user password.'
    ];

	public function type(): \GraphQL\Type\Definition\Type
	{
        return Type::nonNull(GraphQL::type('User'));
	}

	public function args(): array
	{
		return [
            'oldPassword' => [
				'name' => 'oldPassword',
				'type' => Type::nonNull(Type::string())
			],
            'password' => [
				'name' => 'password',
				'type' => Type::nonNull(Type::string())
			],
            'passwordConfirm' => [
				'name' => 'passwordConfirm',
				'type' => Type::nonNull(Type::string())
			]           
        ];
	}

	public function resolve($root, $args)
	{
        if (empty($args['oldPassword'])) {
            throw new \GraphQL\Error\Error('Old password is required.');
        }

        if (empty($args['password'])) {
            throw new \GraphQL\Error\Error('Password is required.');
        }

        if (empty($args['passwordConfirm'])) {
            throw new \GraphQL\Error\Error('Password confirmation is required.');
        }

        if (strlen(trim($args['password'])) < MINIMAL_PASSWORD_LENGTH) {
            throw new \GraphQL\Error\Error('Password minimal length character is ' . MINIMAL_PASSWORD_LENGTH . '.');
        }

        if ($args['password'] != $args['passwordConfirm']) {
            throw new \GraphQL\Error\Error('Passwords must be same.');
        }

        $user = auth()->user();

        if (!password_verify($args['oldPassword'], $user->password)) {
            throw new \GraphQL\Error\Error('Old passwords is different.');
        }

        if (empty($user)) {
            throw new \GraphQL\Error\Error('User is empty.');
        }

		return $user->updatePassword($args['password']);
	}


}
