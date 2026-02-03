<?php
/**
 * Admin Settings for Vision IA Community
 * Partie 21: Paramètres Généraux
 * Partie 22: Activation/Désactivation Fonctionnalités
 */

if (!defined('ABSPATH')) {
    exit;
}

class VIC_Admin_Settings {

    /**
     * Option names
     */
    const OPTION_GENERAL = 'vic_general_settings';
    const OPTION_FEATURES = 'vic_features_settings';

    public function __construct() {
        add_action('admin_menu', [$this, 'add_menu']);
        add_action('admin_init', [$this, 'register_settings']);
        add_action('wp_ajax_vic_save_settings', [$this, 'ajax_save_settings']);
    }

    /**
     * Add admin menu pages
     */
    public function add_menu() {
        // Main settings page
        add_submenu_page(
            'edit.php?post_type=community_post',
            'Paramètres Communauté',
            'Paramètres',
            'manage_options',
            'vic-settings',
            [$this, 'render_settings_page']
        );
    }

    /**
     * Register settings
     */
    public function register_settings() {
        register_setting('vic_settings', self::OPTION_GENERAL);
        register_setting('vic_settings', self::OPTION_FEATURES);
    }

    /**
     * Get default general settings
     */
    public static function get_default_general_settings() {
        return [
            'community_enabled' => true,
            'posts_per_page' => 10,
            'default_sort' => 'recent',
            'show_timestamps' => true,
            'public_like_counts' => true,
            'maintenance_mode' => false,
        ];
    }

    /**
     * Get default feature settings
     */
    public static function get_default_feature_settings() {
        return [
            'likes_enabled' => true,
            'comments_enabled' => true,
            'replies_enabled' => true,
            'attachments_enabled' => true,
            'youtube_embed_enabled' => true,
            'gifs_enabled' => true,
            'emoji_picker_enabled' => true,
            'urls_enabled' => true,
            'pinning_enabled' => true,
            'post_reporting_enabled' => true,
            'comment_reporting_enabled' => true,
            'search_enabled' => true,
            'category_filters_enabled' => true,
            'profile_popup_enabled' => true,
            'user_activity_enabled' => true,
            'levels_system_enabled' => true,
        ];
    }

    /**
     * Get general settings
     */
    public static function get_general_settings() {
        $settings = get_option(self::OPTION_GENERAL, []);
        return wp_parse_args($settings, self::get_default_general_settings());
    }

    /**
     * Get feature settings
     */
    public static function get_feature_settings() {
        $settings = get_option(self::OPTION_FEATURES, []);
        return wp_parse_args($settings, self::get_default_feature_settings());
    }

    /**
     * Check if a feature is enabled
     */
    public static function is_feature_enabled($feature) {
        $settings = self::get_feature_settings();
        return isset($settings[$feature]) ? (bool) $settings[$feature] : true;
    }

    /**
     * Check if community is enabled
     */
    public static function is_community_enabled() {
        $settings = self::get_general_settings();
        return !empty($settings['community_enabled']) && empty($settings['maintenance_mode']);
    }

    /**
     * AJAX save settings
     */
    public function ajax_save_settings() {
        check_ajax_referer('vic_settings_nonce', 'nonce');

        if (!current_user_can('manage_options')) {
            wp_send_json_error('Permission denied');
        }

        $section = sanitize_text_field($_POST['section'] ?? '');
        $settings = isset($_POST['settings']) ? $_POST['settings'] : [];

        if ($section === 'general') {
            $sanitized = [
                'community_enabled' => !empty($settings['community_enabled']),
                'posts_per_page' => absint($settings['posts_per_page'] ?? 10),
                'default_sort' => sanitize_text_field($settings['default_sort'] ?? 'recent'),
                'show_timestamps' => !empty($settings['show_timestamps']),
                'public_like_counts' => !empty($settings['public_like_counts']),
                'maintenance_mode' => !empty($settings['maintenance_mode']),
            ];
            update_option(self::OPTION_GENERAL, $sanitized);
        } elseif ($section === 'features') {
            $sanitized = [];
            $defaults = self::get_default_feature_settings();
            foreach (array_keys($defaults) as $key) {
                $sanitized[$key] = !empty($settings[$key]);
            }
            update_option(self::OPTION_FEATURES, $sanitized);
        }

        wp_send_json_success(['message' => 'Paramètres sauvegardés']);
    }

    /**
     * Render settings page
     */
    public function render_settings_page() {
        $general = self::get_general_settings();
        $features = self::get_feature_settings();
        $active_tab = isset($_GET['tab']) ? sanitize_text_field($_GET['tab']) : 'general';

        ?>
        <div class="wrap vic-settings-wrap">
            <h1>⚙️ Paramètres de la Communauté</h1>

            <style>
                .vic-settings-wrap {
                    max-width: 900px;
                }
                .vic-tabs {
                    display: flex;
                    gap: 0;
                    border-bottom: 1px solid #ddd;
                    margin-bottom: 25px;
                }
                .vic-tab {
                    padding: 12px 20px;
                    background: #f5f5f5;
                    border: 1px solid #ddd;
                    border-bottom: none;
                    margin-right: -1px;
                    cursor: pointer;
                    font-size: 14px;
                    font-weight: 500;
                    color: #555;
                    text-decoration: none;
                    transition: all 0.2s;
                }
                .vic-tab:hover {
                    background: #fff;
                    color: #333;
                }
                .vic-tab.active {
                    background: #fff;
                    color: #2271b1;
                    border-bottom-color: #fff;
                    margin-bottom: -1px;
                }
                .vic-tab-content {
                    display: none;
                    background: #fff;
                    padding: 25px;
                    border: 1px solid #ddd;
                    border-top: none;
                    border-radius: 0 0 8px 8px;
                }
                .vic-tab-content.active {
                    display: block;
                }
                .vic-settings-section {
                    margin-bottom: 30px;
                }
                .vic-settings-section h2 {
                    font-size: 16px;
                    margin: 0 0 15px 0;
                    padding-bottom: 10px;
                    border-bottom: 1px solid #eee;
                    display: flex;
                    align-items: center;
                    gap: 8px;
                }
                .vic-setting-row {
                    display: flex;
                    align-items: center;
                    justify-content: space-between;
                    padding: 15px 0;
                    border-bottom: 1px solid #f0f0f0;
                }
                .vic-setting-row:last-child {
                    border-bottom: none;
                }
                .vic-setting-info {
                    flex: 1;
                }
                .vic-setting-label {
                    font-weight: 500;
                    color: #1d2327;
                    margin-bottom: 4px;
                }
                .vic-setting-description {
                    font-size: 12px;
                    color: #888;
                }
                .vic-toggle {
                    position: relative;
                    width: 50px;
                    height: 26px;
                    flex-shrink: 0;
                }
                .vic-toggle input {
                    opacity: 0;
                    width: 0;
                    height: 0;
                }
                .vic-toggle-slider {
                    position: absolute;
                    cursor: pointer;
                    top: 0;
                    left: 0;
                    right: 0;
                    bottom: 0;
                    background-color: #ccc;
                    transition: .3s;
                    border-radius: 26px;
                }
                .vic-toggle-slider:before {
                    position: absolute;
                    content: "";
                    height: 20px;
                    width: 20px;
                    left: 3px;
                    bottom: 3px;
                    background-color: white;
                    transition: .3s;
                    border-radius: 50%;
                    box-shadow: 0 1px 3px rgba(0,0,0,0.2);
                }
                .vic-toggle input:checked + .vic-toggle-slider {
                    background-color: #10B981;
                }
                .vic-toggle input:checked + .vic-toggle-slider:before {
                    transform: translateX(24px);
                }
                .vic-toggle.warning input:checked + .vic-toggle-slider {
                    background-color: #F59E0B;
                }
                .vic-toggle.danger input:checked + .vic-toggle-slider {
                    background-color: #EF4444;
                }
                .vic-select {
                    padding: 8px 12px;
                    border: 1px solid #ddd;
                    border-radius: 4px;
                    font-size: 14px;
                    min-width: 150px;
                }
                .vic-number-input {
                    width: 80px;
                    padding: 8px 12px;
                    border: 1px solid #ddd;
                    border-radius: 4px;
                    font-size: 14px;
                    text-align: center;
                }
                .vic-features-grid {
                    display: grid;
                    grid-template-columns: repeat(2, 1fr);
                    gap: 0 30px;
                }
                @media (max-width: 782px) {
                    .vic-features-grid {
                        grid-template-columns: 1fr;
                    }
                }
                .vic-save-bar {
                    position: sticky;
                    bottom: 0;
                    background: #fff;
                    padding: 15px 25px;
                    margin: 25px -25px -25px -25px;
                    border-top: 1px solid #ddd;
                    display: flex;
                    align-items: center;
                    gap: 15px;
                }
                .vic-save-status {
                    font-size: 13px;
                    color: #10B981;
                    display: none;
                }
                .vic-save-status.visible {
                    display: inline;
                }
                .vic-maintenance-banner {
                    background: #FEF3C7;
                    border: 1px solid #F59E0B;
                    border-radius: 8px;
                    padding: 15px 20px;
                    margin-bottom: 20px;
                    display: flex;
                    align-items: center;
                    gap: 12px;
                }
                .vic-maintenance-banner.danger {
                    background: #FEE2E2;
                    border-color: #EF4444;
                }
                .vic-feature-category {
                    margin-bottom: 25px;
                }
                .vic-feature-category h3 {
                    font-size: 14px;
                    color: #666;
                    margin: 0 0 10px 0;
                    text-transform: uppercase;
                    letter-spacing: 0.5px;
                }
            </style>

            <?php if (!empty($general['maintenance_mode'])) : ?>
            <div class="vic-maintenance-banner danger">
                <span style="font-size: 24px;">🚧</span>
                <div>
                    <strong>Mode maintenance actif</strong><br>
                    <span style="font-size: 13px; color: #666;">La communauté n'est pas accessible aux utilisateurs.</span>
                </div>
            </div>
            <?php endif; ?>

            <div class="vic-tabs">
                <a href="?post_type=community_post&page=vic-settings&tab=general"
                   class="vic-tab <?php echo $active_tab === 'general' ? 'active' : ''; ?>">
                    ⚙️ Paramètres Généraux
                </a>
                <a href="?post_type=community_post&page=vic-settings&tab=features"
                   class="vic-tab <?php echo $active_tab === 'features' ? 'active' : ''; ?>">
                    🎛️ Fonctionnalités
                </a>
            </div>

            <!-- Tab: Paramètres Généraux -->
            <div class="vic-tab-content <?php echo $active_tab === 'general' ? 'active' : ''; ?>" data-tab="general">
                <form id="vic-general-form">
                    <?php wp_nonce_field('vic_settings_nonce', 'vic_nonce'); ?>

                    <div class="vic-settings-section">
                        <h2>🌐 État de la Communauté</h2>

                        <div class="vic-setting-row">
                            <div class="vic-setting-info">
                                <div class="vic-setting-label">Activer la communauté</div>
                                <div class="vic-setting-description">Active ou désactive complètement le module communautaire</div>
                            </div>
                            <label class="vic-toggle">
                                <input type="checkbox" name="community_enabled" value="1"
                                       <?php checked($general['community_enabled']); ?>>
                                <span class="vic-toggle-slider"></span>
                            </label>
                        </div>

                        <div class="vic-setting-row">
                            <div class="vic-setting-info">
                                <div class="vic-setting-label">Mode maintenance</div>
                                <div class="vic-setting-description">Affiche un message de maintenance aux utilisateurs (sauf admins)</div>
                            </div>
                            <label class="vic-toggle warning">
                                <input type="checkbox" name="maintenance_mode" value="1"
                                       <?php checked($general['maintenance_mode']); ?>>
                                <span class="vic-toggle-slider"></span>
                            </label>
                        </div>
                    </div>

                    <div class="vic-settings-section">
                        <h2>📋 Affichage des Posts</h2>

                        <div class="vic-setting-row">
                            <div class="vic-setting-info">
                                <div class="vic-setting-label">Posts par page</div>
                                <div class="vic-setting-description">Nombre de posts affichés par défaut avant pagination</div>
                            </div>
                            <input type="number" name="posts_per_page" class="vic-number-input"
                                   value="<?php echo esc_attr($general['posts_per_page']); ?>"
                                   min="1" max="50">
                        </div>

                        <div class="vic-setting-row">
                            <div class="vic-setting-info">
                                <div class="vic-setting-label">Ordre de tri par défaut</div>
                                <div class="vic-setting-description">Comment les posts sont triés à l'ouverture</div>
                            </div>
                            <select name="default_sort" class="vic-select">
                                <option value="recent" <?php selected($general['default_sort'], 'recent'); ?>>Plus récents</option>
                                <option value="popular" <?php selected($general['default_sort'], 'popular'); ?>>Plus populaires</option>
                                <option value="most_comments" <?php selected($general['default_sort'], 'most_comments'); ?>>Plus commentés</option>
                                <option value="most_likes" <?php selected($general['default_sort'], 'most_likes'); ?>>Plus likés</option>
                            </select>
                        </div>

                        <div class="vic-setting-row">
                            <div class="vic-setting-info">
                                <div class="vic-setting-label">Afficher les timestamps</div>
                                <div class="vic-setting-description">Affiche "il y a 2 heures" ou la date de publication</div>
                            </div>
                            <label class="vic-toggle">
                                <input type="checkbox" name="show_timestamps" value="1"
                                       <?php checked($general['show_timestamps']); ?>>
                                <span class="vic-toggle-slider"></span>
                            </label>
                        </div>

                        <div class="vic-setting-row">
                            <div class="vic-setting-info">
                                <div class="vic-setting-label">Compteurs de likes publics</div>
                                <div class="vic-setting-description">Affiche le nombre de likes publiquement à tous les visiteurs</div>
                            </div>
                            <label class="vic-toggle">
                                <input type="checkbox" name="public_like_counts" value="1"
                                       <?php checked($general['public_like_counts']); ?>>
                                <span class="vic-toggle-slider"></span>
                            </label>
                        </div>
                    </div>

                    <div class="vic-save-bar">
                        <button type="submit" class="button button-primary button-large">
                            💾 Sauvegarder les paramètres
                        </button>
                        <span class="vic-save-status">✓ Sauvegardé</span>
                    </div>
                </form>
            </div>

            <!-- Tab: Fonctionnalités -->
            <div class="vic-tab-content <?php echo $active_tab === 'features' ? 'active' : ''; ?>" data-tab="features">
                <form id="vic-features-form">
                    <?php wp_nonce_field('vic_settings_nonce', 'vic_nonce'); ?>

                    <p style="color: #666; margin-bottom: 25px;">
                        Activez ou désactivez les fonctionnalités selon vos besoins. Les changements sont appliqués immédiatement.
                    </p>

                    <div class="vic-features-grid">
                        <!-- Interactions -->
                        <div class="vic-feature-category">
                            <h3>❤️ Interactions</h3>

                            <div class="vic-setting-row">
                                <div class="vic-setting-info">
                                    <div class="vic-setting-label">J'aime (Likes)</div>
                                    <div class="vic-setting-description">Permet aux utilisateurs de liker les posts</div>
                                </div>
                                <label class="vic-toggle">
                                    <input type="checkbox" name="likes_enabled" value="1"
                                           <?php checked($features['likes_enabled']); ?>>
                                    <span class="vic-toggle-slider"></span>
                                </label>
                            </div>

                            <div class="vic-setting-row">
                                <div class="vic-setting-info">
                                    <div class="vic-setting-label">Commentaires</div>
                                    <div class="vic-setting-description">Permet de commenter les posts</div>
                                </div>
                                <label class="vic-toggle">
                                    <input type="checkbox" name="comments_enabled" value="1"
                                           <?php checked($features['comments_enabled']); ?>>
                                    <span class="vic-toggle-slider"></span>
                                </label>
                            </div>

                            <div class="vic-setting-row">
                                <div class="vic-setting-info">
                                    <div class="vic-setting-label">Réponses aux commentaires</div>
                                    <div class="vic-setting-description">Permet de répondre aux commentaires (threads)</div>
                                </div>
                                <label class="vic-toggle">
                                    <input type="checkbox" name="replies_enabled" value="1"
                                           <?php checked($features['replies_enabled']); ?>>
                                    <span class="vic-toggle-slider"></span>
                                </label>
                            </div>
                        </div>

                        <!-- Médias -->
                        <div class="vic-feature-category">
                            <h3>📎 Médias</h3>

                            <div class="vic-setting-row">
                                <div class="vic-setting-info">
                                    <div class="vic-setting-label">Pièces jointes</div>
                                    <div class="vic-setting-description">Upload d'images et fichiers</div>
                                </div>
                                <label class="vic-toggle">
                                    <input type="checkbox" name="attachments_enabled" value="1"
                                           <?php checked($features['attachments_enabled']); ?>>
                                    <span class="vic-toggle-slider"></span>
                                </label>
                            </div>

                            <div class="vic-setting-row">
                                <div class="vic-setting-info">
                                    <div class="vic-setting-label">Embed YouTube</div>
                                    <div class="vic-setting-description">Intégration automatique des vidéos YouTube</div>
                                </div>
                                <label class="vic-toggle">
                                    <input type="checkbox" name="youtube_embed_enabled" value="1"
                                           <?php checked($features['youtube_embed_enabled']); ?>>
                                    <span class="vic-toggle-slider"></span>
                                </label>
                            </div>

                            <div class="vic-setting-row">
                                <div class="vic-setting-info">
                                    <div class="vic-setting-label">GIFs</div>
                                    <div class="vic-setting-description">Sélecteur de GIFs (Tenor/Giphy)</div>
                                </div>
                                <label class="vic-toggle">
                                    <input type="checkbox" name="gifs_enabled" value="1"
                                           <?php checked($features['gifs_enabled']); ?>>
                                    <span class="vic-toggle-slider"></span>
                                </label>
                            </div>
                        </div>

                        <!-- Contenu -->
                        <div class="vic-feature-category">
                            <h3>✏️ Contenu</h3>

                            <div class="vic-setting-row">
                                <div class="vic-setting-info">
                                    <div class="vic-setting-label">Sélecteur d'émojis</div>
                                    <div class="vic-setting-description">Picker emoji dans l'éditeur</div>
                                </div>
                                <label class="vic-toggle">
                                    <input type="checkbox" name="emoji_picker_enabled" value="1"
                                           <?php checked($features['emoji_picker_enabled']); ?>>
                                    <span class="vic-toggle-slider"></span>
                                </label>
                            </div>

                            <div class="vic-setting-row">
                                <div class="vic-setting-info">
                                    <div class="vic-setting-label">URLs cliquables</div>
                                    <div class="vic-setting-description">Convertit les URLs en liens automatiquement</div>
                                </div>
                                <label class="vic-toggle">
                                    <input type="checkbox" name="urls_enabled" value="1"
                                           <?php checked($features['urls_enabled']); ?>>
                                    <span class="vic-toggle-slider"></span>
                                </label>
                            </div>

                            <div class="vic-setting-row">
                                <div class="vic-setting-info">
                                    <div class="vic-setting-label">Épinglage de posts</div>
                                    <div class="vic-setting-description">Permet aux admins d'épingler des posts</div>
                                </div>
                                <label class="vic-toggle">
                                    <input type="checkbox" name="pinning_enabled" value="1"
                                           <?php checked($features['pinning_enabled']); ?>>
                                    <span class="vic-toggle-slider"></span>
                                </label>
                            </div>
                        </div>

                        <!-- Modération -->
                        <div class="vic-feature-category">
                            <h3>🛡️ Modération</h3>

                            <div class="vic-setting-row">
                                <div class="vic-setting-info">
                                    <div class="vic-setting-label">Signalement de posts</div>
                                    <div class="vic-setting-description">Bouton de signalement sur les posts</div>
                                </div>
                                <label class="vic-toggle">
                                    <input type="checkbox" name="post_reporting_enabled" value="1"
                                           <?php checked($features['post_reporting_enabled']); ?>>
                                    <span class="vic-toggle-slider"></span>
                                </label>
                            </div>

                            <div class="vic-setting-row">
                                <div class="vic-setting-info">
                                    <div class="vic-setting-label">Signalement de commentaires</div>
                                    <div class="vic-setting-description">Bouton de signalement sur les commentaires</div>
                                </div>
                                <label class="vic-toggle">
                                    <input type="checkbox" name="comment_reporting_enabled" value="1"
                                           <?php checked($features['comment_reporting_enabled']); ?>>
                                    <span class="vic-toggle-slider"></span>
                                </label>
                            </div>
                        </div>

                        <!-- Navigation -->
                        <div class="vic-feature-category">
                            <h3>🔍 Navigation</h3>

                            <div class="vic-setting-row">
                                <div class="vic-setting-info">
                                    <div class="vic-setting-label">Recherche</div>
                                    <div class="vic-setting-description">Barre de recherche dans la communauté</div>
                                </div>
                                <label class="vic-toggle">
                                    <input type="checkbox" name="search_enabled" value="1"
                                           <?php checked($features['search_enabled']); ?>>
                                    <span class="vic-toggle-slider"></span>
                                </label>
                            </div>

                            <div class="vic-setting-row">
                                <div class="vic-setting-info">
                                    <div class="vic-setting-label">Filtres par catégorie</div>
                                    <div class="vic-setting-description">Affiche les filtres de catégories</div>
                                </div>
                                <label class="vic-toggle">
                                    <input type="checkbox" name="category_filters_enabled" value="1"
                                           <?php checked($features['category_filters_enabled']); ?>>
                                    <span class="vic-toggle-slider"></span>
                                </label>
                            </div>
                        </div>

                        <!-- Profils -->
                        <div class="vic-feature-category">
                            <h3>👤 Profils</h3>

                            <div class="vic-setting-row">
                                <div class="vic-setting-info">
                                    <div class="vic-setting-label">Popup profil</div>
                                    <div class="vic-setting-description">Popup au survol du nom d'utilisateur</div>
                                </div>
                                <label class="vic-toggle">
                                    <input type="checkbox" name="profile_popup_enabled" value="1"
                                           <?php checked($features['profile_popup_enabled']); ?>>
                                    <span class="vic-toggle-slider"></span>
                                </label>
                            </div>

                            <div class="vic-setting-row">
                                <div class="vic-setting-info">
                                    <div class="vic-setting-label">Activité utilisateur</div>
                                    <div class="vic-setting-description">Affiche l'activité récente dans le profil</div>
                                </div>
                                <label class="vic-toggle">
                                    <input type="checkbox" name="user_activity_enabled" value="1"
                                           <?php checked($features['user_activity_enabled']); ?>>
                                    <span class="vic-toggle-slider"></span>
                                </label>
                            </div>

                            <div class="vic-setting-row">
                                <div class="vic-setting-info">
                                    <div class="vic-setting-label">Système de niveaux</div>
                                    <div class="vic-setting-description">Badges et progression des utilisateurs</div>
                                </div>
                                <label class="vic-toggle">
                                    <input type="checkbox" name="levels_system_enabled" value="1"
                                           <?php checked($features['levels_system_enabled']); ?>>
                                    <span class="vic-toggle-slider"></span>
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="vic-save-bar">
                        <button type="submit" class="button button-primary button-large">
                            💾 Sauvegarder les fonctionnalités
                        </button>
                        <span class="vic-save-status">✓ Sauvegardé</span>
                    </div>
                </form>
            </div>
        </div>

        <script>
        jQuery(document).ready(function($) {
            // Save general settings
            $('#vic-general-form').on('submit', function(e) {
                e.preventDefault();
                saveSettings('general', $(this));
            });

            // Save feature settings
            $('#vic-features-form').on('submit', function(e) {
                e.preventDefault();
                saveSettings('features', $(this));
            });

            function saveSettings(section, $form) {
                const $btn = $form.find('button[type="submit"]');
                const $status = $form.find('.vic-save-status');
                const originalText = $btn.html();

                $btn.html('⏳ Sauvegarde...').prop('disabled', true);

                const settings = {};
                $form.find('input, select').each(function() {
                    const name = $(this).attr('name');
                    if (!name || name === 'vic_nonce' || name === '_wp_http_referer') return;

                    if ($(this).is(':checkbox')) {
                        settings[name] = $(this).is(':checked') ? 1 : 0;
                    } else {
                        settings[name] = $(this).val();
                    }
                });

                $.post(ajaxurl, {
                    action: 'vic_save_settings',
                    nonce: $form.find('#vic_nonce').val(),
                    section: section,
                    settings: settings
                }, function(response) {
                    $btn.html(originalText).prop('disabled', false);
                    if (response.success) {
                        $status.addClass('visible');
                        setTimeout(() => $status.removeClass('visible'), 3000);
                    } else {
                        alert('Erreur lors de la sauvegarde');
                    }
                }).fail(function() {
                    $btn.html(originalText).prop('disabled', false);
                    alert('Erreur de connexion');
                });
            }
        });
        </script>
        <?php
    }
}

// Initialize
new VIC_Admin_Settings();
