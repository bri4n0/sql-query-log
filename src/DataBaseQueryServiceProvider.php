<?php
declare(strict_types=1);

namespace Bri4n0\SqlQueryLog;

use Carbon\Carbon;
use Illuminate\Database\Events\TransactionBeginning;
use Illuminate\Database\Events\TransactionCommitted;
use Illuminate\Database\Events\TransactionRolledBack;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\ServiceProvider;

class DataBaseQueryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/logging.php', 'logging');

        if (config('logging.enable_sql_log') === false) {
            return;
        }
        $logLevel = config('logging.sql_log_level');

        if ($logLevel) {
            Log::setDefaultDriver($logLevel);
        }

        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../config/logging.php' => config_path('logging.php'),
            ], 'config');
        }
        $logChannel = config('logging.sql_log_channel');

        $log = Log::channel();
        if ($logChannel) {
            $log = Log::channel($logChannel);
        }

        DB::listen(function ($query) use ($log, $logChannel) {
            $sql = $query->sql;
            foreach ($query->bindings as $binding) {
                if (is_string($binding)) {
                    $binding = "'{$binding}'";
                } elseif ($binding === null) {
                    $binding = 'NULL';
                } elseif ($binding instanceof Carbon) {
                    $binding = "'{$binding->toDateTimeString()}'";
                } elseif ($binding instanceof \DateTime) {
                    $binding = "'{$binding->format('Y-m-d H:i:s')}'";
                }

                $sql = preg_replace("/\?/", $binding, $sql, 1);
            }

            $log::{$logChannel}('SQL', ['sql' => $sql, 'time' => "$query->time ms"]);
        });

        Event::listen(TransactionBeginning::class, function (TransactionBeginning $event) use ($log, $logChannel) {
            $log::{$logChannel}('START TRANSACTION');
        });

        Event::listen(TransactionCommitted::class, function (TransactionCommitted $event) use ($log, $logChannel) {
            $log::{$logChannel}('COMMIT');
        });

        Event::listen(TransactionRolledBack::class, function (TransactionRolledBack $event) use ($log, $logChannel) {
            $log::{$logChannel}('ROLLBACK');
        });
    }
}
