<?php
namespace App\GraphQL\Mutation\UserGroup;

use GraphQL;
use App\Tenant\User;
use App\Tenant\UserGroup;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Mutation;
use Illuminate\Support\Facades\Validator;

class DeleteUserGroupsMutation extends Mutation {

    protected $attributes = [
        'name' => 'DeleteGroups',
        'description' => 'Delete user groups.'
    ];

	public function type(): \GraphQL\Type\Definition\Type
	{
        return Type::nonNull(Type::listOf(Type::nonNull(Type::int())));
	}

	public function args(): array
	{
		return [
            'groupIds' => [
				'name' => 'groupIds',
				'type' => Type::nonNull(Type::listOf(Type::nonNull(Type::int())))
			]
        ];
	}

	public function resolve($root, $args)
	{
        if (!(User::checkAction('UserGroup', 'delete', 'global', false))) { // first check global rights
            User::checkAction('UserGroup', 'delete');
        }
        
        $ret = [];
        
        if (empty($args['groupIds'])) {
            throw new \GraphQL\Error\Error('Group IDs is required.');
        }
        
        //User::checkAction('UserGroup', 'delete');
        
        // check all groups if user can delete, check all groups if exists
        foreach ($args['groupIds'] AS $g_id) {
            
            $group = UserGroup::find($g_id);

            if (!$group) {
                throw new \GraphQL\Error\Error('Group with ID ' . $g_id . ' not existed.');
            }

        }

        foreach ($args['groupIds'] AS $g_id) {
  
            /*
                detach user connections and delete item
            */
            UserGroup::deleteItem($g_id);

            $ret[] = $g_id;
            
        }
        
        return $ret;
	}


}
