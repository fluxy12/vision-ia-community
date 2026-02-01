<?php
/**
 * Plugin Name: Vision IA Community
 * Description: Communauté style Skool pour Vision IA
 * Version: 1.0.0
 * Author: Vision IA
 * Text Domain: vision-ia-community
 */

if (!defined('ABSPATH')) {
    exit;
}

define('VIC_VERSION', '1.0.0');
define('VIC_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('VIC_PLUGIN_URL', plugin_dir_url(__FILE__));

class Vision_IA_Community {

    public function __construct() {
        add_action('init', [$this, 'register_post_type']);
        add_action('init', [$this, 'register_taxonomy']);
        add_action('wp_enqueue_scripts', [$this, 'enqueue_assets']);
        add_action('wp_ajax_vic_like_post', [$this, 'handle_like']);
        add_action('wp_ajax_vic_create_post', [$this, 'handle_create_post']);
        add_action('wp_ajax_vic_load_posts', [$this, 'handle_load_posts']);
        add_action('wp_ajax_nopriv_vic_load_posts', [$this, 'handle_load_posts']);
        add_action('wp_ajax_vic_pin_post', [$this, 'handle_pin_post']);
        add_shortcode('community_feed', [$this, 'render_feed']);
        
        // Hook MasterStudy profile tab
        add_filter('stm_lms_profile_tabs', [$this, 'add_profile_tab']);
        add_action('stm_lms_profile_tab_community', [$this, 'render_profile_tab']);
        
        // Allow additional file types for community uploads
        add_filter('upload_mimes', [$this, 'allow_additional_mimes']);
        
        // Activation hook
        register_activation_hook(__FILE__, [$this, 'activate']);
    }

    /**
     * Allow additional mime types for uploads
     */
    public function allow_additional_mimes($mimes) {
        // Audio
        $mimes['mp3'] = 'audio/mpeg';
        $mimes['wav'] = 'audio/wav';
        $mimes['ogg'] = 'audio/ogg';
        
        // Video
        $mimes['mp4'] = 'video/mp4';
        $mimes['webm'] = 'video/webm';
        $mimes['mov'] = 'video/quicktime';
        
        return $mimes;
    }

    /**
     * Plugin activation - create default categories
     */
    public function activate() {
        $this->register_post_type();
        $this->register_taxonomy();
        
        $categories = [
            'discussion-generale' => 'Discussion générale',
            'besoin-aide' => 'Besoin d\'aide',
            'victoires' => 'Victoires',
            'annonces' => 'Annonces'
        ];
        
        foreach ($categories as $slug => $name) {
            if (!term_exists($slug, 'community_category')) {
                wp_insert_term($name, 'community_category', ['slug' => $slug]);
            }
        }
        
        flush_rewrite_rules();
    }

    /**
     * Register Custom Post Type
     */
    public function register_post_type() {
        register_post_type('community_post', [
            'labels' => [
                'name' => 'Posts Communauté',
                'singular_name' => 'Post Communauté',
                'add_new' => 'Ajouter',
                'add_new_item' => 'Ajouter un post',
                'edit_item' => 'Modifier le post',
                'view_item' => 'Voir le post',
                'search_items' => 'Rechercher',
                'not_found' => 'Aucun post trouvé'
            ],
            'public' => true,
            'has_archive' => true,
            'supports' => ['title', 'editor', 'author', 'comments', 'thumbnail'],
            'menu_icon' => 'dashicons-groups',
            'show_in_rest' => true,
            'rewrite' => ['slug' => 'communaute']
        ]);
    }

    /**
     * Register Taxonomy for categories
     */
    public function register_taxonomy() {
        register_taxonomy('community_category', 'community_post', [
            'labels' => [
                'name' => 'Catégories Communauté',
                'singular_name' => 'Catégorie',
                'add_new_item' => 'Ajouter une catégorie',
                'search_items' => 'Rechercher'
            ],
            'public' => true,
            'hierarchical' => true,
            'show_in_rest' => true,
            'rewrite' => ['slug' => 'categorie-communaute']
        ]);
    }

    /**
     * Enqueue CSS and JS
     */
    public function enqueue_assets() {
        wp_enqueue_style('vic-styles', VIC_PLUGIN_URL . 'assets/css/community.css', [], VIC_VERSION);
        wp_enqueue_script('vic-scripts', VIC_PLUGIN_URL . 'assets/js/community.js', ['jquery'], VIC_VERSION, true);
        
        wp_localize_script('vic-scripts', 'vicAjax', [
            'ajaxurl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('vic_nonce')
        ]);
    }

    /**
     * Check if user has active membership (Paid Memberships Pro)
     */
    public function user_can_post($user_id = null) {
        if (!$user_id) {
            $user_id = get_current_user_id();
        }
        
        if (!$user_id) {
            return false;
        }
        
        // Admin can always post
        if (user_can($user_id, 'manage_options')) {
            return true;
        }
        
        // Check Paid Memberships Pro
        if (function_exists('pmpro_hasMembershipLevel')) {
            return pmpro_hasMembershipLevel(null, $user_id);
        }
        
        // Fallback: allow all logged-in users if PMP not active
        return is_user_logged_in();
    }

    /**
     * Check if user can post in "Annonces" category (admin only)
     */
    public function user_can_post_annonces($user_id = null) {
        if (!$user_id) {
            $user_id = get_current_user_id();
        }
        return user_can($user_id, 'manage_options');
    }

    /**
     * Render the community feed shortcode
     */
    public function render_feed($atts) {
        $atts = shortcode_atts([
            'posts_per_page' => 10
        ], $atts);

        ob_start();
        ?>
        <div class="vic-community-wrapper">
            
            <?php if ($this->user_can_post()) : ?>
            <!-- Post Creation Form -->
            <div class="vic-create-post-box">
                <div class="vic-create-post-header">
                    <?php echo get_avatar(get_current_user_id(), 48); ?>
                    <button class="vic-create-post-trigger" id="vic-open-form">
                        Écrivez quelque chose
                    </button>
                </div>
                
                <div class="vic-create-post-form" id="vic-post-form" style="display: none;">
                    <form id="vic-new-post-form">
                        <?php wp_nonce_field('vic_create_post', 'vic_post_nonce'); ?>
                        
                        <input type="text" name="post_title" placeholder="Titre" class="vic-input vic-input-title" required>
                        
                        <textarea name="post_content" placeholder="Write something..." class="vic-textarea" required></textarea>
                        
                        <!-- Champ URL caché -->
                        <div class="vic-url-field" id="vic-url-field" style="display: none;">
                            <input type="url" name="post_url" placeholder="https://example.com" class="vic-input">
                            <button type="button" class="vic-btn-remove-field" data-target="vic-url-field">✕</button>
                        </div>
                        
                        <!-- Preview des fichiers -->
                        <div class="vic-attachments-preview" id="vic-attachments-preview"></div>
                        
                        <div class="vic-form-footer">
                            <div class="vic-form-actions">
                                <!-- Bouton Pièce jointe -->
                                <label class="vic-btn-icon vic-upload-btn" title="Ajouter une pièce jointe">
                                    <input type="file" name="post_attachments[]" multiple accept="image/*,video/*,audio/*,.pdf" style="display:none;" id="vic-file-input">
                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M21.44 11.05l-9.19 9.19a6 6 0 0 1-8.49-8.49l9.19-9.19a4 4 0 0 1 5.66 5.66l-9.2 9.19a2 2 0 0 1-2.83-2.83l8.49-8.48"/>
                                    </svg>
                                </label>
                                
                                <!-- Bouton Lien -->
                                <button type="button" class="vic-btn-icon" title="Ajouter un lien" id="vic-add-url">
                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"/>
                                        <path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"/>
                                    </svg>
                                </button>
                                
                                <!-- Bouton YouTube -->
                                <button type="button" class="vic-btn-icon" title="Ajouter une vidéo YouTube" id="vic-add-youtube">
                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <rect x="2" y="4" width="20" height="16" rx="2"/>
                                        <polygon points="10,8 16,12 10,16"/>
                                    </svg>
                                </button>
                                
                                <select name="post_category" class="vic-select-category">
                                    <?php
                                    $categories = get_terms([
                                        'taxonomy' => 'community_category',
                                        'hide_empty' => false
                                    ]);
                                    foreach ($categories as $cat) {
                                        // Skip "Annonces" for non-admins
                                        if ($cat->slug === 'annonces' && !$this->user_can_post_annonces()) {
                                            continue;
                                        }
                                        echo '<option value="' . esc_attr($cat->term_id) . '">' . esc_html($cat->name) . '</option>';
                                    }
                                    ?>
                                </select>
                            </div>
                            
                            <div class="vic-form-submit">
                                <button type="button" class="vic-btn vic-btn-cancel" id="vic-cancel-form">ANNULER</button>
                                <button type="submit" class="vic-btn vic-btn-primary">POSTE</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <?php endif; ?>

            <!-- Category Filters -->
            <div class="vic-filters">
                <button class="vic-filter-btn active" data-category="all">Tous</button>
                <?php
                $categories = get_terms([
                    'taxonomy' => 'community_category',
                    'hide_empty' => false
                ]);
                
                $category_emojis = [
                    'discussion-generale' => '💬',
                    'besoin-aide' => '❗',
                    'victoires' => '🌟',
                    'annonces' => '📢'
                ];
                
                foreach ($categories as $cat) {
                    $emoji = isset($category_emojis[$cat->slug]) ? $category_emojis[$cat->slug] : '';
                    echo '<button class="vic-filter-btn" data-category="' . esc_attr($cat->slug) . '">' . esc_html($cat->name) . ' ' . $emoji . '</button>';
                }
                ?>
            </div>

            <!-- Posts Feed -->
            <div class="vic-feed" id="vic-feed">
                <?php echo $this->get_posts_html($atts['posts_per_page']); ?>
            </div>
            
            <!-- Load More -->
            <div class="vic-load-more-wrapper">
                <button class="vic-btn vic-btn-load-more" id="vic-load-more" data-page="1">
                    Charger plus
                </button>
            </div>
        </div>
        <?php
        return ob_get_clean();
    }

    /**
     * Get posts HTML
     */
    public function get_posts_html($posts_per_page = 10, $paged = 1, $category = '') {
        $args = [
            'post_type' => 'community_post',
            'posts_per_page' => $posts_per_page,
            'paged' => $paged,
            'orderby' => 'date',
            'order' => 'DESC',
            'meta_query' => [
                'relation' => 'OR',
                [
                    'key' => '_vic_pinned',
                    'compare' => 'NOT EXISTS'
                ],
                [
                    'key' => '_vic_pinned',
                    'value' => '1',
                    'compare' => '='
                ]
            ]
        ];

        // Pinned posts first
        $args['orderby'] = [
            'meta_value_num' => 'DESC',
            'date' => 'DESC'
        ];
        $args['meta_key'] = '_vic_pinned';

        if ($category && $category !== 'all') {
            $args['tax_query'] = [
                [
                    'taxonomy' => 'community_category',
                    'field' => 'slug',
                    'terms' => $category
                ]
            ];
        }

        $query = new WP_Query($args);
        
        ob_start();
        
        if ($query->have_posts()) {
            while ($query->have_posts()) {
                $query->the_post();
                $this->render_single_post(get_the_ID());
            }
            wp_reset_postdata();
        } else {
            echo '<p class="vic-no-posts">Aucun post pour le moment.</p>';
        }
        
        return ob_get_clean();
    }

    /**
     * Render a single post card
     */
    public function render_single_post($post_id) {
        $author_id = get_post_field('post_author', $post_id);
        $author_name = get_the_author_meta('display_name', $author_id);
        $post_date = human_time_diff(get_the_time('U', $post_id), current_time('timestamp')) . ' ago';
        $likes = (int) get_post_meta($post_id, '_vic_likes', true);
        $user_liked = $this->user_has_liked($post_id);
        $is_pinned = get_post_meta($post_id, '_vic_pinned', true);
        $comment_count = get_comments_number($post_id);
        
        $categories = get_the_terms($post_id, 'community_category');
        $category_name = $categories ? $categories[0]->name : '';
        $category_slug = $categories ? $categories[0]->slug : '';
        
        $category_emojis = [
            'discussion-generale' => '💬',
            'besoin-aide' => '❗',
            'victoires' => '🌟',
            'annonces' => '📢'
        ];
        $emoji = isset($category_emojis[$category_slug]) ? $category_emojis[$category_slug] : '';
        ?>
        <article class="vic-post-card" data-post-id="<?php echo $post_id; ?>">
            <?php if ($is_pinned) : ?>
                <div class="vic-pinned-badge">📌 Épinglé</div>
            <?php endif; ?>
            
            <div class="vic-post-header">
                <div class="vic-author-info">
                    <?php echo get_avatar($author_id, 48); ?>
                    <div class="vic-author-meta">
                        <span class="vic-author-name"><?php echo esc_html($author_name); ?></span>
                        <span class="vic-post-meta">
                            <?php echo esc_html($post_date); ?> • 
                            <span class="vic-category-tag"><?php echo esc_html($category_name); ?> <?php echo $emoji; ?></span>
                        </span>
                    </div>
                </div>
            </div>
            
            <h3 class="vic-post-title">
                <a href="<?php the_permalink($post_id); ?>"><?php echo get_the_title($post_id); ?></a>
            </h3>
            
            <div class="vic-post-content">
                <?php 
                $content = get_the_content(null, false, $post_id);
                $content = wp_trim_words($content, 50, '... <a href="' . get_permalink($post_id) . '" class="vic-read-more">En savoir plus</a>');
                echo wpautop($content);
                ?>
            </div>
            
            <?php 
            // Check for YouTube embed in content
            $full_content = get_post_field('post_content', $post_id);
            if (preg_match('/(?:youtube\.com\/watch\?v=|youtu\.be\/)([a-zA-Z0-9_-]+)/', $full_content, $matches)) {
                echo '<div class="vic-video-embed">';
                echo '<iframe src="https://www.youtube.com/embed/' . esc_attr($matches[1]) . '" frameborder="0" allowfullscreen></iframe>';
                echo '</div>';
            }
            
            // Display URL if set
            $post_url = get_post_meta($post_id, '_vic_post_url', true);
            if ($post_url) {
                $url_host = parse_url($post_url, PHP_URL_HOST);
                echo '<div class="vic-post-link">';
                echo '<a href="' . esc_url($post_url) . '" target="_blank" rel="noopener">';
                echo '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"/><path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"/></svg>';
                echo '<span>' . esc_html($url_host) . '</span>';
                echo '</a>';
                echo '</div>';
            }
            
            // Display attachments
            $attachments = get_post_meta($post_id, '_vic_attachments', true);
            if (!empty($attachments) && is_array($attachments)) {
                echo '<div class="vic-attachments">';
                foreach ($attachments as $attachment_id) {
                    $mime_type = get_post_mime_type($attachment_id);
                    $url = wp_get_attachment_url($attachment_id);
                    $filename = basename(get_attached_file($attachment_id));
                    
                    if (strpos($mime_type, 'image/') === 0) {
                        // Image
                        $thumb = wp_get_attachment_image_src($attachment_id, 'medium');
                        echo '<div class="vic-attachment vic-attachment-image">';
                        echo '<a href="' . esc_url($url) . '" target="_blank">';
                        echo '<img src="' . esc_url($thumb[0]) . '" alt="' . esc_attr($filename) . '">';
                        echo '</a>';
                        echo '</div>';
                    } elseif (strpos($mime_type, 'video/') === 0) {
                        // Video
                        echo '<div class="vic-attachment vic-attachment-video">';
                        echo '<video controls preload="metadata">';
                        echo '<source src="' . esc_url($url) . '" type="' . esc_attr($mime_type) . '">';
                        echo '</video>';
                        echo '</div>';
                    } elseif (strpos($mime_type, 'audio/') === 0) {
                        // Audio
                        echo '<div class="vic-attachment vic-attachment-audio">';
                        echo '<audio controls preload="metadata">';
                        echo '<source src="' . esc_url($url) . '" type="' . esc_attr($mime_type) . '">';
                        echo '</audio>';
                        echo '<span class="vic-audio-filename">' . esc_html($filename) . '</span>';
                        echo '</div>';
                    } elseif ($mime_type === 'application/pdf') {
                        // PDF
                        echo '<div class="vic-attachment vic-attachment-pdf">';
                        echo '<a href="' . esc_url($url) . '" target="_blank" class="vic-pdf-link">';
                        echo '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/></svg>';
                        echo '<span>' . esc_html($filename) . '</span>';
                        echo '</a>';
                        echo '</div>';
                    }
                }
                echo '</div>';
            }
            ?>
            
            <div class="vic-post-footer">
                <button class="vic-like-btn <?php echo $user_liked ? 'liked' : ''; ?>" data-post-id="<?php echo $post_id; ?>">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="<?php echo $user_liked ? 'currentColor' : 'none'; ?>" stroke="currentColor" stroke-width="2">
                        <path d="M14 9V5a3 3 0 0 0-3-3l-4 9v11h11.28a2 2 0 0 0 2-1.7l1.38-9a2 2 0 0 0-2-2.3zM7 22H4a2 2 0 0 1-2-2v-7a2 2 0 0 1 2-2h3"/>
                    </svg>
                    <span class="vic-like-count"><?php echo $likes; ?></span>
                </button>
                
                <a href="<?php the_permalink($post_id); ?>#comments" class="vic-comment-btn">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
                    </svg>
                    <span><?php echo $comment_count; ?></span>
                </a>
                
                <?php if (current_user_can('manage_options')) : ?>
                <button class="vic-pin-btn <?php echo $is_pinned ? 'pinned' : ''; ?>" data-post-id="<?php echo $post_id; ?>">
                    📌
                </button>
                <?php endif; ?>
            </div>
        </article>
        <?php
    }

    /**
     * Check if current user has liked a post
     */
    public function user_has_liked($post_id) {
        $user_id = get_current_user_id();
        if (!$user_id) return false;
        
        $likes = get_post_meta($post_id, '_vic_liked_users', true);
        if (!is_array($likes)) return false;
        
        return in_array($user_id, $likes);
    }

    /**
     * Handle like AJAX
     */
    public function handle_like() {
        check_ajax_referer('vic_nonce', 'nonce');
        
        if (!is_user_logged_in()) {
            wp_send_json_error(['message' => 'Vous devez être connecté']);
        }
        
        $post_id = intval($_POST['post_id']);
        $user_id = get_current_user_id();
        
        $likes = (int) get_post_meta($post_id, '_vic_likes', true);
        $liked_users = get_post_meta($post_id, '_vic_liked_users', true);
        
        if (!is_array($liked_users)) {
            $liked_users = [];
        }
        
        if (in_array($user_id, $liked_users)) {
            // Unlike
            $likes--;
            $liked_users = array_diff($liked_users, [$user_id]);
            $action = 'unliked';
        } else {
            // Like
            $likes++;
            $liked_users[] = $user_id;
            $action = 'liked';
        }
        
        update_post_meta($post_id, '_vic_likes', $likes);
        update_post_meta($post_id, '_vic_liked_users', $liked_users);
        
        wp_send_json_success([
            'likes' => $likes,
            'action' => $action
        ]);
    }

    /**
     * Handle post creation AJAX
     */
    public function handle_create_post() {
        check_ajax_referer('vic_nonce', 'nonce');
        
        if (!$this->user_can_post()) {
            wp_send_json_error(['message' => 'Vous n\'avez pas la permission de poster']);
        }
        
        $title = sanitize_text_field($_POST['post_title']);
        $content = wp_kses_post($_POST['post_content']);
        $category_id = intval($_POST['post_category']);
        $post_url = isset($_POST['post_url']) ? esc_url_raw($_POST['post_url']) : '';
        
        // Check if trying to post in "Annonces" without admin rights
        $term = get_term($category_id, 'community_category');
        if ($term && $term->slug === 'annonces' && !$this->user_can_post_annonces()) {
            wp_send_json_error(['message' => 'Seuls les administrateurs peuvent poster des annonces']);
        }
        
        $post_id = wp_insert_post([
            'post_title' => $title,
            'post_content' => $content,
            'post_type' => 'community_post',
            'post_status' => 'publish',
            'post_author' => get_current_user_id()
        ]);
        
        if (is_wp_error($post_id)) {
            wp_send_json_error(['message' => 'Erreur lors de la création du post']);
        }
        
        wp_set_object_terms($post_id, [$category_id], 'community_category');
        
        // Save URL if provided
        if ($post_url) {
            update_post_meta($post_id, '_vic_post_url', $post_url);
        }
        
        // Handle file uploads
        if (!empty($_FILES['post_attachments'])) {
            require_once(ABSPATH . 'wp-admin/includes/image.php');
            require_once(ABSPATH . 'wp-admin/includes/file.php');
            require_once(ABSPATH . 'wp-admin/includes/media.php');
            
            $attachment_ids = [];
            $files = $_FILES['post_attachments'];
            
            // Allowed file types
            $allowed_types = [
                'image/jpeg', 'image/png', 'image/gif', 'image/webp',
                'video/mp4', 'video/webm', 'video/ogg', 'video/quicktime',
                'audio/mpeg', 'audio/wav', 'audio/ogg', 'audio/mp3',
                'application/pdf'
            ];
            
            // Max file size (10MB)
            $max_size = 10 * 1024 * 1024;
            
            for ($i = 0; $i < count($files['name']); $i++) {
                if ($files['error'][$i] === UPLOAD_ERR_OK) {
                    // Check file type
                    $file_type = $files['type'][$i];
                    if (!in_array($file_type, $allowed_types)) {
                        continue;
                    }
                    
                    // Check file size
                    if ($files['size'][$i] > $max_size) {
                        continue;
                    }
                    
                    $file = [
                        'name' => $files['name'][$i],
                        'type' => $files['type'][$i],
                        'tmp_name' => $files['tmp_name'][$i],
                        'error' => $files['error'][$i],
                        'size' => $files['size'][$i]
                    ];
                    
                    $_FILES['upload_file'] = $file;
                    
                    $attachment_id = media_handle_upload('upload_file', $post_id);
                    
                    if (!is_wp_error($attachment_id)) {
                        $attachment_ids[] = $attachment_id;
                    }
                }
            }
            
            if (!empty($attachment_ids)) {
                update_post_meta($post_id, '_vic_attachments', $attachment_ids);
            }
        }
        
        // Get the new post HTML
        ob_start();
        $this->render_single_post($post_id);
        $post_html = ob_get_clean();
        
        wp_send_json_success([
            'message' => 'Post créé avec succès',
            'post_html' => $post_html
        ]);
    }

    /**
     * Handle load posts AJAX (for filters and pagination)
     */
    public function handle_load_posts() {
        check_ajax_referer('vic_nonce', 'nonce');
        
        $category = sanitize_text_field($_POST['category']);
        $page = intval($_POST['page']);
        $posts_per_page = 10;
        
        $html = $this->get_posts_html($posts_per_page, $page, $category);
        
        // Check if there are more posts
        $args = [
            'post_type' => 'community_post',
            'posts_per_page' => $posts_per_page,
            'paged' => $page + 1
        ];
        
        if ($category && $category !== 'all') {
            $args['tax_query'] = [
                [
                    'taxonomy' => 'community_category',
                    'field' => 'slug',
                    'terms' => $category
                ]
            ];
        }
        
        $next_query = new WP_Query($args);
        $has_more = $next_query->have_posts();
        
        wp_send_json_success([
            'html' => $html,
            'has_more' => $has_more
        ]);
    }

    /**
     * Handle pin/unpin post AJAX (admin only)
     */
    public function handle_pin_post() {
        check_ajax_referer('vic_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_send_json_error(['message' => 'Permission refusée']);
        }
        
        $post_id = intval($_POST['post_id']);
        $is_pinned = get_post_meta($post_id, '_vic_pinned', true);
        
        if ($is_pinned) {
            delete_post_meta($post_id, '_vic_pinned');
            $pinned = false;
        } else {
            update_post_meta($post_id, '_vic_pinned', '1');
            $pinned = true;
        }
        
        wp_send_json_success([
            'pinned' => $pinned
        ]);
    }

    /**
     * Add tab to MasterStudy profile
     */
    public function add_profile_tab($tabs) {
        $tabs['community'] = [
            'id' => 'community',
            'title' => 'Communauté',
            'icon' => 'fa fa-comments',
            'callback' => [$this, 'render_profile_tab']
        ];
        return $tabs;
    }

    /**
     * Render MasterStudy profile tab content
     */
    public function render_profile_tab($user_id = null) {
        if (!$user_id) {
            $user_id = get_query_var('user_id', get_current_user_id());
        }
        
        // User's posts
        $posts = get_posts([
            'post_type' => 'community_post',
            'author' => $user_id,
            'posts_per_page' => 10,
            'orderby' => 'date',
            'order' => 'DESC'
        ]);
        
        // User's comments
        $comments = get_comments([
            'user_id' => $user_id,
            'post_type' => 'community_post',
            'number' => 10,
            'orderby' => 'comment_date',
            'order' => 'DESC'
        ]);
        
        ?>
        <div class="vic-profile-tab">
            <div class="vic-profile-section">
                <h3>Mes Posts (<?php echo count($posts); ?>)</h3>
                <?php if ($posts) : ?>
                    <ul class="vic-profile-posts-list">
                        <?php foreach ($posts as $post) : ?>
                            <li>
                                <a href="<?php echo get_permalink($post->ID); ?>">
                                    <?php echo esc_html($post->post_title); ?>
                                </a>
                                <span class="vic-date"><?php echo human_time_diff(strtotime($post->post_date), current_time('timestamp')); ?></span>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php else : ?>
                    <p>Aucun post pour le moment.</p>
                <?php endif; ?>
            </div>
            
            <div class="vic-profile-section">
                <h3>Mes Commentaires (<?php echo count($comments); ?>)</h3>
                <?php if ($comments) : ?>
                    <ul class="vic-profile-comments-list">
                        <?php foreach ($comments as $comment) : ?>
                            <li>
                                <a href="<?php echo get_comment_link($comment); ?>">
                                    <?php echo wp_trim_words($comment->comment_content, 15); ?>
                                </a>
                                <span class="vic-date"><?php echo human_time_diff(strtotime($comment->comment_date), current_time('timestamp')); ?></span>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php else : ?>
                    <p>Aucun commentaire pour le moment.</p>
                <?php endif; ?>
            </div>
        </div>
        <?php
    }
}

// Initialize plugin
new Vision_IA_Community();
