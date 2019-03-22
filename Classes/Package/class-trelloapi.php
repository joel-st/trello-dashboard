<?php

namespace TvpTrelloDashboard\Plugin\Package;

class TrelloAPI {
	public $prefix;
	public $plugin_page;

	public $trello_options_group;
	public $trello_options_name;
	public $trello_section_name;
	private $trello_options;

	public function __construct() {
		$this->prefix      = tvp_tdash_plugin()->prefix;
		$this->plugin_page = tvp_tdash_plugin()->plugin_page;

		$this->trello_options_group = $this->prefix . '_trello_options';
		$this->trello_options_name  = $this->prefix . '_trello_settings';
		$this->trello_section_name  = $this->prefix . '_trello_section';
		$this->trello_options       = get_option( $this->trello_options_name );
	}



	/* ------------------------------------------------------------------------ *
	 * initialisation
	 * ------------------------------------------------------------------------ */

	public function run() {
		// make sure that the trello options exist
		if ( false == get_option( $this->trello_options_name ) ) {
			add_option( $this->trello_options_name );
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
	 * register trello settings and trello settngs section
	 */
	public function tvp_tdash_register_settings() {
		register_setting(
			$this->trello_options_group, // group
			$this->trello_options_name  // name
		);

		add_settings_section(
			$this->trello_section_name, // id used to identify this section and with which to register options
			__( 'Trello API Options', tvp_tdash_plugin()->text_domain ), // title to be displayed on the administration page
			[ $this, $this->prefix . '_section_callback' ], // callback used to render the description of the section
			$this->trello_options_group // page on which to add this section of settings
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

		$board_id_field_id = $this->prefix . '_board_id';
		add_settings_field(
			$board_id_field_id,                                // id used to identify the field throughout the theme
			__( 'Board ID', tvp_tdash_plugin()->text_domain ), // the label to the left of the option interface element
			[ $this, $this->prefix . '_render_fields' ],        // the name of the function responsible for rendering the option interface
			$this->trello_options_group,                                // the page on which this option will be displayed
			$this->trello_section_name,                         // the name of the section to which this field belongs
			[                                                   // the array of arguments to pass to the callback.
				'field_id'          => $board_id_field_id,
				'field_type'        => 'text',
				'field_description' => false,
			]
		);

		$trello_api_key_field_id = $this->prefix . '_trello_api_key';
		add_settings_field(
			$trello_api_key_field_id,                                   // id used to identify the field throughout the theme
			__( 'Trello API Key', tvp_tdash_plugin()->text_domain ),    // the label to the left of the option interface element
			[ $this, $this->prefix . '_render_fields' ],                // the name of the function responsible for rendering the option interface
			$this->trello_options_group,                                        // the page on which this option will be displayed
			$this->trello_section_name,                                 // the name of the section to which this field belongs
			[                                                           // the array of arguments to pass to the callback.
				'field_id'          => $trello_api_key_field_id,
				'field_type'        => 'text',
				'field_description' => false,
			]
		);

		$trello_token_field_id = $this->prefix . '_trello_token';
		add_settings_field(
			$trello_token_field_id,                                 // id used to identify the field throughout the theme
			__( 'Trello Token', tvp_tdash_plugin()->text_domain ),  // the label to the left of the option interface element
			[ $this, $this->prefix . '_render_fields' ],            // the name of the function responsible for rendering the option interface
			$this->trello_options_group,                                    // the page on which this option will be displayed
			$this->trello_section_name,                             // the name of the section to which this field belongs
			[                                                       // the array of arguments to pass to the callback.
				'field_id'          => $trello_token_field_id,
				'field_type'        => 'text',
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
			$value   = $this->trello_options[ $args->field_id ];
			$name    = $this->trello_options_name . '[' . $args->field_id . ']';
			$output .= '<input type="text" id="' . $args->field_id . '" name="' . $name . '" value="' . esc_attr( $value ) . '" style="width: 100%;" />';
		}

		echo $output;
	}

	/* ------------------------------------------------------------------------ *
	 * output the settings
	 * ------------------------------------------------------------------------ */

	public function tvp_tdash_render_trello_api_settings() {
		echo '<form method="post" action="options.php">';
		settings_fields( $this->trello_options_group );
		do_settings_sections( $this->trello_options_group );
		submit_button();
		echo '</form>';
	}
}
