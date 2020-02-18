<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://github.com/bianqui149
 * @since      1.0.0
 *
 * @package    Aws_Integration
 * @subpackage Aws_Integration/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Aws_Integration
 * @subpackage Aws_Integration/admin
 * @author     Julian Bianqui <bianquijulian@gmail.com>
 */


use Aws\S3\S3Client;
use Aws\S3\Exception\S3Exception;


class Aws_Integration_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {
		$this->plugin_name = $plugin_name;
		$this->version = $version;
		add_action( 'admin_menu', array( $this, 'add_options' ) );
		add_action( 'admin_init', array( $this, 'settings_aws_rn' ) );
	}
	public function settings_aws_rn() {
		//register our settings
		register_setting( 'aws_rn_credentials', 'aws_key_credential_rn' );
		register_setting( 'aws_rn_credentials', 'aws_password_credential_rn' );
		register_setting( 'aws_rn_credentials', 'aws_bucket_credential_rn' );
	}
	public function add_options() {
		 add_options_page( 'Aws Integration', 'Aws Integration', 'manage_options', 'aws-integration', array( $this, 'aws_options_page' ) );
	}
	public function aws_options_page(){
		
		// Display the object in the browser.

		include_once 'partials/aws-integration-admin-display.php';
	}
	public function setAwsFiles(){
		$bucketName = get_option( 'aws_bucket_credential' );
		$IAM_KEY = get_option( 'aws_key_credential' );
		$IAM_SECRET = get_option( 'aws_password_credential' );

		// Set Amazon S3 Credentials
		$s3 = S3Client::factory(
			array(
				'credentials' => array(
					'key' => $IAM_KEY,
					'secret' => $IAM_SECRET
				),
				'version' => 'latest',
				'region'  => 'us-east-2'
			)
		);
		if (!file_exists('/tmp/tmpfile')) {
			mkdir('/tmp/tmpfile');
		}
		$list_item = $this->query_by_month_attachment_rn();
		foreach ( $list_item as $key ) {
			$filePath     = $key['path'];
			$keyName      = basename( $filePath );
			$tempFilePath = '/tmp/tmpfile/' . basename( $filePath );
			$tempFile     = fopen( $tempFilePath, "w" ) or die( "Error: Unable to open file." );
			$fileContents = file_get_contents($filePath);
			$tempFile     = file_put_contents($tempFilePath, $fileContents);
			$str_guid     = str_replace( get_home_url() . '/wp-content/', '', $key['path'] );
			$s3->putObject(
				array(
					'Bucket'       => $bucketName,
					'Key'          =>  $str_guid,
					'SourceFile'   => $tempFilePath,
					'StorageClass' => 'REDUCED_REDUNDANCY'
				)
			);
		}
		if ( $s3 ) {
				return( 'Query success!' );
			} else {
				//put a try catch error here! 
				return ( 'Query error! More than two post contain errors' );
			}
	}
	protected function query_by_month_attachment_rn(){
		$args = array(
			'post_status'    => 'inherit',
			'post_type'      => 'attachment',
			'posts_per_page' => 10,
			'date_query'     => array(
				array(
					'year'  => 2019,
					'month' => 11,
				),
			),
		);
		$query = new WP_Query( $args );
		$list  = array();
		foreach ( $query->posts as $key ) {
			$list[]   = array( 'path' => $key->guid, 'name' => $key->post_title );	
		}
		return $list;
	}
	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Aws_Integration_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Aws_Integration_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/aws-integration-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Aws_Integration_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Aws_Integration_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/aws-integration-admin.js', array( 'jquery' ), $this->version, false );

	}

}
