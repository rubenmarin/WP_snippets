<?php

add_filter('removefromurl/protocol' , function($url = ''){
	$url = preg_replace('(^https?://)', '', $url ); // removes protocol
	return $url;	
},10,1);

add_filter('removefromurl/query' , function($url = ''){
	$url = preg_replace('/\?.*/', '', $url); //removes query
	return $url;	
},10,1);
add_filter('removefromurl/www' , function($url = ''){
	$url = preg_replace('/www./i' , '' , $url ); // removes 'www.' from url
	return $url;	
},10,1);
add_filter('removefromurl/protocol-www' , function($url = ''){
	$url = preg_replace('(^https?://)', '', $url ); // removes protocol
	$url = preg_replace('/www./i' , '' , $url ); // removes www
	return $url;	
},10,1);
add_filter('removefromurl/protocol-www-query' , function($url = ''){
	$url = preg_replace('(^https?://)', '', $url ); // removes protocol
	$url = preg_replace('/www./i' , '' , $url ); // removes wwww
	$url = preg_replace('/\?.*/', '', $url); //removes query
	return $url;	
},10,1);

add_filter('script_loader_tag' , function( $tag , $handle , $src ){
	global $wp_version;
	if($wp_version >= '4.1.0'):
		if(!preg_match('/<!--/i' , $tag )):
				$homeurl  = apply_filters('removefromurl/protocol-www' , home_url() );
				$cleansrc = apply_filters('removefromurl/protocol-www-query' , $src );
				$regexurl = preg_replace('/\//i' , '\/' , $homeurl );
				if(preg_match("/$regexurl/" , $cleansrc )):
					$cleansrc = preg_replace("/$regexurl/" , '' , $cleansrc );
					$filepath = rtrim(ABSPATH , '/') .'/'. ltrim($cleansrc , '/');
					if(file_exists($filepath)):
						$filecontents = file_get_contents($filepath);
						$newtag = "<script class=\"inlinejs_{$handle}\">{$filecontents}</script>";
						return $newtag;
					endif;
				endif;
			return $tag;
		endif;
	endif;
	return $tag;
}, 100 , 3);
