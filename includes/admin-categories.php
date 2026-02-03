<?php
/**
 * Admin Categories Management for Vision IA Community
 * Gestion des catégories avec emoji, couleur, permissions et niveaux PMP
 */

if (!defined('ABSPATH')) {
    exit;
}

class VIC_Admin_Categories {

    public function __construct() {
        add_action('admin_menu', [$this, 'add_menu']);
        add_action('admin_init', [$this, 'register_settings']);
        add_action('wp_ajax_vic_save_category_order', [$this, 'save_category_order']);
        add_action('wp_ajax_vic_delete_category', [$this, 'delete_category']);
        add_action('wp_ajax_vic_toggle_category', [$this, 'toggle_category']);
    }

    /**
     * Add submenu page
     */
    public function add_menu() {
        add_submenu_page(
            'edit.php?post_type=community_post',
            'Gestion des Catégories',
            'Catégories',
            'manage_options',
            'vic-categories-settings',
            [$this, 'render_page']
        );
    }

    /**
     * Register settings
     */
    public function register_settings() {
        register_setting('vic_categories_settings', 'vic_categories_config');
    }

    /**
     * Get default categories
     */
    public static function get_default_categories() {
        return [
            [
                'slug' => 'discussion-generale',
                'name' => 'Discussion générale',
                'emoji' => '💬',
                'color' => '#6B7280',
                'description' => 'Discussions générales de la communauté',
                'roles' => ['administrator', 'editor', 'author', 'contributor', 'subscriber'],
                'pmp_levels' => [], // Tous les niveaux PMP si vide
                'enabled' => true,
                'order' => 1,
            ],
            [
                'slug' => 'besoin-aide',
                'name' => 'Besoin d\'aide',
                'emoji' => '🆘',
                'color' => '#EF4444',
                'description' => 'Posez vos questions et demandez de l\'aide',
                'roles' => ['administrator', 'editor', 'author', 'contributor', 'subscriber'],
                'pmp_levels' => [],
                'enabled' => true,
                'order' => 2,
            ],
            [
                'slug' => 'victoires',
                'name' => 'Victoires',
                'emoji' => '🏆',
                'color' => '#10B981',
                'description' => 'Partagez vos succès et réalisations',
                'roles' => ['administrator', 'editor', 'author', 'contributor', 'subscriber'],
                'pmp_levels' => [],
                'enabled' => true,
                'order' => 3,
            ],
            [
                'slug' => 'annonces',
                'name' => 'Annonces',
                'emoji' => '📢',
                'color' => '#F59E0B',
                'description' => 'Annonces officielles',
                'roles' => ['administrator'],
                'pmp_levels' => [],
                'enabled' => true,
                'order' => 4,
            ],
        ];
    }

    /**
     * Get categories config from options or defaults
     */
    public static function get_categories_config() {
        $config = get_option('vic_categories_config');
        if (!$config || empty($config)) {
            return self::get_default_categories();
        }
        return $config;
    }

    /**
     * Get PMP membership levels
     */
    public static function get_pmp_levels() {
        if (!function_exists('pmpro_getAllLevels')) {
            return [];
        }
        return pmpro_getAllLevels(true, true);
    }

    /**
     * Check if user can post in category
     */
    public static function user_can_post_in_category($category_slug, $user_id = null) {
        if (!$user_id) {
            $user_id = get_current_user_id();
        }

        if (!$user_id) {
            return false;
        }

        $user = get_userdata($user_id);
        if (!$user) {
            return false;
        }

        $categories = self::get_categories_config();

        foreach ($categories as $cat) {
            if ($cat['slug'] === $category_slug) {
                if (!$cat['enabled']) {
                    return false;
                }

                // Check WordPress roles
                $has_role = false;
                foreach ($user->roles as $role) {
                    if (in_array($role, $cat['roles'] ?? [])) {
                        $has_role = true;
                        break;
                    }
                }

                // If no role match and roles are required, check PMP levels
                if (!$has_role && !empty($cat['roles'])) {
                    return false;
                }

                // Check PMP membership levels if specified
                if (!empty($cat['pmp_levels']) && function_exists('pmpro_hasMembershipLevel')) {
                    $has_level = pmpro_hasMembershipLevel($cat['pmp_levels'], $user_id);
                    if (!$has_level) {
                        return false;
                    }
                }

                return true;
            }
        }

        return false;
    }

    /**
     * Get available categories for user
     */
    public static function get_user_categories($user_id = null) {
        if (!$user_id) {
            $user_id = get_current_user_id();
        }

        $categories = self::get_categories_config();
        $available = [];

        foreach ($categories as $cat) {
            if ($cat['enabled'] && self::user_can_post_in_category($cat['slug'], $user_id)) {
                $available[] = $cat;
            }
        }

        // Sort by order
        usort($available, function($a, $b) {
            return ($a['order'] ?? 0) - ($b['order'] ?? 0);
        });

        return $available;
    }

    /**
     * Get all WordPress roles
     */
    private function get_all_roles() {
        global $wp_roles;
        return $wp_roles->get_names();
    }

    /**
     * AJAX: Save category order
     */
    public function save_category_order() {
        check_ajax_referer('vic_categories_nonce', 'nonce');

        if (!current_user_can('manage_options')) {
            wp_send_json_error('Permission denied');
        }

        $order = isset($_POST['order']) ? array_map('sanitize_text_field', $_POST['order']) : [];
        $categories = self::get_categories_config();

        foreach ($categories as &$cat) {
            $position = array_search($cat['slug'], $order);
            if ($position !== false) {
                $cat['order'] = $position + 1;
            }
        }

        update_option('vic_categories_config', $categories);
        wp_send_json_success();
    }

    /**
     * AJAX: Toggle category enabled/disabled
     */
    public function toggle_category() {
        check_ajax_referer('vic_categories_nonce', 'nonce');

        if (!current_user_can('manage_options')) {
            wp_send_json_error('Permission denied');
        }

        $slug = sanitize_text_field($_POST['slug'] ?? '');
        $enabled = $_POST['enabled'] === 'true';

        $categories = self::get_categories_config();

        foreach ($categories as &$cat) {
            if ($cat['slug'] === $slug) {
                $cat['enabled'] = $enabled;
                break;
            }
        }

        update_option('vic_categories_config', $categories);
        wp_send_json_success();
    }

    /**
     * AJAX: Delete category
     */
    public function delete_category() {
        check_ajax_referer('vic_categories_nonce', 'nonce');

        if (!current_user_can('manage_options')) {
            wp_send_json_error('Permission denied');
        }

        $slug = sanitize_text_field($_POST['slug'] ?? '');
        if (empty($slug)) {
            wp_send_json_error('Invalid slug');
        }

        $categories = self::get_categories_config();
        $categories = array_filter($categories, function($cat) use ($slug) {
            return $cat['slug'] !== $slug;
        });

        $categories = array_values($categories);
        update_option('vic_categories_config', $categories);

        // Also delete the WordPress taxonomy term
        $term = get_term_by('slug', $slug, 'community_category');
        if ($term) {
            wp_delete_term($term->term_id, 'community_category');
        }

        wp_send_json_success();
    }

    /**
     * Sync categories with WordPress taxonomy
     */
    public static function sync_taxonomy() {
        $categories = self::get_categories_config();

        foreach ($categories as $cat) {
            if (!$cat['enabled']) {
                continue;
            }

            $term = get_term_by('slug', $cat['slug'], 'community_category');

            if (!$term) {
                wp_insert_term(
                    $cat['name'],
                    'community_category',
                    [
                        'slug' => $cat['slug'],
                        'description' => $cat['description'] ?? '',
                    ]
                );
            } else {
                wp_update_term(
                    $term->term_id,
                    'community_category',
                    [
                        'name' => $cat['name'],
                        'description' => $cat['description'] ?? '',
                    ]
                );
            }
        }
    }

    /**
     * Render admin page
     */
    public function render_page() {
        // Handle form submission
        if (isset($_POST['vic_save_categories']) && check_admin_referer('vic_categories_nonce')) {
            $this->save_categories();
            echo '<div class="notice notice-success"><p>Catégories sauvegardées avec succès !</p></div>';
        }

        // Handle add new category
        if (isset($_POST['vic_add_category']) && check_admin_referer('vic_categories_nonce')) {
            $this->add_category();
            echo '<div class="notice notice-success"><p>Nouvelle catégorie ajoutée !</p></div>';
        }

        // Handle reset
        if (isset($_POST['vic_reset_categories']) && check_admin_referer('vic_categories_nonce')) {
            delete_option('vic_categories_config');
            self::sync_taxonomy();
            echo '<div class="notice notice-info"><p>Catégories réinitialisées aux valeurs par défaut.</p></div>';
        }

        $categories = self::get_categories_config();
        $all_roles = $this->get_all_roles();
        $pmp_levels = self::get_pmp_levels();

        // Sort by order
        usort($categories, function($a, $b) {
            return ($a['order'] ?? 0) - ($b['order'] ?? 0);
        });

        ?>
        <div class="wrap vic-categories-wrap">
            <h1>📁 Gestion des Catégories</h1>
            <p class="description">Gérez les catégories de votre communauté, leurs permissions et leur apparence.</p>

            <style>
                .vic-categories-wrap {
                    max-width: 900px;
                }
                .vic-cat-item {
                    background: #fff;
                    border: 1px solid #ddd;
                    border-radius: 8px;
                    margin-bottom: 10px;
                    overflow: hidden;
                    transition: box-shadow 0.2s;
                }
                .vic-cat-item:hover {
                    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
                }
                .vic-cat-item.sortable-ghost {
                    opacity: 0.4;
                }
                .vic-cat-header {
                    display: flex;
                    align-items: center;
                    padding: 12px 15px;
                    cursor: pointer;
                    background: #fafafa;
                    border-bottom: 1px solid transparent;
                    transition: background 0.2s;
                }
                .vic-cat-header:hover {
                    background: #f5f5f5;
                }
                .vic-cat-item.open .vic-cat-header {
                    border-bottom-color: #ddd;
                    background: #fff;
                }
                .vic-cat-drag {
                    cursor: move;
                    color: #999;
                    font-size: 16px;
                    margin-right: 12px;
                    padding: 5px;
                }
                .vic-cat-drag:hover {
                    color: #666;
                }
                .vic-cat-emoji {
                    font-size: 24px;
                    margin-right: 12px;
                    min-width: 30px;
                    text-align: center;
                }
                .vic-cat-info {
                    flex: 1;
                    display: flex;
                    align-items: center;
                    gap: 10px;
                }
                .vic-cat-name {
                    font-weight: 600;
                    font-size: 14px;
                    color: #1d2327;
                }
                .vic-cat-badge {
                    padding: 3px 10px;
                    border-radius: 12px;
                    font-size: 11px;
                    font-weight: 500;
                    color: white;
                }
                .vic-cat-meta {
                    font-size: 12px;
                    color: #888;
                    margin-left: auto;
                    margin-right: 15px;
                }
                .vic-cat-actions {
                    display: flex;
                    align-items: center;
                    gap: 10px;
                }
                .vic-toggle {
                    position: relative;
                    width: 44px;
                    height: 24px;
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
                    border-radius: 24px;
                }
                .vic-toggle-slider:before {
                    position: absolute;
                    content: "";
                    height: 18px;
                    width: 18px;
                    left: 3px;
                    bottom: 3px;
                    background-color: white;
                    transition: .3s;
                    border-radius: 50%;
                }
                .vic-toggle input:checked + .vic-toggle-slider {
                    background-color: #10B981;
                }
                .vic-toggle input:checked + .vic-toggle-slider:before {
                    transform: translateX(20px);
                }
                .vic-cat-delete {
                    color: #999;
                    cursor: pointer;
                    font-size: 16px;
                    padding: 5px;
                    transition: color 0.2s;
                }
                .vic-cat-delete:hover {
                    color: #dc3545;
                }
                .vic-cat-chevron {
                    color: #999;
                    font-size: 12px;
                    transition: transform 0.2s;
                    margin-left: 5px;
                }
                .vic-cat-item.open .vic-cat-chevron {
                    transform: rotate(180deg);
                }
                .vic-cat-body {
                    display: none;
                    padding: 20px;
                    background: #fff;
                }
                .vic-cat-item.open .vic-cat-body {
                    display: block;
                }
                .vic-form-grid {
                    display: grid;
                    grid-template-columns: repeat(2, 1fr);
                    gap: 15px;
                    margin-bottom: 20px;
                }
                .vic-form-field {
                    display: flex;
                    flex-direction: column;
                }
                .vic-form-field.full-width {
                    grid-column: span 2;
                }
                .vic-form-field label {
                    font-weight: 500;
                    margin-bottom: 5px;
                    font-size: 13px;
                    color: #1d2327;
                }
                .vic-form-field input[type="text"],
                .vic-form-field input[type="color"],
                .vic-form-field textarea {
                    padding: 8px 10px;
                    border: 1px solid #ddd;
                    border-radius: 4px;
                    font-size: 13px;
                }
                .vic-form-field input[type="color"] {
                    height: 38px;
                    padding: 3px;
                    cursor: pointer;
                }
                .vic-form-field input:focus,
                .vic-form-field textarea:focus {
                    border-color: #2271b1;
                    outline: none;
                    box-shadow: 0 0 0 1px #2271b1;
                }
                .vic-permissions-section {
                    background: #f9f9f9;
                    border-radius: 6px;
                    padding: 15px;
                    margin-top: 10px;
                }
                .vic-permissions-section h4 {
                    margin: 0 0 12px 0;
                    font-size: 13px;
                    color: #1d2327;
                    display: flex;
                    align-items: center;
                    gap: 6px;
                }
                .vic-checkbox-grid {
                    display: flex;
                    flex-wrap: wrap;
                    gap: 8px;
                }
                .vic-checkbox-item {
                    display: flex;
                    align-items: center;
                    gap: 5px;
                    background: #fff;
                    padding: 6px 10px;
                    border-radius: 4px;
                    border: 1px solid #e0e0e0;
                    font-size: 12px;
                    cursor: pointer;
                    transition: all 0.2s;
                }
                .vic-checkbox-item:hover {
                    border-color: #2271b1;
                }
                .vic-checkbox-item input:checked + span {
                    font-weight: 500;
                }
                .vic-checkbox-item.pmp-level {
                    background: #fef3c7;
                    border-color: #fcd34d;
                }
                .vic-add-section {
                    background: #f0f6fc;
                    border: 2px dashed #c3dafa;
                    border-radius: 8px;
                    padding: 25px;
                    margin-top: 25px;
                }
                .vic-add-section h3 {
                    margin: 0 0 20px 0;
                    color: #1d2327;
                    font-size: 15px;
                }
                .vic-btn-group {
                    display: flex;
                    gap: 10px;
                    margin-top: 25px;
                }
                .vic-disabled-overlay {
                    opacity: 0.5;
                }
            </style>

            <form method="post" action="">
                <?php wp_nonce_field('vic_categories_nonce'); ?>

                <div id="vic-categories-list">
                    <?php foreach ($categories as $index => $cat) :
                        $slug = $cat['slug'];
                        $is_enabled = $cat['enabled'] ?? true;
                    ?>
                    <div class="vic-cat-item <?php echo !$is_enabled ? 'vic-disabled-overlay' : ''; ?>" data-slug="<?php echo esc_attr($slug); ?>">
                        <div class="vic-cat-header" onclick="toggleCategory(this)">
                            <span class="vic-cat-drag" onclick="event.stopPropagation()" title="Glisser pour réordonner">☰</span>
                            <span class="vic-cat-emoji"><?php echo esc_html($cat['emoji']); ?></span>
                            <div class="vic-cat-info">
                                <span class="vic-cat-name"><?php echo esc_html($cat['name']); ?></span>
                                <span class="vic-cat-badge" style="background-color: <?php echo esc_attr($cat['color']); ?>">
                                    <?php echo esc_html($cat['name']); ?>
                                </span>
                            </div>
                            <span class="vic-cat-meta">
                                <?php
                                $role_count = count($cat['roles'] ?? []);
                                $pmp_count = count($cat['pmp_levels'] ?? []);
                                echo $role_count . ' rôle' . ($role_count > 1 ? 's' : '');
                                if ($pmp_count > 0) {
                                    echo ' • ' . $pmp_count . ' niveau' . ($pmp_count > 1 ? 'x' : '') . ' PMP';
                                }
                                ?>
                            </span>
                            <div class="vic-cat-actions" onclick="event.stopPropagation()">
                                <label class="vic-toggle" title="<?php echo $is_enabled ? 'Désactiver' : 'Activer'; ?>">
                                    <input type="checkbox"
                                           name="cat[<?php echo $index; ?>][enabled]"
                                           value="1"
                                           <?php checked($is_enabled); ?>
                                           onchange="toggleCategoryEnabled('<?php echo esc_js($slug); ?>', this.checked)">
                                    <span class="vic-toggle-slider"></span>
                                </label>
                                <span class="vic-cat-delete" onclick="deleteCategory('<?php echo esc_js($slug); ?>')" title="Supprimer">🗑️</span>
                            </div>
                            <span class="vic-cat-chevron">▼</span>
                        </div>

                        <div class="vic-cat-body">
                            <input type="hidden" name="cat[<?php echo $index; ?>][slug]" value="<?php echo esc_attr($slug); ?>">
                            <input type="hidden" name="cat[<?php echo $index; ?>][order]" value="<?php echo $cat['order'] ?? $index + 1; ?>">

                            <div class="vic-form-grid">
                                <div class="vic-form-field">
                                    <label>Nom de la catégorie</label>
                                    <input type="text"
                                           name="cat[<?php echo $index; ?>][name]"
                                           value="<?php echo esc_attr($cat['name']); ?>"
                                           required>
                                </div>

                                <div class="vic-form-field">
                                    <label>Slug (URL)</label>
                                    <input type="text"
                                           name="cat[<?php echo $index; ?>][slug_display]"
                                           value="<?php echo esc_attr($slug); ?>"
                                           readonly
                                           style="background: #f5f5f5; color: #666;">
                                </div>

                                <div class="vic-form-field">
                                    <label>Emoji / Icône</label>
                                    <input type="text"
                                           name="cat[<?php echo $index; ?>][emoji]"
                                           value="<?php echo esc_attr($cat['emoji']); ?>"
                                           style="font-size: 20px; text-align: center; width: 80px;">
                                </div>

                                <div class="vic-form-field">
                                    <label>Couleur</label>
                                    <input type="color"
                                           name="cat[<?php echo $index; ?>][color]"
                                           value="<?php echo esc_attr($cat['color']); ?>">
                                </div>

                                <div class="vic-form-field full-width">
                                    <label>Description</label>
                                    <input type="text"
                                           name="cat[<?php echo $index; ?>][description]"
                                           value="<?php echo esc_attr($cat['description'] ?? ''); ?>"
                                           placeholder="Description courte de la catégorie">
                                </div>
                            </div>

                            <div class="vic-permissions-section">
                                <h4>🔒 Rôles WordPress autorisés</h4>
                                <div class="vic-checkbox-grid">
                                    <?php foreach ($all_roles as $role_key => $role_name) : ?>
                                    <label class="vic-checkbox-item">
                                        <input type="checkbox"
                                               name="cat[<?php echo $index; ?>][roles][]"
                                               value="<?php echo esc_attr($role_key); ?>"
                                               <?php checked(in_array($role_key, $cat['roles'] ?? [])); ?>>
                                        <span><?php echo esc_html($role_name); ?></span>
                                    </label>
                                    <?php endforeach; ?>
                                </div>
                            </div>

                            <?php if (!empty($pmp_levels)) : ?>
                            <div class="vic-permissions-section" style="margin-top: 15px;">
                                <h4>💳 Niveaux Paid Memberships Pro</h4>
                                <p style="font-size: 12px; color: #666; margin: 0 0 10px 0;">
                                    Si aucun niveau n'est sélectionné, tous les membres avec un abonnement actif peuvent poster.
                                </p>
                                <div class="vic-checkbox-grid">
                                    <?php foreach ($pmp_levels as $level) : ?>
                                    <label class="vic-checkbox-item pmp-level">
                                        <input type="checkbox"
                                               name="cat[<?php echo $index; ?>][pmp_levels][]"
                                               value="<?php echo esc_attr($level->id); ?>"
                                               <?php checked(in_array($level->id, $cat['pmp_levels'] ?? [])); ?>>
                                        <span><?php echo esc_html($level->name); ?></span>
                                    </label>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>

                <!-- Add new category form -->
                <div class="vic-add-section">
                    <h3>➕ Ajouter une nouvelle catégorie</h3>
                    <div class="vic-form-grid">
                        <div class="vic-form-field">
                            <label>Nom de la catégorie *</label>
                            <input type="text" name="new_cat_name" placeholder="Ex: Ressources">
                        </div>
                        <div class="vic-form-field">
                            <label>Slug (URL) *</label>
                            <input type="text" name="new_cat_slug" placeholder="Ex: ressources">
                        </div>
                        <div class="vic-form-field">
                            <label>Emoji</label>
                            <input type="text" name="new_cat_emoji" placeholder="📚" style="font-size: 20px; text-align: center; width: 80px;">
                        </div>
                        <div class="vic-form-field">
                            <label>Couleur</label>
                            <input type="color" name="new_cat_color" value="#6B7280">
                        </div>
                        <div class="vic-form-field full-width">
                            <label>Description</label>
                            <input type="text" name="new_cat_description" placeholder="Description de la catégorie">
                        </div>
                    </div>

                    <div class="vic-permissions-section">
                        <h4>🔒 Rôles WordPress autorisés</h4>
                        <div class="vic-checkbox-grid">
                            <?php foreach ($all_roles as $role_key => $role_name) : ?>
                            <label class="vic-checkbox-item">
                                <input type="checkbox"
                                       name="new_cat_roles[]"
                                       value="<?php echo esc_attr($role_key); ?>"
                                       <?php checked(in_array($role_key, ['administrator', 'editor', 'author', 'contributor', 'subscriber'])); ?>>
                                <span><?php echo esc_html($role_name); ?></span>
                            </label>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <?php if (!empty($pmp_levels)) : ?>
                    <div class="vic-permissions-section" style="margin-top: 15px;">
                        <h4>💳 Niveaux Paid Memberships Pro</h4>
                        <div class="vic-checkbox-grid">
                            <?php foreach ($pmp_levels as $level) : ?>
                            <label class="vic-checkbox-item pmp-level">
                                <input type="checkbox"
                                       name="new_cat_pmp_levels[]"
                                       value="<?php echo esc_attr($level->id); ?>">
                                <span><?php echo esc_html($level->name); ?></span>
                            </label>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <?php endif; ?>

                    <button type="submit" name="vic_add_category" class="button button-secondary" style="margin-top: 15px;">
                        ➕ Ajouter cette catégorie
                    </button>
                </div>

                <div class="vic-btn-group">
                    <button type="submit" name="vic_save_categories" class="button button-primary button-large">
                        💾 Sauvegarder les modifications
                    </button>
                    <button type="submit" name="vic_reset_categories" class="button button-secondary"
                            onclick="return confirm('Êtes-vous sûr de vouloir réinitialiser toutes les catégories aux valeurs par défaut ?');">
                        🔄 Réinitialiser
                    </button>
                </div>
            </form>
        </div>

        <script>
        function toggleCategory(header) {
            const item = header.closest('.vic-cat-item');
            item.classList.toggle('open');
        }

        function toggleCategoryEnabled(slug, enabled) {
            const item = document.querySelector(`[data-slug="${slug}"]`);
            if (enabled) {
                item.classList.remove('vic-disabled-overlay');
            } else {
                item.classList.add('vic-disabled-overlay');
            }

            // AJAX save
            fetch(ajaxurl, {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: new URLSearchParams({
                    action: 'vic_toggle_category',
                    nonce: '<?php echo wp_create_nonce('vic_categories_nonce'); ?>',
                    slug: slug,
                    enabled: enabled
                })
            });
        }

        function deleteCategory(slug) {
            if (!confirm('Êtes-vous sûr de vouloir supprimer cette catégorie ? Les posts associés ne seront pas supprimés.')) {
                return;
            }

            fetch(ajaxurl, {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: new URLSearchParams({
                    action: 'vic_delete_category',
                    nonce: '<?php echo wp_create_nonce('vic_categories_nonce'); ?>',
                    slug: slug
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    document.querySelector(`[data-slug="${slug}"]`).remove();
                } else {
                    alert('Erreur lors de la suppression');
                }
            });
        }

        // Simple drag & drop (without external library)
        document.addEventListener('DOMContentLoaded', function() {
            const list = document.getElementById('vic-categories-list');
            let draggedItem = null;

            list.querySelectorAll('.vic-cat-drag').forEach(handle => {
                const item = handle.closest('.vic-cat-item');

                handle.addEventListener('mousedown', function() {
                    item.setAttribute('draggable', 'true');
                });

                item.addEventListener('dragstart', function(e) {
                    draggedItem = this;
                    this.classList.add('sortable-ghost');
                    e.dataTransfer.effectAllowed = 'move';
                });

                item.addEventListener('dragend', function() {
                    this.classList.remove('sortable-ghost');
                    this.removeAttribute('draggable');
                    draggedItem = null;
                    updateOrder();
                });

                item.addEventListener('dragover', function(e) {
                    e.preventDefault();
                    if (draggedItem && draggedItem !== this) {
                        const rect = this.getBoundingClientRect();
                        const midY = rect.top + rect.height / 2;
                        if (e.clientY < midY) {
                            list.insertBefore(draggedItem, this);
                        } else {
                            list.insertBefore(draggedItem, this.nextSibling);
                        }
                    }
                });
            });

            function updateOrder() {
                const items = list.querySelectorAll('.vic-cat-item');
                items.forEach((item, index) => {
                    const orderInput = item.querySelector('input[name*="[order]"]');
                    if (orderInput) {
                        orderInput.value = index + 1;
                    }
                });
            }
        });
        </script>
        <?php
    }

    /**
     * Save categories from form
     */
    private function save_categories() {
        if (!isset($_POST['cat']) || !is_array($_POST['cat'])) {
            return;
        }

        $categories = [];

        foreach ($_POST['cat'] as $cat_data) {
            $categories[] = [
                'slug' => sanitize_title($cat_data['slug'] ?? ''),
                'name' => sanitize_text_field($cat_data['name'] ?? ''),
                'emoji' => sanitize_text_field($cat_data['emoji'] ?? '📁'),
                'color' => sanitize_hex_color($cat_data['color'] ?? '#6B7280'),
                'description' => sanitize_text_field($cat_data['description'] ?? ''),
                'roles' => array_map('sanitize_text_field', $cat_data['roles'] ?? []),
                'pmp_levels' => array_map('intval', $cat_data['pmp_levels'] ?? []),
                'enabled' => isset($cat_data['enabled']),
                'order' => intval($cat_data['order'] ?? 0),
            ];
        }

        update_option('vic_categories_config', $categories);
        self::sync_taxonomy();
    }

    /**
     * Add new category from form
     */
    private function add_category() {
        $name = sanitize_text_field($_POST['new_cat_name'] ?? '');
        $slug = sanitize_title($_POST['new_cat_slug'] ?? $name);

        if (empty($name) || empty($slug)) {
            echo '<div class="notice notice-error"><p>Le nom et le slug sont requis.</p></div>';
            return;
        }

        $categories = self::get_categories_config();

        // Check if slug already exists
        foreach ($categories as $cat) {
            if ($cat['slug'] === $slug) {
                echo '<div class="notice notice-error"><p>Une catégorie avec ce slug existe déjà.</p></div>';
                return;
            }
        }

        // Get max order
        $max_order = 0;
        foreach ($categories as $cat) {
            if (($cat['order'] ?? 0) > $max_order) {
                $max_order = $cat['order'];
            }
        }

        $categories[] = [
            'slug' => $slug,
            'name' => $name,
            'emoji' => sanitize_text_field($_POST['new_cat_emoji'] ?? '📁'),
            'color' => sanitize_hex_color($_POST['new_cat_color'] ?? '#6B7280'),
            'description' => sanitize_text_field($_POST['new_cat_description'] ?? ''),
            'roles' => array_map('sanitize_text_field', $_POST['new_cat_roles'] ?? ['administrator']),
            'pmp_levels' => array_map('intval', $_POST['new_cat_pmp_levels'] ?? []),
            'enabled' => true,
            'order' => $max_order + 1,
        ];

        update_option('vic_categories_config', $categories);
        self::sync_taxonomy();
    }
}

// Initialize
new VIC_Admin_Categories();
