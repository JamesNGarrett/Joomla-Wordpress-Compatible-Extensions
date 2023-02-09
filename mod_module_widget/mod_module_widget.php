<?php

/*
Plugin Name: Module Widget Skeleton
Author: Preflight
Author URI: https://preflight.com.au/
Description: Skeleton for a Joomla-Wordpress Module-Widget
Version: 1.0.0
*/

// ========= Prevent Direct Access and determine the CMS =============
if(defined('_JEXEC')){
	$cms = 'joomla';
} elseif(defined('WPINC')){
	$cms = 'wp';
} else {
	die();
}

// ============ Any General Code should be Placed in here =========
class ModModuleWidgetRenderer
{
	public $title = '';
	public $content = '';
	public $args = [];

	public function __construct($title = '',$content = '',$args = [])
	{
		$this->title = $title;
		$this->content = $content;
		$this->args = $args;
	}

	public function render()
	{
		$html  = '';
		$html .= $this->args['before_widget'] ?? '';
		$html .= $this->args['before_title'] ?? '';
		$html .= $this->title;
		$html .= $this->args['after_title'] ?? '';
		$html .= $this->content;
		$html .= $this->args['after_widget'] ?? '';
		return $html;
	}
}

// ------------ WP -----------------------------------------------

/* Joomla Modules Entry Files are only called when they display 
 * as any admin or installation etc is done via the xml
 * When the file is called in Joomla two object vars are present, 
 * $module (info about the module including the title) and $params (the settings)
*/

if($cms == 'joomla'){
   	$renderer = new ModModuleWidgetRenderer(
   		$module->showtitle ? $module->title : '',
   		$params->get('content','')
   	);
   	echo $renderer->render();
   	return;
}

// ------------ WP -----------------------------------------------

class ModModuleWidget extends WP_Widget {

	private $xml_config = [];

    public function __construct() {

    	$this->xml_config = $this->parseXmlConfig('mod_module_widget.xml');

        parent::__construct(
        	'mod_module_widget',
        	'Module Widget Skeleton',
        	['description' => 'Skeleton for a Joomla-Wordpress Module-Widget']
        );
    }
 
 	/* Front end display
 	 * $args = array of info about the widget position and the widget
 	 * $instance = array of data which is stored in the options table in a serialized format
 	 */ 
    public function widget( $args, $instance ) {
    	$renderer = new ModModuleWidgetRenderer(
    		apply_filters('widget_title', $instance['title']),
    		apply_filters('widget_text', $instance['content']),
    		$args
    	);
    	echo $renderer->render();
    }

 	/* Backend admin form and save
     */ 
 
    public function form( $instance ) {
    	
    	$instance['title'] = $instance['title'] ?? 'New Title';

    	$html  = '';
    	$html .= '<p>';
    	$html .= '<label for="' . $this->get_field_id( "title" ) . '">Title:</label>';
    	$html .= '<input class="widefat" id="' . $this->get_field_id( 'title' ) . '" name="' . $this->get_field_name( 'title' ) . '" type="text" value="' . esc_attr( $instance['title'] ) . '" />';
		$html .= '</p>';
		$html .= '<p>';
		$html .= '<label for="' . $this->get_field_id( "content" ) . '" id="' . $this->get_field_id( "content" ) . '-label">Content</label>';
		$html .= '<textarea id="' . $this->get_field_id( "content" ) . '" class="widefat content" rows="16" cols="20"></textarea>';
		$html .= '</p>';

		echo $html;
    }
 
    public function update($new_instance, $old_instance){
        $instance = [];
		$instance['title'] = !empty($new_instance['title']) ? strip_tags($new_instance['title']) : '';
		$instance['content'] = !empty($new_instance['content']) ? strip_tags($new_instance['content']) : '';
		return $instance;
    }

    private function parseXmlConfig($filename)
    {
    	$xml = simplexml_load_file($filename, "SimpleXMLElement", LIBXML_NOCDATA);
		$json = json_encode($xml);
		return json_decode($json,TRUE);
    }
}

// ====================== Wordpress Init the Widget =============

add_action( 'widgets_init', 'mod_module_widget_register_widgets' );

function mod_module_widget_register_widgets() {
    register_widget( 'ModModuleWidget' );
}
