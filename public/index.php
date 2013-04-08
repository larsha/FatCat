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
	if( ( $controller = Controller::LoadClassFromURI( $_SERVER["REQUEST_URI"] ) ) == FALSE )
	{
		header("HTTP/1.0 404 Not Found");
		exit();
	}

	// Get class hierarchy
	$hierarchy = $controller->GetClassHierarchy();

	// Process template
	$template = new Template( "../view/".strtolower( $hierarchy[1] )."/".strtolower( $hierarchy[2] ).".tpl" );
	$template->SetVars( $controller->GetData() );
	echo $template->Process();

	echo "\n<!-- Page loaded in: ".round( microtime() - $ms, 5 )." ms -->";