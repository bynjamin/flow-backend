<?php

namespace App\GraphQL\Query;

use GraphQL;
use App\Tenant\Task;
use App\Tenant\User;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Query;

use Hyn\Tenancy\Environment;

class TasksQuery extends Query {

    protected $attributes = [
        'name' => 'Tasks query',
        'description' => 'Get all tasks'
    ];

    public function type(): \GraphQL\Type\Definition\Type
    {
        return Type::nonNull(GraphQL::type('TaskPagination'));
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
            'projectId' => [
                'type'    => Type::int()
            ],
            'search' => [
                'type'    => Type::string()
            ]
        ];
    }
    
    public function resolve($root, $args)
    {
        User::checkAction('Task', 'read');
        
        $tasks = Task::where('deleted', 0);

        $user = auth()->user();

        // ak nema global pravo na read taskov tak len jeho tasky a tasky z projektov kde je owner alebo manager
        // pokial bolo zadane projectId tak len tasky patriace k tomu projektu
        if (!User::checkAction('Task', 'read', 'global', false)) {

            if (!empty($args['projectId'])) {
                $tasks = $user->getTasks($args['projectId']);
            } else {
                $tasks = $user->getTasks();
            }

            if (empty($tasks)) {

                return [
                    'count' => 0,
                    'items' => []
                ];

            }

        }

        if (!empty($args['search'])) {
            $search = $args['search'];

            $tasks->where(function ($q) use ($search) {
                $q->whereRaw("name like '%" . $search . "%'")
                    ->orWhereRaw("description like '%" . $search . "%'");
            });

        }

        if (!empty($args['projectId'])) {

            $tasks->where('project_id', $args['project_id']);

        }

        if (!empty($args['orderBy'])) {
        
            $tasks->orderBy($args['orderBy'], $args['orderDirection']);
                    
        }

        $count = $tasks->count();

        $skip  = empty($args['skip']) ? 0 : $args['skip'];
        $first = empty($args['first']) ? DEFAULT_PER_PAGE : $args['first'];

        return [
            'count' => $count,
            'items' => $tasks->skip($skip)->take($first)->get()
        ];
    }

}
