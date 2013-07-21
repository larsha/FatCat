<?php
	namespace Core\Controller;

	use Core\Db\Type;
	use Core\Model\Model;

	abstract class Controller
	{
		final public static function LoadClassFromURI( $uri, $request )
		{
			$url = explode( "?", $_SERVER["REQUEST_URI"] );

			if( $url[0] == "/" )
			{
				$class = "Controller\\Core\\Index";
			}
			else
			{
				// Get requested url
				$controller = explode( "/", $url[0] );

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

			return isset( $class ) ? new $class( $request ) : FALSE;
		}

		/** @var $model Model */
		public $model;
		protected $args;
		protected $view;

		public function __construct( $args = array(), $model = NULL)
		{
			$this->args = $args;

			if( $model !== NULL )
			{
				$this->model = $model;
			}
			else
			{
				list( $catalog, $namespace, $class ) = $this->GetClassHierarchy();

				try
				{
					$classname = "Model\\$namespace\\$class";

					if( class_exists( $classname ) )
						$this->model = new $classname();
				}
				catch( \Exception $e ){}
			}
		}

		/**
		 * @return string
		 */
		final public function GetClassHierarchy()
		{
			return explode( "\\", get_called_class() );
		}

		/**
		 * @return string
		 */
		final public function GetView()
		{
			list( $catalog, $namespace, $class ) = ( $this->view ) ? explode( "\\", $this->view ) : $this->GetClassHierarchy();

			return fatcat_root_dir."view/".strtolower( $namespace )."/".strtolower( $class ).".tpl";
		}

		abstract public function GetData();
	}