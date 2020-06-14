<?php
namespace App\GraphQL\Type;

use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Type as GraphQLType;

class TenantType extends GraphQLType {

    protected $attributes = [
        'name'          => 'Tenant',
        'description'   => 'Tenant model',
        'model'         => \App\Tenant::class,
    ];

    public function fields(): array
    {
        return [
            'id' => [
                'type'        => Type::nonNull(Type::int()),
                'description' => 'Tenant ID',
                'resolve' => function($model) {
                    return ($model->hostname->id) ?: 0;
                }
            ],
            'uuid' => [
                'type'        => Type::nonNull(Type::string()),
                'description' => 'Tenant uuid',
                'resolve' => function($model) {
                    return ($model->website->uuid) ?: 0;
                }
            ],
            'fqdn' => [
                'type' => Type::nonNull(Type::string()),
                'description' => 'Tenant fqdn',
                'resolve' => function($model) {
                    return ($model->hostname->fqdn) ?: '';
                }
            ],
        ];
    }

}
