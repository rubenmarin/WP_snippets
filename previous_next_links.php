<?php
/*
	Custom Function that modifies the standard previous/next link for single/index templates
	tested on : index.php , single.php
*/
function prevnext__modify( $a , $attr = '' ){
	if(empty($a)) return;	
	$_attr = '';
	if(!empty( $attr ) && is_array($attr)):		
		foreach($attr as $key => $val):
			$_attr[] = "{$key}=\"{$val}\"";
		endforeach;
		$_attr = join(' ', $_attr );
	endif;
	echo preg_replace('/(<a href="(.*)")/i', '$1 '  . $_attr , $a );
}

////////////////////////* index.php */

prevnext__modify( get_previous_posts_link() , 
	$attributes = array(
		'class' => 'button alignleft',
	));

prevnext__modify( get_next_posts_link() , 
	$attributes = array(
		'class' => 'button alignright',
	));

////////////////////// /* SINGLE.php */

prevnext__modify( get_previous_post_link( $format = "<span class=\"alignleft\">&laquo; %link</span>") , 
	$attributes = array(
		'class' => 'button',
	));

prevnext__modify( get_next_post_link( $format = "<span class=\"alignright\">%link &raquo;</span>" ) , 
	$attributes = array(
		'class' => 'button',
	));
