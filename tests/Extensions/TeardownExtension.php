<?php

declare(strict_types=1);

namespace Tests\Extensions;

use PHPUnit\Runner\AfterLastTestHook;
use Illuminate\Support\Facades\Artisan;
use Tests\CreatesApplication;

final class TeardownExtension implements AfterLastTestHook
{
    use CreatesApplication;

    public function executeAfterLastTest(): void
    {
        $this->createApplication();
        Artisan::call('custom:drop');
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
