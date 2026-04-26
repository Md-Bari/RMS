<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('venues', function (Blueprint $table) {
            $table->uuid('venue_id')->primary();
            $table->string('name');
            $table->string('subscription_tier')->default('starter');
            $table->char('currency', 3)->default('USD');
            $table->string('timezone')->default('UTC');
            $table->boolean('is_active')->default(true);
            $table->text('brand_logo_url')->nullable();
            $table->text('welcome_banner')->nullable();
            $table->decimal('service_charge_pct', 5, 2)->default(0);
            $table->timestamps();
        });

        Schema::create('staff_users', function (Blueprint $table) {
            $table->uuid('user_id')->primary();
            $table->foreignUuid('venue_id')->constrained('venues', 'venue_id')->cascadeOnDelete();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('role')->default('manager');
            $table->string('pin_hash')->nullable();
            $table->unsignedTinyInteger('failed_logins')->default(0);
            $table->timestamp('locked_until')->nullable();
            $table->timestamp('invited_at')->nullable();
            $table->timestamp('last_login_at')->nullable();
            $table->timestamps();
        });

        Schema::create('table_units', function (Blueprint $table) {
            $table->uuid('table_id')->primary();
            $table->foreignUuid('venue_id')->constrained('venues', 'venue_id')->cascadeOnDelete();
            $table->string('label');
            $table->string('section')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->unique(['venue_id', 'label']);
        });

        Schema::create('qr_codes', function (Blueprint $table) {
            $table->uuid('qr_id')->primary();
            $table->foreignUuid('table_id')->unique()->constrained('table_units', 'table_id')->cascadeOnDelete();
            $table->foreignUuid('venue_id')->constrained('venues', 'venue_id')->cascadeOnDelete();
            $table->text('code_url');
            $table->boolean('is_active')->default(true);
            $table->timestamp('generated_at')->nullable();
            $table->timestamps();
        });

        Schema::create('menu_categories', function (Blueprint $table) {
            $table->uuid('category_id')->primary();
            $table->foreignUuid('venue_id')->constrained('venues', 'venue_id')->cascadeOnDelete();
            $table->string('name');
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->unique(['venue_id', 'name']);
        });

        Schema::create('menu_items', function (Blueprint $table) {
            $table->uuid('item_id')->primary();
            $table->foreignUuid('venue_id')->constrained('venues', 'venue_id')->cascadeOnDelete();
            $table->foreignUuid('category_id')->constrained('menu_categories', 'category_id')->cascadeOnDelete();
            $table->string('name');
            $table->text('description')->nullable();
            $table->decimal('price', 10, 2);
            $table->unsignedSmallInteger('calories')->default(0);
            $table->decimal('protein_g', 8, 2)->default(0);
            $table->decimal('carbs_g', 8, 2)->default(0);
            $table->decimal('fat_g', 8, 2)->default(0);
            $table->unsignedTinyInteger('health_score')->default(0);
            $table->boolean('is_available')->default(true);
            $table->boolean('admin_adjusted')->default(false);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('menu_item_photos', function (Blueprint $table) {
            $table->uuid('photo_id')->primary();
            $table->foreignUuid('item_id')->constrained('menu_items', 'item_id')->cascadeOnDelete();
            $table->text('s3_url');
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamp('uploaded_at')->nullable();
            $table->timestamps();
        });

        Schema::create('dietary_tags', function (Blueprint $table) {
            $table->uuid('tag_id')->primary();
            $table->string('name')->unique();
            $table->char('color_code', 7)->default('#2F855A');
            $table->timestamps();
        });

        Schema::create('allergens', function (Blueprint $table) {
            $table->uuid('allergen_id')->primary();
            $table->string('name')->unique();
            $table->string('icon_code')->nullable();
            $table->timestamps();
        });

        Schema::create('menu_item_tags', function (Blueprint $table) {
            $table->foreignUuid('item_id')->constrained('menu_items', 'item_id')->cascadeOnDelete();
            $table->foreignUuid('tag_id')->constrained('dietary_tags', 'tag_id')->cascadeOnDelete();
            $table->primary(['item_id', 'tag_id']);
        });

        Schema::create('menu_item_allergens', function (Blueprint $table) {
            $table->foreignUuid('item_id')->constrained('menu_items', 'item_id')->cascadeOnDelete();
            $table->foreignUuid('allergen_id')->constrained('allergens', 'allergen_id')->cascadeOnDelete();
            $table->primary(['item_id', 'allergen_id']);
        });

        Schema::create('customers', function (Blueprint $table) {
            $table->uuid('customer_id')->primary();
            $table->foreignUuid('venue_id')->constrained('venues', 'venue_id')->cascadeOnDelete();
            $table->string('phone_number')->nullable();
            $table->timestamp('created_at')->nullable();
            $table->timestamp('anonymized_at')->nullable();
        });

        Schema::create('orders', function (Blueprint $table) {
            $table->uuid('order_id')->primary();
            $table->foreignUuid('venue_id')->constrained('venues', 'venue_id')->cascadeOnDelete();
            $table->foreignUuid('table_id')->nullable()->constrained('table_units', 'table_id')->nullOnDelete();
            $table->foreignUuid('customer_id')->nullable()->constrained('customers', 'customer_id')->nullOnDelete();
            $table->string('status')->default('ORDER_TAKEN');
            $table->decimal('total_amount', 10, 2)->default(0);
            $table->unsignedSmallInteger('estimated_wait_min')->default(18);
            $table->timestamp('served_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->timestamps();
        });

        Schema::create('order_items', function (Blueprint $table) {
            $table->uuid('order_item_id')->primary();
            $table->foreignUuid('order_id')->constrained('orders', 'order_id')->cascadeOnDelete();
            $table->foreignUuid('item_id')->constrained('menu_items', 'item_id')->restrictOnDelete();
            $table->unsignedInteger('quantity');
            $table->decimal('unit_price', 10, 2);
            $table->text('special_instruction')->nullable();
            $table->timestamps();
        });

        Schema::create('payments', function (Blueprint $table) {
            $table->uuid('payment_id')->primary();
            $table->foreignUuid('order_id')->constrained('orders', 'order_id')->cascadeOnDelete();
            $table->foreignUuid('refund_initiated_by')->nullable()->constrained('staff_users', 'user_id')->nullOnDelete();
            $table->string('stripe_payment_intent_id')->nullable();
            $table->decimal('amount', 10, 2);
            $table->char('currency', 3)->default('USD');
            $table->string('status')->default('PAY_ON_TABLE');
            $table->string('method')->default('CASH');
            $table->timestamp('paid_at')->nullable();
            $table->timestamp('refunded_at')->nullable();
            $table->timestamps();
        });

        Schema::create('receipts', function (Blueprint $table) {
            $table->uuid('receipt_id')->primary();
            $table->foreignUuid('payment_id')->unique()->constrained('payments', 'payment_id')->cascadeOnDelete();
            $table->string('delivery_channel')->default('screen');
            $table->timestamp('delivered_at')->nullable();
            $table->timestamps();
        });

        Schema::create('notifications', function (Blueprint $table) {
            $table->uuid('notif_id')->primary();
            $table->foreignUuid('venue_id')->constrained('venues', 'venue_id')->cascadeOnDelete();
            $table->foreignUuid('order_id')->nullable()->constrained('orders', 'order_id')->cascadeOnDelete();
            $table->string('recipient_type')->default('kitchen');
            $table->string('delivery_method')->default('web');
            $table->text('content_snapshot');
            $table->timestamp('sent_at')->nullable();
            $table->timestamp('re_alert_at')->nullable();
            $table->timestamp('acknowledged_at')->nullable();
            $table->timestamps();
        });

        Schema::create('kds_sessions', function (Blueprint $table) {
            $table->uuid('session_id')->primary();
            $table->foreignUuid('venue_id')->constrained('venues', 'venue_id')->cascadeOnDelete();
            $table->foreignUuid('user_id')->nullable()->constrained('staff_users', 'user_id')->nullOnDelete();
            $table->string('device_fingerprint')->nullable();
            $table->boolean('is_online')->default(true);
            $table->timestamp('last_ping_at')->nullable();
            $table->timestamps();
        });

        Schema::create('offline_queue', function (Blueprint $table) {
            $table->uuid('queue_id')->primary();
            $table->foreignUuid('venue_id')->constrained('venues', 'venue_id')->cascadeOnDelete();
            $table->foreignUuid('order_id')->nullable()->constrained('orders', 'order_id')->cascadeOnDelete();
            $table->json('payload');
            $table->string('status')->default('pending');
            $table->timestamp('created_at')->nullable();
            $table->timestamp('processed_at')->nullable();
        });

        Schema::create('audit_logs', function (Blueprint $table) {
            $table->uuid('log_id')->primary();
            $table->foreignUuid('venue_id')->constrained('venues', 'venue_id')->cascadeOnDelete();
            $table->foreignUuid('actor_user_id')->nullable()->constrained('staff_users', 'user_id')->nullOnDelete();
            $table->string('action_type');
            $table->json('payload')->nullable();
            $table->string('ip_address')->nullable();
            $table->timestamp('performed_at')->nullable();
        });

        Schema::create('sales_reports', function (Blueprint $table) {
            $table->uuid('report_id')->primary();
            $table->foreignUuid('venue_id')->constrained('venues', 'venue_id')->cascadeOnDelete();
            $table->foreignUuid('generated_by')->nullable()->constrained('staff_users', 'user_id')->nullOnDelete();
            $table->string('period_type');
            $table->date('date_from');
            $table->date('date_to');
            $table->string('format')->default('json');
            $table->text('file_url')->nullable();
            $table->timestamp('created_at')->nullable();
        });
    }

    public function down(): void
    {
        foreach ([
            'sales_reports', 'audit_logs', 'offline_queue', 'kds_sessions', 'notifications',
            'receipts', 'payments', 'order_items', 'orders', 'customers', 'menu_item_allergens',
            'menu_item_tags', 'allergens', 'dietary_tags', 'menu_item_photos', 'menu_items',
            'menu_categories', 'qr_codes', 'table_units', 'staff_users', 'venues',
        ] as $table) {
            Schema::dropIfExists($table);
        }
    }
};
