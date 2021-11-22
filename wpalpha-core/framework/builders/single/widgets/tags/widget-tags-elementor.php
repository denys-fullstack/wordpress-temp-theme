<?php
/**
 * Alpha Single Tags Elementor Widget
 *
 * @author     FunnyWP
 * @package    Alpha Core Framework
 * @subpackage Core
 * @since      1.0
 */
defined( 'ABSPATH' ) || die;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Typography;

class Alpha_Single_Tags_Elementor_Widget extends \Elementor\Widget_Base {

	public function get_name() {
		return ALPHA_NAME . '_single_tags';
	}

	public function get_title() {
		return esc_html__( 'Post Tags', 'alpha-core' );
	}

	public function get_icon() {
		return 'alpha-elementor-widget-icon eicon-tags';
	}

	public function get_categories() {
		return array( 'alpha_single_widget' );
	}

	public function get_keywords() {
		return array( 'single', 'custom', 'layout', 'post', 'tags' );
	}

	protected function _register_controls() {

		$this->start_controls_section(
			'section_single_tags',
			array(
				'label' => esc_html__( 'Style', 'alpha-core' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

			$this->add_control(
				'sp_align',
				array(
					'label'     => esc_html__( 'Align', 'alpha-core' ),
					'type'      => Controls_Manager::CHOOSE,
					'options'   => array(
						'left'    => array(
							'title' => esc_html__( 'Left', 'alpha-core' ),
							'icon'  => 'eicon-text-align-left',
						),
						'center'  => array(
							'title' => esc_html__( 'Center', 'alpha-core' ),
							'icon'  => 'eicon-text-align-center',
						),
						'right'   => array(
							'title' => esc_html__( 'Right', 'alpha-core' ),
							'icon'  => 'eicon-text-align-right',
						),
						'justify' => array(
							'title' => esc_html__( 'Justify', 'alpha-core' ),
							'icon'  => 'eicon-text-align-justify',
						),
					),
					'selectors' => array(
						'.elementor-element-{{ID}} .post-tags' => 'text-align: {{VALUE}};',
					),
				)
			);

			$this->add_group_control(
				Group_Control_Typography::get_type(),
				array(
					'name'     => 'link_typography',
					'selector' => '.elementor-element-{{ID}} a',
				)
			);

			$this->add_group_control(
				Group_Control_Border::get_type(),
				array(
					'name'     => 'border',
					'selector' => '.elementor-element-{{ID}} a',
				)
			);

			$this->add_control(
				'separator_sp_style',
				array(
					'type' => Controls_Manager::DIVIDER,
				)
			);

			$this->start_controls_tabs( 'sp_tab_color' );
				$this->start_controls_tab(
					'sp_tab_normal',
					array(
						'label' => esc_html__( 'Normal', 'alpha-core' ),
					)
				);

				$this->add_control(
					'bg_color',
					array(
						'label'     => esc_html__( 'Background Color', 'alpha-core' ),
						'type'      => Controls_Manager::COLOR,
						'selectors' => array(
							'.elementor-element-{{ID}} a' => 'background-color: {{VALUE}}',
						),
					)
				);

				$this->add_control(
					'link_color',
					array(
						'label'     => esc_html__( 'Color', 'alpha-core' ),
						'type'      => Controls_Manager::COLOR,
						'selectors' => array(
							'.elementor-element-{{ID}} a' => 'color: {{VALUE}}',
						),
					)
				);

				$this->end_controls_tab();

				$this->start_controls_tab(
					'sp_tab_hover',
					array(
						'label' => esc_html__( 'Hover', 'alpha-core' ),
					)
				);

					$this->add_control(
						'bg_h_color',
						array(
							'label'     => esc_html__( 'Background Color', 'alpha-core' ),
							'type'      => Controls_Manager::COLOR,
							'selectors' => array(
								'.elementor-element-{{ID}} a:hover, .elementor-element-{{ID}} a:focus' => 'background-color: {{VALUE}}',
							),
						)
					);

					$this->add_control(
						'link_h_color',
						array(
							'label'     => esc_html__( 'Color', 'alpha-core' ),
							'type'      => Controls_Manager::COLOR,
							'selectors' => array(
								'.elementor-element-{{ID}} a:hover, .elementor-element-{{ID}} a:focus' => 'color: {{VALUE}}',
							),
						)
					);

					$this->add_control(
						'bd_h_color',
						array(
							'label'     => esc_html__( 'Border Color', 'alpha-core' ),
							'type'      => Controls_Manager::COLOR,
							'selectors' => array(
								'.elementor-element-{{ID}} a:hover, .elementor-element-{{ID}} a:focus' => 'border-color: {{VALUE}}',
							),
						)
					);

				$this->end_controls_tab();

			$this->end_controls_tabs();

		$this->end_controls_section();
	}

	protected function render() {
		if ( apply_filters( 'alpha_single_builder_set_preview', false ) ) {
			alpha_get_template_part( 'posts/single/post', 'tag' );
			do_action( 'alpha_single_builder_unset_preview' );
		}
	}
}
