<?php
namespace App\GraphQL\Mutation;

use GraphQL;
use App\Tenant\User;
use App\Tenant\Permission;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Mutation;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class LogoutMutation extends Mutation {

    protected $attributes = [
        'name' => 'Logout',
        'description' => 'Logout user'
    ];

	public function type(): \GraphQL\Type\Definition\Type
	{
        return Type::nonNull(Type::boolean());
	}

	public function args(): array
	{
		return [];
	}

	public function resolve($root, $args)
	{
        auth()->logout();

        return true;
	}


}
