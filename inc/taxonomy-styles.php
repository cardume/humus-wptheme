<?php

/**
 * Taxonomy custom fields for styles
 * Fields:
 * - Icon
 * - Menu icon
 * - Color
 *
 * Requires ACF
 */

class Humus_Taxonomy_Styles {

	function __construct() {

		add_action('init', array($this, 'init'), 100);

	}

	function init() {

		if(function_exists('register_field_group')){
			$this->register_field_group();
			add_action('wp_head', array($this, 'color_var'), 5);
			add_action('wp_footer', array($this, 'set_page_color'), 15);
		}

	}

	function get_taxonomies() {
		return apply_filters('humus_styled_taxonomies', array());
	}

	function register_field_group() {

		$field_group = array(
			'id' => 'acf_taxonomy_styles',
			'title' => __('Taxonomy Styles', 'humus'),
			'fields' => array(
				array(
					'key' => 'field_tax_icon',
					'label' => __('Icon', 'humus'),
					'name' => 'term_icon',
					'type' => 'image',
					'instructions' => __('60x60 image to represent the section on the section\'s page and posts association', 'humus'),
					'save_format' => 'url',
					'preview_size' => 'thumbnail',
					'library' => 'all',
				),
				array(
					'key' => 'field_tax_menu_icon',
					'label' => __('Menu icon', 'humus'),
					'name' => 'term_menu_icon',
					'type' => 'image',
					'instructions' => __('21x21 image to represent the section on the navigation menu', 'humus'),
					'save_format' => 'url',
					'preview_size' => 'full',
					'library' => 'all',
				),
				array(
					'key' => 'field_tax_color',
					'label' => __('Color', 'humus'),
					'name' => 'term_color',
					'type' => 'color_picker',
					'default_value' => '',
				),
			),
			'options' => array(
				'position' => 'normal',
				'layout' => 'no_box',
				'hide_on_screen' => array(),
			),
			'menu_order' => 0,
		);

		$locations = array();

		$taxonomies = $this->get_taxonomies();

		if(is_array($taxonomies) && !empty($taxonomies)) {

			foreach($taxonomies as $taxonomy) {

				$locations[] = array(
					array(
						'param' => 'ef_taxonomy',
						'operator' => '==',
						'value' => $taxonomy,
						'order_no' => 0,
						'group_no' => 0,
					)
				);

			}

		}

		if(!empty($locations)) {

			$field_group['location'] = $locations;

			$field_group = apply_filters('humus_taxonomy_styles_field_group', $field_group);

			register_field_group($field_group);

		}

	}

    function get_post_color($post_id = false) {
        global $post;
        $post_id = $post_id ? $post_id : $post->ID;

        $taxonomies = $this->get_taxonomies();
        $term = false;

        foreach($taxonomies as $taxonomy) {

            if($term)
                continue;

            $terms = get_the_terms($post->ID, $taxonomy);

            if($terms) {

                foreach($terms as $t) {

                    if($term)
                        continue;

                    if(get_field('term_color', $t->taxonomy . '_' . $t->term_id))
                        $term = array_shift($terms);
                }

            }

        }

        $color = get_field('term_color', $term->taxonomy . '_' . $term->term_id);

        return $color;

    }

	function color_var() {

		if(!isset($GLOBALS['humus_page_color'])) {

			$taxonomies = $this->get_taxonomies();

			if(is_tax($taxonomies)) {

				$term = get_queried_object();
                $color = get_field('term_color', $term->taxonomy . '_' . $term->term_id);

			} elseif(is_single()) {

				$color = $this->get_post_color();

			}

			$GLOBALS['humus_page_color'] = apply_filters('humus_page_color', $color);

		}

	}

	function set_page_color() {

		$color = $GLOBALS['humus_page_color'];

		if($color) {
			?>
			<style>
			body::-webkit-scrollbar-thumb,
			.page-scrollbar-thumb::-webkit-scrollbar-thumb {
				background: <?php echo $color; ?> !important;
			}
			.page-color {
				color: <?php echo $color; ?> !important;
			}
			.page-background {
				background-color: <?php echo $color; ?> !important;
			}
			</style>
			<?php
		}
	}

}

$GLOBALS['humus_taxonomy_styles'] = new Humus_Taxonomy_Styles();

function humus_get_post_color($post_id = false) {
    return $GLOBALS['humus_taxonomy_styles']->get_post_color($post_id);
}

function humus_get_term_icon_url($id = false, $tax = false) {

	if($id)
		$field = get_field('term_icon', $id);

	if($field)
		return $field;

	if($tax) {
		$terms = get_the_terms($id, $tax);
		if($terms) {
			$term = array_shift($terms);
			$id = $tax . '_' . $term->term_id;
		}
	} elseif(is_tax()) {
		$term = get_term_by('slug', get_query_var('term'), get_query_var('taxonomy'));
		$id = get_query_var('taxonomy') . '_' . $term->term_id;
	} elseif(is_category()) {
		$term = get_category_by_slug(get_query_var('category_name'));
		$id = 'category_' . $term->term_id;
	} elseif(is_single() || get_post($id)) {
		global $post;
		$id = $post->ID;
	}

	if($id)
		return get_field('term_icon', $id);

	return false;
}
