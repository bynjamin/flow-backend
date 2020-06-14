<?php
namespace App\GraphQL\Mutation\User;

use GraphQL;
use App\Tenant\User;
use App\Tenant\InvitedUser;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Mutation;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class CheckInvitedUserMutation extends Mutation {

    protected $attributes = [
        'name' => 'CheckInvitedUser',
        'description' => 'Check invited user.'
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
            'token' => [
				'name' => 'token',
				'type' => Type::nonNull(Type::string())
			]			
        ];
	}

	public function resolve($root, $args)
	{

        if (empty($args['email'])) {
            throw new \GraphQL\Error\Error('Email is required.');
        }

        if (empty($args['token'])) {
            throw new \GraphQL\Error\Error('Token is required.');
        }

        $was_invited = InvitedUser::where('email', $args['email'])->where('inviteToken', $args['token'])->first();

        if (!$was_invited) {
            throw new \GraphQL\Error\Error('Error! Was not invited.');
        }

        $was_invited->delete();

        $credentials = [
            'email'    => $args['email'], 
            'password' => 'dankojekral' . time()
        ];

        User::createUser($credentials['email'], $credentials['password'], true);

        $fqdn = User::getFQDN();

        if (!$token = auth()->claims(['fqdn' => $fqdn])->attempt($credentials)) {
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
