<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\Shop;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ContactUniquenessTest extends TestCase
{
    use RefreshDatabase;

    public function test_customer_cannot_reuse_an_admin_email_or_phone(): void
    {
        $user = User::factory()->create(['email' => 'admin@example.com', 'phone' => '+221700000000']);
        $shop = Shop::create(['name' => 'Test Shop', 'slug' => 'test-shop']);
        $shop->users()->attach($user, ['role' => 'owner']);

        $response = $this->actingAs($user)->post(route('customers.store', $shop), [
            'name' => 'Client test',
            'email' => 'admin@example.com',
            'phone' => '+221700000000',
        ]);

        $response->assertSessionHasErrors(['email', 'phone']);
        $this->assertDatabaseCount('customers', 0);
    }

    public function test_admin_cannot_reuse_a_customer_email_or_phone(): void
    {
        $user = User::factory()->create();
        $shop = Shop::create(['name' => 'Test Shop', 'slug' => 'test-shop']);
        Customer::create([
            'shop_id' => $shop->id,
            'name' => 'Client test',
            'email' => 'client@example.com',
            'phone' => '+221711111111',
        ]);

        $response = $this->post(route('register.store'), [
            'name' => 'Admin test',
            'email' => 'client@example.com',
            'phone' => '+221711111111',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response->assertSessionHasErrors(['email', 'phone']);
        $this->assertDatabaseCount('users', 1);
    }
}
