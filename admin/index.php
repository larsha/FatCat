<?php
	$ms = microtime();

	require_once("../core/settings.php");

	use Core\Template\Template;
	use Core\Model\Model;

	$menu = array();
	foreach( Model::GetModelsHierarchy() AS $module => $files )
	{
		foreach( $files AS $file )
		{
			try
			{
				$classname = "Model\\$module\\".str_replace( ".php", "", $file );

				/** @var $class Model */
				$class = new $classname();
				$hierarchy = $class->GetClassHierarchy();

				$menu[$hierarchy[1]][] = $hierarchy[2];
			}
			catch( Exception $e ){}
		}
	}

	print_r($menu);

	$data = array(
		"title" => "Admin",
		"body" => "This is the admin directory.",
		"menu" => $menu
	);

	// Process template
	$template = new Template( ninja_root_dir."admin/view/index.tpl" );
	$template->SetVars( $data );
	echo $template->Process();

	echo "\n<!-- Page loaded in: ".round( microtime() - $ms, 5 )." ms -->";