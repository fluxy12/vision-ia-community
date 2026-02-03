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

        // === NOUVEAUX CONTRÔLES PARTIE 1 ===

        $this->add_responsive_control(
            'wrapper_margin',
            [
                'label' => 'Marge externe',
                'type' => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'default' => [
                    'top' => 0,
                    'right' => 'auto',
                    'bottom' => 0,
                    'left' => 'auto',
                    'unit' => 'px',
                    'isLinked' => false,
                ],
                'selectors' => [
                    '{{WRAPPER}} .vic-community-wrapper' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Border::get_type(),
            [
                'name' => 'wrapper_border',
                'label' => 'Bordure',
                'selector' => '{{WRAPPER}} .vic-community-wrapper',
            ]
        );

        $this->add_control(
            'wrapper_border_radius',
            [
                'label' => 'Rayon de bordure',
                'type' => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'default' => [
                    'top' => 0,
                    'right' => 0,
                    'bottom' => 0,
                    'left' => 0,
                    'unit' => 'px',
                ],
                'selectors' => [
                    '{{WRAPPER}} .vic-community-wrapper' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'wrapper_box_shadow',
                'label' => 'Ombre portée',
                'selector' => '{{WRAPPER}} .vic-community-wrapper',
            ]
        );

        // === FIN NOUVEAUX CONTRÔLES PARTIE 1 ===

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

        // === NOUVEAUX CONTRÔLES PARTIE 2 ===

        $this->add_control(
            'card_border_width',
            [
                'label' => 'Largeur de bordure',
                'type' => \Elementor\Controls_Manager::SLIDER,
                'size_units' => ['px'],
                'range' => ['px' => ['min' => 0, 'max' => 10]],
                'default' => ['unit' => 'px', 'size' => 1],
                'selectors' => [
                    '{{WRAPPER}} .vic-post-card' => 'border-width: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
            'card_border_style',
            [
                'label' => 'Style de bordure',
                'type' => \Elementor\Controls_Manager::SELECT,
                'default' => 'solid',
                'options' => [
                    'none' => 'Aucune',
                    'solid' => 'Solide',
                    'dashed' => 'Tirets',
                    'dotted' => 'Pointillés',
                    'double' => 'Double',
                    'groove' => 'Groove',
                    'ridge' => 'Ridge',
                ],
                'selectors' => [
                    '{{WRAPPER}} .vic-post-card' => 'border-style: {{VALUE}};',
                ],
            ]
        );

        // === FIN NOUVEAUX CONTRÔLES PARTIE 2 ===

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

        // === EFFETS AU SURVOL (HOVER) PARTIE 2 ===

        $this->add_control(
            'card_hover_heading',
            [
                'label' => 'Effets au survol',
                'type' => \Elementor\Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );

        $this->add_control(
            'card_hover_bg_color',
            [
                'label' => 'Couleur de fond (survol)',
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .vic-post-card:hover' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'card_hover_border_color',
            [
                'label' => 'Couleur bordure (survol)',
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .vic-post-card:hover' => 'border-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'card_hover_box_shadow',
                'label' => 'Ombre (survol)',
                'selector' => '{{WRAPPER}} .vic-post-card:hover',
            ]
        );

        $this->add_control(
            'card_hover_transform',
            [
                'label' => 'Effet de transformation',
                'type' => \Elementor\Controls_Manager::SELECT,
                'default' => 'none',
                'options' => [
                    'none' => 'Aucun',
                    'translateY(-2px)' => 'Légère élévation',
                    'translateY(-4px)' => 'Élévation moyenne',
                    'translateY(-6px)' => 'Forte élévation',
                    'scale(1.01)' => 'Léger zoom',
                    'scale(1.02)' => 'Zoom moyen',
                ],
                'selectors' => [
                    '{{WRAPPER}} .vic-post-card:hover' => 'transform: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'card_transition_duration',
            [
                'label' => 'Durée de transition',
                'type' => \Elementor\Controls_Manager::SLIDER,
                'size_units' => ['s', 'ms'],
                'range' => [
                    's' => ['min' => 0, 'max' => 1, 'step' => 0.1],
                    'ms' => ['min' => 0, 'max' => 1000, 'step' => 50],
                ],
                'default' => ['unit' => 's', 'size' => 0.3],
                'selectors' => [
                    '{{WRAPPER}} .vic-post-card' => 'transition: all {{SIZE}}{{UNIT}} ease;',
                ],
            ]
        );

        // === FIN EFFETS AU SURVOL PARTIE 2 ===

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

        // === NOUVEAUX CONTRÔLES PARTIE 4 - TITRES ===

        $this->add_control(
            'post_title_color_hover',
            [
                'label' => 'Couleur du titre (survol)',
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#4F46E5',
                'selectors' => [
                    '{{WRAPPER}} .vic-post-title:hover, {{WRAPPER}} .vic-post-title a:hover' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'post_title_spacing',
            [
                'label' => 'Espacement sous le titre',
                'type' => \Elementor\Controls_Manager::SLIDER,
                'size_units' => ['px', 'em'],
                'range' => [
                    'px' => ['min' => 0, 'max' => 30],
                    'em' => ['min' => 0, 'max' => 2],
                ],
                'default' => ['unit' => 'px', 'size' => 8],
                'selectors' => [
                    '{{WRAPPER}} .vic-post-title' => 'margin-bottom: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        // === FIN NOUVEAUX CONTRÔLES PARTIE 4 ===

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

        // === NOUVEAUX CONTRÔLES PARTIE 5 - CONTENU ===

        $this->add_control(
            'post_excerpt_lines',
            [
                'label' => 'Nombre de lignes (extrait)',
                'type' => \Elementor\Controls_Manager::NUMBER,
                'default' => 3,
                'min' => 1,
                'max' => 10,
                'selectors' => [
                    '{{WRAPPER}} .vic-post-excerpt' => 'display: -webkit-box; -webkit-line-clamp: {{VALUE}}; -webkit-box-orient: vertical; overflow: hidden;',
                ],
            ]
        );

        $this->add_control(
            'content_heading',
            [
                'label' => 'Contenu complet',
                'type' => \Elementor\Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'post_content_typography',
                'label' => 'Typographie du contenu',
                'selector' => '{{WRAPPER}} .vic-post-content, {{WRAPPER}} .vic-modal-content',
            ]
        );

        $this->add_control(
            'post_link_color',
            [
                'label' => 'Couleur des liens',
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#4F46E5',
                'selectors' => [
                    '{{WRAPPER}} .vic-post-content a, {{WRAPPER}} .vic-post-excerpt a' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'post_link_color_hover',
            [
                'label' => 'Couleur des liens (survol)',
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#3730A3',
                'selectors' => [
                    '{{WRAPPER}} .vic-post-content a:hover, {{WRAPPER}} .vic-post-excerpt a:hover' => 'color: {{VALUE}};',
                ],
            ]
        );

        // === FIN NOUVEAUX CONTRÔLES PARTIE 5 ===

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

        // === NOUVEAUX CONTRÔLES PARTIE 6 - DATE ===

        $this->add_control(
            'date_heading',
            [
                'label' => 'Date de publication',
                'type' => \Elementor\Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'post_date_typography',
                'label' => 'Typographie de la date',
                'selector' => '{{WRAPPER}} .vic-post-date, {{WRAPPER}} .vic-post-time',
            ]
        );

        $this->add_control(
            'post_date_color',
            [
                'label' => 'Couleur de la date',
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#9CA3AF',
                'selectors' => [
                    '{{WRAPPER}} .vic-post-date, {{WRAPPER}} .vic-post-time' => 'color: {{VALUE}};',
                ],
            ]
        );

        // === FIN NOUVEAUX CONTRÔLES PARTIE 6 ===

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

        // === NOUVEAUX CONTRÔLES PARTIE 7 ===

        $this->add_control(
            'avatar_shape',
            [
                'label' => 'Forme avatar',
                'type' => \Elementor\Controls_Manager::SELECT,
                'default' => 'round',
                'options' => [
                    'round' => 'Rond',
                    'square' => 'Carré',
                    'custom' => 'Personnalisé',
                ],
                'separator' => 'before',
            ]
        );

        $this->add_control(
            'avatar_border_radius',
            [
                'label' => 'Rayon bordure avatar',
                'type' => \Elementor\Controls_Manager::SLIDER,
                'size_units' => ['px', '%'],
                'range' => [
                    'px' => ['min' => 0, 'max' => 50],
                    '%' => ['min' => 0, 'max' => 50],
                ],
                'default' => ['unit' => 'px', 'size' => 8],
                'selectors' => [
                    '{{WRAPPER}} .vic-author-info img, {{WRAPPER}} .vic-commenters-avatars img' => 'border-radius: {{SIZE}}{{UNIT}} !important;',
                ],
                'condition' => [
                    'avatar_shape' => 'custom',
                ],
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Border::get_type(),
            [
                'name' => 'avatar_border',
                'label' => 'Bordure avatar',
                'selector' => '{{WRAPPER}} .vic-author-info img, {{WRAPPER}} .vic-commenters-avatars img',
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'avatar_shadow',
                'label' => 'Ombre avatar',
                'selector' => '{{WRAPPER}} .vic-author-info img, {{WRAPPER}} .vic-commenters-avatars img',
            ]
        );

        // === FIN NOUVEAUX CONTRÔLES PARTIE 7 ===

        $this->end_controls_section();

        // ========================================
        // STYLE TAB - BADGE DE NIVEAU (Partie 8)
        // ========================================

        $this->start_controls_section(
            'style_level_badge',
            [
                'label' => 'Badge de Niveau',
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'level_badge_size',
            [
                'label' => 'Taille badge',
                'type' => \Elementor\Controls_Manager::SLIDER,
                'size_units' => ['px'],
                'range' => ['px' => ['min' => 14, 'max' => 32]],
                'default' => ['unit' => 'px', 'size' => 18],
                'selectors' => [
                    '{{WRAPPER}} .vic-level-badge' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}}; font-size: calc({{SIZE}}{{UNIT}} * 0.55);',
                ],
            ]
        );

        $this->add_control(
            'level_badge_position',
            [
                'label' => 'Position badge',
                'type' => \Elementor\Controls_Manager::SELECT,
                'default' => 'bottom-right',
                'options' => [
                    'bottom-right' => 'Bas droite',
                    'bottom-left' => 'Bas gauche',
                    'top-right' => 'Haut droite',
                    'top-left' => 'Haut gauche',
                    'bottom-center' => 'Bas centre',
                ],
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Border::get_type(),
            [
                'name' => 'level_badge_border',
                'label' => 'Bordure badge',
                'selector' => '{{WRAPPER}} .vic-level-badge',
                'fields_options' => [
                    'border' => ['default' => 'solid'],
                    'width' => ['default' => ['top' => '2', 'right' => '2', 'bottom' => '2', 'left' => '2', 'isLinked' => true]],
                    'color' => ['default' => '#ffffff'],
                ],
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'level_badge_shadow',
                'label' => 'Ombre badge',
                'selector' => '{{WRAPPER}} .vic-level-badge',
            ]
        );

        $this->add_control(
            'progress_ring_thickness',
            [
                'label' => 'Épaisseur anneau progression',
                'type' => \Elementor\Controls_Manager::SLIDER,
                'size_units' => ['px'],
                'range' => ['px' => ['min' => 2, 'max' => 8]],
                'default' => ['unit' => 'px', 'size' => 4],
                'separator' => 'before',
                'description' => 'Anneau de progression dans le popup profil',
            ]
        );

        // === FIN CONTRÔLES PARTIE 8 ===

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

        // === NOUVEAUX CONTRÔLES PARTIE 9 ===

        $this->add_control(
            'buttons_icon_size',
            [
                'label' => 'Taille icônes',
                'type' => \Elementor\Controls_Manager::SLIDER,
                'size_units' => ['px'],
                'range' => ['px' => ['min' => 12, 'max' => 28]],
                'default' => ['unit' => 'px', 'size' => 18],
                'selectors' => [
                    '{{WRAPPER}} .vic-like-btn svg, {{WRAPPER}} .vic-comment-btn svg' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
                ],
                'separator' => 'before',
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'buttons_counter_typography',
                'label' => 'Police compteurs',
                'selector' => '{{WRAPPER}} .vic-like-count, {{WRAPPER}} .vic-comment-count',
            ]
        );

        $this->add_control(
            'buttons_spacing',
            [
                'label' => 'Espacement boutons',
                'type' => \Elementor\Controls_Manager::SLIDER,
                'size_units' => ['px'],
                'range' => ['px' => ['min' => 0, 'max' => 40]],
                'default' => ['unit' => 'px', 'size' => 16],
                'selectors' => [
                    '{{WRAPPER}} .vic-post-actions' => 'gap: {{SIZE}}{{UNIT}};',
                    '{{WRAPPER}} .vic-post-stats' => 'gap: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
            'buttons_click_animation',
            [
                'label' => 'Animation au clic',
                'type' => \Elementor\Controls_Manager::SELECT,
                'default' => 'scale',
                'options' => [
                    'none' => 'Aucune',
                    'scale' => 'Pulse (zoom)',
                    'bounce' => 'Rebond',
                    'shake' => 'Secousse',
                ],
            ]
        );

        // === FIN CONTRÔLES PARTIE 9 ===

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

        // === NOUVEAUX CONTRÔLES PARTIE 10 ===

        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'filter_typography',
                'label' => 'Police filtres',
                'selector' => '{{WRAPPER}} .vic-filter-btn',
                'separator' => 'before',
            ]
        );

        $this->add_responsive_control(
            'filter_padding',
            [
                'label' => 'Padding filtres',
                'type' => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em'],
                'selectors' => [
                    '{{WRAPPER}} .vic-filter-btn' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
            'filter_spacing',
            [
                'label' => 'Espacement entre filtres',
                'type' => \Elementor\Controls_Manager::SLIDER,
                'size_units' => ['px'],
                'range' => ['px' => ['min' => 0, 'max' => 20]],
                'default' => ['unit' => 'px', 'size' => 8],
                'selectors' => [
                    '{{WRAPPER}} .vic-filters' => 'gap: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Border::get_type(),
            [
                'name' => 'filter_border',
                'label' => 'Bordure filtres',
                'selector' => '{{WRAPPER}} .vic-filter-btn',
            ]
        );

        $this->add_control(
            'filter_hover_heading',
            [
                'label' => 'Effet Survol',
                'type' => \Elementor\Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );

        $this->add_control(
            'filter_hover_bg_color',
            [
                'label' => 'Couleur fond (survol)',
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .vic-filter-btn:not(.active):hover' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'filter_hover_text_color',
            [
                'label' => 'Couleur texte (survol)',
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .vic-filter-btn:not(.active):hover' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'filter_hover_border_color',
            [
                'label' => 'Couleur bordure (survol)',
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .vic-filter-btn:not(.active):hover' => 'border-color: {{VALUE}};',
                ],
            ]
        );

        // === FIN CONTRÔLES PARTIE 10 ===

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

        // === NOUVEAUX CONTRÔLES PARTIE 11 ===

        $this->add_group_control(
            \Elementor\Group_Control_Border::get_type(),
            [
                'name' => 'thumbnail_border',
                'label' => 'Bordure miniature',
                'selector' => '{{WRAPPER}} .vic-post-thumbnail',
                'separator' => 'before',
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'thumbnail_shadow',
                'label' => 'Ombre miniature',
                'selector' => '{{WRAPPER}} .vic-post-thumbnail',
            ]
        );

        $this->add_control(
            'thumbnail_hover_zoom',
            [
                'label' => 'Effet zoom au survol',
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'label_on' => 'Oui',
                'label_off' => 'Non',
                'return_value' => 'yes',
                'default' => 'yes',
            ]
        );

        // === FIN CONTRÔLES PARTIE 11 ===

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

        // === NOUVEAUX CONTRÔLES PARTIE 12 ===

        $this->add_control(
            'pinned_bg_color',
            [
                'label' => 'Couleur de fond',
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#FEF3C7',
                'selectors' => [
                    '{{WRAPPER}} .vic-pinned-badge' => 'background-color: {{VALUE}};',
                ],
                'separator' => 'before',
            ]
        );

        $this->add_responsive_control(
            'pinned_padding',
            [
                'label' => 'Padding',
                'type' => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em'],
                'default' => [
                    'top' => '4',
                    'right' => '8',
                    'bottom' => '4',
                    'left' => '8',
                    'unit' => 'px',
                    'isLinked' => false,
                ],
                'selectors' => [
                    '{{WRAPPER}} .vic-pinned-badge' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
            'pinned_icon',
            [
                'label' => 'Icône',
                'type' => \Elementor\Controls_Manager::SELECT,
                'default' => 'pin',
                'options' => [
                    'pin' => '📌 Épingle',
                    'star' => '⭐ Étoile',
                    'fire' => '🔥 Feu',
                    'megaphone' => '📢 Mégaphone',
                    'none' => 'Aucune',
                ],
            ]
        );

        $this->add_control(
            'pinned_border_radius',
            [
                'label' => 'Rayon de bordure',
                'type' => \Elementor\Controls_Manager::SLIDER,
                'size_units' => ['px'],
                'range' => ['px' => ['min' => 0, 'max' => 20]],
                'default' => ['unit' => 'px', 'size' => 4],
                'selectors' => [
                    '{{WRAPPER}} .vic-pinned-badge' => 'border-radius: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        // === FIN CONTRÔLES PARTIE 12 ===

        $this->end_controls_section();

        // ========================================
        // STYLE TAB - SEARCH BAR (Partie 14)
        // ========================================

        $this->start_controls_section(
            'style_search',
            [
                'label' => 'Barre de Recherche',
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'search_bg_color',
            [
                'label' => 'Couleur de fond',
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#F3F4F6',
                'selectors' => [
                    '{{WRAPPER}} .vic-search-input' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'search_text_color',
            [
                'label' => 'Couleur du texte',
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#111827',
                'selectors' => [
                    '{{WRAPPER}} .vic-search-input' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'search_placeholder_color',
            [
                'label' => 'Couleur placeholder',
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#9CA3AF',
                'selectors' => [
                    '{{WRAPPER}} .vic-search-input::placeholder' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Border::get_type(),
            [
                'name' => 'search_border',
                'label' => 'Bordure',
                'selector' => '{{WRAPPER}} .vic-search-input',
            ]
        );

        $this->add_control(
            'search_border_radius',
            [
                'label' => 'Rayon de bordure',
                'type' => \Elementor\Controls_Manager::SLIDER,
                'size_units' => ['px'],
                'range' => ['px' => ['min' => 0, 'max' => 30]],
                'default' => ['unit' => 'px', 'size' => 8],
                'selectors' => [
                    '{{WRAPPER}} .vic-search-input' => 'border-radius: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
            'search_icon_color',
            [
                'label' => 'Couleur icône',
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#6B7280',
                'selectors' => [
                    '{{WRAPPER}} .vic-search-icon' => 'color: {{VALUE}};',
                    '{{WRAPPER}} .vic-search-toggle-btn svg' => 'color: {{VALUE}};',
                ],
                'separator' => 'before',
            ]
        );

        $this->add_control(
            'search_focus_border_color',
            [
                'label' => 'Couleur bordure (focus)',
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#3B82F6',
                'selectors' => [
                    '{{WRAPPER}} .vic-search-input:focus' => 'border-color: {{VALUE}}; box-shadow: 0 0 0 2px {{VALUE}}33;',
                ],
            ]
        );

        // === FIN CONTRÔLES PARTIE 14 ===

        $this->end_controls_section();

        // ========================================
        // STYLE TAB - POST FORM (Partie 15)
        // ========================================

        $this->start_controls_section(
            'style_post_form',
            [
                'label' => 'Formulaire de Post',
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'form_bg_color',
            [
                'label' => 'Couleur fond formulaire',
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#FFFFFF',
                'selectors' => [
                    '{{WRAPPER}} .vic-create-post-box' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Border::get_type(),
            [
                'name' => 'form_border',
                'label' => 'Bordure formulaire',
                'selector' => '{{WRAPPER}} .vic-create-post-box',
            ]
        );

        $this->add_control(
            'form_border_radius',
            [
                'label' => 'Rayon bordure formulaire',
                'type' => \Elementor\Controls_Manager::SLIDER,
                'size_units' => ['px'],
                'range' => ['px' => ['min' => 0, 'max' => 24]],
                'default' => ['unit' => 'px', 'size' => 12],
                'selectors' => [
                    '{{WRAPPER}} .vic-create-post-box' => 'border-radius: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
            'form_fields_heading',
            [
                'label' => 'Champs de saisie',
                'type' => \Elementor\Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );

        $this->add_control(
            'field_bg_color',
            [
                'label' => 'Couleur fond champs',
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#F9FAFB',
                'selectors' => [
                    '{{WRAPPER}} .vic-create-post-trigger' => 'background-color: {{VALUE}};',
                    '{{WRAPPER}} .vic-post-title-input, {{WRAPPER}} .vic-post-content-input' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'field_text_color',
            [
                'label' => 'Couleur texte champs',
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#111827',
                'selectors' => [
                    '{{WRAPPER}} .vic-create-post-trigger' => 'color: {{VALUE}};',
                    '{{WRAPPER}} .vic-post-title-input, {{WRAPPER}} .vic-post-content-input' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'field_placeholder_color',
            [
                'label' => 'Couleur placeholder',
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#9CA3AF',
                'selectors' => [
                    '{{WRAPPER}} .vic-post-title-input::placeholder, {{WRAPPER}} .vic-post-content-input::placeholder' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Border::get_type(),
            [
                'name' => 'field_border',
                'label' => 'Bordure champs',
                'selector' => '{{WRAPPER}} .vic-create-post-trigger, {{WRAPPER}} .vic-post-title-input, {{WRAPPER}} .vic-post-content-input',
            ]
        );

        $this->add_control(
            'field_focus_border_color',
            [
                'label' => 'Couleur bordure (focus)',
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#3B82F6',
                'selectors' => [
                    '{{WRAPPER}} .vic-post-title-input:focus, {{WRAPPER}} .vic-post-content-input:focus' => 'border-color: {{VALUE}}; box-shadow: 0 0 0 2px {{VALUE}}33;',
                ],
            ]
        );

        $this->add_control(
            'form_button_heading',
            [
                'label' => 'Bouton Publier',
                'type' => \Elementor\Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );

        $this->add_control(
            'button_bg_color',
            [
                'label' => 'Couleur fond bouton',
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#F59E0B',
                'selectors' => [
                    '{{WRAPPER}} .vic-create-post-form .vic-btn-primary, {{WRAPPER}} .vic-btn.vic-btn-primary' => 'background: {{VALUE}} !important;',
                ],
            ]
        );

        $this->add_control(
            'button_text_color',
            [
                'label' => 'Couleur texte bouton',
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#FFFFFF',
                'selectors' => [
                    '{{WRAPPER}} .vic-create-post-form .vic-btn-primary, {{WRAPPER}} .vic-btn.vic-btn-primary' => 'color: {{VALUE}} !important;',
                ],
            ]
        );

        $this->add_control(
            'button_hover_bg_color',
            [
                'label' => 'Couleur fond (survol)',
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#D97706',
                'selectors' => [
                    '{{WRAPPER}} .vic-create-post-form .vic-btn-primary:hover, {{WRAPPER}} .vic-btn.vic-btn-primary:hover' => 'background: {{VALUE}} !important;',
                ],
            ]
        );

        // === FIN CONTRÔLES PARTIE 15 ===

        $this->end_controls_section();

        // ========================================
        // STYLE TAB - MODAL POST (Partie 16)
        // ========================================

        $this->start_controls_section(
            'style_modal',
            [
                'label' => 'Modale Post',
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'modal_overlay_color',
            [
                'label' => 'Couleur overlay',
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => 'rgba(0, 0, 0, 0.5)',
                'selectors' => [
                    '{{WRAPPER}} .vic-modal-overlay, .vic-modal-overlay' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'modal_bg_color',
            [
                'label' => 'Couleur fond modale',
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#FFFFFF',
                'selectors' => [
                    '{{WRAPPER}} .vic-modal-content, .vic-modal-content' => 'background-color: {{VALUE}};',
                    '{{WRAPPER}} .vic-post-modal, .vic-post-modal' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Border::get_type(),
            [
                'name' => 'modal_border',
                'label' => 'Bordure modale',
                'selector' => '{{WRAPPER}} .vic-modal-content, .vic-modal-content, {{WRAPPER}} .vic-post-modal, .vic-post-modal',
            ]
        );

        $this->add_control(
            'modal_border_radius',
            [
                'label' => 'Rayon bordure modale',
                'type' => \Elementor\Controls_Manager::SLIDER,
                'size_units' => ['px'],
                'range' => ['px' => ['min' => 0, 'max' => 40]],
                'default' => ['unit' => 'px', 'size' => 16],
                'selectors' => [
                    '{{WRAPPER}} .vic-modal-content, .vic-modal-content' => 'border-radius: {{SIZE}}{{UNIT}};',
                    '{{WRAPPER}} .vic-post-modal, .vic-post-modal' => 'border-radius: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'modal_shadow',
                'label' => 'Ombre modale',
                'selector' => '{{WRAPPER}} .vic-modal-content, .vic-modal-content, {{WRAPPER}} .vic-post-modal, .vic-post-modal',
            ]
        );

        $this->add_control(
            'modal_max_width',
            [
                'label' => 'Largeur max modale',
                'type' => \Elementor\Controls_Manager::SLIDER,
                'size_units' => ['px', '%', 'vw'],
                'range' => [
                    'px' => ['min' => 400, 'max' => 1200],
                    '%' => ['min' => 50, 'max' => 100],
                    'vw' => ['min' => 50, 'max' => 100],
                ],
                'default' => ['unit' => 'px', 'size' => 800],
                'selectors' => [
                    '{{WRAPPER}} .vic-modal-content, .vic-modal-content' => 'max-width: {{SIZE}}{{UNIT}};',
                    '{{WRAPPER}} .vic-post-modal, .vic-post-modal' => 'max-width: {{SIZE}}{{UNIT}};',
                ],
                'separator' => 'before',
            ]
        );

        $this->add_control(
            'modal_max_height',
            [
                'label' => 'Hauteur max modale',
                'type' => \Elementor\Controls_Manager::SLIDER,
                'size_units' => ['px', '%', 'vh'],
                'range' => [
                    'px' => ['min' => 300, 'max' => 1000],
                    '%' => ['min' => 50, 'max' => 100],
                    'vh' => ['min' => 50, 'max' => 100],
                ],
                'default' => ['unit' => 'vh', 'size' => 90],
                'selectors' => [
                    '{{WRAPPER}} .vic-modal-content, .vic-modal-content' => 'max-height: {{SIZE}}{{UNIT}};',
                    '{{WRAPPER}} .vic-post-modal, .vic-post-modal' => 'max-height: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
            'modal_animation',
            [
                'label' => 'Animation ouverture',
                'type' => \Elementor\Controls_Manager::SELECT,
                'default' => 'fade-scale',
                'options' => [
                    'none' => 'Aucune',
                    'fade' => 'Fondu',
                    'fade-scale' => 'Fondu + Zoom',
                    'slide-up' => 'Glissement haut',
                    'slide-down' => 'Glissement bas',
                ],
                'separator' => 'before',
            ]
        );

        // === FIN CONTRÔLES PARTIE 16 ===

        $this->end_controls_section();

        // ========================================
        // STYLE TAB - COMMENTS SECTION (Partie 17)
        // ========================================

        $this->start_controls_section(
            'style_comments',
            [
                'label' => 'Section Commentaires',
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'comment_bg_color',
            [
                'label' => 'Couleur fond commentaire',
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#F9FAFB',
                'selectors' => [
                    '{{WRAPPER}} .vic-comment-item, .vic-comment-item' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Border::get_type(),
            [
                'name' => 'comment_border',
                'label' => 'Bordure commentaire',
                'selector' => '{{WRAPPER}} .vic-comment-item, .vic-comment-item',
            ]
        );

        $this->add_control(
            'comment_border_radius',
            [
                'label' => 'Rayon bordure commentaire',
                'type' => \Elementor\Controls_Manager::SLIDER,
                'size_units' => ['px'],
                'range' => ['px' => ['min' => 0, 'max' => 20]],
                'default' => ['unit' => 'px', 'size' => 8],
                'selectors' => [
                    '{{WRAPPER}} .vic-comment-item, .vic-comment-item' => 'border-radius: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'comment_padding',
            [
                'label' => 'Padding commentaire',
                'type' => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em'],
                'default' => [
                    'top' => '12',
                    'right' => '16',
                    'bottom' => '12',
                    'left' => '16',
                    'unit' => 'px',
                    'isLinked' => false,
                ],
                'selectors' => [
                    '{{WRAPPER}} .vic-comment-item, .vic-comment-item' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
            'comment_spacing',
            [
                'label' => 'Espacement commentaires',
                'type' => \Elementor\Controls_Manager::SLIDER,
                'size_units' => ['px'],
                'range' => ['px' => ['min' => 0, 'max' => 30]],
                'default' => ['unit' => 'px', 'size' => 12],
                'selectors' => [
                    '{{WRAPPER}} .vic-comment-item, .vic-comment-item' => 'margin-bottom: {{SIZE}}{{UNIT}};',
                    '{{WRAPPER}} .vic-comments-list, .vic-comments-list' => 'gap: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
            'comment_typography_heading',
            [
                'label' => 'Typographie',
                'type' => \Elementor\Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'commenter_name_typography',
                'label' => 'Police nom commentateur',
                'selector' => '{{WRAPPER}} .vic-comment-author, .vic-comment-author',
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'comment_content_typography',
                'label' => 'Police contenu commentaire',
                'selector' => '{{WRAPPER}} .vic-comment-text, .vic-comment-text, {{WRAPPER}} .vic-comment-content, .vic-comment-content',
            ]
        );

        $this->add_control(
            'comment_replies_heading',
            [
                'label' => 'Réponses',
                'type' => \Elementor\Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );

        $this->add_control(
            'reply_bg_color',
            [
                'label' => 'Couleur fond réponses',
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#F3F4F6',
                'selectors' => [
                    '{{WRAPPER}} .vic-comment-reply, .vic-comment-reply' => 'background-color: {{VALUE}};',
                    '{{WRAPPER}} .vic-reply-item, .vic-reply-item' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'reply_indent',
            [
                'label' => 'Indentation réponses',
                'type' => \Elementor\Controls_Manager::SLIDER,
                'size_units' => ['px'],
                'range' => ['px' => ['min' => 0, 'max' => 60]],
                'default' => ['unit' => 'px', 'size' => 24],
                'selectors' => [
                    '{{WRAPPER}} .vic-comment-reply, .vic-comment-reply' => 'margin-left: {{SIZE}}{{UNIT}};',
                    '{{WRAPPER}} .vic-reply-item, .vic-reply-item' => 'margin-left: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        // === FIN CONTRÔLES PARTIE 17 ===

        $this->end_controls_section();

        // ========================================
        // STYLE TAB - USER PROFILE POPUP (Partie 18)
        // ========================================

        $this->start_controls_section(
            'style_profile_popup',
            [
                'label' => 'Popup Profil Utilisateur',
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'popup_bg_color',
            [
                'label' => 'Couleur fond popup',
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#FFFFFF',
                'selectors' => [
                    '#vic-profile-popup, .vic-profile-popup' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Border::get_type(),
            [
                'name' => 'popup_border',
                'label' => 'Bordure popup',
                'selector' => '#vic-profile-popup, .vic-profile-popup',
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'popup_shadow',
                'label' => 'Ombre popup',
                'selector' => '#vic-profile-popup, .vic-profile-popup',
            ]
        );

        $this->add_control(
            'popup_border_radius',
            [
                'label' => 'Rayon bordure popup',
                'type' => \Elementor\Controls_Manager::SLIDER,
                'size_units' => ['px'],
                'range' => ['px' => ['min' => 0, 'max' => 24]],
                'default' => ['unit' => 'px', 'size' => 12],
                'selectors' => [
                    '#vic-profile-popup, .vic-profile-popup' => 'border-radius: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
            'popup_width',
            [
                'label' => 'Largeur popup',
                'type' => \Elementor\Controls_Manager::SLIDER,
                'size_units' => ['px'],
                'range' => ['px' => ['min' => 200, 'max' => 400]],
                'default' => ['unit' => 'px', 'size' => 280],
                'selectors' => [
                    '#vic-profile-popup, .vic-profile-popup' => 'width: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'popup_username_typography',
                'label' => 'Police nom utilisateur',
                'selector' => '#vic-profile-popup .vic-profile-popup-name, .vic-profile-popup .vic-profile-popup-name',
                'separator' => 'before',
            ]
        );

        $this->add_control(
            'popup_stats_color',
            [
                'label' => 'Couleur statistiques',
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#6B7280',
                'selectors' => [
                    '#vic-profile-popup .vic-profile-popup-stats, .vic-profile-popup .vic-profile-popup-stats' => 'color: {{VALUE}};',
                    '#vic-profile-popup .vic-profile-popup-stat, .vic-profile-popup .vic-profile-popup-stat' => 'color: {{VALUE}};',
                ],
            ]
        );

        // === FIN CONTRÔLES PARTIE 18 ===

        $this->end_controls_section();

        // ========================================
        // STYLE TAB - YOUTUBE EMBED (Partie 19)
        // ========================================

        $this->start_controls_section(
            'style_youtube',
            [
                'label' => 'Embed YouTube',
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'youtube_border_radius',
            [
                'label' => 'Rayon bordure vidéo',
                'type' => \Elementor\Controls_Manager::SLIDER,
                'size_units' => ['px'],
                'range' => ['px' => ['min' => 0, 'max' => 24]],
                'default' => ['unit' => 'px', 'size' => 12],
                'selectors' => [
                    '{{WRAPPER}} .vic-youtube-embed, .vic-youtube-embed' => 'border-radius: {{SIZE}}{{UNIT}}; overflow: hidden;',
                    '{{WRAPPER}} .vic-youtube-embed iframe, .vic-youtube-embed iframe' => 'border-radius: {{SIZE}}{{UNIT}};',
                    '{{WRAPPER}} .vic-video-wrapper, .vic-video-wrapper' => 'border-radius: {{SIZE}}{{UNIT}}; overflow: hidden;',
                ],
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'youtube_shadow',
                'label' => 'Ombre vidéo',
                'selector' => '{{WRAPPER}} .vic-youtube-embed, .vic-youtube-embed, {{WRAPPER}} .vic-video-wrapper, .vic-video-wrapper',
            ]
        );

        $this->add_control(
            'youtube_aspect_ratio',
            [
                'label' => 'Ratio d\'aspect',
                'type' => \Elementor\Controls_Manager::SELECT,
                'default' => '16-9',
                'options' => [
                    '16-9' => '16:9 (Standard)',
                    '4-3' => '4:3 (Classique)',
                    '21-9' => '21:9 (Cinéma)',
                    '1-1' => '1:1 (Carré)',
                ],
            ]
        );

        $this->add_control(
            'youtube_play_icon',
            [
                'label' => 'Icône play overlay',
                'type' => \Elementor\Controls_Manager::SELECT,
                'default' => 'youtube',
                'options' => [
                    'youtube' => 'YouTube (rouge)',
                    'circle' => 'Cercle (blanc)',
                    'minimal' => 'Triangle minimaliste',
                    'none' => 'Aucune',
                ],
                'separator' => 'before',
            ]
        );

        // === FIN CONTRÔLES PARTIE 19 ===

        $this->end_controls_section();

        // ========================================
        // STYLE TAB - ATTACHMENTS (Partie 20)
        // ========================================

        $this->start_controls_section(
            'style_attachments',
            [
                'label' => 'Pièces Jointes',
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'attachments_grid_columns',
            [
                'label' => 'Colonnes grille',
                'type' => \Elementor\Controls_Manager::NUMBER,
                'default' => 3,
                'min' => 1,
                'max' => 6,
                'selectors' => [
                    '{{WRAPPER}} .vic-attachments-grid, .vic-attachments-grid' => 'grid-template-columns: repeat({{VALUE}}, 1fr);',
                    '{{WRAPPER}} .vic-post-images, .vic-post-images' => 'grid-template-columns: repeat({{VALUE}}, 1fr);',
                ],
            ]
        );

        $this->add_control(
            'attachments_grid_gap',
            [
                'label' => 'Espacement grille',
                'type' => \Elementor\Controls_Manager::SLIDER,
                'size_units' => ['px'],
                'range' => ['px' => ['min' => 0, 'max' => 24]],
                'default' => ['unit' => 'px', 'size' => 8],
                'selectors' => [
                    '{{WRAPPER}} .vic-attachments-grid, .vic-attachments-grid' => 'gap: {{SIZE}}{{UNIT}};',
                    '{{WRAPPER}} .vic-post-images, .vic-post-images' => 'gap: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
            'attachments_border_radius',
            [
                'label' => 'Rayon bordure images',
                'type' => \Elementor\Controls_Manager::SLIDER,
                'size_units' => ['px'],
                'range' => ['px' => ['min' => 0, 'max' => 20]],
                'default' => ['unit' => 'px', 'size' => 8],
                'selectors' => [
                    '{{WRAPPER}} .vic-attachments-grid img, .vic-attachments-grid img' => 'border-radius: {{SIZE}}{{UNIT}};',
                    '{{WRAPPER}} .vic-post-images img, .vic-post-images img' => 'border-radius: {{SIZE}}{{UNIT}};',
                    '{{WRAPPER}} .vic-attachment-item, .vic-attachment-item' => 'border-radius: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
            'attachments_overlay_color',
            [
                'label' => 'Overlay images (survol)',
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => 'rgba(0, 0, 0, 0.3)',
                'selectors' => [
                    '{{WRAPPER}} .vic-attachments-grid img:hover, .vic-attachments-grid img:hover' => 'filter: brightness(0.7);',
                    '{{WRAPPER}} .vic-attachment-item:hover::after, .vic-attachment-item:hover::after' => 'content: ""; position: absolute; inset: 0; background-color: {{VALUE}};',
                ],
                'separator' => 'before',
            ]
        );

        $this->add_control(
            'attachments_zoom_icon',
            [
                'label' => 'Icône zoom',
                'type' => \Elementor\Controls_Manager::SELECT,
                'default' => 'magnifier',
                'options' => [
                    'magnifier' => '🔍 Loupe',
                    'expand' => '⤢ Expansion',
                    'eye' => '👁 Œil',
                    'none' => 'Aucune',
                ],
            ]
        );

        // === FIN CONTRÔLES PARTIE 20 ===

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
        $wrapper_selector = '.elementor-element-' . $this->get_id();

        // Output custom CSS if provided
        if (!empty($settings['custom_css'])) {
            $custom_css = str_replace('{{WRAPPER}}', $wrapper_selector, $settings['custom_css']);
            echo '<style>' . $custom_css . '</style>';
        }

        // Output avatar shape styles (for round/square presets)
        $avatar_shape = isset($settings['avatar_shape']) ? $settings['avatar_shape'] : 'round';
        if ($avatar_shape !== 'custom') {
            $border_radius = ($avatar_shape === 'round') ? '50%' : '0';
            echo '<style>';
            echo $wrapper_selector . ' .vic-author-info img, ';
            echo $wrapper_selector . ' .vic-commenters-avatars img { border-radius: ' . $border_radius . ' !important; }';
            echo '</style>';
        }

        // Output level badge position styles
        $badge_position = isset($settings['level_badge_position']) ? $settings['level_badge_position'] : 'bottom-right';
        $position_styles = [
            'bottom-right' => 'bottom: -2px; right: -2px; left: auto; top: auto; transform: none;',
            'bottom-left' => 'bottom: -2px; left: -2px; right: auto; top: auto; transform: none;',
            'top-right' => 'top: -2px; right: -2px; bottom: auto; left: auto; transform: none;',
            'top-left' => 'top: -2px; left: -2px; bottom: auto; right: auto; transform: none;',
            'bottom-center' => 'bottom: -2px; left: 50%; right: auto; top: auto; transform: translateX(-50%);',
        ];
        if (isset($position_styles[$badge_position])) {
            echo '<style>';
            echo $wrapper_selector . ' .vic-level-badge { ' . $position_styles[$badge_position] . ' }';
            echo '</style>';
        }

        // Output progress ring thickness (global style - popup is appended to body)
        $ring_thickness = isset($settings['progress_ring_thickness']['size']) ? $settings['progress_ring_thickness']['size'] : 4;
        $ring_unit = isset($settings['progress_ring_thickness']['unit']) ? $settings['progress_ring_thickness']['unit'] : 'px';
        echo '<style>';
        echo '#vic-profile-popup .vic-profile-popup-progress-ring circle { stroke-width: ' . $ring_thickness . $ring_unit . ' !important; }';
        echo '</style>';

        // Output button click animation styles
        $click_animation = isset($settings['buttons_click_animation']) ? $settings['buttons_click_animation'] : 'scale';
        if ($click_animation !== 'none') {
            echo '<style>';
            $animation_css = '';
            switch ($click_animation) {
                case 'scale':
                    $animation_css = $wrapper_selector . ' .vic-like-btn:active, ' . $wrapper_selector . ' .vic-comment-btn:active { transform: scale(1.2); }';
                    $animation_css .= $wrapper_selector . ' .vic-like-btn, ' . $wrapper_selector . ' .vic-comment-btn { transition: transform 0.15s ease; }';
                    break;
                case 'bounce':
                    $animation_css = '@keyframes vic-bounce { 0%, 100% { transform: scale(1); } 50% { transform: scale(1.3); } }';
                    $animation_css .= $wrapper_selector . ' .vic-like-btn:active, ' . $wrapper_selector . ' .vic-comment-btn:active { animation: vic-bounce 0.3s ease; }';
                    break;
                case 'shake':
                    $animation_css = '@keyframes vic-shake { 0%, 100% { transform: translateX(0); } 25% { transform: translateX(-3px); } 75% { transform: translateX(3px); } }';
                    $animation_css .= $wrapper_selector . ' .vic-like-btn:active, ' . $wrapper_selector . ' .vic-comment-btn:active { animation: vic-shake 0.3s ease; }';
                    break;
            }
            echo $animation_css;
            echo '</style>';
        }

        // Output thumbnail hover zoom effect
        $hover_zoom = isset($settings['thumbnail_hover_zoom']) ? $settings['thumbnail_hover_zoom'] : 'yes';
        if ($hover_zoom === 'yes') {
            echo '<style>';
            echo $wrapper_selector . ' .vic-post-thumbnail { overflow: hidden; }';
            echo $wrapper_selector . ' .vic-post-thumbnail img { transition: transform 0.3s ease; }';
            echo $wrapper_selector . ' .vic-post-thumbnail:hover img { transform: scale(1.05); }';
            echo '</style>';
        }

        // Output pinned badge icon
        $pinned_icon = isset($settings['pinned_icon']) ? $settings['pinned_icon'] : 'pin';
        $icons = [
            'pin' => '📌',
            'star' => '⭐',
            'fire' => '🔥',
            'megaphone' => '📢',
            'none' => '',
        ];
        if (isset($icons[$pinned_icon]) && $pinned_icon !== 'none') {
            echo '<style>';
            echo $wrapper_selector . ' .vic-pinned-badge::before { content: "' . $icons[$pinned_icon] . ' "; }';
            echo '</style>';
        } elseif ($pinned_icon === 'none') {
            echo '<style>';
            echo $wrapper_selector . ' .vic-pinned-badge::before { content: none; }';
            echo '</style>';
        }

        // Output publish button styles (need high specificity to override CSS variables)
        $btn_bg = isset($settings['button_bg_color']) ? $settings['button_bg_color'] : '';
        $btn_text = isset($settings['button_text_color']) ? $settings['button_text_color'] : '';
        $btn_hover = isset($settings['button_hover_bg_color']) ? $settings['button_hover_bg_color'] : '';
        if ($btn_bg || $btn_text || $btn_hover) {
            echo '<style>';
            if ($btn_bg) {
                echo $wrapper_selector . ' .vic-form-submit .vic-btn-primary { background: ' . $btn_bg . ' !important; }';
            }
            if ($btn_text) {
                echo $wrapper_selector . ' .vic-form-submit .vic-btn-primary { color: ' . $btn_text . ' !important; }';
            }
            if ($btn_hover) {
                echo $wrapper_selector . ' .vic-form-submit .vic-btn-primary:hover { background: ' . $btn_hover . ' !important; }';
            }
            echo '</style>';
        }

        // === PARTIE 16: Modal animation styles ===
        $modal_animation = isset($settings['modal_animation']) ? $settings['modal_animation'] : 'fade-scale';
        if ($modal_animation !== 'none') {
            echo '<style>';
            $modal_css = '';
            switch ($modal_animation) {
                case 'fade':
                    $modal_css = '.vic-modal-overlay { opacity: 0; transition: opacity 0.3s ease; }';
                    $modal_css .= '.vic-modal-overlay.active { opacity: 1; }';
                    $modal_css .= '.vic-modal-content, .vic-post-modal { opacity: 0; transition: opacity 0.3s ease; }';
                    $modal_css .= '.vic-modal-overlay.active .vic-modal-content, .vic-modal-overlay.active .vic-post-modal { opacity: 1; }';
                    break;
                case 'fade-scale':
                    $modal_css = '.vic-modal-overlay { opacity: 0; transition: opacity 0.3s ease; }';
                    $modal_css .= '.vic-modal-overlay.active { opacity: 1; }';
                    $modal_css .= '.vic-modal-content, .vic-post-modal { opacity: 0; transform: scale(0.9); transition: opacity 0.3s ease, transform 0.3s ease; }';
                    $modal_css .= '.vic-modal-overlay.active .vic-modal-content, .vic-modal-overlay.active .vic-post-modal { opacity: 1; transform: scale(1); }';
                    break;
                case 'slide-up':
                    $modal_css = '.vic-modal-overlay { opacity: 0; transition: opacity 0.3s ease; }';
                    $modal_css .= '.vic-modal-overlay.active { opacity: 1; }';
                    $modal_css .= '.vic-modal-content, .vic-post-modal { opacity: 0; transform: translateY(30px); transition: opacity 0.3s ease, transform 0.3s ease; }';
                    $modal_css .= '.vic-modal-overlay.active .vic-modal-content, .vic-modal-overlay.active .vic-post-modal { opacity: 1; transform: translateY(0); }';
                    break;
                case 'slide-down':
                    $modal_css = '.vic-modal-overlay { opacity: 0; transition: opacity 0.3s ease; }';
                    $modal_css .= '.vic-modal-overlay.active { opacity: 1; }';
                    $modal_css .= '.vic-modal-content, .vic-post-modal { opacity: 0; transform: translateY(-30px); transition: opacity 0.3s ease, transform 0.3s ease; }';
                    $modal_css .= '.vic-modal-overlay.active .vic-modal-content, .vic-modal-overlay.active .vic-post-modal { opacity: 1; transform: translateY(0); }';
                    break;
            }
            echo $modal_css;
            echo '</style>';
        }

        // === PARTIE 19: YouTube aspect ratio styles ===
        $youtube_ratio = isset($settings['youtube_aspect_ratio']) ? $settings['youtube_aspect_ratio'] : '16-9';
        $aspect_ratios = [
            '16-9' => '56.25%',
            '4-3' => '75%',
            '21-9' => '42.85%',
            '1-1' => '100%',
        ];
        if (isset($aspect_ratios[$youtube_ratio])) {
            echo '<style>';
            echo '.vic-youtube-embed, .vic-video-wrapper { position: relative; padding-bottom: ' . $aspect_ratios[$youtube_ratio] . '; height: 0; overflow: hidden; }';
            echo '.vic-youtube-embed iframe, .vic-video-wrapper iframe { position: absolute; top: 0; left: 0; width: 100%; height: 100%; }';
            echo '</style>';
        }

        // === PARTIE 19: YouTube play icon overlay ===
        $youtube_icon = isset($settings['youtube_play_icon']) ? $settings['youtube_play_icon'] : 'youtube';
        if ($youtube_icon !== 'none') {
            echo '<style>';
            $icon_css = '.vic-youtube-thumbnail { position: relative; cursor: pointer; }';
            $icon_css .= '.vic-youtube-thumbnail::after { content: ""; position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); pointer-events: none; }';
            switch ($youtube_icon) {
                case 'youtube':
                    $icon_css .= '.vic-youtube-thumbnail::after { width: 68px; height: 48px; background: url("data:image/svg+xml,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' viewBox=\'0 0 68 48\'%3E%3Cpath fill=\'%23f00\' d=\'M66.52 7.74c-.78-2.93-2.49-5.41-5.42-6.19C55.79.13 34 0 34 0S12.21.13 6.9 1.55c-2.93.78-4.63 3.26-5.42 6.19C.06 13.05 0 24 0 24s.06 10.95 1.48 16.26c.78 2.93 2.49 5.41 5.42 6.19C12.21 47.87 34 48 34 48s21.79-.13 27.1-1.55c2.93-.78 4.64-3.26 5.42-6.19C67.94 34.95 68 24 68 24s-.06-10.95-1.48-16.26z\'/%3E%3Cpath fill=\'%23fff\' d=\'M45 24 27 14v20\'/%3E%3C/svg%3E") no-repeat center; background-size: contain; }';
                    break;
                case 'circle':
                    $icon_css .= '.vic-youtube-thumbnail::after { width: 60px; height: 60px; background: rgba(255,255,255,0.9); border-radius: 50%; }';
                    $icon_css .= '.vic-youtube-thumbnail::before { content: ""; position: absolute; top: 50%; left: 50%; transform: translate(-40%, -50%); border-style: solid; border-width: 12px 0 12px 20px; border-color: transparent transparent transparent #333; z-index: 1; }';
                    break;
                case 'minimal':
                    $icon_css .= '.vic-youtube-thumbnail::after { border-style: solid; border-width: 15px 0 15px 26px; border-color: transparent transparent transparent rgba(255,255,255,0.9); filter: drop-shadow(0 2px 4px rgba(0,0,0,0.3)); }';
                    break;
            }
            echo $icon_css;
            echo '</style>';
        }

        // === PARTIE 20: Attachments zoom icon ===
        $zoom_icon = isset($settings['attachments_zoom_icon']) ? $settings['attachments_zoom_icon'] : 'magnifier';
        if ($zoom_icon !== 'none') {
            echo '<style>';
            $zoom_icons = [
                'magnifier' => '🔍',
                'expand' => '⤢',
                'eye' => '👁',
            ];
            if (isset($zoom_icons[$zoom_icon])) {
                echo '.vic-attachment-item, .vic-attachments-grid img, .vic-post-images img { position: relative; cursor: pointer; }';
                echo '.vic-attachment-item:hover::before, .vic-attachments-grid img:hover + .vic-zoom-icon::before { content: "' . $zoom_icons[$zoom_icon] . '"; position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); font-size: 24px; z-index: 2; }';
            }
            echo '</style>';
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
