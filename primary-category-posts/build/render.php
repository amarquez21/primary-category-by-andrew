<?php
/**
 * @see https://github.com/WordPress/gutenberg/blob/trunk/docs/reference-guides/block-api/block-metadata.md#render
 */
?>




<?php 
// In your block's PHP render function:
function render_my_recent_posts_block() {
    ?>

	<?php
	$categories = get_categories();
	?>
	<ul class="cat-list">
		<?php foreach ($categories as $category) : ?>
			<li>
				<a class="cat-list_item" href="#" data-category-id="<?php echo $category->term_id; ?>" data-slug="<?= $category->slug; ?>">
					<?= $category->name; ?>
				</a>
			</li>
		<?php endforeach; ?>
	</ul>


	<?
}

function render_posts() {
	?>
	<div class="posts-wrapper">
	</div>
	<?php

}



?>
<div <?php echo get_block_wrapper_attributes(); ?>>
	<?php 
	echo render_my_recent_posts_block(); 
	echo render_posts();
	?>

</div>
