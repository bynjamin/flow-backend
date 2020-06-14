<?php
namespace App\GraphQL\Query;

use GraphQL;
use App\Tenant;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Query;
      
class SiteAddressAvailabilityQuery extends Query {

    protected $attributes = [
        'name' => 'SiteAddressAvailability',
        'description' => 'Check if site name isn\'t already occupied'
    ];

    public function type(): \GraphQL\Type\Definition\Type
    {
        return Type::nonNull(Type::boolean());
    }

    public function args(): array
    {
        return [
            'siteAddress' => [
                'name' => 'siteAddress',
                'type' => Type::string()
            ],
        ];
    }

    public function resolve($root, $args)
    {

        if (Tenant::tenantExists($args['siteAddress'])) {
            return false;
        }

        return true;
    }

}
