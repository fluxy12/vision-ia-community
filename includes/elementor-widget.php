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
