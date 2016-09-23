<?php

	/**
	 * Returns true if cpb has post types and false if not
	 * @return bool
	 * @since  1.0
	 */
	function cpb_has_post_types() {
		$cpb = cpb_get_post_types();
		return empty($cpb) ? false : true;
	}

	/**
	 * Returns all post types made by cbp
	 * @return mixed
	 * @since  1.0
	 */
	function cpb_get_post_types() {

		$cpb = get_option('cpb_post_types');
		$cpb =  (array) apply_filters('cpb_get_post_types', cpb_unserialize($cpb) );
		return array_filter($cpb);
	}

	/**
	 * Returns single post type made by cbp
	 * @return mixed
	 * @since  1.0
	 */
	function cpb_get_post_type($post_type) {

		$cpts = cpb_get_post_types();

		return isset($cpts[$post_type]) ? $cpts[$post_type] : false;
	}

	/**
	 * save/update a post type
	 * @return bool
	 * @since  1.0
	 */
	function cpb_save_post_type($post_type) {
		$post_types = cpb_get_post_types();
		$name = $post_type['labels']['post_type_name'];
		// as this function also get executed to update specific parts example column etc,
		// to avoid overwriting anything, update only bits of data provided
		if( isset($post_type['labels']) )
		$post_types[$name]['labels'] 	= $post_type['labels'];

		if( isset($post_type['options']) )
		$post_types[$name]['options'] 	= $post_type['options'];

		if( isset($post_type['taxonomies']) )
		$post_types[$name]['taxonomies'] 	= $post_type['taxonomies'];

		if( isset($post_type['admin_cols']) )
			$post_types[$name]['admin_cols'] 	= $post_type['admin_cols'];

		if( isset($post_type['order']) )
			$post_types[$name]['order'] 	= $post_type['order'];

		if( isset($post_type['filters']) )
			$post_types[$name]['filters'] 	= $post_type['filters'];

		$post_types = cpb_serialize($post_types);
		return update_option('cpb_post_types',$post_types);
	}

	/**
	 * delete a post type
	 * @return bool
	 * @since  1.0
	 */
	function cpb_delete_post_type($post_type) {
		$post_types = cpb_get_post_types();

		if( isset($post_types[$post_type]) ) {
			unset($post_types[$post_type]);
		}
		$post_types = cpb_serialize($post_types);
		return update_option('cpb_post_types',$post_types);
	}

	/**
	 * Returns defaut columns for a opst type
	 * @param  string|array $post_type name of post type
	 * @return array list of default admin columns
	 */
	function cpb_get_default_admin_cols($post_type = '') {

		if( is_string($post_type) )
			$post_type = cpb_get_post_type( sanitize_text_field( $post_type ) );

		// default wordpress cols
		$cols_list  = array(

            'title'    =>  array(

                'label'     =>  __('Title',CPB_TEXT_DOMAIN),
                'type'      =>  'prebuilt',
                'status'    =>  'on'

            ),
            'icon'    =>  array(

                'label'     =>  __('Featured Image',CPB_TEXT_DOMAIN),
                'type'      =>  'prebuilt',
                'status'    =>  'on',

            ),
            'author'    =>  array(

                'label'     =>  __('Author',CPB_TEXT_DOMAIN),
                'type'      =>  'prebuilt',
                'status'    =>  'on'

            ),
            'date'    =>  array(

                'label'     =>  __('Date',CPB_TEXT_DOMAIN),
                'type'      =>  'prebuilt',
                'status'    =>  'on'

            ),
            'comments'    =>  array(

                'label'     =>  __('Comments',CPB_TEXT_DOMAIN),
                'type'      =>  'prebuilt',
                'status'    =>  'off',

            )
        );

		// taxonomy columns
        if( !empty($post_type['taxonomies']) ) {
            $taxes = explode(',',$post_type['taxonomies']);
            $taxes = array_map('trim',$taxes);
            foreach($taxes as $tax) {
                $cols_list[$tax] = array(
                    'label'     =>  __($tax,CPB_TEXT_DOMAIN),
                    'type'      =>  'taxonomy',
                    'status'    =>  'on'
                );
            }
        }

        return apply_filters('cpb_default_cols_list',$cols_list,$post_type);
	}

	/**
	 * Returns true if cpb has taxonomies and false if not
	 * @return bool
	 * @since  1.0
	 */
	function cpb_has_taxonomies() {
		$cpb = cpb_get_taxonomies();
		return empty($cpb) ? false : true;
	}

	/**
	 * Returns all taxonomies made by cbp
	 * @return mixed
	 * @since  1.0
	 */
	function cpb_get_taxonomies() {

		$cpb = get_option('cpb_taxonomies');
		$cpb =  (array) apply_filters('cpb_get_taxonomies', cpb_unserialize($cpb) );
		return array_filter($cpb);
	}

	/**
	 * Returns single taxonomy made by cbp
	 * @return mixed
	 * @since  1.0
	 */
	function cpb_get_taxonomy($taxonomy) {

		$taxonomies = cpb_get_taxonomies();

		return isset($taxonomies[$taxonomy]) ? $taxonomies[$taxonomy] : false;
	}

	/**
	 * save/update a post type
	 * @return bool
	 * @since  1.0
	 */
	function cpb_save_taxonomy($taxonomy) {
		$taxonomies = cpb_get_taxonomies();

		$taxonomies[$taxonomy['labels']['taxonomy_name']] = $taxonomy;

		$taxonomies = cpb_serialize($taxonomies);
		return update_option('cpb_taxonomies',$taxonomies);
	}

	/**
	 * delete a post type
	 * @return bool
	 * @since  1.0
	 */
	function cpb_delete_taxonomy($taxonomy) {
		$taxonomies = cpb_get_taxonomies();

		if( isset($taxonomies[$taxonomy]) ) {
			unset($taxonomies[$taxonomy]);
		}
		$taxonomies = cpb_serialize($taxonomies);
		return update_option('cpb_taxonomies',$taxonomies);
	}


	function cpb_message($type,$message,$die=true) {
		echo '
			<span class="cpb-notification-wrap">
				<span class="cpb-notification cpb-notification-'.$type.'">
					<span class="cpb-notifaction-icon">
						<span class="dashicons dashicons-'.$type.'"></span>
					</span>
					'.__($message,CPB_TEXT_DOMAIN).'
				</span>
			</span>
		';
		if($die)
			wp_die();
	}

	/**
	 * Returns true if current user can create posts
	 * @since  1.0
	 * @return bool
	 */
	function cpb_cpt_creation_allowed() {

		$allowed = false;

		$cap = apply_filters('cpb_cpt_creation_cap','edit_posts');

		 if( current_user_can($cap) ) {
		 	$allowed = true;
		 }

		 return apply_filters('cpb_cpt_creation_allowed',$allowed);
	}

	/** 
	 * Returns array on cpb menu items
	 * @since  1.0
	 * @return array menu items
	 */
	function cpb_menus_items() {
		return apply_filters('cpb_menu_items',array(
			'cpb_create'			=>	__('Create post type',CPB_TEXT_DOMAIN),
			'cpb_created'			=>	__('Post types',CPB_TEXT_DOMAIN),
			'cpb_tax_create'		=>	__('Create taxonomy',CPB_TEXT_DOMAIN),
			'cpb_tax_created'		=>	__('Taxonomies',CPB_TEXT_DOMAIN),
			'cpb_admin_columns'		=>	__('Admin columns',CPB_TEXT_DOMAIN),
			'cpb_admin_filters'		=>	__('Filters',CPB_TEXT_DOMAIN)
		));
	}

	/**
	 * Renders CPB menus
	 * @return null
	 * @since  1.0
	 */
	function cpb_render_menus() { ?>

        <div class="cpb-menus-wrap">
            <ul class="cpb-menu-list">
            <?php
                foreach(cpb_menus_items() as $id =>  $cpb_menus_item) {
                	$active = '';
                	if($id == 'cpb_created') {
                		$active = 'active';
                	}
                    echo '
                        <li class="'.$active.' cpb-menu-item '.$id.'-menu">
                            <a id="'.$id.'" href="#">
                            '.$cpb_menus_item.'
                            </a>
                        </li>
                    ';
                }
            ?>
            </ul>
        </div>
        <?php

	}
	add_action('cpb_render_menus','cpb_render_menus');

	/**
	 * Generates a CPT builder form 
	 * @return void
	 * @since  1.0
	 */
	function cpb_cpt_template() { ?>

		<div class="cpb-cpt-tpl cpb-cpt-layout">

			<form method="post" class="cpb-cpt-form">

				<div class="cpb-cpt-post-type">
					<?php cpb_cpt_post_type_section(); ?>
				</div>

				<div class="cpb-cpt-labels">
					<?php cpb_cpt_labels_section(); ?>
				</div>

				<div class="cpb-cpt-options">
					<?php cpb_cpt_options_section(); ?>
				</div>

				<div class="cpb-cpt-taxonomies">
					<?php cpb_cpt_taxonomy_section(); ?>
				</div>

				<div class="cpb-cpt-save">
					<input type="hidden" name="action" value="cpb_cpt_create"/>
					<a href="#" name="cpb_submit" class="cpb_submit"><?php _e('Save',CPB_TEXT_DOMAIN); ?></a>		
				</div>

			</form>

		</div>
	<?php
	}
	add_action('cpb_cpt_template','cpb_cpt_template');

	function cpb_cpt_post_type_section() {

		$post_type = array(
			 
			'post_type_name' 						=> __("This is the name of the post type to be created, ideally the post type name is all lowercase and words separated with an underscore _.",CPB_TEXT_DOMAIN),
		);

		$post_type = apply_filters('cpb_cpt_post_type',$post_type);

		echo '<h3>'.__('Post Type Name',CPB_TEXT_DOMAIN).'<span class="dashicons dashicons-arrow-down-alt2"></span></h3>';
		
		echo '<div class="cpb-post-type-wrap">';

		foreach($post_type as $key	=>	$title) {

			echo '<div class="cpb-cpt-field">';
				echo '<span class="cpb-label-wrap">';
					echo '<label class="cpb-label cpb-label-'.$key.'" for="'.CPB_PREFIX.$key.'" >'.ucwords(str_replace('_',' ',$key)).'</label>';
					echo '<span data-tipso="'.$title.'" class="cpb-help dashicons dashicons-editor-help"></span>';
				echo '</span>';
				echo '<span class="cpb-input-wrap">';
					echo '<input type="text" class="cpb-input cpb-input-'.$key.'" id="'.CPB_PREFIX.$key.'" name="labels['.$key.']" />';
				echo '</span>';
			echo '</div>';
		}
		echo '</div>';
	}

	function cpb_cpt_labels_section() {

		$labels = array(
			 
			'singular' 					=> __("name for one object of this post type. Default is Post/Page",CPB_TEXT_DOMAIN),
			'plural' 					=> __("name for multiple object of this post type. Default is Posts/Pages",CPB_TEXT_DOMAIN),
			'slug' 						=> __("slug is url friendly name of the post type",CPB_TEXT_DOMAIN),
		);

		$labels = apply_filters('cpb_cpt_labels',$labels);

		echo '<h3>'.__('Labels',CPB_TEXT_DOMAIN).'<span class="dashicons dashicons-arrow-right-alt2"></span></h3>';
		echo '<div class="cpb-labels-wrap">';
		echo '<span class="cpb-msg">';
			_e('Labels are optional. They can be used to customise every mention of the post type in wordpress.');
		echo '</span>';

		foreach($labels as $key	=>	$title) {

			echo '<div class="cpb-cpt-field">';
				echo '<span class="cpb-label-wrap">';
					echo '<label class="cpb-label cpb-label-'.$key.'" for="'.CPB_PREFIX.$key.'" >'.ucwords(str_replace('_',' ',$key)).'</label>';
					echo '<span data-tipso="'.$title.'" class="cpb-help dashicons dashicons-editor-help"></span>';
				echo '</span>';
				echo '<span class="cpb-input-wrap">';
					echo '<input type="text" class="cpb-input cpb-input-'.$key.'" id="'.CPB_PREFIX.$key.'" name="labels['.$key.']" />';
				echo '</span>';
			echo '</div>';
		}
		echo '</div>';
	}

	function cpb_taxonomy_labels_section() {

		$labels = array(
			 
			'taxonomy_name'				=>__('taxonomy name',CPB_TEXT_DOMAIN),
			'singular' 					=> __("name for one object of this taxonomy",CPB_TEXT_DOMAIN),
			'plural' 					=> __("name for multiple object of this taxonomy",CPB_TEXT_DOMAIN),
			'slug' 						=> __("slug is url friendly name of the taxonomy",CPB_TEXT_DOMAIN),
		);

		$labels = apply_filters('cpb_taxonomy_labels',$labels);

		echo '<h3>'.__('Labels',CPB_TEXT_DOMAIN).'<span class="dashicons dashicons-arrow-right-alt2"></span></h3>';
		echo '<div class="cpb-labels-wrap">';
		echo '<span class="cpb-msg">';
			_e('They can be used to customise every mention of the taxonomy in wordpress.');
		echo '</span>';

		foreach($labels as $key	=>	$title) {

			echo '<div class="cpb-cpt-field">';
				echo '<span class="cpb-label-wrap">';
					echo '<label class="cpb-label cpb-label-'.$key.'" for="'.CPB_PREFIX.$key.'" >'.ucwords(str_replace('_',' ',$key)).'</label>';
					echo '<span data-tipso="'.$title.'" class="cpb-help dashicons dashicons-editor-help"></span>';
				echo '</span>';
				echo '<span class="cpb-input-wrap">';
					echo '<input type="text" class="cpb-input cpb-input-'.$key.'" id="'.CPB_PREFIX.$key.'" name="labels['.$key.']" />';
				echo '</span>';
			echo '</div>';
		}
		echo '</div>';
	}

	function cpb_cpt_options_section() {

		$options = array(
			'public'				=>	array(
											'name'	=>	'public',
											'type'	=>	'radio',
											'help'	=>	__('Default to true. True means post type is visible in admin area, menu area and in frontend',CPB_TEXT_DOMAIN),
											'opts'	=>	array(
												'true'	=>	__('Yes',CPB_TEXT_DOMAIN),
												'false'	=>	__('No',CPB_TEXT_DOMAIN),
											),
											'default'	=>	'true'
										),
			'publicly_queryable'	=>	array(
											'name'	=>	'publicly_queryable',
											'type'	=>	'radio',
											'help'	=>	__('Default to true. Whether queries can be performed on the front end',CPB_TEXT_DOMAIN),
											'opts'	=>	array(
												'true'	=>	__('Yes',CPB_TEXT_DOMAIN),
												'false'	=>	__('No',CPB_TEXT_DOMAIN),
											),
											'default'	=>	'true'
										),
			'show_ui'				=>	array(
											'name'	=>	'show_ui',
											'type'	=>	'radio',
											'help'	=>	__('Default to true. Whether to generate a default UI for managing this post type in the admin',CPB_TEXT_DOMAIN),
											'opts'	=>	array(
												'true'	=>	__('Yes',CPB_TEXT_DOMAIN),
												'false'	=>	__('No',CPB_TEXT_DOMAIN),
											),
											'default'	=>	'true'
										),
			'show_in_menu'			=>	array(
											'name'	=>	'show_in_menu',
											'type'	=>	'radio',
											'help'	=>	__('Default to true. Whether to show the post type in the admin menu. `Show Ui` must be true for this',CPB_TEXT_DOMAIN),
											'opts'	=>	array(
												'true'	=>	__('Yes',CPB_TEXT_DOMAIN),
												'false'	=>	__('No',CPB_TEXT_DOMAIN),
											),
											'default'	=>	'true'
										),
			'capability_type'		=>	array(
											'name'	=>	'capability_type',
											'type'	=>	'text',
											'help'	=>	__('Default to post. The string to use to build the read, edit, and delete capabilities',CPB_TEXT_DOMAIN),
											'default'	=>	'post'
										),
			'has_archive'			=>	array(
											'name'	=>	'has_archive',
											'type'	=>	'radio',
											'help'	=>	__('Default to true. Enables post type archives. Will use post name as archive slug by default. ',CPB_TEXT_DOMAIN),
											'opts'	=>	array(
												'true'	=>	__('Yes',CPB_TEXT_DOMAIN),
												'false'	=>	__('No',CPB_TEXT_DOMAIN),
											),
											'default'	=>	'true'
										),
			'hierarchical'			=>	array(
											'name'	=>	'hierarchical',
											'type'	=>	'radio',
											'help'	=>	__('Default to false. Whether the post type is hierarchical(example page).Allows Parent to be specified. The post type should support `page-attributes` to show the parent select box on the editor page. ',CPB_TEXT_DOMAIN),
											'opts'	=>	array(
												'true'	=>	__('Yes',CPB_TEXT_DOMAIN),
												'false'	=>	__('No',CPB_TEXT_DOMAIN),
											),
											'default'	=>	'false'
										),
			'menu_icon'			=>	array(
											'name'	=>	'menu_icon',
											'type'	=>	'image',
											'help'	=>	__('the url to the icon to be used for this menu or the name of the icon from the dashicons',CPB_TEXT_DOMAIN),
											'default'	=>	'dashicons-admin-post'
										),
			'menu_position'			=>	array(
											'name'	=>	'menu_position',
											'type'	=>	'text',
											'help'	=>	__('Default to 26.87(after comments). The position in the menu order the post type should appear. show_in_menu must be true.',CPB_TEXT_DOMAIN),
											'default'	=>	'2.87'
										),
			'supports'				=>	array(
											'name'	=>	'supports',
											'type'	=>	'checkbox',
											'help'	=>	__("By default following are supported : 'title', 'editor', 'author', 'thumbnail', 'excerpt', 'comments', 'revisions'",CPB_TEXT_DOMAIN),
											'opts'	=>	array(
												'title'				=>	__('Title',CPB_TEXT_DOMAIN),
												'editor'			=>	__('Editor',CPB_TEXT_DOMAIN),
												'author'			=>	__('Author',CPB_TEXT_DOMAIN),
												'thumbnail'			=>	__('Thumbnail',CPB_TEXT_DOMAIN),
												'excerpt'			=>	__('Excerpt',CPB_TEXT_DOMAIN),
												'trackbacks'		=>	__('Trackbacks',CPB_TEXT_DOMAIN),
												'custom-fields'		=>	__('Custom Fields',CPB_TEXT_DOMAIN),
												'comments'			=>	__('Comments',CPB_TEXT_DOMAIN),
												'revisions'			=>	__('Revisions',CPB_TEXT_DOMAIN),
												'page-attributes'	=>	__('Page Attributes',CPB_TEXT_DOMAIN),
												'post-formats'		=>	__('Post Formats',CPB_TEXT_DOMAIN)
											),
											'default'	=>	array(
												'title',
												'editor',
												'author',
												'thumbnail',
												'excerpt',
												'comments',
												'revisions',
											)
										)
		);

		$options = apply_filters('cpb_cpt_options',$options);

		echo '<h3>'.__('Options',CPB_TEXT_DOMAIN).'<span class="dashicons dashicons-arrow-right-alt2"></span></h3>';
		echo '<div class="cpb-options-wrap">';
		foreach($options as $key	=>	$field) {

			echo '<div class="cpb-cpt-field">';
				echo '<span class="cpb-label-wrap">';
					echo '<label class="cpb-label cpb-label-'.$key.'" for="'.CPB_PREFIX.$key.'" >'.ucwords(str_replace('_',' ',$key)).'</label>';
					echo '<span data-tipso="'.$field['help'].'" class="cpb-help dashicons dashicons-editor-help"></span>';
				echo '</span>';
				echo '<span class="cpb-input-wrap">';
					render_field($field);
				echo '</span>';
			echo '</div>';
		}
		echo '</div>';

	}

	function cpb_taxonomy_options_section() {

		$options = array(
			'public'				=>	array(
											'name'	=>	'public',
											'type'	=>	'radio',
											'help'	=>	__('Default to true. True means taxonomy is visible in admin area, menu area and in frontend',CPB_TEXT_DOMAIN),
											'opts'	=>	array(
												'true'	=>	__('Yes',CPB_TEXT_DOMAIN),
												'false'	=>	__('No',CPB_TEXT_DOMAIN),
											),
											'default'	=>	'true'
										),
			'show_ui'				=>	array(
											'name'	=>	'show_ui',
											'type'	=>	'radio',
											'help'	=>	__('Default to true. Whether to generate a default UI for managing this taxonomy in the admin',CPB_TEXT_DOMAIN),
											'opts'	=>	array(
												'true'	=>	__('Yes',CPB_TEXT_DOMAIN),
												'false'	=>	__('No',CPB_TEXT_DOMAIN),
											),
											'default'	=>	'true'
										),
			'show_in_menu'			=>	array(
											'name'	=>	'show_in_menu',
											'type'	=>	'radio',
											'help'	=>	__('Default to true. Whether to show the taxonomy in the admin menu. `Show Ui` must be true for this',CPB_TEXT_DOMAIN),
											'opts'	=>	array(
												'true'	=>	__('Yes',CPB_TEXT_DOMAIN),
												'false'	=>	__('No',CPB_TEXT_DOMAIN),
											),
											'default'	=>	'true'
										),
			'show_in_nav_menus'			=>	array(
											'name'	=>	'show_in_nav_menus',
											'type'	=>	'radio',
											'help'	=>	__('Default to true. true makes this taxonomy available for selection in navigation menus',CPB_TEXT_DOMAIN),
											'opts'	=>	array(
												'true'	=>	__('Yes',CPB_TEXT_DOMAIN),
												'false'	=>	__('No',CPB_TEXT_DOMAIN),
											),
											'default'	=>	'true'
										),
			'show_tagcloud'			=>	array(
											'name'	=>	'show_tagcloud',
											'type'	=>	'radio',
											'help'	=>	__('Default to true. Whether to allow the Tag Cloud widget to use this taxonomy',CPB_TEXT_DOMAIN),
											'opts'	=>	array(
												'true'	=>	__('Yes',CPB_TEXT_DOMAIN),
												'false'	=>	__('No',CPB_TEXT_DOMAIN),
											),
											'default'	=>	'true'
										),
			'show_in_quick_edit'	=>	array(
											'name'	=>	'show_in_quick_edit',
											'type'	=>	'radio',
											'help'	=>	__('Default to true. Whether to show the taxonomy in the quick/bulk edit panel( works for wp version > 4.2 )',CPB_TEXT_DOMAIN),
											'opts'	=>	array(
												'true'	=>	__('Yes',CPB_TEXT_DOMAIN),
												'false'	=>	__('No',CPB_TEXT_DOMAIN),
											),
											'default'	=>	'true'
										),
			'show_admin_column'	=>	array(
											'name'	=>	'show_admin_column',
											'type'	=>	'radio',
											'help'	=>	__('Default to true. Whether to allow automatic creation of taxonomy columns on associated post-types table',CPB_TEXT_DOMAIN),
											'opts'	=>	array(
												'true'	=>	__('Yes',CPB_TEXT_DOMAIN),
												'false'	=>	__('No',CPB_TEXT_DOMAIN),
											),
											'default'	=>	'true'
										),
			'hierarchical'	=>	array(
											'name'	=>	'hierarchical',
											'type'	=>	'radio',
											'help'	=>	__('Default to true. Is this taxonomy hierarchical (have descendants) like categories or not hierarchical like tags',CPB_TEXT_DOMAIN),
											'opts'	=>	array(
												'true'	=>	__('Yes',CPB_TEXT_DOMAIN),
												'false'	=>	__('No',CPB_TEXT_DOMAIN),
											),
											'default'	=>	'true'
										),
			'rewrite'				=>	array(
											'name'	=>	'rewrite',
											'type'	=>	'radio',
											'help'	=>	__('Default to true. Set to false to prevent automatic URL rewriting a.k.a. pretty permalinks ',CPB_TEXT_DOMAIN),
											'opts'	=>	array(
												'true'	=>	__('Yes',CPB_TEXT_DOMAIN),
												'false'	=>	__('No',CPB_TEXT_DOMAIN),
											),
											'default'	=>	'false'
										),
			'sort'					=>	array(
											'name'	=>	'sort',
											'type'	=>	'radio',
											'help'	=>	__('Default to true. Whether this taxonomy should remember the order in which terms are added to objects',CPB_TEXT_DOMAIN),
											'opts'	=>	array(
												'true'	=>	__('Yes',CPB_TEXT_DOMAIN),
												'false'	=>	__('No',CPB_TEXT_DOMAIN),
											),
											'default'	=>	'true'
										),
		);

		$options = apply_filters('cpb_taxonomy_options',$options);

		echo '<h3>'.__('Options',CPB_TEXT_DOMAIN).'<span class="dashicons dashicons-arrow-right-alt2"></span></h3>';
		echo '<div class="cpb-options-wrap">';
		foreach($options as $key	=>	$field) {

			echo '<div class="cpb-cpt-field">';
				echo '<span class="cpb-label-wrap">';
					echo '<label class="cpb-label cpb-label-'.$key.'" for="'.CPB_PREFIX.$key.'" >'.ucwords(str_replace('_',' ',$key)).'</label>';
					echo '<span data-tipso="'.$field['help'].'" class="cpb-help dashicons dashicons-editor-help"></span>';
				echo '</span>';
				echo '<span class="cpb-input-wrap">';
					render_field($field);
				echo '</span>';
			echo '</div>';
		}
		echo '</div>';

	}

	function cpb_cpt_taxonomy_section() {

		$post_type = array(
			 
			'taxonomies' 						=> __("Comma seperated list of taxonomies you wish to add to this post type",CPB_TEXT_DOMAIN),
		);

		$post_type = apply_filters('cpb_cpt_taxonomies',$post_type);

		echo '<h3>'.__('Taxonomies',CPB_TEXT_DOMAIN).'<span class="dashicons dashicons-arrow-right-alt2"></span></h3>';
		
		echo '<div class="cpb-taxonomy-wrap">';

		echo '<span class="cpb-no-posts-msg">'.__('It is recommended that you should first create taxonomy in create taxonomy section and then assign it here. Directly assigning name in this section will result in a new taxonomy, exclusive to this post type only.',CPB_TEXT_DOMAIN).'</span>';

		foreach($post_type as $key	=>	$title) {

			echo '<div class="cpb-cpt-field">';
				echo '<span class="cpb-label-wrap">';
					echo '<label class="cpb-label cpb-label-'.$key.'" for="'.CPB_PREFIX.$key.'" >'.ucwords(str_replace('_',' ',$key)).'</label>';
					echo '<span data-tipso="'.$title.'" class="cpb-help dashicons dashicons-editor-help"></span>';
				echo '</span>';
				echo '<span class="cpb-input-wrap">';
					echo '<input type="text" class="cpb-input cpb-input-'.$key.'" id="'.CPB_PREFIX.$key.'" name="'.$key.'" />';
				echo '</span>';
			echo '</div>';
		}

		// echo '<ul class="cpb-existing-taxes">';
		// 	$taxonomies = get_taxonomies(array('public'   => true, '_builtin' => true),'objects','or'); 
		// 	foreach ( $taxonomies as $taxonomy ) {

		// 		if( !in_array($taxonomy->name,cpb_skipped_taxes()) )
		// 	    	echo '<li data-slug="'.$taxonomy->name.'" >' . ucfirst(str_replace('_',' ',$taxonomy->label)) . '</li>';
		// 	}
		// echo '</ul>';
		echo '</div>';

	}

	function cpb_skipped_taxes() {
		return apply_filters('cpb_skipped_taxes',array('nav_menu','link_category','post_format'));
	}

	function render_field ( $field = array() , $val = '' ) {

		if($val == '' && isset($field['default']) ) {
			$val = $field['default'];
		}

	 	switch($field['type']) {

			case 'select':

				echo '<select name="options['.$field['name'].']" class="cpb-input cpb-input-select" id="'.$field['name'].'">';
					
					if(isset($field['opts']) && !empty($field['opts'])) {
						foreach($field['opts'] as $k=>$v) {
							$selected = '';
							if($val == $k || ($val=='' && !empty($field['default']) && $field['default'] == $k) ) {
								$selected = 'selected="selected"';
							}
							if(is_array($v)) {
								
								$v = $v['label'];
							}
							echo '<option value="'.$k.'" '.$selected.'>'.__($v, CPB_TEXT_DOMAIN ).'</option>';
						}
					} else {
						echo '<option value=""> </option>';
					}
				echo '</select>';
				break;

			case 'checkbox':
				if(!empty($field['opts'])) {
					foreach($field['opts'] as $k=>$v) {
						$checked = '';
						if(!empty($val)) {
							$val = (array) $val;
							if( in_array($k, $val) ) {
								$checked = 'checked="checked"';
							}
						}
						echo '<input class="cpb-input cpb-input-checkbox" type="checkbox" name="options['.$field['name'].'][]" id="'.$field['name'].'_'.$k.'" value="'.$k.'" '.$checked.' /> <label class="cpb-input-label cpb-label-checkbox" for="'.$field['name'].'_'.$k.'">'.__($v, CPB_TEXT_DOMAIN ).'</label>';
					}
				}
				break;

			case 'checkbox_single':
				if(!empty($field['opts'])) {
					foreach($field['opts'] as $k=>$v) {
						$checked = '';
						if(!empty($val)) {
							$checkbox_single_options = apply_filters('epl_checkbox_single_check_options', array(1,'yes','on','true'));
							if( $k == $val || in_array($val,$checkbox_single_options) ) {
								$checked = 'checked="checked"';
							}
						}
						if(count($field['opts']) == 1)
							$v = $field['label'];
						echo '<input type="checkbox" class="cpb-input cpb-input-checkbox" name="options['.$field['name'].']" id="'.$field['name'].'_'.$k.'" value="'.$k.'" '.$checked.' /> <label class="cpb-input-label cpb-label-checkbox" for="'.$field['name'].'_'.$k.'">'.__($v, CPB_TEXT_DOMAIN ).'</label>';
					}
				}
				break;

			case 'radio':
				//print_exit($field);
				if(!empty($field['opts'])) {
					foreach($field['opts'] as $k=>$v) {
						$checked = '';
						if($val == $k || ($val=='' && !empty($field['default']) && $field['default'] == $k)) {
							$checked = 'checked="checked"';
						}
						echo '<input class="cpb-input cpb-input-radio" type="radio" name="options['.$field['name'].']" id="'.$field['name'].'_'.$k.'" value="'.$k.'" '.$checked.' /> <label for="'.$field['name'].'_'.$k.'"  class="cpb-input-label cpb-label-radio">'.__($v, CPB_TEXT_DOMAIN ).'</label>';
					}
				}
				break;


			case 'number':
				$atts = '';
				if(isset($field['maxlength']) && $field['maxlength'] > 0) {
					$atts = ' maxlength="'.$field['maxlength'].'"';
				}
				echo '<input type="number" name="options['.$field['name'].']" id="'.$field['name'].'" value="'.stripslashes($val).'" class="cpb-input" '.$atts.' />';
				break;

			case 'email':
				echo '<input type="text" name="options['.$field['name'].'" id="'.$field['name'].'" value="'.stripslashes($val).'" class="cpb-input" />';
				break;

			case 'textarea':
				echo '<textarea class="cpb-input cpb-input-textarea" name="'.$field['name'].'" id="'.$field['name'].'" >'.stripslashes($val).'</textarea>';
			break;
			case 'editor':
				wp_editor(stripslashes($val), $field['name'], $settings = array('textarea_rows'=>5,'media_buttons' => false));
			break;
			case 'image':
			case 'file':
				if($val != '') {
					$img = $val;
				} else {
					$img = '';
				}
				echo '
						<input type="text" name="options['.$field['name'].']" id="'.$field['name'].'" class="cpb-input" value="'.stripslashes($val).'" />
						&nbsp;&nbsp;<input type="button" name="cpb_upload_button" class="button" value="'.__('Add File', CPB_TEXT_DOMAIN ).'" />';

						if( in_array( pathinfo($img, PATHINFO_EXTENSION), array('jpg','jpeg','png','gif') ) ) {
							echo '&nbsp;&nbsp;<img src="'.$img.'" alt="" />';
						}
			break;
			default:

				foreach($field as $temp_key	=>	$temp_value) {
					if (0 === strpos($temp_key, 'data-')) {
					  $atts .= ''.$temp_key.'="'.$temp_value.'"';
					}
				}
	            echo '<input type="'.$field['type'].'" name="options['.$field['name'].']" id="'.$field['name'].'" class="cpb-input"  value="'.stripslashes($val).'" />';
		}
		
	}

	function cpb_serialize($data) {
		return base64_encode(serialize($data));
	}

	function cpb_unserialize($data) {
		return unserialize(base64_decode($data));
	}

	function cpb_cpt_option_list() {
		$list = apply_filters(
					'cpb_cpt_option_list',
					array(
						'no'	=>	__('Delete',CPB_TEXT_DOMAIN),
						'edit'	=>	__('Edit',CPB_TEXT_DOMAIN)
					)
				);

		ob_start();
		foreach($list as $item_icon	=>	$item_label) {
			echo '<span data-action="'.$item_icon.'" id="cpb_cpt_option_'.$item_icon.'" alt="'.$item_label.'" title="'.$item_label.'" class="dashicons dashicons-'.$item_icon.'"></span>';
		}
		return ob_get_clean();
	}

	function cpb_tax_option_list() {
		$list = apply_filters(
					'cpb_tax_option_list',
					array(
						'no'	=>	__('Delete',CPB_TEXT_DOMAIN),
						'edit'	=>	__('Edit',CPB_TEXT_DOMAIN)
					)
				);

		ob_start();
		foreach($list as $item_icon	=>	$item_label) {
			echo '<span data-action="'.$item_icon.'" id="cpb_tax_option_'.$item_icon.'" alt="'.$item_label.'" title="'.$item_label.'" class="dashicons dashicons-'.$item_icon.'"></span>';
		}
		return ob_get_clean();
	}



	function show_cpb_cpt_list() {

        $cpts = cpb_get_post_types();

        do_action('cpb_pre_cpt_list');

        if( !empty($cpts) ) {
            echo '<ul class="cpb-cpt-list">';
            foreach($cpts as $cpt => $options) {
                echo '<li class="cpb-cpt-list-item">'.$cpt.cpb_cpt_option_list().'</li>';
            }
            echo '</ul>';
        }

        do_action('cpb_post_cpt_list');

        if ( is_admin() && defined( 'DOING_AJAX' ) && DOING_AJAX ) {
            
            wp_die();
        }
    }

	function show_cpb_tax_list() {

        $taxonomies = cpb_get_taxonomies();

        do_action('cpb_pre_tax_list');

        if( !empty($taxonomies) ) {
            echo '<ul class="cpb-tax-list">';
            foreach($taxonomies as $taxonomy => $options) {
                echo '<li class="cpb-tax-list-item">'.$taxonomy.cpb_tax_option_list().'</li>';
            }
            echo '</ul>';
        }

        do_action('cpb_post_tax_list');

        if ( is_admin() && defined( 'DOING_AJAX' ) && DOING_AJAX ) {
            
            wp_die();
        }
    }


	/**
	 * Generates a Taxonomy builder form 
	 * @return void
	 * @since  1.0
	 */
	function cpb_taxonomy_template() { ?>

		<div class="cpb-taxonomy-tpl cpb-cpt-tpl">

			<form method="post" class="cpb-taxonomy-form">

				<div class="cpb-taxonomy-labels">
					<?php cpb_taxonomy_labels_section(); ?>
				</div>

				<div class="cpb-taxonomy-options">
					<?php cpb_taxonomy_options_section(); ?>
				</div>

				<div class="cpb-taxonomy-save">
					<input type="hidden" name="action" value="cpb_taxonomy_create"/>
					<a href="#" name="cpb_submit" class="cpb_submit"><?php _e('Save',CPB_TEXT_DOMAIN); ?></a>		
				</div>

			</form>

		</div>
	<?php
	}
	add_action('cpb_taxonomy_template','cpb_taxonomy_template');

	function cbp_get_tools_tab() {

		$default_tabs = array(
			'import'	=>	array(
				'label'		=>	__('Import',CPB_TEXT_DOMAIN),
				'callback'	=>	'cpb_import'
			),
			'export'	=>	array(
				'label'		=>	__('Export',CPB_TEXT_DOMAIN),
				'callback'	=>	'cpb_export'
			)
		);
		return apply_filters('cpb_tools_tabs',$default_tabs);
	}

	function cpb_import() {

		do_action('pre_cpb_import_fields');

		$fields = array(

			array(
				'name'		=>	'cpb_import',
				'label'		=>	__('Export data',CPB_TEXT_DOMAIN),
				'type'		=>	'textarea',
				'help'		=>	__("Paste exported data here",CPB_TEXT_DOMAIN),
			)
		);

		$fields = apply_filters('cpb_import_fields',$fields);

		foreach($fields as $field) {

			echo '<div class="cpb-cpt-field">';
				echo '<span class="cpb-label-wrap">';
					echo '<label class="cpb-label cpb-label-'.$field['name'].'" for="'.CPB_PREFIX.$field['name'].'" >'.$field['label'].'</label>';
					echo '<span data-tipso="'.$field['help'].'" class="cpb-help dashicons dashicons-editor-help"></span>';
				echo '</span>';
				echo '<span class="cpb-input-wrap">';
					render_field($field);
				echo '</span>';
			echo '</div>';
		}



		do_action('post_cpb_export_fields');

	}

	function cpb_export() {

		do_action('pre_cpb_export_fields');

		$taxonomies = array_keys(cpb_get_taxonomies());
		$post_types = array_keys(cpb_get_post_types());
		$fields = array(

			array(
				'name'		=>	'export_cpts',
				'label'		=>	__('Export Post Types :',CPB_TEXT_DOMAIN),
				'type'		=>	'checkbox',
				'help'		=>	__("Select post types to export",CPB_TEXT_DOMAIN),
				'opts'		=>	array_combine($post_types,$post_types),
				'default'	=>	$post_types
			),
			array(
				'name'		=>	'export_taxes',
				'label'		=>	__('Export Taxonomies :',CPB_TEXT_DOMAIN),
				'type'		=>	'checkbox',
				'help'		=>	__("Select taxonomies to export",CPB_TEXT_DOMAIN),
				'opts'		=>	array_combine($taxonomies,$taxonomies),
				'default'	=>	$taxonomies
			),

		);

		$fields = apply_filters('cpb_export_fields',$fields);

		foreach($fields as $field) {

			echo '<div class="cpb-cpt-field">';
				echo '<span class="cpb-label-wrap">';
					echo '<label class="cpb-label cpb-label-'.$field['name'].'" for="'.CPB_PREFIX.$field['name'].'" >'.$field['label'].'</label>';
					echo '<span data-tipso="'.$field['help'].'" class="cpb-help dashicons dashicons-editor-help"></span>';
				echo '</span>';
				echo '<span class="cpb-input-wrap">';
					render_field($field);
				echo '</span>';
			echo '</div>';
		}

		do_action('post_cpb_export_fields');
		
	}

	function cpb_new_col_template() { 

		$fields = array(

			array(
				'name'		=>	'cpb_col_name',
				'label'		=>	__('Column Name',CPB_TEXT_DOMAIN),
				'type'		=>	'text',
				'help'		=>	__("Name of the column",CPB_TEXT_DOMAIN),
			),
			array(
				'name'		=>	'cpb_col_slug',
				'label'		=>	__('Slug',CPB_TEXT_DOMAIN),
				'type'		=>	'text',
				'help'		=>	__("Slug of the column",CPB_TEXT_DOMAIN),
			),
			array(
				'name'		=>	'cpb_col_content',
				'label'		=>	__('Content',CPB_TEXT_DOMAIN),
				'type'		=>	'textarea',
				'help'		=>	__("Content",CPB_TEXT_DOMAIN),
			)

		); ?>

		
		
		<div class="cpb-new-col-tpl cpb-fancy-bg cpb-clearfix">
			<form class="cpb-create-col-form">
				<?php
					foreach($fields as $field) {
						echo '<div class="cpb-cpt-field">';
							echo '<span class="cpb-label-wrap">';
								echo '<label class="cpb-label cpb-label-'.$field['name'].'" for="'.CPB_PREFIX.$field['name'].'" >'.$field['label'].'</label>';
								echo '<span data-tipso="'.$field['help'].'" class="cpb-help dashicons dashicons-editor-help"></span>';
							echo '</span>';
							echo '<span class="cpb-input-wrap">';
								render_field($field);
							echo '</span>';
						echo '</div>';
					}
				?>
				<div class="cpb-col-submit-wrap">
					<input type="hidden" name="action" value="cpb_create_column"/>
					<a href="#" name="cpb_col_submit" class="cpb_col_submit"><?php _e('Create',CPB_TEXT_DOMAIN); ?></a>		
				</div>
			</form>
		</div> <?php
	
	}
	add_action('cpb_new_col_template','cpb_new_col_template');

	function cpb_get_col_content($content) {
		return do_shortcode(stripslashes($content));
	}

    function cpb_get_post_cols($type = '') {
        
        if( $type == '')
        	return;

        if( $post_type = cpb_get_post_type( sanitize_text_field( $type ) ) ) {
            
        } else {
            return;
        }

        $cols_list  = array(

            'title'    =>  array(

                'label'     =>  __('Title',CPB_TEXT_DOMAIN),
                'type'      =>  'prebuilt',
                'status'    =>  'on',
                'content'   =>  __('Default Content : Post Title',CPB_TEXT_DOMAIN)

            ),
            'icon'    =>  array(

                'label'     =>  __('Featured Image',CPB_TEXT_DOMAIN),
                'type'      =>  'prebuilt',
                'status'    =>  'on',
                'content'   =>  __('Default Content : Featured Image',CPB_TEXT_DOMAIN)

            ),
            'author'    =>  array(

                'label'     =>  __('Author',CPB_TEXT_DOMAIN),
                'type'      =>  'prebuilt',
                'status'    =>  'on',
                'content'   =>  __('Default Content : Post Author',CPB_TEXT_DOMAIN)
            ),
            'date'    =>  array(

                'label'     =>  __('Date',CPB_TEXT_DOMAIN),
                'type'      =>  'prebuilt',
                'status'    =>  'on',
                'content'   =>  __('Default Content : Post Publish Date',CPB_TEXT_DOMAIN)

            ),
            'comments'    =>  array(

                'label'     =>  __('Comments',CPB_TEXT_DOMAIN),
                'type'      =>  'prebuilt',
                'status'    =>  'off',
                'content'   =>  __('Default Content : Post Comments',CPB_TEXT_DOMAIN)

            )
        );

        

        if( !empty($post_type['taxonomies']) ) {
            $taxes = explode(',',$post_type['taxonomies']);
            $taxes = array_map('trim',$taxes);
            foreach($taxes as $tax) {
                $cols_list[$tax] = array(
                    'label'     =>  __($tax,CPB_TEXT_DOMAIN),
                    'type'      =>  'taxonomy',
                    'status'    =>  'on',
                    'content'   =>  __("Post's ".$tax,CPB_TEXT_DOMAIN)
                );
            }
        }

        /**
         * merge custom admin columns with default columns and taxonomy columns
         * @var array
         */
        $post_type['admin_cols'] = isset($post_type['admin_cols']) ? $post_type['admin_cols'] : array();
        $cols_list = (array) array_replace_recursive($cols_list,$post_type['admin_cols']);
        $order          = isset($post_type['order']) ? $post_type['order'] : array();
        return array_merge( array_intersect_key(array_flip( $order ),$cols_list), $cols_list);

    }

    function cpb_get_dashicons_list() { ob_start(); ?>
    	<div class="cpb-dashicons">
			<ul class="cpb-dashicons-list">
			<li><span class="dashicons dashicons-menu"></span><span class="dashicons-class">dashicons-menu</span><span class="dashicons-code">“\f333”</span></li>
			<li><span class="dashicons dashicons-admin-site"></span><span class="dashicons-class">dashicons-admin-site</span><span class="dashicons-code">“\f319”</span></li>
			<li><span class="dashicons dashicons-dashboard"></span><span class="dashicons-class">dashicons-dashboard</span><span class="dashicons-code">“\f226”</span></li>
			<li><span class="dashicons dashicons-admin-post"></span><span class="dashicons-class">dashicons-admin-post</span><span class="dashicons-code">“\f109”</span></li>
			<li><span class="dashicons dashicons-admin-media"></span><span class="dashicons-class">dashicons-admin-media</span><span class="dashicons-code">“\f104”</span></li>
			<li><span class="dashicons dashicons-admin-links"></span><span class="dashicons-class">dashicons-admin-links</span><span class="dashicons-code">“\f103”</span></li>
			<li><span class="dashicons dashicons-admin-page"></span><span class="dashicons-class">dashicons-admin-page</span><span class="dashicons-code">“\f105”</span></li>
			<li><span class="dashicons dashicons-admin-comments"></span><span class="dashicons-class">dashicons-admin-comments</span><span class="dashicons-code">“\f101”</span></li>
			<li><span class="dashicons dashicons-admin-appearance"></span><span class="dashicons-class">dashicons-admin-appearance</span><span class="dashicons-code">“\f100”</span></li>
			<li><span class="dashicons dashicons-admin-plugins"></span><span class="dashicons-class">dashicons-admin-plugins</span><span class="dashicons-code">“\f106”</span></li>
			<li><span class="dashicons dashicons-admin-users"></span><span class="dashicons-class">dashicons-admin-users</span><span class="dashicons-code">“\f110”</span></li>
			<li><span class="dashicons dashicons-admin-tools"></span><span class="dashicons-class">dashicons-admin-tools</span><span class="dashicons-code">“\f107”</span></li>
			<li><span class="dashicons dashicons-admin-settings"></span><span class="dashicons-class">dashicons-admin-settings</span><span class="dashicons-code">“\f108”</span></li>
			<li><span class="dashicons dashicons-admin-network"></span><span class="dashicons-class">dashicons-admin-network</span><span class="dashicons-code">“\f112”</span></li>
			<li><span class="dashicons dashicons-admin-home"></span><span class="dashicons-class">dashicons-admin-home</span><span class="dashicons-code">“\f102”</span></li>
			<li><span class="dashicons dashicons-admin-generic"></span><span class="dashicons-class">dashicons-admin-generic</span><span class="dashicons-code">“\f111”</span></li>
			<li><span class="dashicons dashicons-admin-collapse"></span><span class="dashicons-class">dashicons-admin-collapse</span><span class="dashicons-code">“\f148”</span></li>
			<li><span class="dashicons dashicons-welcome-write-blog"></span><span class="dashicons-class">dashicons-welcome-write-blog</span><span class="dashicons-code">“\f119”</span></li>
			<li><span class="dashicons dashicons-welcome-add-page"></span><span class="dashicons-class">dashicons-welcome-add-page</span><span class="dashicons-code">“\f133”</span></li>
			<li><span class="dashicons dashicons-welcome-view-site"></span><span class="dashicons-class">dashicons-welcome-view-site</span><span class="dashicons-code">“\f115”</span></li>
			<li><span class="dashicons dashicons-welcome-widgets-menus"></span><span class="dashicons-class">dashicons-welcome-widgets-menus</span><span class="dashicons-code">“\f116”</span></li>
			<li><span class="dashicons dashicons-welcome-comments"></span><span class="dashicons-class">dashicons-welcome-comments</span><span class="dashicons-code">“\f117”</span></li>
			<li><span class="dashicons dashicons-welcome-learn-more"></span><span class="dashicons-class">dashicons-welcome-learn-more</span><span class="dashicons-code">“\f118”</span></li>
			<li><span class="dashicons dashicons-format-aside"></span><span class="dashicons-class">dashicons-format-aside</span><span class="dashicons-code">“\f123”</span></li>
			<li><span class="dashicons dashicons-format-image"></span><span class="dashicons-class">dashicons-format-image</span><span class="dashicons-code">“\f128”</span></li>
			<li><span class="dashicons dashicons-format-gallery"></span><span class="dashicons-class">dashicons-format-gallery</span><span class="dashicons-code">“\f161”</span></li>
			<li><span class="dashicons dashicons-format-video"></span><span class="dashicons-class">dashicons-format-video</span><span class="dashicons-code">“\f126”</span></li>
			<li><span class="dashicons dashicons-format-status"></span><span class="dashicons-class">dashicons-format-status</span><span class="dashicons-code">“\f130”</span></li>
			<li><span class="dashicons dashicons-format-quote"></span><span class="dashicons-class">dashicons-format-quote</span><span class="dashicons-code">“\f122”</span></li>
			<li><span class="dashicons dashicons-format-chat"></span><span class="dashicons-class">dashicons-format-chat</span><span class="dashicons-code">“\f125”</span></li>
			<li><span class="dashicons dashicons-format-audio"></span><span class="dashicons-class">dashicons-format-audio</span><span class="dashicons-code">“\f127”</span></li>
			<li><span class="dashicons dashicons-camera"></span><span class="dashicons-class">dashicons-camera</span><span class="dashicons-code">“\f306”</span></li>
			<li><span class="dashicons dashicons-images-alt"></span><span class="dashicons-class">dashicons-images-alt</span><span class="dashicons-code">“\f232”</span></li>
			<li><span class="dashicons dashicons-images-alt2"></span><span class="dashicons-class">dashicons-images-alt2</span><span class="dashicons-code">“\f233”</span></li>
			<li><span class="dashicons dashicons-video-alt"></span><span class="dashicons-class">dashicons-video-alt</span><span class="dashicons-code">“\f234”</span></li>
			<li><span class="dashicons dashicons-video-alt2"></span><span class="dashicons-class">dashicons-video-alt2</span><span class="dashicons-code">“\f235”</span></li>
			<li><span class="dashicons dashicons-video-alt3"></span><span class="dashicons-class">dashicons-video-alt3</span><span class="dashicons-code">“\f236”</span></li>
			<li><span class="dashicons dashicons-image-crop"></span><span class="dashicons-class">dashicons-image-crop</span><span class="dashicons-code">“\f165”</span></li>
			<li><span class="dashicons dashicons-image-rotate-left"></span><span class="dashicons-class">dashicons-image-rotate-left</span><span class="dashicons-code">“\f166”</span></li>
			<li><span class="dashicons dashicons-image-rotate-right"></span><span class="dashicons-class">dashicons-image-rotate-right</span><span class="dashicons-code">“\f167”</span></li>
			<li><span class="dashicons dashicons-image-flip-vertical"></span><span class="dashicons-class">dashicons-image-flip-vertical</span><span class="dashicons-code">“\f168”</span></li>
			<li><span class="dashicons dashicons-image-flip-horizontal"></span><span class="dashicons-class">dashicons-image-flip-horizontal</span><span class="dashicons-code">“\f169”</span></li>
			<li><span class="dashicons dashicons-undo"></span><span class="dashicons-class">dashicons-undo</span><span class="dashicons-code">“\f171”</span></li>
			<li><span class="dashicons dashicons-redo"></span><span class="dashicons-class">dashicons-redo</span><span class="dashicons-code">“\f172”</span></li>
			<li><span class="dashicons dashicons-editor-bold"></span><span class="dashicons-class">dashicons-editor-bold</span><span class="dashicons-code">“\f200”</span></li>
			<li><span class="dashicons dashicons-editor-italic"></span><span class="dashicons-class">dashicons-editor-italic</span><span class="dashicons-code">“\f201”</span></li>
			<li><span class="dashicons dashicons-editor-ul"></span><span class="dashicons-class">dashicons-editor-ul</span><span class="dashicons-code">“\f203”</span></li>
			<li><span class="dashicons dashicons-editor-ol"></span><span class="dashicons-class">dashicons-editor-ol</span><span class="dashicons-code">“\f204”</span></li>
			<li><span class="dashicons dashicons-editor-quote"></span><span class="dashicons-class">dashicons-editor-quote</span><span class="dashicons-code">“\f205”</span></li>
			<li><span class="dashicons dashicons-editor-alignleft"></span><span class="dashicons-class">dashicons-editor-alignleft</span><span class="dashicons-code">“\f206”</span></li>
			<li><span class="dashicons dashicons-editor-aligncenter"></span><span class="dashicons-class">dashicons-editor-aligncenter</span><span class="dashicons-code">“\f207”</span></li>
			<li><span class="dashicons dashicons-editor-alignright"></span><span class="dashicons-class">dashicons-editor-alignright</span><span class="dashicons-code">“\f208”</span></li>
			<li><span class="dashicons dashicons-editor-insertmore"></span><span class="dashicons-class">dashicons-editor-insertmore</span><span class="dashicons-code">“\f209”</span></li>
			<li><span class="dashicons dashicons-editor-spellcheck"></span><span class="dashicons-class">dashicons-editor-spellcheck</span><span class="dashicons-code">“\f210”</span></li>
			<li><span class="dashicons dashicons-editor-distractionfree"></span><span class="dashicons-class">dashicons-editor-distractionfree</span><span class="dashicons-code">“\f211”</span></li>
			<li><span class="dashicons dashicons-editor-kitchensink"></span><span class="dashicons-class">dashicons-editor-kitchensink</span><span class="dashicons-code">“\f212”</span></li>
			<li><span class="dashicons dashicons-editor-underline"></span><span class="dashicons-class">dashicons-editor-underline</span><span class="dashicons-code">“\f213”</span></li>
			<li><span class="dashicons dashicons-editor-justify"></span><span class="dashicons-class">dashicons-editor-justify</span><span class="dashicons-code">“\f214”</span></li>
			<li><span class="dashicons dashicons-editor-textcolor"></span><span class="dashicons-class">dashicons-editor-textcolor</span><span class="dashicons-code">“\f215”</span></li>
			<li><span class="dashicons dashicons-editor-paste-word"></span><span class="dashicons-class">dashicons-editor-paste-word</span><span class="dashicons-code">“\f216”</span></li>
			<li><span class="dashicons dashicons-editor-paste-text"></span><span class="dashicons-class">dashicons-editor-paste-text</span><span class="dashicons-code">“\f217”</span></li>
			<li><span class="dashicons dashicons-editor-removeformatting"></span><span class="dashicons-class">dashicons-editor-removeformatting</span><span class="dashicons-code">“\f218”</span></li>
			<li><span class="dashicons dashicons-editor-video"></span><span class="dashicons-class">dashicons-editor-video</span><span class="dashicons-code">“\f219”</span></li>
			<li><span class="dashicons dashicons-editor-customchar"></span><span class="dashicons-class">dashicons-editor-customchar</span><span class="dashicons-code">“\f220”</span></li>
			<li><span class="dashicons dashicons-editor-outdent"></span><span class="dashicons-class">dashicons-editor-outdent</span><span class="dashicons-code">“\f221”</span></li>
			<li><span class="dashicons dashicons-editor-indent"></span><span class="dashicons-class">dashicons-editor-indent</span><span class="dashicons-code">“\f222”</span></li>
			<li><span class="dashicons dashicons-editor-help"></span><span class="dashicons-class">dashicons-editor-help</span><span class="dashicons-code">“\f223”</span></li>
			<li><span class="dashicons dashicons-editor-strikethrough"></span><span class="dashicons-class">dashicons-editor-strikethrough</span><span class="dashicons-code">“\f224”</span></li>
			<li><span class="dashicons dashicons-editor-unlink"></span><span class="dashicons-class">dashicons-editor-unlink</span><span class="dashicons-code">“\f225”</span></li>
			<li><span class="dashicons dashicons-editor-rtl"></span><span class="dashicons-class">dashicons-editor-rtl</span><span class="dashicons-code">“\f320”</span></li>
			<li><span class="dashicons dashicons-align-left"></span><span class="dashicons-class">dashicons-align-left</span><span class="dashicons-code">“\f135”</span></li>
			<li><span class="dashicons dashicons-align-right"></span><span class="dashicons-class">dashicons-align-right</span><span class="dashicons-code">“\f136”</span></li>
			<li><span class="dashicons dashicons-align-center"></span><span class="dashicons-class">dashicons-align-center</span><span class="dashicons-code">“\f134”</span></li>
			<li><span class="dashicons dashicons-align-none"></span><span class="dashicons-class">dashicons-align-none</span><span class="dashicons-code">“\f138”</span></li>
			<li><span class="dashicons dashicons-lock"></span><span class="dashicons-class">dashicons-lock</span><span class="dashicons-code">“\f160”</span></li>
			<li><span class="dashicons dashicons-calendar"></span><span class="dashicons-class">dashicons-calendar</span><span class="dashicons-code">“\f145”</span></li>
			<li><span class="dashicons dashicons-visibility"></span><span class="dashicons-class">dashicons-visibility</span><span class="dashicons-code">“\f177”</span></li>
			<li><span class="dashicons dashicons-post-status"></span><span class="dashicons-class">dashicons-post-status</span><span class="dashicons-code">“\f173”</span></li>
			<li><span class="dashicons dashicons-edit"></span><span class="dashicons-class">dashicons-edit</span><span class="dashicons-code">“\f464”</span></li>
			<li><span class="dashicons dashicons-trash"></span><span class="dashicons-class">dashicons-trash</span><span class="dashicons-code">“\f182”</span></li>
			<li><span class="dashicons dashicons-arrow-up"></span><span class="dashicons-class">dashicons-arrow-up</span><span class="dashicons-code">“\f142”</span></li>
			<li><span class="dashicons dashicons-arrow-down"></span><span class="dashicons-class">dashicons-arrow-down</span><span class="dashicons-code">“\f140”</span></li>
			<li><span class="dashicons dashicons-arrow-right"></span><span class="dashicons-class">dashicons-arrow-right</span><span class="dashicons-code">“\f139”</span></li>
			<li><span class="dashicons dashicons-arrow-left"></span><span class="dashicons-class">dashicons-arrow-left</span><span class="dashicons-code">“\f141”</span></li>
			<li><span class="dashicons dashicons-arrow-up-alt"></span><span class="dashicons-class">dashicons-arrow-up-alt</span><span class="dashicons-code">“\f342”</span></li>
			<li><span class="dashicons dashicons-arrow-down-alt"></span><span class="dashicons-class">dashicons-arrow-down-alt</span><span class="dashicons-code">“\f346”</span></li>
			<li><span class="dashicons dashicons-arrow-right-alt"></span><span class="dashicons-class">dashicons-arrow-right-alt</span><span class="dashicons-code">“\f344”</span></li>
			<li><span class="dashicons dashicons-arrow-left-alt"></span><span class="dashicons-class">dashicons-arrow-left-alt</span><span class="dashicons-code">“\f340”</span></li>
			<li><span class="dashicons dashicons-arrow-up-alt2"></span><span class="dashicons-class">dashicons-arrow-up-alt2</span><span class="dashicons-code">“\f343”</span></li>
			<li><span class="dashicons dashicons-arrow-down-alt2"></span><span class="dashicons-class">dashicons-arrow-down-alt2</span><span class="dashicons-code">“\f347”</span></li>
			<li><span class="dashicons dashicons-arrow-right-alt2"></span><span class="dashicons-class">dashicons-arrow-right-alt2</span><span class="dashicons-code">“\f345”</span></li>
			<li><span class="dashicons dashicons-arrow-left-alt2"></span><span class="dashicons-class">dashicons-arrow-left-alt2</span><span class="dashicons-code">“\f341”</span></li>
			<li><span class="dashicons dashicons-sort"></span><span class="dashicons-class">dashicons-sort</span><span class="dashicons-code">“\f156”</span></li>
			<li><span class="dashicons dashicons-leftright"></span><span class="dashicons-class">dashicons-leftright</span><span class="dashicons-code">“\f229”</span></li>
			<li><span class="dashicons dashicons-list-view"></span><span class="dashicons-class">dashicons-list-view</span><span class="dashicons-code">“\f163”</span></li>
			<li><span class="dashicons dashicons-exerpt-view"></span><span class="dashicons-class">dashicons-exerpt-view</span><span class="dashicons-code">“\f164”</span></li>
			<li><span class="dashicons dashicons-share"></span><span class="dashicons-class">dashicons-share</span><span class="dashicons-code">“\f237”</span></li>
			<li><span class="dashicons dashicons-share-alt"></span><span class="dashicons-class">dashicons-share-alt</span><span class="dashicons-code">“\f240”</span></li>
			<li><span class="dashicons dashicons-share-alt2"></span><span class="dashicons-class">dashicons-share-alt2</span><span class="dashicons-code">“\f242”</span></li>
			<li><span class="dashicons dashicons-twitter"></span><span class="dashicons-class">dashicons-twitter</span><span class="dashicons-code">“\f301”</span></li>
			<li><span class="dashicons dashicons-rss"></span><span class="dashicons-class">dashicons-rss</span><span class="dashicons-code">“\f303”</span></li>
			<li><span class="dashicons dashicons-email"></span><span class="dashicons-class">dashicons-email</span><span class="dashicons-code">“\f465”</span></li>
			<li><span class="dashicons dashicons-email-alt"></span><span class="dashicons-class">dashicons-email-alt</span><span class="dashicons-code">“\f466”</span></li>
			<li><span class="dashicons dashicons-facebook"></span><span class="dashicons-class">dashicons-facebook</span><span class="dashicons-code">“\f304”</span></li>
			<li><span class="dashicons dashicons-facebook-alt"></span><span class="dashicons-class">dashicons-facebook-alt</span><span class="dashicons-code">“\f305”</span></li>
			<li><span class="dashicons dashicons-googleplus"></span><span class="dashicons-class">dashicons-googleplus</span><span class="dashicons-code">“\f462”</span></li>
			<li><span class="dashicons dashicons-networking"></span><span class="dashicons-class">dashicons-networking</span><span class="dashicons-code">“\f325”</span></li>
			<li><span class="dashicons dashicons-hammer"></span><span class="dashicons-class">dashicons-hammer</span><span class="dashicons-code">“\f308”</span></li>
			<li><span class="dashicons dashicons-art"></span><span class="dashicons-class">dashicons-art</span><span class="dashicons-code">“\f309”</span></li>
			<li><span class="dashicons dashicons-migrate"></span><span class="dashicons-class">dashicons-migrate</span><span class="dashicons-code">“\f310”</span></li>
			<li><span class="dashicons dashicons-performance"></span><span class="dashicons-class">dashicons-performance</span><span class="dashicons-code">“\f311”</span></li>
			<li><span class="dashicons dashicons-wordpress"></span><span class="dashicons-class">dashicons-wordpress</span><span class="dashicons-code">“\f120”</span></li>
			<li><span class="dashicons dashicons-wordpress-alt"></span><span class="dashicons-class">dashicons-wordpress-alt</span><span class="dashicons-code">“\f324”</span></li>
			<li><span class="dashicons dashicons-pressthis"></span><span class="dashicons-class">dashicons-pressthis</span><span class="dashicons-code">“\f157”</span></li>
			<li><span class="dashicons dashicons-update"></span><span class="dashicons-class">dashicons-update</span><span class="dashicons-code">“\f463”</span></li>
			<li><span class="dashicons dashicons-screenoptions"></span><span class="dashicons-class">dashicons-screenoptions</span><span class="dashicons-code">“\f180”</span></li>
			<li><span class="dashicons dashicons-info"></span><span class="dashicons-class">dashicons-info</span><span class="dashicons-code">“\f348”</span></li>
			<li><span class="dashicons dashicons-cart"></span><span class="dashicons-class">dashicons-cart</span><span class="dashicons-code">“\f174”</span></li>
			<li><span class="dashicons dashicons-feedback"></span><span class="dashicons-class">dashicons-feedback</span><span class="dashicons-code">“\f175”</span></li>
			<li><span class="dashicons dashicons-cloud"></span><span class="dashicons-class">dashicons-cloud</span><span class="dashicons-code">“\f176”</span></li>
			<li><span class="dashicons dashicons-translation"></span><span class="dashicons-class">dashicons-translation</span><span class="dashicons-code">“\f326”</span></li>
			<li><span class="dashicons dashicons-tag"></span><span class="dashicons-class">dashicons-tag</span><span class="dashicons-code">“\f323”</span></li>
			<li><span class="dashicons dashicons-category"></span><span class="dashicons-class">dashicons-category</span><span class="dashicons-code">“\f318”</span></li>
			<li><span class="dashicons dashicons-yes"></span><span class="dashicons-class">dashicons-yes</span><span class="dashicons-code">“\f147”</span></li>
			<li><span class="dashicons dashicons-no"></span><span class="dashicons-class">dashicons-no</span><span class="dashicons-code">“\f158”</span></li>
			<li><span class="dashicons dashicons-no-alt"></span><span class="dashicons-class">dashicons-no-alt</span><span class="dashicons-code">“\f335”</span></li>
			<li><span class="dashicons dashicons-plus"></span><span class="dashicons-class">dashicons-plus</span><span class="dashicons-code">“\f132”</span></li>
			<li><span class="dashicons dashicons-minus"></span><span class="dashicons-class">dashicons-minus</span><span class="dashicons-code">“\f460”</span></li>
			<li><span class="dashicons dashicons-dismiss"></span><span class="dashicons-class">dashicons-dismiss</span><span class="dashicons-code">“\f153”</span></li>
			<li><span class="dashicons dashicons-marker"></span><span class="dashicons-class">dashicons-marker</span><span class="dashicons-code">“\f159”</span></li>
			<li><span class="dashicons dashicons-star-filled"></span><span class="dashicons-class">dashicons-star-filled</span><span class="dashicons-code">“\f155”</span></li>
			<li><span class="dashicons dashicons-star-half"></span><span class="dashicons-class">dashicons-star-half</span><span class="dashicons-code">“\f459”</span></li>
			<li><span class="dashicons dashicons-star-empty"></span><span class="dashicons-class">dashicons-star-empty</span><span class="dashicons-code">“\f154”</span></li>
			<li><span class="dashicons dashicons-flag"></span><span class="dashicons-class">dashicons-flag</span><span class="dashicons-code">“\f227”</span></li>
			<li><span class="dashicons dashicons-location"></span><span class="dashicons-class">dashicons-location</span><span class="dashicons-code">“\f230”</span></li>
			<li><span class="dashicons dashicons-location-alt"></span><span class="dashicons-class">dashicons-location-alt</span><span class="dashicons-code">“\f231”</span></li>
			<li><span class="dashicons dashicons-vault"></span><span class="dashicons-class">dashicons-vault</span><span class="dashicons-code">“\f178”</span></li>
			<li><span class="dashicons dashicons-shield"></span><span class="dashicons-class">dashicons-shield</span><span class="dashicons-code">“\f332”</span></li>
			<li><span class="dashicons dashicons-shield-alt"></span><span class="dashicons-class">dashicons-shield-alt</span><span class="dashicons-code">“\f334”</span></li>
			<li><span class="dashicons dashicons-sos"></span><span class="dashicons-class">dashicons-sos</span><span class="dashicons-code">“\f468”</span></li>
			<li><span class="dashicons dashicons-search"></span><span class="dashicons-class">dashicons-search</span><span class="dashicons-code">“\f179”</span></li>
			<li><span class="dashicons dashicons-slides"></span><span class="dashicons-class">dashicons-slides</span><span class="dashicons-code">“\f181”</span></li>
			<li><span class="dashicons dashicons-analytics"></span><span class="dashicons-class">dashicons-analytics</span><span class="dashicons-code">“\f183”</span></li>
			<li><span class="dashicons dashicons-chart-pie"></span><span class="dashicons-class">dashicons-chart-pie</span><span class="dashicons-code">“\f184”</span></li>
			<li><span class="dashicons dashicons-chart-bar"></span><span class="dashicons-class">dashicons-chart-bar</span><span class="dashicons-code">“\f185”</span></li>
			<li><span class="dashicons dashicons-chart-line"></span><span class="dashicons-class">dashicons-chart-line</span><span class="dashicons-code">“\f238”</span></li>
			<li><span class="dashicons dashicons-chart-area"></span><span class="dashicons-class">dashicons-chart-area</span><span class="dashicons-code">“\f239”</span></li>
			<li><span class="dashicons dashicons-groups"></span><span class="dashicons-class">dashicons-groups</span><span class="dashicons-code">“\f307”</span></li>
			<li><span class="dashicons dashicons-businessman"></span><span class="dashicons-class">dashicons-businessman</span><span class="dashicons-code">“\f338”</span></li>
			<li><span class="dashicons dashicons-id"></span><span class="dashicons-class">dashicons-id</span><span class="dashicons-code">“\f336”</span></li>
			<li><span class="dashicons dashicons-id-alt"></span><span class="dashicons-class">dashicons-id-alt</span><span class="dashicons-code">“\f337”</span></li>
			<li><span class="dashicons dashicons-products"></span><span class="dashicons-class">dashicons-products</span><span class="dashicons-code">“\f312”</span></li>
			<li><span class="dashicons dashicons-awards"></span><span class="dashicons-class">dashicons-awards</span><span class="dashicons-code">“\f313”</span></li>
			<li><span class="dashicons dashicons-forms"></span><span class="dashicons-class">dashicons-forms</span><span class="dashicons-code">“\f314”</span></li>
			<li><span class="dashicons dashicons-testimonial"></span><span class="dashicons-class">dashicons-testimonial</span><span class="dashicons-code">“\f473”</span></li>
			<li><span class="dashicons dashicons-portfolio"></span><span class="dashicons-class">dashicons-portfolio</span><span class="dashicons-code">“\f322”</span></li>
			<li><span class="dashicons dashicons-book"></span><span class="dashicons-class">dashicons-book</span><span class="dashicons-code">“\f330”</span></li>
			<li><span class="dashicons dashicons-book-alt"></span><span class="dashicons-class">dashicons-book-alt</span><span class="dashicons-code">“\f331”</span></li>
			<li><span class="dashicons dashicons-download"></span><span class="dashicons-class">dashicons-download</span><span class="dashicons-code">“\f316”</span></li>
			<li><span class="dashicons dashicons-upload"></span><span class="dashicons-class">dashicons-upload</span><span class="dashicons-code">“\f317”</span></li>
			<li><span class="dashicons dashicons-backup"></span><span class="dashicons-class">dashicons-backup</span><span class="dashicons-code">“\f321”</span></li>
			<li><span class="dashicons dashicons-clock"></span><span class="dashicons-class">dashicons-clock</span><span class="dashicons-code">“\f469”</span></li>
			<li><span class="dashicons dashicons-lightbulb"></span><span class="dashicons-class">dashicons-lightbulb</span><span class="dashicons-code">“\f339”</span></li>
			<li><span class="dashicons dashicons-desktop"></span><span class="dashicons-class">dashicons-desktop</span><span class="dashicons-code">“\f472”</span></li>
			<li><span class="dashicons dashicons-tablet"></span><span class="dashicons-class">dashicons-tablet</span><span class="dashicons-code">“\f471”</span></li>
			<li><span class="dashicons dashicons-smartphone"></span><span class="dashicons-class">dashicons-smartphone</span><span class="dashicons-code">“\f470”</span></li>
			<li><span class="dashicons dashicons-smiley"></span><span class="dashicons-class">dashicons-smiley</span><span class="dashicons-code">“\f328”</span></li>
			</ul>
			</div> <?php
			return ob_get_clean();
    }
