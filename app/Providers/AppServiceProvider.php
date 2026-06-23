<?php

namespace App\Providers;

use App\View\DevBladeCompiler;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

/**
 * Provider principal da aplicação FinControl.
 */
class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        if ($this->app->environment('local')) {
            $this->app->extend('blade.compiler', function ($compiler, $app) {
                return new DevBladeCompiler(
                    $app['files'],
                    $app['config']['view.compiled']
                );
            });
        }
    }

    public function boot(): void
    {
        // Usar Bootstrap para paginação (compatível com nosso CSS)
        Paginator::useBootstrapFive();

        // Diretiva Blade customizada para formatar moeda BRL
        Blade::directive('money', function ($amount) {
            return "<?php echo 'R$ ' . number_format($amount, 2, ',', '.'); ?>";
        });
    }
}
 