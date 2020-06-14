<?php
namespace App\GraphQL\Mutation\UserGroup;

use GraphQL;
use App\Tenant\User;
use App\Tenant\UserGroup;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Mutation;
use Illuminate\Support\Facades\Validator;

class AddUsersToUserGroupMutation extends Mutation {

    protected $attributes = [
        'name' => 'AddUsersToUserGroupMutation',
        'description' => 'Add users to user group.'
    ];

	public function type(): \GraphQL\Type\Definition\Type
	{
        return Type::nonNull(GraphQL::type('UserGroup'));
	}

	public function args(): array
	{
		return [
            'groupId' => [
				'name' => 'groupId',
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

        if (empty($args['groupId'])) {
            throw new \GraphQL\Error\Error('User group ID is required.');
        }

        if (empty($args['users'])) {
            throw new \GraphQL\Error\Error('User IDs is required.');
        }

        $group = UserGroup::find($args['groupId']);

        if (!$group) {
            throw new \GraphQL\Error\Error('Group does not exist.');
        }

        if ($group->is_role == 1) {
            throw new \GraphQL\Error\Error('Group does not exist.');
        }

        foreach ($args['users'] AS $userId) {

            $user = User::find($userId);
            
            if (!$user) {
                //throw new \GraphQL\Error\Error('User does not exist.');
                continue;
            }

            /*
            If already has connection
            */
            if ($user->groups->contains($args['groupId'])) {
                continue;
            }

            /*
            Add user group to user
            */
            $user->groups()->attach($args['groupId']);
            $user->save();

        }

		return $group;
	}


}
