<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Hazard;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthApiTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test API registration success.
     */
    public function test_registration_success(): void
    {
        $response = $this->postJson('/api/auth/register', [
            'name' => 'Mihir Aditya',
            'email' => 'mihir.aditya@gmail.com',
            'password' => 'securepassword123',
        ]);

        $response->assertStatus(201)
                 ->assertJsonStructure([
                     'success',
                     'message',
                     'data' => [
                         'user' => ['id', 'name', 'email', 'role'],
                         'token',
                     ],
                 ]);

        $token = $response->json('data.token');
        $this->assertNotEmpty($token);

        $this->assertDatabaseHas('users', [
            'email' => 'mihir.aditya@gmail.com',
            'remember_token' => $token,
            'role' => 'Citizen',
        ]);
    }

    /**
     * Test API registration validation failure.
     */
    public function test_registration_validation_fails(): void
    {
        $response = $this->postJson('/api/auth/register', [
            'email' => 'invalid-email',
            // Missing name and password
        ]);

        $response->assertStatus(422)
                 ->assertJson([
                     'success' => false,
                     'message' => 'Validation error',
                 ]);
    }

    /**
     * Test API login success.
     */
    public function test_login_success(): void
    {
        // First register a user
        $user = User::create([
            'name' => 'Jaykishan Rawat',
            'email' => 'jksonu1436@gmail.com',
            'password' => bcrypt('password123'),
            'role' => 'Citizen',
        ]);

        $response = $this->postJson('/api/auth/login', [
            'email' => 'jksonu1436@gmail.com',
            'password' => 'password123',
        ]);

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'success',
                     'message',
                     'data' => [
                         'user' => ['id', 'name', 'email', 'role'],
                         'token',
                     ],
                 ]);

        $token = $response->json('data.token');
        $this->assertNotEmpty($token);

        // Check token is saved in database
        $user->refresh();
        $this->assertEquals($token, $user->remember_token);
    }

    /**
     * Test API login fails with incorrect credentials.
     */
    public function test_login_fails_with_wrong_password(): void
    {
        $user = User::create([
            'name' => 'Jaykishan Rawat',
            'email' => 'jksonu1436@gmail.com',
            'password' => bcrypt('password123'),
            'role' => 'Citizen',
        ]);

        $response = $this->postJson('/api/auth/login', [
            'email' => 'jksonu1436@gmail.com',
            'password' => 'wrongpassword',
        ]);

        $response->assertStatus(401)
                 ->assertJson([
                     'success' => false,
                     'message' => 'Invalid email or password',
                 ]);
    }

    /**
     * Test Google registration & login flow.
     */
    public function test_google_login_new_user(): void
    {
        $response = $this->postJson('/api/auth/google-login', [
            'email' => 'google.user@gmail.com',
            'name' => 'Google User',
            'photo_url' => 'https://lh3.googleusercontent.com/a/photo.jpg',
        ]);

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'success',
                     'message',
                     'data' => [
                         'user' => ['id', 'name', 'email', 'role'],
                         'token',
                     ],
                 ]);

        $token = $response->json('data.token');
        $this->assertNotEmpty($token);

        $this->assertDatabaseHas('users', [
            'email' => 'google.user@gmail.com',
            'remember_token' => $token,
            'role' => 'Citizen',
        ]);
    }

    /**
     * Test Google login for existing user.
     */
    public function test_google_login_existing_user(): void
    {
        // Pre-create user
        $user = User::create([
            'name' => 'Old Name',
            'email' => 'google.user@gmail.com',
            'password' => bcrypt('secret'),
            'role' => 'Citizen',
        ]);

        $response = $this->postJson('/api/auth/google-login', [
            'email' => 'google.user@gmail.com',
            'name' => 'Updated Name',
            'photo_url' => null,
        ]);

        $response->assertStatus(200);

        $user->refresh();
        $this->assertEquals('Updated Name', $user->name);
        $this->assertNotEmpty($user->remember_token);
        $this->assertEquals($response->json('data.token'), $user->remember_token);
    }

    /**
     * Test that guest users can view hazards.
     */
    public function test_hazards_list_is_public(): void
    {
        $response = $this->getJson('/api/hazards');
        $response->assertStatus(200)
                 ->assertJsonStructure(['success', 'data']);
    }

    /**
     * Test that hazard reporting requires authentication.
     */
    public function test_store_hazard_requires_token(): void
    {
        $response = $this->postJson('/api/hazards', [
            'category' => 'Pothole',
            'location_name' => 'Kalyan Circle',
            'latitude' => 25.18,
            'longitude' => 75.83,
            'severity' => 'High Risk',
            'description' => 'A large deep pothole on the main lane',
        ]);

        $response->assertStatus(401)
                 ->assertJson([
                     'success' => false,
                     'message' => 'Authorization header is missing.',
                 ]);
    }

    /**
     * Test hazard reporting with invalid token.
     */
    public function test_store_hazard_rejects_invalid_token(): void
    {
        $response = $this->withHeaders([
            'Authorization' => 'Bearer invalidtoken123',
        ])->postJson('/api/hazards', [
            'category' => 'Pothole',
            'location_name' => 'Kalyan Circle',
            'latitude' => 25.18,
            'longitude' => 75.83,
            'severity' => 'High Risk',
            'description' => 'A large deep pothole on the main lane',
        ]);

        $response->assertStatus(401)
                 ->assertJson([
                     'success' => false,
                     'message' => 'Invalid or expired authentication token.',
                 ]);
    }

    /**
     * Test hazard reporting with a valid token.
     */
    public function test_store_hazard_accepts_valid_token(): void
    {
        $user = User::create([
            'name' => 'Mihir Aditya',
            'email' => 'mihir.aditya@gmail.com',
            'password' => bcrypt('password'),
            'role' => 'Citizen',
        ]);
        $user->remember_token = 'myvalidtoken123';
        $user->save();

        $response = $this->withHeaders([
            'Authorization' => 'Bearer myvalidtoken123',
        ])->postJson('/api/hazards', [
            'category' => 'Pothole',
            'location_name' => 'Kalyan Circle',
            'latitude' => 25.18,
            'longitude' => 75.83,
            'severity' => 'High Risk',
            'description' => 'A large deep pothole on the main lane',
        ]);

        $response->assertStatus(201)
                 ->assertJson([
                     'success' => true,
                 ])
                 ->assertJsonStructure([
                     'success',
                     'data' => [
                         'id',
                         'category',
                         'location_name',
                         'latitude',
                         'longitude',
                         'severity',
                         'description',
                     ],
                 ]);

        $this->assertDatabaseHas('hazards', [
            'location_name' => 'Kalyan Circle',
            'description' => 'A large deep pothole on the main lane',
        ]);
    }

    /**
     * Test duplicate hazard reports within 20 meters are merged.
     */
    public function test_store_hazard_merges_duplicates_within_20_meters(): void
    {
        $user = User::create([
            'name' => 'Mihir Aditya',
            'email' => 'mihir.aditya@gmail.com',
            'password' => bcrypt('password'),
            'role' => 'Citizen',
        ]);
        $user->remember_token = 'myvalidtoken123';
        $user->save();

        // 1. Submit first hazard
        $response1 = $this->withHeaders([
            'Authorization' => 'Bearer myvalidtoken123',
        ])->postJson('/api/hazards', [
            'category' => 'Pothole',
            'location_name' => 'First Location',
            'latitude' => 25.18254,
            'longitude' => 75.82736,
            'severity' => 'High Risk',
            'description' => 'Original pothole reported',
            'image_path' => 'hazards/original.jpg'
        ]);

        $response1->assertStatus(201);
        $firstId = $response1->json('data.id');

        // 2. Submit second hazard (11 meters away, same category)
        // 0.0001 latitude difference is roughly 11.1 meters
        $response2 = $this->withHeaders([
            'Authorization' => 'Bearer myvalidtoken123',
        ])->postJson('/api/hazards', [
            'category' => 'Pothole',
            'location_name' => 'Nearby Location',
            'latitude' => 25.18264,
            'longitude' => 75.82736,
            'severity' => 'Medium Risk',
            'description' => 'Pothole reported again by another source',
            'image_path' => 'hazards/evidence.jpg'
        ]);

        $response2->assertStatus(200)
                 ->assertJson([
                     'success' => true,
                     'merged' => true,
                     'data' => [
                         'id' => $firstId,
                         'verification_count' => 1,
                         'image_path' => 'hazards/original.jpg,hazards/evidence.jpg'
                     ]
                 ]);

        // Assert database only has 1 hazard record
        $this->assertEquals(1, Hazard::count());
    }
}
