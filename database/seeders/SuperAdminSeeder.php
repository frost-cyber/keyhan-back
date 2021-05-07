<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Carbon\Carbon;

class SuperAdminSeeder extends Seeder {
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run() {
        User::create( [
            'name'               => 'Admin',
            'mobile'             => '9123456789',
            'email'              => 'admin@admin.com',
            'email_verified_at'  => Carbon::now(),
            'mobile_verified_at' => Carbon::now(),
            'password'           => \Hash::make('AdminCafeEnergy'),
        ] );
    }
}
