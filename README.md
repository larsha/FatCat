PHP Ninja framework
===================
Lightweight, easy to use and fast progress with your projects.

Quick start
-----------

1.  Publish Ninja-folder to your favourite webserver with PHP installed.
2. 	Edit /core/settings.php
3. 	Point your webserver root to /public
4. 	Now you're ready to use the Ninja framework!

Crash course
------------

<dl>
	<dt>Model</dt>
	<dd>Models har placed in the models directory in a subfolder named by the module you are creating. The model has to be in the Model namespace (Model\Modulename) and extend the Model class from the Core\Model namespace.</dd>
	<dt>View</dt>
	<dd>Views, or template files, are placed in the view directory in a subfolder named by the module you are creating. File name depends on how you name the controller class but the file ending is always ".tpl".</dd>
	<dt>Controller</dt>
	<dd>Controller classes are placed in the controller directory in a subfolder named by the module you are creating. File name is always the same as the contained class. A controller should be in the Controller\Modulename namespace. It should also extend the Controller class from the Core\Controller namespace.</dd>
</dl>

Into the deep
=============

Models
------

Basic model example. This is a file named "article.php" and placed in /model/article/ directory. It also uses a database column called "title":

	namespace Model\Test;

	use Core\Db\Type;
	use Core\Model\Model;

	class Article extends Model
	{
		public function __construct()
		{
			parent::__construct();

			$this->Field( Type::String, "title", array(
				"length" => 255
			) );
		}
	}

When a model is created and fields are added or changed the /core/sync.php script has to run one time to build the database with its tables. In the example above a database table named "article_article" will be created with a column called "title" in varchar(255). All arguments in $this->Field() method are optional.

Arguments in Model::Field() method:
<dl>
	<dt>length</dt>
	<dd>Length will automatically determine the type of your field. The type "String" and no value provided to the length argument will create a field of type text.</dd>
	<dt>foreign_key</dt>
	<dd>Example: "Model\Modulename\Classname". This will automatically create a foreign key of your field linked to the ID field of the model you've provided. Using argument asumes your field is of type "Int".</dd>
</dl>

Database tables are named after the model (modelname_classname). A column named "id" will always be created.

Views
-----

<dl>
	<dt>Variables</dt>
	<dd>{{name}}</dd>
	<dt>One dimensional arrays</dt>
	<dd>{{name.key}}</dd>
	<dt>Loop multidimensional arrays</dt>
	<dd>{{FOR items AS item}} {{ENDFOR}}</dd>
	<dt>Replace variable</dt>
	<dd>{{REPLACE name}} {{ENDREPLACE}}</dd>
	<dt>Import another template</dt>
	<dd>import View\Module\Template</dd>
	<dt>IF statement</dt>
	<dd>{{IF name}}{{ENDIF}}</dd>
	<dt>IF/ELSE statement</dt>
	<dd>{{IF name}} {{ELSE}} {{ENDIF}}</dd>
</dl>

A basic view example. This view uses the Index view from the Core module and replaces the body variable with a loop and prints the articles from the controller example below.

	import View\Core\Index
	
	{{REPLACE body}}
		{{FOR articles AS article}}
			<article>
				<h1>{{article.title}} ({{article.id}})</h1>
			</article>
		{{ENDFOR}}
	{{ENDREPLACE}}

Controllers
-----------

Basic controller example. This controller loads all articles created with the model from the example above and returns an array with the loaded content.

	namespace Controller\Article;

	use Core\Controller\Controller;

	class Index extends Controller
	{
		public function GetData()
		{
			return array(
				"title" => "My list of articles",
				"articles" => $this->model->QueryGetData()
			);
		}
	}

Use another view template file with your controller: Just change the value of the "$this->view"-parameter to the view you would like to use.
Example:

	public function __construct()
	{
		$this->view = "View\\Core\\Index";
	}