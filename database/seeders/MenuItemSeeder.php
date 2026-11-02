<?php

namespace Database\Seeders;

use App\Models\MenuItem;
use Illuminate\Database\Seeder;

class MenuItemSeeder extends Seeder
{
    public function run(): void
    {
        MenuItem::truncate();

        $menu = [
            // FOOD
            [
                'name' => 'Buffet',
                'category' => 'FOOD',
                'price' => 1550,
                'imageUrl' => 'https://images.unsplash.com/photo-1555244162-803834f70033?w=600&auto=format&fit=crop&q=80',
                'popular' => true,
                'description' => 'Lavish Gura Bicha game-day buffet spread with assorted prime meat dishes, sides, and Ethiopian accompaniments.',
            ],
            [
                'name' => 'Steak Sandwich',
                'category' => 'FOOD',
                'price' => 1200,
                'imageUrl' => 'https://images.unsplash.com/photo-1528735602780-2552fd46c7af?w=600&auto=format&fit=crop&q=80',
                'popular' => true,
                'description' => 'Juicy grilled beef tenderloin steak cuts in toasted artisan bread with caramelized onions, house relish and crispy fries.',
            ],
            [
                'name' => 'Chicken Sandwich',
                'category' => 'FOOD',
                'price' => 1200,
                'imageUrl' => 'https://images.unsplash.com/photo-1606755962773-d324e0a13086?w=600&auto=format&fit=crop&q=80',
                'popular' => false,
                'description' => 'Tender seasoned chicken fillet grilled to perfection, topped with crisp lettuce, ripe tomato and savory herb mayo with fries.',
            ],
            [
                'name' => 'Chicken Wings',
                'category' => 'FOOD',
                'price' => 1200,
                'imageUrl' => 'https://images.unsplash.com/photo-1567620832903-9fc6debc209f?w=600&auto=format&fit=crop&q=80',
                'popular' => true,
                'description' => 'Crispy golden fried chicken wings tossed in savory glaze, served with creamy blue cheese dip and fresh celery sticks.',
            ],
            [
                'name' => 'Cheese Burger',
                'category' => 'FOOD',
                'price' => 1200,
                'imageUrl' => 'https://images.unsplash.com/photo-1568901346375-23c9450c58cd?w=600&auto=format&fit=crop&q=80',
                'popular' => true,
                'description' => 'Double smashed prime beef patty layered with melted cheddar cheese, fresh lettuce, pickles, and signature stadium relish with fries.',
            ],
            [
                'name' => 'Lasagna',
                'category' => 'FOOD',
                'price' => 1400,
                'imageUrl' => 'https://images.unsplash.com/photo-1574894709920-11b28e7367e3?w=600&auto=format&fit=crop&q=80',
                'popular' => false,
                'description' => 'Oven-baked layered pasta sheets with slow-simmered minced beef ragù, silky béchamel sauce, and rich golden melted cheese.',
            ],

            // PIZZA
            [
                'name' => 'Margherita Pizza',
                'category' => 'PIZZA',
                'price' => 1500,
                'imageUrl' => 'https://images.unsplash.com/photo-1604382355076-af4b0eb60143?w=600&auto=format&fit=crop&q=80',
                'popular' => true,
                'description' => 'Classic stone-baked thin crust pizza topped with rich Italian tomato sauce, generous mozzarella cheese, fresh basil and olive oil.',
            ],
            [
                'name' => 'Tuna Pizza',
                'category' => 'PIZZA',
                'price' => 1500,
                'imageUrl' => 'https://images.unsplash.com/photo-1513104890138-7c749659a591?w=600&auto=format&fit=crop&q=80',
                'popular' => false,
                'description' => 'Freshly baked pizza topped with seasoned flaked tuna, sliced red onions, kalamata olives, tomato base and melted mozzarella.',
            ],

            // BEER & DRINKS
            [
                'name' => 'Heineken',
                'category' => 'DRINKS',
                'price' => 280,
                'imageUrl' => 'https://images.unsplash.com/photo-1584225064785-c62a8b43d148?w=600&auto=format&fit=crop&q=80',
                'popular' => true,
                'description' => 'Chilled premium Heineken pure malt lager bottle served ice-cold.',
            ],
            [
                'name' => 'Arada',
                'category' => 'DRINKS',
                'price' => 280,
                'imageUrl' => 'https://images.unsplash.com/photo-1551024709-8f23befc6f87?w=600&auto=format&fit=crop&q=80',
                'popular' => true,
                'description' => 'Refreshing Ethiopian hard seltzer with natural fruit citrus flavors.',
            ],
            [
                'name' => 'Heineken Draft',
                'category' => 'DRINKS',
                'price' => 190,
                'imageUrl' => 'https://images.unsplash.com/photo-1518176258769-f227c798150e?w=600&auto=format&fit=crop&q=80',
                'popular' => true,
                'description' => 'Ice-cold Heineken draft poured fresh from the stadium tap into a chilled beer stein.',
            ],
            [
                'name' => 'Local Beer',
                'category' => 'DRINKS',
                'price' => 220,
                'imageUrl' => 'https://images.unsplash.com/photo-1535958636474-b021ee887b13?w=600&auto=format&fit=crop&q=80',
                'popular' => true,
                'description' => 'Choice of chilled Ethiopian bottled beers: St. George, Habesha, Walia, or Bedele.',
            ],
            [
                'name' => 'Mojito',
                'category' => 'DRINKS',
                'price' => 600,
                'imageUrl' => 'https://images.unsplash.com/photo-1551024709-8f23befc6f87?w=600&auto=format&fit=crop&q=80',
                'popular' => true,
                'description' => 'Signature chilled cocktail prepared with fresh mint leaves, lime juice, rum, cane sugar, and sparkling soda.',
            ],
        ];

        foreach ($menu as $item) {
            MenuItem::create($item);
        }
    }
}
