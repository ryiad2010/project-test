<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Customer;
use App\Models\Order;
use App\Models\SupportTicket;

class CustomerDashboardSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Create the primary Customer (Customer 1)
        // We use firstOrCreate to ensure it doesn't duplicate if you run the seeder twice
        $customer = Customer::firstOrCreate(
            ['email' => 'john.doe@example.com'], // Search by email
            ['name' => 'John Doe']               // Create with this name if not found
        );

        // 2. Create Orders for Customer 1
        // This will give us a 'total_spent' of $350.50
        Order::create([
            'customer_id' => $customer->id,
            'total_price' => 150.00,
        ]);

        Order::create([
            'customer_id' => $customer->id,
            'total_price' => 45.50,
        ]);

        Order::create([
            'customer_id' => $customer->id,
            'total_price' => 155.00,
        ]);

        // 3. Create Support Tickets for Customer 1
        // We will create 2 'open' tickets and 1 'closed' ticket.
        // The dashboard metric should calculate exactly 2 open tickets.
        SupportTicket::create([
            'customer_id' => $customer->id,
            'status' => 'open',
        ]);

        SupportTicket::create([
            'customer_id' => $customer->id,
            'status' => 'open',
        ]);

        SupportTicket::create([
            'customer_id' => $customer->id,
            'status' => 'closed',
        ]);

        // (Optional) Create a second customer just to prove the dashboard
        // strictly isolates data for the specific record you are viewing.
        $customer2 = Customer::firstOrCreate(
            ['email' => 'jane.smith@example.com'],
            ['name' => 'Jane Smith']
        );

        Order::create([
            'customer_id' => $customer2->id,
            'total_price' => 999.99,
        ]);
    }
}
