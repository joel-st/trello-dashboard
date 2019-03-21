<?php

namespace TvpTrelloDashboard\Plugin\Package;

class Pages {
	public $prefix;
	public $plugin_page;

	public $pages_options_group;
	public $pages_options_name;
	public $pages_section_name;
	private $pages_options;

	public function __construct() {
		$this->prefix      = tvp_tdash_plugin()->prefix;
		$this->plugin_page = tvp_tdash_plugin()->plugin_page;

		$this->pages_options_group = $this->prefix . '_pages_options';
		$this->pages_options_name  = $this->prefix . '_pages_settings';
		$this->pages_section_name  = $this->prefix . '_pages_section';
		$this->pages_options       = get_option( $this->pages_options_name );
	}



	/* ------------------------------------------------------------------------ *
	 * initialisation
	 * ------------------------------------------------------------------------ */

	public function run() {
		// make sure that the pages options exist
		if ( false == get_option( $this->pages_options_name ) ) {
			add_option( $this->pages_options_name );
		}

		// settings registration
		add_action( 'admin_init', [ $this, $this->prefix . '_register_settings' ] );

		// field registration
		add_action( 'admin_init', [ $this, $this->prefix . '_register_fields' ] );
	}



	/* ------------------------------------------------------------------------ *
	 * settings registration
	 * ------------------------------------------------------------------------ */

	/**
	 * register pages settings and pages settngs section
	 */
	public function tvp_tdash_register_settings() {
		register_setting(
			$this->pages_options_group, // group
			$this->pages_options_name  // name
		);

		add_settings_section(
			$this->pages_section_name, // id used to identify this section and with which to register options
			__( 'Pages Options', tvp_tdash_plugin()->text_domain ), // title to be displayed on the administration page
			[ $this, $this->prefix . '_section_callback' ], // callback used to render the description of the section
			$this->pages_options_group // page on which to add this section of settings
		);
	}

	/**
	 * this function provides a simple description for the settings page.
	 *
	 * it is called from the 'tvp_tdash_register_settings' function by being passed as a parameter
	 * in the add_settings_section function.
	 */
	public function tvp_tdash_section_callback() {
		echo '<p>Maecenas faucibus mollis interdum. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Aenean eu leo quam. Pellentesque ornare sem lacinia quam venenatis vestibulum. Cras mattis consectetur purus sit amet fermentum.</p>';
	}



	/* ------------------------------------------------------------------------ *
	 * fields registration
	 * ------------------------------------------------------------------------ */
	public function tvp_tdash_register_fields() {

		$page_for_dashboard_id = $this->prefix . '_dashboard_page';
		add_settings_field(
			$page_for_dashboard_id,                                // id used to identify the field throughout the theme
			__( 'Dashboard Page', tvp_tdash_plugin()->text_domain ), // the label to the left of the option interface element
			[ $this, $this->prefix . '_render_fields' ],        // the name of the function responsible for rendering the option interface
			$this->pages_options_group,                                // the page on which this option will be displayed
			$this->pages_section_name,                         // the name of the section to which this field belongs
			[                                                   // the array of arguments to pass to the callback.
				'field_id'          => $page_for_dashboard_id,
				'field_type'        => 'select',
				'field_description' => false,
			]
		);

		$page_for_submission = $this->prefix . '_submission_page';
		add_settings_field(
			$page_for_submission,                                // id used to identify the field throughout the theme
			__( 'Submission Page', tvp_tdash_plugin()->text_domain ), // the label to the left of the option interface element
			[ $this, $this->prefix . '_render_fields' ],        // the name of the function responsible for rendering the option interface
			$this->pages_options_group,                                // the page on which this option will be displayed
			$this->pages_section_name,                         // the name of the section to which this field belongs
			[                                                   // the array of arguments to pass to the callback.
				'field_id'          => $page_for_submission,
				'field_type'        => 'select',
				'field_description' => false,
			]
		);

	}

	/**
	 * this function renders the interface elements (fields).
	 *
	 * it accepts an array of arguments which is defined in each
	 * 'add_settings_field' parameter in the 'tvp_tdash_register_fields' function.
	 *
	 * default args are: 'field_id', 'field_type', 'field_description'
	 */
	public function tvp_tdash_render_fields( $args ) {
		// note the id and the name attribute of the field should match that of the id in the call to add_settings_field
		// the id is passed within the $args arguments

		// convert the $args array into an object
		$args = (object) $args;

		// start the output variable
		$output = '';

		if ( 'text' == $args->field_type ) {
			[ $args->field_id ];
			$name    = $this->pages_options_name . '[' . $args->field_id . ']';
			$output .= '<input type="text" id="' . $args->field_id . '" name="' . $name . '" value="' . esc_attr( $value ) . '" style="width: 100%;" />';
		} elseif ( 'select' == $args->field_type ) {

			// get all pages for our select field
			$exclude = [];

			// exclude special pages
			$exclude[] = get_option( 'page_on_front' );
			$exclude[] = get_option( 'page_for_posts' );
			// if ( $args->field_id == $this->prefix . '_submission_page' ) {
			// 	$exclude[] = $this->pages_options[ $this->prefix . '_dashboard_page' ];
			// } elseif ( $args->field_id == $this->prefix . '_dashboard_page' ) {
			// 	$exclude[] = $this->pages_options[ $this->prefix . '_submission_page' ];
			// }

			$pages = get_pages(
				[
					'sort_order'  => 'ASC',
					'sort_column' => 'post_title',
					'exclude'     => $exclude,
				]
			);

			// the select fields name
			$name     = $this->pages_options_name . '[' . $args->field_id . ']';
			$selected = $this->pages_options[ $args->field_id ];

			// create the select field
			$output .= '<select id="' . $args->field_id . '" name="' . $name . '">';
			// the default value
			$output .= '<option value="0" ' . selected( $selected, '0', false ) . ' >' . __( '-- Choose Page --', tvp_tdash_plugin()->text_domain ) . '</option>';
			// all pages
			foreach ( $pages as $key => $page ) {
				$output .= '<option value="' . $page->ID . '" ' . selected( $selected, $page->ID, false ) . ' >' . $page->post_title . '</option>';
			}
			$output .= '</select>';
		}

		echo $output;
	}

	/* ------------------------------------------------------------------------ *
	 * output the settings
	 * ------------------------------------------------------------------------ */

	public function tvp_tdash_render_pages_settings() {
		echo '<form method="post" action="options.php">';
		settings_fields( $this->pages_options_group );
		do_settings_sections( $this->pages_options_group );
		submit_button();
		echo '</form>';
	}

}
