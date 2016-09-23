<?php

class CPB_ADMIN_SETTINGS {


    /*
     * @var CPB_ADMIN_SETTINGS instance
     * @since 1.0
     */
    private static $instance;

    /**
     * Main Instance
     *
     * @staticvar   array   $instance
     * @return      The one true instance
     */
    public static function instance() {
        if ( ! isset( self::$instance ) ) {
             self::$instance = new self;
            self::$instance->admin_hooks();
        }
 
        return self::$instance;
    }

    /**
     * add admin specific hooks
     * 
     */
    public function admin_hooks() {

        add_action('admin_menu', array($this,'add_menu') );
        add_action('admin_enqueue_scripts', array($this,'admin_styles_and_scripts') );
        add_action('wp_ajax_cpb_taxonomy_create', array($this,'cpb_taxonomy_create') );
        add_action('wp_ajax_cpb_cpt_create', array($this,'cpb_cpt_create') );
        add_action('wp_ajax_cpb_cpt_update', array($this,'cpb_cpt_update') );
        add_action('wp_ajax_cpb_tax_update', array($this,'cpb_tax_update') );
        add_action('wp_ajax_show_cpb_cpt_list', array($this,'show_cpb_cpt_list') );
        add_action('wp_ajax_show_cpb_tax_list', array($this,'show_cpb_tax_list') );
        add_action('wp_ajax_cpb_process_action', array($this,'cpb_process_action') );
        add_action('wp_ajax_cpb_process_tax_action', array($this,'cpb_process_tax_action') );
        add_action('wp_ajax_show_admin_cols_ui', array($this,'show_admin_cols_ui') );
        add_action('wp_ajax_show_admin_filter_ui', array($this,'show_admin_filter_ui') );

        add_action('init',array($this, 'cpb_handle_tools_form') );
    }

    function admin_styles_and_scripts() {

        /** javascript */
        wp_enqueue_script('jquery');
        wp_enqueue_script('jquery-ui-core');
        wp_enqueue_script('jquery-effects-core');
        wp_enqueue_script('jquery-ui-sortable');
        wp_enqueue_script(CPB_SLUG.'-helptip-script',CPB_PLUGIN_URL.'assets/tipso.min.js');
        wp_enqueue_media();
        wp_enqueue_script( 'tinymce_js', includes_url( 'js/tinymce/' ) . 'wp-tinymce.php', array( 'jquery' ), false, true );
        wp_enqueue_script(CPB_SLUG.'-admin-script',CPB_PLUGIN_URL.'assets/admin.js');


        /** Stylesheets */
        wp_enqueue_style(CPB_SLUG.'-helptip-style',CPB_PLUGIN_URL.'assets/tipso.min.css');
        wp_enqueue_style(CPB_SLUG.'-admin-style',CPB_PLUGIN_URL.'assets/admin.css');

    }


    /**
     * add cpb menus
     */
    public function add_menu() {

        add_menu_page(
            __('Custom Posts Builder',CPB_TEXT_DOMAIN),
            __('Custom Posts Builder',CPB_TEXT_DOMAIN),
            apply_filters(CPB_PREFIX.'admin_menu_capability','manage_options'),
            CPB_SLUG,
            array($this,'welcome'),
            'dashicons-portfolio',
            '26.9'
        );

        $submenus = $this->get_sub_menus();

        foreach($submenus as $submenu) {

            add_submenu_page(CPB_SLUG,$submenu['page_title'],$submenu['menu_title'],$submenu['capability'],$submenu['menu_slug'],$submenu['callable']);
        }
    }

    /**
     * returns array of all sub menu items
     * @return [type] [description]
     */
    function get_sub_menus(){
        $submenus = array(
            CPB_SLUG.'create_cpt'   =>  array(
                'page_title'  =>  __('CPB',CPB_TEXT_DOMAIN),
                'menu_title'  =>  __('CPB',CPB_TEXT_DOMAIN),
                'capability'  =>  apply_filters(CPB_PREFIX.'post_types_menu_capability','manage_options'),   
                'menu_slug'   =>  CPB_SLUG,
                'callable'    =>  array($this,'post_types_page')    
            ),
            CPB_SLUG.'create_tax'   =>  array(
                'page_title'  =>  __('Tools',CPB_TEXT_DOMAIN),
                'menu_title'  =>  __('Tools',CPB_TEXT_DOMAIN),
                'capability'  =>  apply_filters(CPB_PREFIX.'tools_menu_capability','manage_options'),   
                'menu_slug'   =>  CPB_PREFIX.'tools',
                'callable'    =>  array($this,'tools')    
            )
        );
        return apply_filters(CPB_PREFIX.'admin_submenus',$submenus);
    }

    /**
     * renders main menu page
     * 
     */
    public function welcome() {
         
    }

    public function post_types_page() {
         include_once(CPB_PLUGIN_PATH.'admin/view/crud.php');
    }

    public function tools() {
         include_once(CPB_PLUGIN_PATH.'admin/view/tools.php');
    }

    /**
     * Creates custom post type as per user configuration
     * @since 1.0
     */
    public function cpb_cpt_create() {

        if( cpb_cpt_creation_allowed() ) {

            // sanitize whole post array at once
            $post_data  = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);

            // check if post name is given and doesnt already exists
            if( isset($post_data['labels']['post_type_name']) &&  in_array($post_data['labels']['post_type_name'], get_post_types( '', 'names' ) ) ) {

                // post already exists . terminate with message
                cpb_message('warning','this post type already exits.');
            }

            $post_data = apply_filters('cpb_pre_create_cpt',$post_data);
 
            // save post type 
            if( cpb_save_post_type($post_data) ) {
                cpb_message('yes','post type created successfully !.');
            } else {
                cpb_message('no','oops! something went wrong');
            }

        }

    }

    /**
     * Creates custom taxonomy as per user configuration
     * @since 1.0
     */
    public function cpb_taxonomy_create() {

        if( cpb_cpt_creation_allowed() ) {

            // sanitize whole post array at once
            $post_data  = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);

            // check if post name is given and doesnt already exists
            if( isset($post_data['labels']['taxonomy_name']) &&  in_array($post_data['labels']['taxonomy_name'], get_taxonomies( '', 'names' ) ) ) {

                // taxonomy already exists . terminate with message
                cpb_message('warning','this taxonomy already exits.');
            }

            $post_data = apply_filters('cpb_pre_create_taxonomy',$post_data);
 
            // save post type 
            if( cpb_save_taxonomy($post_data) ) {
                cpb_message('yes','taxonomy created successfully !.');
            } else {
                cpb_message('no','oops! something went wrong');
            }

        }

    }


    /**
     * Updates custom post type as per user configuration
     * @since 1.0
     */
    public function cpb_cpt_update() {

        if( cpb_cpt_creation_allowed() ) {

            // sanitize whole post array at once
            $post_data  = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
            
            $post_data = apply_filters('cpb_pre_update_cpt',$post_data);
 
            // save post type 
            if( cpb_save_post_type($post_data) ) {
                cpb_message('yes','post type updated successfully !.');
            } else {
                cpb_message('no','oops! something went wrong');
            }

        }

    }

    /**
     * Updates custom post type as per user configuration
     * @since 1.0
     */
    public function cpb_tax_update() {

        if( cpb_cpt_creation_allowed() ) {

            // sanitize whole post array at once
            $post_data  = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);

            $post_data = apply_filters('cpb_pre_update_tax',$post_data);
 
            // save post type 
            if( cpb_save_taxonomy($post_data) ) {
                cpb_message('yes','taxonomy updated successfully !.');
            } else {
                cpb_message('no','oops! something went wrong');
            }

        }

    }




    function show_cpb_cpt_list() {

        show_cpb_cpt_list();
    }

    function show_cpb_tax_list() {

        show_cpb_tax_list();
    }

    function cpb_process_action() {

        // sanitize whole post array at once
        $post_data  = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);

        if( !cpb_cpt_creation_allowed() ) {
            cpb_message('no',__('You are not authorised for this operation',CPB_TEXT_DOMAIN) );
        }

        if( isset($post_data['cpb_action']) && isset($post_data['cpt']) ) {

            $action = sanitize_text_field($post_data['cpb_action']);
            $cpt    = sanitize_text_field($post_data['cpt']);

            $post_types = cpb_get_post_types();

            switch($action) {

                case 'edit' :

                    $cpt = cpb_get_post_type($cpt);
                    wp_die( json_encode($cpt) );

                break;

                case 'no' :

                    if( cpb_delete_post_type($cpt) )
                        wp_die('success');

                break;
            }

        }

    }

    function cpb_process_tax_action() {

        // sanitize whole post array at once
        $post_data  = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);

        if( !cpb_cpt_creation_allowed() ) {
            cpb_message('no',__('You are not authorised for this operation',CPB_TEXT_DOMAIN) );
        }

        if( isset($post_data['cpb_action']) && isset($post_data['tax']) ) {

            $action = sanitize_text_field($post_data['cpb_action']);
            $tax    = sanitize_text_field($post_data['tax']);

            $taxonomies = cpb_get_taxonomies();

            switch($action) {

                case 'edit' :

                    $tax = cpb_get_taxonomy($tax);
                    wp_die( json_encode($tax) );

                break;

                case 'no' :

                    if( cpb_delete_taxonomy($tax) )
                        wp_die('success');

                break;
            }

        }

    }

    function cpb_handle_tools_form() {

        if( !isset($_GET['page']) || $_GET['page'] != 'cpb_tools' || !isset($_POST['cpb_tools_submit'])  )
            return;

        if( !cpb_cpt_creation_allowed() ) {
            wp_die( __('you are not authorised for this operation'), CPB_TEXT_DOMAIN );
        }

        // sanitize post array
        $post_data  = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
        $taxonomies = cpb_get_taxonomies();
        $post_types = cpb_get_post_types();

        $tab    = isset($_GET['tab']) ? sanitize_text_field($_GET['tab']) : 'import'; // default is import

        switch($tab) {

            case 'export':
                $export = array();
                
                if( !empty($taxonomies) ) {
                    foreach($taxonomies as $name    =>  $data) {
                        if( !in_array($name,$post_data['options']['export_taxes']) ) {
                            unset($taxonomies['name']);
                        }
                    }
                    $export['taxes'] = $taxonomies;
                }
                
                if( !empty($post_types) ) {
                    foreach($post_types as $name    =>  $data) {
                        if( !in_array($name,$post_data['options']['export_cpts']) ) {
                            unset($post_types['name']);
                        }
                    }
                    $export['cpts'] = $post_types;
                }

                header('Content-Description: File Transfer');
                header('Content-Type: application/octet-stream');
                header('Content-Disposition: attachment; filename=cpb-export.txt');
                header('Content-Transfer-Encoding: binary');
                header('Expires: 0');
                header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
                header('Pragma: public');
                ob_clean();
                flush();
                echo cpb_serialize($export);
                die;
            break;

            case 'import':

                if( trim($post_data['cpb_import']) == '')
                    return;

                $imported_data = cpb_unserialize($post_data['cpb_import']);

                $post_types = cpb_serialize( array_merge($imported_data['cpts'],$post_types) );
                update_option('cpb_post_types',$post_types);

                $taxonomies = cpb_serialize( array_merge($imported_data['taxes'],$taxonomies) );
                $status = update_option('cpb_taxonomies',$taxonomies);

                add_action('cpb_import_status',array($this,'cpb_import_status'),$status);

            break;
        }

    }

    function cpb_import_status($status) {

        if($status) {
            cpb_message('yes','Import process completed successfully');
        }
    }

    function show_admin_cols_ui() {


        echo '<span class="cpb-msg">'.__("This is a pro feature").'<span class="cpb-pro-only">'.$this->get_pro_link().'</span></span>';
        
        ?>
    <div class="cpb-fancy-bg cpb-admin-cols-ui">
        <img src="<?php echo CPB_PLUGIN_URL.'assets/ss/admin-cols.jpg'; ?>">
    </div> <?php
        if ( is_admin() && defined( 'DOING_AJAX' ) && DOING_AJAX ) {
            
            wp_die();
        }

    }

    function show_admin_filter_ui() {

       echo '<span class="cpb-msg">'.__("This is a pro feature").'<span class="cpb-pro-only">'.$this->get_pro_link().'</span></span>';
        
        ?>
        <div class="cpb-fancy-bg cpb-admin-cols-ui">
            <img src="<?php echo CPB_PLUGIN_URL.'assets/ss/filter-ui.jpg'; ?>">
        </div> <?php
            if ( is_admin() && defined( 'DOING_AJAX' ) && DOING_AJAX ) {
                
                wp_die();
            }

    }

    public function get_pro_link() {
        $url = 'https://codecanyon.net/item/custom-posts-builder-pro/17966160?ref=wpdevstudio';
        return '<a target="_blank" href="'.$url.'">'.__('Get Pro',CPB_TEXT_DOMAIN).'</a>';
    }


}