<?php
namespace App\GraphQL\Type;

use App\GraphQL\TypeRegistry;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Type as GraphQLType;

class AttendanceType extends GraphQLType {

    protected $attributes = [
        'name'          => 'Attendance',
        'description'   => 'Attendance type',
        'model'         => \App\Tenant\Attendance::class,
    ];

    public function fields(): array
    {
        return [
            'id' => [
                'type'        => Type::nonNull(Type::int()),
                'description' => 'Attendance ID'
            ],
            'start' => [
                'type'        => Type::string(),
                'description' => 'User start of work',
                'resolve'     => function($model) {

                    $startOfAttendance = $model->start();
                    
                    return ($startOfAttendance) ? $startOfAttendance->time : null;

                },
            ],
            'end' => [
                'type'        => Type::string(),
                'description' => 'User end of work',
                'resolve'     => function($model) {

                    $endOfAttendance = $model->end();

                    return ($endOfAttendance) ? $endOfAttendance->time : null;

                },
            ], 
            'user' => [
                'type'        => Type::nonNull(GraphQL::type('User')),
                'description' => 'User',
            ],
            'total' => [
                'type'        => Type::nonNull(Type::string()),
                'description' => 'User start of work',
                'resolve'     => function($model) {

                    return $model->total();

                },
            ],          
        ];
    }

}
