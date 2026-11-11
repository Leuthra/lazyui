<?php

namespace Workbench\Database\Seeders;

use Filament\Notifications\Notification;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Workbench\App\Enums\OrderStatus;
use Workbench\App\Enums\ProductStatus;
use Workbench\App\Models\Category;
use Workbench\App\Models\Order;
use Workbench\App\Models\Product;
use Workbench\App\Models\User;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Deterministic data: the visual regression suite compares screenshots against baselines.
        mt_srand(42);
        fake()->seed(42);

        $user = User::query()->firstOrCreate(
            ['email' => 'admin@lazyui.test'],
            ['name' => 'Lazy Admin', 'password' => 'password', 'email_verified_at' => now()],
        );

        $categories = collect(['Audio', 'Cameras', 'Laptops', 'Phones', 'Wearables', 'Accessories'])
            ->map(fn (string $name, int $i): Category => Category::query()->create([
                'name' => $name,
                'slug' => Str::slug($name),
                'description' => fake()->sentence(),
                'color' => ['#10b981', '#6366f1', '#3b82f6', '#ec4899', '#f97316', '#a855f7'][$i],
                'is_visible' => $i !== 5,
            ]));

        foreach (range(1, 48) as $i) {
            Product::query()->create([
                'category_id' => $categories->random()->id,
                'name' => ucfirst(fake()->words(mt_rand(2, 3), true)),
                'sku' => 'SKU-'.str_pad((string) $i, 4, '0', STR_PAD_LEFT),
                'status' => fake()->randomElement(ProductStatus::cases()),
                'price' => fake()->randomFloat(2, 9, 2400),
                'stock' => mt_rand(0, 250),
                'rating' => mt_rand(1, 5),
                'is_featured' => mt_rand(0, 4) === 0,
                'color' => fake()->hexColor(),
                'description' => fake()->paragraph(),
                'tags' => fake()->randomElements(['new', 'sale', 'eco', 'limited', 'bundle'], mt_rand(0, 3)),
                'attributes' => ['Weight' => mt_rand(100, 2000).' g', 'Warranty' => mt_rand(1, 3).' years'],
                'variants' => [['name' => 'Default', 'price' => 0, 'stock' => mt_rand(1, 50)]],
                'published_at' => fake()->dateTimeBetween('2025-07-01', '2026-06-30'),
            ]);
        }

        foreach (range(1, 60) as $i) {
            Order::query()->create([
                'number' => 'ORD-'.(10000 + $i),
                'customer' => fake()->name(),
                'email' => fake()->safeEmail(),
                'status' => fake()->randomElement(OrderStatus::cases()),
                'total' => fake()->randomFloat(2, 20, 4000),
                'items' => [['product' => 'SKU-000'.mt_rand(1, 9), 'quantity' => mt_rand(1, 4)]],
                'address' => fake()->address(),
                'created_at' => fake()->dateTimeBetween('2025-08-01', '2026-06-30'),
            ]);
        }

        Notification::make()
            ->title('Welcome to the LazyUI workbench')
            ->body('Every Filament feature, styled by LazyUI.')
            ->success()
            ->sendToDatabase($user);
    }
}
