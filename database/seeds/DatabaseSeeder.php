<?php

use Illuminate\Database\Seeder;

use App\Tenant\User;
use App\Tenant\UserGroup;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $now = now();

        /*
, [
                'email'     => 'admin@test.sk',
                'password'  => 'dankojekral'
            ], [
                'email'     => 'user1@test.sk',
                'password'  => 'dankojekral'
            ], [
                'email'     => 'user2@test.sk',
                'password'  => 'dankojekral'
            ], [
                'email'     => 'user3@test.sk',
                'password'  => 'dankojekral'
            ], [
                'email'     => 'user4@test.sk',
                'password'  => 'dankojekral'
            ], [
                'email'     => 'user5@test.sk',
                'password'  => 'dankojekral'
            ], [
                'email'     => 'user6@test.sk',
                'password'  => 'dankojekral'
            ], [
                'email'     => 'user7@test.sk',
                'password'  => 'dankojekral'
            ], [
                'email'     => 'user8@test.sk',
                'password'  => 'dankojekral'
            ], [
                'email'     => 'user9@test.sk',
                'password'  => 'dankojekral'
            ], [
                'email'     => 'user10@test.sk',
                'password'  => 'dankojekral'
            ], [
                'email'     => 'user11@test.sk',
                'password'  => 'dankojekral'
            ], [
                'email'     => 'user12@test.sk',
                'password'  => 'dankojekral'
            ], [
                'email'     => 'user13@test.sk',
                'password'  => 'dankojekral'
            ], [
                'email'     => 'user14@test.sk',
                'password'  => 'dankojekral'
            ], [
                'email'     => 'user15@test.sk',
                'password'  => 'dankojekral'
            ], [
                'email'     => 'user16@test.sk',
                'password'  => 'dankojekral'
            ], [
                'email'     => 'user17@test.sk',
                'password'  => 'dankojekral'
            ], [
                'email'     => 'user18@test.sk',
                'password'  => 'dankojekral'
            ], [
                'email'     => 'user19@test.sk',
                'password'  => 'dankojekral'
            ], [
                'email'     => 'user20@test.sk',
                'password'  => 'dankojekral'
            ], [
                'email'     => 'user21@test.sk',
                'password'  => 'dankojekral'
            ], [
                'email'     => 'user22@test.sk',
                'password'  => 'dankojekral'
            ], [
                'email'     => 'user23@test.sk',
                'password'  => 'dankojekral'
            ], [
                'email'     => 'user24@test.sk',
                'password'  => 'dankojekral'
            ], [
                'email'     => 'user25@test.sk',
                'password'  => 'dankojekral'
            ], [
                'email'     => 'user26@test.sk',
                'password'  => 'dankojekral'
            ], [
                'email'     => 'user27@test.sk',
                'password'  => 'dankojekral'
            ], [
                'email'     => 'user28@test.sk',
                'password'  => 'dankojekral'
            ],

        */

        // Users
        $users = [
            [
                'email'     => 'superadmin@test.sk',
                'password'  => 'dankojekral'
            ], [
                'email'     => 'user29@test.sk',
                'password'  => 'dankojekral'
            ]
        ];

        foreach ($users as $user) {
            User::createUser($user['email'], $user['password'], true);
        }        

        // User Groups
        $u2gs = [
            [
                'user_id'       => 1,
                'user_group_id' => 1
            ], [
                'user_id'       => 2,
                'user_group_id' => 1
            ]
        ];

        foreach ($u2gs AS $u2g) {

            $user = User::find($u2g['user_id']);

            $user->role()->detach(DEFAULT_USER_ROLE);
            $user->role()->attach($u2g['user_group_id']);
            $user->save();

        }
        
        // pridat super adminovi all basic access
        $role = UserGroup::find(1);

        for ($i = 1; $i < 25; $i++) {
            $role->modules()->attach($i);
        }

        // pridat super adminovi all basic access
        $role = UserGroup::find(2);

        for ($i = 1; $i < 25; $i++) {
            $role->modules()->attach($i);
        }

        // pridat ostatnym vsetky prava okrem permissions
        for ($j = 3; $j < 5; $j++) {

            $role = UserGroup::find($j);

            for ($i = 1; $i < 25; $i++) {

                if (($i < 9) || ($i > 12)) {
                    $role->modules()->attach($i);
                }
            }
        }

        // pre rolu User LEN read okrem perms
        $role = UserGroup::find(5);

        $role->modules()->attach(2);  // User - read
        $role->modules()->attach(6);  // UserGroup - read
        $role->modules()->attach(14); // Task - read
        $role->modules()->attach(15); // Task - update
        $role->modules()->attach(18); // Project - read
        $role->modules()->attach(21); // Attendance - create
        $role->modules()->attach(22); // Attendance - read


    }
}
