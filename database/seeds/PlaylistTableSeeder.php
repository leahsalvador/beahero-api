<?php

use Illuminate\Database\Seeder;

use App\Playlist;

class PlaylistTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        factory(Playlist::class, 2) ->create ();

    }
}
