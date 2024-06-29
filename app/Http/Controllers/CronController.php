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
        $url='https://live-calls-network.trackdrive.com/api/v1/calls';
        $params=array(
        );
        $response = Http::withHeaders([
                        'Authorization' => 'Basic dGRwdWI3Y2JiNzNiYjc2MjlhM2VmOWY1NTUzNWQ1MjVhMTJkMzp0ZHBydjkxZTg2Zjc2MmJlZTg4NmNkNWRlZTlhMzg1ZGNmNWM3YjE5MGFmMjM=',
                        //'Accept' => 'application/json',
                    ])->get($url,$params);
        $a=$response->json();
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

    public function index2(){
   
        //https://live-calls-network.trackdrive.com/api/v1/calls?page=1&created_at_from=2024-06-19 00:00:00 UTC&created_at_to=2024-06-20 01:00:00 +0000&buyer_id=10455172

        //?page=1&created_at_from=2024-06-19 00:00:00 UTC&created_at_to=2024-06-19 02:00:00 +0000&buyer_id=10455172
        $url='https://live-calls-network.trackdrive.com/api/v1/calls';
        $params=array(
            'page'=>'1',
            //'created_at_from'=>'2024-06-19 00:00:00 UTC',
            //'created_at_to'=>'2024-06-19 01:59:59 +0000',

            'created_at_from'=>'2024-06-19 00:00:00 UTC',
            'created_at_to'=>'2024-06-19 23:59:59 +0000',
            'buyer_id'=>'10455172',
            //'traffic_source_id'=>'10160398',
            'per_page'=>'50',
        );
        $differenceHours=$this->differenceHours($params);
        //dd($differenceHours);
        
        $response = Http::withHeaders([
                        'Authorization' => 'Basic dGRwdWI3Y2JiNzNiYjc2MjlhM2VmOWY1NTUzNWQ1MjVhMTJkMzp0ZHBydjkxZTg2Zjc2MmJlZTg4NmNkNWRlZTlhMzg1ZGNmNWM3YjE5MGFmMjM=',
                        //'Accept' => 'application/json',
                    ])->get($url,$params);
        $a=$response->json();
        //dd($a);
        $total_pages = $a['metadata']['total_pages'];
        $table_data=array();
        /*for ($i=1; $i <=$total_pages ; $i++) { 
            $params['page']=$i;
            $response1 = Http::withHeaders([
                        'Authorization' => 'Basic dGRwdWI3Y2JiNzNiYjc2MjlhM2VmOWY1NTUzNWQ1MjVhMTJkMzp0ZHBydjkxZTg2Zjc2MmJlZTg4NmNkNWRlZTlhMzg1ZGNmNWM3YjE5MGFmMjM=',
                        //'Accept' => 'application/json',
                    ])->get($url,$params);
            $b=$response1->json();
            $table_data1=isset($b['calls'])?$b['calls']:array();
            //print_r(count($table_data1));
            $table_data=array_merge($table_data,$table_data1);
        }*/
        $table_data=isset($a['calls'])?$a['calls']:array();


        //$table_data=isset($a['calls'])?$a['calls']:array();
        //die();
        //return false;

        //$table_data=isset($a['calls'])?$a['calls']:array();
        //dd($table_data);
        //$table_data=isset($a['calls'])?$a['calls']:array();

        $filters='Filters used : ';
        foreach ($params as $key => $value) {
            //dd($value);
            $filters.= $key.' = '.$value .',';
        }

        //dd($filters);
        $excel_data_filters=array();
        $excel_data_buyer_headings=array();
        $excel_data_stats=array();
        $excel_data=array();
        $excel_data_traffic_stats=array();


        $excel_data_filters[]=array(
            $filters,
        );
        $excel_data_filters[]=array();//insert empty row after filters
        $excel_data[]=array(
            '<b>#</b>',
            '<b>Buyer Id</b>',
            '<b>Caller Number</b>',
            '<b>Caller City</b>',
            '<b>Caller Country</b>',
            '<b>Status</b>',
            '<b>Total Duration</b>',
            '<b>Hold Duration</b>',
            '<b>IVR Duration</b>',
            '<b>Attempted Duration</b>',
            '<b>Answered Duration</b>',
            '<b>Agent Duration</b>',
            '<b>Revenue</b>',
            '<b>Traffic Source Converted</b>',
            '<b>Traffic Source Id</b>',
            '<b>Traffic Source</b>',
            //'<b>traffic_source_payout</b>',
            '<b>Payout</b>',
            //'<b>trackdrive_cost</b>',
            '<b>Provider Cost</b>',
            '<b>Call Ended At</b>',
        );

        $i=1;
        

        //for sum starts
        $total_duration_sum=0;
        $hold_duration_sum=0;
        $ivr_duration_sum=0;
        $attempted_duration_sum=0;
        $answered_duration_sum=0;
        $agent_duration_sum=0;
        $revenue_sum=0;

        $traffic_source_payout_sum=0;
        $payout_sum=0;
        $trackdrive_cost_sum=0;
        $provider_cost_sum=0;
        //for sum ends

        //stats variables starts
        $total_calls=0;//total number of calls
        $average_number_of_calls=0;// by default 0,  total number of calls/total_duration_sum

        $total_converted_calls=0; // converted calls
        $conversion_rate=0; // converted calls / total_calls

        $average_payout_price=0;//payout_sum/total_converted_calls

        $city_counts_arr = array();//initialized empty array
        $traffic_source_counts_arr = array();//initialized empty array
        $traffic_source_names_arr = array();//initialized empty array , we will fill names in it
        $traffic_source_group_arr = array();//initialized empty array , key will be traffic source id and grouped data together

        $average_revenue=0;
        //stats variables ends

        $buyer='';//initialized name of buyer



        foreach ($table_data as $key => $value) {

            $total_calls=$total_calls+1;
            if($value['buyer_converted']=='Converted'){
                $total_converted_calls=$total_converted_calls+1;
            }
            $city = $value['caller_city'];
            if (!isset($city_counts_arr[$city])) {
                $city_counts_arr[$city] = 0;
            }
            $city_counts_arr[$city]++;


            $traffic_source = $value['traffic_source'];
            $buyer = $value['buyer'];

            if (!isset($traffic_source_counts_arr[$traffic_source])) {
                $traffic_source_counts_arr[$traffic_source] = 0;
            }
            $traffic_source_counts_arr[$traffic_source]++;

            $traffic_source_names_arr[]=$value['traffic_source'];//pushing names of traffic source
            $traffic_source_group_arr[$value['traffic_source_id']][]=$value;//pushing data grouped by traffic source


            $excel_data[]=array(
                $i,
                $value['buyer_id'],
                $value['caller_number'],
                $value['caller_city'],
                $value['caller_country'],
                $value['status'],
                $value['total_duration'],
                $value['hold_duration'],
                $value['ivr_duration'],
                $value['attempted_duration'],
                $value['answered_duration'],
                $value['agent_duration'],
                $value['revenue'],
                $value['traffic_source_converted'],
                $value['traffic_source_id'],
                $value['traffic_source'],
                //$value['traffic_source_payout'],
                $value['payout'],
                //$value['trackdrive_cost'],
                $value['provider_cost'],
                $value['ended_at'],
            );
            if($value['total_duration']!=""){
                $total_duration_sum=$total_duration_sum+$value['total_duration'];
               
            }
            if($value['hold_duration']!=""){
                $hold_duration_sum=$hold_duration_sum+$value['hold_duration'];
            }
            if($value['ivr_duration']!=""){
                $ivr_duration_sum=$ivr_duration_sum+$value['ivr_duration'];
            }
            if($value['attempted_duration']!=""){
                $attempted_duration_sum=$attempted_duration_sum+$value['attempted_duration'];
            }
            if($value['answered_duration']!=""){
                $answered_duration_sum=$answered_duration_sum+$value['answered_duration'];
            }
            if($value['agent_duration']!=""){
                $agent_duration_sum=$agent_duration_sum+$value['agent_duration'];
            }
            if($value['revenue']!=""){
                $revenue_sum=$revenue_sum+$value['revenue'];
            }
            if($value['traffic_source_payout']!=""){
                $traffic_source_payout_sum=$traffic_source_payout_sum+$value['traffic_source_payout'];
            }
            if($value['payout']!=""){
                $payout_sum=$payout_sum+$value['payout'];
            }
            if($value['trackdrive_cost']!=""){
                $trackdrive_cost_sum=$trackdrive_cost_sum+$value['trackdrive_cost'];
            }
            if($value['provider_cost']!=""){
                $provider_cost_sum=$provider_cost_sum+$value['provider_cost'];
            }
           $i++; 
        }//foreach $table_data ends here

        if($differenceHours!=0){            
            $average_number_of_calls=($total_calls/$differenceHours);
            $average_number_of_calls=round($average_number_of_calls);
        }
        if($total_calls!=0){            
            $conversion_rate=(($total_converted_calls*100)/$total_calls);
            $conversion_rate=round($conversion_rate);
        }
        if($total_converted_calls!=0){            
            $average_payout_price=($payout_sum/$total_converted_calls);
            $average_payout_price=round($average_payout_price);

            $average_revenue=($revenue_sum/$total_converted_calls);
            $average_revenue=round($average_revenue);
        }
        //print_r(max($city_counts_arr));
        //dd($city_counts_arr);
        //$most_common_city = array_search(max($city_counts_arr), $city_counts_arr);

        $max_count = 0;
        $most_common_city = '';
        $most_common_cities='';
        //dd($this->allElementsSame($city_counts_arr));
        if(!$this->allElementsSame($city_counts_arr)){
            foreach ($city_counts_arr as $city => $count)    {
                if ($count > $max_count) {
                    $max_count = $count;
                    $most_common_city = $city;
                }
            }
            $most_common_cities='';
            foreach ($city_counts_arr as $city => $count) {
                if ($count == $max_count) {
                    $most_common_cities.= $city.',';
                    $most_common_cities = trim($most_common_cities, ',');   
                }
            }
        }//if all elements value not same

        $max_traffic_source_count = 0;
        $most_common_traffic_sources='';
        //dd($this->allElementsSame($city_counts_arr));
        if(!$this->allElementsSame($traffic_source_counts_arr)){
            foreach ($traffic_source_counts_arr as $key_traffic_source => $val_traffic_source) {
                if ($val_traffic_source > $max_traffic_source_count) {
                    $max_traffic_source_count = $val_traffic_source;
                    //$most_common_city = $city;
                }
            }
            $most_common_traffic_sources='';
            foreach ($traffic_source_counts_arr as $key_traffic_source => $val_traffic_source) {
                if ($val_traffic_source == $max_traffic_source_count) {
                    $most_common_traffic_sources.= $key_traffic_source.',';
                }
            }
        }//if all elements value not same

        $uniqueTrafficSource=$this->uniqueTrafficSource($traffic_source_names_arr);

        //pass array grouped by traffic source in it and return the processed data
        $traffic_source_wise_result=$this->trafficWiseDataProcess($traffic_source_group_arr,$differenceHours);
        //dd($traffic_source_wise_result);
        



        $excel_data[]=array(
                '<b>Total<b>',
                '',
                '',
                '',
                '',
                '',
                '<b>'.intdiv($total_duration_sum, 60).':'. ($total_duration_sum % 60).'</b>',
                '<b>'.intdiv($hold_duration_sum, 60).':'. ($hold_duration_sum % 60).'</b>',
                '<b>'.intdiv($ivr_duration_sum, 60).':'. ($ivr_duration_sum % 60).'</b>',
                '<b>'.$attempted_duration_sum.'</b>',
                '<b>'.$answered_duration_sum.'</b>',
                '<b>'.$agent_duration_sum.'</b>',
                '<b>'.$revenue_sum.'</b>',
                '',
                '',
                '',
                //'<b>'.$traffic_source_payout_sum.'</b>',
                '<b>'.$payout_sum.'</b>',
                //'<b>'.$trackdrive_cost_sum.'</b>',
                '<b>'.$provider_cost_sum.'</b>',
                '',
            );
        //buyer heading excel starts
            
        $excel_data_buyer_headings[]=array(
            '<style font-size="15">'.
            '<bottom><center>'.
            'BUYER: '.$buyer.
            '</bottom></center>'.
            '</style>'
        );
        $excel_data_buyer_headings[]=array();//insert empty row
        //buyer heading excel ends
        //pushing excel data in buyer stats starts
        $excel_data_stats[]=array(
            '<style font-size="12">'.
            '<bottom><center>'.
            '<b>'.
            'Buyer Stats (From all traffic sources)'.
            '</b>'.
            '</bottom></center>'.
            '</style>'
        );
        $excel_data_stats[]=array(
            '<style font-size="12">'.
            '<center>'.
            '<b>'.
            'Avg. number of calls (per hour)'.
            '</b>'.
            '</center>'.
            '</style>',

            '<style font-size="12">'.
            '<center>'.
            '<b>'.
            'Conversion Rate'.
            '</b>'.
            '</center>'.
            '</style>',

            '<style font-size="12">'.
            '<center>'.
            '<b>'.
            'Average Payout Price'.
            '</b>'.
            '</center>'.
            '</style>',

            '<style font-size="12">'.
            '<center>'.
            '<b>'.
            'Average Revenue Per Call'.
            '</b>'.
            '</center>'.
            '</style>',

            '<style font-size="12">'.
            '<center>'.
            '<b>'.
            'Most Common City'.
            '</b>'.
            '</center>'.
            '</style>',

            '<style font-size="12">'.
            '<center>'.
            '<b>'.
            'Unique Traffic Sources'.
            '</b>'.
            '</center>'.
            '</style>',
        );
        $excel_data_stats[]=array(
            '<center>'.$average_number_of_calls.'</center>',
            '<center>'.$conversion_rate.'%'.'</center>',
            '<center>'.$average_payout_price.'</center>',
            '<center>'.$average_revenue.'</center>',
            '<center>'.$most_common_cities.'</center>',
            '<center>'.$uniqueTrafficSource.'</center>',
        );
        $excel_data_stats[]=array();//insert empty row after stats
        //pushing excel data in buyer stats ends

        //pushing excel data in traffic stats starts
        //$starting_excel_number=8;
        //$modified_starting_number=8;
        //$auto_merge_arr=array();
        $excel_data_traffic_stats[]=array(
            '<style font-size="12">'.
            '<bottom><center>'.
            '<b>'.
            'Buyer Stats (Per traffic sources)'.
            '</b>'.
            '</bottom></center>'.
            '</style>'
        );       
        foreach ($traffic_source_wise_result as $key => $value) {               
            //$auto_merge_arr[]=$starting_excel_number;
            //$modified_starting_number=$modified_starting_number+4;
            //$auto_merge_arr[]=$modified_starting_number;

            $excel_data_traffic_stats[]=array(
                '<style font-size="12">'.
                '<center>'.
                '<b>'.
                $value['traffic_source'].
                '</b>'.
                '</center>'.
                '</style>'
            );

            $excel_data_traffic_stats[]=array(
                '<style font-size="12">'.
                '<center>'.
                '<b>'.
                'Avg. number of calls (per hour)'.
                '</b>'.
                '</center>'.
                '</style>',

                '<style font-size="12">'.
                '<center>'.
                '<b>'.
                'Conversion Rat'.
                '</b>'.
                '</center>'.
                '</style>',

                '<style font-size="12">'.
                '<center>'.
                '<b>'.
                'Average Payout Price'.
                '</b>'.
                '</center>'.
                '</style>',

                '<style font-size="12">'.
                '<center>'.
                '<b>'.
                'Average Revenue Per Call'.
                '</b>'.
                '</center>'.
                '</style>',

                '<style font-size="12">'.
                '<center>'.
                '<b>'.
                'Most Common City'.
                '</b>'.
                '</center>'.
                '</style>',
            );
            $excel_data_traffic_stats[]=array(
                '<center>'.$value['average_number_of_calls'].'</center>',
                '<center>'.$value['conversion_rate'].'%'.'</center>',
                '<center>'.$value['average_payout_price'].'</center>',
                '<center>'.$value['average_revenue'].'</center>',
                '<center>'.$value['most_common_cities'].'</center>',
            );
            $excel_data_traffic_stats[]=array();//insert empty row
            //$traffic_source_wise_result_sr_no=$traffic_source_wise_result_sr_no+1;
        }//foreach $traffic_source_wise_result
        
        //pushing excel data in traffic stats ends

        // $final_excel_data=array_merge($excel_data_filters,$excel_data_stats,$excel_data);
        $final_excel_data=array_merge(
            $excel_data_buyer_headings,
            $excel_data_stats,
            $excel_data_traffic_stats,
            $excel_data
        );

        $xlsx = SimpleXLSXGen::fromArray( $final_excel_data );
        $xlsx->mergeCells('A1:C1'); //merge buyer name
        $xlsx->mergeCells('A3:D3');//merge buyer stats heading
        $xlsx->mergeCells('A7:D7');//merge buyer stats per traffic source heading
        //dd($traffic_source_wise_result);
        $start_auto_merge_number=8;
        foreach ($traffic_source_wise_result as $key => $value) {
            $merge='A'.$start_auto_merge_number.':D'.$start_auto_merge_number;
            $xlsx->mergeCells($merge);//merge buyer stats per traffic source name
            $start_auto_merge_number=$start_auto_merge_number+4;

        }
        $xlsx->downloadAs('Analysis-'.$params['buyer_id'].'-'.date('Y-m-d').'.xlsx');

        //return view('external_api')->with($data);
        //dd($a);
    }


}
