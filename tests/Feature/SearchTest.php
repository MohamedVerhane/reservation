<?php

namespace Tests\Feature;

use App\Models\Hotel;
use App\Models\Room;
use App\Models\RoomType;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SearchTest extends TestCase
{
    use RefreshDatabase;

    public function test_search_page_is_displayed(): void
    {
        $response = $this->get('/search');

        $response->assertStatus(200);
    }

    public function test_search_returns_hotels(): void
    {
        Hotel::factory()->count(3)->create();

        $response = $this->get('/search?search=Hotel');

        $response->assertStatus(200);
    }

    public function test_search_ajax_endpoint_returns_json(): void
    {
        Hotel::factory()->count(3)->create();

        $response = $this->getJson('/search/ajax?search=Hotel');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'html',
                'pagination',
                'total',
                'currentPage',
                'lastPage',
                'hasPages',
            ]);
    }

    public function test_live_search_returns_results(): void
    {
        Hotel::factory()->create(['name' => 'Grand Hotel']);

        $response = $this->getJson('/search/live?q=Grand');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'results' => [
                    '*' => ['id', 'name', 'slug', 'city', 'url'],
                ],
            ]);
    }

    public function test_options_endpoint_returns_filter_data(): void
    {
        Hotel::factory()->count(3)->create();

        $response = $this->getJson('/search/options');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'cities',
                'min_price',
                'max_price',
                'room_types',
                'amenities',
                'star_ratings',
            ]);
    }

    public function test_search_filters_by_city(): void
    {
        Hotel::factory()->create(['city' => 'New York']);
        Hotel::factory()->create(['city' => 'Los Angeles']);

        $response = $this->get('/search?city=New York');

        $response->assertStatus(200);
    }

    public function test_search_filters_by_star_rating(): void
    {
        Hotel::factory()->create(['star_rating' => 5]);
        Hotel::factory()->create(['star_rating' => 3]);

        $response = $this->get('/search?star_rating=5');

        $response->assertStatus(200);
    }
}
