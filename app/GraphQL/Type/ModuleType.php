<?php
namespace App\GraphQL\Type;

use App\GraphQL\TypeRegistry;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Type as GraphQLType;

class ModuleType extends GraphQLType {

    protected $attributes = [
        'name'          => 'Module',
        'description'   => 'Module model'
    ];

    public function fields(): array
    {
        return [   
            'id' => [
                'type'        => Type::nonNull(Type::string()),
                'description' => 'ID of module',
            ],   
            'model' => [
                'type'        => Type::nonNull(Type::string()),
                'description' => 'Model of module',
            ],  
            'actions' => [
                'type'        => Type::nonNull(GraphQL::type('Actions')),
                'description' => 'CRUD actions'
            ],          
        ];
    }

}