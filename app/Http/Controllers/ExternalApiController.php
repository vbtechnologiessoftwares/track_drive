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
    		'created_at_to'=>'2024-06-19 02:00:00 +0000',
    		'buyer_id'=>'10455172',
    	);
    	$response = Http::withHeaders([
					    'Authorization' => 'Basic dGRwdWI3Y2JiNzNiYjc2MjlhM2VmOWY1NTUzNWQ1MjVhMTJkMzp0ZHBydjkxZTg2Zjc2MmJlZTg4NmNkNWRlZTlhMzg1ZGNmNWM3YjE5MGFmMjM=',
					    //'Accept' => 'application/json',
					])->get($url,$params);
    	$a=$response->json();

    	$data['table_data']=$table_data=isset($a['calls'])?$a['calls']:array();

        $filters='Filters used';
        foreach ($params as $key => $value) {
            //dd($value);
            $filters.= $key.' = '.$value .',';
        }

        //dd($filters);
        $excel_data=array();


        $excel_data[]=array(
            $filters,
        );
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
            '<b>traffic_source_payout</b>',
            '<b>payout</b>',
            '<b>trackdrive_cost</b>',
            '<b>provider_cost</b>',
            '<b>ended_at</b>',
        );

        $i=1;
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



        foreach ($table_data as $key => $value) {
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
                $value['traffic_source_payout'],
                $value['payout'],
                $value['trackdrive_cost'],
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
                '<b>'.$traffic_source_payout_sum.'</b>',
                '<b>'.$payout_sum.'</b>',
                '<b>'.$trackdrive_cost_sum.'</b>',
                '<b>'.$provider_cost_sum.'</b>',
                '',
            );

    	//return Excel::download(new InvoicesExport, 'invoices.xlsx');

        /*$books = [
            ['ISBN', 'title', 'author', 'publisher', 'ctry' ],
            [618260307, 'The Hobbit', 'J. R. R. Tolkien', 'Houghton Mifflin', 'USA'],
            [908606664, 'Slinky Malinki', 'Lynley Dodd', 'Mallinson Rendel', 'NZ']
        ];*/
        $xlsx = SimpleXLSXGen::fromArray( $excel_data );
        $xlsx->mergeCells('A1:S1');
        $xlsx->downloadAs('books.xlsx');

    	//return view('external_api')->with($data);
    	//dd($a);
    }

}
