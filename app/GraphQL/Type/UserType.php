<?php
namespace App\GraphQL\Type;

use App\GraphQL\TypeRegistry;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Type as GraphQLType;

class UserType extends GraphQLType {

    protected $attributes = [
        'name'          => 'User',
        'description'   => 'User model',
        'model'         => \App\Tenant\User::class,
    ];

    public function fields(): array
    {
        return [
            'id' => [
                'type'        => Type::nonNull(Type::int()),
                'description' => 'User ID',
            ],
            'email' => [
                'type' => Type::nonNull(Type::string()),
                'description' => 'User email',
            ],            
            'isAdmin' => [
                'type' => Type::nonNull(Type::boolean()),
                'description' => 'Is user Admin',
                'resolve' => function($model) {
                    return $model->isAdmin();
                },
            ],
            'isSuperAdmin' => [
                'type' => Type::nonNull(Type::boolean()),
                'description' => 'Is user Super Admin',
                'resolve' => function($model) {
                    return $model->isSuperAdmin();
                },
            ],
            'address' => [
                'type' => Type::nonNull(GraphQL::type('Address')),
                'description' => 'User address',
                'resolve' => function($model) {

                    if (!empty($model->profile)) {

                        if (!empty($model->profile->address)) {
                            return $model->profile->address;
                        }

                    }

                    return [];
                },
            ],
            'firstName' => [
                'type' => Type::nonNull(Type::string()),
                'description' => 'User first name',
                'resolve' => function($model) {

                    if (!empty($model->profile)) {

                        return (!empty($model->profile->firstName)) ? $model->profile->firstName : '';

                    }

                    return '';
                },
            ],
            'lastName' => [
                'type' => Type::nonNull(Type::string()),
                'description' => 'User last name',
                'resolve' => function($model) {

                    if (!empty($model->profile)) {

                        return (!empty($model->profile->lastName)) ? $model->profile->lastName : '';

                    }

                    return '';
                },
            ],
            'fullName' => [
                'type' => Type::nonNull(Type::string()),
                'description' => 'User full name',
                'resolve' => function($model) {

                    if (!empty($model->profile)) {

                        $name  = (!empty($model->profile->firstName)) ? $model->profile->firstName : '';
                        $name .= ' ';
                        $name .= (!empty($model->profile->lastName)) ? $model->profile->lastName : '';

                        return $name;

                    }

                    return '';
                },
            ],
            'title' => [
                'type' => Type::nonNull(Type::string()),
                'description' => 'User title',
                'resolve' => function($model) {

                    if (!empty($model->profile)) {

                        return (!empty($model->profile->title)) ? $model->profile->title : '';

                    }

                    return '';
                },
            ],
            'phone' => [
                'type' => Type::nonNull(Type::string()),
                'description' => 'User phone',
                'resolve' => function($model) {

                    if (!empty($model->profile)) {

                        return (!empty($model->profile->phone)) ? $model->profile->phone : '';

                    }

                    return '';
                },
            ],
            'about' => [
                'type' => Type::nonNull(Type::string()),
                'description' => 'User about',
                'resolve' => function($model) {

                    if (!empty($model->profile)) {

                        return (!empty($model->profile->about)) ? $model->profile->about : '';

                    }

                    return '';
                },
            ],
            //$table->date('birthday')->nullable();
            /*'birthday' => [
                'type' => Type::nonNull(Type::string()),
                'description' => 'User gender',
                'resolve' => function($model) {

                    if (!empty($model->profile)) {

                        return (!empty($model->profile->gender)) ? $model->profile->gender : '';

                    }

                    return '';
                },
            ],*/
            'gender' => [
                'type' => Type::nonNull(Type::string()),
                'description' => 'User gender',
                'resolve' => function($model) {

                    if (!empty($model->profile)) {

                        return (!empty($model->profile->gender)) ? $model->profile->gender : '';

                    }

                    return '';
                },
            ],
            'gdpr' => [
                'type' => Type::nonNull(Type::boolean()),
                'description' => 'User gdpr',
                'resolve' => function($model) {

                    if (!empty($model->profile)) {

                        return (!empty($model->profile->gdpr)) ? $model->profile->gdpr : false;

                    }

                    return false;
                },
            ],
            'position' => [
                'type' => Type::nonNull(Type::string()),
                'description' => 'User position',
                'resolve' => function($model) {

                    if (!empty($model->profile)) {

                        return (!empty($model->profile->position)) ? $model->profile->position : '';

                    }

                    return '';
                },
            ],


            //$table->date('employedFrom')->nullable();


            'employmentType' => [
                'type' => Type::nonNull(Type::string()),
                'description' => 'User employmentType',
                'resolve' => function($model) {

                    if (!empty($model->profile)) {

                        return (!empty($model->profile->employmentType)) ? $model->profile->employmentType : '';

                    }

                    return '';
                },
            ],


            'permissions' => [
                'type'        => Type::nonNull(Type::listOf(Type::nonNull(GraphQL::type('Module')))),
                'description' => 'User access',
                'resolve'     => function($model) {

                    return $model->getModulesAccess();

                },
            ],
            'allPermissions' => [
                'type'        => Type::nonNull(Type::listOf(Type::nonNull(GraphQL::type('Module')))),
                'description' => 'All user access, including access from user groups and user roles',
                'resolve'     => function($model) {

                    return $model->getModulesAccess(true);

                },
            ],

            'isActive' => [
                'type' => Type::nonNull(Type::boolean()),
                'description' => 'User role',
                'resolve' => function($model) {

                    return ($model->isActive) ? true : false;

                },
            ],
            'workState' => [
                'type' => Type::nonNull(GraphQL::type('AttendanceState')),
                'description' => 'User work state',
                'resolve' => function($model) {

                    $attendance = $model->lastAttendance();

                    if (!$attendance) {
                        return 'OUT_OF_WORK'; // OUT OF WORK
                    }

                    // ak zacal a neskoncil
                    if (($attendance->start()) && (!$attendance->end())) {
                        return 'WORK'; // WORK
                    }

                    return 'OUT_OF_WORK'; // OUT OF WORK
                },
            ],

            'role' => [
                'type' => Type::nonNull(GraphQL::type('UserRole')),
                'description' => 'User role',
                'resolve' => function($model) {

                    foreach ($model->role AS $role) {
                        return $role;
                    }

                },
            ],
            'groups' => [
                'type' => Type::nonNull(Type::listOf(GraphQL::type('UserGroup'))),
                'description' => 'User groups',
                'resolve' => function($model) {

                    return $model->groups;

                },
            ],
            'collaborators' => [
                'type' => Type::nonNull(GraphQL::type('Collaborators')),
                'description' => 'All users and user groups that has permissions to C,R,U,D',
                'resolve' => function($model) {

                    return $model->getCollaborators();
                    
                },
            ]
        ];
    }

}

/*

	createdAt: String!
	projects: [Project]!

 */
