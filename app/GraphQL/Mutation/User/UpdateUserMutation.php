<?php
namespace App\GraphQL\Mutation\User;

use GraphQL;
use App\Tenant\User;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Mutation;
use Illuminate\Support\Facades\Validator;

class UpdateUserMutation extends Mutation {

    protected $attributes = [
        'name' => 'UpdateUser',
        'description' => 'Update user profile and address.'
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
				'type' => Type::int()
            ],
            'firstName' => [
				'name' => 'firstName',
				'type' => Type::string()
			],
            'lastName' => [
				'name' => 'lastName',
				'type' => Type::string()
			],
            'title' => [
				'name' => 'title',
				'type' => Type::string()
			],
            'phone' => [
				'name' => 'phone',
				'type' => Type::string()
            ],
            'about' => [
				'name' => 'about',
				'type' => Type::string()
            ],
            /*'birthday' => [
				'name' => 'birthday',
				'type' => Type::nonNull(Type::string())
            ],*/
            'gender' => [
				'name' => 'gender',
				'type' => Type::string()
            ],
            'gdpr' => [
				'name' => 'gdpr',
				'type' => Type::boolean()
            ],
            'position' => [
				'name' => 'position',
				'type' => Type::string()
            ],
            /*'employedFrom' => [
				'name' => 'employedFrom',
				'type' => Type::nonNull(Type::string())
            ],*/
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
                'type' => Type::int()
            ]
        ];
	}

	public function resolve($root, $args)
	{
        if (!(User::checkAction('User', 'update', 'global', false))) { // first check global rights
            User::checkAction('User', 'update');
        }

        $dataProfile = [];
        $dataAddress = [];

        /*
        if (empty($args['firstName'])) {
            throw new \GraphQL\Error\Error('First name is required.');
        }

        if (empty($args['lastName'])) {
            throw new \GraphQL\Error\Error('Last name is required.');
        }

        if (empty($args['title'])) {
            throw new \GraphQL\Error\Error('Title is required.');
        }

        if (empty($args['phone'])) {
            throw new \GraphQL\Error\Error('Phone is required.');
        }    

        if (empty($args['about'])) {
            throw new \GraphQL\Error\Error('About is required.');
        } */
        
        /*if (empty($args['birthday'])) {
            throw new \GraphQL\Error\Error('Birthday is required.');
        }  */
/*
        if (empty($args['gender'])) {
            throw new \GraphQL\Error\Error('Gender is required.');
        }    

        if (empty($args['gdpr'])) {
            throw new \GraphQL\Error\Error('Gdpr is required.');
        }    

        if (empty($args['position'])) {
            throw new \GraphQL\Error\Error('Position is required.');
        } */
        /*
        if (empty($args['employedFrom'])) {
            throw new \GraphQL\Error\Error('Employed from is required.');
        }*/

        /*if (empty($args['employmentType'])) {
            throw new \GraphQL\Error\Error('Employment type is required.');
        }   

        if (empty($args['street'])) {
            throw new \GraphQL\Error\Error('Street is required.');
        } else {
            $dataAddress['street'] = $args['street']; 
        }

        if (empty($args['city'])) {
            throw new \GraphQL\Error\Error('City is required.');
        } else {
            $dataAddress['city'] = $args['city']; 
        }

        if (empty($args['zip'])) {
            throw new \GraphQL\Error\Error('Zip is required.');
        } else {
            $dataAddress['zip'] = $args['zip']; 
        }

        if (empty($args['country'])) {
            throw new \GraphQL\Error\Error('Country is required.');
        }    else {
            $dataAddress['country'] = $args['country']; 
        }     */

        if (!empty($args['firstName'])) {
            $dataProfile['firstName'] = $args['firstName']; 
        }

        if (!empty($args['lastName'])) {
            $dataProfile['lastName'] = $args['lastName']; 
        }

        if (!empty($args['title'])) {
            $dataProfile['title'] = $args['title']; 
        }

        if (!empty($args['phone'])) {
            $dataProfile['phone'] = $args['phone']; 
        }    

        if (!empty($args['about'])) {
            $dataProfile['about'] = $args['about']; 
        } 
        
        /*if (!empty($args['birthday'])) {
            $dataProfile['birthday'] = $args['birthday']; 
        }  */

        if (!empty($args['gender'])) {
            $dataProfile['gender'] = $args['gender']; 
        }    

        if (!empty($args['gdpr'])) {
            $dataProfile['gdpr'] = $args['gdpr']; 
        }    

        if (!empty($args['position'])) {
            $dataProfile['position'] = $args['position']; 
        } 
        /*
        if (!empty($args['employedFrom'])) {
            $dataProfile['employedFrom'] = $args['employedFrom']; 
        }*/

        if (!empty($args['employmentType'])) {
            $dataProfile['employmentType'] = $args['employmentType']; 
        }  



        if (!empty($args['street'])) {
            $dataAddress['street'] = $args['street']; 
        }

        if (!empty($args['city'])) {
            $dataAddress['city'] = $args['city']; 
        }

        if (!empty($args['zip'])) {
            $dataAddress['zip'] = $args['zip']; 
        }

        if (!empty($args['country'])) {
            $dataAddress['country'] = $args['country']; 
        }

        if (!empty($args['userId'])) {

            $user = User::find($args['userId']);

        } else {

            $user = auth()->user();

        }

        if (empty($user)) {
            throw new \GraphQL\Error\Error('User is empty.');
        }

        $user->profile->update($dataProfile);

        $user->profile->address->update($dataAddress);

        if (!empty($args['roleId'])) {

            $roleId = $user->role[0]->id;
            $user->role()->detach($roleId);
            $user->role()->attach($args['roleId']);

            $user->save();
            
        }

		return User::find($user->id);

	}


}
