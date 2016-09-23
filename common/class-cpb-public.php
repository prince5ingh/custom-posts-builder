<?php

class CPB_PUBLIC {



    function __construct() {
        $this->hooks();
        $this->init();
    }

    /**
     * add common hooks
     * 
     */
    public function hooks() {
        add_action('wp_enqueue_scripts', array($this,'styles_and_scripts') );
        
    }

    function styles_and_scripts() {

    }

    function typecast($data) {

        foreach($data as $key   =>  &$data_value) {
            if ( in_array($data_value,array('true','false')) ) {
                $data_value = filter_var($data_value, FILTER_VALIDATE_BOOLEAN);
            }
        }   

        return $data;
    }

    function add_admin_cols($cpt_object,$admin_cols,$order) {

        $cols = array();
        foreach($admin_cols as $admin_slug => $admin_col) {

            if( isset($admin_col['status']) && $admin_col['status'] != 'off' )
                $cols[$admin_slug] = __($admin_col['label'],CPB_TEXT_DOMAIN);
        }
        $cols = array('cb' => '<input type="checkbox" />') + $cols;
        $cols = array_merge( array_intersect_key(array_flip( $order ),$cols), $cols);
        $cpt_object->columns($cols);
        
        foreach($admin_cols as $admin_slug => $admin_col) {

            if( isset($admin_col['status']) && $admin_col['status'] != 'off' && $admin_col['type'] == 'custom' && $admin_col['content'] != '') {
                $cpt_object->populate_column($admin_slug, function($column, $post) use ($admin_col) {
                    echo cpb_get_col_content(html_entity_decode($admin_col['content']));

                });
            }
        }
    }

    function handle_tax_registration($cpt,$cpt_object) {

        if(trim($cpt['taxonomies']) != '') {

            $taxes = explode(',',$cpt['taxonomies']);

            $cpt_object->filters( apply_filters('cpb_admin_post_filters',$taxes,$cpt) );

            $taxonomies  = cpb_get_taxonomies();

            foreach($taxes as $tax) {

                // check if we have more details for this tax, register with them if available
                if( isset($taxonomies[$tax])) {
                    $tax_labels  = $taxonomies[$tax]['labels'];
                    $tax_options = $taxonomies[$tax]['options'];
                    $cpt_object->register_taxonomy($tax_labels,$tax_options);
                } else {
                    $cpt_object->register_taxonomy($tax);
                }
                
            }
        }

    }

    /**
     * Initialise post types & taxonomies
     */
    public function init() {

        include_once('class-cpt.php');

        $cpts = cpb_get_post_types();

         if( !empty($cpts) ) {
           foreach($cpts as  $cpt) {
                
                $options = $this->typecast($cpt['options']);
                $cpt_object = new CPB_CPT( array_filter($cpt['labels']),array_filter($options));

                $default_cols   = cpb_get_default_admin_cols($cpt);
                $custom_cols    = !empty($cpt['admin_cols'])?$cpt['admin_cols']:array();
                $admin_cols     = (array) array_replace_recursive($default_cols,$custom_cols);
                $order          = isset($cpt['order']) ? $cpt['order'] : array();

                $this->add_admin_cols($cpt_object,$admin_cols,$order);

                $this->handle_tax_registration($cpt,$cpt_object,$order);

            } 
        }
        
    }
        
}

new CPB_PUBLIC();