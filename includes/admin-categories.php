<?php
/**
 * Admin Categories Management for Vision IA Community
 * Gestion des catégories avec emoji, couleur et permissions
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

                // Check if user has any of the allowed roles
                foreach ($user->roles as $role) {
                    if (in_array($role, $cat['roles'])) {
                        return true;
                    }
                }
                return false;
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

        // Re-index array
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
                // Create term
                wp_insert_term(
                    $cat['name'],
                    'community_category',
                    [
                        'slug' => $cat['slug'],
                        'description' => $cat['description'] ?? '',
                    ]
                );
            } else {
                // Update term
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

        // Sort by order
        usort($categories, function($a, $b) {
            return ($a['order'] ?? 0) - ($b['order'] ?? 0);
        });

        ?>
        <div class="wrap">
            <h1>📁 Gestion des Catégories - Vision IA Community</h1>

            <style>
                .vic-cat-card {
                    background: #fff;
                    border: 1px solid #ddd;
                    border-radius: 8px;
                    padding: 20px;
                    margin-bottom: 15px;
                    position: relative;
                    transition: box-shadow 0.2s;
                }
                .vic-cat-card:hover {
                    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
                }
                .vic-cat-card.sortable-ghost {
                    opacity: 0.4;
                    background: #f0f0f1;
                }
                .vic-cat-header {
                    display: flex;
                    align-items: center;
                    gap: 15px;
                    margin-bottom: 15px;
                    padding-bottom: 15px;
                    border-bottom: 1px solid #eee;
                }
                .vic-cat-drag {
                    cursor: move;
                    color: #999;
                    font-size: 20px;
                }
                .vic-cat-emoji {
                    font-size: 28px;
                    width: 40px;
                    text-align: center;
                }
                .vic-cat-title {
                    flex: 1;
                    font-size: 16px;
                    font-weight: 600;
                }
                .vic-cat-badge {
                    padding: 4px 12px;
                    border-radius: 20px;
                    font-size: 12px;
                    font-weight: 500;
                    color: white;
                }
                .vic-cat-grid {
                    display: grid;
                    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
                    gap: 15px;
                }
                .vic-cat-field label {
                    display: block;
                    font-weight: 500;
                    margin-bottom: 5px;
                    color: #333;
                }
                .vic-cat-field input[type="text"],
                .vic-cat-field input[type="color"],
                .vic-cat-field textarea {
                    width: 100%;
                }
                .vic-cat-field input[type="color"] {
                    height: 40px;
                    padding: 2px;
                    cursor: pointer;
                }
                .vic-roles-grid {
                    display: flex;
                    flex-wrap: wrap;
                    gap: 10px;
                }
                .vic-role-item {
                    display: flex;
                    align-items: center;
                    gap: 5px;
                    background: #f5f5f5;
                    padding: 5px 10px;
                    border-radius: 5px;
                    font-size: 13px;
                }
                .vic-cat-actions {
                    position: absolute;
                    top: 15px;
                    right: 15px;
                    display: flex;
                    gap: 10px;
                }
                .vic-delete-cat {
                    color: #dc3545;
                    cursor: pointer;
                    font-size: 18px;
                }
                .vic-delete-cat:hover {
                    color: #a71d2a;
                }
                .vic-add-cat-form {
                    background: #f8f9fa;
                    border: 2px dashed #ddd;
                    border-radius: 8px;
                    padding: 25px;
                    margin-top: 20px;
                }
                .vic-toggle-switch {
                    position: relative;
                    width: 50px;
                    height: 26px;
                }
                .vic-toggle-switch input {
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
                }
                .vic-toggle-switch input:checked + .vic-toggle-slider {
                    background-color: #10B981;
                }
                .vic-toggle-switch input:checked + .vic-toggle-slider:before {
                    transform: translateX(24px);
                }
            </style>

            <form method="post" action="">
                <?php wp_nonce_field('vic_categories_nonce'); ?>

                <div id="vic-categories-list">
                    <?php foreach ($categories as $index => $cat) :
                        $slug = $cat['slug'];
                    ?>
                    <div class="vic-cat-card" data-slug="<?php echo esc_attr($slug); ?>">
                        <div class="vic-cat-actions">
                            <label class="vic-toggle-switch" title="Activer/Désactiver">
                                <input type="checkbox"
                                       name="cat[<?php echo $index; ?>][enabled]"
                                       value="1"
                                       <?php checked($cat['enabled'] ?? true); ?>>
                                <span class="vic-toggle-slider"></span>
                            </label>
                            <span class="vic-delete-cat" onclick="deleteCategory('<?php echo esc_js($slug); ?>')" title="Supprimer">
                                🗑️
                            </span>
                        </div>

                        <div class="vic-cat-header">
                            <span class="vic-cat-drag" title="Glisser pour réordonner">☰</span>
                            <span class="vic-cat-emoji"><?php echo esc_html($cat['emoji']); ?></span>
                            <span class="vic-cat-title"><?php echo esc_html($cat['name']); ?></span>
                            <span class="vic-cat-badge" style="background-color: <?php echo esc_attr($cat['color']); ?>">
                                <?php echo esc_html($cat['name']); ?>
                            </span>
                        </div>

                        <input type="hidden" name="cat[<?php echo $index; ?>][slug]" value="<?php echo esc_attr($slug); ?>">
                        <input type="hidden" name="cat[<?php echo $index; ?>][order]" value="<?php echo $cat['order'] ?? $index + 1; ?>">

                        <div class="vic-cat-grid">
                            <div class="vic-cat-field">
                                <label>Nom de la catégorie</label>
                                <input type="text"
                                       name="cat[<?php echo $index; ?>][name]"
                                       value="<?php echo esc_attr($cat['name']); ?>"
                                       required>
                            </div>

                            <div class="vic-cat-field">
                                <label>Emoji / Icône</label>
                                <input type="text"
                                       name="cat[<?php echo $index; ?>][emoji]"
                                       value="<?php echo esc_attr($cat['emoji']); ?>"
                                       style="font-size: 20px; text-align: center;">
                            </div>

                            <div class="vic-cat-field">
                                <label>Couleur</label>
                                <input type="color"
                                       name="cat[<?php echo $index; ?>][color]"
                                       value="<?php echo esc_attr($cat['color']); ?>">
                            </div>

                            <div class="vic-cat-field">
                                <label>Description</label>
                                <input type="text"
                                       name="cat[<?php echo $index; ?>][description]"
                                       value="<?php echo esc_attr($cat['description'] ?? ''); ?>">
                            </div>
                        </div>

                        <div class="vic-cat-field" style="margin-top: 15px;">
                            <label>🔒 Rôles autorisés à poster dans cette catégorie</label>
                            <div class="vic-roles-grid">
                                <?php foreach ($all_roles as $role_key => $role_name) : ?>
                                <label class="vic-role-item">
                                    <input type="checkbox"
                                           name="cat[<?php echo $index; ?>][roles][]"
                                           value="<?php echo esc_attr($role_key); ?>"
                                           <?php checked(in_array($role_key, $cat['roles'] ?? [])); ?>>
                                    <?php echo esc_html($role_name); ?>
                                </label>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>

                <!-- Add new category form -->
                <div class="vic-add-cat-form">
                    <h3 style="margin-top: 0;">➕ Ajouter une nouvelle catégorie</h3>
                    <div class="vic-cat-grid">
                        <div class="vic-cat-field">
                            <label>Nom de la catégorie</label>
                            <input type="text" name="new_cat_name" placeholder="Ex: Ressources">
                        </div>
                        <div class="vic-cat-field">
                            <label>Slug (URL)</label>
                            <input type="text" name="new_cat_slug" placeholder="Ex: ressources">
                        </div>
                        <div class="vic-cat-field">
                            <label>Emoji</label>
                            <input type="text" name="new_cat_emoji" placeholder="📚" style="font-size: 20px; text-align: center;">
                        </div>
                        <div class="vic-cat-field">
                            <label>Couleur</label>
                            <input type="color" name="new_cat_color" value="#6B7280">
                        </div>
                    </div>
                    <div class="vic-cat-field" style="margin-top: 15px;">
                        <label>Description</label>
                        <input type="text" name="new_cat_description" placeholder="Description de la catégorie" style="width: 100%;">
                    </div>
                    <div class="vic-cat-field" style="margin-top: 15px;">
                        <label>🔒 Rôles autorisés</label>
                        <div class="vic-roles-grid">
                            <?php foreach ($all_roles as $role_key => $role_name) : ?>
                            <label class="vic-role-item">
                                <input type="checkbox"
                                       name="new_cat_roles[]"
                                       value="<?php echo esc_attr($role_key); ?>"
                                       <?php checked($role_key !== 'administrator'); ?>>
                                <?php echo esc_html($role_name); ?>
                            </label>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <button type="submit" name="vic_add_category" class="button button-secondary" style="margin-top: 15px;">
                        ➕ Ajouter cette catégorie
                    </button>
                </div>

                <div style="margin-top: 25px; display: flex; gap: 10px;">
                    <button type="submit" name="vic_save_categories" class="button button-primary button-large">
                        💾 Sauvegarder les modifications
                    </button>
                    <button type="submit" name="vic_reset_categories" class="button button-secondary button-large"
                            onclick="return confirm('Êtes-vous sûr de vouloir réinitialiser toutes les catégories ?');">
                        🔄 Réinitialiser par défaut
                    </button>
                </div>
            </form>
        </div>

        <script>
        // Delete category
        function deleteCategory(slug) {
            if (!confirm('Êtes-vous sûr de vouloir supprimer cette catégorie ?')) {
                return;
            }

            fetch(ajaxurl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
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

        // Make categories sortable (optional - requires Sortable.js)
        document.addEventListener('DOMContentLoaded', function() {
            const list = document.getElementById('vic-categories-list');
            if (typeof Sortable !== 'undefined') {
                new Sortable(list, {
                    handle: '.vic-cat-drag',
                    animation: 150,
                    ghostClass: 'sortable-ghost',
                    onEnd: function() {
                        // Update order inputs
                        const cards = list.querySelectorAll('.vic-cat-card');
                        cards.forEach((card, index) => {
                            const orderInput = card.querySelector('input[name*="[order]"]');
                            if (orderInput) {
                                orderInput.value = index + 1;
                            }
                        });
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
                'emoji' => sanitize_text_field($cat_data['emoji'] ?? ''),
                'color' => sanitize_hex_color($cat_data['color'] ?? '#6B7280'),
                'description' => sanitize_text_field($cat_data['description'] ?? ''),
                'roles' => array_map('sanitize_text_field', $cat_data['roles'] ?? []),
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
            'enabled' => true,
            'order' => $max_order + 1,
        ];

        update_option('vic_categories_config', $categories);
        self::sync_taxonomy();
    }
}

// Initialize
new VIC_Admin_Categories();
