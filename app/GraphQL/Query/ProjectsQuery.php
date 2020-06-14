<?php

namespace App\GraphQL\Query;

use GraphQL;
use App\Tenant\User;
use App\Tenant\Project;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Query;

use Hyn\Tenancy\Environment;

class ProjectsQuery extends Query {

    protected $attributes = [
        'name' => 'Projects query',
        'description' => 'Get all projects'
    ];

    public function type(): \GraphQL\Type\Definition\Type
    {
        return Type::nonNull(GraphQL::type('ProjectPagination'));
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
        User::checkAction('Project', 'read');
        
        $projects = Project::where('deleted', 0);

        // ak nema global pravo na read projektov tak len jeho projekty kde je owner alebo manager
        // a tiez aj projekty kde je task kde som asignee
        if (!User::checkAction('Project', 'read', 'global', false)) {

            $user = auth()->user();

            $projects = $user->myProjects();

            if (empty($projects)) {

                return [
                    'count' => 0,
                    'items' => []
                ];

            }

        }

        if (!empty($args['search'])) {
            $search = $args['search'];

            $projects->where(function ($q) use ($search) {
                $q->whereRaw("name like '%" . $search . "%'")
                    ->orWhereRaw("description like '%" . $search . "%'");
            });

        }

        if (!empty($args['orderBy'])) {
        
            $projects->orderBy($args['orderBy'], $args['orderDirection']);
                    
        }

        $count = $projects->count();

        $skip  = empty($args['skip']) ? 0 : $args['skip'];
        $first = empty($args['first']) ? DEFAULT_PER_PAGE : $args['first'];

        return [
            'count' => $count,
            'items' => $projects->skip($skip)->take($first)->get()
        ];
    }

}
