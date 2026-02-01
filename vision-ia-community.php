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
        add_action('wp_ajax_vic_search_posts', [$this, 'handle_search_posts']);
        add_action('wp_ajax_nopriv_vic_search_posts', [$this, 'handle_search_posts']);
        add_action('wp_ajax_vic_get_post_modal', [$this, 'handle_get_post_modal']);
        add_action('wp_ajax_nopriv_vic_get_post_modal', [$this, 'handle_get_post_modal']);
        add_action('wp_ajax_vic_add_comment', [$this, 'handle_add_comment']);
        add_action('wp_ajax_vic_like_comment', [$this, 'handle_like_comment']);
        add_action('wp_ajax_vic_reply_comment', [$this, 'handle_reply_comment']);
        add_action('wp_ajax_vic_report_comment', [$this, 'handle_report_comment']);
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
                        
                        <textarea name="post_content" placeholder="Écrivez quelque chose..." class="vic-textarea" required></textarea>

                        <!-- Champs cachés pour GIF et YouTube -->
                        <input type="hidden" name="post_gif" id="vic-post-gif-input" value="">
                        <input type="hidden" name="post_youtube" id="vic-post-youtube-input" value="">

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

                                <!-- Bouton Emoji -->
                                <button type="button" class="vic-btn-icon vic-post-add-emoji" title="Ajouter un emoji">
                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <circle cx="12" cy="12" r="10"/>
                                        <path d="M8 14s1.5 2 4 2 4-2 4-2"/>
                                        <line x1="9" y1="9" x2="9.01" y2="9"/>
                                        <line x1="15" y1="9" x2="15.01" y2="9"/>
                                    </svg>
                                </button>

                                <!-- Bouton GIF -->
                                <button type="button" class="vic-btn-icon vic-post-add-gif" title="Ajouter un GIF">
                                    <span style="font-weight: 600; font-size: 11px;">GIF</span>
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

            <!-- Search Bar -->
            <div class="vic-search-box">
                <div class="vic-search-wrapper">
                    <svg class="vic-search-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="11" cy="11" r="8"/>
                        <path d="M21 21l-4.35-4.35"/>
                    </svg>
                    <input type="text" id="vic-search-input" class="vic-search-input" placeholder="Rechercher dans la communauté...">
                    <button type="button" id="vic-search-clear" class="vic-search-clear" style="display: none;">✕</button>
                </div>
            </div>

            <!-- Category Filters -->
            <div class="vic-filters">
                <button class="vic-filter-btn active" data-category="all">Tous</button>
                <button class="vic-search-toggle-btn" id="vic-search-toggle" title="Rechercher">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="11" cy="11" r="8"/>
                        <path d="M21 21l-4.35-4.35"/>
                    </svg>
                </button>
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
            'post_status' => 'publish'
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

        // Get pinned posts first, then others by date
        $pinned_args = array_merge($args, [
            'meta_key' => '_vic_pinned',
            'meta_value' => '1',
            'orderby' => 'date',
            'order' => 'DESC'
        ]);

        $regular_args = array_merge($args, [
            'meta_query' => [
                'relation' => 'OR',
                [
                    'key' => '_vic_pinned',
                    'compare' => 'NOT EXISTS'
                ],
                [
                    'key' => '_vic_pinned',
                    'value' => '1',
                    'compare' => '!='
                ]
            ],
            'orderby' => 'date',
            'order' => 'DESC'
        ]);

        $pinned_query = new WP_Query($pinned_args);
        $regular_query = new WP_Query($regular_args);

        ob_start();

        $has_posts = false;

        // Render pinned posts first
        if ($pinned_query->have_posts()) {
            $has_posts = true;
            while ($pinned_query->have_posts()) {
                $pinned_query->the_post();
                $this->render_single_post(get_the_ID());
            }
            wp_reset_postdata();
        }

        // Then render regular posts
        if ($regular_query->have_posts()) {
            $has_posts = true;
            while ($regular_query->have_posts()) {
                $regular_query->the_post();
                $this->render_single_post(get_the_ID());
            }
            wp_reset_postdata();
        }

        if (!$has_posts) {
            echo '<p class="vic-no-posts">Aucun post pour le moment.</p>';
        }

        return ob_get_clean();
    }

    /**
     * Render a single post card (feed view with thumbnail)
     */
    public function render_single_post($post_id) {
        $author_id = get_post_field('post_author', $post_id);
        $author_name = get_the_author_meta('display_name', $author_id);
        $post_date = human_time_diff(get_the_time('U', $post_id), current_time('timestamp'));
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

        // Get thumbnail (first image, video, or YouTube)
        $thumbnail = $this->get_post_thumbnail($post_id);

        // Get recent commenters
        $recent_comments = get_comments([
            'post_id' => $post_id,
            'number' => 5,
            'orderby' => 'comment_date',
            'order' => 'DESC'
        ]);
        ?>
        <article class="vic-post-card" data-post-id="<?php echo $post_id; ?>">
            <?php if ($is_pinned) : ?>
                <div class="vic-pinned-badge">📌 Épinglé</div>
            <?php endif; ?>

            <div class="vic-post-body">
                <div class="vic-post-main">
                    <div class="vic-post-header">
                        <div class="vic-author-info">
                            <?php echo get_avatar($author_id, 40); ?>
                            <div class="vic-author-meta">
                                <span class="vic-author-name"><?php echo esc_html($author_name); ?></span>
                                <span class="vic-post-meta">
                                    <?php echo esc_html($post_date); ?> •
                                    <span class="vic-category-tag"><?php echo esc_html($category_name); ?> <?php echo $emoji; ?></span>
                                </span>
                            </div>
                        </div>
                    </div>

                    <h3 class="vic-post-title"><?php echo get_the_title($post_id); ?></h3>

                    <p class="vic-post-excerpt">
                        <?php
                        $content = get_post_field('post_content', $post_id);
                        // Remove URLs for excerpt
                        $content = preg_replace('/https?:\/\/[^\s]+/', '', $content);
                        // Remove GIF tags for excerpt
                        $content = preg_replace('/\[gif\].*?\[\/gif\]/i', '', $content);
                        echo wp_trim_words(wp_strip_all_tags($content), 25, '...');
                        ?>
                    </p>
                </div>

                <?php if ($thumbnail) : ?>
                <div class="vic-post-thumbnail <?php echo $thumbnail['type']; ?>">
                    <?php if ($thumbnail['type'] === 'vic-post-thumbnail-youtube') : ?>
                        <img src="https://img.youtube.com/vi/<?php echo esc_attr($thumbnail['youtube_id']); ?>/mqdefault.jpg" alt="YouTube">
                    <?php elseif ($thumbnail['type'] === 'vic-post-thumbnail-image') : ?>
                        <img src="<?php echo esc_url($thumbnail['url']); ?>" alt="">
                    <?php elseif ($thumbnail['type'] === 'vic-post-thumbnail-video') : ?>
                        <video src="<?php echo esc_url($thumbnail['url']); ?>" muted></video>
                    <?php elseif ($thumbnail['type'] === 'vic-post-thumbnail-gif') : ?>
                        <img src="<?php echo esc_url($thumbnail['url']); ?>" alt="GIF" class="vic-gif-thumbnail">
                    <?php endif; ?>
                </div>
                <?php endif; ?>
            </div>

            <div class="vic-post-footer">
                <button class="vic-like-btn <?php echo $user_liked ? 'liked' : ''; ?>" data-post-id="<?php echo $post_id; ?>">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="<?php echo $user_liked ? 'currentColor' : 'none'; ?>" stroke="currentColor" stroke-width="2">
                        <path d="M14 9V5a3 3 0 0 0-3-3l-4 9v11h11.28a2 2 0 0 0 2-1.7l1.38-9a2 2 0 0 0-2-2.3zM7 22H4a2 2 0 0 1-2-2v-7a2 2 0 0 1 2-2h3"/>
                    </svg>
                    <span class="vic-like-count"><?php echo $likes; ?></span>
                </button>

                <button class="vic-comment-btn" data-post-id="<?php echo $post_id; ?>">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
                    </svg>
                    <span><?php echo $comment_count; ?></span>
                </button>

                <?php if (current_user_can('manage_options')) : ?>
                <button class="vic-pin-btn <?php echo $is_pinned ? 'pinned' : ''; ?>" data-post-id="<?php echo $post_id; ?>">
                    📌
                </button>
                <?php endif; ?>

                <?php if (!empty($recent_comments)) : ?>
                <div class="vic-commenters">
                    <div class="vic-commenters-avatars">
                        <?php
                        $shown = 0;
                        $unique_users = [];
                        foreach ($recent_comments as $comment) {
                            if ($shown >= 4) break;
                            if (in_array($comment->user_id, $unique_users)) continue;
                            $unique_users[] = $comment->user_id;
                            echo get_avatar($comment->user_id ?: $comment->comment_author_email, 24);
                            $shown++;
                        }
                        ?>
                    </div>
                    <span class="vic-last-comment">
                        <?php
                        $last_comment_time = human_time_diff(strtotime($recent_comments[0]->comment_date), current_time('timestamp'));
                        echo 'Nouveau commentaire il y a ' . $last_comment_time;
                        ?>
                    </span>
                </div>
                <?php endif; ?>
            </div>
        </article>
        <?php
    }

    /**
     * Get post thumbnail (image, video, YouTube, or GIF)
     */
    public function get_post_thumbnail($post_id) {
        $content = get_post_field('post_content', $post_id);
        $attachments = get_post_meta($post_id, '_vic_attachments', true);
        $post_gif = get_post_meta($post_id, '_vic_post_gif', true);
        $post_youtube = get_post_meta($post_id, '_vic_post_youtube', true);

        // Check for YouTube from meta field first
        if (!empty($post_youtube) && preg_match('/(?:youtube\.com\/watch\?v=|youtu\.be\/)([a-zA-Z0-9_-]+)/', $post_youtube, $matches)) {
            return [
                'type' => 'vic-post-thumbnail-youtube',
                'youtube_id' => $matches[1]
            ];
        }

        // Check for YouTube in content (legacy support)
        if (preg_match('/(?:youtube\.com\/watch\?v=|youtu\.be\/)([a-zA-Z0-9_-]+)/', $content, $matches)) {
            return [
                'type' => 'vic-post-thumbnail-youtube',
                'youtube_id' => $matches[1]
            ];
        }

        // Check for GIF from meta field first
        if (!empty($post_gif)) {
            return [
                'type' => 'vic-post-thumbnail-gif',
                'url' => $post_gif
            ];
        }

        // Check for GIF [gif]url[/gif] in content (legacy support)
        if (preg_match('/\[gif\](.*?)\[\/gif\]/i', $content, $gif_matches)) {
            return [
                'type' => 'vic-post-thumbnail-gif',
                'url' => $gif_matches[1]
            ];
        }

        // Check attachments
        if (!empty($attachments) && is_array($attachments)) {
            foreach ($attachments as $attachment_id) {
                $mime_type = get_post_mime_type($attachment_id);

                if (strpos($mime_type, 'image/') === 0) {
                    $thumb = wp_get_attachment_image_src($attachment_id, 'thumbnail');
                    return [
                        'type' => 'vic-post-thumbnail-image',
                        'url' => $thumb[0]
                    ];
                }

                if (strpos($mime_type, 'video/') === 0) {
                    return [
                        'type' => 'vic-post-thumbnail-video',
                        'url' => wp_get_attachment_url($attachment_id)
                    ];
                }
            }
        }

        return null;
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
        $post_gif = isset($_POST['post_gif']) ? esc_url_raw($_POST['post_gif']) : '';
        $post_youtube = isset($_POST['post_youtube']) ? esc_url_raw($_POST['post_youtube']) : '';
        
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

        // Save GIF if provided
        if ($post_gif) {
            update_post_meta($post_id, '_vic_post_gif', $post_gif);
        }

        // Save YouTube URL if provided
        if ($post_youtube) {
            update_post_meta($post_id, '_vic_post_youtube', $post_youtube);
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
     * Handle search posts AJAX
     */
    public function handle_search_posts() {
        check_ajax_referer('vic_nonce', 'nonce');

        $search = sanitize_text_field($_POST['search']);
        $category = sanitize_text_field($_POST['category']);
        $posts_per_page = 10;

        $args = [
            'post_type' => 'community_post',
            'posts_per_page' => $posts_per_page,
            'orderby' => 'date',
            'order' => 'DESC'
        ];

        // Add search parameter
        if (!empty($search)) {
            $args['s'] = $search;
        }

        // Add category filter
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
            if (!empty($search)) {
                echo '<p class="vic-no-posts">Aucun résultat pour "' . esc_html($search) . '"</p>';
            } else {
                echo '<p class="vic-no-posts">Aucun post pour le moment.</p>';
            }
        }

        $html = ob_get_clean();

        // Check if there are more posts
        $has_more = $query->max_num_pages > 1;

        wp_send_json_success([
            'html' => $html,
            'has_more' => $has_more,
            'found_posts' => $query->found_posts
        ]);
    }

    /**
     * Handle get post modal AJAX
     */
    public function handle_get_post_modal() {
        check_ajax_referer('vic_nonce', 'nonce');

        $post_id = intval($_POST['post_id']);
        $post = get_post($post_id);

        if (!$post) {
            wp_send_json_error(['message' => 'Post non trouvé']);
        }

        $author_id = $post->post_author;
        $author_name = get_the_author_meta('display_name', $author_id);
        $post_date = human_time_diff(strtotime($post->post_date), current_time('timestamp'));
        $likes = (int) get_post_meta($post_id, '_vic_likes', true);
        $user_liked = $this->user_has_liked($post_id);
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

        ob_start();
        ?>
        <!-- Modal Header -->
        <div class="vic-modal-header">
            <div class="vic-modal-header-left">
                <?php echo get_avatar($author_id, 40); ?>
                <h3 class="vic-modal-title"><?php echo esc_html($author_name); ?></h3>
            </div>
            <div class="vic-modal-actions">
                <button class="vic-modal-action-btn vic-modal-close">✕</button>
            </div>
        </div>

        <!-- Modal Content -->
        <div class="vic-modal-content">
            <div class="vic-modal-post-meta">
                <span>il y a <?php echo esc_html($post_date); ?></span>
                <span>•</span>
                <span class="vic-category-tag"><?php echo esc_html($category_name); ?> <?php echo $emoji; ?></span>
            </div>

            <h2 class="vic-modal-post-title"><?php echo esc_html($post->post_title); ?></h2>

            <div class="vic-modal-post-content">
                <?php
                $content = $post->post_content;

                // Parser les GIFs [gif]url[/gif] et les transformer en images
                $content = preg_replace_callback('/\[gif\](.*?)\[\/gif\]/i', function($matches) {
                    $gif_url = esc_url($matches[1]);
                    return '<img src="' . $gif_url . '" alt="GIF" class="vic-post-gif">';
                }, $content);

                // Convert URLs to links
                $content = make_clickable($content);
                echo wpautop($content);

                // YouTube embed
                if (preg_match('/(?:youtube\.com\/watch\?v=|youtu\.be\/)([a-zA-Z0-9_-]+)/', $post->post_content, $matches)) {
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
                            $full = wp_get_attachment_image_src($attachment_id, 'large');
                            echo '<div class="vic-attachment vic-attachment-image">';
                            echo '<img src="' . esc_url($full[0]) . '" alt="' . esc_attr($filename) . '">';
                            echo '</div>';
                        } elseif (strpos($mime_type, 'video/') === 0) {
                            echo '<div class="vic-attachment vic-attachment-video">';
                            echo '<video controls><source src="' . esc_url($url) . '" type="' . esc_attr($mime_type) . '"></video>';
                            echo '</div>';
                        } elseif (strpos($mime_type, 'audio/') === 0) {
                            echo '<div class="vic-attachment vic-attachment-audio">';
                            echo '<audio controls><source src="' . esc_url($url) . '" type="' . esc_attr($mime_type) . '"></audio>';
                            echo '<span class="vic-audio-filename">' . esc_html($filename) . '</span>';
                            echo '</div>';
                        } elseif ($mime_type === 'application/pdf') {
                            echo '<div class="vic-attachment vic-attachment-pdf">';
                            echo '<a href="' . esc_url($url) . '" target="_blank" class="vic-pdf-link">';
                            echo '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>';
                            echo '<span>' . esc_html($filename) . '</span>';
                            echo '</a>';
                            echo '</div>';
                        }
                    }
                    echo '</div>';
                }
                ?>
            </div>

            <!-- Post Actions -->
            <div class="vic-modal-post-actions">
                <button class="vic-like-btn <?php echo $user_liked ? 'liked' : ''; ?>" data-post-id="<?php echo $post_id; ?>">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="<?php echo $user_liked ? 'currentColor' : 'none'; ?>" stroke="currentColor" stroke-width="2">
                        <path d="M14 9V5a3 3 0 0 0-3-3l-4 9v11h11.28a2 2 0 0 0 2-1.7l1.38-9a2 2 0 0 0-2-2.3zM7 22H4a2 2 0 0 1-2-2v-7a2 2 0 0 1 2-2h3"/>
                    </svg>
                    <span>J'aime</span>
                    <span class="vic-like-count"><?php echo $likes; ?></span>
                </button>

                <span class="vic-comment-count">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
                    </svg>
                    <?php echo $comment_count; ?> commentaires
                </span>
            </div>

            <!-- Comments Section -->
            <div class="vic-comments-section">
                <div class="vic-comments-list">
                    <?php
                    // Only get top-level comments (replies are rendered recursively)
                    $comments = get_comments([
                        'post_id' => $post_id,
                        'status' => 'approve',
                        'parent' => 0,
                        'orderby' => 'comment_date',
                        'order' => 'ASC'
                    ]);

                    if ($comments) {
                        foreach ($comments as $comment) {
                            $this->render_single_comment($comment, 0);
                        }
                    } else {
                        echo '<p class="vic-no-comments">Soyez le premier à commenter !</p>';
                    }
                    ?>
                </div>
            </div>

            <!-- Jump to Latest Comment - Sticky -->
            <?php if ($comment_count > 1) : ?>
            <div class="vic-jump-latest-sticky" id="vic-jump-latest">
                <button class="vic-jump-latest" type="button">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M19 12l-7 7-7-7"/>
                    </svg>
                    Aller au dernier commentaire
                </button>
            </div>
            <?php endif; ?>

            <!-- Comment Form (Skool-style) -->
            <?php if (is_user_logged_in()) : ?>
            <div class="vic-comment-form-wrapper-skool">
                <div class="vic-comment-form-skool">
                    <?php echo get_avatar(get_current_user_id(), 32, '', '', ['class' => 'vic-comment-avatar-skool']); ?>
                    <div class="vic-comment-input-skool-wrapper">
                        <input type="text" class="vic-comment-input-skool" placeholder="Votre commentaire">
                        <div class="vic-comment-tools-skool">
                            <label class="vic-comment-tool-skool" title="Pièce jointe">
                                <input type="file" class="vic-comment-file-input" accept="image/*" style="display:none;">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M21.44 11.05l-9.19 9.19a6 6 0 0 1-8.49-8.49l9.19-9.19a4 4 0 0 1 5.66 5.66l-9.2 9.19a2 2 0 0 1-2.83-2.83l8.49-8.48"/>
                                </svg>
                            </label>
                            <button type="button" class="vic-comment-tool-skool vic-comment-add-link" title="Lien">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"/>
                                    <path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"/>
                                </svg>
                            </button>
                            <button type="button" class="vic-comment-tool-skool vic-comment-add-youtube" title="YouTube">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <rect x="2" y="4" width="20" height="16" rx="2"/>
                                    <polygon points="10,8 16,12 10,16"/>
                                </svg>
                            </button>
                            <button type="button" class="vic-comment-tool-skool vic-comment-add-emoji" title="Emoji">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <circle cx="12" cy="12" r="10"/>
                                    <path d="M8 14s1.5 2 4 2 4-2 4-2"/>
                                    <line x1="9" y1="9" x2="9.01" y2="9"/>
                                    <line x1="15" y1="9" x2="15.01" y2="9"/>
                                </svg>
                            </button>
                            <button type="button" class="vic-comment-tool-skool vic-comment-add-gif" title="GIF">
                                <span style="font-weight: 600; font-size: 12px;">GIF</span>
                            </button>
                            <button type="button" class="vic-comment-submit-skool" title="Envoyer">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <line x1="22" y1="2" x2="11" y2="13"/>
                                    <polygon points="22 2 15 22 11 13 2 9 22 2"/>
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="vic-comment-attachments-preview"></div>
            </div>
            <?php else : ?>
            <div class="vic-comment-form-wrapper-skool">
                <p style="text-align: center; color: #6B7280; padding: 12px;">
                    <a href="<?php echo wp_login_url(get_permalink($post_id)); ?>">Connectez-vous</a> pour commenter
                </p>
            </div>
            <?php endif; ?>
        </div>
        <?php
        $html = ob_get_clean();

        wp_send_json_success(['html' => $html]);
    }

    /**
     * Render a single comment
     */
    public function render_single_comment($comment, $depth = 0) {
        $comment_date = human_time_diff(strtotime($comment->comment_date), current_time('timestamp'));
        $likes = (int) get_comment_meta($comment->comment_ID, '_vic_comment_likes', true);
        $user_liked = $this->user_has_liked_comment($comment->comment_ID);
        $max_depth = 3; // Maximum reply depth
        ?>
        <div class="vic-comment <?php echo $depth > 0 ? 'vic-comment-reply' : ''; ?>" data-comment-id="<?php echo $comment->comment_ID; ?>" data-depth="<?php echo $depth; ?>">
            <?php echo get_avatar($comment->user_id ?: $comment->comment_author_email, 36, '', '', ['class' => 'vic-comment-avatar']); ?>
            <div class="vic-comment-body">
                <div class="vic-comment-header">
                    <span class="vic-comment-author"><?php echo esc_html($comment->comment_author); ?></span>
                    <span class="vic-comment-date">• <?php echo $comment_date; ?></span>
                </div>
                <div class="vic-comment-content">
                    <?php
                    $content = $comment->comment_content;

                    // Parser les GIFs [gif]url[/gif] et les transformer en images
                    $content = preg_replace_callback('/\[gif\](.*?)\[\/gif\]/i', function($matches) {
                        $gif_url = esc_url($matches[1]);
                        return '<img src="' . $gif_url . '" alt="GIF" class="vic-comment-gif">';
                    }, $content);

                    // Parser les URLs YouTube et les transformer en aperçu cliquable
                    $content = preg_replace_callback(
                        '/(https?:\/\/(?:www\.)?(?:youtube\.com\/watch\?v=|youtu\.be\/)([a-zA-Z0-9_-]+)[^\s]*)/',
                        function($matches) {
                            $full_url = esc_url($matches[1]);
                            $video_id = $matches[2];
                            $thumbnail = 'https://img.youtube.com/vi/' . $video_id . '/mqdefault.jpg';
                            return '<a href="' . $full_url . '" target="_blank" rel="noopener" class="vic-youtube-embed-link">
                                <div class="vic-youtube-embed">
                                    <img src="' . $thumbnail . '" alt="YouTube" class="vic-youtube-embed-thumb">
                                    <div class="vic-youtube-play-overlay">
                                        <svg width="48" height="48" viewBox="0 0 24 24" fill="white">
                                            <path d="M8 5v14l11-7z"/>
                                        </svg>
                                    </div>
                                </div>
                            </a>';
                        },
                        $content
                    );

                    $content = make_clickable($content);
                    echo wpautop($content);

                    // Display comment attachments if any
                    $attachments = get_comment_meta($comment->comment_ID, '_vic_comment_attachments', true);
                    if (!empty($attachments) && is_array($attachments)) {
                        foreach ($attachments as $attachment_id) {
                            $url = wp_get_attachment_url($attachment_id);
                            $mime_type = get_post_mime_type($attachment_id);
                            if (strpos($mime_type, 'image/') === 0) {
                                echo '<img src="' . esc_url($url) . '" alt="">';
                            }
                        }
                    }
                    ?>
                </div>
                <div class="vic-comment-actions">
                    <button class="vic-comment-like-btn <?php echo $user_liked ? 'liked' : ''; ?>" data-comment-id="<?php echo $comment->comment_ID; ?>">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="<?php echo $user_liked ? 'currentColor' : 'none'; ?>" stroke="currentColor" stroke-width="2">
                            <path d="M14 9V5a3 3 0 0 0-3-3l-4 9v11h11.28a2 2 0 0 0 2-1.7l1.38-9a2 2 0 0 0-2-2.3zM7 22H4a2 2 0 0 1-2-2v-7a2 2 0 0 1 2-2h3"/>
                        </svg>
                        <span class="vic-comment-like-count"><?php echo $likes; ?></span>
                    </button>
                    <?php if ($depth < $max_depth && is_user_logged_in()) : ?>
                    <button class="vic-comment-reply-btn" data-comment-id="<?php echo $comment->comment_ID; ?>" data-post-id="<?php echo $comment->comment_post_ID; ?>">Répondre</button>
                    <?php endif; ?>

                    <!-- Menu 3 points -->
                    <div class="vic-comment-menu">
                        <button class="vic-comment-menu-trigger" type="button">•••</button>
                        <div class="vic-comment-menu-dropdown">
                            <button type="button" class="vic-copy-link" data-comment-id="<?php echo $comment->comment_ID; ?>" data-post-id="<?php echo $comment->comment_post_ID; ?>">Copier le lien</button>
                            <button type="button" class="vic-report-comment" data-comment-id="<?php echo $comment->comment_ID; ?>">Signaler aux admins</button>
                        </div>
                    </div>
                </div>

                <!-- Reply Form (hidden by default) -->
                <?php if ($depth < $max_depth && is_user_logged_in()) : ?>
                <div class="vic-reply-form-wrapper" style="display: none;">
                    <div class="vic-reply-form">
                        <?php echo get_avatar(get_current_user_id(), 28, '', '', ['class' => 'vic-reply-avatar']); ?>
                        <div class="vic-reply-input-wrapper">
                            <textarea class="vic-reply-input" placeholder="Répondre à <?php echo esc_attr($comment->comment_author); ?>..." rows="1"></textarea>
                            <div class="vic-reply-form-actions">
                                <button type="button" class="vic-reply-cancel">Annuler</button>
                                <button type="button" class="vic-reply-submit" data-comment-id="<?php echo $comment->comment_ID; ?>" data-post-id="<?php echo $comment->comment_post_ID; ?>">Répondre</button>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Nested Replies -->
                <?php
                $replies = get_comments([
                    'parent' => $comment->comment_ID,
                    'status' => 'approve',
                    'orderby' => 'comment_date',
                    'order' => 'ASC'
                ]);

                if ($replies) {
                    echo '<div class="vic-comment-replies">';
                    foreach ($replies as $reply) {
                        $this->render_single_comment($reply, $depth + 1);
                    }
                    echo '</div>';
                }
                ?>
            </div>
        </div>
        <?php
    }

    /**
     * Check if current user has liked a comment
     */
    public function user_has_liked_comment($comment_id) {
        $user_id = get_current_user_id();
        if (!$user_id) return false;

        $likes = get_comment_meta($comment_id, '_vic_comment_liked_users', true);
        if (!is_array($likes)) return false;

        return in_array($user_id, $likes);
    }

    /**
     * Handle comment like AJAX
     */
    public function handle_like_comment() {
        check_ajax_referer('vic_nonce', 'nonce');

        if (!is_user_logged_in()) {
            wp_send_json_error(['message' => 'Vous devez être connecté']);
        }

        $comment_id = intval($_POST['comment_id']);
        $user_id = get_current_user_id();

        $likes = (int) get_comment_meta($comment_id, '_vic_comment_likes', true);
        $liked_users = get_comment_meta($comment_id, '_vic_comment_liked_users', true);

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

        update_comment_meta($comment_id, '_vic_comment_likes', $likes);
        update_comment_meta($comment_id, '_vic_comment_liked_users', $liked_users);

        wp_send_json_success([
            'likes' => $likes,
            'action' => $action
        ]);
    }

    /**
     * Handle reply to comment AJAX
     */
    public function handle_reply_comment() {
        check_ajax_referer('vic_nonce', 'nonce');

        if (!is_user_logged_in()) {
            wp_send_json_error(['message' => 'Vous devez être connecté']);
        }

        $post_id = intval($_POST['post_id']);
        $parent_comment_id = intval($_POST['parent_comment_id']);
        $content = sanitize_textarea_field($_POST['content']);

        if (empty($content)) {
            wp_send_json_error(['message' => 'La réponse ne peut pas être vide']);
        }

        $user = wp_get_current_user();

        $comment_data = [
            'comment_post_ID' => $post_id,
            'comment_parent' => $parent_comment_id,
            'comment_content' => $content,
            'user_id' => $user->ID,
            'comment_author' => $user->display_name,
            'comment_author_email' => $user->user_email,
            'comment_approved' => 1
        ];

        $comment_id = wp_insert_comment($comment_data);

        if (!$comment_id) {
            wp_send_json_error(['message' => 'Erreur lors de l\'ajout de la réponse']);
        }

        // Get comment HTML
        $comment = get_comment($comment_id);

        // Get parent depth
        $parent_depth = 0;
        $parent = get_comment($parent_comment_id);
        while ($parent && $parent->comment_parent) {
            $parent_depth++;
            $parent = get_comment($parent->comment_parent);
        }

        ob_start();
        $this->render_single_comment($comment, $parent_depth + 1);
        $comment_html = ob_get_clean();

        wp_send_json_success([
            'comment_html' => $comment_html,
            'comment_id' => $comment_id
        ]);
    }

    /**
     * Handle add comment AJAX
     */
    public function handle_add_comment() {
        check_ajax_referer('vic_nonce', 'nonce');

        if (!is_user_logged_in()) {
            wp_send_json_error(['message' => 'Vous devez être connecté']);
        }

        $post_id = intval($_POST['post_id']);
        $content = sanitize_textarea_field($_POST['content']);

        if (empty($content) && empty($_FILES['comment_attachments'])) {
            wp_send_json_error(['message' => 'Le commentaire ne peut pas être vide']);
        }

        $user = wp_get_current_user();

        $comment_data = [
            'comment_post_ID' => $post_id,
            'comment_content' => $content,
            'user_id' => $user->ID,
            'comment_author' => $user->display_name,
            'comment_author_email' => $user->user_email,
            'comment_approved' => 1
        ];

        $comment_id = wp_insert_comment($comment_data);

        if (!$comment_id) {
            wp_send_json_error(['message' => 'Erreur lors de l\'ajout du commentaire']);
        }

        // Handle file uploads for comment
        if (!empty($_FILES['comment_attachments'])) {
            require_once(ABSPATH . 'wp-admin/includes/image.php');
            require_once(ABSPATH . 'wp-admin/includes/file.php');
            require_once(ABSPATH . 'wp-admin/includes/media.php');

            $attachment_ids = [];
            $files = $_FILES['comment_attachments'];

            for ($i = 0; $i < count($files['name']); $i++) {
                if ($files['error'][$i] === UPLOAD_ERR_OK) {
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
                update_comment_meta($comment_id, '_vic_comment_attachments', $attachment_ids);
            }
        }

        // Get comment HTML
        $comment = get_comment($comment_id);
        ob_start();
        $this->render_single_comment($comment);
        $comment_html = ob_get_clean();

        wp_send_json_success([
            'comment_html' => $comment_html,
            'comment_id' => $comment_id
        ]);
    }

    /**
     * Handle report comment AJAX
     */
    public function handle_report_comment() {
        check_ajax_referer('vic_nonce', 'nonce');

        if (!is_user_logged_in()) {
            wp_send_json_error(['message' => 'Vous devez être connecté']);
        }

        $comment_id = intval($_POST['comment_id']);
        $user = wp_get_current_user();
        $comment = get_comment($comment_id);

        if (!$comment) {
            wp_send_json_error(['message' => 'Commentaire non trouvé']);
        }

        // Send email to admin
        $admin_email = get_option('admin_email');
        $subject = '[Vision IA] Commentaire signalé #' . $comment_id;
        $message = "Un commentaire a été signalé sur la communauté Vision IA.\n\n";
        $message .= "Signalé par: " . $user->display_name . " (" . $user->user_email . ")\n\n";
        $message .= "Auteur du commentaire: " . $comment->comment_author . "\n";
        $message .= "Contenu du commentaire:\n" . $comment->comment_content . "\n\n";
        $message .= "Lien vers le post: " . get_permalink($comment->comment_post_ID);

        wp_mail($admin_email, $subject, $message);

        wp_send_json_success(['message' => 'Commentaire signalé']);
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
