<?php
/**
 * Elementor Widget for Vision IA Community
 */

if (!defined('ABSPATH')) {
    exit;
}

class VIC_Elementor_Widget extends \Elementor\Widget_Base {

    public function get_name() {
        return 'vic_community_feed';
    }

    public function get_title() {
        return 'Community Feed';
    }

    public function get_icon() {
        return 'eicon-comments';
    }

    public function get_categories() {
        return ['general'];
    }

    public function get_keywords() {
        return ['community', 'feed', 'skool', 'posts', 'forum'];
    }

    protected function register_controls() {

        // ========================================
        // CONTENT TAB
        // ========================================

        $this->start_controls_section(
            'content_section',
            [
                'label' => 'Contenu',
                'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'posts_per_page',
            [
                'label' => 'Posts par page',
                'type' => \Elementor\Controls_Manager::NUMBER,
                'default' => 10,
                'min' => 1,
                'max' => 50,
            ]
        );

        $this->add_control(
            'show_create_post',
            [
                'label' => 'Afficher "Écrire un post"',
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'show_filters',
            [
                'label' => 'Afficher les filtres',
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'default' => 'yes',
            ]
        );

        $this->end_controls_section();

        // ========================================
        // STYLE TAB - WRAPPER
        // ========================================

        $this->start_controls_section(
            'style_wrapper',
            [
                'label' => 'Conteneur principal',
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'wrapper_bg_color',
            [
                'label' => 'Couleur de fond',
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#F3F4F6',
                'selectors' => [
                    '{{WRAPPER}} .vic-community-wrapper' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'wrapper_max_width',
            [
                'label' => 'Largeur maximale',
                'type' => \Elementor\Controls_Manager::SLIDER,
                'size_units' => ['px', '%'],
                'range' => [
                    'px' => ['min' => 400, 'max' => 1400],
                    '%' => ['min' => 50, 'max' => 100],
                ],
                'default' => ['unit' => 'px', 'size' => 800],
                'selectors' => [
                    '{{WRAPPER}} .vic-community-wrapper' => 'max-width: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'wrapper_padding',
            [
                'label' => 'Padding',
                'type' => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em'],
                'default' => [
                    'top' => 24,
                    'right' => 20,
                    'bottom' => 24,
                    'left' => 20,
                    'unit' => 'px',
                ],
                'selectors' => [
                    '{{WRAPPER}} .vic-community-wrapper' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_section();

        // ========================================
        // STYLE TAB - POST CARDS
        // ========================================

        $this->start_controls_section(
            'style_post_card',
            [
                'label' => 'Cartes de posts',
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'card_bg_color',
            [
                'label' => 'Couleur de fond',
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#FFFFFF',
                'selectors' => [
                    '{{WRAPPER}} .vic-post-card' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'card_border_color',
            [
                'label' => 'Couleur de bordure',
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#E5E7EB',
                'selectors' => [
                    '{{WRAPPER}} .vic-post-card' => 'border-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'card_border_radius',
            [
                'label' => 'Rayon de bordure',
                'type' => \Elementor\Controls_Manager::SLIDER,
                'size_units' => ['px'],
                'range' => ['px' => ['min' => 0, 'max' => 30]],
                'default' => ['unit' => 'px', 'size' => 12],
                'selectors' => [
                    '{{WRAPPER}} .vic-post-card' => 'border-radius: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'card_padding',
            [
                'label' => 'Padding',
                'type' => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => ['px'],
                'default' => [
                    'top' => 24,
                    'right' => 24,
                    'bottom' => 24,
                    'left' => 24,
                    'unit' => 'px',
                ],
                'selectors' => [
                    '{{WRAPPER}} .vic-post-card' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
            'card_margin_bottom',
            [
                'label' => 'Espace entre les cartes',
                'type' => \Elementor\Controls_Manager::SLIDER,
                'size_units' => ['px'],
                'range' => ['px' => ['min' => 0, 'max' => 50]],
                'default' => ['unit' => 'px', 'size' => 12],
                'selectors' => [
                    '{{WRAPPER}} .vic-post-card' => 'margin-bottom: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'card_box_shadow',
                'label' => 'Ombre',
                'selector' => '{{WRAPPER}} .vic-post-card',
            ]
        );

        $this->end_controls_section();

        // ========================================
        // STYLE TAB - ADMIN POST BORDER
        // ========================================

        $this->start_controls_section(
            'style_admin_post',
            [
                'label' => 'Posts administrateur',
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'admin_border_color',
            [
                'label' => 'Couleur de bordure admin',
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#F59E0B',
                'selectors' => [
                    '{{WRAPPER}} .vic-post-card.vic-admin-post' => 'border-color: {{VALUE}}; border-width: 2px;',
                ],
            ]
        );

        $this->end_controls_section();

        // ========================================
        // STYLE TAB - TYPOGRAPHY
        // ========================================

        $this->start_controls_section(
            'style_typography',
            [
                'label' => 'Typographie',
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'title_heading',
            [
                'label' => 'Titre du post',
                'type' => \Elementor\Controls_Manager::HEADING,
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'post_title_typography',
                'label' => 'Typographie du titre',
                'selector' => '{{WRAPPER}} .vic-post-title',
            ]
        );

        $this->add_control(
            'post_title_color',
            [
                'label' => 'Couleur du titre',
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#111827',
                'selectors' => [
                    '{{WRAPPER}} .vic-post-title, {{WRAPPER}} .vic-post-title a' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'excerpt_heading',
            [
                'label' => 'Extrait du post',
                'type' => \Elementor\Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'post_excerpt_typography',
                'label' => 'Typographie de l\'extrait',
                'selector' => '{{WRAPPER}} .vic-post-excerpt',
            ]
        );

        $this->add_control(
            'post_excerpt_color',
            [
                'label' => 'Couleur de l\'extrait',
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#4B5563',
                'selectors' => [
                    '{{WRAPPER}} .vic-post-excerpt' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'author_heading',
            [
                'label' => 'Nom de l\'auteur',
                'type' => \Elementor\Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'author_name_typography',
                'label' => 'Typographie du nom',
                'selector' => '{{WRAPPER}} .vic-author-name',
            ]
        );

        $this->add_control(
            'author_name_color',
            [
                'label' => 'Couleur du nom',
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#111827',
                'selectors' => [
                    '{{WRAPPER}} .vic-author-name' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->end_controls_section();

        // ========================================
        // STYLE TAB - AVATAR
        // ========================================

        $this->start_controls_section(
            'style_avatar',
            [
                'label' => 'Avatars',
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'avatar_size',
            [
                'label' => 'Taille de l\'avatar auteur',
                'type' => \Elementor\Controls_Manager::SLIDER,
                'size_units' => ['px'],
                'range' => ['px' => ['min' => 20, 'max' => 80]],
                'default' => ['unit' => 'px', 'size' => 40],
                'selectors' => [
                    '{{WRAPPER}} .vic-author-info img' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
            'commenter_avatar_size',
            [
                'label' => 'Taille avatars commentateurs',
                'type' => \Elementor\Controls_Manager::SLIDER,
                'size_units' => ['px'],
                'range' => ['px' => ['min' => 16, 'max' => 50]],
                'default' => ['unit' => 'px', 'size' => 28],
                'selectors' => [
                    '{{WRAPPER}} .vic-commenters-avatars img' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
            'commenter_avatar_spacing',
            [
                'label' => 'Chevauchement avatars',
                'type' => \Elementor\Controls_Manager::SLIDER,
                'size_units' => ['px'],
                'range' => ['px' => ['min' => -20, 'max' => 10]],
                'default' => ['unit' => 'px', 'size' => -6],
                'selectors' => [
                    '{{WRAPPER}} .vic-commenters-avatars img:not(:first-child)' => 'margin-left: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_section();

        // ========================================
        // STYLE TAB - BUTTONS & INTERACTIONS
        // ========================================

        $this->start_controls_section(
            'style_buttons',
            [
                'label' => 'Boutons & Interactions',
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'like_color',
            [
                'label' => 'Couleur like (normal)',
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#9CA3AF',
                'selectors' => [
                    '{{WRAPPER}} .vic-like-btn svg' => 'color: {{VALUE}};',
                    '{{WRAPPER}} .vic-like-count' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'like_color_active',
            [
                'label' => 'Couleur like (actif)',
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#F59E0B',
                'selectors' => [
                    '{{WRAPPER}} .vic-like-btn.liked svg' => 'color: {{VALUE}}; fill: {{VALUE}}; stroke: {{VALUE}};',
                    '{{WRAPPER}} .vic-like-btn.liked .vic-like-count' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'comment_icon_color',
            [
                'label' => 'Couleur icône commentaires',
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#9CA3AF',
                'selectors' => [
                    '{{WRAPPER}} .vic-comment-btn svg' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'new_comment_color',
            [
                'label' => 'Couleur "Nouveau commentaire"',
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#3B82F6',
                'selectors' => [
                    '{{WRAPPER}} .vic-last-comment, {{WRAPPER}} .vic-last-comment a' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->end_controls_section();

        // ========================================
        // STYLE TAB - FILTERS
        // ========================================

        $this->start_controls_section(
            'style_filters',
            [
                'label' => 'Filtres',
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'filter_bg_color',
            [
                'label' => 'Couleur de fond',
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#FFFFFF',
                'selectors' => [
                    '{{WRAPPER}} .vic-filter-btn' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'filter_text_color',
            [
                'label' => 'Couleur du texte',
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#374151',
                'selectors' => [
                    '{{WRAPPER}} .vic-filter-btn' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'filter_active_bg_color',
            [
                'label' => 'Couleur fond (actif)',
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#111827',
                'selectors' => [
                    '{{WRAPPER}} .vic-filter-btn.active' => 'background-color: {{VALUE}}; border-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'filter_active_text_color',
            [
                'label' => 'Couleur texte (actif)',
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#FFFFFF',
                'selectors' => [
                    '{{WRAPPER}} .vic-filter-btn.active' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'filter_border_radius',
            [
                'label' => 'Rayon de bordure',
                'type' => \Elementor\Controls_Manager::SLIDER,
                'size_units' => ['px'],
                'range' => ['px' => ['min' => 0, 'max' => 30]],
                'default' => ['unit' => 'px', 'size' => 24],
                'selectors' => [
                    '{{WRAPPER}} .vic-filter-btn' => 'border-radius: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_section();

        // ========================================
        // STYLE TAB - THUMBNAIL
        // ========================================

        $this->start_controls_section(
            'style_thumbnail',
            [
                'label' => 'Miniature',
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'thumbnail_width',
            [
                'label' => 'Largeur',
                'type' => \Elementor\Controls_Manager::SLIDER,
                'size_units' => ['px'],
                'range' => ['px' => ['min' => 60, 'max' => 200]],
                'default' => ['unit' => 'px', 'size' => 130],
                'selectors' => [
                    '{{WRAPPER}} .vic-post-thumbnail' => 'width: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
            'thumbnail_height',
            [
                'label' => 'Hauteur',
                'type' => \Elementor\Controls_Manager::SLIDER,
                'size_units' => ['px'],
                'range' => ['px' => ['min' => 40, 'max' => 150]],
                'default' => ['unit' => 'px', 'size' => 95],
                'selectors' => [
                    '{{WRAPPER}} .vic-post-thumbnail' => 'height: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
            'thumbnail_border_radius',
            [
                'label' => 'Rayon de bordure',
                'type' => \Elementor\Controls_Manager::SLIDER,
                'size_units' => ['px'],
                'range' => ['px' => ['min' => 0, 'max' => 30]],
                'default' => ['unit' => 'px', 'size' => 12],
                'selectors' => [
                    '{{WRAPPER}} .vic-post-thumbnail' => 'border-radius: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
            'thumbnail_margin_top',
            [
                'label' => 'Marge supérieure',
                'type' => \Elementor\Controls_Manager::SLIDER,
                'size_units' => ['px'],
                'range' => ['px' => ['min' => 0, 'max' => 60]],
                'default' => ['unit' => 'px', 'size' => 32],
                'selectors' => [
                    '{{WRAPPER}} .vic-post-thumbnail' => 'margin-top: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_section();

        // ========================================
        // STYLE TAB - PINNED BADGE
        // ========================================

        $this->start_controls_section(
            'style_pinned',
            [
                'label' => 'Badge Épinglé',
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'pinned_color',
            [
                'label' => 'Couleur du texte',
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#6B7280',
                'selectors' => [
                    '{{WRAPPER}} .vic-pinned-badge' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'pinned_typography',
                'selector' => '{{WRAPPER}} .vic-pinned-badge',
            ]
        );

        $this->end_controls_section();

        // ========================================
        // STYLE TAB - CATEGORY TAG
        // ========================================

        $this->start_controls_section(
            'style_category',
            [
                'label' => 'Tag catégorie',
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'category_bg_color',
            [
                'label' => 'Couleur de fond',
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#F3F4F6',
                'selectors' => [
                    '{{WRAPPER}} .vic-category-tag' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'category_text_color',
            [
                'label' => 'Couleur du texte',
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#4B5563',
                'selectors' => [
                    '{{WRAPPER}} .vic-category-tag' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'category_border_radius',
            [
                'label' => 'Rayon de bordure',
                'type' => \Elementor\Controls_Manager::SLIDER,
                'size_units' => ['px'],
                'range' => ['px' => ['min' => 0, 'max' => 30]],
                'default' => ['unit' => 'px', 'size' => 20],
                'selectors' => [
                    '{{WRAPPER}} .vic-category-tag' => 'border-radius: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_section();

        // ========================================
        // ADVANCED TAB - CUSTOM CSS
        // ========================================

        $this->start_controls_section(
            'section_custom_css',
            [
                'label' => 'CSS Personnalisé',
                'tab' => \Elementor\Controls_Manager::TAB_ADVANCED,
            ]
        );

        $this->add_control(
            'custom_css',
            [
                'label' => 'CSS Personnalisé',
                'type' => \Elementor\Controls_Manager::CODE,
                'language' => 'css',
                'rows' => 20,
                'description' => 'Ajoutez votre CSS personnalisé ici. Utilisez {{WRAPPER}} pour cibler ce widget spécifiquement.',
            ]
        );

        $this->end_controls_section();
    }

    protected function render() {
        $settings = $this->get_settings_for_display();

        // Output custom CSS if provided
        if (!empty($settings['custom_css'])) {
            $custom_css = str_replace('{{WRAPPER}}', '.elementor-element-' . $this->get_id(), $settings['custom_css']);
            echo '<style>' . $custom_css . '</style>';
        }

        // Build shortcode attributes
        $atts = [
            'posts_per_page' => $settings['posts_per_page'],
        ];

        // Render the community feed
        echo do_shortcode('[community_feed posts_per_page="' . esc_attr($settings['posts_per_page']) . '"]');
    }
}

/**
 * Register the widget with Elementor
 */
function vic_register_elementor_widget($widgets_manager) {
    $widgets_manager->register(new VIC_Elementor_Widget());
}
add_action('elementor/widgets/register', 'vic_register_elementor_widget');
