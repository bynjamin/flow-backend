<?php

namespace App\GraphQL\EnumType;

use Rebing\GraphQL\Support\Type as GraphQLType;

class AttendanceStateEnumType extends GraphQLType
{
    protected $enumObject = true;

    protected $attributes = [
        'name' => 'AttendanceState',
        'description' => 'Attendance state',
        'values' => [
            '0' => 'WORK',
            '1' => 'OUT_OF_WORK',
            '2' => 'LUNCH',
            '3' => 'BREAK',
        ],
    ];
}