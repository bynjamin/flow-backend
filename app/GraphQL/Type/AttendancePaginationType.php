<?php
namespace App\GraphQL\Type;

use App\GraphQL\TypeRegistry;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Type as GraphQLType;

class AttendancePaginationType extends GraphQLType {

    protected $attributes = [
        'name'          => 'AttendancePagination',
        'description'   => 'Attendance pagination model'
    ];

    public function fields(): array
    {
        return [
            'count' => [
                'type'        => Type::nonNull(Type::Int()),
                'description' => 'Total attendance count',
            ],            
            'items' => [
                'type' => Type::nonNull(Type::listOf(Type::nonNull(GraphQL::type('Attendance')))),
                'description' => 'List of attendance'
            ]
        ];
    }

}