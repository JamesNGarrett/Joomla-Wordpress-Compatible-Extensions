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

function d($t){echo '<pre>' . print_r($t,1) . '</pre>';}
function dd($t){d($t);die();}

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

/* Joomla Modules Entry Files are only called when they display as any admin or installation etc is done via the xml
 * When the file is called in Joomla two object vars are present, $module (info about the module including the title) and $params (the settings)
 *
 *
*/

if($cms == 'joomla'){
   	$renderer = new ModModuleWidgetRenderer(
   		$module->title,
   		$params->get('content','')
   	);
   	echo $renderer->render();
   	return;
}

class My_Widget extends WP_Widget {

    public function __construct() {
        parent::__construct(
        	'module_widget',
        	'Module Widget Skeleton',
        	['description' => 'Skeleton for a Joomla-Wordpress Module-Widget']
        );
    }
 
 	/* Front end display
 	 * $args = array of info about the widget position and the widget
 	 * $instance = array of data which is stored in the options table in a serialized format
 	 */ 
    public function widget( $args, $instance ) {
    	$title = apply_filters( 'widget_title', $instance['title'] );
    	$title = !empty($title) ? $args['before_title'] . $title . $args['after_title'] : '';
		
		$content = 'Testing Only';

    	$renderer = new ModModuleWidgetRenderer($title,$content,$args);
    	echo $renderer->render();
    	echo 'test';
    }
 
    public function form( $instance ) {
    	
    	$instance['title'] = $instance['title'] ?? 'New Title';

    	$html  = '';
    	$html .= '<p class="hi">';
    	$html .= '<label for="' . $this->get_field_id( "title" ) . '">Title:</label>';
    	$html .= '<input class="widefat" id="' . $this->get_field_id( 'title' ) . '" name="' . $this->get_field_name( 'title' ) . '" type="text" value="' . esc_attr( $instance['title'] ) . '" />';
		$html .= '</p>';

		echo $html;
    }
 
    public function update($new_instance, $old_instance){
        $instance = [];
		$instance['title'] = !empty($new_instance['title']) ? strip_tags($new_instance['title']) : '';
		return $instance;
    }
}

// ====================== Wordpress Init the Widget =============
if($cms == 'wp'){
	add_action( 'widgets_init', 'pf_register_widgets' );

	function pf_register_widgets() {
	    register_widget( 'My_Widget' );
	}
}