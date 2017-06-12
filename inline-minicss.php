<?php

/*
Examples :
miniCSS::file('style.css');
miniCSS::file('assets/css/some.css');
miniCSS::url('http://some.google.font.com');
*/

class miniCSS{
	
	public static $fileDirectory;

	public static function file( $file = null, $OPTIONS = array() ){
		$file = ltrim( $file , '/');
		$OPTIONS['originalfile'] = "{$file}";

		$miniFail = false;//not used yet
		
		//Makes mini directory , cache file is saved here.

		$fileDirectory = (isset($OPTIONS['filedir'])) ? $OPTIONS['filedir']: get_template_directory();

		$OPTIONS['fileuri'] = (isset($OPTIONS['fileuri'])) ? $OPTIONS['fileuri']: get_template_directory_uri();
		
		$OPTIONS['alwaysrebuild'] = (!isset($OPTIONS['alwaysrebuild'])) ? false : true ;


		$fileDirectory = rtrim( $fileDirectory , '/');

		$miniDir = static::makeDir();

		$filePath = $fileDirectory . DIRECTORY_SEPARATOR . $file;
		
		if(file_exists( $filePath )):

			// check if your mini/path is writable
			$__DIR = $OPTIONS['fileuri'];
			$__DIR = explode('/',$__DIR);
			$__DIR = array_reverse($__DIR);
			$__DIR = $__DIR[0];
			
			$miniFileName = "{$__DIR}/{$file}";
			//$miniFileName = $file;
			
			if( preg_match('/\//', $miniFileName ) ):
				if( preg_match('/\//', $miniFileName ) ):
					$miniFileName = preg_replace('/\//', '%', $miniFileName);
				endif;
			endif;


			$miniFilePath = '';
			
			if($miniDir != false):
				
				$OPTIONS['minifilepath'] = $miniDir . $miniFileName;
				//echo $OPTIONS['minifilepath'];
				if(!file_exists($OPTIONS['minifilepath']) || filemtime($filePath) > filemtime($OPTIONS['minifilepath'])):

					$miniFilePath = static::writeFile( $filePath , $OPTIONS);

				elseif($OPTIONS['alwaysrebuild'] == true):
				
					$miniFilePath = static::writeFile( $filePath , $OPTIONS);
				
				elseif(file_exists($OPTIONS['minifilepath'])):
				
					$miniFilePath = $OPTIONS['minifilepath'];
				
				endif;

			else:
				//if we can't write file then we return theme path
				$miniFilePath = $filePath;
				$miniFail = true;
			endif;
			if(isset($OPTIONS['echo']) && $OPTIONS['echo'] == false):
				return static::writeInline( $miniFilePath , $file );
			else:
				echo static::writeInline( $miniFilePath , $file );
			endif;		
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

			return "<style data-inlinecssdate=\"{$time}\" data-inlinecssname=\"{$originalFile}\">{$css}</style>\n";
		endif;
	}

	public static function writeFile( $filePath = null , $OPTIONS = array() ){
			
		//we get file contents
		$fileContents = file_get_contents($filePath);
		
		if(!isset($OPTIONS['default_searchreplace'])):
			//run our default search/replace
			$fileContents = preg_replace_callback('/url\((.*?)\)/i', function($matches) use ($filePath){
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
		endif;
		
		//run our search and replace if set
		if(isset($OPTIONS['regex']) && is_array($OPTIONS['regex'])):
			$fileContents = static::searchReplace($fileContents , $OPTIONS['regex'] );
		endif;

		//minify
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
		//not used
	}

	public static function url($url = null , $OPTIONS = array()){
		/*
			need to build cache option
		*/
		$output = '';
		$urlCleanName = preg_replace('/^https?:\/\/|www\.|\.|\/|\?|\=|\,|\+|\:/i','',$url);
		$urlCleanName = strtolower($urlCleanName);
		$OPTIONS['urlname'] = $urlCleanName;
		
		if(!isset($OPTIONS['cachetime'])):
			$OPTIONS['cachetime'] = 60 * 60 * 6; //6 hrs
		endif;
		if(!isset($OPTIONS['ie-support'])):
			$OPTIONS['ie-support'] = true;
		endif;
		if(!isset($OPTIONS['echo'])):
			$OPTIONS['echo'] = true;
		endif;

		$inclineCss = static::urlWriteFile($url , $OPTIONS);
		
		if($OPTIONS['ie-support'] == false):
			$output = $inclineCss;
		else:
			$output .= "<!--[if IE]>\n";
			$output .= "<link href=\"{$url}\" rel=\"stylesheet\">\n";
			$output .= "<![endif]-->\n";
			$output .= "<!--[if !IE]><!-->\n";
			$output .= $inclineCss;
			$output .= "<!--<![endif]-->";
		endif;

		if($OPTIONS['echo'] == false): return $output; else: echo $output; endif;
					
	}

	public static function urlWriteFile($url , $OPTIONS){
		$miniDir = static::makeDir();
		$urlCleanName = $OPTIONS['urlname'];
		$filename = "{$miniDir}{$urlCleanName}.css";
		$cachetime = $OPTIONS['cachetime'];
		$filecontents = false;
		$local = true;

		

		if(is_writable($miniDir)):
			//if file doesn't exist = create it
			//if file exist but it's past its cache time = renew it
			if(!file_exists($filename) || (file_exists($filename) && (time() - filemtime($filename)) > $cachetime) ):
				$css = static::file_get_contents($url);//get url contents
				$filetowrite = fopen($filename, "w");//start of write file
				fwrite( $filetowrite ,  $css );//write contents to file	
				fclose( $filetowrite );//close file
			endif;
		else:
			$local = false;
		endif;

		if($local == true):
			$filecontents = file_get_contents($filename);//from file
		else:
			// if no file was created because of permission issues then serve from url
			$filecontents = static::file_get_contents($url);//from url
		endif;
		
		$inclineCss = "<style data-inlinecssurl=\"{$urlCleanName}\">{$filecontents}</style>\n";
		return $inclineCss;
	}

	public static function searchReplace( $file , $regexArr){
		foreach($regexArr as $regexSetting):
			$file = preg_replace( $regexSetting['search'], $regexSetting['replacewith'] , $file); 
		endforeach;
		return $file;
	}

	//
	public static function file_get_contents($url){
		$curl = curl_init();
		curl_setopt($curl, CURLOPT_URL, $url);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_HEADER, false);
		$data = curl_exec($curl);
		curl_close($curl);
		return $data;
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
