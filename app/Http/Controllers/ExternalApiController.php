<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Exports\InvoicesExport;
use Shuchkin\SimpleXLSXGen;
//use Maatwebsite\Excel\Facades\Excel;

class ExternalApiController extends Controller
{
    private function differenceHours($params){
        $created_at_from_sec=strtotime($params['created_at_from']);
        $created_at_to_sec=strtotime($params['created_at_to']);
        $created_at_diff_sec=$created_at_to_sec-$created_at_from_sec;

        $hours = floor($created_at_diff_sec / 3600); 
        $minutes = floor(($created_at_diff_sec % 3600) / 60); 
        if($minutes>0){
            return $hours+1;
        }
        return $hours;
    }
    public function index(){
    	//https://live-calls-network.trackdrive.com/api/v1/calls?page=1&created_at_from=2024-06-19 00:00:00 UTC&created_at_to=2024-06-20 01:00:00 +0000&buyer_id=10455172

        //?page=1&created_at_from=2024-06-19 00:00:00 UTC&created_at_to=2024-06-19 02:00:00 +0000&buyer_id=10455172
    	$url='https://live-calls-network.trackdrive.com/api/v1/calls';
    	$params=array(
    		'page'=>'1',
    		'created_at_from'=>'2024-06-19 00:00:00 UTC',
    		'created_at_to'=>'2024-06-19 23:59:59 +0000',
    		'buyer_id'=>'10455172',
            'per_page'=>'25',
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
        for ($i=1; $i <=$total_pages ; $i++) { 
            $params['page']=$i;
            $response1 = Http::withHeaders([
                        'Authorization' => 'Basic dGRwdWI3Y2JiNzNiYjc2MjlhM2VmOWY1NTUzNWQ1MjVhMTJkMzp0ZHBydjkxZTg2Zjc2MmJlZTg4NmNkNWRlZTlhMzg1ZGNmNWM3YjE5MGFmMjM=',
                        //'Accept' => 'application/json',
                    ])->get($url,$params);
            $b=$response1->json();
            $table_data1=isset($b['calls'])?$b['calls']:array();
            //print_r(count($table_data1));
            $table_data=array_merge($table_data,$table_data1);
        }
        //die();
        //return false;
        //dd($table_data);
    	//$table_data=isset($a['calls'])?$a['calls']:array();

        $filters='Filters used : ';
        foreach ($params as $key => $value) {
            //dd($value);
            $filters.= $key.' = '.$value .',';
        }

        //dd($filters);
        $excel_data_filters=array();
        $excel_data_stats=array();
        $excel_data=array();


        $excel_data_filters[]=array(
            $filters,
        );
        $excel_data_filters[]=array();//insert empty row after filters
        $excel_data[]=array(
            '<b>#</b>',
            '<b>buyer_id</b>',
            '<b>caller_number</b>',
            '<b>caller_city</b>',
            '<b>caller_country</b>',
            '<b>status</b>',
            '<b>total_duration</b>',
            '<b>hold_duration</b>',
            '<b>ivr_duration</b>',
            '<b>attempted_duration</b>',
            '<b>answered_duration</b>',
            '<b>agent_duration</b>',
            '<b>revenue</b>',
            '<b>traffic_source_converted</b>',
            //'<b>traffic_source_payout</b>',
            '<b>payout</b>',
            //'<b>trackdrive_cost</b>',
            '<b>provider_cost</b>',
            '<b>ended_at</b>',
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
        //stats variables ends



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
        }

        if($total_duration_sum!=0){            
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
        }
        //print_r(max($city_counts_arr));
        //dd($city_counts_arr);
        //$most_common_city = array_search(max($city_counts_arr), $city_counts_arr);

        $max_count = 0;
        $most_common_city = '';
        $most_common_cities='';
        //dd($this->allElementsSame($city_counts_arr));
        if(!$this->allElementsSame($city_counts_arr)){
            foreach ($city_counts_arr as $city => $count) {
                if ($count > $max_count) {
                    $max_count = $count;
                    $most_common_city = $city;
                }
            }
            $most_common_cities='';
            foreach ($city_counts_arr as $city => $count) {
                if ($count == $max_count) {
                    $most_common_cities.= $city.',';
                }
            }
        }//if all elements value not same

        $excel_data[]=array(
                '<b>Total<b>',
                '',
                '',
                '',
                '',
                '',
                '<b>'.$total_duration_sum.'</b>',
                '<b>'.$hold_duration_sum.'</b>',
                '<b>'.$ivr_duration_sum.'</b>',
                '<b>'.$attempted_duration_sum.'</b>',
                '<b>'.$answered_duration_sum.'</b>',
                '<b>'.$agent_duration_sum.'</b>',
                '<b>'.$revenue_sum.'</b>',
                '',
                //'<b>'.$traffic_source_payout_sum.'</b>',
                '<b>'.$payout_sum.'</b>',
                //'<b>'.$trackdrive_cost_sum.'</b>',
                '<b>'.$provider_cost_sum.'</b>',
                '',
            );

        $excel_data_stats[]=array(
            '<b>average_number_of_calls</b>',
            '<b>conversion_rate</b>',
            '<b>average_payout_price</b>',
            '<b>most_common_city</b>',
        );
        $excel_data_stats[]=array(
            $average_number_of_calls,
            $conversion_rate.'%',
            $average_payout_price,
            $most_common_cities,
        );
        $excel_data_stats[]=array();//insert empty row after stats

        $final_excel_data=array_merge($excel_data_filters,$excel_data_stats,$excel_data);

        $xlsx = SimpleXLSXGen::fromArray( $final_excel_data );
        $xlsx->mergeCells('A1:S1');
        $xlsx->downloadAs('books.xlsx');

    	//return view('external_api')->with($data);
    	//dd($a);
    }
    public function index1(){
        //https://live-calls-network.trackdrive.com/api/v1/calls?page=1&created_at_from=2024-06-19 00:00:00 UTC&created_at_to=2024-06-20 01:00:00 +0000&buyer_id=10455172

        //?page=1&created_at_from=2024-06-19 00:00:00 UTC&created_at_to=2024-06-19 02:00:00 +0000&buyer_id=10455172
        $url='https://live-calls-network.trackdrive.com/api/v1/calls';
        $params=array(
            'page'=>'1',
            //'created_at_from'=>'2024-06-19 00:00:00 UTC',
            //'created_at_to'=>'2024-06-19 01:59:59 +0000',

            'created_at_from'=>'2024-06-19 00:00:00 UTC',
            'created_at_to'=>'2024-06-19 4:59:59 +0000',
            //'buyer_id'=>'10455172',
            'traffic_source_id'=>'10160398',
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
        $excel_data_stats=array();
        $excel_data=array();


        $excel_data_filters[]=array(
            $filters,
        );
        $excel_data_filters[]=array();//insert empty row after filters
        $excel_data[]=array(
            '<b>#</b>',
            '<b>buyer_id</b>',
            '<b>caller_number</b>',
            '<b>caller_city</b>',
            '<b>caller_country</b>',
            '<b>status</b>',
            '<b>total_duration</b>',
            '<b>hold_duration</b>',
            '<b>ivr_duration</b>',
            '<b>attempted_duration</b>',
            '<b>answered_duration</b>',
            '<b>agent_duration</b>',
            '<b>revenue</b>',
            '<b>traffic_source_converted</b>',
            '<b>traffic_source_id</b>',
            '<b>traffic_source</b>',
            //'<b>traffic_source_payout</b>',
            '<b>payout</b>',
            //'<b>trackdrive_cost</b>',
            '<b>provider_cost</b>',
            '<b>ended_at</b>',
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

        $average_revenue=0;
        //stats variables ends



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
            if (!isset($traffic_source_counts_arr[$traffic_source])) {
                $traffic_source_counts_arr[$traffic_source] = 0;
            }
            $traffic_source_counts_arr[$traffic_source]++;


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
        }

        if($total_duration_sum!=0){            
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
            foreach ($city_counts_arr as $city => $count) {
                if ($count > $max_count) {
                    $max_count = $count;
                    $most_common_city = $city;
                }
            }
            $most_common_cities='';
            foreach ($city_counts_arr as $city => $count) {
                if ($count == $max_count) {
                    $most_common_cities.= $city.',';
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

        



        $excel_data[]=array(
                '<b>Total<b>',
                '',
                '',
                '',
                '',
                '',
                '<b>'.$total_duration_sum.'</b>',
                '<b>'.$hold_duration_sum.'</b>',
                '<b>'.$ivr_duration_sum.'</b>',
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

        $excel_data_stats[]=array(
            '<b>average_number_of_calls</b>',
            '<b>conversion_rate</b>',
            '<b>average_payout_price</b>',
            '<b>average_revenue</b>',
            '<b>most_common_city</b>',
            '<b>Unique Traffic</b>',
        );
        $excel_data_stats[]=array(
            $average_number_of_calls,
            $conversion_rate.'%',
            $average_payout_price,
            $average_revenue,
            $most_common_cities,
            $most_common_traffic_sources,
        );
        $excel_data_stats[]=array();//insert empty row after stats

        $final_excel_data=array_merge($excel_data_filters,$excel_data_stats,$excel_data);

        $xlsx = SimpleXLSXGen::fromArray( $final_excel_data );
        $xlsx->mergeCells('A1:S1');
        $xlsx->downloadAs('books.xlsx');

        //return view('external_api')->with($data);
        //dd($a);
    }

    //for cities we are checking if every element of array is same , if same then return true else return false
    private function allElementsSame($array){
         // Get the first element of the array
        $first = reset($array);

        //dd($first);

        // Check if all elements are the same as the first element
        foreach ($array as $value) {
            //dd($value);
            if ($value !== $first) {
                return false;
            }
        }

        return true;
    }

}
