<?php
/**
 * AeenWoo API Helper.
 *
 * @package AeenWoo
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class AeenWoo_API {

	public static function is_configured() {
		$settings = get_option( 'aeen_woo_settings' );
		return ! empty( $settings['store_url'] ) && ! empty( $settings['access_code'] ) && ! empty( $settings['inbox_id'] );
	}

	public static function send_message( $phone, $message, $image_url = null ) {
		$settings = get_option( 'aeen_woo_settings' );

		if ( empty( $settings['store_url'] ) || empty( $settings['access_code'] ) || empty( $settings['inbox_id'] ) ) {
			return array(
				'status'  => 'error',
				'message' => 'Missing configuration.',
			);
		}

		$store_url = untrailingslashit( $settings['store_url'] );
		$url       = $store_url . '/webhooks/woo/send_message';

		$body = array(
			'access_code' => $settings['access_code'],
			'inbox_id'    => $settings['inbox_id'],
			'phone'       => $phone,
			'content'     => $message,
		);

		if ( ! empty( $image_url ) ) {
			$body['attachment_url'] = $image_url;
		}

		$response = wp_remote_post(
			$url,
			array(
				'body'      => $body,
				'timeout'   => 15,
				'sslverify' => true,
			)
		);

		if ( is_wp_error( $response ) ) {
			return array(
				'status'  => 'error',
				'message' => $response->get_error_message(),
			);
		}

		$body_content = wp_remote_retrieve_body( $response );
		$data         = json_decode( $body_content, true );

		if ( isset( $data['error'] ) ) {
			return array(
				'status'  => 'error',
				'message' => $data['error'],
			);
		}

		return array(
			'status'  => 'success',
			'message' => 'Message sent successfully',
		);
	}
}
