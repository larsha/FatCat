<?php
	require_once("__autoload.php");

	/**
	 * The root directory in the file system tree of Ninja catalog.
	 * Example: /var/www/Ninja/
	 */
	define( "ninja_root_dir", "/Users/rasmusbrandberg/Projects/Ninja/" );

	/**
	 * Database server to connect to.
	 */
	define( "ninja_db_server", "localhost" );

	/**
	 * Valid options are: sqlite, mysqli
	 */
	define( "ninja_db_type", "sqlite" );

	/**
	 * Database name or, if database is type sqlite, the directory of the database file.
	 */
	define( "ninja_db_name", ninja_root_dir."private/db/database.txt" );

	// Initialize database connection
	Core\Db\Connect::Instance()->Initialize();