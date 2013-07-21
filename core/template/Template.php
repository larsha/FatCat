<?php
	namespace Core\Template;

	use Core\Form\Form;

	class Template
	{
		private $content;
		private $file;
		private $vars;

		/**
		 * @param $file
		 */
		public function __construct( $file )
		{
			if( !file_exists( $file ) )
				throw new \ErrorException( "Template file $file is missing." );

			$this->file = $file;
			$this->vars = array();
		}

		/**
		 * @param array $vars
		 */
		public function SetVars( array $vars )
		{
			$this->vars = $vars;
		}

		public function Process()
		{
			// Load file contents
			$this->content = file_get_contents( $this->file );

			// Include linked template files
			if( $this->IncludeFile() )
				return $this->content;

			$this->DebugVars();

			// Loop vars and replace content from file
			foreach( $this->vars AS $key => $data )
			{
				// Replace arrays
				if( is_array( $data ) )
				{
					if( $this->IsMultidimensionalArray( $data ) )
						$this->ReplaceMultidimensionalArray( $data, $key );
					else
						$this->ReplaceArray( $key, $data );
				}
				// Replace objects
				elseif( is_object( $data ) )
				{
					$this->ReplaceObject( $key, $data );
				}
				// Replace strings
				else
				{
					$this->ReplaceString( $key, $data );
				}
			}

			// Replaces statements
			$this->ReplaceStatements();

			// Clean up code
			$this->CleanUp();

			return $this->content;
		}

		private function CleanUp()
		{
			// Remove unused variables
			$this->content = preg_replace( "/{{[a-z \.=>]+}}/is", "", $this->content );
		}

		/**
		 * @return array
		 */
		private function GetReplaces()
		{
			$replaces = array();

			if( preg_match_all( "/{{REPLACE ([a-z]+)}}(.*?){{ENDREPLACE}}/is", $this->content, $matches ) )
			{
				foreach( $matches[1] AS $i => $key )
				{
					$replaces[$key] = $matches[2][$i];
				}
			}

			return $replaces;
		}

		/**
		 * @return bool
		 */
		private function IncludeFile()
		{
			if( preg_match( "/import (View\\\[a-z\\\]+)|(\"(.+\.tpl)\")/is", $this->content, $matches ) )
			{
				// Get class hierarchy
				$hierarchy = explode( "\\", $matches[1] );

				$file = ( isset( $matches[3] ) ) ? $matches[3] : "../view/".strtolower( $hierarchy[1] )."/".strtolower( $hierarchy[2] ).".tpl";

				// Process template
				$template = new Template( $file );
				$template->SetVars( array_merge( $this->GetReplaces(), $this->vars ) );
				$this->content = $template->Process();

				return true;
			}

			return false;
		}

		/**
		 * @param string $key
		 * @param array $data
		 */
		private function ReplaceArray( $key, array $data )
		{
			if( !$match = $this->MatchArray( $key ) )
				return;

			$body = "";
			foreach( $data AS $itemKey => $item )
				$body .= preg_replace( "/{{(".$itemKey."\.)?".$match[3]."}}/", $item, $match[1] );

			$this->content = str_ireplace( $match[0], $body, $this->content );
		}

		/**
		 * @param string $content
		 * @return bool|array
		 */
		private function MatchArray( $key, $content = "" )
		{
			if( $content == "" )
				$content = $this->content;

			$re = '% # Match outermost {{FOR}}...{{ENDFOR}} structure.
				{{FOR\ ([a-z]+)\ AS\ (([a-z]+)\ =>\ )?([a-z]+)}}              # Literal start tag.
				(                  # $1: Element contents.
				  (?:              # Zero or more contents alternatives.
					[^{{]*          # Either non-{{FOR}}...{{ENDFOR}} stuff...
					(?:            # Begin {(special normal*)*}.
					  {{           # {special} Tag open literal char,
					  (?!ENDFOR|FOR\ [a-z]+\ AS\ ([a-z]+ =>\ )?[a-z]+}})    # but only if NOT {{FOR}} or {{ENDFOR}}.
					  [^{{]*        # More {normal*}.
					)*             # Finish {(special normal*)*}.
				  | (?R)           # Or a nested {{FOR}}...{{ENDFOR}} structure.
				  )*               # Zero or more contents alternatives.
				)                  # $1: Element contents.
				{{ENDFOR}}            # Literal end tag.
			%x';

			if( !preg_match_all( $re, $content, $matches ) )
				return false;

			foreach( $matches[1] AS $i => $match )
				if( $match == $key )
					return array(
						$matches[0][$i],
						$matches[5][$i],
						$matches[3][$i],
						$matches[4][$i],
					);

			return false;
		}

		/**
		 * @param string $content
		 */
		private function ReplaceMultidimensionalArray( array $datas, $key, $content = "" )
		{
			$match = $this->MatchArray( $key, $content );

			if( is_array( $match ) )
				list( $root, $content, $contentKey, $contentItem ) = $match;
			else
				return;

			$body = "";

			foreach( $datas AS $dataKey => $data )
			{
				$row = "";

				// Replace key
				if( is_string( $dataKey ) )
					$this->ReplaceString( $contentKey, $dataKey );

				// Replace multidimensional array
				if( is_array( $data ) && $this->IsMultidimensionalArray( $data ) )
				{
					$this->ReplaceMultidimensionalArray( $data, $contentItem, $content );
				}
				// Replace simple array
				elseif( is_array( $data ) )
				{
					if( $this->ContainsLoop( $content ) )
					{
						list( $innerRoot, $innerContent, $innerContentKey, $innerContentItem ) = $this->MatchArray( $contentItem, $content );

						$innerBody = "";
						foreach( $data AS $innerData )
							$innerBody .= str_ireplace( "{{".$innerContentItem."}}", $innerData, $innerContent );

						$row = str_ireplace( $innerRoot, $innerBody, $content );
					}

					if( !$row )
						$row = $content;

					foreach( $data AS $itemKey => $item )
						$row = str_ireplace( "{{".$contentItem.".".$itemKey."}}", $item, $row );

					$body .= $row;
				}
				// Replace string
				else
				{
					var_dump( $data );
				}
			}

			$this->content = str_ireplace( $root, $body, $this->content );
		}

		/**
		 * @param string $key
		 * @param object $data
		 */
		private function ReplaceObject( $key, $data )
		{
			if( $data instanceof Form )
				$this->content = str_ireplace( "{{".$key."}}", $data->Generate(), $this->content );
		}

		/**
		 * @return void
		 */
		private function ReplaceStatements()
		{
			if( preg_match_all( "/{{IF ([a-z]+)}}(.*?){{ENDIF}}/is", $this->content, $matches ) )
			{
				foreach( $matches[2] AS $key => $match )
				{
					$items = explode( "{{ELSE}}", $match );

					// No else and var exist
					if( count( $items ) == 1 && array_key_exists( $matches[1][$key], $this->vars ) )
					{
						$this->content = str_ireplace( $matches[0][$key], $items[0], $this->content );
					}
					// Else and var exist
					elseif( array_key_exists( $matches[1][$key], $this->vars ) )
					{
						$this->content = str_ireplace( $matches[0][$key], $items[0], $this->content );
					}
					// Else and no var exist
					elseif( !array_key_exists( $matches[1][$key], $this->vars ) )
					{
						$this->content = str_ireplace( $matches[0][$key], $items[1], $this->content );
					}
				}
			}
		}

		/**
		 * @param string $key
		 * @param string $data
		 */
		private function ReplaceString( $key, $data )
		{
			$this->content = str_ireplace( "{{".$key."}}", $data, $this->content );
		}

		/**
		 * @param string $string
		 * @return bool
		 */
		private function ContainsLoop( $string )
		{
			return (bool)preg_match( '/{{ENDFOR}}/', $string );
		}

		private function DebugVars()
		{
			if( !fatcat_debug_mode )
				return;

			// Add debug data
			$debug = '<div class="alert alert-info">';
			$debug .= "<strong>Debug mode is on.</strong>";

			if( fatcat_db_server )
			{
				$debug .= "<p><strong>Database queries:</strong>";
				$debug .= "<ol>";
				foreach( \Core\Db\Connect::Instance()->GetQueries() AS $query )
					$debug .= "<li>".$query."</li>";
				$debug .= "</ol></p>";
			}

			$debug .= "</div>";

			$this->vars["debug"] = $debug;
		}

		/**
		 * @param array $array
		 * @return bool
		 */
		private function IsMultidimensionalArray( array $array )
		{
			foreach( $array AS $item )
				if( is_array( $item ) )
					return true;

			return false;
		}
	}