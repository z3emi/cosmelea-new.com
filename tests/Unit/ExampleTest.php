<?php

namespace Tests\Unit;

use Tests\TestCase;

class ExampleTest extends TestCase
{
    /**
     * A basic test example.
     */
    public function test_homepage_returns_successful_response(): void
    {
        $response = $this->get('/');
        $response->assertStatus(200);
        // Consider adding more assertions to test page content or structure
        // $response->assertSee('Welcome'); // Example: check for text on the homepage
    }
}
