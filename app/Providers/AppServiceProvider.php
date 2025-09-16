<?php

namespace App\Providers;

use App\Repositories\EloquentCategoryPositionRepository;
use App\Repositories\Interfaces\CategoryPositionRepositoryInterface;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(
            CategoryPositionRepositoryInterface::class,
            EloquentCategoryPositionRepository::class
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
