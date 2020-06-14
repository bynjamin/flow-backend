<?php

declare(strict_types=1);

use example\Type\ExampleType;
use example\Query\ExampleQuery;
use example\Mutation\ExampleMutation;
use example\Type\ExampleRelationType;

return [

    // The prefix for routes
    'prefix' => 'api/graphql',

    // The routes to make GraphQL request. Either a string that will apply
    // to both query and mutation or an array containing the key 'query' and/or
    // 'mutation' with the according Route
    //
    // Example:
    //
    // Same route for both query and mutation
    //
    // 'routes' => 'path/to/query/{graphql_schema?}',
    //
    // or define each route
    //
    // 'routes' => [
    //     'query' => 'query/{graphql_schema?}',
    //     'mutation' => 'mutation/{graphql_schema?}',
    // ]
    //
    //'routes' => '{graphql_schema?}',

    // The controller to use in GraphQL request. Either a string that will apply
    // to both query and mutation or an array containing the key 'query' and/or
    // 'mutation' with the according Controller and method
    //
    // Example:
    //
    // 'controllers' => [
    //     'query' => '\Rebing\GraphQL\GraphQLController@query',
    //     'mutation' => '\Rebing\GraphQL\GraphQLController@mutation'
    // ]
    //
    'controllers' => \Rebing\GraphQL\GraphQLController::class.'@query',

    // Any middleware for the graphql route group
    'middleware' => [],

    // Additional route group attributes
    //
    // Example:
    //
    // 'route_group_attributes' => ['guard' => 'api']
    //
    'route_group_attributes' => [],

    // The name of the default schema used when no argument is provided
    // to GraphQL::schema() or when the route is used without the graphql_schema
    // parameter.
    'default_schema' => 'default',

    // The schemas for query and/or mutation. It expects an array of schemas to provide
    // both the 'query' fields and the 'mutation' fields.
    //
    // You can also provide a middleware that will only apply to the given schema
    //
    // Example:
    //
    //  'schema' => 'default',
    //
    //  'schemas' => [
    //      'default' => [
    //          'query' => [
    //              'users' => 'App\GraphQL\Query\UsersQuery'
    //          ],
    //          'mutation' => [
    //
    //          ]
    //      ],
    //      'user' => [
    //          'query' => [
    //              'profile' => 'App\GraphQL\Query\ProfileQuery'
    //          ],
    //          'mutation' => [
    //
    //          ],
    //          'middleware' => ['auth'],
    //      ],
    //      'user/me' => [
    //          'query' => [
    //              'profile' => 'App\GraphQL\Query\MyProfileQuery'
    //          ],
    //          'mutation' => [
    //
    //          ],
    //          'middleware' => ['auth'],
    //      ],
    //  ]
    //
    'schemas' => [
        'default' => [
            'query' => [
                'me'                               => \App\GraphQL\Query\MeQuery::class,
                'user'                             => \App\GraphQL\Query\UserQuery::class,
                'users'                            => \App\GraphQL\Query\UsersQuery::class,
                'canPerformAction'                 => \App\GraphQL\Query\CanPerformActionQuery::class,

                'userGroup'                        => \App\GraphQL\Query\UserGroupQuery::class,
                'userGroups'                       => \App\GraphQL\Query\UserGroupsQuery::class,

                'userRole'                         => \App\GraphQL\Query\UserRoleQuery::class,
                'userRoles'                        => \App\GraphQL\Query\UserRolesQuery::class,

                'task'                             => \App\GraphQL\Query\TaskQuery::class,
                'tasks'                            => \App\GraphQL\Query\TasksQuery::class,

                'project'                          => \App\GraphQL\Query\ProjectQuery::class,
                'projects'                         => \App\GraphQL\Query\ProjectsQuery::class,

                'attendance'                       => \App\GraphQL\Query\AttendanceQuery::class,
                'attendances'                      => \App\GraphQL\Query\AttendancesQuery::class,
            ],
            'mutation' => [
                'refreshLogin'                     => \App\GraphQL\Mutation\RefreshLoginMutation::class,
                'logout'                           => \App\GraphQL\Mutation\LogoutMutation::class,

                'inviteUser'                       => \App\GraphQL\Mutation\User\InviteUserMutation::class,
                
                // Users
                'createUser'                       => \App\GraphQL\Mutation\User\CreateUserMutation::class,
                'deleteUsers'                      => \App\GraphQL\Mutation\User\DeleteUsersMutation::class,
                'updateAddress'                    => \App\GraphQL\Mutation\User\UpdateAddressMutation::class,
                'updateProfile'                    => \App\GraphQL\Mutation\User\UpdateProfileMutation::class,
                'updateUser'                       => \App\GraphQL\Mutation\User\UpdateUserMutation::class,
                'updateUserWithPassword'           => \App\GraphQL\Mutation\User\UpdateUserWithPasswordMutation::class,
                'updatePassword'                   => \App\GraphQL\Mutation\User\UpdatePasswordMutation::class,                

                // User Groups
                'createUserGroup'                  => \App\GraphQL\Mutation\UserGroup\CreateUserGroupMutation::class,
                'updateUserGroup'                  => \App\GraphQL\Mutation\UserGroup\UpdateUserGroupMutation::class,
                'deleteUserGroups'                 => \App\GraphQL\Mutation\UserGroup\DeleteUserGroupsMutation::class,
                'addUserGroupToUser'               => \App\GraphQL\Mutation\UserGroup\AddUserGroupToUserMutation::class,
                'addUsersToUserGroup'              => \App\GraphQL\Mutation\UserGroup\AddUsersToUserGroupMutation::class,
                'removeUserGroupFromUser'          => \App\GraphQL\Mutation\UserGroup\RemoveUserGroupFromUserMutation::class,
                'removeUsersFromUserGroup'         => \App\GraphQL\Mutation\UserGroup\RemoveUsersFromUserGroupMutation::class,
                
                // User roles
                'createUserRole'                   => \App\GraphQL\Mutation\UserGroup\CreateUserRoleMutation::class,
                'addUsersToUserRole'               => \App\GraphQL\Mutation\UserGroup\AddUsersToUserRoleMutation::class,

                // Tasks
                'createTask'                       => \App\GraphQL\Mutation\Task\CreateTaskMutation::class,                
                'updateTask'                       => \App\GraphQL\Mutation\Task\UpdateTaskMutation::class,                
                'deleteTask'                       => \App\GraphQL\Mutation\Task\DeleteTaskMutation::class, 
                
                // Projects
                'createProject'                    => \App\GraphQL\Mutation\Project\CreateProjectMutation::class,                
                'updateProject'                    => \App\GraphQL\Mutation\Project\UpdateProjectMutation::class,                
                'deleteProject'                    => \App\GraphQL\Mutation\Project\DeleteProjectMutation::class, 

                // Attendance
                'arrival'                          => \App\GraphQL\Mutation\Attendance\ArrivalMutation::class, 
                'leave'                            => \App\GraphQL\Mutation\Attendance\LeaveMutation::class, 

                // Permissions
                //'addPermission'                  => \App\GraphQL\Mutation\Permission\AddPermissionMutation::class,
                //'removePermission'               => \App\GraphQL\Mutation\Permission\RemovePermissionMutation::class,
                'updateUserPermissions'            => \App\GraphQL\Mutation\Permission\UpdateUserPermissionsMutation::class,
                'updateUserGroupPermissions'       => \App\GraphQL\Mutation\Permission\UpdateUserGroupPermissionsMutation::class,
                'updateUserRolePermissions'        => \App\GraphQL\Mutation\Permission\UpdateUserRolePermissionsMutation::class,
            ],
            'middleware' => ['api:auth', 'tenant.confirm'],
            'method'     => ['get', 'post'],
        ],
        'public' => [
            'query' => [
                'isSiteAddressAvailable' => \App\GraphQL\Query\SiteAddressAvailabilityQuery::class,
            ],
            'mutation' => [
                'newRegistration'        => \App\GraphQL\Mutation\RegisterNewTenantMutation::class,
                'login'                  => \App\GraphQL\Mutation\LoginMutation::class,

                'checkInvitedUser'       => \App\GraphQL\Mutation\User\CheckInvitedUserMutation::class
            ],
            'middleware' => [],
            'method'     => ['get', 'post'],
        ],
    ],

    // The types available in the application. You can then access it from the
    // facade like this: GraphQL::type('user')
    //
    // Example:
    //
    // 'types' => [
    //     'user' => 'App\GraphQL\Type\UserType'
    // ]
    //
    
    'types' => [
        'Tenant'                 => \App\GraphQL\Type\TenantType::class,

        'Login'                  => \App\GraphQL\Type\LoginType::class,

        'User'                   => \App\GraphQL\Type\UserType::class,
        'UserPagination'         => \App\GraphQL\Type\UserPaginationType::class,
 
        'Address'                => \App\GraphQL\Type\AddressType::class,

        'UserGroup'              => \App\GraphQL\Type\UserGroupType::class,
        'UserGroupPagination'    => \App\GraphQL\Type\UserGroupPaginationType::class,
        'UserRole'               => \App\GraphQL\Type\UserRoleType::class,

        'Action'                 => \App\GraphQL\Type\ActionType::class,
        'Actions'                => \App\GraphQL\Type\ActionsType::class,
        'Crud'                   => \App\GraphQL\Type\CrudType::class,

        'Module'                 => \App\GraphQL\Type\ModuleType::class,

        'Collaborators'          => \App\GraphQL\Type\CollaboratorsType::class,
        'UserCollaborator'       => \App\GraphQL\Type\UserCollaboratorType::class,
        'UserGroupCollaborator'  => \App\GraphQL\Type\UserGroupCollaboratorType::class,

        // Tasks
        'Task'                   => \App\GraphQL\Type\TaskType::class,
        'TaskPagination'         => \App\GraphQL\Type\TaskPaginationType::class,

        // Projects
        'Project'                => \App\GraphQL\Type\ProjectType::class,
        'ProjectPagination'      => \App\GraphQL\Type\ProjectPaginationType::class,

        // Attendance
        'Attendance'             => \App\GraphQL\Type\AttendanceType::class,
        'AttendancePagination'   => \App\GraphQL\Type\AttendancePaginationType::class,

        // Input types
        'AccessInput'            => \App\GraphQL\InputType\AccessInputType::class,
        'ActionInput'            => \App\GraphQL\InputType\ActionInputType::class,

        // Enum types
        'TaskState'              => \App\GraphQL\EnumType\TaskStateEnumType::class,
        'AttendanceState'        => \App\GraphQL\EnumType\AttendanceStateEnumType::class,
    ],

    // The types will be loaded on demand. Default is to load all types on each request
    // Can increase performance on schemes with many types
    // Presupposes the config type key to match the type class name property
    'lazyload_types' => false,

    // This callable will be passed the Error object for each errors GraphQL catch.
    // The method should return an array representing the error.
    // Typically:
    // [
    //     'message' => '',
    //     'locations' => []
    // ]
    'error_formatter' => ['\Rebing\GraphQL\GraphQL', 'formatError'],

    /*
     * Custom Error Handling
     *
     * Expected handler signature is: function (array $errors, callable $formatter): array
     *
     * The default handler will pass exceptions to laravel Error Handling mechanism
     */
    'errors_handler' => ['\Rebing\GraphQL\GraphQL', 'handleErrors'],

    // You can set the key, which will be used to retrieve the dynamic variables
    'params_key'    => 'variables',

    /*
     * Options to limit the query complexity and depth. See the doc
     * @ https://github.com/webonyx/graphql-php#security
     * for details. Disabled by default.
     */
    'security' => [
        'query_max_complexity'  => null,
        'query_max_depth'       => null,
        'disable_introspection' => false,
    ],

    /*
     * You can define your own pagination type.
     * Reference \Rebing\GraphQL\Support\PaginationType::class
     */
    'pagination_type' => \Rebing\GraphQL\Support\PaginationType::class,

    /*
     * Config for GraphiQL (see (https://github.com/graphql/graphiql).
     */
    'graphiql' => [
        'prefix'     => 'graphiql/',
        'controller' => \Rebing\GraphQL\GraphQLController::class.'@graphiql',
        'middleware' => [],
        'view'       => 'graphql::graphiql',
        'display'    => env('ENABLE_GRAPHIQL', true),
    ],

    /*
     * Overrides the default field resolver
     * See http://webonyx.github.io/graphql-php/data-fetching/#default-field-resolver
     *
     * Example:
     *
     * ```php
     * 'defaultFieldResolver' => function ($root, $args, $context, $info) {
     * },
     * ```
     * or
     * ```php
     * 'defaultFieldResolver' => [SomeKlass::class, 'someMethod'],
     * ```
     */
    'defaultFieldResolver' => null,

    /*
     * Any headers that will be added to the response returned by the default controller
     */
    'headers' => [],

    /*
     * Any JSON encoding options when returning a response from the default controller
     * See http://php.net/manual/function.json-encode.php for the full list of options
     */
    'json_encoding_options' => 0,
];
