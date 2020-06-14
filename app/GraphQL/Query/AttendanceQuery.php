<?php

namespace App\GraphQL\Query;

use GraphQL;
use App\Tenant\User;
use App\Tenant\Attendance;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Query;

use Hyn\Tenancy\Environment;

class AttendanceQuery extends Query {

    protected $attributes = [
        'name' => 'Attendance query',
        'description' => 'Get attendance'
    ];

    public function type(): \GraphQL\Type\Definition\Type
    {
        return Type::nonNull(GraphQL::type('Attendance'));
    }
    
    public function args(): array
    {
        return [
            'attendanceId' => [
                'type'    => Type::nonNull(Type::int())
            ]
        ];
    }
    
    public function resolve($root, $args)
    {
        User::checkAction('Attendance', 'read');
        
        if (empty($args['attendanceId'])) {
            throw new \GraphQL\Error\Error('Attendance ID is required.');
        }
        
        $attendance = Attendance::find($args['attendanceId']);

        // ak nema global pravo na read dochadzky tak len jeho dochadzku
        if (!User::checkAction('Attendance', 'read', 'global', false)) {

            $user = auth()->user();

            if ($attendance->user_id != $args['attendanceId']) {
                throw new \GraphQL\Error\Error('You can see only your attendance.');
            }
            
        }

        return $attendance;
    }

}
