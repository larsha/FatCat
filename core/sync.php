<?php
	require_once("settings.php");

	use Core\Db\Table;

	/**
	 * @param $resource
	 * @return array
	 */
	function getContents( $resource )
	{
		$contents = array();

		while( ( $dir = readdir( $resource ) ) !== false )
		{
			if( $dir == "." || $dir == ".." )
				continue;

			$contents[] = $dir;
		}

		return $contents;
	}

	// Get all model dirs
	$root = ninja_root_dir."model";

	$models = getContents( opendir( $root ) );

	// Loop model dirs
	foreach( $models AS $model )
	{
		// Get model dirs content
		$files = getContents( opendir( $root."/".$model ) );

		// Loop content
		foreach( $files AS $file )
		{
			// Remove suffix and create namespace string
			$table = str_replace( ".php", "", $file );
			$class = "Model\\$model\\$table";

			/** @var $instance \Core\Model\Model */
			$instance = new $class();

			// Create table
			$db = new Table( strtolower( $model."_".$table ) );

			// Loop models fields
			foreach( $instance->Fields() AS $field )
			{
				list( $type, $name, $length ) = $field;

				$db->Field( $type, $name, $length );
			}

			// Query database
			$db->Query();
		}
	}