<?php

namespace App\GraphQL\Mutation\Attendance;

use GraphQL;
use App\Tenant\User;
use App\Tenant\Attendance;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Mutation;
use Illuminate\Support\Facades\Validator;

class LeaveMutation extends Mutation {

    protected $attributes = [
        'name' => 'Leave',
        'description' => 'User leave from work.'
    ];

	public function type(): \GraphQL\Type\Definition\Type
	{
        return Type::nonNull(GraphQL::type('Attendance'));
	}

	public function args(): array
	{
		return [
            'userId' => [
				'name' => 'userId',
				'type' => Type::int()
			]
        ];
	}

	public function resolve($root, $args)
	{
        if (!(User::checkAction('Attendance', 'create', 'global', false))) { // first check global rights
            User::checkAction('Attendance', 'create'); // check basic rights
        }

        if (!empty($args['userId'])) {

            $user = User::find($args['userId']);

        } else {

            $user = auth()->user();

        }

        if (!$user) {
            throw new \GraphQL\Error\Error('User not existed.');
        }

        $attendance = $user->lastAttendance();

        // ak ma posledna attendance vyplneny end alebo neexistuje -> je potrebne spravit novu
        if ((!$attendance) || (!$attendance->start())) {

            throw new \GraphQL\Error\Error('You have to start new attendance.');
            
        }

        $attendance->leave();

        $attendanceId = $attendance->id;

		return Attendance::find($attendanceId);
	}


}