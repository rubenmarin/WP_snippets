<?php

class cssMini{
	
	public static $fileDirectory;

	public static function file( $file = null, $OPTIONS = array() ){
		$file = ltrim( $file , '/');
		$OPTIONS['originalfile'] = "{$file}";
		
		//Makes mini directory , cache file is saved here.

		$fileDirectory = (isset($OPTIONS['filedir'])) ? $OPTIONS['filedir']: get_template_directory();

		$OPTIONS['fileuri'] = (isset($OPTIONS['fileuri'])) ? $OPTIONS['fileuri']: get_template_directory_uri();
		
		$fileDirectory = rtrim( $fileDirectory , '/');

		$miniDir = static::makeDir();

		$filePath = $fileDirectory . DIRECTORY_SEPARATOR . $file;
		
		if(file_exists( $filePath )):

			// check if your mini/path is writable
			$miniFileName = $file;
			if( preg_match('/\//', $miniFileName ) ):
				if( preg_match('/\//', $miniFileName ) ):
					$miniFileName = preg_replace('/\//', '%', $file);
				endif;
			endif;


			$miniFilePath = '';
			if($miniDir != false):
				
				$OPTIONS['minifilepath'] = $miniDir . $miniFileName;

				if(!file_exists($OPTIONS['minifilepath']) || filemtime($filePath) > filemtime($OPTIONS['minifilepath'])):

					$miniFilePath = static::writeFile( $filePath , $OPTIONS);
				
				elseif(file_exists($OPTIONS['minifilepath'])):
				
					$miniFilePath = $OPTIONS['minifilepath'];
				
				endif;

			else:
				//if we can't write file then we return theme path
				$miniFilePath = $filePath;
			endif;

			echo static::writeInline( $miniFilePath , $file );
		
		endif;
	}
	
	public static function compress($csscontents = null){
		$csscontents = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $csscontents);
		/* remove tabs, spaces, newlines, etc. */
		$csscontents = str_replace(array("\r\n", "\r", "\n", "\t", '  ', '    ', '    '), '', $csscontents);
		return trim($csscontents);
	}

	public static function writeInline( $filePath = null , $originalFile = null , $OPTIONS = array() ){
		if(file_exists($filePath)):
			$time = date ("F d Y H:i", filemtime($filePath) );
			
			$css = file_get_contents($filePath);

			echo "<style data-inlinecssdate=\"{$time}\" data-inlinecssname=\"{$originalFile}\">{$css}</style>";
		endif;
	}

	public static function writeFile( $filePath = null , $OPTIONS = array() ){
			
			//we get file contents
			$fileContents = file_get_contents($filePath);
			
			//run our default search/replace
			$fileContents = preg_replace_callback('/url\((.*)\)/i', function($matches) use ($filePath){
				if(isset($matches[1])):
					$matches[1] = trim($matches[1], '"');
					$matches[1] = trim($matches[1], "'");

						if(preg_match('/^.*.\.\//i' , $matches[1])):
							
							preg_match('/^.*.\.\//i' , $matches[1] , $tree);

							$mainfile = preg_replace('/^.*.\.\//i' ,'',$matches[1]);
							$mainfile = ltrim($mainfile , '/');
							
							$upLevel = realpath(dirname($filePath) . '/'.rtrim($tree[0],'/'));
							$upLevel = str_replace( ABSPATH , '' , $upLevel );
							$upLevel = rtrim($upLevel , '/');

							$path = ltrim($upLevel . '/' . $mainfile,'/');
							$fileurl = home_url($path);
							
							return 'url("'.$fileurl.'")';

						elseif(preg_match('/^\.\//i' , $matches[1])):
							$mainfile = preg_replace('/^\.\//i' ,'',$matches[1]);
														
							$path = realpath(dirname($filePath));
							$path = str_replace( ABSPATH , '' , $path );
							$path = rtrim($path,'/') .'/'. ltrim($mainfile,'/');
							$path = ltrim($path,'/');
							
							$fileurl = home_url($path);
							
							return 'url("'.$fileurl.'")';

						else:
						
							$path = realpath(dirname($filePath));
							$path = str_replace( ABSPATH , '' , $path );
							$path = rtrim($path,'/') .'/'. ltrim($matches[1],'/');
							$path = ltrim($path,'/');
							
							$fileurl = home_url($path);
							
							return 'url("'.$fileurl.'")';
						
						endif;
				else:
					return $matches[0];
				endif;
				
			},$fileContents);
			
			//run our search and replace if set
			if(isset($OPTIONS['regex']) && is_array($OPTIONS['regex'])):
				$fileContents = static::searchReplace($fileContents , $OPTIONS['regex'] );
			endif;

			$fileContents = static::compress($fileContents);

			//create file
			$filetowrite = fopen($OPTIONS['minifilepath'], "w");
			//write contents to file		
			fwrite( $filetowrite , $fileContents );
			//close file		
			fclose( $filetowrite );

			//when done return path;
			return $OPTIONS['minifilepath'];

	}

	public static function getFile($filePath){

	}

	public static function url($url = null){
			$css = file_get_contents($url);
			echo "<style data-inlinecssurl=\"{$url}\">{$css}</style>";
	}

	public static function searchReplace( $file , $regexArr){
		foreach($regexArr as $regexSetting):
			$file = preg_replace( $regexSetting['search'], $regexSetting['replacewith'] , $file); 
		endforeach;
		return $file;
	}

	public static function makeDir(){
		$upload_dir = wp_upload_dir(); 
		$baseupload_dir = $upload_dir['basedir'];
		if( !file_exists($baseupload_dir . '/mini/css/') && is_writable($baseupload_dir) ):
			mkdir($baseupload_dir . '/mini/css/' , 0775 , true );	
		elseif(!is_writable($baseupload_dir)):
			return false;
		endif;
		return $baseupload_dir . '/mini/css/';
	}
}
