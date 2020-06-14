<?php
namespace App\GraphQL\Mutation\UserGroup;

use GraphQL;
use App\Tenant\User;
use App\Tenant\UserGroup;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Mutation;
use Illuminate\Support\Facades\Validator;

class RemoveUserGroupFromUserMutation extends Mutation {

    protected $attributes = [
        'name' => 'RemoveUserGroupFromUser',
        'description' => 'Remove user group from user.'
    ];

	public function type(): \GraphQL\Type\Definition\Type
	{
        return Type::nonNull(GraphQL::type('User'));
	}

	public function args(): array
	{
		return [
            'groupId' => [
				'name' => 'groupId',
				'type' => Type::nonNull(Type::int())
            ],
            'userId' => [
				'name' => 'userId',
				'type' => Type::nonNull(Type::int())
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
            throw new \GraphQL\Error\Error('Group ID is required.');
        }

        if (empty($args['userId'])) {
            throw new \GraphQL\Error\Error('User ID is required.');
        }

        $user = User::find($args['userId']);
        
        if (!$user) {
            throw new \GraphQL\Error\Error('User does not exist.');
        }

        $group = UserGroup::find($args['groupId']);

        if (!$group) {
            throw new \GraphQL\Error\Error('Group does not exist.');
        }

        if ($group->is_role == 1) {
            throw new \GraphQL\Error\Error('Group does not exist.');
        }

        /*
            Remove user group to user
        */
        $user->groups()->detach($args['groupId']);

		return $user;
	}


}
