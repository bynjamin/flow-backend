<?php

namespace App\GraphQL\Query;

use GraphQL;
use App\Tenant\User;
use App\Tenant\Attendance;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Query;

use Hyn\Tenancy\Environment;

class AttendancesQuery extends Query {

    protected $attributes = [
        'name' => 'Attendances query',
        'description' => 'Get all attendances'
    ];

    public function type(): \GraphQL\Type\Definition\Type
    {
        return Type::nonNull(GraphQL::type('AttendancePagination'));
    }
    
    public function args(): array
    {
        return [
            'first' => [
                'type'    => Type::int(),
                'default' => DEFAULT_PER_PAGE
            ],
            'skip' => [
                'type'    => Type::int(),
                'default' => 0
            ],
            'orderBy' => [
                'type'    => Type::string(),
                'default' => ''
            ],
            'orderDirection' => [
                'type'    => Type::string(),
                'default' => DEFAULT_ORDER_DIRECTION
            ],
            'userId' => [
                'type'    => Type::int()
            ]
        ];
    }
    
    public function resolve($root, $args)
    {
        User::checkAction('Attendance', 'read');
        
        $attendances = Attendance::query();

        // ak nema global pravo na read dochadzky tak len jeho dochadzku
        if (!User::checkAction('Attendance', 'read', 'global', false)) {

            $user = auth()->user();

            $attendances = $user->attendances();

            if (empty($attendances)) {

                return [
                    'count' => 0,
                    'items' => []
                ];

            }

        }

        if (!empty($args['userId'])) {
            $userId = $args['userId'];

            $attendances->where('user_id', $userId);

        }

        if (!empty($args['orderBy'])) {
        
            $attendances->orderBy($args['orderBy'], $args['orderDirection']);
                    
        }

        $count = $attendances->count();

        $skip  = empty($args['skip']) ? 0 : $args['skip'];
        $first = empty($args['first']) ? DEFAULT_PER_PAGE : $args['first'];

        return [
            'count' => $count,
            'items' => $attendances->skip($skip)->take($first)->get()
        ];
    }

}
