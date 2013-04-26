<!DOCTYPE html>
<html>
	<head>
		<title>{{title}}</title>
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<link href="/css/bootstrap.min.css" rel="stylesheet" media="screen">
		<link href="/css/bootstrap-responsive.min.css" rel="stylesheet">
		<script src="/js/jquery-1.9.1.min.js"></script>
		<style type="text/css">
			ul.sidenav
			{
				top: 40px;
			}
		</style>
	</head>
	<body>
		<ul class="nav nav-list affix sidenav">
			{{FOR menu AS item}}
				<li>
					<a href="">{{item}}</a>
				</li>
			{{ENDFOR}}
		</ul>

		<div class="container">
			<h1>{{title}}</h1>
			<p>
				{{body}}
			</p>
		</div>

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