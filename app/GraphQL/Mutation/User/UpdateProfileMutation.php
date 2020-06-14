<?php
namespace App\GraphQL\Mutation\User;

use GraphQL;
use App\Tenant\User;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Mutation;
use Illuminate\Support\Facades\Validator;

class UpdateProfileMutation extends Mutation {

    protected $attributes = [
        'name' => 'UpdateProfile',
        'description' => 'If given user ID update profile for that user else update profile for logged user.'
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
				'type' => Type::nonNull(Type::string())
			],
            'lastName' => [
				'name' => 'lastName',
				'type' => Type::nonNull(Type::string())
			],
            'title' => [
				'name' => 'title',
				'type' => Type::nonNull(Type::string())
			],
            'phone' => [
				'name' => 'phone',
				'type' => Type::nonNull(Type::string())
            ],
            'about' => [
				'name' => 'about',
				'type' => Type::nonNull(Type::string())
            ],
            /*'birthday' => [
				'name' => 'birthday',
				'type' => Type::nonNull(Type::string())
            ],*/
            'gender' => [
				'name' => 'gender',
				'type' => Type::nonNull(Type::string())
            ],
            'gdpr' => [
				'name' => 'gdpr',
				'type' => Type::nonNull(Type::boolean())
            ],
            'position' => [
				'name' => 'position',
				'type' => Type::nonNull(Type::string())
            ],
            /*'employedFrom' => [
				'name' => 'employedFrom',
				'type' => Type::nonNull(Type::string())
            ],*/
            'employmentType' => [
				'name' => 'employmentType',
				'type' => Type::nonNull(Type::string())
            ]
        ];
	}

	public function resolve($root, $args)
	{
        if (!(User::checkAction('User', 'update', 'global', false))) { // first check global rights
            User::checkAction('User', 'update');
        }

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
        } 
        
        /*if (empty($args['birthday'])) {
            throw new \GraphQL\Error\Error('Birthday is required.');
        }  */

        if (empty($args['gender'])) {
            throw new \GraphQL\Error\Error('Gender is required.');
        }    

        if (empty($args['gdpr'])) {
            throw new \GraphQL\Error\Error('Gdpr is required.');
        }    

        if (empty($args['position'])) {
            throw new \GraphQL\Error\Error('Position is required.');
        } 
        /*
        if (empty($args['employedFrom'])) {
            throw new \GraphQL\Error\Error('Employed from is required.');
        }*/

        if (empty($args['employmentType'])) {
            throw new \GraphQL\Error\Error('Employment type is required.');
        }      

        if (!empty($args['userId'])) {

            $user = User::find($args['userId']);

        } else {

            $user = auth()->user();

        }

        if (empty($user)) {
            throw new \GraphQL\Error\Error('User is empty.');
        }

        $user->profile->update([
            'firstName'      => $args['firstName'], 
            'lastName'       => $args['lastName'], 
            'title'          => $args['title'], 
            'phone'          => $args['phone'], 
            'about'          => $args['about'], 
            //'birthday'     => $args['birthday'],
            'gender'         => $args['gender'], 
            'gdpr'           => $args['gdpr'], 
            'position'       => $args['position'], 
            //'employedFrom' => $args['employedFrom'], 
            'employmentType' => $args['employmentType'] 
        ]);

		return $user;
	}


}
