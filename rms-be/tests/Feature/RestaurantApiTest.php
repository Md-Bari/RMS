<?php

namespace Tests\Feature;

use App\Models\MenuItem;
use App\Models\TableUnit;
use App\Models\Venue;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RestaurantApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_menu_endpoint_returns_frontend_dtos(): void
    {
        $this->seed();

        $this->getJson('/api/menu')
            ->assertOk()
            ->assertJsonStructure([
                'venue' => ['venue_id', 'name', 'currency'],
                'items' => [['id', 'name', 'price', 'category', 'imageUrl', 'available', 'healthScore']],
            ]);
    }

    public function test_customer_can_create_order(): void
    {
        $this->seed();

        $venue = Venue::firstOrFail();
        $table = TableUnit::firstOrFail();
        $item = MenuItem::firstOrFail();

        $this->postJson('/api/orders', [
            'venue_id' => $venue->venue_id,
            'table_id' => $table->table_id,
            'payment_method' => 'CASH',
            'payment_status' => 'PAY_ON_TABLE',
            'items' => [
                ['item_id' => $item->item_id, 'quantity' => 2, 'special_instruction' => 'No onion'],
            ],
        ])
            ->assertCreated()
            ->assertJsonPath('status', 'ORDER_TAKEN')
            ->assertJsonPath('items.0.quantity', 2);
    }
}
