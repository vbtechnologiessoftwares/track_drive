<!doctype html>
<html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">

    <title>Hello, world!</title>
  </head>
  <body>
    <table class="table">
	    <thead>
	      <tr>
	        <th>#</th>
	        {{-- <th>uuid</th> --}}
	        <th>buyer_id</th>
	        <th>caller_number</th>
	        <th>caller_city</th>
	        <th>caller_country</th>
	        <th>status</th>
	        <th>total_duration</th>
	        <th>hold_duration</th>
	        <th>ivr_duration</th>
	        <th>attempted_duration</th>
	        <th>answered_duration</th>
	        <th>agent_duration</th>
	        <th>revenue</th>
	        <th>traffic_source_converted</th>
	        <th>traffic_source_payout</th>
	        <th>payout</th>
	        <th>trackdrive_cost</th>
	        <th>provider_cost</th>
	        <th>ended_at</th>
	      </tr>
	    </thead>
	    <tbody>
	    	@php
		      $i=1;
		    @endphp
	      	@foreach($table_data as $key =>$value)	      
		      <tr>
		        <td>{{$i++}}</td>
		        {{-- <td>{{$value['uuid']}}</td> --}}
		        <td>{{$value['buyer_id']}}</td>
		        <td>{{$value['caller_number']}}</td>
		        <td>{{$value['caller_city']}}</td>
		        <td>{{$value['caller_country']}}</td>
		        <td>{{$value['status']}}</td>
		        <td>{{$value['total_duration']}}</td>
		        <td>{{$value['hold_duration']}}</td>
		        <td>{{$value['ivr_duration']}}</td>
		        <td>{{$value['attempted_duration']}}</td>
		        <td>{{$value['answered_duration']}}</td>
		        <td>{{$value['agent_duration']}}</td>
		        <td>{{$value['revenue']}}</td>
		        <td>{{$value['traffic_source_converted']}}</td>
		        <td>{{$value['traffic_source_payout']}}</td>
		        <td>{{$value['payout']}}</td>
		        <td>{{$value['trackdrive_cost']}}</td>
		        <td>{{$value['provider_cost']}}</td>
		        <td>{{$value['ended_at']}}</td>
		      </tr>
		    @endforeach
	    </tbody>
	 </table>

    <!-- Option 1: Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>

    <!-- Option 2: Separate Popper and Bootstrap JS -->
    <!--
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js" integrity="sha384-IQsoLXl5PILFhosVNubq5LC7Qb9DXgDA9i+tQ8Zj3iwWAwPtgFTxbJ8NT4GN1R8p" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.min.js" integrity="sha384-cVKIPhGWiC2Al4u+LWgxfKTRIcfu0JTxR+EQDz/bgldoEyl4H0zUF0QKbrJ0EcQF" crossorigin="anonymous"></script>
    -->
  </body>
</html>