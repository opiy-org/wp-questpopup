<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class questpopup_Settings {

	/**
	 * The single instance of questpopup_Settings.
	 * @var    object
	 * @access  private
	 * @since    1.0.0
	 */
	private static $_instance = null;

	/**
	 * The main plugin object.
	 * @var    object
	 * @access  public
	 * @since    1.0.0
	 */
	public $parent = null;

	/**
	 * Prefix for plugin settings.
	 * @var     string
	 * @access  public
	 * @since   1.0.0
	 */
	public $base = '';

	/**
	 * Available settings for plugin.
	 * @var     array
	 * @access  public
	 * @since   1.0.0
	 */
	public $settings = array();

	public function __construct( $parent ) {
		$this->parent = $parent;

		$this->base = 'qp_';

		// Initialise settings
		add_action( 'init', array( $this, 'init_settings' ), 11 );

		// Register plugin settings
		add_action( 'admin_init', array( $this, 'register_settings' ) );

		// Add settings page to menu
		add_action( 'admin_menu', array( $this, 'add_menu_item' ) );

		// Add settings link to plugins page
		add_filter( 'plugin_action_links_' . plugin_basename( $this->parent->file ), array(
			$this,
			'add_settings_link'
		) );
	}

	/**
	 * Initialise settings
	 * @return void
	 */
	public function init_settings() {
		$this->settings = $this->settings_fields();
	}

	/**
	 * Add settings page to admin menu
	 * @return void
	 */
	public function add_menu_item() {
		$page = add_options_page( __( 'Настройки поп-апа', 'questpopup' ), __( 'Plugin Settings', 'questpopup' ), 'manage_options', $this->parent->_token . '_settings', array(
			$this,
			'settings_page'
		) );
		add_action( 'admin_print_styles-' . $page, array( $this, 'settings_assets' ) );
	}

	/**
	 * Load settings JS & CSS
	 * @return void
	 */
	public function settings_assets() {

		// We're including the farbtastic script & styles here because they're needed for the colour picker
		// If you're not including a colour picker field then you can leave these calls out as well as the farbtastic dependency for the wpt-admin-js script below
		wp_enqueue_style( 'farbtastic' );
		wp_enqueue_script( 'farbtastic' );

		// We're including the WP media scripts here because they're needed for the image upload field
		// If you're not including an image upload then you can leave this function call out
		wp_enqueue_media();

		wp_register_script( $this->parent->_token . '-settings-js', $this->parent->assets_url . 'js/settings' . $this->parent->script_suffix . '.js', array(
			'farbtastic',
			'jquery'
		), '1.0.0' );
		wp_enqueue_script( $this->parent->_token . '-settings-js' );
	}

	/**
	 * Add settings link to plugin list table
	 *
	 * @param  array $links Existing links
	 *
	 * @return array        Modified links
	 */
	public function add_settings_link( $links ) {
		$settings_link = '<a href="options-general.php?page=' . $this->parent->_token . '_settings">' . __( 'Настройки', 'questpopup' ) . '</a>';
		array_push( $links, $settings_link );

		return $links;
	}

	/**
	 * Build settings fields
	 * @return array Fields to be displayed on settings page
	 */
	private function settings_fields() {


		$settings['content'] = array(
			'title'       => __( 'Контент', 'questpopup' ),
			'description' => __( 'Здесь можно задать содержимое всплывающего окна', 'questpopup' ),

			'fields' => array(
				array(
					'id'          => 'h2_header',
					'label'       => __( 'Основной заголовок', 'questpopup' ),
					'description' => __( 'Текст внутри H2 тэга', 'questpopup' ),
					'type'        => 'text',
					'default'     => '',
				),

				array(
					'id'      => 'h2_header_status',
					'label'   => __( 'Показать заголовок?', 'questpopup' ),
					'type'    => 'checkbox',
					'default' => ''
				),

				array(
					'id'          => 'h3_header',
					'label'       => __( 'Подзаголовок', 'questpopup' ),
					'description' => __( 'Текст внутри H3 тэга', 'questpopup' ),
					'type'        => 'text',
					'default'     => '',
				),

				array(
					'id'      => 'h3_header_status',
					'label'   => __( 'Показать подзаголовок?', 'questpopup' ),
					'type'    => 'checkbox',
					'default' => ''
				),

				array(
					'id'          => 'main_text',
					'label'       => __( 'Содержимое окна', 'questpopup' ),
					'description' => __( 'Основной текст  <small>html</small>', 'questpopup' ),
					'type'        => 'textarea',
					'default'     => '',
				),

				array(
					'id'      => 'bg',
					'label'   => __( 'Фон', 'questpopup' ),
					'type'    => 'checkbox_multi',
					'options' => array(
						'color'    => 'Заливка цветом',
						'image' => 'Изображение',
					),
					'default' => array( 'main' )
				),


				array(
					'id'      => 'bg_colour_picker',
					'label'   => __( 'Цвет фона', 'questpopup' ),
					'type'    => 'color',
					'default' => '#21759B'
				),


				array(
					'id'          => 'bg_image',
					'label'       => __( 'фоновое изображение', 'questpopup' ),
					'type'        => 'image',
					'default'     => '',
					'placeholder' => ''
				),

				array(
					'id'      => 'text_colour_picker',
					'label'   => __( 'Цвет текста', 'questpopup' ),
					'type'    => 'color',
					'default' => '#FFFFFF'
				),

				array(
					'id'      => 'btn_label',
					'label'   => __( 'Текст на кнопке', 'questpopup' ),
					'type'    => 'text',
					'default' => '',
				),

				array(
					'id'      => 'btn_colour_picker',
					'label'   => __( 'Цвет кнопки', 'questpopup' ),
					'type'    => 'color',
					'default' => '#e4002e'
				),

				array(
					'id'      => 'btn_text_colour_picker',
					'label'   => __( 'Цвет текста кнопки', 'questpopup' ),
					'type'    => 'color',
					'default' => '#ffffff'
				),

				array(
					'id'          => 'btn_href',
					'label'       => __( 'Ссылка кнопки', 'questpopup' ),
					'description' => __( 'Адрес ссылки, куда перейдем по клику', 'questpopup' ),
					'type'        => 'text',
					'default'     => '',
				),

				array(
					'id'          => 'btn_onclick',
					'label'       => __( 'Действие по клику', 'questpopup' ),
					'description' => __( 'javascript по клику на кнопке', 'questpopup' ),
					'type'        => 'text',
					'default'     => '',
				),

				array(
					'id'      => 'btn_status',
					'label'   => __( 'Показать кнопку?', 'questpopup' ),
					'type'    => 'checkbox',
					'default' => ''
				),


			)
		);

		$settings['behaviour'] = array(
			'title'       => __( 'Поведение', 'questpopup' ),
			'description' => __( 'Настройки поведения окна', 'questpopup' ),
			'fields'      => array(
				array(
					'id'          => 'window_width',
					'label'       => __( 'Ширина окна', 'questpopup' ),
					'description' => __( 'пикселей  (auto - для авто размеров)', 'questpopup' ),
					'type'        => 'text',
					'default'     => '600',
				),


				array(
					'id'          => 'window_height',
					'label'       => __( 'Высота окна', 'questpopup' ),
					'description' => __( 'пикселей (auto - для авто размеров)', 'questpopup' ),
					'type'        => 'text',
					'default'     => '400',
				),

				array(
					'id'          => 'window_padding',
					'label'       => __( 'Отступы ', 'questpopup' ),
					'description' => __( 'пикселей (по внутреннему контуру)', 'questpopup' ),
					'type'        => 'text',
					'default'     => '10',
				),


				array(
					'id'      => 'on_which_pages',
					'label'   => __( 'На каких страницах показывать?', 'questpopup' ),
					'type'    => 'checkbox_multi',
					'options' => array(
						'main'    => 'Главная',
						'notmain' => 'Страницы кроме главной',
					),
					'default' => array( 'main' )
				),


				array(
					'id'          => 'how_often',
					'label'       => __( 'Как часто показывать?', 'questpopup' ),
					'description' => __( 'Одному пользователю', 'questpopup' ),
					'type'        => 'radio',
					'options'     => array(
						'never' => 'Никогда',
						'once' => 'Однажды',
						'everyday' => 'Каждый день',
						'everytime' => 'Каждый переход'
					),
					'default'     => 'once'
				),

				array(
					'id'          => 'cookie',
					'label'       => __( 'Уникальный id окна', 'questpopup' ),
					'description' => __( 'Чтобы проверять, показывали такое окно пользователю или нет', 'questpopup' ),
					'type'        => 'text',
					'default'     => 'qp_'.uniqid(),
				),


				array(
					'id'          => 'delay',
					'label'       => __( 'Задержка перед всплытием', 'questpopup' ),
					'description' => __( 'Через сколько секунд после захода на страницу всплывет окно', 'questpopup' ),
					'type'        => 'number',
					'default'     => '0',
				),

//				array(
//					'id'          => 'multi_select_box',
//					'label'       => __( 'A Multi-Select Box', 'questpopup' ),
//					'description' => __( 'A standard multi-select box - the saved data is stored as an array.', 'questpopup' ),
//					'type'        => 'select_multi',
//					'options'     => array( 'linux' => 'Linux', 'mac' => 'Mac', 'windows' => 'Windows' ),
//					'default'     => array( 'linux' )
//				)
			)
		);


		$settings['extra'] = array(
			'title'       => __( 'Дополнительно', 'questpopup' ),
			'description' => __( 'Тонкие ручные настройки', 'questpopup' ),

			'fields' => array(


				array(
					'id'          => 'extra_css',
					'label'       => __( 'Дополнительные стили', 'questpopup' ),
					'description' => __( 'css', 'questpopup' ),
					'type'        => 'textarea',
					'default'     => '',
				),


				array(
					'id'          => 'extra_js',
					'label'       => __( 'Дополнительные скрипты', 'questpopup' ),
					'description' => __( 'js', 'questpopup' ),
					'type'        => 'textarea',
					'default'     => '',
				),

			)
		);

		$settings = apply_filters( $this->parent->_token . '_settings_fields', $settings );

		return $settings;
	}

	/**
	 * Register plugin settings
	 * @return void
	 */
	public function register_settings() {
		if ( is_array( $this->settings ) ) {

			// Check posted/selected tab
			$current_section = '';
			if ( isset( $_POST['tab'] ) && $_POST['tab'] ) {
				$current_section = $_POST['tab'];
			} else {
				if ( isset( $_GET['tab'] ) && $_GET['tab'] ) {
					$current_section = $_GET['tab'];
				}
			}

			foreach ( $this->settings as $section => $data ) {

				if ( $current_section && $current_section != $section ) {
					continue;
				}

				// Add section to page
				add_settings_section( $section, $data['title'], array(
					$this,
					'settings_section'
				), $this->parent->_token . '_settings' );

				foreach ( $data['fields'] as $field ) {

					// Validation callback for field
					$validation = '';
					if ( isset( $field['callback'] ) ) {
						$validation = $field['callback'];
					}

					// Register field
					$option_name = $this->base . $field['id'];
					register_setting( $this->parent->_token . '_settings', $option_name, $validation );

					// Add field to page
					add_settings_field( $field['id'], $field['label'], array(
						$this->parent->admin,
						'display_field'
					), $this->parent->_token . '_settings', $section, array(
						'field'  => $field,
						'prefix' => $this->base
					) );
				}

				if ( ! $current_section ) {
					break;
				}
			}
		}
	}

	public function settings_section( $section ) {
		$html = '<p> ' . $this->settings[ $section['id'] ]['description'] . '</p>' . "\n";
		echo $html;
	}

	/**
	 * Load settings page content
	 * @return void
	 */
	public function settings_page() {

		// Build page HTML
		$html = '<div class="wrap" id="' . $this->parent->_token . '_settings">' . "\n";
		$html .= '<h2>' . __( 'Plugin Settings', 'questpopup' ) . '</h2>' . "\n";

		$tab = '';
		if ( isset( $_GET['tab'] ) && $_GET['tab'] ) {
			$tab .= $_GET['tab'];
		}

		// Show page tabs
		if ( is_array( $this->settings ) && 1 < count( $this->settings ) ) {

			$html .= '<h2 class="nav-tab-wrapper">' . "\n";

			$c = 0;
			foreach ( $this->settings as $section => $data ) {

				// Set tab class
				$class = 'nav-tab';
				if ( ! isset( $_GET['tab'] ) ) {
					if ( 0 == $c ) {
						$class .= ' nav-tab-active';
					}
				} else {
					if ( isset( $_GET['tab'] ) && $section == $_GET['tab'] ) {
						$class .= ' nav-tab-active';
					}
				}

				// Set tab link
				$tab_link = add_query_arg( array( 'tab' => $section ) );
				if ( isset( $_GET['settings-updated'] ) ) {
					$tab_link = remove_query_arg( 'settings-updated', $tab_link );
				}

				// Output tab
				$html .= '<a href="' . $tab_link . '" class="' . esc_attr( $class ) . '">' . esc_html( $data['title'] ) . '</a>' . "\n";

				++ $c;
			}

			$html .= '</h2>' . "\n";
		}

		$html .= '<form method="post" action="options.php" enctype="multipart/form-data">' . "\n";

		// Get settings fields
		ob_start();
		settings_fields( $this->parent->_token . '_settings' );
		do_settings_sections( $this->parent->_token . '_settings' );
		$html .= ob_get_clean();

		$html .= '<p class="submit">' . "\n";
		$html .= '<input type="hidden" name="tab" value="' . esc_attr( $tab ) . '" />' . "\n";
		$html .= '<input name="Submit" type="submit" class="button-primary" value="' . esc_attr( __( 'Save Settings', 'questpopup' ) ) . '" />' . "\n";
		$html .= '</p>' . "\n";
		$html .= '</form>' . "\n";
		$html .= '</div>' . "\n";

		echo $html;
	}

	/**
	 * Main questpopup_Settings Instance
	 *
	 * Ensures only one instance of questpopup_Settings is loaded or can be loaded.
	 *
	 * @since 1.0.0
	 * @static
	 * @see questpopup()
	 * @return Main questpopup_Settings instance
	 */
	public static function instance( $parent ) {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self( $parent );
		}

		return self::$_instance;
	} // End instance()

	/**
	 * Cloning is forbidden.
	 *
	 * @since 1.0.0
	 */
	public function __clone() {
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?' ), $this->parent->_version );
	} // End __clone()

	/**
	 * Unserializing instances of this class is forbidden.
	 *
	 * @since 1.0.0
	 */
	public function __wakeup() {
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?' ), $this->parent->_version );
	} // End __wakeup()

}