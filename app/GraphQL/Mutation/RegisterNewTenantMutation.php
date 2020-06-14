<?php
namespace App\GraphQL\Mutation;

use GraphQL;
use App\Tenant;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Mutation;
use Illuminate\Support\Facades\Validator;

class RegisterNewTenantMutation extends Mutation {

    protected $attributes = [
        'name' => 'NewRegistration',
        'description' => 'Create new registration and send activation mail if success.'
    ];

	public function type(): \GraphQL\Type\Definition\Type
	{
        return Type::nonNull(GraphQL::type('Tenant'));
	}

	public function args(): array
	{
		return [
            'siteAddress' => [
				'name' => 'siteAddress',
				'type' => Type::nonNull(Type::string())
			],
			'company' => [
				'name' => 'company',
				'type' => Type::nonNull(Type::string())
			],
			'fullName' => [
				'name' => 'fullName',
				'type' => Type::nonNull(Type::string())
			],
            'mail' => [
				'name' => 'mail',
				'type' => Type::nonNull(Type::string())
			],
            'password' => [
				'name' => 'password',
				'type' => Type::nonNull(Type::string())
			],
            'passwordConfirm' => [
				'name' => 'passwordConfirm',
				'type' => Type::nonNull(Type::string())
			],
            'captcha' => [
				'name' => 'captcha',
				'type' => Type::nonNull(Type::string())
			],
        ];
	}

	public function resolve($root, $args)
	{

        if (empty($args['siteAddress'])) {
            throw new \GraphQL\Error\Error('Site address is required.');
        }

        if (Tenant::tenantExists($args['siteAddress'])) {
            throw new \GraphQL\Error\Error('This site address is occupated.');
        }

        if (empty($args['company'])) {
            throw new \GraphQL\Error\Error('Company name is required.');
        }

        if (empty($args['fullName'])) {
            throw new \GraphQL\Error\Error('Your full name is required.');
        }

        if (empty($args['mail'])) {
            throw new \GraphQL\Error\Error('Email is required.');
        }

        $validator = Validator::make($args, [
           'mail' => 'email:rfc,dns',
        ]);

        if ($validator->fails()) {
            throw new \GraphQL\Error\Error('Email is not in valid format.');
        }

        if (empty($args['password'])) {
            throw new \GraphQL\Error\Error('Password is required.');
        }

        if (strlen(trim($args['password'])) < 8) {
            throw new \GraphQL\Error\Error('Password minimal length character is 8.');
        }

        if ((trim($args['password'])) != (trim($args['passwordConfirm']))) {
            throw new \GraphQL\Error\Error('You need to confirm your password.');
        }

		return Tenant::create($args['siteAddress'], $args['mail'], $args['mail'], $args['password']);

	}


}
