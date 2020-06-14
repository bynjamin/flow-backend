<?php
namespace App\GraphQL\Mutation;

use GraphQL;
use App\Tenant\User;
use App\Tenant\Permission;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Mutation;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class RefreshLoginMutation extends Mutation {

    protected $attributes = [
        'name' => 'RefreshLogin',
        'description' => 'Refresh login'
    ];

	public function type(): \GraphQL\Type\Definition\Type
	{
        return Type::nonNull(GraphQL::type('Login'));
	}

	public function args(): array
	{
		return [];
	}

	public function resolve($root, $args)
	{
        if (!auth()->user()) {
            throw new \GraphQL\Error\Error('No user logged!');
        }
       
        return [
            'statusCode'   => 200,
            'statusText'   => 'Ok',

            'accessToken'  => auth()->refresh(),
            'tokenType'    => 'bearer',
            'expiresIn'    => auth()->factory()->getTTL() * 5,//60,
            'fqdn'         => User::getFQDN(),
            'user'         => auth()->user()
        ];    
	}


}
