<!DOCTYPE html>
<html>
	<head>
		<title>{{title}}</title>
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<link href="/css/bootstrap.min.css" rel="stylesheet">
		<script type="text/javascript" src="/js/jquery-2.0.2.min.js"></script>
	</head>
	<body>
		<div class="container text-center">
			<div>
				<h1>{{title}}</h1>
				<p class="lead">
					{{body}}
				</p>
				<h2>Introducing PHP Fat Cat framework.</h2>
				<p class="lead">
					Three reasons to use the Fat Cat framework.
				</p>
			</div>
			<div class="row">
				<div class="col-4">
					<h3>Lightweight</h3>
					<p>
						Just the stuff you need to get you started.
					</p>
				</div>
				<div class="col-4">
					<h3>Easy to use</h3>
					<p>
						Comes with great frameworks like jQuery and Twitter Bootstrap built in from scratch.
					</p>
				</div>
				<div class="col-4">
					<h3>Fast progress</h3>
					<p>

					</p>
				</div>
			</div>
		</div>

		{{debug}}

		<script type="text/javascript">
			( function(){
				var e = document.createElement( "script" );
				e.type = "text/javascript";
				e.src = "/js/bootstrap.min.js";
				e.async = true;

				var m = document.getElementsByTagName( "head" )[0];
				m.appendChild( e );
			})();
		</script>
	</body>
</html>