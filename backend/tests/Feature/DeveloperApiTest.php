<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Hazard;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DeveloperApiTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test the government billboards API endpoint.
     */
    public function test_billboards_api_returns_success(): void
    {
        $response = $this->getJson('/api/developer/billboards');

        $response->assertStatus(200)
                 ->assertJson([
                     'success' => true
                 ])
                 ->assertJsonStructure([
                     'success',
                     'count',
                     'data' => [
                         '*' => [
                             'id',
                             'location_name',
                             'latitude',
                             'longitude',
                             'size',
                             'type',
                             'hourly_rate_inr',
                             'status'
                         ]
                     ]
                 ]);
    }

    /**
     * Test the advertisements API endpoint.
     */
    public function test_advertisements_api_returns_success(): void
    {
        $response = $this->getJson('/api/developer/advertisements');

        $response->assertStatus(200)
                 ->assertJson([
                     'success' => true
                 ])
                 ->assertJsonStructure([
                     'success',
                     'count',
                     'data' => [
                         '*' => [
                             'id',
                             'ward',
                             'zone_type',
                             'latitude',
                             'longitude',
                             'daily_average_footfall',
                             'monthly_booking_rate_inr',
                             'availability'
                         ]
                     ]
                 ]);
    }

    /**
     * Test the road notices API endpoint.
     */
    public function test_road_notices_api_returns_success(): void
    {
        $response = $this->getJson('/api/developer/road-notices');

        $response->assertStatus(200)
                 ->assertJson([
                     'success' => true
                 ])
                 ->assertJsonStructure([
                     'success',
                     'count',
                     'data' => [
                         '*' => [
                             'id',
                             'title',
                             'description',
                             'severity',
                             'status',
                             'affected_street',
                             'started_at',
                             'estimated_duration'
                         ]
                     ]
                 ]);
    }

    /**
     * Test the EV Hazards GeoJSON API endpoint.
     */
    public function test_ev_hazards_geojson_returns_success(): void
    {
        // 1. Create a mock active hazard in DB
        $hazard = Hazard::create([
            'category' => 'Pothole',
            'location_name' => 'Main Crossroad',
            'latitude' => 25.18254,
            'longitude' => 75.82736,
            'severity' => 'High Risk',
            'status' => 'Pending',
            'description' => 'Dangerous deep pothole on flyover route.',
            'verification_count' => 5
        ]);

        // 2. Fetch EV hazards GeoJSON
        $response = $this->getJson('/api/developer/ev-hazards');

        $response->assertStatus(200)
                 ->assertJson([
                     'type' => 'FeatureCollection'
                 ])
                 ->assertJsonStructure([
                     'type',
                     'crs' => [
                         'type',
                         'properties' => ['name']
                     ],
                     'features' => [
                         '*' => [
                             'type',
                             'geometry' => [
                                 'type',
                                 'coordinates' => [0, 1] // longitude, latitude
                             ],
                             'properties' => [
                                 'id',
                                 'category',
                                 'severity',
                                 'description',
                                 'status',
                                 'reported_at',
                                 'verification_count'
                             ]
                         ]
                     ]
                 ]);

        $this->assertCount(1, $response->json('features'));
        $this->assertEquals('Pothole', $response->json('features.0.properties.category'));
    }
}
