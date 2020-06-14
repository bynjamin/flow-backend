<?php

namespace App\GraphQL\Query;

use GraphQL;
use App\Tenant\User;
use App\Tenant\UserGroup;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Query;

class UserGroupsQuery extends Query {

    protected $attributes = [
        'name' => 'Get user groups',
        'description' => 'Get user groups'
    ];

    public function type(): \GraphQL\Type\Definition\Type
    {
        return Type::nonNull(GraphQL::type('UserGroupPagination'));
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
            'search' => [
                'type'    => Type::string()
            ]
        ];
    }

    public function resolve($root, $args)
    {
        User::checkAction('UserGroup', 'read');   
        
        $groups = UserGroup::group();

        if (!empty($args['search'])) {
            $search = $args['search'];

            $groups->where(function ($q) use ($search) {
                $q->whereRaw("name like '%" . $search . "%'")
                    ->orWhereRaw("description like '%" . $search . "%'");
            });

        }
        
        if (!empty($args['orderBy'])) {
            
            $groups->orderBy($args['orderBy'], $args['orderDirection']);
            
        }
        
        $count = $groups->count();
        $skip  = empty($args['skip']) ? 0 : $args['skip'];
        $first = empty($args['first']) ? DEFAULT_PER_PAGE : $args['first'];

        return [
            'count' => $count,
            'items' => $groups->skip($skip)->take($first)->get()
        ];
    }

}
