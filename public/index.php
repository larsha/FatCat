<?php
	$ms = microtime();

	require_once("../core/settings.php");

	use Core\Template\Template, Core\Controller\Controller;

	// Load admin
	if( substr( $_SERVER["REQUEST_URI"], 1, 5 ) == "admin" && ninja_enable_admin )
	{
		require_once( "../admin/index.php" );
		exit();
	}

	/** @var $controller \Core\Controller\Controller */
	if( ( $controller = Controller::LoadClassFromURI( $_SERVER["REQUEST_URI"], $_REQUEST ) ) == FALSE )
	{
		header("HTTP/1.0 404 Not Found");
		exit();
	}

	// Process template
	$template = new Template( $controller->GetView() );
	$template->SetVars( $controller->GetData() );
	echo $template->Process();

	echo "\n<!--Page loaded in: ".round( microtime() - $ms, 5 )." ms-->";

	// Print debug data
	if( ninja_debug_mode )
	{
		echo "\nDebug mode is on.\n";

		if( ninja_db_server )
		{
			echo "Database queries:\n";
			foreach( \Core\Db\Connect::Instance()->GetQueries() AS $query )
				echo $query."\n";
		}
	}