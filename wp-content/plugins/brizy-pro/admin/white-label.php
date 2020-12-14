<?php

class BrizyPro_Admin_WhiteLabel {

	const KEY = 'brizy-white-label';
	const WL_SESSION_KEY = 'brizy-white-label-enabled';

	/**
	 * @var Brizy_TwigEngine
	 */
	private $twig;

	/**
	 * @var string[]
	 */
	private $values;

	/**
	 * @return BrizyPro_Admin_WhiteLabel
	 * @throws Exception
	 */
	public static function _init() {

		static $instance;

		return $instance ? $instance : $instance = new self();
	}

	/**
	 * BrizyPro_Admin_WhiteLabel constructor.
	 * @throws Exception
	 */
	private function __construct() {

		add_action( 'admin_init', array( $this, 'enableWhiteLabelInterface' ) );
		add_action( 'wp_logout', array( $this, 'disableWhiteLabelInterface' ) );
		add_action( 'admin_init', array( $this, '_action_enqueue_editor_assets' ), 9999 );

		if ( is_multisite() ) {
			add_action( 'network_admin_menu', array( $this, 'actionRegisterPage' ),11 );
		} else {
			if ( get_transient( self::WL_SESSION_KEY ) == 1 ) {
				add_action( 'admin_menu', array( $this, 'actionRegisterPage' ), 11 );
			}
		}

		// hide traces of brizy if the white labels was activated
		if ( $this->getEnabled() ) {
			if ( is_admin() && ( ! get_transient( self::WL_SESSION_KEY ) && ! is_network_admin() ) ) {
				add_filter( 'all_plugins', array( $this, 'hidePlugins' ) );
				add_filter( 'site_transient_update_plugins', array( $this, 'hideUpdatePlugins' ) );
			}

		}

		add_filter( 'brizy_wl_value', array( $this, 'filterKeys' ), 11 );
		add_filter( 'brizy_editor_config_texts', array( $this, 'editorConfigTexts' ) );

		if ( isset( $_REQUEST['brz-action'] ) && $_REQUEST['brz-action'] == 'save-values' ) {
			add_action( 'admin_init', array( $this, 'handleSubmit' ), 10 );
		}

		if ( isset( $_REQUEST['brz-action'] ) && $_REQUEST['brz-action'] == 'reset-values' ) {
			add_action( 'admin_init', array( $this, 'handleResetValues' ), 10 );
		}

		$this->values = $this->getValues();

		$this->twig = Brizy_TwigEngine::instance( BRIZY_PRO_PLUGIN_PATH . "/admin/views/" );
	}

	public function _action_enqueue_editor_assets() {

        if ( isset($_REQUEST['page']) &&  $_REQUEST['page']===self::KEY  ) {
            // jQuery
            wp_enqueue_script('jquery');
            // This will enqueue the Media Uploader script
            wp_enqueue_media();
        }

	}

	public function enableWhiteLabelInterface() {
		if ( isset( $_GET['brizy_enable_wl'] ) ) {

		    if ( is_network_admin() ) {
                $url = network_admin_url( 'admin.php?page=' . self::network_menu_slug(), false );
            } else {
		        $url = menu_page_url( Brizy_Admin_Settings::menu_slug(), false );
            }

            set_transient( self::WL_SESSION_KEY, 1, 3 * HOUR_IN_SECONDS );

			header( "location: " . $url );
			exit;
		}
	}

	public function disableWhiteLabelInterface() {
		if ( get_transient( self::WL_SESSION_KEY ) == 1 ) {
			delete_transient( self::WL_SESSION_KEY );
		}
	}

	public function getDefaultValues() {
		return array(
			'brizy'         => new BrizyPro_Whitelabel_Value( 'brizy', 'text', 'Brizy', 'Company Name' ),
			'brizy-prefix'  => new BrizyPro_Whitelabel_Value( 'brizy-refix', 'text', 'brizy', 'Prefix' ),
			'brizy-logo'    => new BrizyPro_Whitelabel_Value( 'logo-brizy-text', 'file', BRIZY_PLUGIN_URL . '/admin/static/img/brizy-logo.svg', 'Logo (20px x 20px)' ),
			'brizy-logo-2x' => new BrizyPro_Whitelabel_Value( 'logo-brizy-text-2x', 'file', BRIZY_PLUGIN_URL . '/admin/static/img/brizy-logo.svg', 'Logo Retina (40px x 40px)' ),
			'support-url'   => new BrizyPro_Whitelabel_Value( 'support-url', 'text', Brizy_Config::SUPPORT_URL, 'Support URL' ),
			'about-url'     => new BrizyPro_Whitelabel_Value( 'about-url', 'text', Brizy_Config::ABOUT_URL, 'About URL' )
		);
	}

	/**
	 * @return BrizyPro_Whitelabel_Value[]
	 */
	private function getValues() {

		if ( $this->values ) {
			return $this->values;
		}

		if ( is_multisite() ) {
			$data = get_network_option( null, self::KEY, $this->getDefaultValues() );
		} else {
			$data = get_option( self::KEY, $this->getDefaultValues() );
		}

		return $data;
	}

	/**
	 * @param $data
	 *
	 * @return $this
	 */
	private function saveValues( $data ) {

		$this->values = $data;

		if ( is_multisite() ) {
			update_network_option( null, self::KEY, $data );
		} else {
			update_option( self::KEY, $data, true );
		}

		// set the plugin prefix
		$values = $this->getValues();
		Brizy_Editor::setPrefix( $values['brizy-prefix']->getValue() );

		Brizy_Editor_Post::mark_all_for_compilation();

		return $this;
	}

	public function getEnabled() {

		$values  = $this->getValues();
		$enabled = false;

		if ( isset( $values['brizy'] ) && $values['brizy'] instanceof BrizyPro_Whitelabel_Value ) {
			$enabled = $values['brizy']->getValue() != 'Brizy';
		}

		return $enabled;
	}

	public function handleSubmit() {
		$data = array();

		foreach ( $this->getDefaultValues() as $key => $defaultValue ) {
			$data[ $key ] = new BrizyPro_Whitelabel_Value( $key, $_POST['values'][ $key ]['type'], wp_unslash( $_POST['values'][ $key ]['value'] ) );
		}

		$this->saveValues( $data );

		Brizy_Admin_Flash::instance()->add_success( __( 'Settings saved.', 'brizy-pro' ) );

		if ( is_multisite() ) {
			wp_redirect( network_admin_url( 'admin.php?page=' . self::network_menu_slug(), false ) );
		} else {
			wp_redirect( menu_page_url( self::menu_slug(), false ) );
		}

		exit;
	}

	public function handleResetValues() {
		$this->saveValues( $this->getDefaultValues() );

		Brizy_Admin_Flash::instance()->add_success( __( 'Settings saved.', 'brizy-pro' ) );

		if ( is_multisite() ) {
			wp_redirect( network_admin_url( 'admin.php?page=' . self::network_menu_slug(), false ) );
		} else {
			wp_redirect( menu_page_url( self::menu_slug(), false ) );
		}

		// reset the plugin prefix
		Brizy_Editor::setPrefix( 'brizy' );

		exit;
	}

	public function filterKeys( $data ) {

		if ( isset( $this->values[ $data['key'] ] ) && $this->values[ $data['key'] ] instanceof BrizyPro_Whitelabel_Value ) {
			return $this->values[ $data['key'] ]->getValue();
		}

		return $data;
	}

	public function actionRegisterPage() {

		add_submenu_page( is_multisite() ? Brizy_Admin_NetworkSettings::menu_slug() : Brizy_Admin_Settings::menu_slug(),
			__( 'White Label', 'brizy-pro' ),
			__( 'White Label', 'brizy-pro' ),
			is_multisite() ? 'manage_network' : 'manage_options',
			is_multisite() ? self::network_menu_slug() : self::menu_slug(),
			array( $this, 'render' )
		);
	}

	/**
	 * @throws Twig_Error_Loader
	 * @throws Twig_Error_Runtime
	 * @throws Twig_Error_Syntax
	 */
	public function render() {

		$context = array(
			'action'       => add_query_arg( 'brz-action', 'save-values', menu_page_url( self::menu_slug(), false ) ),
			'resetAction'  => add_query_arg( 'brz-action', 'reset-values', menu_page_url( self::menu_slug(), false ) ),
			'nonce'        => wp_nonce_field( 'validate-wl', '_wpnonce', true, false ),
			'defaultData'  => $this->getDefaultValues(),
			'data'         => $this->getValues(),
			'submit_label' => 'Save Changes',
			'message'      => isset( $_REQUEST['message'] ) ? $_REQUEST['message'] : null,
		);

		echo $this->twig->render( 'white-label.html.twig', $context );
	}

	public static function menu_slug() {
		return self::KEY;
	}

	public static function network_menu_slug() {
		return 'network-' . self::KEY;
	}

	public function hidePlugins( $plugins ) {
		if ( in_array( 'brizy-pro/brizy-pro.php', array_keys( $plugins ) ) ) {
			unset( $plugins['brizy-pro/brizy-pro.php'] );
		}
		if ( in_array( 'brizy/brizy.php', array_keys( $plugins ) ) ) {
			unset( $plugins['brizy/brizy.php'] );
		}

		return $plugins;
	}

	public function hideUpdatePlugins( $plugins ) {

		if ( isset($plugins->checked) && is_array( $plugins->checked ) && in_array( 'brizy-pro/brizy-pro.php', array_keys( $plugins->checked ) ) ) {
			unset( $plugins->checked['brizy-pro/brizy-pro.php'] );
		}
		if (  isset($plugins->checked) && is_array( $plugins->checked ) && in_array( 'brizy/brizy.php', array_keys( $plugins->checked ) ) ) {
			unset( $plugins->checked['brizy/brizy.php'] );
		}
		if (  isset($plugins->response) && is_array( $plugins->response ) && in_array( 'brizy-pro/brizy-pro.php', array_keys( $plugins->response ) ) ) {
			unset( $plugins->response['brizy-pro/brizy-pro.php'] );
		}
		if (  isset($plugins->response) && is_array( $plugins->response ) && in_array( 'brizy/brizy.php', array_keys( $plugins->response ) ) ) {
			unset( $plugins->response['brizy/brizy.php'] );
		}

		return $plugins;
	}

	public function editorConfigTexts( $texts ) {

		if ( ! $this->getEnabled() ) {
			return $texts;
		}

		$brizy = __bt( 'brizy', 'Brizy' );

		foreach ( $texts as $key => $text ) {
			if ( strpos( $text, 'Brizy' ) !== false ) {
				$texts[ $key ] = str_replace( 'Brizy', $brizy, $text );
			}
		}

		return $texts;
	}
}
