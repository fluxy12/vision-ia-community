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

// Load Elementor widget if Elementor is active
add_action('elementor/init', function() {
    require_once VIC_PLUGIN_DIR . 'includes/elementor-widget.php';
});

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
        add_action('wp_ajax_vic_delete_post', [$this, 'handle_delete_post']);
        add_action('wp_ajax_vic_edit_post', [$this, 'handle_edit_post']);
        add_action('wp_ajax_vic_get_post_data_for_edit', [$this, 'handle_get_post_data_for_edit']);
        add_action('wp_ajax_vic_search_posts', [$this, 'handle_search_posts']);
        add_action('wp_ajax_nopriv_vic_search_posts', [$this, 'handle_search_posts']);
        add_action('wp_ajax_vic_get_post_modal', [$this, 'handle_get_post_modal']);
        add_action('wp_ajax_nopriv_vic_get_post_modal', [$this, 'handle_get_post_modal']);
        add_action('wp_ajax_vic_add_comment', [$this, 'handle_add_comment']);
        add_action('wp_ajax_vic_like_comment', [$this, 'handle_like_comment']);
        add_action('wp_ajax_vic_reply_comment', [$this, 'handle_reply_comment']);
        add_action('wp_ajax_vic_report_comment', [$this, 'handle_report_comment']);
        add_action('wp_ajax_vic_report_post', [$this, 'handle_report_post']);
        add_action('wp_ajax_vic_delete_comment', [$this, 'handle_delete_comment']);
        add_action('wp_ajax_vic_edit_comment', [$this, 'handle_edit_comment']);
        add_action('wp_ajax_vic_get_comment_data_for_edit', [$this, 'handle_get_comment_data_for_edit']);
        add_action('wp_ajax_vic_get_user_profile', [$this, 'handle_get_user_profile']);
        add_action('wp_ajax_nopriv_vic_get_user_profile', [$this, 'handle_get_user_profile']);
        add_action('wp_ajax_vic_get_user_activity', [$this, 'handle_get_user_activity']);
        add_action('wp_ajax_nopriv_vic_get_user_activity', [$this, 'handle_get_user_activity']);
        add_shortcode('community_feed', [$this, 'render_feed']);
        add_shortcode('community_user_activity', [$this, 'render_user_activity']);
        
        // Hook MasterStudy profile tab
        add_filter('stm_lms_profile_tabs', [$this, 'add_profile_tab']);
        add_action('stm_lms_profile_tab_community', [$this, 'render_profile_tab']);
        
        // Allow additional file types for community uploads
        add_filter('upload_mimes', [$this, 'allow_additional_mimes']);

        // Admin menu for level settings
        add_action('admin_menu', [$this, 'add_admin_menu']);
        add_action('admin_init', [$this, 'register_settings']);

        // Activation hook
        register_activation_hook(__FILE__, [$this, 'activate']);
    }

    /**
     * Add admin menu page
     */
    public function add_admin_menu() {
        add_submenu_page(
            'edit.php?post_type=community_post',
            'Paramètres des Niveaux',
            'Niveaux & Points',
            'manage_options',
            'vic-levels-settings',
            [$this, 'render_admin_page']
        );
    }

    /**
     * Register settings
     */
    public function register_settings() {
        register_setting('vic_levels_settings', 'vic_levels_config');
    }

    /**
     * Get default levels configuration
     */
    public static function get_default_levels() {
        return [
            1 => ['name' => 'Explorateur IA', 'points' => 0, 'emoji' => '🔍', 'color' => '#9CA3AF'],
            2 => ['name' => 'Apprenti Automation', 'points' => 100, 'emoji' => '🤖', 'color' => '#6B7280'],
            3 => ['name' => 'Opérateur IA', 'points' => 300, 'emoji' => '⚙️', 'color' => '#3B82F6'],
            4 => ['name' => 'Créateur d\'Agents', 'points' => 600, 'emoji' => '🛠️', 'color' => '#8B5CF6'],
            5 => ['name' => 'Maître Agent', 'points' => 1200, 'emoji' => '🎯', 'color' => '#EC4899'],
            6 => ['name' => 'Architecte IA', 'points' => 2500, 'emoji' => '🏗️', 'color' => '#F97316'],
            7 => ['name' => 'Expert IA', 'points' => 5000, 'emoji' => '💎', 'color' => '#EF4444'],
            8 => ['name' => 'Génie IA', 'points' => 10000, 'emoji' => '✨', 'color' => '#10B981'],
            9 => ['name' => 'Grand Maître IA', 'points' => 50000, 'emoji' => '🚀', 'color' => '#FBBF24'],
        ];
    }

    /**
     * Get levels configuration (from options or defaults)
     */
    public static function get_levels_config() {
        $config = get_option('vic_levels_config');
        if (!$config || empty($config)) {
            return self::get_default_levels();
        }
        return $config;
    }

    /**
     * Render admin settings page
     */
    public function render_admin_page() {
        // Save settings
        if (isset($_POST['vic_save_levels']) && check_admin_referer('vic_levels_nonce')) {
            $levels = [];
            for ($i = 1; $i <= 9; $i++) {
                $levels[$i] = [
                    'name' => sanitize_text_field($_POST['level_name_' . $i] ?? ''),
                    'points' => intval($_POST['level_points_' . $i] ?? 0),
                    'emoji' => sanitize_text_field($_POST['level_emoji_' . $i] ?? ''),
                    'color' => sanitize_hex_color($_POST['level_color_' . $i] ?? '#9CA3AF'),
                ];
            }
            update_option('vic_levels_config', $levels);

            // Save admin level
            if (isset($_POST['vic_admin_level'])) {
                update_option('vic_admin_level', intval($_POST['vic_admin_level']));
            }

            echo '<div class="notice notice-success"><p>Paramètres sauvegardés avec succès !</p></div>';
        }

        // Reset to defaults
        if (isset($_POST['vic_reset_levels']) && check_admin_referer('vic_levels_nonce')) {
            delete_option('vic_levels_config');
            delete_option('vic_admin_level');
            echo '<div class="notice notice-info"><p>Paramètres réinitialisés aux valeurs par défaut.</p></div>';
        }

        $levels = self::get_levels_config();
        $admin_level = intval(get_option('vic_admin_level', 8));
        ?>
        <div class="wrap">
            <h1>🎮 Paramètres des Niveaux - Vision IA Community</h1>

            <form method="post" action="">
                <?php wp_nonce_field('vic_levels_nonce'); ?>

                <div style="background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); margin-bottom: 20px;">
                    <h2 style="margin-top: 0;">⚙️ Configuration Générale</h2>
                    <table class="form-table">
                        <tr>
                            <th>Niveau automatique des administrateurs</th>
                            <td>
                                <select name="vic_admin_level">
                                    <?php for ($i = 1; $i <= 9; $i++) : ?>
                                        <option value="<?php echo $i; ?>" <?php selected($admin_level, $i); ?>>
                                            Niveau <?php echo $i; ?> - <?php echo esc_html($levels[$i]['name']); ?>
                                        </option>
                                    <?php endfor; ?>
                                </select>
                                <p class="description">Les administrateurs auront automatiquement ce niveau, peu importe leurs points.</p>
                            </td>
                        </tr>
                    </table>
                </div>

                <div style="background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
                    <h2 style="margin-top: 0;">🏆 Configuration des Niveaux</h2>
                    <p style="color: #666; margin-bottom: 20px;">
                        <strong>Points MasterStudy LMS :</strong> course_purchased = 50 pts | lesson_passed = 10 pts | certificate_received = 25 pts | user_registered = 50 pts
                    </p>

                    <table class="widefat striped" style="border-collapse: collapse;">
                        <thead>
                            <tr style="background: #f0f0f1;">
                                <th style="padding: 12px; text-align: center; width: 60px;">Niveau</th>
                                <th style="padding: 12px;">Nom du niveau</th>
                                <th style="padding: 12px; width: 120px;">Points requis</th>
                                <th style="padding: 12px; width: 80px;">Emoji</th>
                                <th style="padding: 12px; width: 100px;">Couleur</th>
                                <th style="padding: 12px; width: 100px;">Aperçu</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php for ($i = 1; $i <= 9; $i++) :
                                $level = $levels[$i];
                            ?>
                            <tr>
                                <td style="padding: 12px; text-align: center; font-weight: bold; font-size: 18px;">
                                    <?php echo $i; ?>
                                </td>
                                <td style="padding: 12px;">
                                    <input type="text"
                                           name="level_name_<?php echo $i; ?>"
                                           value="<?php echo esc_attr($level['name']); ?>"
                                           class="regular-text"
                                           style="width: 100%;">
                                </td>
                                <td style="padding: 12px;">
                                    <input type="number"
                                           name="level_points_<?php echo $i; ?>"
                                           value="<?php echo esc_attr($level['points']); ?>"
                                           min="0"
                                           style="width: 100%;"
                                           <?php echo $i === 1 ? 'readonly' : ''; ?>>
                                    <?php if ($i === 1) : ?>
                                        <small style="color: #666;">Toujours 0</small>
                                    <?php endif; ?>
                                </td>
                                <td style="padding: 12px;">
                                    <input type="text"
                                           name="level_emoji_<?php echo $i; ?>"
                                           value="<?php echo esc_attr($level['emoji']); ?>"
                                           style="width: 60px; text-align: center; font-size: 18px;">
                                </td>
                                <td style="padding: 12px;">
                                    <input type="color"
                                           name="level_color_<?php echo $i; ?>"
                                           value="<?php echo esc_attr($level['color']); ?>"
                                           style="width: 50px; height: 35px; padding: 0; border: none; cursor: pointer;">
                                </td>
                                <td style="padding: 12px;">
                                    <span style="display: inline-flex; align-items: center; gap: 8px;">
                                        <span style="background: <?php echo esc_attr($level['color']); ?>; color: white; width: 24px; height: 24px; border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; font-size: 12px; font-weight: bold;"><?php echo $i; ?></span>
                                        <span><?php echo esc_html($level['emoji']); ?></span>
                                    </span>
                                </td>
                            </tr>
                            <?php endfor; ?>
                        </tbody>
                    </table>
                </div>

                <div style="margin-top: 20px; display: flex; gap: 10px;">
                    <button type="submit" name="vic_save_levels" class="button button-primary button-large">
                        💾 Sauvegarder les paramètres
                    </button>
                    <button type="submit" name="vic_reset_levels" class="button button-secondary button-large" onclick="return confirm('Êtes-vous sûr de vouloir réinitialiser tous les paramètres ?');">
                        🔄 Réinitialiser par défaut
                    </button>
                </div>
            </form>

            <div style="background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); margin-top: 30px;">
                <h2 style="margin-top: 0;">📊 Statistiques des membres</h2>
                <?php
                global $wpdb;
                $table_name = $wpdb->prefix . 'stm_lms_user_points';

                // Get top 10 users by points
                $top_users = $wpdb->get_results("
                    SELECT user_id, SUM(score) as total_points
                    FROM $table_name
                    GROUP BY user_id
                    ORDER BY total_points DESC
                    LIMIT 10
                ");
                ?>

                <?php if ($top_users) : ?>
                <table class="widefat striped">
                    <thead>
                        <tr>
                            <th>Rang</th>
                            <th>Utilisateur</th>
                            <th>Points</th>
                            <th>Niveau</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($top_users as $index => $user_data) :
                            $user = get_user_by('id', $user_data->user_id);
                            if (!$user) continue;
                            $user_level = self::get_user_level($user_data->user_id);
                            $level_config = $levels[$user_level];
                        ?>
                        <tr>
                            <td><strong>#<?php echo $index + 1; ?></strong></td>
                            <td>
                                <?php echo get_avatar($user_data->user_id, 24); ?>
                                <?php echo esc_html($user->display_name); ?>
                            </td>
                            <td><?php echo number_format($user_data->total_points); ?> pts</td>
                            <td>
                                <span style="background: <?php echo esc_attr($level_config['color']); ?>; color: white; padding: 2px 8px; border-radius: 12px; font-size: 12px;">
                                    <?php echo $level_config['emoji']; ?> Niveau <?php echo $user_level; ?> - <?php echo esc_html($level_config['name']); ?>
                                </span>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <?php else : ?>
                    <p>Aucune donnée de points disponible.</p>
                <?php endif; ?>
            </div>
        </div>
        <?php
    }

    /**
     * Get user avatar - MasterStudy LMS compatible
     * Falls back to WordPress avatar if MasterStudy avatar not found
     */
    public static function get_user_avatar($user_id, $size = 40, $class = '') {
        $avatar_url = '';

        // Try MasterStudy LMS avatar first
        $stm_avatar = get_user_meta($user_id, 'stm_lms_user_avatar', true);
        if ($stm_avatar) {
            // Check if it's a URL (MasterStudy stores URL directly) or an attachment ID
            if (filter_var($stm_avatar, FILTER_VALIDATE_URL) || strpos($stm_avatar, 'http') === 0) {
                // It's a URL - use directly (remove query string for clean URL)
                $avatar_url = strtok($stm_avatar, '?');
            } elseif (is_numeric($stm_avatar)) {
                // It's an attachment ID
                $avatar_url = wp_get_attachment_image_url($stm_avatar, 'thumbnail');
            }
        }

        // Fallback: Try BuddyPress/BuddyBoss avatar
        if (empty($avatar_url) && function_exists('bp_core_fetch_avatar')) {
            $avatar_url = bp_core_fetch_avatar([
                'item_id' => $user_id,
                'type' => 'thumb',
                'html' => false
            ]);
        }

        // Final fallback: WordPress default avatar (Gravatar)
        if (empty($avatar_url)) {
            return get_avatar($user_id, $size, '', '', ['class' => $class]);
        }

        $class_attr = $class ? ' class="' . esc_attr($class) . '"' : '';
        return '<img src="' . esc_url($avatar_url) . '" alt="" width="' . intval($size) . '" height="' . intval($size) . '"' . $class_attr . ' style="border-radius: 50%; object-fit: cover;">';
    }

    /**
     * Get user points from MasterStudy LMS
     */
    public static function get_user_points($user_id) {
        global $wpdb;

        // Try from dedicated MasterStudy points table first
        $table_name = $wpdb->prefix . 'stm_lms_user_points';
        if ($wpdb->get_var("SHOW TABLES LIKE '$table_name'") === $table_name) {
            $points = $wpdb->get_var($wpdb->prepare(
                "SELECT SUM(score) FROM $table_name WHERE user_id = %d",
                $user_id
            ));
            if ($points !== null) {
                return intval($points);
            }
        }

        // Fallback: Try user meta
        $points = get_user_meta($user_id, 'stm_lms_points', true);
        return intval($points);
    }

    /**
     * Get user level based on points (Skool-style)
     * Returns level 1-9 based on points thresholds from saved settings
     */
    public static function get_user_level($user_id) {
        // Admins get automatic level from settings
        $admin_level = intval(get_option('vic_admin_level', 8));
        if (user_can($user_id, 'administrator')) {
            return $admin_level;
        }

        $points = self::get_user_points($user_id);
        $levels_config = self::get_levels_config();

        // Sort levels by points descending to check highest first
        $thresholds = [];
        foreach ($levels_config as $level => $config) {
            $thresholds[$level] = $config['points'];
        }
        arsort($thresholds);

        foreach ($thresholds as $level => $threshold) {
            if ($points >= $threshold) {
                return $level;
            }
        }

        return 1;
    }

    /**
     * Get level name from saved settings
     */
    public static function get_level_name($level) {
        $config = self::get_levels_config();
        return isset($config[$level]['name']) ? $config[$level]['name'] : 'Explorateur IA';
    }

    /**
     * Get level emoji from saved settings
     */
    public static function get_level_emoji($level) {
        $config = self::get_levels_config();
        return isset($config[$level]['emoji']) ? $config[$level]['emoji'] : '🔍';
    }

    /**
     * Get level badge color from saved settings
     */
    public static function get_level_color($level) {
        $config = self::get_levels_config();
        return isset($config[$level]['color']) ? $config[$level]['color'] : '#9CA3AF';
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
                    <?php echo self::get_user_avatar(get_current_user_id(), 48); ?>
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
                                <button type="submit" class="vic-btn vic-btn-primary">POSTER</button>
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

            <?php
            // Check if filtering by author
            $filter_author = isset($_GET['vic_author']) ? intval($_GET['vic_author']) : 0;
            if ($filter_author > 0) {
                $filter_user = get_user_by('ID', $filter_author);
                if ($filter_user) {
                    ?>
                    <div class="vic-author-filter-banner">
                        <span>Activités de <strong><?php echo esc_html($filter_user->display_name); ?></strong></span>
                        <a href="<?php echo esc_url(remove_query_arg('vic_author')); ?>" class="vic-clear-filter">✕ Effacer le filtre</a>
                    </div>
                    <?php
                }
            }
            ?>

            <!-- Posts Feed -->
            <div class="vic-feed" id="vic-feed" data-author="<?php echo esc_attr($filter_author); ?>">
                <?php echo $this->get_posts_html($atts['posts_per_page'], 1, '', $filter_author); ?>
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
     * Render user activity page
     */
    public function render_user_activity($atts) {
        $atts = shortcode_atts([
            'user_id' => 0
        ], $atts);

        // Get user ID from URL parameter if not set
        $user_id = $atts['user_id'] ? intval($atts['user_id']) : (isset($_GET['user_id']) ? intval($_GET['user_id']) : 0);

        if (!$user_id) {
            return '<p>Utilisateur non spécifié.</p>';
        }

        $user = get_user_by('ID', $user_id);
        if (!$user) {
            return '<p>Utilisateur non trouvé.</p>';
        }

        // Get user info
        $display_name = $user->display_name;
        $avatar = self::get_user_avatar($user_id, 64);
        $user_level = self::get_user_level($user_id);
        $level_info = [
            'name' => self::get_level_name($user_level),
            'color' => self::get_level_color($user_level),
            'emoji' => self::get_level_emoji($user_level)
        ];

        // Get posts by user
        $posts = get_posts([
            'post_type' => 'community_post',
            'author' => $user_id,
            'posts_per_page' => 20,
            'orderby' => 'date',
            'order' => 'DESC'
        ]);

        // Get comments by user on community posts
        $comments = get_comments([
            'user_id' => $user_id,
            'post_type' => 'community_post',
            'number' => 50,
            'orderby' => 'comment_date',
            'order' => 'DESC'
        ]);

        ob_start();
        ?>
        <div class="vic-user-activity-page">
            <!-- User Header -->
            <div class="vic-activity-header">
                <a href="javascript:history.back()" class="vic-activity-back">← Retour</a>
                <div class="vic-activity-user-info">
                    <?php echo $avatar; ?>
                    <div class="vic-activity-user-details">
                        <h2><?php echo esc_html($display_name); ?></h2>
                        <span class="vic-activity-user-level" style="background: <?php echo esc_attr($level_info['color']); ?>">
                            <?php echo esc_html($level_info['emoji'] . ' ' . $level_info['name']); ?>
                        </span>
                    </div>
                </div>
            </div>

            <!-- Activity Tabs -->
            <div class="vic-activity-tabs">
                <button class="vic-activity-tab active" data-tab="posts">
                    Posts <span class="vic-tab-count"><?php echo count($posts); ?></span>
                </button>
                <button class="vic-activity-tab" data-tab="comments">
                    Commentaires <span class="vic-tab-count"><?php echo count($comments); ?></span>
                </button>
            </div>

            <!-- Posts Tab Content -->
            <div class="vic-activity-content" id="vic-activity-posts">
                <?php if ($posts) : ?>
                    <?php foreach ($posts as $post) :
                        $post_date = human_time_diff(strtotime($post->post_date), current_time('timestamp'));
                        $categories = wp_get_object_terms($post->ID, 'community_category');
                        $category_name = !empty($categories) ? $categories[0]->name : '';
                        $likes = (int) get_post_meta($post->ID, '_vic_likes', true);
                        $comment_count = get_comments_number($post->ID);
                    ?>
                        <div class="vic-activity-item vic-activity-post" data-post-id="<?php echo $post->ID; ?>">
                            <div class="vic-activity-item-header">
                                <?php echo $avatar; ?>
                                <div class="vic-activity-item-meta">
                                    <span class="vic-activity-author"><?php echo esc_html($display_name); ?></span>
                                    <span class="vic-activity-context">a publié un post</span>
                                    <span class="vic-activity-date">· <?php echo $post_date; ?></span>
                                </div>
                                <?php if ($category_name) : ?>
                                    <span class="vic-activity-category"><?php echo esc_html($category_name); ?></span>
                                <?php endif; ?>
                            </div>
                            <div class="vic-activity-item-content">
                                <h3 class="vic-activity-post-title"><?php echo esc_html($post->post_title); ?></h3>
                                <p class="vic-activity-post-excerpt"><?php echo wp_trim_words(strip_tags($post->post_content), 30, '...'); ?></p>
                            </div>
                            <div class="vic-activity-item-stats">
                                <span>❤️ <?php echo $likes; ?></span>
                                <span>💬 <?php echo $comment_count; ?></span>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else : ?>
                    <p class="vic-no-activity">Aucun post pour le moment.</p>
                <?php endif; ?>
            </div>

            <!-- Comments Tab Content -->
            <div class="vic-activity-content" id="vic-activity-comments" style="display: none;">
                <?php if ($comments) : ?>
                    <?php foreach ($comments as $comment) :
                        $comment_date = human_time_diff(strtotime($comment->comment_date), current_time('timestamp'));
                        $parent_post = get_post($comment->comment_post_ID);
                        if (!$parent_post) continue;
                        $post_author = get_user_by('ID', $parent_post->post_author);
                        $post_author_name = $post_author ? $post_author->display_name : 'Utilisateur';
                        $post_author_avatar = $post_author ? $this->get_user_avatar($post_author->ID, 24) : '';
                        $is_own_post = ($parent_post->post_author == $user_id);
                    ?>
                        <div class="vic-activity-item vic-activity-comment" data-post-id="<?php echo $comment->comment_post_ID; ?>" data-comment-id="<?php echo $comment->comment_ID; ?>">
                            <div class="vic-activity-item-header">
                                <?php echo $avatar; ?>
                                <div class="vic-activity-item-meta">
                                    <span class="vic-activity-author"><?php echo esc_html($display_name); ?></span>
                                    <span class="vic-activity-context">
                                        a commenté <?php echo $is_own_post ? 'son post' : 'le post de'; ?>
                                        <?php if (!$is_own_post) : ?>
                                            <strong><?php echo esc_html($post_author_name); ?></strong>
                                        <?php endif; ?>
                                    </span>
                                    <span class="vic-activity-date">· <?php echo $comment_date; ?></span>
                                </div>
                            </div>

                            <!-- Parent Post Preview -->
                            <div class="vic-activity-parent-post">
                                <div class="vic-activity-parent-header">
                                    <?php echo $post_author_avatar; ?>
                                    <span class="vic-activity-parent-author"><?php echo esc_html($post_author_name); ?></span>
                                </div>
                                <h4 class="vic-activity-parent-title"><?php echo esc_html($parent_post->post_title); ?></h4>
                            </div>

                            <!-- Comment Content -->
                            <div class="vic-activity-comment-content">
                                <p><?php echo wp_trim_words(strip_tags($comment->comment_content), 40, '...'); ?></p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else : ?>
                    <p class="vic-no-activity">Aucun commentaire pour le moment.</p>
                <?php endif; ?>
            </div>
        </div>
        <?php
        return ob_get_clean();
    }

    /**
     * Get posts HTML
     */
    public function get_posts_html($posts_per_page = 10, $paged = 1, $category = '', $author_id = 0) {
        $args = [
            'post_type' => 'community_post',
            'posts_per_page' => $posts_per_page,
            'paged' => $paged,
            'post_status' => 'publish'
        ];

        // Filter by author if specified
        if ($author_id > 0) {
            $args['author'] = $author_id;
        }

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

        // Check if author is admin
        $is_admin_post = user_can($author_id, 'administrator');
        $admin_class = $is_admin_post ? ' vic-admin-post' : '';
        ?>
        <article class="vic-post-card<?php echo $admin_class; ?>" data-post-id="<?php echo $post_id; ?>">
            <?php if ($is_pinned) : ?>
                <div class="vic-pinned-badge">📌 Épinglé</div>
            <?php endif; ?>

            <div class="vic-post-body">
                <div class="vic-post-main">
                    <div class="vic-post-header">
                        <div class="vic-author-info">
                            <div class="vic-avatar-with-level vic-profile-hover" data-user-id="<?php echo $author_id; ?>">
                                <?php echo self::get_user_avatar($author_id, 40); ?>
                                <?php $user_level = self::get_user_level($author_id); ?>
                                <span class="vic-level-badge" style="background-color: <?php echo self::get_level_color($user_level); ?>"><?php echo $user_level; ?></span>
                            </div>
                            <div class="vic-author-meta">
                                <div class="vic-author-name-row">
                                    <span class="vic-author-name vic-profile-hover" data-user-id="<?php echo $author_id; ?>"><?php echo esc_html($author_name); ?></span>
                                    <?php if ($user_level >= 7) : ?>
                                    <span class="vic-status-icon">🔥</span>
                                    <?php endif; ?>
                                </div>
                                <span class="vic-post-meta">
                                    <?php echo esc_html($post_date); ?> • <span class="vic-category-tag"><?php echo esc_html($category_name); ?> <?php echo $emoji; ?></span>
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

                <?php
                $current_user_id = get_current_user_id();
                $is_owner = $current_user_id && (int) $author_id === $current_user_id;
                $is_admin = current_user_can('manage_options');
                ?>
                <div class="vic-post-menu">
                    <button class="vic-post-menu-trigger" type="button">•••</button>
                    <div class="vic-post-menu-dropdown">
                        <?php if ($is_owner) : ?>
                        <button type="button" class="vic-edit-post" data-post-id="<?php echo $post_id; ?>">Modifier</button>
                        <?php endif; ?>
                        <?php if ($is_owner || $is_admin) : ?>
                        <button type="button" class="vic-delete-post" data-post-id="<?php echo $post_id; ?>">Supprimer</button>
                        <?php endif; ?>
                        <button type="button" class="vic-copy-post-link" data-post-id="<?php echo $post_id; ?>">Copier le lien</button>
                        <?php if (is_user_logged_in() && !$is_owner) : ?>
                        <button type="button" class="vic-report-post" data-post-id="<?php echo $post_id; ?>">Signaler aux admins</button>
                        <?php endif; ?>
                    </div>
                </div>

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
                            echo $comment->user_id ? self::get_user_avatar($comment->user_id, 24) : get_avatar($comment->comment_author_email, 24);
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

        // Update user's last activity timestamp
        update_user_meta($user_id, 'vic_last_activity', current_time('mysql'));

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

        // Update user's last activity timestamp
        update_user_meta(get_current_user_id(), 'vic_last_activity', current_time('mysql'));

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
        $author_id = isset($_POST['author_id']) ? intval($_POST['author_id']) : 0;

        $html = $this->get_posts_html($posts_per_page, $page, $category, $author_id);

        // Check if there are more posts
        $args = [
            'post_type' => 'community_post',
            'posts_per_page' => $posts_per_page,
            'paged' => $page + 1
        ];

        if ($author_id > 0) {
            $args['author'] = $author_id;
        }

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
     * Handle delete post AJAX
     * Users can delete their own posts, admins can delete any post
     */
    public function handle_delete_post() {
        check_ajax_referer('vic_nonce', 'nonce');

        if (!is_user_logged_in()) {
            wp_send_json_error(['message' => 'Vous devez être connecté']);
        }

        $post_id = intval($_POST['post_id']);
        $post = get_post($post_id);

        if (!$post || $post->post_type !== 'community_post') {
            wp_send_json_error(['message' => 'Post non trouvé']);
        }

        $current_user_id = get_current_user_id();
        $is_admin = current_user_can('manage_options');
        $is_owner = (int) $post->post_author === $current_user_id;

        // Only owner or admin can delete
        if (!$is_owner && !$is_admin) {
            wp_send_json_error(['message' => 'Vous n\'avez pas la permission de supprimer ce post']);
        }

        // Delete attachments
        $attachments = get_post_meta($post_id, '_vic_attachments', true);
        if (!empty($attachments) && is_array($attachments)) {
            foreach ($attachments as $attachment_id) {
                wp_delete_attachment($attachment_id, true);
            }
        }

        // Delete the post
        $deleted = wp_delete_post($post_id, true);

        if ($deleted) {
            wp_send_json_success(['message' => 'Post supprimé avec succès']);
        } else {
            wp_send_json_error(['message' => 'Erreur lors de la suppression']);
        }
    }

    /**
     * Handle edit post AJAX (full version with media support)
     * Only post owner can edit
     */
    public function handle_edit_post() {
        check_ajax_referer('vic_nonce', 'nonce');

        if (!is_user_logged_in()) {
            wp_send_json_error(['message' => 'Vous devez être connecté']);
        }

        $post_id = intval($_POST['post_id']);
        $post = get_post($post_id);

        if (!$post || $post->post_type !== 'community_post') {
            wp_send_json_error(['message' => 'Post non trouvé']);
        }

        $current_user_id = get_current_user_id();
        $is_owner = (int) $post->post_author === $current_user_id;

        // Only owner can edit
        if (!$is_owner) {
            wp_send_json_error(['message' => 'Vous ne pouvez modifier que vos propres posts']);
        }

        $title = sanitize_text_field($_POST['post_title']);
        $content = wp_kses_post($_POST['post_content']);

        if (empty($title) || empty($content)) {
            wp_send_json_error(['message' => 'Le titre et le contenu sont requis']);
        }

        // Update post content
        $updated = wp_update_post([
            'ID' => $post_id,
            'post_title' => $title,
            'post_content' => $content
        ], true);

        if (is_wp_error($updated)) {
            wp_send_json_error(['message' => 'Erreur lors de la modification']);
        }

        // Update URL
        $post_url = isset($_POST['post_url']) ? esc_url_raw($_POST['post_url']) : '';
        if (!empty($post_url)) {
            update_post_meta($post_id, '_vic_post_url', $post_url);
        } else {
            delete_post_meta($post_id, '_vic_post_url');
        }

        // Update GIF
        $post_gif = isset($_POST['post_gif']) ? esc_url_raw($_POST['post_gif']) : '';
        if (!empty($post_gif)) {
            update_post_meta($post_id, '_vic_post_gif', $post_gif);
        } else {
            delete_post_meta($post_id, '_vic_post_gif');
        }

        // Update YouTube
        $post_youtube = isset($_POST['post_youtube']) ? esc_url_raw($_POST['post_youtube']) : '';
        if (!empty($post_youtube)) {
            update_post_meta($post_id, '_vic_post_youtube', $post_youtube);
        } else {
            delete_post_meta($post_id, '_vic_post_youtube');
        }

        // Handle attachments to remove
        $attachments_to_remove = isset($_POST['attachments_to_remove']) ? sanitize_text_field($_POST['attachments_to_remove']) : '';
        if (!empty($attachments_to_remove)) {
            $ids_to_remove = array_map('intval', explode(',', $attachments_to_remove));
            $current_attachments = get_post_meta($post_id, '_vic_attachments', true);
            if (is_array($current_attachments)) {
                // Remove specified attachments
                foreach ($ids_to_remove as $att_id) {
                    // Optionally delete the attachment from media library
                    // wp_delete_attachment($att_id, true);
                    $current_attachments = array_diff($current_attachments, [$att_id]);
                }
                // Update the attachments list
                if (!empty($current_attachments)) {
                    update_post_meta($post_id, '_vic_attachments', array_values($current_attachments));
                } else {
                    delete_post_meta($post_id, '_vic_attachments');
                }
            }
        }

        // Handle new file uploads
        if (!empty($_FILES['new_attachments'])) {
            require_once(ABSPATH . 'wp-admin/includes/image.php');
            require_once(ABSPATH . 'wp-admin/includes/file.php');
            require_once(ABSPATH . 'wp-admin/includes/media.php');

            $current_attachments = get_post_meta($post_id, '_vic_attachments', true);
            if (!is_array($current_attachments)) {
                $current_attachments = [];
            }

            $files = $_FILES['new_attachments'];

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
                        $current_attachments[] = $attachment_id;
                    }
                }
            }

            if (!empty($current_attachments)) {
                update_post_meta($post_id, '_vic_attachments', $current_attachments);
            }
        }

        // Get updated post HTML
        ob_start();
        $this->render_single_post($post_id);
        $post_html = ob_get_clean();

        wp_send_json_success([
            'message' => 'Post modifié avec succès',
            'post_html' => $post_html
        ]);
    }

    /**
     * Handle get post data for edit AJAX
     * Returns all post data including attachments, GIF, YouTube, URL
     */
    public function handle_get_post_data_for_edit() {
        check_ajax_referer('vic_nonce', 'nonce');

        if (!is_user_logged_in()) {
            wp_send_json_error(['message' => 'Vous devez être connecté']);
        }

        $post_id = intval($_POST['post_id']);
        $post = get_post($post_id);

        if (!$post || $post->post_type !== 'community_post') {
            wp_send_json_error(['message' => 'Post non trouvé']);
        }

        $current_user_id = get_current_user_id();
        $is_owner = (int) $post->post_author === $current_user_id;

        // Only owner can get edit data
        if (!$is_owner) {
            wp_send_json_error(['message' => 'Vous ne pouvez modifier que vos propres posts']);
        }

        // Get post content
        $content = $post->post_content;

        // Get URL
        $url = get_post_meta($post_id, '_vic_post_url', true);

        // Get GIF
        $gif = get_post_meta($post_id, '_vic_post_gif', true);

        // Get YouTube
        $youtube = get_post_meta($post_id, '_vic_post_youtube', true);

        // Get attachments
        $attachment_ids = get_post_meta($post_id, '_vic_attachments', true);
        $attachments = [];

        if (!empty($attachment_ids) && is_array($attachment_ids)) {
            foreach ($attachment_ids as $att_id) {
                $att_url = wp_get_attachment_url($att_id);
                $att_type = wp_check_filetype($att_url);
                $mime = get_post_mime_type($att_id);
                $filename = basename($att_url);

                $type = 'file';
                if (strpos($mime, 'image') !== false) {
                    $type = 'image';
                } elseif (strpos($mime, 'video') !== false) {
                    $type = 'video';
                } elseif (strpos($mime, 'audio') !== false) {
                    $type = 'audio';
                }

                $attachments[] = [
                    'id' => $att_id,
                    'url' => $att_url,
                    'type' => $type,
                    'mime' => $mime,
                    'filename' => $filename
                ];
            }
        }

        wp_send_json_success([
            'content' => $content,
            'url' => $url,
            'gif' => $gif,
            'youtube' => $youtube,
            'attachments' => $attachments
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
        <?php
        $current_user_id = get_current_user_id();
        $is_owner = $current_user_id && (int) $author_id === $current_user_id;
        $is_admin = current_user_can('manage_options');
        ?>
        <!-- Modal Header -->
        <div class="vic-modal-header">
            <div class="vic-modal-header-left">
                <div class="vic-avatar-with-level vic-profile-hover" data-user-id="<?php echo $author_id; ?>">
                    <?php echo self::get_user_avatar($author_id, 40); ?>
                    <?php $user_level = self::get_user_level($author_id); ?>
                    <span class="vic-level-badge" style="background-color: <?php echo self::get_level_color($user_level); ?>"><?php echo $user_level; ?></span>
                </div>
                <div class="vic-modal-author-info">
                    <div class="vic-author-name-row">
                        <h3 class="vic-modal-title vic-profile-hover" data-user-id="<?php echo $author_id; ?>"><?php echo esc_html($author_name); ?></h3>
                        <?php if ($user_level >= 7) : ?>
                        <span class="vic-status-icon">🔥</span>
                        <?php endif; ?>
                    </div>
                    <span class="vic-post-meta"><?php echo esc_html($post_date); ?> • <span class="vic-category-tag"><?php echo esc_html($category_name); ?> <?php echo $emoji; ?></span></span>
                </div>
            </div>
            <div class="vic-modal-actions">
                <div class="vic-post-menu vic-modal-post-menu">
                    <button class="vic-post-menu-trigger" type="button">•••</button>
                    <div class="vic-post-menu-dropdown">
                        <?php if ($is_owner) : ?>
                        <button type="button" class="vic-edit-post" data-post-id="<?php echo $post_id; ?>">Modifier</button>
                        <?php endif; ?>
                        <?php if ($is_owner || $is_admin) : ?>
                        <button type="button" class="vic-delete-post" data-post-id="<?php echo $post_id; ?>">Supprimer</button>
                        <?php endif; ?>
                        <button type="button" class="vic-copy-post-link" data-post-id="<?php echo $post_id; ?>">Copier le lien</button>
                        <?php if (is_user_logged_in() && !$is_owner) : ?>
                        <button type="button" class="vic-report-post" data-post-id="<?php echo $post_id; ?>">Signaler aux admins</button>
                        <?php endif; ?>
                    </div>
                </div>
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

                // Parser les GIFs [gif]url[/gif] et les transformer en images (legacy)
                $content = preg_replace_callback('/\[gif\](.*?)\[\/gif\]/i', function($matches) {
                    $gif_url = esc_url($matches[1]);
                    return '<img src="' . $gif_url . '" alt="GIF" class="vic-post-gif">';
                }, $content);

                // Convert URLs to links
                $content = make_clickable($content);
                echo wpautop($content);

                // Display GIF from meta field
                $post_gif = get_post_meta($post_id, '_vic_post_gif', true);
                if (!empty($post_gif)) {
                    echo '<div class="vic-modal-media">';
                    echo '<img src="' . esc_url($post_gif) . '" alt="GIF" class="vic-post-gif">';
                    echo '</div>';
                }

                // YouTube embed from meta field first
                $post_youtube = get_post_meta($post_id, '_vic_post_youtube', true);
                if (!empty($post_youtube) && preg_match('/(?:youtube\.com\/watch\?v=|youtu\.be\/)([a-zA-Z0-9_-]+)/', $post_youtube, $matches)) {
                    echo '<div class="vic-video-embed">';
                    echo '<iframe src="https://www.youtube.com/embed/' . esc_attr($matches[1]) . '" frameborder="0" allowfullscreen></iframe>';
                    echo '</div>';
                }
                // YouTube embed from content (legacy)
                elseif (preg_match('/(?:youtube\.com\/watch\?v=|youtu\.be\/)([a-zA-Z0-9_-]+)/', $post->post_content, $matches)) {
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
                    <?php echo self::get_user_avatar(get_current_user_id(), 32, 'vic-comment-avatar-skool'); ?>
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
        <div class="vic-comment <?php echo $depth > 0 ? 'vic-comment-reply' : ''; ?>" id="comment-<?php echo $comment->comment_ID; ?>" data-comment-id="<?php echo $comment->comment_ID; ?>" data-depth="<?php echo $depth; ?>">
            <div class="vic-comment-avatar-wrapper vic-profile-hover" data-user-id="<?php echo $comment->user_id; ?>">
                <?php echo $comment->user_id ? self::get_user_avatar($comment->user_id, 36, 'vic-comment-avatar') : get_avatar($comment->comment_author_email, 36, '', '', ['class' => 'vic-comment-avatar']); ?>
            </div>
            <div class="vic-comment-body">
                <div class="vic-comment-header">
                    <span class="vic-comment-author vic-profile-hover" data-user-id="<?php echo $comment->user_id; ?>"><?php echo esc_html($comment->comment_author); ?></span>
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
                    <?php
                    $current_user_id = get_current_user_id();
                    $is_comment_owner = $current_user_id && (int) $comment->user_id === $current_user_id;
                    $is_admin = current_user_can('manage_options');
                    ?>
                    <div class="vic-comment-menu">
                        <button class="vic-comment-menu-trigger" type="button">•••</button>
                        <div class="vic-comment-menu-dropdown">
                            <?php if ($is_comment_owner) : ?>
                            <button type="button" class="vic-edit-comment" data-comment-id="<?php echo $comment->comment_ID; ?>">Modifier</button>
                            <?php endif; ?>
                            <?php if ($is_comment_owner || $is_admin) : ?>
                            <button type="button" class="vic-delete-comment" data-comment-id="<?php echo $comment->comment_ID; ?>">Supprimer</button>
                            <?php endif; ?>
                            <button type="button" class="vic-copy-link" data-comment-id="<?php echo $comment->comment_ID; ?>" data-post-id="<?php echo $comment->comment_post_ID; ?>">Copier le lien</button>
                            <?php if (is_user_logged_in() && !$is_comment_owner) : ?>
                            <button type="button" class="vic-report-comment" data-comment-id="<?php echo $comment->comment_ID; ?>">Signaler aux admins</button>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Reply Form (hidden by default) -->
                <?php if ($depth < $max_depth && is_user_logged_in()) : ?>
                <div class="vic-reply-form-wrapper" style="display: none;">
                    <div class="vic-reply-form">
                        <?php echo self::get_user_avatar(get_current_user_id(), 28, 'vic-reply-avatar'); ?>
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

        // Update user's last activity timestamp
        update_user_meta($user_id, 'vic_last_activity', current_time('mysql'));

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

        // Update user's last activity timestamp
        update_user_meta($user->ID, 'vic_last_activity', current_time('mysql'));

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
     * Handle report post AJAX
     */
    public function handle_report_post() {
        check_ajax_referer('vic_nonce', 'nonce');

        if (!is_user_logged_in()) {
            wp_send_json_error(['message' => 'Vous devez être connecté']);
        }

        $post_id = intval($_POST['post_id']);
        $user = wp_get_current_user();
        $post = get_post($post_id);

        if (!$post || $post->post_type !== 'community_post') {
            wp_send_json_error(['message' => 'Post non trouvé']);
        }

        // Send email to admin
        $admin_email = get_option('admin_email');
        $subject = '[Vision IA] Post signalé #' . $post_id;
        $message = "Un post a été signalé sur la communauté Vision IA.\n\n";
        $message .= "Signalé par: " . $user->display_name . " (" . $user->user_email . ")\n\n";
        $message .= "Auteur du post: " . get_the_author_meta('display_name', $post->post_author) . "\n";
        $message .= "Titre du post: " . $post->post_title . "\n";
        $message .= "Contenu du post:\n" . wp_strip_all_tags($post->post_content) . "\n\n";
        $message .= "Lien vers le post: " . get_permalink($post_id);

        wp_mail($admin_email, $subject, $message);

        wp_send_json_success(['message' => 'Post signalé aux administrateurs']);
    }

    /**
     * Handle delete comment AJAX
     * Users can delete their own comments, admins can delete any comment
     */
    public function handle_delete_comment() {
        check_ajax_referer('vic_nonce', 'nonce');

        if (!is_user_logged_in()) {
            wp_send_json_error(['message' => 'Vous devez être connecté']);
        }

        $comment_id = intval($_POST['comment_id']);
        $comment = get_comment($comment_id);

        if (!$comment) {
            wp_send_json_error(['message' => 'Commentaire non trouvé']);
        }

        $current_user_id = get_current_user_id();
        $is_admin = current_user_can('manage_options');
        $is_owner = (int) $comment->user_id === $current_user_id;

        // Only owner or admin can delete
        if (!$is_owner && !$is_admin) {
            wp_send_json_error(['message' => 'Vous n\'avez pas la permission de supprimer ce commentaire']);
        }

        // Delete comment attachments
        $attachments = get_comment_meta($comment_id, '_vic_comment_attachments', true);
        if (!empty($attachments) && is_array($attachments)) {
            foreach ($attachments as $attachment_id) {
                wp_delete_attachment($attachment_id, true);
            }
        }

        // Delete the comment
        $deleted = wp_delete_comment($comment_id, true);

        if ($deleted) {
            wp_send_json_success(['message' => 'Commentaire supprimé avec succès']);
        } else {
            wp_send_json_error(['message' => 'Erreur lors de la suppression']);
        }
    }

    /**
     * Handle edit comment AJAX (with media support)
     * Only comment owner can edit
     */
    public function handle_edit_comment() {
        check_ajax_referer('vic_nonce', 'nonce');

        if (!is_user_logged_in()) {
            wp_send_json_error(['message' => 'Vous devez être connecté']);
        }

        $comment_id = intval($_POST['comment_id']);
        $comment = get_comment($comment_id);

        if (!$comment) {
            wp_send_json_error(['message' => 'Commentaire non trouvé']);
        }

        $current_user_id = get_current_user_id();
        $is_owner = (int) $comment->user_id === $current_user_id;

        // Only owner can edit
        if (!$is_owner) {
            wp_send_json_error(['message' => 'Vous ne pouvez modifier que vos propres commentaires']);
        }

        $content = sanitize_textarea_field($_POST['content']);

        // Allow empty content if there are images
        $has_new_images = !empty($_FILES['comment_attachments']);
        $attachments_to_remove = isset($_POST['attachments_to_remove']) ? sanitize_text_field($_POST['attachments_to_remove']) : '';
        $current_attachments = get_comment_meta($comment_id, '_vic_comment_attachments', true);
        if (!is_array($current_attachments)) {
            $current_attachments = [];
        }

        // Calculate remaining attachments after removal
        $remaining_attachments = $current_attachments;
        if (!empty($attachments_to_remove)) {
            $ids_to_remove = array_map('intval', explode(',', $attachments_to_remove));
            $remaining_attachments = array_diff($current_attachments, $ids_to_remove);
        }

        if (empty($content) && empty($remaining_attachments) && !$has_new_images) {
            wp_send_json_error(['message' => 'Le commentaire ne peut pas être vide']);
        }

        $updated = wp_update_comment([
            'comment_ID' => $comment_id,
            'comment_content' => $content
        ]);

        if ($updated === false) {
            wp_send_json_error(['message' => 'Erreur lors de la modification']);
        }

        // Handle attachments to remove
        if (!empty($attachments_to_remove)) {
            $ids_to_remove = array_map('intval', explode(',', $attachments_to_remove));
            foreach ($ids_to_remove as $att_id) {
                $current_attachments = array_diff($current_attachments, [$att_id]);
            }
            if (!empty($current_attachments)) {
                update_comment_meta($comment_id, '_vic_comment_attachments', array_values($current_attachments));
            } else {
                delete_comment_meta($comment_id, '_vic_comment_attachments');
            }
        }

        // Handle new file uploads
        if (!empty($_FILES['comment_attachments'])) {
            require_once(ABSPATH . 'wp-admin/includes/image.php');
            require_once(ABSPATH . 'wp-admin/includes/file.php');
            require_once(ABSPATH . 'wp-admin/includes/media.php');

            $current_attachments = get_comment_meta($comment_id, '_vic_comment_attachments', true);
            if (!is_array($current_attachments)) {
                $current_attachments = [];
            }

            $files = $_FILES['comment_attachments'];
            $allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
            $max_size = 5 * 1024 * 1024; // 5MB

            for ($i = 0; $i < count($files['name']); $i++) {
                if ($files['error'][$i] === UPLOAD_ERR_OK) {
                    $file_type = $files['type'][$i];
                    if (!in_array($file_type, $allowed_types)) {
                        continue;
                    }
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
                    $attachment_id = media_handle_upload('upload_file', $comment->comment_post_ID);

                    if (!is_wp_error($attachment_id)) {
                        $current_attachments[] = $attachment_id;
                    }
                }
            }

            if (!empty($current_attachments)) {
                update_comment_meta($comment_id, '_vic_comment_attachments', $current_attachments);
            }
        }

        // Get updated comment HTML
        $comment = get_comment($comment_id);

        // Determine depth
        $depth = 0;
        $parent = $comment;
        while ($parent && $parent->comment_parent) {
            $depth++;
            $parent = get_comment($parent->comment_parent);
        }

        ob_start();
        $this->render_single_comment($comment, $depth);
        $comment_html = ob_get_clean();

        wp_send_json_success([
            'message' => 'Commentaire modifié avec succès',
            'comment_html' => $comment_html
        ]);
    }

    /**
     * Handle get comment data for edit AJAX
     */
    public function handle_get_comment_data_for_edit() {
        check_ajax_referer('vic_nonce', 'nonce');

        if (!is_user_logged_in()) {
            wp_send_json_error(['message' => 'Vous devez être connecté']);
        }

        $comment_id = intval($_POST['comment_id']);
        $comment = get_comment($comment_id);

        if (!$comment) {
            wp_send_json_error(['message' => 'Commentaire non trouvé']);
        }

        $current_user_id = get_current_user_id();
        $is_owner = (int) $comment->user_id === $current_user_id;

        if (!$is_owner) {
            wp_send_json_error(['message' => 'Vous ne pouvez modifier que vos propres commentaires']);
        }

        // Get content
        $content = $comment->comment_content;

        // Get attachments
        $attachment_ids = get_comment_meta($comment_id, '_vic_comment_attachments', true);
        $attachments = [];

        if (!empty($attachment_ids) && is_array($attachment_ids)) {
            foreach ($attachment_ids as $att_id) {
                $att_url = wp_get_attachment_url($att_id);
                $filename = basename($att_url);
                $attachments[] = [
                    'id' => $att_id,
                    'url' => $att_url,
                    'filename' => $filename
                ];
            }
        }

        wp_send_json_success([
            'content' => $content,
            'attachments' => $attachments
        ]);
    }

    /**
     * Handle AJAX request for user profile popup
     */
    public function handle_get_user_profile() {
        $user_id = isset($_POST['user_id']) ? intval($_POST['user_id']) : 0;

        if (!$user_id) {
            wp_send_json_error(['message' => 'User ID manquant']);
        }

        $user = get_user_by('id', $user_id);
        if (!$user) {
            wp_send_json_error(['message' => 'Utilisateur non trouvé']);
        }

        // Get user data
        $display_name = $user->display_name;
        $user_level = self::get_user_level($user_id);
        $level_name = self::get_level_name($user_level);
        $level_color = self::get_level_color($user_level);
        $level_emoji = self::get_level_emoji($user_level);
        $points = self::get_user_points($user_id);
        $is_admin = user_can($user_id, 'administrator');

        // Get avatar URL
        $avatar_html = self::get_user_avatar($user_id, 80);

        // Get user bio/description
        $bio = get_user_meta($user_id, 'description', true);
        if (empty($bio)) {
            $bio = get_user_meta($user_id, 'stm_lms_bio', true);
        }

        // Count posts
        $post_count = count_user_posts($user_id, 'community_post');

        // Count comments
        global $wpdb;
        $comment_count = $wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(*) FROM {$wpdb->comments} c
             INNER JOIN {$wpdb->posts} p ON c.comment_post_ID = p.ID
             WHERE c.user_id = %d AND p.post_type = 'community_post'",
            $user_id
        ));

        // Calculate points to next level and progress percentage
        $levels_config = self::get_levels_config();
        $next_level = $user_level + 1;
        $points_to_next = 0;
        $progress_percent = 100; // Default to 100% if max level

        $current_level_points = isset($levels_config[$user_level]) ? $levels_config[$user_level]['points'] : 0;
        $next_level_points = isset($levels_config[$next_level]) ? $levels_config[$next_level]['points'] : 0;

        if (isset($levels_config[$next_level])) {
            $points_to_next = $next_level_points - $points;
            if ($points_to_next < 0) $points_to_next = 0;

            // Calculate progress percentage within current level
            $level_range = $next_level_points - $current_level_points;
            $points_in_level = $points - $current_level_points;
            if ($level_range > 0) {
                $progress_percent = min(100, max(0, ($points_in_level / $level_range) * 100));
            }
        }

        // Get user profile URL (MasterStudy LMS public profile)
        $profile_url = '';

        // Check if user is an instructor or student
        $is_instructor = user_can($user_id, 'stm_lms_instructor') || user_can($user_id, 'administrator');

        // Get the LMS settings for profile pages
        $instructor_page = get_option('stm_lms_instructor_public_page', '');
        $student_page = get_option('stm_lms_student_public_page', '');

        if ($is_instructor && $instructor_page) {
            $profile_url = get_permalink($instructor_page) . $user_id . '/';
        } elseif ($student_page) {
            $profile_url = get_permalink($student_page) . $user_id . '/';
        } else {
            // Fallback: try to construct URL based on common MasterStudy patterns
            $site_url = home_url();
            if ($is_instructor) {
                $profile_url = $site_url . '/instructor-public-account/' . $user_id . '/';
            } else {
                $profile_url = $site_url . '/student-public-account/' . $user_id . '/';
            }
        }

        // Check active status - only show "En ligne" for the current user viewing their own profile
        $is_online = false;
        $last_activity = null;

        // Only mark as "online" if this is the currently logged-in user viewing their own popup
        if ($user_id == get_current_user_id()) {
            $is_online = true;
        }

        // For last activity display, try multiple sources (most recent first)
        $activity_dates = [];

        // Source 1: Our own plugin's activity tracker (most reliable)
        $vic_activity = get_user_meta($user_id, 'vic_last_activity', true);
        if (!empty($vic_activity)) {
            $activity_dates[] = strtotime($vic_activity);
        }

        // Source 2: MasterStudy LMS last activity
        $lms_activity = get_user_meta($user_id, 'stm_lms_last_activity', true);
        if (!empty($lms_activity)) {
            $activity_dates[] = strtotime($lms_activity);
        }

        // Source 3: Check last post date in community
        $last_post = get_posts([
            'post_type' => 'community_post',
            'author' => $user_id,
            'posts_per_page' => 1,
            'orderby' => 'date',
            'order' => 'DESC',
            'fields' => 'ids'
        ]);
        if (!empty($last_post)) {
            $post_date = get_post_field('post_date', $last_post[0]);
            if ($post_date) {
                $activity_dates[] = strtotime($post_date);
            }
        }

        // Source 4: Check last comment date on community posts
        $last_comment = get_comments([
            'user_id' => $user_id,
            'post_type' => 'community_post',
            'number' => 1,
            'orderby' => 'comment_date_gmt',
            'order' => 'DESC'
        ]);
        if (!empty($last_comment)) {
            $activity_dates[] = strtotime($last_comment[0]->comment_date_gmt);
        }

        // Source 5: BuddyPress/BuddyBoss last activity if available
        $bp_last_activity = get_user_meta($user_id, 'last_activity', true);
        if (!empty($bp_last_activity)) {
            $activity_dates[] = strtotime($bp_last_activity);
        }

        // Source 6: Check WooCommerce last order date if available
        if (class_exists('WooCommerce')) {
            $last_order = wc_get_orders([
                'customer_id' => $user_id,
                'limit' => 1,
                'orderby' => 'date',
                'order' => 'DESC',
                'return' => 'ids'
            ]);
            if (!empty($last_order)) {
                $order = wc_get_order($last_order[0]);
                if ($order) {
                    $activity_dates[] = $order->get_date_created()->getTimestamp();
                }
            }
        }

        // Use the most recent activity date found
        if (!empty($activity_dates)) {
            $last_activity = date('Y-m-d H:i:s', max($activity_dates));
        } else {
            // Fallback to registration date only if no activity found
            $last_activity = $user->user_registered;
        }

        // Check if active within last 15 minutes (more realistic "active now")
        $is_active = $is_online || (strtotime($last_activity) > strtotime('-15 minutes'));

        // Format last activity
        if ($is_online) {
            $last_activity_formatted = 'En ligne';
        } else {
            $last_activity_formatted = human_time_diff(strtotime($last_activity), current_time('timestamp'));
        }

        wp_send_json_success([
            'user_id' => $user_id,
            'display_name' => $display_name,
            'avatar_html' => $avatar_html,
            'bio' => wp_trim_words($bio, 15, '...'),
            'level' => $user_level,
            'level_name' => $level_name,
            'level_color' => $level_color,
            'level_emoji' => $level_emoji,
            'points' => $points,
            'points_to_next' => $points_to_next,
            'progress_percent' => round($progress_percent, 1),
            'post_count' => intval($post_count),
            'comment_count' => intval($comment_count),
            'is_admin' => $is_admin,
            'is_active' => $is_active,
            'last_activity' => $last_activity_formatted,
            'profile_url' => $profile_url
        ]);
    }

    /**
     * Handle get user activity AJAX
     */
    public function handle_get_user_activity() {
        check_ajax_referer('vic_nonce', 'nonce');

        $user_id = intval($_POST['user_id']);
        if (!$user_id) {
            wp_send_json_error(['message' => 'ID utilisateur manquant']);
        }

        $user = get_user_by('ID', $user_id);
        if (!$user) {
            wp_send_json_error(['message' => 'Utilisateur non trouvé']);
        }

        // Get user info
        $display_name = $user->display_name;
        $avatar = self::get_user_avatar($user_id, 48);
        $user_level = self::get_user_level($user_id);
        $level_info = [
            'name' => self::get_level_name($user_level),
            'color' => self::get_level_color($user_level),
            'emoji' => self::get_level_emoji($user_level)
        ];

        // Get posts by user
        $posts = get_posts([
            'post_type' => 'community_post',
            'author' => $user_id,
            'posts_per_page' => 20,
            'orderby' => 'date',
            'order' => 'DESC'
        ]);

        // Get comments by user on community posts
        $comments = get_comments([
            'user_id' => $user_id,
            'post_type' => 'community_post',
            'number' => 50,
            'orderby' => 'comment_date',
            'order' => 'DESC'
        ]);

        ob_start();
        ?>
        <button class="vic-activity-modal-close">&times;</button>

        <!-- User Header -->
        <div class="vic-activity-modal-header">
            <?php echo $avatar; ?>
            <div class="vic-activity-modal-user-info">
                <h3><?php echo esc_html($display_name); ?></h3>
                <span class="vic-activity-user-level" style="background: <?php echo esc_attr($level_info['color']); ?>">
                    <?php echo esc_html($level_info['emoji'] . ' ' . $level_info['name']); ?>
                </span>
            </div>
        </div>

        <!-- Activity Tabs -->
        <div class="vic-activity-tabs">
            <button class="vic-activity-tab active" data-tab="posts">
                Posts <span class="vic-tab-count"><?php echo count($posts); ?></span>
            </button>
            <button class="vic-activity-tab" data-tab="comments">
                Commentaires <span class="vic-tab-count"><?php echo count($comments); ?></span>
            </button>
        </div>

        <!-- Posts Tab Content -->
        <div class="vic-activity-content" id="vic-activity-posts">
            <?php if ($posts) : ?>
                <?php foreach ($posts as $post) :
                    $post_date = human_time_diff(strtotime($post->post_date), current_time('timestamp'));
                    $categories = wp_get_object_terms($post->ID, 'community_category');
                    $category_name = !empty($categories) ? $categories[0]->name : '';
                    $likes = (int) get_post_meta($post->ID, '_vic_likes', true);
                    $comment_count = get_comments_number($post->ID);
                ?>
                    <div class="vic-activity-item vic-activity-post" data-post-id="<?php echo $post->ID; ?>">
                        <div class="vic-activity-item-header">
                            <?php echo $avatar; ?>
                            <div class="vic-activity-item-meta">
                                <span class="vic-activity-author"><?php echo esc_html($display_name); ?></span>
                                <span class="vic-activity-context">a publié un post</span>
                                <span class="vic-activity-date">· <?php echo $post_date; ?></span>
                            </div>
                            <?php if ($category_name) : ?>
                                <span class="vic-activity-category"><?php echo esc_html($category_name); ?></span>
                            <?php endif; ?>
                        </div>
                        <div class="vic-activity-item-content">
                            <h4 class="vic-activity-post-title"><?php echo esc_html($post->post_title); ?></h4>
                            <p class="vic-activity-post-excerpt"><?php echo wp_trim_words(strip_tags($post->post_content), 25, '...'); ?></p>
                        </div>
                        <div class="vic-activity-item-stats">
                            <span>❤️ <?php echo $likes; ?></span>
                            <span>💬 <?php echo $comment_count; ?></span>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else : ?>
                <p class="vic-no-activity">Aucun post pour le moment.</p>
            <?php endif; ?>
        </div>

        <!-- Comments Tab Content -->
        <div class="vic-activity-content" id="vic-activity-comments" style="display: none;">
            <?php if ($comments) : ?>
                <?php foreach ($comments as $comment) :
                    $comment_date = human_time_diff(strtotime($comment->comment_date), current_time('timestamp'));
                    $parent_post = get_post($comment->comment_post_ID);
                    if (!$parent_post) continue;
                    $post_author = get_user_by('ID', $parent_post->post_author);
                    $post_author_name = $post_author ? $post_author->display_name : 'Utilisateur';
                    $post_author_avatar = $post_author ? self::get_user_avatar($post_author->ID, 24) : '';
                    $is_own_post = ($parent_post->post_author == $user_id);
                ?>
                    <div class="vic-activity-item vic-activity-comment" data-post-id="<?php echo $comment->comment_post_ID; ?>" data-comment-id="<?php echo $comment->comment_ID; ?>">
                        <div class="vic-activity-item-header">
                            <?php echo $avatar; ?>
                            <div class="vic-activity-item-meta">
                                <span class="vic-activity-author"><?php echo esc_html($display_name); ?></span>
                                <span class="vic-activity-context">
                                    a commenté <?php echo $is_own_post ? 'son post' : 'le post de'; ?>
                                    <?php if (!$is_own_post) : ?>
                                        <strong><?php echo esc_html($post_author_name); ?></strong>
                                    <?php endif; ?>
                                </span>
                                <span class="vic-activity-date">· <?php echo $comment_date; ?></span>
                            </div>
                        </div>

                        <!-- Parent Post Preview -->
                        <div class="vic-activity-parent-post">
                            <div class="vic-activity-parent-header">
                                <?php echo $post_author_avatar; ?>
                                <span class="vic-activity-parent-author"><?php echo esc_html($post_author_name); ?></span>
                            </div>
                            <h4 class="vic-activity-parent-title"><?php echo esc_html($parent_post->post_title); ?></h4>
                        </div>

                        <!-- Comment Content -->
                        <div class="vic-activity-comment-content">
                            <p><?php echo wp_trim_words(strip_tags($comment->comment_content), 35, '...'); ?></p>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else : ?>
                <p class="vic-no-activity">Aucun commentaire pour le moment.</p>
            <?php endif; ?>
        </div>
        <?php

        $html = ob_get_clean();
        wp_send_json_success(['html' => $html]);
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
