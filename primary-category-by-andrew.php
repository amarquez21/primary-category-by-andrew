<?php
/**
 * Plugin Name:       Primary Category by Andrew
 * Description:       Adds option to set primary categories for posts. Also includes block to display and filter posts by primary category on frontend
 * Requires at least: 6.1
 * Requires PHP:      7.0
 * Version:           0.1.0
 * Author:            Andrew
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       primary-category-by-andrew
 *
 * @package CreateBlock
 */

 namespace Andrew\SlotFill;
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Registers the block using the metadata loaded from the `block.json` file.
 * Behind the scenes, it registers also all assets so they can be enqueued
 * through the block editor in the corresponding context.
 *
 * @see https://developer.wordpress.org/reference/functions/register_block_type/
 */


 // eneueue js
 function enqueue_block_editor_assets() {

	$index_assets = plugin_dir_path( __FILE__ ) . 'build/index.asset.php';

	if ( file_exists( $index_assets ) ) {

		$assets = require_once $index_assets;
		\wp_enqueue_script(
			'gutenberg-slot-fill',
			plugin_dir_url( __FILE__ ) . '/build/index.js',
			$assets[ 'dependencies' ],
			$assets['version'],
			true
		);

	}
 }
 \add_action( 'enqueue_block_editor_assets', __NAMESPACE__ . '\enqueue_block_editor_assets' );

 // Register custom meta tag field for posts
 function my_custom_field_setup() {
    register_post_meta(
        'post', // Post type (e.g., 'post', 'page', or custom post type)
        'primary_category', // Meta key
        array(
            'show_in_rest' => true, // Enable REST API support
            'single'       => true, // Whether it's a single value or an array
            'type'         => 'integer', // Data type (string, integer, etc.)
        )
    );
}
\add_action( 'init', __NAMESPACE__ . '\my_custom_field_setup' );



/**
 * Registers the block using the metadata loaded from the `block.json` file.
 * Behind the scenes, it registers also all assets so they can be enqueued
 * through the block editor in the corresponding context.
 *
 * @see https://developer.wordpress.org/reference/functions/register_block_type/
 */
function create_block_example_dynamic_block_init() {
	register_block_type( __DIR__ . '/primary-category-posts/build' );
}
\add_action( 'init', __NAMESPACE__ . '\create_block_example_dynamic_block_init' );


function register_primary_category_js() {
	wp_register_script( 'primary_category_script', plugin_dir_url( __FILE__ ) . '/primary-category-posts/js/primary-category.js',  array('jquery'), '', true );
}

\add_action( 'wp_enqueue_scripts', __NAMESPACE__ . '\register_primary_category_js' );



function localize_script() {
	$the_query = new \WP_Query();
	
	wp_localize_script('primary_category_script', 'primary_category_params', array(

		'ajaxurl' => site_url() . '/wp-admin/admin-ajax.php',
		'posts' => json_encode($the_query->query_vars),
		'post_id' => get_queried_object_id(),
	));
}

\add_action( 'wp_enqueue_scripts', __NAMESPACE__ . '\localize_script' );


function ajx_handle_my_action() {
	$categoryID = $_POST['category_id']; // Retrieve the category ID

	// wp_send_json([
	// 	'category-id' => $categoryID
	// ]);

    //$args = json_decode( stripslashes( $_GET['query'] ), true );
	$args = array(
		'post_type' => 'post',  // Replace 'post' with your desired post type
		'posts_per_page' => -1, // Number of posts to retrieve
		'meta_query' => array(
			array(
				'key' => 'primary_category',
				'value' => $categoryID,
				'compare' => '=', // Greater than or equal to
				'type' => 'NUMERIC', // Specify numeric type
			),
		),
	);


	// wp_send_json([
	// 	'args' => $args
	// ]);

	$custom_query = new \WP_Query($args);

	// Loop through the retrieved posts
	if ($custom_query->have_posts()) {
		while ($custom_query->have_posts()) {
			$custom_query->the_post();
			?>

			<div class="post-link">
				<a href="<?php the_permalink(); ?>">
					<?php echo the_title();?>
				</a>
			</div>

			<?php
		}
		wp_reset_postdata(); // Reset post data
	}

	die;
}

\add_action('wp_ajax_my_action_name', __NAMESPACE__ . '\ajx_handle_my_action');
\add_action('wp_ajax_nopriv_my_action_name', __NAMESPACE__ . '\ajx_handle_my_action');
