<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\Call;

class CronController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
    	$current_time_stamp=date('Y-m-d H:i:s');

    	$unix_before=strtotime($current_time_stamp)-(30*60);
    	$ended_at_from=date('Y-m-d H:i:s',$unix_before);
    	$ended_at_to=$current_time_stamp;

        $url='https://live-calls-network.trackdrive.com/api/v1/calls';
        $params=array(
        	'ended_at_from'=>$ended_at_from,
        	'ended_at_to'=>$ended_at_to,
        );
        $response = Http::withHeaders([
                        'Authorization' => 'Basic dGRwdWI3Y2JiNzNiYjc2MjlhM2VmOWY1NTUzNWQ1MjVhMTJkMzp0ZHBydjkxZTg2Zjc2MmJlZTg4NmNkNWRlZTlhMzg1ZGNmNWM3YjE5MGFmMjM=',
                        //'Accept' => 'application/json',
                    ])->get($url,$params);
        $a=$response->json();
        //dd($a);
        $table_data=isset($a['calls'])?$a['calls']:array();

        foreach ($table_data as $key => $value) {        	      	
    		$create_data=array(
        		'id'=>$value['id'],
	            'legacy_id'=>$value['legacy_id'],
	            'type'=>$value['type'],
	            'uuid'=>$value['uuid'],
	            'created_at'=>$value['created_at'],
	            'updated_at'=>$value['updated_at'],
	            'deleted_at'=>$value['deleted_at'],
	            'user_updated_at'=>$value['user_updated_at'],

	            'routes_show_path'=>$value['routes_show_path'],
	            'routes_edit_path'=>$value['routes_edit_path'],
	            'external_record_id'=>$value['external_record_id'],
	            'name'=>$value['name'],
	            'recording_url'=>$value['recording_url'],
	            'category'=>$value['category'],
	            'number_called'=>$value['number_called'],
	            'number_id'=>$value['number_id'],
	            'connected_to'=>$value['connected_to'],
	            'caller_number'=>$value['caller_number'],

	            'offer'=>$value['offer'],
	            'user_offer_id'=>$value['user_offer_id'],
	            'offer_id'=>$value['offer_id'],
	            'quality_assurance_user_id'=>$value['quality_assurance_user_id'],
	            'quality_assurance_name'=>$value['quality_assurance_name'],
	            'quality_assurance_id'=>$value['quality_assurance_id'],
	            'agent_id'=>$value['agent_id'],
	            'traffic_source'=>$value['traffic_source'],
	            'user_traffic_source_id'=>$value['user_traffic_source_id'],
	            'traffic_source_id'=>$value['traffic_source_id'],
	            'buyer'=>$value['buyer'],

	            'user_buyer_id'=>$value['user_buyer_id'],
	            'buyer_id'=>$value['buyer_id'],
	            'obfuscated_caller_number'=>$value['obfuscated_caller_number'],
	            'caller_city'=>$value['caller_city'],
	            'caller_country'=>$value['caller_country'],
	            'token_values'=>$value['token_values'],
	            'total_duration'=>$value['total_duration'],
	            'hold_duration'=>$value['hold_duration'],
	            'ivr_duration'=>$value['ivr_duration'],
	            'attempted_duration'=>$value['attempted_duration'],
	            'answered_duration'=>$value['answered_duration'],
	            'agent_duration'=>$value['agent_duration'],
	            'sub_id'=>$value['sub_id'],

	            'schedule_id'=>$value['schedule_id'],
	            'schedule_name'=>$value['schedule_name'],
	            'ring_pool_id'=>$value['ring_pool_id'],
	            'status'=>$value['status'],
	            'buyer_converted'=>$value['buyer_converted'],
	            'buyer_repeat_caller'=>$value['buyer_repeat_caller'],
	            'buyer_revenue'=>$value['buyer_revenue'],
	            'revenue'=>$value['revenue'],
	            'traffic_source_converted'=>$value['traffic_source_converted'],
	            'traffic_source_repeat_caller'=>$value['traffic_source_repeat_caller'],
	            'traffic_source_payout'=>$value['traffic_source_payout'],
	            'payout'=>$value['payout'],
	            'trackdrive_cost'=>$value['trackdrive_cost'],
	            'provider_cost'=>$value['provider_cost'],
	            'call_sid'=>$value['call_sid'],

	            'provider'=>$value['provider'],
	            'outgoing_webhooks_count'=>$value['outgoing_webhooks_count'],
	            'ended_at'=>$value['ended_at'],
	            'contact_field_type'=>$value['contact_field_type'],
	            'disposition_id'=>$value['disposition_id'],
	            'disposition_key'=>$value['disposition_key'],
	            'disposition_name'=>$value['disposition_name'],
	            'disposition_notes'=>$value['disposition_notes'],
	            'hangup_cause'=>$value['hangup_cause'],
        	);//create_data array
        	$check_count=Call::where('id',$value['id'])->count();
        	if($check_count==0){
        		Call::create($create_data);
        	}else{
        		Call::where('id',$value['id'])->update($create_data);
        	}
        }//foreach ends here

    }

    public function index1(Request $request)
    {
    	if($request->input('ended_at_from')=="" || $request->input('ended_at_to')=="")
    	{
    		return 'Parameters not defined';
    	}
        $url='https://live-calls-network.trackdrive.com/api/v1/calls';
        $params=array(
        	'ended_at_from'=>$request->input('ended_at_from'),
        	'ended_at_to'=>$request->input('ended_at_to'),
        );
        $response = Http::withHeaders([
                        'Authorization' => 'Basic dGRwdWI3Y2JiNzNiYjc2MjlhM2VmOWY1NTUzNWQ1MjVhMTJkMzp0ZHBydjkxZTg2Zjc2MmJlZTg4NmNkNWRlZTlhMzg1ZGNmNWM3YjE5MGFmMjM=',
                        //'Accept' => 'application/json',
                    ])->get($url,$params);
        $a=$response->json();
        dd($a);
        $table_data=isset($a['calls'])?$a['calls']:array();

        foreach ($table_data as $key => $value) {        	      	
    		$create_data=array(
        		'id'=>$value['id'],
	            'legacy_id'=>$value['legacy_id'],
	            'type'=>$value['type'],
	            'uuid'=>$value['uuid'],
	            'created_at'=>$value['created_at'],
	            'updated_at'=>$value['updated_at'],
	            'deleted_at'=>$value['deleted_at'],
	            'user_updated_at'=>$value['user_updated_at'],

	            'routes_show_path'=>$value['routes_show_path'],
	            'routes_edit_path'=>$value['routes_edit_path'],
	            'external_record_id'=>$value['external_record_id'],
	            'name'=>$value['name'],
	            'recording_url'=>$value['recording_url'],
	            'category'=>$value['category'],
	            'number_called'=>$value['number_called'],
	            'number_id'=>$value['number_id'],
	            'connected_to'=>$value['connected_to'],
	            'caller_number'=>$value['caller_number'],

	            'offer'=>$value['offer'],
	            'user_offer_id'=>$value['user_offer_id'],
	            'offer_id'=>$value['offer_id'],
	            'quality_assurance_user_id'=>$value['quality_assurance_user_id'],
	            'quality_assurance_name'=>$value['quality_assurance_name'],
	            'quality_assurance_id'=>$value['quality_assurance_id'],
	            'agent_id'=>$value['agent_id'],
	            'traffic_source'=>$value['traffic_source'],
	            'user_traffic_source_id'=>$value['user_traffic_source_id'],
	            'traffic_source_id'=>$value['traffic_source_id'],
	            'buyer'=>$value['buyer'],

	            'user_buyer_id'=>$value['user_buyer_id'],
	            'buyer_id'=>$value['buyer_id'],
	            'obfuscated_caller_number'=>$value['obfuscated_caller_number'],
	            'caller_city'=>$value['caller_city'],
	            'caller_country'=>$value['caller_country'],
	            'token_values'=>$value['token_values'],
	            'total_duration'=>$value['total_duration'],
	            'hold_duration'=>$value['hold_duration'],
	            'ivr_duration'=>$value['ivr_duration'],
	            'attempted_duration'=>$value['attempted_duration'],
	            'answered_duration'=>$value['answered_duration'],
	            'agent_duration'=>$value['agent_duration'],
	            'sub_id'=>$value['sub_id'],

	            'schedule_id'=>$value['schedule_id'],
	            'schedule_name'=>$value['schedule_name'],
	            'ring_pool_id'=>$value['ring_pool_id'],
	            'status'=>$value['status'],
	            'buyer_converted'=>$value['buyer_converted'],
	            'buyer_repeat_caller'=>$value['buyer_repeat_caller'],
	            'buyer_revenue'=>$value['buyer_revenue'],
	            'revenue'=>$value['revenue'],
	            'traffic_source_converted'=>$value['traffic_source_converted'],
	            'traffic_source_repeat_caller'=>$value['traffic_source_repeat_caller'],
	            'traffic_source_payout'=>$value['traffic_source_payout'],
	            'payout'=>$value['payout'],
	            'trackdrive_cost'=>$value['trackdrive_cost'],
	            'provider_cost'=>$value['provider_cost'],
	            'call_sid'=>$value['call_sid'],

	            'provider'=>$value['provider'],
	            'outgoing_webhooks_count'=>$value['outgoing_webhooks_count'],
	            'ended_at'=>$value['ended_at'],
	            'contact_field_type'=>$value['contact_field_type'],
	            'disposition_id'=>$value['disposition_id'],
	            'disposition_key'=>$value['disposition_key'],
	            'disposition_name'=>$value['disposition_name'],
	            'disposition_notes'=>$value['disposition_notes'],
	            'hangup_cause'=>$value['hangup_cause'],
        	);//create_data array
        	$check_count=Call::where('id',$value['id'])->count();
        	if($check_count==0){
        		Call::create($create_data);
        	}else{
        		Call::where('id',$value['id'])->update($create_data);
        	}
        }//foreach ends here

    }

    


}
