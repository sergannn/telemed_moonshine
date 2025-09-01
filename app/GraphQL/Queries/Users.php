<?php

namespace App\GraphQL\Queries;

use App\Models\User;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;

class Users
{
    /**
     * Return a paginated list of users with optional email filtering
     *
     * @param  mixed  $rootValue
     * @param  array  $args
     * @param  GraphQLContext  $context
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function __invoke($rootValue, array $args, GraphQLContext $context)
    {
        $query = User::query();
        
        // Filter by email if provided
        if (isset($args['email'])) {
            $query->where('email', $args['email']);
        }
        
        return $query->paginate();
    }
}
