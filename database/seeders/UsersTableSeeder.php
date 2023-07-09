<?php

use App\Models\Admin;
use Illuminate\Database\Seeder;
class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Admin::truncate();


        $admin = Admin::create([
			'admin_name' => 'hieutan',
			'admin_email' => 'hieutan@gmail.com',
			'admin_phone' => '0932023991',
			'admin_password' => md5('123456')
        ]);
        $author = Admin::create([
			'admin_name' => 'hieutan123',
			'admin_email' => 'hieutan123@gmail.com',
			'admin_phone' => '0932023992',
			'admin_password' => md5('123456')
        ]);
        $user = Admin::create([
			'admin_name' => 'hieutan456',
			'admin_email' => 'hieutan456@gmail.com',
			'admin_phone' => '0932023993',
			'admin_password' => md5('123456')
        ]);
    }
}
