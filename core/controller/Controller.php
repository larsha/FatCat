<?php
	namespace Core\Controller;

	abstract class Controller
	{
		public static function LoadClassFromURI( $uri )
		{
			if( $uri == "/" )
			{
				$class = "Controller\\Core\\Index";
			}
			else
			{
				// Get requested url
				$controller = explode( "/", $_SERVER["REQUEST_URI"] );

				// Build controller hierarchy
				$controller[0] = "Controller";

				if( !isset( $controller[2] ) || $controller[2][0] == "" || $controller[2][0] == "?" )
					$controller[2] = "Index";

				try
				{
					// Check if class exist
					if( class_exists( implode( "\\", $controller ) ) )
						$class = implode( "\\", $controller );
				}
				catch( \ErrorException $e ){}
			}

			return isset( $class ) ? new $class() : FALSE;
		}

		/**
		 * @return string
		 */
		public function GetClassHierarchy()
		{
			return explode( "\\", get_called_class() );
		}

		abstract public function GetData();
	}