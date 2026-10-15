<?php

namespace App\Providers;

use App\Models\Order;
use App\Models\User;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Define authorization gates
        Gate::define('admin', function (User $user) {
            return $user->isAdmin();
        });

        Gate::define('view-order', function (User $user, Order $order) {
            return $user->isAdmin() || $order->user_id === $user->id;
        });

        Gate::define('view-payment-proof', function (User $user, Order $order) {
            return $user->isAdmin() || $order->user_id === $user->id;
        });

        Gate::define('cancel-order', function (User $user, Order $order) {
            if ($user->isAdmin()) {
                return true;
            }
            return $order->user_id === $user->id && $order->order_status === 'pending_payment';
        });

        // Blade directive for formatting Rupiah
        Blade::directive('currency', function ($expression) {
            return "<?php echo 'Rp ' . number_format((float)($expression), 0, ',', '.'); ?>";
        });
    }
}
