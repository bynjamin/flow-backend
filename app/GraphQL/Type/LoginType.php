<?php
namespace App\GraphQL\Type;

use App\GraphQL\TypeRegistry;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Type as GraphQLType;

class LoginType extends GraphQLType {

    protected $attributes = [
        'name'          => 'Login',
        'description'   => 'Login type'
    ];

    public function fields(): array
    {
        return [
            'accessToken' => [
                'type'        => Type::nonNull(Type::string()),
                'description' => 'Token',
            ],
            'tokenType' => [
                'type'        => Type::nonNull(Type::string()),
                'description' => 'Token type',
            ],
            'expiresIn' => [
                'type'        => Type::nonNull(Type::int()),
                'description' => 'Token expires in',
            ],
            'fqdn' => [
                'type'        => Type::nonNull(Type::string()),
                'description' => 'FQDN',
            ],
            'user' => [
                'type'        => Type::nonNull(GraphQL::type('User')),
                'description' => 'User',
            ]
        ];
    }

}
