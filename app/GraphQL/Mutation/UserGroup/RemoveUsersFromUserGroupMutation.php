<?php
namespace App\GraphQL\Mutation\UserGroup;

use GraphQL;
use App\Tenant\User;
use App\Tenant\UserGroup;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Mutation;
use Illuminate\Support\Facades\Validator;

class RemoveUsersFromUserGroupMutation extends Mutation {

    protected $attributes = [
        'name' => 'RemoveUsersFromUserGroup',
        'description' => 'Remove users from user group.'
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
            'userIds' => [
				'name' => 'userIds',
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
            throw new \GraphQL\Error\Error('Group ID is required.');
        }

        $group = UserGroup::find($args['groupId']);

        if (!$group) {
            throw new \GraphQL\Error\Error('Group does not exist.');
        }

        if (empty($args['userIds'])) {
            throw new \GraphQL\Error\Error('User IDs is required.');
        }

        foreach ($args['userIds'] AS $userId) {

            $user = User::find($userId);

            if ($user) {
                
                $user->groups()->detach($group->id);
                $user->save();

            }
        }

		return $group;
	}


}
