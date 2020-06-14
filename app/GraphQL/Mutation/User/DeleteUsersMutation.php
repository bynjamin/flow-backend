<?php
namespace App\GraphQL\Mutation\User;

use GraphQL;
use App\Tenant\User;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Mutation;
use Illuminate\Support\Facades\Validator;

class DeleteUsersMutation extends Mutation {

    protected $attributes = [
        'name' => 'DeleteUsers',
        'description' => 'Delete users.'
    ];

	public function type(): \GraphQL\Type\Definition\Type
	{
        return Type::nonNull(Type::listOf(Type::nonNull(Type::int())));
	}
    
	public function args(): array
	{
		return [
            'userIds' => [
				'name' => 'userIds',
				'type' => Type::nonNull(Type::listOf(Type::nonNull(Type::int())))
			],
        ];
	}

	public function resolve($root, $args)
	{        
        if (!(User::checkAction('User', 'delete', 'global', false))) { // first check global rights
            User::checkAction('User', 'delete');
        }

        if (empty($args['userIds'])) {
            throw new \GraphQL\Error\Error('User IDs is required.');
        }

        foreach ($args['userIds'] AS $u_id) {

            //User::checkAction('User', 'delete', $u_id);
    
            if ($u_id == auth()->user()->id) {
                throw new \GraphQL\Error\Error('User can\'t delete himself.');
            }

            $user = User::find($u_id);

            if (!$user) {
                throw new \GraphQL\Error\Error('User not existed.');
            }

        }

        foreach ($args['userIds'] AS $u_id) {
  
            $user = User::find($u_id);
    
            $user->deleted = 1;
            $user->save();

            $ret[] = $user->id;

        }
        
        return $ret;
	}

}
