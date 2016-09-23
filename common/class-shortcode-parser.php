<?php

CLASS CPB_SHORTCODE_PARSER {

	private $types;

	/**
	 * Constructor
	 */
	function __construct() {

		$this->types 	= $this->get_shortcode_types();
		add_shortcode('cpb_column_data',array($this, 'cpb_column_data') );
		add_action( 'cpb_shortcode_callback_function', array($this, 'function_callback'), 25, 1 );
		add_action( 'cpb_shortcode_callback_meta', array($this, 'meta_callback'), 25, 1 );
		add_action( 'cpb_shortcode_callback_tax', array($this, 'tax_callback'), 25, 1 );

	}

	function get_shortcode_types() {

		//'shortcode'	=>	array of shortcode atts
		$types = array(
			'function'	=>	array('name'	=>	'','args'	=>	''),
			'meta'		=>	array('key'		=>	''),
			'tax'		=>	array('slug'	=>	'')
		);

		return apply_filters('cpb_shortcode_types',$types);
	}

	function cpb_column_data($atts) {

		if( !isset($atts['type']) || $atts['type'] == '' )
			return false;

		$type = $atts['type'];

		$atts = shortcode_atts( $this->get_defaults($atts['type']), $atts );

		$atts = apply_filters('cpb_shortcode_atts',$atts);

		ob_start();

		switch($type) {

			case 'function' :
				do_action('cpb_shortcode_callback_function',$atts);
			break;

			case 'meta' :
				do_action('cpb_shortcode_callback_meta',$atts);
			break;

			case 'tax' :
				do_action('cpb_shortcode_callback_tax',$atts);
			break;

			default :

				do_action('cpb_shortcode_callback_'.sanitize_key($atts['type']) , $atts );
			break;
		}

		return ob_get_clean();

	}

	function get_defaults($type) {

		$defaults = isset($this->types[$type]) ? $this->types[$type] : array();
		return apply_filters('cpb_shortcode_defaults',$defaults,$type);
	}

	function function_callback($atts) {

		if( trim($atts['name'] == '') || !function_exists($atts['name']) )
			return;

		$args 	= $atts['args'] == '' ? false : explode(',',$atts['args']);
		

		if($args) {
			$args	= array_map('trim',$args);
			call_user_func_array($atts['name'],$args);
		} else {
			call_user_func($atts['name']);
		}
	}

	function meta_callback($atts) {

		global $post;

		if( trim($atts['key'] == '') )
			return;

		$key 	= sanitize_key($atts['key']);
		$value 	= get_post_meta($post->ID,$key,true);
		$value 	= maybe_unserialize($value);

		echo implode(' ',(array) $value);

	}

	function tax_callback($atts) {

		if( trim($atts['slug'] == '') )
			return;

		global $post;

		$key 	= sanitize_key($atts['slug']);
		$value 	= wp_get_object_terms( $post->ID, $key, 'slugs' );
		$return = array();
		if( !empty($value) ) {
			foreach($value as $val) {
				$return[] = $val->name;
			}
			echo implode(' ', $return);
		}
		
	}


}


new CPB_SHORTCODE_PARSER();