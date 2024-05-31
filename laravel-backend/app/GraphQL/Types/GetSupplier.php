<?php
declare(strict_types=1);

namespace App\GraphQL\Types;

use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Type as GraphQLType;

class GetSupplier extends GraphQLType
{
    protected $attributes = [
        'name' => 'GetSupplier',
        'description' => 'A type'
    ];

    public function fields(): array
    {
        return [
            "id" => [
                "type" => Type::int(),
                "description" => 'supplier_id of supplier',
            ],
            "code" => [
                "type" => Type::string(),
                "description" => 'code of supplier',
            ],
            "name" => [
                "type" => Type::string(),
                "description" => 'name of supplier',
            ],
        ];
    }
}
?>