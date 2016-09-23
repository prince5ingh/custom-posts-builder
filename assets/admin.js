jQuery(document).ready( function($) {

	
	CPB_ADMIN = {

		/**
		 * Initialise all the functions which should execute on document.ready
		 * 
		 */
		init : function(){

			this.active_menu();
			this.create_cpt();
			this.admin_cols();
			this.create_tax();
			this.created_cpt();
			this.created_tax();
			this.help_tip();
			this.toggle_cpt_sections();
			this.save_cpt();
			this.cpt_action();
			this.tax_action();
			this.admin_filters();
			this.filter_dashicon();
			this.select_dashicon();
			this.media_upload();

			
		},

		htmlentities: function (str) {
			if(str) 
				return $("<textarea />").text(str).html();
			else
				return false;
		},

		html_entity_decode : function (str) {
			if(str) 
				return $("<textarea />").html(str).text();
			else
				return false;
		},

		active_menu : function() {
			$('body').on('click','.cpb-menu-list li',function(e) {
				$(this).siblings().removeClass('active');
				$(this).addClass('active');
			});

		},

		/**
		 * Shows create post typpe form to user
		 * @since 1.0
		 */
		create_cpt : function() {

			$('body').on('click','#cpb_create',function(e) {
				e.preventDefault();

				CPB_ADMIN.clean_section();

				var _html = $('.cpb-cpt-layout').clone();

				CPB_ADMIN.block_ui();

				$('.cpb-content').append(_html);
				_html.fadeIn( 400 );
				CPB_ADMIN.help_tip();
				_html.css({'transform':'scale(1)'});

				CPB_ADMIN.unblock_ui();

			});
		},

		/**
		 * Shows create taxonomy form to user
		 * @since 1.0
		 */
		create_tax : function() {

			$('body').on('click','#cpb_tax_create',function(e) {
				e.preventDefault();

				CPB_ADMIN.clean_section();

				var _html = $('.cpb-taxonomy-tpl').clone();

				CPB_ADMIN.block_ui();

				$('.cpb-content').append(_html);
				_html.fadeIn( 400 );
				CPB_ADMIN.help_tip();
				_html.css({'transform':'scale(1)'});

				CPB_ADMIN.unblock_ui();

			});
		},
		/**
		 * Toggles sections of post type creation form on clicking on their headings
		 * @since 1.0
		 */
		toggle_cpt_sections : function() {

			var sel = '.cpb-cpt-tpl h3, .cpb-taxonomy-tpl h3';

			$('body').on('click',sel,function(e) {
				e.preventDefault();

				if( $(this).find('span').hasClass('dashicons-arrow-down-alt2') )
					$(this).find('span').switchClass( "dashicons-arrow-down-alt2", "dashicons-arrow-right-alt2", 200, "easeInOutQuad" );

				else
					$(this).find('span').switchClass( "dashicons-arrow-right-alt2", "dashicons-arrow-down-alt2", 200, "easeInOutQuad" );

				$(this).next().fadeToggle();

			});
		},
		/**
		 * Shows help tip for each fields
		 * @since 1.0
		 */
		help_tip : function() {

			jQuery('.cpb-help').tipso({
				speed             : 400,        
//				background        : '#55b555',
				titleBackground   : '#333333',
				color             : '#ffffff',
				titleColor        : '#ffffff',
				titleContent      : '',       
				showArrow         : true,
				position          : 'top',
				width             : 200,
				maxWidth          : '',
				delay             : 200,
				hideDelay         : 0,
				animationIn       : '',
				animationOut      : '',
				offsetX           : 0,
				offsetY           : 0,
				tooltipHover      : false,
				content           : null,
				ajaxContentUrl    : null,
				contentElementId  : null,
				useTitle          : false,
				templateEngineFunc: null,
				onBeforeShow      : null,
				onShow            : null,
				onHide            : null
			});
		},
		/**
		 * Save post types & taxonomies
		 * 
		 */
		save_cpt : function() {
			$('body').on('click','.cpb_submit',function(e) {
				e.preventDefault();
				var form_data = $(this).closest('form').serializeArray();
				$.ajax({
					method	: 'POST',
					url 	: ajaxurl,
					data 	: form_data
				}).done( function(response) {
					$('.cpb-content .cpb-cpt-tpl').css({'transform':'scale(0)'}).remove();
					$(response).hide().appendTo('.cpb-content').fadeIn();
				});
			});
		},
		/**
		 * Shows list of created post types
		 * @since 1.0
		 */
		created_cpt : function() {

			$('body').on('click','#cpb_created',function(e) {
				e.preventDefault();

				CPB_ADMIN.clean_section();
				CPB_ADMIN.block_ui();

				$.ajax({
					method	: 'POST',
					url 	: ajaxurl,
					data 	: {
						'action' : 'show_cpb_cpt_list'
					}
				}).done( function(cpts_list) {

					cpts_list = $(cpts_list);
					$('.cpb-content').append(cpts_list);
					cpts_list.fadeIn( 400 );
					cpts_list.css({'transform':'scale(1)'});
					CPB_ADMIN.unblock_ui();
					
				});

			});
		},

		/**
		 * Shows list of created taxonomies
		 * @since 1.0
		 */
		created_tax : function() {

			$('body').on('click','#cpb_tax_created',function(e) {
				e.preventDefault();

				CPB_ADMIN.clean_section();
				CPB_ADMIN.block_ui();

				$.ajax({
					method	: 'POST',
					url 	: ajaxurl,
					data 	: {
						'action' : 'show_cpb_tax_list'
					}
				}).done( function(cpts_list) {

					cpts_list = $(cpts_list);
					$('.cpb-content').append(cpts_list);
					cpts_list.fadeIn( 400 );
					cpts_list.css({'transform':'scale(1)'});
					CPB_ADMIN.unblock_ui();
					
				});

			});
		},


		/**
		 * Perform edit & delete actions on cpt
		 * @return 
		 * @since  1.0
		 */
		cpt_action : function() {

			$('body').on('click','.cpb-cpt-list-item span',function(e) {

				e.preventDefault();
				var _this = $(this);
				CPB_ADMIN.block_ui();

				var action = $(this).data('action');

				$.ajax({
					method	: 'POST',
					url 	: ajaxurl,
					data 	: {
						'cpb_action' : action,
						'action' 	 : 'cpb_process_action',
						'cpt'		 : $(this).parent().text()
					}
				}).done( function(form_data) {

					switch(action) {

						case 'no':
							if(form_data == 'success') {
								_this.parent('.cpb-cpt-list-item').fadeIn(400,function() {
									_this.parent('.cpb-cpt-list-item').remove();
								});
							}
						break;

						case 'edit':

							CPB_ADMIN.clean_section();
							form_data 		= jQuery.parseJSON ( form_data );
							var labels 		= form_data.labels;
							var options 	= form_data.options;
							var supports 	= form_data.options.supports;
							// clone the add form and add form values of selected post type
							
							var _html = $('.cpb-cpt-layout').clone();
							$('.cpb-content').append(_html);
							_html.fadeIn( 400 ).find('input[name="action"]').val('cpb_cpt_update');
							CPB_ADMIN.help_tip();
							_html.css({'transform':'scale(1)'});

							// fill form labels
							$.each(labels,function(i,v) {

								if( $('#cpb_'+i).length ) {
									$('#cpb_'+i).val(v);
								}
							});
							// fill form options
							$.each(options,function(i,v) {

								if( $('#'+i).length ) {
									$('#'+i).val(v);
								}

								// for radio options
								if(v == 'true' || v == 'false') {
									$('#'+i+'_'+v).attr('checked','checked');
								}
							});

							if(supports) {

								// support checkboxes
								$(_html).find('[id^="supports_"]').attr('checked',false);
								$.each(supports,function(i,v) {

									if( $(_html).find('#supports_'+v).length ) {
										$(_html).find('#supports_'+v).attr('checked','checked');
									}
								});
							}
							
							// taxonomies
							if(form_data.taxonomies != '' ) {
								$('#cpb_taxonomies').val(form_data.taxonomies);
							}
						break;
					}
					CPB_ADMIN.unblock_ui();
					
				});

			});
		},

		/**
		 * Perform edit & delete actions on taxonomy
		 * @since  1.0
		 */
		tax_action : function() {

			$('body').on('click','.cpb-tax-list-item span',function(e) {

				e.preventDefault();
				var _this = $(this);
				CPB_ADMIN.block_ui();

				var action = $(this).data('action');

				$.ajax({
					method	: 'POST',
					url 	: ajaxurl,
					data 	: {
						'cpb_action' : action,
						'action' 	 : 'cpb_process_tax_action',
						'tax'		 : $(this).parent().text()
					}
				}).done( function(form_data) {

					switch(action) {

						case 'no':
							if(form_data == 'success') {
								_this.parent('.cpb-tax-list-item').fadeIn(400,function() {
									_this.parent('.cpb-tax-list-item').remove();
								});
							}
						break;

						case 'edit':

							CPB_ADMIN.clean_section();
							form_data 		= jQuery.parseJSON ( form_data );
							var labels 		= form_data.labels;
							var options 	= form_data.options;
							// clone the add form and add form values of selected post type
							
							var _html = $('.cpb-taxonomy-tpl').clone();
							$('.cpb-content').append(_html);
							_html.fadeIn( 400 ).find('input[name="action"]').val('cpb_tax_update');
							CPB_ADMIN.help_tip();
							_html.css({'transform':'scale(1)'});

							// fill form labels
							$.each(labels,function(i,v) {

								if( $('#cpb_'+i).length ) {
									$('#cpb_'+i).val(v);
								}
							});

							// fill form options
							$.each(options,function(i,v) {

								if( $('#'+i).length ) {
									$('#'+i).val(v);
								}

								// for radio options
								if(v == 'true' || v == 'false') {
									$('#'+i+'_'+v).attr('checked','checked');
								}
							});
						break;
					}
					CPB_ADMIN.unblock_ui();
					
				});

			});
		},

		/**
		 * blocks ui while ajax request in process
		 * @since 1.0
		 */
		block_ui : function() {
			$('.cbp-wrap').find('.cpb-block').css({'transform' : 'scale(1)'});
		},
		/**
		 * unblocks ui after ajax request
		 * @since 1.0
		 */
		unblock_ui : function() {
			$('.cbp-wrap').find('.cpb-block').css({'transform' : 'scale(0)'});
		},
		section_not_exists : function() {
			var sections = '.cpb-content .cpb-cpt-tpl, .cpb-content .cpb-cpt-list';
			
			if( $(sections).length == 0 ) {
				return true;
			}

			return false;
		},
		/**
		 * Clean content div after each ajax request for coming ajax request
		 * @since 1.0
		 */
		clean_section : function() {
			//var sections = '.cpb-content .cpb-cpt-tpl, .cpb-content .cpb-cpt-list, .cpb-notification-wrap, .cpb_confirm_box, .cpb-content .cpb-tax-list';
			
			//$(sections).fadeOut().remove();

			var save = $('.cpb-content .cpb-post-types').detach();
			$('.cpb-content').empty().append(save);


		},

		/**
		 * Admin columns for post types
		 * @since 1.0
		 */
		admin_cols : function() {

			$('body').on('click','#cpb_admin_columns',function(e) {
				e.preventDefault();

				CPB_ADMIN.clean_section();
				CPB_ADMIN.block_ui();

				$.ajax({
					method	: 'POST',
					url 	: ajaxurl,
					data 	: {
						'action' : 'show_admin_cols_ui'
					}
				}).done( function(cpts_list) {

					cpts_list = $(cpts_list);
					$('.cpb-content').append(cpts_list);
					cpts_list.fadeIn( 400 );
					cpts_list.css({'transform':'scale(1)'});

					CPB_ADMIN.unblock_ui();
					
				});

			});
		},

		/**
		 * Admin columns for post types
		 * @since 1.0
		*/
		admin_filters : function() {

			$('body').on('click','#cpb_admin_filters',function(e) {
				e.preventDefault();

				CPB_ADMIN.clean_section();
				CPB_ADMIN.block_ui();

				$.ajax({
					method	: 'POST',
					url 	: ajaxurl,
					data 	: {
						'action' : 'show_admin_filter_ui'
					}
				}).done( function(cpts_list) {

					cpts_list = $(cpts_list);
					$('.cpb-content').append(cpts_list);
					cpts_list.fadeIn( 400 );
					cpts_list.css({'transform':'scale(1)'});

					CPB_ADMIN.unblock_ui();
					
				});

			});
		},

		filter_dashicon : function() {
			/** dashicons */
			$(document).on('keyup','.cpb-input-wrap #menu_icon', function() {

				if( $(this).next('.cpb-dashicons').length <= 0 )
					$('.cpb-dashicons').clone().insertAfter($(this));

				var $rows 	= $(this).next().find('.cpb-dashicons-list li');
				var val 	= jQuery.trim(jQuery(this).val()).replace(/ +/g, ' ').toLowerCase();
				if(val == '')
					return false;
				$rows
				.hide()
				.filter(function() {
			    	var text 	= jQuery(this).find('.dashicons-class').text().replace(/\s+/g, ' ').toLowerCase();
					var status 	=  text.indexOf(val) >= 0 ? true : false;
					console.log(status);
					return status;
				}).show()
				.css({'display': 'inline-block'});
					
			});
		},
		select_dashicon : function() {
			/** dashicons */
			$(document).on('click','.cpb-dashicons-list li', function() {

				var icon = jQuery(this).find('.dashicons-class').text().replace(/\s+/g, ' ').toLowerCase();
				$(this).closest('.cpb-input-wrap').find('#menu_icon').val(icon);
				$(this).closest('.cpb-dashicons').fadeOut().remove();
					
			});
		},
		media_upload : function() {
			if ($('input[name="cpb_upload_button"]').length > 0) {
				if ( typeof wp !== 'undefined' && wp.media && wp.media.editor) {
				    $(document).on('click', 'input[name="cpb_upload_button"]', function(e) {
				        e.preventDefault();
				        var button = $(this);
				        var id = button.prev();	
				        wp.media.editor.send.attachment = function(props, attachment) {
				        	id.val(attachment.url);
				        };
				        wp.media.editor.open(button);
				        return false;
				    });
				}
			}
		},



	}

	CPB_ADMIN.init();

});