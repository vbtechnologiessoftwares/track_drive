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
    public function index(){
    	//https://live-calls-network.trackdrive.com/api/v1/calls?page=1&created_at_from=2024-06-19 00:00:00 UTC&created_at_to=2024-06-20 01:00:00 +0000&buyer_id=10455172

        //?page=1&created_at_from=2024-06-19 00:00:00 UTC&created_at_to=2024-06-19 02:00:00 +0000&buyer_id=10455172
    	$url='https://live-calls-network.trackdrive.com/api/v1/calls';
    	$params=array(
    		'page'=>'1',
    		'created_at_from'=>'2024-06-19 00:00:00 UTC',
    		'created_at_to'=>'2024-06-19 23:59:59 +0000',
    		'buyer_id'=>'10455172',
            'per_page'=>'50',
    	);
        $params['page']='1';
    	$response = Http::withHeaders([
					    'Authorization' => 'Basic dGRwdWI3Y2JiNzNiYjc2MjlhM2VmOWY1NTUzNWQ1MjVhMTJkMzp0ZHBydjkxZTg2Zjc2MmJlZTg4NmNkNWRlZTlhMzg1ZGNmNWM3YjE5MGFmMjM=',
					    //'Accept' => 'application/json',
					])->get($url,$params);
    	$a=$response->json();
        //dd($a);
    	$data['table_data']=$table_data=isset($a['calls'])?$a['calls']:array();

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
            $average_number_of_calls=($total_calls/($total_duration_sum/60));
        }
        if($total_calls!=0){            
            $conversion_rate=($total_converted_calls/$total_calls);
        }
        if($total_converted_calls!=0){            
            $average_payout_price=($payout_sum/$total_converted_calls);
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
            $conversion_rate,
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
