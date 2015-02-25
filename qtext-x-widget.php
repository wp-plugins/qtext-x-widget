<?php
/*
 * Plugin Name: qText X Widget
 * Version: 2.0
 * Plugin URI: http://blog.evaria.com/wp-content/uploads/2015/02/qtext-x.zip
 * Description: Multilingual Text Widget For Wordpress 4.x.x 
 * Author: Thomas Egtvedt
 * Author URI: evaria.com
 *	Note: This plugins works only with qTranslate-X plugin (http://qtranslatexteam.wordpress.com/about)
 *	License: 
    Copyright 2015  Thomas Egtvedt  (email : tmeweb@gmail.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License version 2, 
    as published by the Free Software Foundation. 
    
    You may NOT assume that you can use any other version of the GPL.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.
    
    The license for this software can likely be found here: 
    http://www.gnu.org/licenses/gpl-2.0.html
 */
class qTextxWidget extends WP_Widget
{
	public $qtext_enabled_langs_num; # enabled languages number
	public $qtext_enabled_langs; # enabled languages @array
	function qTextxWidget()
	{
		if(function_exists('qtranxf_init')) {
			$widget_ops = array('classname' => 'qTextxWidget', 'description' => __( "Multilingual text widget working with qTranslate-X") );
			$control_ops = array('width' => 'auto', 'height' => 'auto');
			$this->WP_Widget('gtexttext', __('qText X Widget'), $widget_ops, $control_ops);
			$this->qtext_enabled_langs = qtranxf_getSortedLanguages(); // get enabled languages
			$this->qtext_enabled_langs_num = count($this->qtext_enabled_langs); // get enabled languages number
		}
	}
	/**
	*	Adds qTranslate's language delimiters to text
	*/
	function qtext_lang_ini($qtext_lang,$qtext_lang_content)
	{
		return "<!--:$qtext_lang-->$qtext_lang_content<!--:-->";
	}
	function widget($args, $instance)
	{
		extract($args);
		$text = empty($instance['text']) ? '' : $instance['text'];		
		echo $before_widget;
		echo $before_title . $instance['lang_title'] . $after_title;	
		echo '<div>' . $instance['lang_text'] .  "</div>";
		echo $after_widget;
	}
	function update($new_instance, $old_instance)
	{
		$instance = $old_instance;
		$instance['lang_title'] = ""; # Clear Old Title
		$instance['lang_text'] = ""; # Clear Old Text
		foreach($this->qtext_enabled_langs as $lng) {
			$instance['lang_title'] .= self::qtext_lang_ini($lng,$new_instance[$lng]);
		}
		foreach($this->qtext_enabled_langs as $lng) {
			$instance['lang_text'] .= self::qtext_lang_ini($lng,$new_instance['text_'.$lng]);
		}
		return $instance;
	}
	function form($instance)
	{
		# check if qTranslate-X installed
		if(!defined("QTX_VERSION")) {
			echo "You Must Enable/Install Qtranslate-X To Use This Plugin";
		}
		else
		{
			$instance = wp_parse_args( (array) $instance, array('title'=>'', 'text'=>'') );	
			$title = $instance['title'];
	        $text = $instance['text'];
			$qtext_parsed_title = qtranxf_split($instance['lang_title'],''); # parse qTranslate-X's lang delimiters from title
			$qtext_parsed_text = qtranxf_split($instance['lang_text'],''); # parse qTranslate-X's lang delimiters from text
			foreach($this->qtext_enabled_langs as $qtext_lang)
			{
				echo '<p><label for="' . $this->get_field_name($qtext_lang) . '">' . __('Title['.$qtext_lang .']') . '</label><br /><input style="width:400px;margin-left:10px;" id="' . $this->get_field_id($qtext_lang) . '" name="' . $this->get_field_name($qtext_lang) . '" type="text" value="' . $qtext_parsed_title[$qtext_lang] . '" /></p>';
				echo '<p><label for="' . $this->get_field_name("text_".$qtext_lang) . '">' . __('Text['.$qtext_lang.']') . '</label><br /><textarea style="width:400px;height:300px;margin-left:10px;" id="' . $this->get_field_id("text_".$qtext_lang) . '" name="' . $this->get_field_name("text_".$qtext_lang) . '">' . $qtext_parsed_text[$qtext_lang] . '</textarea></p>';
			}
		}
	}
}
function qTextxInit() {
	register_widget('qTextxWidget');
}
add_action('widgets_init', 'qTextxInit');
?>