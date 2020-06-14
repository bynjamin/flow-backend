<?php
namespace App\GraphQL\Mutation\UserGroup;

use GraphQL;
use App\Tenant\User;
use App\Tenant\UserGroup;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Mutation;
use Illuminate\Support\Facades\Validator;

class UpdateUserGroupMutation extends Mutation {

    protected $attributes = [
        'name' => 'UpdateUserGroup',
        'description' => 'Update user group.'
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
            'name' => [
				'name' => 'name',
				'type' => Type::nonNull(Type::string())
			],
            'description' => [
				'name' => 'description',
				'type' => Type::nonNull(Type::string())
			],
			'memberIds' => [
				'name' => 'memberIds',
				'type' => Type::listOf(Type::nonNull(Type::int()))
			]
        ];
	}

	public function resolve($root, $args)
	{
        if (!(User::checkAction('UserGroup', 'update', 'global', false))) { // first check global rights
            User::checkAction('UserGroup', 'update');
        }
        
        if (empty($args['groupId'])) {
            throw new \GraphQL\Error\Error('Group ID is required.');
        }

        if (empty($args['name'])) {
            throw new \GraphQL\Error\Error('Name is required.');
        }

        if (empty($args['description'])) {
            throw new \GraphQL\Error\Error('Description is required.');
		}

        $group = UserGroup::find($args['groupId']);
        
        if (!$group) {
            throw new \GraphQL\Error\Error('User group not exists.');
        }

        $group->name        = $args['name'];
        $group->description = $args['description'];
        
        $group->users()->detach();
        $group->save();

        foreach ($args['memberIds'] AS $member) {

            $user = User::find($member);

            if ($user) {

                $user->groups()->attach($args['groupId']);
                $user->save();

            }

        }

		return $group;
	}


}
