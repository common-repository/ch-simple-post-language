<?php

//* Don't access this file directly
defined( 'ABSPATH' ) or die();

/*
Plugin Name: CH Simple Post Language
Plugin URI: https://haensel.pro/wordpress-ch-simple-post-language-plugin
Description: Set the HTML language attribute for every single post or page
Author: Christian Hänsel
Version: 1.0
Author URI: https://haensel.pro
Text Domain: ch-post-lang
License: GPLv2

This is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 2 of the License, or
any later version.

This plugin is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this.
*/

class ChPostLang {

	public $languages;
	public $activated_languages;

	/**
	 * Der Konstruktor - hier bitte nichts ändern
	 *
	 * ChPostLang constructor.
	 */
	public function __construct() {

		if ( is_admin() ) {
			// admin only stuff
			add_action( 'add_meta_boxes', array( $this, 'ch_simple_lang_display_meta_box' ) );
			add_action( 'save_post', array( $this, 'ch_simple_lang_save_data' ) );
			add_action( 'admin_menu', array( $this, 'my_admin_menu' ) );
			$this->languages           = json_decode( $this->getFile( "languages.json" ) );
			$this->activated_languages = json_decode( get_option( 'ch_html_languages' ) );
			// Sort it
			natsort( $this->activated_languages );
		} else {
			// non-admin enqueues, actions, and filters
			add_filter( 'language_attributes', array( $this, 'language_attributes' ), 10, 2 );

		}
	}


	/**
	 * Get the content from plugin  files
	 *
	 * @param $templateName
	 *
	 * @return false|string
	 */
	public function getFile( $filename ) {
		$tpl = file_get_contents( plugin_dir_path( __FILE__ ) . $filename );

		return $tpl;
	}


	/**
	 * The method to add the meta box
	 */
	public function ch_simple_lang_display_meta_box() {
		add_meta_box(
			'ch_post_lang_box',          // this is HTML id of the box on edit screen
			'HTML Language',    // title of the box
			array( $this, 'ch_post_lang_custom_field_box' ),   // function to be called to display the checkboxes, see the function below
			[ 'page', 'post' ],        // on which edit screen the box should appear
			'side',      // part of page where the box should appear
			'high'      // priority of the box
		);
	}


	/**
	 * Display the admin meta box for the HTML language attribute
	 */
	public function ch_post_lang_custom_field_box( $post ) {
		$html_lang = get_post_meta( $post->ID, "ch_html_lang", true );
		echo 'Select a language for the HTML lang attribute';
		echo '<select name="ch_html_lang" id="ch_html_lang" style="width:100%">';

		$use_activated_languages = false;
		if ( count( $this->activated_languages ) > 0 ) {
			$use_activated_languages = true;
		}

		foreach ( $this->languages->languageCodes as $language ):
			$selected = '';
			if ( $html_lang == $language->code ) {
				$selected = 'selected';
			}
			$displayCode = $language->code;
			if ( $language->code == "" ) {
				$displayCode = get_bloginfo( 'language' );
			}

			if ( $use_activated_languages ) {
				if ( in_array( $language->code, $this->activated_languages ) || $language->code == "" ) {
					echo '<option value="' . $language->code . '" ' . $selected . '>' . $language->language . ' (' . $displayCode . ')</option>';
				}

			} else {
				echo '<option value="' . $language->code . '" ' . $selected . '>' . $language->language . ' (' . $displayCode . ')</option>';
			}


		endforeach;
		echo '</select>';
	}

	/**
	 * Saving data
	 *
	 * @param $post_id
	 */
	public function ch_simple_lang_save_data( $post_id ) {
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}
		if ( isset( $_POST['ch_html_lang'] ) ) {
			$html_lang = sanitize_text_field( $_POST['ch_html_lang'] );
			update_post_meta( $post_id, 'ch_html_lang', $html_lang );
		}
	}

	/**
	 * Set the langauge attributes
	 *
	 * @param $language_attributes
	 *
	 * @return string
	 */
	public function language_attributes( $language_attributes ) {
		global $post;
		$html_lang = get_post_meta( $post->ID, "ch_html_lang", true );
		if ( strlen( $html_lang ) > 0 ) {
			return 'lang="' . $html_lang . '"';
		}

		return 'lang="' . get_bloginfo( 'language' ) . '"';
	}

	/**
	 * Add the admin menu
	 */
	public function my_admin_menu() {
		add_menu_page( 'Simple Post Language', 'Simple Post Language', 'manage_options', 'ch-post-lang/ch-post-lang.php', array( $this, 'ch_post_lang_admin_page' ),
			'dashicons-tickets' );
	}

	public function ch_post_lang_admin_page() {

		// Saving options

		if ( isset( $_POST['saveLanguages'] ) ) {

			// Kategorien wurden gesetzt
			$html_languages = [];
			if ( is_array( $_POST['ch_html_languages'] ) && count( $_POST['ch_html_languages'] ) > 0 ) {
				foreach ( $_POST['ch_html_languages'] as $lang ) {
					$html_languages[] = sanitize_text_field( $lang );
				}
			}

			$lang_json = json_encode( $html_languages );
			update_option( 'ch_html_languages', $lang_json );
		}

		$html_languages_all       = $this->languages;
		$html_languages_activated = json_decode( get_option( 'ch_html_languages' ) );

		include( plugin_dir_path( __FILE__ ) . "pages/admin.php" );
	}

}

$chPostLang = new ChPostLang();

