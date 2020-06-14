<?php

namespace App\GraphQL\EnumType;

use Rebing\GraphQL\Support\Type as GraphQLType;

class TaskStateEnumType extends GraphQLType
{
    protected $enumObject = true;

    protected $attributes = [
        'name' => 'TaskState',
        'description' => 'Task state',
        'values' => [
            '0' => 'NOT_STARTED',
            '1' => 'IN_PROGRESS',
            '2' => 'WAITING',
            '3' => 'FINISHED',
        ],
    ];
}