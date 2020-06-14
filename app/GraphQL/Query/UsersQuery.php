<?php

namespace App\GraphQL\Query;

use GraphQL;
use App\Tenant;
use App\Tenant\User;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Query;

use Hyn\Tenancy\Environment;

class UsersQuery extends Query {

    protected $attributes = [
        'name' => 'Users query',
        'description' => 'Get all users'
    ];

    public function type(): \GraphQL\Type\Definition\Type
    {
        return Type::nonNull(GraphQL::type('UserPagination'));
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
        if (!(User::checkAction('User', 'read', 'global', false))) { // first check global rights
            
            return [
                'count' => 1,
                'items' => [
                    auth()->user()
                ]
            ];

        }
        
        $users = User::visible();
        $users->join('user_profiles AS profile', 'users.id', '=', 'profile.user_id');

        if (!empty($args['search'])) {
            $search = $args['search'];

            $users->where(function ($q) use ($search) {
                $q->whereRaw("email like '%" . $search . "%'")
                    ->orWhereRaw("profile.firstName like '%" . $search . "%'")
                    ->orWhereRaw("profile.lastName like '%" . $search . "%'");
            });

        }

        if (!empty($args['orderBy'])) {
        
            if ($args['orderBy'] == 'email') {
            
                $users->orderBy('email', $args['orderDirection']);
                
            } else {

                $users->orderBy('profile.' . $args['orderBy'], $args['orderDirection']);
                
            }
                    
        }

        $count = $users->count();

        $skip  = empty($args['skip']) ? 0 : $args['skip'];
        $first = empty($args['first']) ? DEFAULT_PER_PAGE : $args['first'];

        return [
            'count' => $count,
            'items' => $users->skip($skip)->take($first)->get()
        ];
    }

}
