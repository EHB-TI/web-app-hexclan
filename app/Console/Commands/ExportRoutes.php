<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Route;

class ExportRoutes extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'custom:export';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Export all routes in CSV file.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $routes = Route::getRoutes();
        $fp = fopen(base_path('routes.csv'), 'w');
        fputcsv($fp, ['METHOD', 'URI', 'NAME', 'ACTION']);
        foreach ($routes as $route) {
            fputcsv($fp, [head($route->methods()), $route->uri(), $route->getName(), $route->getActionName()]);
        }
        fclose($fp);

        $this->info('All routes exported succesfully');
    }
}
