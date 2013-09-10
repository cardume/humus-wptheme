<?php
/**
 * The template for displaying Comments.
 *
 * The area of the page that contains comments and the comment form.
 *
 * @package WordPress
 * @subpackage Twenty_Thirteen
 * @since Twenty Thirteen 1.0
 */

/*
 * If the current post is protected by a password and the visitor has not yet
 * entered the password we will return early without loading the comments.
 */
if ( post_password_required() )
	return;
?>

<div class="container">

	<div id="comments" class="comments-area">

		<?php if ( have_comments() ) : ?>
			<div class="twelve columns">
				<h2 class="comments-title section-title">
					<?php
						printf( _nx( 'One comment', '%1$s comments', get_comments_number(), 'comments title', 'humus' ),
							number_format_i18n(get_comments_number()));
					?>
				</h2>
			</div>

			<div class="twelve columns">
				<ol class="comment-list">
					<?php
						wp_list_comments( array(
							'style'       => 'ol',
							'short_ping'  => true,
							'avatar_size' => 60,
							'callback' => 'humus_comment'
						) );
					?>
				</ol><!-- .comment-list -->
			</div>

			<?php
				// Are there comments to navigate through?
				if ( get_comment_pages_count() > 1 && get_option( 'page_comments' ) ) :
			?>
			<nav class="navigation comment-navigation" role="navigation">
				<h1 class="screen-reader-text section-heading"><?php _e( 'Comment navigation', 'humus' ); ?></h1>
				<div class="nav-previous"><?php previous_comments_link( __( '&larr; Older Comments', 'humus' ) ); ?></div>
				<div class="nav-next"><?php next_comments_link( __( 'Newer Comments &rarr;', 'humus' ) ); ?></div>
			</nav><!-- .comment-navigation -->
			<?php endif; // Check for comment navigation ?>

			<?php if ( ! comments_open() && get_comments_number() ) : ?>
			<p class="no-comments"><?php _e( 'Comments are closed.' , 'humus' ); ?></p>
			<?php endif; ?>

		<?php endif; // have_comments() ?>

		<?php comment_form(); ?>

	</div><!-- #comments -->

</div>