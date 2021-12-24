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
     * @covers App\Http\Middleware\AcceptableTypeIsJson
     * @test
     */
    public function acceptableTypeIsJson_WithUnacceptableAcceptHeaderType_Returns406()
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

    /**
     * @covers \App\Http\Middleware\SecureResponseHeaders
     * @test
     */
    public function secureResponseHeaders_AnyRequest_ReturnsResponseWithSecureHeaders()
    {
        $response = $this->postJson('/api/register', [
            'data' => [
                //
            ]
        ]);

        $response->assertHeader('Cache-Control', 'no-store, private');
        $response->assertHeader('Content-Security-Policy', "frame-ancestors 'none'");
        $response->assertHeader('Strict-Transport-Security', 'max-age=31536000; includeSubdomains');
        $response->assertHeader('X-Content-Type-Options', 'nosniff');
        $response->assertHeader('X-Frame-Options', 'DENY');
    }
}
