<?php
/**
 * The post list template ( calendar )
 *
 * @author     FunnyWP
 * @package    WP Alpha Framework
 * @subpackage Theme
 * @since      1.0
 */
defined( 'ABSPATH' ) || die;

alpha_get_template_part( 'posts/loop/post', 'media' );
?>
<div class="post-details">
	<?php
	alpha_get_template_part( 'posts/loop/post', 'category' );
	alpha_get_template_part( 'posts/loop/post', 'title' );
	alpha_get_template_part( 'posts/loop/post', 'content' );
	alpha_get_template_part( 'posts/loop/post', 'meta' );
	alpha_get_template_part( 'posts/loop/post', 'readmore' );
	?>
</div>
