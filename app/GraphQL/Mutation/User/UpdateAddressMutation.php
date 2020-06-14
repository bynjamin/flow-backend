<?php
namespace App\GraphQL\Mutation\User;

use GraphQL;
use App\Tenant\User;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Mutation;
use Illuminate\Support\Facades\Validator;

class UpdateAddressMutation extends Mutation {

    protected $attributes = [
        'name' => 'UpdateAddress',
        'description' => 'If given user ID update address for that user else update address for logged user.'
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
			]
        ];
	}

	public function resolve($root, $args)
	{
        if (!(User::checkAction('User', 'update', 'global', false))) { // first check global rights
            User::checkAction('User', 'update');
        }

        $data = [];

        if (empty($args['street'])) {
            throw new \GraphQL\Error\Error('Street is required.');
        } else {
            $data['street'] = $args['street']; 
        }

        if (empty($args['city'])) {
            throw new \GraphQL\Error\Error('City is required.');
        } else {
            $data['city'] = $args['city']; 
        }

        if (empty($args['zip'])) {
            throw new \GraphQL\Error\Error('Zip is required.');
        } else {
            $data['zip'] = $args['zip']; 
        }

        if (empty($args['country'])) {
            throw new \GraphQL\Error\Error('Country is required.');
        }    else {
            $data['country'] = $args['country']; 
        }     

        if (!empty($args['userId'])) {

            $user = User::find($args['userId']);

        } else {

            $user = auth()->user();

        }

        if (empty($user)) {
            throw new \GraphQL\Error\Error('User is empty.');
        }

        $user->profile->address->update($data);

		return $user;

	}


}
