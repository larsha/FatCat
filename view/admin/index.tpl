<!DOCTYPE html>
<html>
	<head>
		<title>{{title}}</title>
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<link href="/css/bootstrap.min.css" rel="stylesheet" media="screen">
		<link href="/css/admin.css" rel="stylesheet">
		<script src="/js/jquery-2.0.2.min.js"></script>
	</head>
	<body>
		<div id="site-container" class="container">
			<div class="row">
				<div class="col-2">
					{{IF menu}}
					<ul id="navigation" class="nav nav-pills nav-stacked sidenav">
						<li>
							<a href="/admin">Start</a>
						</li>
						{{FOR menu AS key => items}}
							<li>
								{{key}}
								<ul class="nav nav-pills nav-stacked">
									{{FOR items AS item}}
										<li>
											<a href="{{item.url}}">{{item.title}}</a>
										</li>
									{{ENDFOR}}
								</ul>
							</li>
						{{ENDFOR}}
					</ul>
					{{ENDIF}}

					<a href="/admin/logout" class="btn btn-default">Logout</a>
				</div>
				<div class="col-10">
					<h1>{{title}}</h1>
					<p>
						{{body}}
					</p>
				</div>
			</div>
		</div>

		{{debug}}

		<script type="text/javascript">
			var files = [
				"/js/bootstrap.min.js"
			];

			( function(){
				for( var i = 0; i < files.length; i++ )
				{
					var e = document.createElement( "script" );
					e.type = "text/javascript";
					e.src = files[i];
					e.async = true;

					var m = document.getElementsByTagName( "head" )[0];
					m.appendChild( e );
				}
			})();
		</script>
	</body>
</html>