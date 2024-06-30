<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Exports\InvoicesExport;
use Shuchkin\SimpleXLSXGen;
use App\Models\Call;
use Carbon\Carbon;
//use Maatwebsite\Excel\Facades\Excel;

class ExternalApiController extends Controller
{
    public function home(){
        return view('home');
    }
     
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
    private function uniqueTrafficSource($arr){
        $uniqueArray = array_unique($arr);
        $uniqueArrayToString='';
        if(!empty($uniqueArray)) {
            $uniqueArrayToString=implode(',', $uniqueArray);
        }
        return $uniqueArrayToString;
    }
    public function index(){
        //https://live-calls-network.trackdrive.com/api/v1/calls?page=1&created_at_from=2024-06-19 00:00:00 UTC&created_at_to=2024-06-20 01:00:00 +0000&buyer_id=10455172

        //?page=1&created_at_from=2024-06-19 00:00:00 UTC&created_at_to=2024-06-19 02:00:00 +0000&buyer_id=10455172
        $url='https://live-calls-network.trackdrive.com/api/v1/calls';
        $params=array(
            //'id_from'=>'420622705',
            //'page'=>'2',
            //'created_at_from'=>'2024-06-19 00:00:00 UTC',
            //'created_at_to'=>'2024-06-19 23:59:59 +0000',
            //'created_at_from'=>'2024-06-28T21:41:35.977+00:00',
            //'created_at_from'=>'2024-06-29T01:15:51.994+00:00',
            //'created_at_from'=>'2024-06-29T01:15:51.994+00:00',
            //'buyer_id'=>'10455172',
            //'per_page'=>'25',
            //'order'=>'created_at',
            //'order_dir'=>'asc',
            //'cursor'=>'1.7188355534787E+14'
            //'cursor'=>'1.7188255574192E+14'
        );
        //$differenceHours=$this->differenceHours($params);
        $differenceHours=2;
        //dd($differenceHours);
        
        

        
        $response = Http::withHeaders([
                        'Authorization' => 'Basic dGRwdWI3Y2JiNzNiYjc2MjlhM2VmOWY1NTUzNWQ1MjVhMTJkMzp0ZHBydjkxZTg2Zjc2MmJlZTg4NmNkNWRlZTlhMzg1ZGNmNWM3YjE5MGFmMjM=',
                        //'Accept' => 'application/json',
                    ])->get($url,$params);
        $a=$response->json();
        dd($a);
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

        $excel_data_stats[]=array(
            '<b>Avg. number of calls (per hour)</b>',
            '<b>Conversion Rate</b>',
            '<b>Average Payout Price</b>',
            '<b>Average Revenue Per Call</b>',
            '<b>Most Common City</b>',
            '<b>Unique Traffic Sources</b>',
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

        // $final_excel_data=array_merge($excel_data_filters,$excel_data_stats,$excel_data);
        $final_excel_data=array_merge($excel_data_stats,$excel_data);

        $xlsx = SimpleXLSXGen::fromArray( $final_excel_data );
        // $xlsx->mergeCells('A1:S1');
        $xlsx->downloadAs('Analysis-'.$value['buyer_id'].'-'.date('Y-m-d').'.xlsx');

        //return view('external_api')->with($data);
        //dd($a);
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
    //pass array grouped by traffic source in it and return the processed data
    private function trafficWiseDataProcess($arr,$differenceHours){
        $stats_arr=array();//this array returns in the end with whole processed result
        foreach ($arr as $mainKey => $mainValue) {

            $i=1;
            $traffic_source_id=$mainKey;
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
            $traffic_source='';//initialized name if traffic source

            
            

            $average_revenue=0;
            //stats variables ends
            foreach ($mainValue as $key => $value) {                
                $total_calls=$total_calls+1;
                if($value['buyer_converted']=='Converted'){
                    $total_converted_calls=$total_converted_calls+1;
                }
                $city = $value['caller_city'];
                if (!isset($city_counts_arr[$city])) {
                    $city_counts_arr[$city] = 0;
                }
                $city_counts_arr[$city]++;

                $traffic_source=$value['traffic_source'];

                //pushing data in excel_data is no longer required because we only want totals and stats

                //here total calculation starts
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
                //total calculations ends
               $i++; 
            }//foreach $mainValue ends here

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

            $stats_arr[$traffic_source_id]=array(
                'average_number_of_calls'=>$average_number_of_calls,
                'conversion_rate'=>$conversion_rate,
                'average_payout_price'=>$average_payout_price,
                'average_revenue'=>$average_revenue,
                'most_common_cities'=>$most_common_cities,
                'traffic_source'=>$traffic_source
            );
        }//foreach $arr ends here

        return $stats_arr;
        
    }

    //for cities we are checking if every element of array is same , if same then return true else return false
    private function allElementsSame($array){
         // Get the first element of the array
        $first = reset($array);
        // Check if all elements are the same as the first element
        foreach ($array as $value) {
            //dd($value);
            if ($value !== $first) {
                return false;
            }
        }
        return true;
    }

    public function downloadExcel(){
    }


}