<?php
namespace App\GraphQL\Mutation\Permission;

use GraphQL;
use App\Tenant\User;
use App\Tenant\Permission;
use App\Tenant\ModuleAccess;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Mutation;
use GraphQL\GraphQL as GraphQLGraphQL;
use Illuminate\Support\Facades\Validator;

class UpdateUserPermissionsMutation extends Mutation {

    protected $attributes = [
        'name' => 'UpdateUserPermissions',
        'description' => 'Update user permissions/access.'
    ];

	public function type(): \GraphQL\Type\Definition\Type
	{
        return Type::nonNull(GraphQL::type('User'));
	}

	public function args(): array
	{
		return [
            'userId' => [
				'name' => 'userId',
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

        if (!(User::checkAction('User', 'update', 'global', false))) { // first check global rights
            User::checkAction('User', 'update'); // check basic rights
        }

        if (empty($args['userId'])) {
            throw new \GraphQL\Error\Error('User ID is required.');
        }

        if (empty($args['model'])) {
            throw new \GraphQL\Error\Error('Model is required.');
        }

        if (empty($args['permissions'])) {
            throw new \GraphQL\Error\Error('Permissions is required.');
        }

        $user = User::find($args['userId']);

        if (!$user) {
            throw new \GraphQL\Error\Error('User not found.');
        }

        $basicAccess  = $args['permissions']['basic'];
        $globalAccess = $args['permissions']['global'];
        
        foreach ($basicAccess AS $actionName => $actionValue) {
            
            $moduleAccess = ModuleAccess::where('module', $args['model'])->where('action', $actionName)->first();

            // add access, first check if has access
            if ($actionValue) { 

                // ak nema dane pravo tak pridame
                if (!$user->modules->contains($moduleAccess->id)) {
                    $user->modules()->attach($moduleAccess->id);
                }

            } else {

                // zrusime access
                $user->modules()->detach($moduleAccess->id);

            }

        }

        foreach ($globalAccess AS $actionName => $actionValue) {
            
            $perm = Permission::where('model', $args['model'])->where('action', $actionName)->where('model_id', 0)->first();

            // add access, first check if has access
            if ($actionValue) { 

                // ak nema dane pravo tak pridame
                if (!$user->permissions->contains($perm->id)) {
                    $user->permissions()->attach($perm->id);
                }

            } else {

                // zrusime access
                $user->permissions()->detach($perm->id);

            }

        }

		return $user;
	}


}