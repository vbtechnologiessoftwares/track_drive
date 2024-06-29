<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('calls', function (Blueprint $table) {
            $table->id('primary_id');

            $table->bigInteger('id')->nullable();
            $table->string('legacy_id')->nullable();
            $table->string('type')->nullable();//50
            $table->string('uuid')->nullable();
            $table->string('created_at')->nullable();
            $table->string('updated_at')->nullable();
            $table->string('deleted_at')->nullable();
            $table->string('user_updated_at')->nullable();
            $table->string('routes_show_path')->nullable();
            $table->string('routes_edit_path')->nullable();
            $table->string('external_record_id')->nullable();//doubt
            $table->string('name')->nullable();
            $table->text('recording_url')->nullable();
            $table->string('category')->nullable();//50
            $table->string('number_called')->nullable();
            $table->bigInteger('number_id')->nullable();
            $table->string('connected_to')->nullable();
            $table->string('caller_number')->nullable();
            $table->string('offer')->nullable();
            $table->string('user_offer_id')->nullable();
            $table->bigInteger('offer_id')->nullable();
            $table->string('quality_assurance_user_id')->nullable();
            $table->string('quality_assurance_name')->nullable();
            $table->string('quality_assurance_id')->nullable();
            $table->string('agent_id')->nullable();
            $table->string('traffic_source')->nullable();
            $table->bigInteger('user_traffic_source_id')->nullable();
            $table->bigInteger('traffic_source_id')->nullable();
            $table->string('buyer')->nullable();
            $table->bigInteger('user_buyer_id')->nullable();
            $table->bigInteger('buyer_id')->nullable();
            $table->string('obfuscated_caller_number')->nullable();
            $table->string('caller_city')->nullable();
            $table->string('caller_country')->nullable();
            $table->text('token_values')->nullable();
            $table->decimal('total_duration',total:20,places:0)->nullable();
            $table->decimal('hold_duration',total:20,places:0)->nullable();
            $table->decimal('ivr_duration',total:20,places:0)->nullable();
            $table->decimal('attempted_duration',total:20,places:0)->nullable();
            $table->decimal('answered_duration',total:20,places:0)->nullable();
            $table->decimal('agent_duration',total:20,places:0)->nullable();
            $table->string('sub_id')->nullable();
            $table->string('schedule_id')->nullable();
            $table->string('schedule_name')->nullable();
            $table->string('ring_pool_id')->nullable();
            $table->string('status')->nullable();
            $table->string('buyer_converted')->nullable();
            $table->string('buyer_repeat_caller')->nullable();
            $table->decimal('buyer_revenue',total:20,places:2)->nullable();
            $table->decimal('revenue',total:20,places:2)->nullable();
            $table->string('traffic_source_converted')->nullable();
            $table->string('traffic_source_repeat_caller')->nullable();
            $table->decimal('traffic_source_payout',total:20,places:2)->nullable();
            $table->decimal('payout',total:20,places:2)->nullable();
            $table->decimal('trackdrive_cost',total:20,places:2)->nullable();
            $table->decimal('provider_cost',total:20,places:2)->nullable();
            $table->string('call_sid')->nullable();
            $table->string('provider')->nullable();
            $table->string('outgoing_webhooks_count')->nullable();
            $table->string('ended_at')->nullable();
            $table->string('contact_field_type')->nullable();
            $table->bigInteger('disposition_id')->nullable();
            $table->string('disposition_key')->nullable();
            $table->string('disposition_name')->nullable();
            $table->text('disposition_notes')->nullable();
            $table->text('hangup_cause')->nullable();

             // Custom timestamp columns
            $table->timestamp('primary_created_at')->nullable();
            $table->timestamp('primary_updated_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('calls');
    }
};
