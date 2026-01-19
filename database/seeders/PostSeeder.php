// database/seeders/PostSeeder.php
<?php
use App\Models\Post;
use Illuminate\Database\Seeder;

class PostSeeder extends Seeder
{
    public function run(): void
    {
        Post::insert([
            [
                'title' => 'Laravel Tips',
                'category' => 'Tech',
                'views_count' => 150,
                'likes_count' => 20,
            ],
            [
                'title' => 'PHP 8 Features',
                'category' => 'Tech',
                'views_count' => 200,
                'likes_count' => 35,
            ],
            [
                'title' => 'World Cup News',
                'category' => 'Sports',
                'views_count' => 500,
                'likes_count' => 90,
            ],
            [
                'title' => 'Match Highlights',
                'category' => 'Sports',
                'views_count' => 300,
                'likes_count' => 50,
            ],
        ]);
    }
}
