<?php
	namespace Controller\Core;

	use Core\Controller\Controller;

	class Data extends Controller
	{
		public function __construct( $args = array(), $model )
		{
			parent::__construct( $args, $model );

			$this->view = "View\\Admin\\Data";
		}

		public function GetData()
		{
			$data = $this->model->select->QueryGetData();
			$headers = array();

			if( is_array( $data ) && isset( $data[0] ) )
			{
				foreach( $data[0] AS $key => $value )
					$headers[] = $key;
			}

			return array(
				"data" => $data,
				"headers" => $headers
			);
		}
	}