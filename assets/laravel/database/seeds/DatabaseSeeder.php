<?php

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        $this->call('RoleTableSeeder');
        $this->call('UserTableSeeder');
        $this->call('LanguagesSeeder');
        $this->call('ArticleFlagsSeeder');

        if( App::environment() === 'development' )
        {
           $this->call('DevelopmentSeeder');
        }

        Model::reguard();
    }
}
