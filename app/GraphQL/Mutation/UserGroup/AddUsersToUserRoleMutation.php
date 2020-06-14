<?php
namespace App\GraphQL\Mutation\UserGroup;

use GraphQL;
use App\Tenant\User;
use App\Tenant\UserGroup;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Mutation;
use Illuminate\Support\Facades\Validator;

class AddUsersToUserRoleMutation extends Mutation {

    protected $attributes = [
        'name' => 'AddUsersToUserRoleMutation',
        'description' => 'Add users to user role.'
    ];

	public function type(): \GraphQL\Type\Definition\Type
	{
        return Type::nonNull(GraphQL::type('UserRole'));
	}

	public function args(): array
	{
		return [
            'roleId' => [
				'name' => 'roleId',
				'type' => Type::nonNull(Type::int())
            ],
            'users' => [
				'name' => 'users',
				'type' => Type::nonNull(Type::listOf(Type::nonNull(Type::int())))
			]
        ];
	}

	public function resolve($root, $args)
	{
        if (!(User::checkAction('User', 'update', 'global', false))) { // first check global rights
            User::checkAction('User', 'update');
        }

        if (!(User::checkAction('UserGroup', 'update', 'global', false))) { // first check global rights
            User::checkAction('UserGroup', 'update');
        }

        if (empty($args['roleId'])) {
            throw new \GraphQL\Error\Error('User role ID is required.');
        }

        if (empty($args['users'])) {
            throw new \GraphQL\Error\Error('User ID is required.');
        }

        $role = UserGroup::find($args['roleId']);

        if (!$role) {
            throw new \GraphQL\Error\Error('User role does not exist.');
        }

        if ($role->is_role == 0) {
            throw new \GraphQL\Error\Error('User role does not exist.');
        }

        foreach ($args['users'] AS $userId) {

            $user = User::find($userId);
            
            if (!$user) {
                //throw new \GraphQL\Error\Error('User does not exist.');
                continue;
            }

            $roleId = $user->role[0]->id;
            $user->role()->detach($roleId);
            $user->role()->attach($args['roleId']);
            $user->save();  
        }

		return $role;
	}


}
