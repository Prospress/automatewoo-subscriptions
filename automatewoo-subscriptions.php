<?php
/*
 * Plugin Name: AutomateWoo - Subscriptions Add-on
 * Plugin URI: https://github.com/Prospress/automatewoo-subscriptions/
 * Description: Advanced actions for automating a subscription's lifecycle with AutomateWoo.
 * Author: Prospress Inc.
 * Author URI: https://prospress.com/
 * License: GPLv3
 * Version: 0.0.1-beta-1
 * Requires at least: 4.0
 * Tested up to: 4.8
 *
 * WC requires at least: 3.0
 * WC tested up to: 3.5
 *
 * GitHub Plugin URI: Prospress/automatewoo-subscriptions
 * GitHub Branch: master
 *
 * Copyright 2018 Prospress, Inc.  (email : freedoms@prospress.com)
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @package		AutomateWoo Subscriptions
 * @author		Prospress Inc.
 * @since		1.0
 */

require_once( 'includes/class-pp-dependencies.php' );

if ( false === PP_Dependencies::is_woocommerce_active( '3.0' ) ) {
	PP_Dependencies::enqueue_admin_notice( 'AutomateWoo - Subscriptions Add-on', 'WooCommerce', '3.0' );
	return;
}

if ( false === PP_Dependencies::is_subscriptions_active( '2.4' ) ) {
	PP_Dependencies::enqueue_admin_notice( 'AutomateWoo - Subscriptions Add-on', 'WooCommerce Subscriptions', '2.4' );
	return;
}

if ( false === PP_Dependencies::is_automatewoo_active( '4.4' ) ) {
	PP_Dependencies::enqueue_admin_notice( 'AutomateWoo - Subscriptions Add-on', 'AutomateWoo', '4.4' );
	return;
}


/**
 * AutomateWoo Subscriptions add-on plugin singleton.
 *
 * We don't extend AutomateWoo\Addon here, because this plugin is really
 * just adding more actions. It's not a full-fledged add-on that requires
 * things like administration options screens. If we eventually add more
 * rules or variables, it may make more sense to extend that.
 *
 * @class   AutomateWoo_Subscriptions
 * @package AutomateWoo_Subscriptions
 */
final class AutomateWoo_Subscriptions {

	/**
	 * The plugin version.
	 *
	 * @var string
	 */
	private $version = '0.0.1';

	/**
	 * Instance of singleton.
	 *
	 * @var AutomateWoo_Subscriptions
	 */
	private static $_instance = null;

	/**
	 * Constructor
	 */
	private function __construct() {
		include_once $this->path() . '/includes/autoloader.php';
		add_action( 'automatewoo/actions', [ $this, 'add_actions' ], 20 );
	}

	/**
	 * Add advanced subscription actions to AutomateWoo's available actions.
	 *
	 * @param array $actions A set of actions returned by 'automatewoo/actions' in the form $action_name => $action_class
	 *
	 * @return array
	 */
	function add_actions( $actions ) {

		$actions = wcs_array_insert_after( 'subscription_send_invoice', $actions, 'subscription_update_schedule', 'AutomateWoo_Subscriptions\Action_Subscription_Update_Schedule' );

		$actions = array_merge( $actions, [
			'subscription_add_shipping'    => 'AutomateWoo_Subscriptions\Action_Subscription_Add_Shipping',
			'subscription_update_shipping' => 'AutomateWoo_Subscriptions\Action_Subscription_Update_Shipping',
			'subscription_remove_shipping' => 'AutomateWoo_Subscriptions\Action_Subscription_Remove_Shipping',
			'subscription_update_currency' => 'AutomateWoo_Subscriptions\Action_Subscription_Update_Currency',
		] );

		return $actions;
	}

	/**
	 * Get the path to something in the plugin dir.
	 *
	 * @param string $end End of the path.
	 *
	 * @return string
	 */
	function path( $end = '' ) {
		return untrailingslashit( dirname( __FILE__ ) ) . $end;
	}

	/**
	 * Return the singleton instance.
	 *
	 * @return AutomateWoo
	 */
	static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

}

/**
 * Access the plugin singleton with this.
 *
 * @return AutomateWoo
 */
function AW_Subscriptions() {
	return AutomateWoo_Subscriptions::instance();
}

AW_Subscriptions();
