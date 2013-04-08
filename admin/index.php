<?php
	$ms = microtime();

	require_once("../core/settings.php");

	use Core\Template\Template;

	$data = array(
		"title" => "Admin",
		"body" => "This is the admin directory."
	);

	// Process template
	$template = new Template( ninja_root_dir."admin/view/index.tpl" );
	$template->SetVars( $data );
	echo $template->Process();

	echo "\n<!-- Page loaded in: ".round( microtime() - $ms, 5 )." ms -->";