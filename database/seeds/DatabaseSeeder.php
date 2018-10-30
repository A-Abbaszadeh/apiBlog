<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // $this->call(UsersTableSeeder::class);

        factory(\App\Article::class , 10)->create()->each(function ($article) {
            factory(App\User::class, 1)->create()->each(function ($user) use ($article) {
                factory(App\Comment::class, rand(6 , 20))->create([
                    'author' => $user->email,
                    'article_id' => $article->id,
                ]);
            });
        });
    }
}
