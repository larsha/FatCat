<?php
	require_once("settings.php");

	use Core\Db\Table;

	/**
	 * @param $resource
	 * @return array
	 */
	function getContents( $resource )
	{
		if( !is_resource( $resource ) )
			return array();

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
		if( !is_dir( $root."/".$model ) )
			continue;

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

			// Check if table exists
			if( Table::Exists( strtolower( $model."_".$table ) ) )
				continue;

			// Create table
			$db = new Table( strtolower( $model."_".$table ) );

			// Loop models fields
			foreach( $instance->Fields() AS $field )
			{
				list( $type, $name, $alias, $args ) = $field;

				$length = isset( $args["length"] ) ? $args["length"] : 0;
				$foreignKey = isset( $args["foreign_key"] ) ? $args["foreign_key"] : "";

				$db->Field( $type, $name, $length, $foreignKey );
			}

			// Query database
			$db->Query();
		}
	}