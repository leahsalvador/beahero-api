<?php

use Illuminate\Database\Seeder;

use App\Songs;
class SongsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        factory(Songs:: class, 200) -> create();
    }
}
