<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class questpopup {

	/**
	 * The single instance of questpopup.
	 * @var    object
	 * @access  private
	 * @since    1.0.0
	 */
	private static $_instance = null;

	/**
	 * Settings class object
	 * @var     object
	 * @access  public
	 * @since   1.0.0
	 */
	public $settings = null;

	/**
	 * The version number.
	 * @var     string
	 * @access  public
	 * @since   1.0.0
	 */
	public $_version;

	/**
	 * The token.
	 * @var     string
	 * @access  public
	 * @since   1.0.0
	 */
	public $_token;

	/**
	 * The main plugin file.
	 * @var     string
	 * @access  public
	 * @since   1.0.0
	 */
	public $file;

	/**
	 * The main plugin directory.
	 * @var     string
	 * @access  public
	 * @since   1.0.0
	 */
	public $dir;

	/**
	 * The plugin assets directory.
	 * @var     string
	 * @access  public
	 * @since   1.0.0
	 */
	public $assets_dir;

	/**
	 * The plugin assets URL.
	 * @var     string
	 * @access  public
	 * @since   1.0.0
	 */
	public $assets_url;

	/**
	 * Suffix for Javascripts.
	 * @var     string
	 * @access  public
	 * @since   1.0.0
	 */
	public $script_suffix;

	/**
	 * Constructor function.
	 * @access  public
	 * @since   1.0.0
	 * @return  void
	 */
	public function __construct( $file = '', $version = '1.0.0' ) {
		$this->_version = $version;
		$this->_token   = 'questpopup';

		// Load plugin environment variables
		$this->file       = $file;
		$this->dir        = dirname( $this->file );
		$this->assets_dir = trailingslashit( $this->dir ) . 'assets';
		$this->assets_url = esc_url( trailingslashit( plugins_url( '/assets/', $this->file ) ) );

		$this->script_suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

		register_activation_hook( $this->file, array( $this, 'install' ) );

		// Load frontend JS & CSS
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_styles' ), 10 );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ), 10 );

		// Load admin JS & CSS
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ), 10, 1 );
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_styles' ), 10, 1 );


		if ( get_option( 'qp_how_oftens' ) != 'never' ) {

			if ( get_option( 'qp_how_oftens' ) != 'everytime' ) {
				if ( ! is_admin() ) {
					add_action( 'init', array( $this, 'popupCookie' ), 0 );
				}
			}
			if ( ! is_admin() ) {
				add_action( 'wp_footer', array( $this, 'doPopup' ), 910 );
			}
		}

		// Load API for generic admin functions
		if ( is_admin() ) {
			$this->admin = new questpopup_Admin_API();
		}

		// Handle localisation
		$this->load_plugin_textdomain();
		add_action( 'init', array( $this, 'load_localisation' ), 0 );
	} // End __construct ()





	/**
	 * Load frontend CSS.
	 * @access  public
	 * @since   1.0.0
	 * @return void
	 */
	public function enqueue_styles() {
		wp_register_style( $this->_token . '-frontend', esc_url( $this->assets_url ) . 'css/frontend.css', array(), $this->_version );
		wp_enqueue_style( $this->_token . '-frontend' );
	} // End enqueue_styles ()

	/**
	 * Load frontend Javascript.
	 * @access  public
	 * @since   1.0.0
	 * @return  void
	 */
	public function enqueue_scripts() {
		wp_register_script( $this->_token . '-frontend', esc_url( $this->assets_url ) . 'js/frontend' . $this->script_suffix . '.js', array( 'jquery' ), $this->_version );
		wp_enqueue_script( $this->_token . '-frontend' );
	} // End enqueue_scripts ()

	/**
	 * Load admin CSS.
	 * @access  public
	 * @since   1.0.0
	 * @return  void
	 */
	public function admin_enqueue_styles( $hook = '' ) {
		wp_register_style( $this->_token . '-admin', esc_url( $this->assets_url ) . 'css/admin.css', array(), $this->_version );
		wp_enqueue_style( $this->_token . '-admin' );
	} // End admin_enqueue_styles ()

	/**
	 * Load admin Javascript.
	 * @access  public
	 * @since   1.0.0
	 * @return  void
	 */
	public function admin_enqueue_scripts( $hook = '' ) {
		wp_register_script( $this->_token . '-admin', esc_url( $this->assets_url ) . 'js/admin' . $this->script_suffix . '.js', array( 'jquery' ), $this->_version );
		wp_enqueue_script( $this->_token . '-admin' );
	} // End admin_enqueue_scripts ()

	/**
	 * Load plugin localisation
	 * @access  public
	 * @since   1.0.0
	 * @return  void
	 */
	public function load_localisation() {
		load_plugin_textdomain( 'questpopup', false, dirname( plugin_basename( $this->file ) ) . '/lang/' );
	} // End load_localisation ()

	/**
	 * Load plugin textdomain
	 * @access  public
	 * @since   1.0.0
	 * @return  void
	 */
	public function load_plugin_textdomain() {
		$domain = 'questpopup';

		$locale = apply_filters( 'plugin_locale', get_locale(), $domain );

		load_textdomain( $domain, WP_LANG_DIR . '/' . $domain . '/' . $domain . '-' . $locale . '.mo' );
		load_plugin_textdomain( $domain, false, dirname( plugin_basename( $this->file ) ) . '/lang/' );
	} // End load_plugin_textdomain ()

	/**
	 * Main questpopup Instance
	 *
	 * Ensures only one instance of questpopup is loaded or can be loaded.
	 *
	 * @since 1.0.0
	 * @static
	 * @see questpopup()
	 * @return Main questpopup instance
	 */
	public static function instance( $file = '', $version = '1.0.0' ) {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self( $file, $version );
		}

		return self::$_instance;
	} // End instance ()

	/**
	 * Cloning is forbidden.
	 *
	 * @since 1.0.0
	 */
	public function __clone() {
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?' ), $this->_version );
	} // End __clone ()

	/**
	 * Unserializing instances of this class is forbidden.
	 *
	 * @since 1.0.0
	 */
	public function __wakeup() {
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?' ), $this->_version );
	} // End __wakeup ()

	/**
	 * Installation. Runs on activation.
	 * @access  public
	 * @since   1.0.0
	 * @return  void
	 */
	public function install() {
		$this->_log_version_number();
	} // End install ()

	/**
	 * Log the plugin version number.
	 * @access  public
	 * @since   1.0.0
	 * @return  void
	 */
	private function _log_version_number() {
		update_option( $this->_token . '_version', $this->_version );
	} // End _log_version_number ()


	public function popupCookie() {
		if ( ! isset( $_COOKIE[ get_option( 'qp_cookie' ) ] ) ) {
			if ( get_option( 'qp_how_often' ) == 'everyday' ) {
				setcookie( get_option( 'qp_cookie' ), 1, time() + 3600 * 24 );
			} else {
				setcookie( get_option( 'qp_cookie' ), 1, time() + 3600 * 24 * 365 );
			}
		}
	}


	public function doPopup() {

		//which_pages
		if ( is_front_page() && ! in_array( 'main', get_option( 'qp_on_which_pages' ) ) ) {
			return;
		}

		if ( ! is_front_page() && ! in_array( 'notmain', get_option( 'qp_on_which_pages' ) ) ) {
			return;
		}

		if ( get_option( 'qp_how_often' ) == 'never' ) {
			return;
		}

		if ( get_option( 'qp_how_often' ) != 'everytime' ) {
			if ( isset( $_COOKIE[ get_option( 'qp_cookie' ) ] ) ) {
				return;
			}
		}


		echo '<style type="text/css">' . "\n";
		echo get_option( 'qp_extra_css' ) . "\n";

		echo '.btnwrap { padding: 1em; text-align: center; } ' . "\n";
		echo '.fancybox-skin h2 { font-size: 2.6em; text-align: center; }' . "\n";
		echo '.fancybox-skin h3 { font-size: 1.6em; text-align: center;  }' . "\n";
		echo '.fancybox-skin {' . "\n";

		$bgcolor = 'transparent';
		if ( in_array( 'color', get_option( 'qp_bg' ) ) ) {
			echo 'background-color:' . get_option( 'qp_bg_colour_picker' ) . ";\n";
			$bgcolor = get_option( 'qp_bg_colour_picker' );
		}

		if ( in_array( 'image', get_option( 'qp_bg' ) ) ) {
			echo 'background: url("' . wp_get_attachment_thumb_url( get_option( 'qp_bg_image' ) ) . '") scroll no-repeat top center  ' . $bgcolor . ";\n";
			echo 'background-size: cover' . ";\n";
		}

		echo 'color: ' . get_option( 'qp_text_colour_picker' ) . ";\n";

		echo " }\n";

		echo '.fancybox-skin .btn { ' .
		     'color: ' . get_option( 'qp_btn_text_colour_picker' ) . ";\n" .
		     'background-color: ' . get_option( 'qp_btn_colour_picker' ) . ";\n" .
		     'border-radius: 4px ' . ";\n" .
		     'text-align: center ' . ";\n" .
		     'padding: 0.6em 1em ' . ";\n" .
		     'text-decoration: none ' . ";\n" .
		     '}' . "\n";

		echo '</style>';

		$popupcontetn = '';

		if ( get_option( 'qp_h2_header_status' ) ) {
			$popupcontetn .= '<h2>' . get_option( 'qp_h2_header' ) . '</h2>';
		}

		if ( get_option( 'qp_h3_header_status' ) ) {
			$popupcontetn .= '<h3>' . get_option( 'qp_h3_header' ) . '</h3>';
		}

		$popupcontetn .= get_option( 'qp_main_text' );

		if ( get_option( 'qp_btn_status' ) ) {

			$href = ( strlen( get_option( 'qp_btn_href' ) ) > 1 ) ? get_option( 'qp_btn_href' ) : '#';

			$click = '';
			if ( strlen( get_option( 'qp_btn_onclick' ) ) > 2 ) {
				$click = ' onClick="';
				$click .= str_replace( '"', "'", get_option( 'qp_btn_onclick' ) );
				$click .= '" ';
			}

			$btn = '<a href="' . $href . '" ' . $click . ' class="btn">' . get_option( 'qp_btn_label' ) . '</a>';
			$popupcontetn .= '<div class="btnwrap">' . $btn . '</div>';
		}

		echo '<div style="display:none" id="qp_cont">';
		echo $popupcontetn;

		echo '</div>';


		if ( wp_script_is( 'jquery', 'done' ) ) {
			echo '<script type="text/javascript">';
			echo 'jQuery(document).ready(function() {' . "\n";

			if ( get_option( 'qp_delay' ) > 0 ) {
				echo 'setTimeout( function() {';
			}

			echo 'jQuery.fancybox(' . "\n" .
			     '{' . "\n";

			echo "'href': '#qp_cont', \n";

			if ( ( get_option( 'qp_window_width' ) != 'auto' ) or ( get_option( 'qp_window_height' ) != 'auto' ) ) {
				echo "'autoScale'	: false,\n" .
				     "'autoSize'	: false,\n" .
				     "'autoDimensions'	: false,\n" .
				     "'width'         		: '" . get_option( 'qp_window_width' ) . "',\n" .
				     "'height'        		: '" . get_option( 'qp_window_height' ) . "',\n";
			}


			echo "'padding'        		: " . get_option( 'qp_window_padding' ) . ",\n";


			echo '}' . "\n" .
			     ');' . "\n";

			if ( get_option( 'qp_delay' ) > 0 ) {
				echo '},' . (int) ( 1000 * get_option( 'qp_delay' ) ) . ');';
			}
			echo get_option( 'qp_extra_js' ) . "\n";

			echo '});';
			echo '</script>';
		}

	}


}