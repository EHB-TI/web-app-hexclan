<?php

declare(strict_types=1);

namespace Tests\Extensions;

use PHPUnit\Runner\AfterLastTestHook;
use Illuminate\Contracts\Console\Kernel;

final class Teardown implements AfterLastTestHook
{
    public function executeAfterLastTest(): void
    {
        // phpunit --testsuite Unit
        //echo sprintf("testsuite: %s\n", $this->getPhpUnitParam("testsuite"));

        // phpunit --filter CreateCompanyTest
        //echo sprintf("filter: %s\n", $this->getPhpUnitParam("filter"));

        $base_path = dirname(__DIR__, 2);
        $app = require $base_path . '/bootstrap/app.php';
        $kernel = $app->make(Kernel::class);
        $kernel->call('custom:drop');
    }

    /**
     * @return string|null
     */
    protected function getPhpUnitParam(string $paramName): ?string
    {
        global $argv;
        $k = array_search("--$paramName", $argv);
        if (!$k) return null;
        return $argv[$k + 1];
    }
}
