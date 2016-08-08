<!doctype html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>Cron Management System</title>
	<style>
		@import url(//fonts.googleapis.com/css?family=Lato:700);

		body {
			margin:0;
			font-family:'Lato', sans-serif;
			text-align:center;
			color: #999;
		}

		.welcome {
			width: 300px;
			height: 200px;
			position: absolute;
			left: 50%;
			top: 50%;
			margin-left: -150px;
			margin-top: -100px;
		}

		a, a:visited {
			text-decoration:none;
		}

		h1 {
			font-size: 32px;
			margin: 16px 0 0 0;
		}
	</style>
	<link href="/bootstrap.css" rel="stylesheet">
	<link href="/font-awesome.css" rel="stylesheet">
	 <script src="/js/jquery-2.1.1.js"></script>
    <script src="/js/bootstrap.min.js"></script>
</head>
<body>
	<div class="wrapper wrapper-content animated fadeInRight">
		<div class="row">
			<div class="col-lg-12">
				<div class="mail-box-header">
	                <h2> Crons </h2>
				</div>
				<table class="table">
					<thead>
						<th>Cron </th>
						<th>Edit</th>
						<th>Delete</th>
						<th></th>
					</thead>
                	<tbody>
                    @if(count($cronData))
	        			@foreach($cronData as $cron)
				            <tr>
					            <td>
					            <div>{{$cron}}</div>
					            </td>
					            <td><a href = "edit/{{$cron}}"></a></td>
					            <td><a href = "delete/{{$cron}}"></a></td>
			            	</tr>
			        	@endforeach
					@else
				        <tr><td colspan='4' id='nocrontoshow' style='width:500px;text-align:center;'>No Cron have been set up for you yet.</td></tr>
					@endif
		            </tbody>
		        </table>               
			</div>	
		</div>
		<div>
		<a href = "addsinglejob" id = "add">Add Job</a>
		<input textbox = "" id = "addjob" value = "">
		</div>
	</body>
</html>
<script>
$("#add").click(function() {
	var val = $("#addjob").attr("value");
});
</script>
