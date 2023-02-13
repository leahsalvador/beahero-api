<?php

use Illuminate\Database\Seeder;
// import file
use App\Accounts;

class accountsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // locating factory file

        factory(Accounts::class, 50) ->create ();
    }
}
