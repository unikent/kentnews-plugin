<?php

MM_Metabox::getInstance('introtext', array(
	'title'=> 'IntroText',
	'hide_title' =>true,
	'context'=>'after_title',
	'mode' => Metamaterial::STORAGE_MODE_EXTRACT,
	'template'=> dirname(__FILE__).'/metaboxes/introtext.php'
));