<?php

namespace App\Providers;

use App\Services\Cms;
use Illuminate\Foundation\AliasLoader;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        AliasLoader::getInstance()->alias('Cms', Cms::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // @text($value): escape CMS text and wrap the Taka sign (see Cms::text()).
        Blade::directive('text', fn (string $expression) => "<?php echo \\App\\Services\\Cms::text({$expression}); ?>");
    }
}
