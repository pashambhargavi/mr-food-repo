<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Create B2B Bulk Inquiries Table
        Schema::create('b2b_inquiries', function (Blueprint $table) {
            $table->id();
            $table->string('company_name');
            $table->string('contact_person');
            $table->string('phone', 30);
            $table->string('email');
            $table->string('occasion_type')->default('corporate_gifting');
            $table->string('estimated_quantity')->default('50-100 boxes');
            $table->date('delivery_date')->nullable();
            $table->string('delivery_location')->default('Hyderabad');
            $table->text('special_requirements')->nullable();
            $table->string('status', 30)->default('new'); // new, contacted, quoted, closed
            $table->timestamps();
        });

        // 2. Add announcement_bar_text to store_settings
        if (Schema::hasTable('store_settings')) {
            Schema::table('store_settings', function (Blueprint $table) {
                if (!Schema::hasColumn('store_settings', 'announcement_bar_text')) {
                    $table->string('announcement_bar_text', 255)
                        ->default('FREE SHIPPING ABOVE RS.1500/- IN PAN INDIA | 100% PURE COW GHEE')
                        ->after('tagline_hi');
                }
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('b2b_inquiries');
        if (Schema::hasTable('store_settings') && Schema::hasColumn('store_settings', 'announcement_bar_text')) {
            Schema::table('store_settings', function (Blueprint $table) {
                $table->dropColumn('announcement_bar_text');
            });
        }
    }
};
