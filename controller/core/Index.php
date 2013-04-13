<?php
	namespace Controller\Core;

	use Core\Controller\Controller, Core\Db\Type;

	class Index extends Controller
	{
		public function GetData()
		{
			return array(
				"title" => "Ninja",
				"body" => "Light weight, easy to use and fast progress with your projects."
			);
		}
	}