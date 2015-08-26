<?php
/*
	Custom Function that modifies the standard previous/next link for single/index templates
	tested on : index.php , single.php
*/
function prevnext__modify( $a , $attr = '' , $before = null , $after = null ){
	if(empty($a)) return;	
	$_attr = '';
	if(!empty( $attr ) && is_array($attr)):		
		foreach($attr as $key => $val):
			$_attr[] = "{$key}=\"{$val}\"";
		endforeach;
		$_attr = join(' ', $_attr );
	endif;
	if(!empty($before)):
		$a = preg_replace('/>(.*)</i', ">{$before} $1<" , $a );
	endif;
	if(!empty($after)):
		$a = preg_replace('/>(.*)</i', ">$1 {$after}<" , $a );
	endif;
	echo preg_replace('/(<a href="(.*)")/i', '$1 '  . $_attr , $a );
}


/// USE LIKE :
////////////////////////* index.php */

// prevnext__modify( get_previous_posts_link() , 
// 	$attributes = array(
// 		'class' => 'button alignleft',
// 	));

// prevnext__modify( get_next_posts_link() , 
// 	$attributes = array(
// 		'class' => 'button alignright',
//	));

////////////////////// /* SINGLE.php */

// prevnext__modify( get_previous_post_link( $format = "<span class=\"alignleft\">&laquo; %link</span>") , 
// 	$attributes = array(
// 		'class' => 'button',
// 	));

// prevnext__modify( get_next_post_link( $format = "<span class=\"alignright\">%link &raquo;</span>" ) , 
// 	$attributes = array(
// 		'class' => 'button',
// 	));
