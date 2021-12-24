<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class HeadersTest extends TestCase
{
    use WithFaker;

    /**
     * @test
     */
    public function serverWantsJSON_WithUnacceptableAcceptHeaderType_Returns406()
    {
        $response = $this->withHeaders([
            'Accept' => 'text/html'
        ])->post('/api/register', [
            'data' => [
                //
            ]
        ]);

        $response->assertStatus(Response::HTTP_NOT_ACCEPTABLE);
    }
}
