<?php
namespace App\GraphQL\Mutation\User;

use GraphQL;
use JWTAuth;
use App\Tenant\User;
use App\Tenant\UserGroup;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Mutation;
use Tymon\JWTAuth\Facades\JWTFactory;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class InviteUserMutation extends Mutation {

    protected $attributes = [
        'name' => 'InviteUser',
        'description' => 'Invite new user.'
    ];

	public function type(): \GraphQL\Type\Definition\Type
	{
        return Type::boolean();
	}

	public function args(): array
	{
		return [
            'email' => [
				'name' => 'email',
				'type' => Type::nonNull(Type::string())
			],
            'firstName' => [
				'name' => 'firstName',
				'type' => Type::nonNull(Type::string())
			],
            'lastName' => [
				'name' => 'lastName',
				'type' => Type::nonNull(Type::string())
			],
            'title' => [
				'name' => 'title',
				'type' => Type::string()
			],
            'phone' => [
				'name' => 'phone',
				'type' => Type::string()
            ],
            'gender' => [
				'name' => 'gender',
				'type' => Type::string()
            ],
            'position' => [
				'name' => 'position',
				'type' => Type::string()
            ],
            'employmentType' => [
				'name' => 'employmentType',
				'type' => Type::string()
            ],
            'street' => [
				'name' => 'street',
				'type' => Type::string()
			],
            'city' => [
				'name' => 'city',
				'type' => Type::string()
			],
            'zip' => [
				'name' => 'zip',
				'type' => Type::string()
			],
            'country' => [
				'name' => 'country',
				'type' => Type::string()
            ],
            'roleId' => [
                'name' => 'roleId',
                'type' => Type::nonNull(Type::int())
            ]	
        ];
	}

	public function resolve($root, $args)
	{
        if (!(User::checkAction('User', 'create', 'global', false))) { // first check global rights
            User::checkAction('User', 'create');
        }

        if (empty($args['email'])) {
            throw new \GraphQL\Error\Error('Email is required.');
        }

        if (empty($args['firstName'])) {
            throw new \GraphQL\Error\Error('First name is required.');
        }

        if (empty($args['lastName'])) {
            throw new \GraphQL\Error\Error('Last name is required.');
        }

        if (empty($args['roleId'])) {
            throw new \GraphQL\Error\Error('Role ID is required.');
        }

        $role = UserGroup::find($args['roleId']);

        if (!$role->isRole()) {
            throw new \GraphQL\Error\Error('Role ID is not for role.');
        }

        $dataProfile = [
            'firstName'      => $args['firstName'],
            'lastName'       => $args['lastName'],
            'title'          => (!empty($args['title'])) ? $args['title'] : '',
            'phone'          => (!empty($args['phone'])) ? $args['phone'] : '',       
            'gender'         => (!empty($args['gender'])) ? $args['gender'] : '',     
            'position'       => (!empty($args['position'])) ? $args['position'] : '', 
            'employmentType' => (!empty($args['employmentType'])) ? $args['employmentType'] : ''
        ];

        $dataAddress = [
            'street'  => (!empty($args['street'])) ? $args['street'] : '',
            'city'    => (!empty($args['city'])) ? $args['city'] : '',    
            'zip'     => (!empty($args['zip'])) ? $args['zip'] : '',    
            'country' => (!empty($args['country'])) ? $args['country'] : ''
        ];

        $ret  = [];
        $fqdn = User::getFQDN();

        $mail = $args['email'];
        $user = User::where('email', $mail)->first();

        if (!$user) {
            
            $password = randomPassword(10);
            $newUser  = User::createUserWithData($mail, $password, $dataProfile, $dataAddress, false);

            $newUser->updateRole($role->id);

            $credentials = [
                'email'    => $mail, 
                'password' => $password
            ];

            if (!$token = auth()->claims(['fqdn' => $fqdn])->attempt($credentials)) {
                throw new \GraphQL\Error\Error('Error!');
            }

            User::inviteUser($mail, $password, $token);  

            return true;
        }

		throw new \GraphQL\Error\Error('User was already created!');

	}


}
