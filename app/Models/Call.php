<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Call extends Model
{
    use HasFactory;
    protected $primaryKey = 'primary_id';
    protected $table = 'calls';
    
    // Specify custom column names
    const CREATED_AT = 'primary_created_at';
    const UPDATED_AT = 'primary_updated_at';

    protected $fillable = [
						    'id',
						    'legacy_id',
						    'type',
						    'uuid',
						    'created_at',
						    'updated_at',
						    'deleted_at',
						    'user_updated_at',
						    'routes_show_path',
						    'routes_edit_path',
						    'external_record_id',
						    'name',
						    'recording_url',
						    'category',
						    'number_called',
						    'number_id',
						    'connected_to',
						    'caller_number',
						    'offer',
						    'user_offer_id',
						    'offer_id',
						    'quality_assurance_user_id',
						    'quality_assurance_name',
						    'quality_assurance_id',
						    'agent_id',
						    'traffic_source',
						    'user_traffic_source_id',
						    'traffic_source_id',
						    'buyer',
						    'user_buyer_id',
						    'buyer_id',
						    'obfuscated_caller_number',
						    'caller_city',
						    'caller_country',
						    'token_values',
						    'total_duration',
						    'hold_duration',
						    'ivr_duration',
						    'attempted_duration',
						    'answered_duration',
						    'agent_duration',
						    'sub_id',
						    'schedule_id',
						    'schedule_name',
						    'ring_pool_id',
						    'status',
						    'buyer_converted',
						    'buyer_repeat_caller',
						    'buyer_revenue',
						    'revenue',
						    'traffic_source_converted',
						    'traffic_source_repeat_caller',
						    'traffic_source_payout',
						    'payout',
						    'trackdrive_cost',
						    'provider_cost',
						    'call_sid',
						    'provider',
						    'outgoing_webhooks_count',
						    'ended_at',
						    'contact_field_type',
						    'disposition_id',
						    'disposition_key',
						    'disposition_name',
						    'disposition_notes',
						    'hangup_cause',
						];
}
