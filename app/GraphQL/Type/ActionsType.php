<?php
namespace App\GraphQL\Type;

use App\GraphQL\TypeRegistry;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Type as GraphQLType;

class ActionsType extends GraphQLType {

    protected $attributes = [
        'name'          => 'Actions',
        'description'   => 'Actions type'
    ];

    public function fields(): array
    {
        return [
            'basic' => [
                'type'        => Type::nonNull(GraphQL::type('Action')),
                'description' => 'Basic actions',
            ],
            'global' => [
                'type'        => Type::nonNull(GraphQL::type('Action')),
                'description' => 'Global actions',
            ]            
        ];
    }

}
