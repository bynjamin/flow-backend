<?php
namespace App\GraphQL\Mutation\Permission;

use GraphQL;
use App\Tenant\User;
use App\Tenant\UserGroup;
use App\Tenant\Permission;
use App\Tenant\ModuleAccess;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Mutation;
use Illuminate\Support\Facades\Validator;

class UpdateUserGroupPermissionsMutation extends Mutation {

    protected $attributes = [
        'name' => 'UpdateUserGroupPermissions',
        'description' => 'Update user group permissions/access.'
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
            'model' => [
				'name' => 'model',
				'type' => Type::nonNull(Type::string())
			],
            'permissions' => [
				'name' => 'permissions',
				'type' => Type::nonNull(GraphQL::type('AccessInput'))
			]
        ];
	}

	public function resolve($root, $args)
	{
        if (!(User::checkAction('Permission', 'update', 'global', false))) { // first check global rights
            User::checkAction('Permission', 'update'); // check basic rights
        }

        if (!(User::checkAction('UserGroup', 'update', 'global', false))) { // first check global rights
            User::checkAction('UserGroup', 'update'); // check basic rights
        }

        if (empty($args['groupId'])) {
            throw new \GraphQL\Error\Error('User group ID is required.');
        }

        if (empty($args['model'])) {
            throw new \GraphQL\Error\Error('Model is required.');
        }

        if (empty($args['permissions'])) {
            throw new \GraphQL\Error\Error('Permissions is required.');
        }

        $group = UserGroup::find($args['groupId']);

        if (!$group) {
            throw new \GraphQL\Error\Error('User group not found.');
        }

        $basicAccess  = $args['permissions']['basic'];
        $globalAccess = $args['permissions']['global'];
        
        foreach ($basicAccess AS $actionName => $actionValue) {
            
            $moduleAccess = ModuleAccess::where('module', $args['model'])->where('action', $actionName)->first();

            // add access, first check if has access
            if ($actionValue) { 

                // ak nema dane pravo tak pridame
                if (!$group->modules->contains($moduleAccess->id)) {
                    $group->modules()->attach($moduleAccess->id);
                }

            } else {

                // zrusime access
                $group->modules()->detach($moduleAccess->id);

            }

        }

        foreach ($globalAccess AS $actionName => $actionValue) {
            
            $perm = Permission::where('model', $args['model'])->where('action', $actionName)->where('model_id', 0)->first();

            // add access, first check if has access
            if ($actionValue) { 

                // ak nema dane pravo tak pridame
                if (!$group->permissions->contains($perm->id)) {
                    $group->permissions()->attach($perm->id);
                }

            } else {

                // zrusime access
                $group->permissions()->detach($perm->id);

            }

        }

		return $group;
	}

}