<?php
/*namespace App\GraphQL\Mutation\Permission;

use GraphQL;
use App\Tenant\Permission;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Mutation;
use Illuminate\Support\Facades\Validator;

class AddPermissionMutation extends Mutation {

    protected $attributes = [
        'name' => 'AddPermission',
        'description' => 'Add permission to group or user.'
    ];

	public function type(): \GraphQL\Type\Definition\Type
	{
        return Type::nonNull(Type::boolean());
	}

	public function args(): array
	{
		return [
            'type' => [
				'name' => 'type',
				'type' => Type::nonNull(Type::string())
			],
            'modelId' => [
				'name' => 'modelId',
				'type' => Type::nonNull(Type::int())
			],
            'permissionId' => [
				'name' => 'permissionId',
				'type' => Type::nonNull(Type::int())
			]
        ];
	}

	public function resolve($root, $args)
	{
        if (empty($args['type'])) {
            throw new \GraphQL\Error\Error('Type is required.');
        }

        if (empty($args['modelId'])) {
            throw new \GraphQL\Error\Error('Model ID is required.');
        }

        if (empty($args['permissionId'])) {
            throw new \GraphQL\Error\Error('Permission ID is required.');
        }

        if (($args['type'] == 'Group') || ($args['type'] == 'Role')) {

            $group = \App\Tenant\UserGroup::find($args['modelId']);
            $group->permissions()->attach($args['permissionId']);

            return true;

        } else if ($args['type'] == 'User') {

            $user = \App\Tenant\User::find($args['modelId']);
            $user->permissions()->attach($args['permissionId']);

            return true;

        }

		return false;
	}


}*/
