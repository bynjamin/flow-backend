<?php
namespace App\GraphQL\Mutation;

use GraphQL;
use App\Tenant\User;
use App\Tenant\Permission;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Mutation;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class LoginMutation extends Mutation {

    protected $attributes = [
        'name' => 'Login',
        'description' => 'Login user'
    ];

	public function type(): \GraphQL\Type\Definition\Type
	{
        return Type::nonNull(GraphQL::type('Login'));
	}

	public function args(): array
	{
		return [           
            'email' => [
				'name' => 'email',
				'type' => Type::nonNull(Type::string())
			],
            'password' => [
				'name' => 'password',
				'type' => Type::nonNull(Type::string())
			]
        ];
	}

	public function resolve($root, $args)
	{
        if (empty($args['email'])) {
            throw new \GraphQL\Error\Error('Email is required.');
        }

        if (empty($args['password'])) {
            throw new \GraphQL\Error\Error('Password is required.');
        }

        $credentials = [
            'email'    => $args['email'], 
            'password' => $args['password']
        ];

        $fqdn = User::getFQDN();

        if (!$token = auth()->claims(['fqdn' => $fqdn])->attempt($credentials)) {
            throw new \GraphQL\Error\Error('Unauthorized!');
        }

        if (!auth()->user()->isActive) {
            auth()->logout();
            
            throw new \GraphQL\Error\Error('Unauthorized!');            
        }

        return [
            'accessToken'  => $token,
            'tokenType'    => 'bearer',
            'expiresIn'    => auth()->factory()->getTTL() * 30,//60,
            'fqdn'         => $fqdn,
            'user'         => auth()->user()
        ];    
	}


}
