<?php
/**
 * Plugin Name: Hype Marketer
 * Plugin URI: https://hypemarketer.in/
 * Description: High-performance Sales CRM & Leads Management Spreadsheet by Hype Marketer. Real-time lead capture from Hero Moving Calculator, Contact Page, Popups, and forms with WhatsApp dispatch, priority sorting, status tracking, spam/trash management, and CSV spreadsheet export.
 * Version: 2.1.0
 * Author: Hype Marketer
 * Author URI: https://hypemarketer.in/
 * Text Domain: hype-marketer
 */

if (!defined('ABSPATH')) {
    exit;
}

define('HYPE_CRM_VERSION', '2.1.0');
define('HYPE_CRM_FILE', __FILE__);
define('HYPE_CRM_DIR', plugin_dir_path(__FILE__));
define('HYPE_CRM_URL', plugin_dir_url(__FILE__));

/**
 * 1. Database Table Creation & Schema Upgrades
 */
register_activation_hook(__FILE__, 'hype_crm_create_table');

function hype_crm_create_table() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'amar_leads';
    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE IF NOT EXISTS $table_name (
        id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
        created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
        customer_name varchar(255) DEFAULT '',
        phone varchar(50) NOT NULL,
        email varchar(100) DEFAULT '',
        move_size varchar(100) DEFAULT '',
        moving_from varchar(255) DEFAULT '',
        moving_to varchar(255) DEFAULT '',
        move_date varchar(100) DEFAULT '',
        service_type varchar(100) DEFAULT '',
        estimated_price varchar(100) DEFAULT '',
        address text DEFAULT '',
        message text DEFAULT '',
        source varchar(100) DEFAULT 'Hero Moving Calculator',
        status varchar(50) NOT NULL DEFAULT 'new',
        priority varchar(20) NOT NULL DEFAULT 'normal',
        is_spam tinyint(1) NOT NULL DEFAULT 0,
        is_trash tinyint(1) NOT NULL DEFAULT 0,
        notes text DEFAULT '',
        ip_address varchar(100) DEFAULT '',
        PRIMARY KEY (id),
        KEY phone (phone),
        KEY status (status),
        KEY priority (priority),
        KEY is_spam (is_spam),
        KEY is_trash (is_trash),
        KEY created_at (created_at)
    ) $charset_collate;";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);

    // Ensure columns exist on upgrade
    $cols = $wpdb->get_col("DESCRIBE $table_name", 0);
    if (!in_array('priority', $cols)) {
        $wpdb->query("ALTER TABLE $table_name ADD COLUMN priority varchar(20) NOT NULL DEFAULT 'normal'");
    }
    if (!in_array('is_spam', $cols)) {
        $wpdb->query("ALTER TABLE $table_name ADD COLUMN is_spam tinyint(1) NOT NULL DEFAULT 0");
    }
    if (!in_array('is_trash', $cols)) {
        $wpdb->query("ALTER TABLE $table_name ADD COLUMN is_trash tinyint(1) NOT NULL DEFAULT 0");
    }
}

add_action('admin_init', function() {
    if (get_option('hype_crm_db_checked') !== HYPE_CRM_VERSION) {
        hype_crm_create_table();
        update_option('hype_crm_db_checked', HYPE_CRM_VERSION);
    }
});

/**
 * 2. Admin Menu — Named "Hype Marketer" with Live Badge
 */
add_action('admin_menu', 'hype_crm_register_admin_menu');

function hype_crm_register_admin_menu() {
    // Legacy redirect fallback
    if (isset($_GET['page']) && $_GET['page'] === 'amar-sales-crm') {
        wp_safe_redirect(admin_url('admin.php?page=hype-marketer'));
        exit;
    }

    global $wpdb;
    $table_name = $wpdb->prefix . 'amar_leads';

    $new_count = 0;
    if ($wpdb->get_var("SHOW TABLES LIKE '$table_name'") === $table_name) {
        $new_count = (int)$wpdb->get_var("SELECT COUNT(*) FROM $table_name WHERE status = 'new' AND is_trash = 0 AND is_spam = 0");
    }

    $badge = $new_count > 0 ? sprintf(' <span class="update-plugins count-%d" style="background:#ee1d23;color:#fff;border-radius:10px;padding:2px 7px;font-size:11px;font-weight:700;"><span class="plugin-count">%d</span></span>', $new_count, $new_count) : '';

    add_menu_page(
        'Hype Marketer Leads',
        'Hype Marketer' . $badge,
        'manage_options',
        'hype-marketer',
        'hype_crm_render_spreadsheet',
        'dashicons-spreadsheet',
        3
    );
}

/**
 * 3. Frontend Lead Capture: Hero Calculator AJAX & WhatsApp Dispatch
 */
add_action('wp_ajax_amar_save_hero_lead', 'hype_crm_save_hero_lead_ajax');
add_action('wp_ajax_nopriv_amar_save_hero_lead', 'hype_crm_save_hero_lead_ajax');
add_action('wp_ajax_hype_save_hero_lead', 'hype_crm_save_hero_lead_ajax');
add_action('wp_ajax_nopriv_hype_save_hero_lead', 'hype_crm_save_hero_lead_ajax');

function hype_crm_save_hero_lead_ajax() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'amar_leads';

    $phone = isset($_POST['phone']) ? sanitize_text_field($_POST['phone']) : '';
    $clean_phone = preg_replace('/[^0-9]/', '', $phone);
    if (empty($phone) || strlen($clean_phone) < 10) {
        wp_send_json_error(array('message' => 'Please enter a valid 10-digit mobile number.'));
    }

    $name = isset($_POST['name']) ? sanitize_text_field($_POST['name']) : '';
    $from = isset($_POST['moving_from']) ? sanitize_text_field($_POST['moving_from']) : '';
    if (empty($name)) {
        $name = !empty($from) ? "Customer ({$from})" : "Website Visitor";
    }

    $data = array(
        'customer_name'   => $name,
        'phone'           => $phone,
        'moving_from'     => $from,
        'moving_to'       => isset($_POST['moving_to']) ? sanitize_text_field($_POST['moving_to']) : '',
        'move_size'       => isset($_POST['move_size']) ? sanitize_text_field($_POST['move_size']) : '1 BHK',
        'move_date'       => isset($_POST['move_date']) ? sanitize_text_field($_POST['move_date']) : '',
        'estimated_price' => isset($_POST['estimated_price']) ? sanitize_text_field($_POST['estimated_price']) : '',
        'source'          => 'Hero Moving Calculator',
        'status'          => 'new',
        'priority'        => 'normal',
        'is_spam'         => 0,
        'is_trash'        => 0,
        'ip_address'      => sanitize_text_field($_SERVER['REMOTE_ADDR'] ?? ''),
        'created_at'      => current_time('mysql'),
    );

    $inserted = $wpdb->insert($table_name, $data);

    if ($inserted) {
        wp_send_json_success(array(
            'message' => 'Lead saved successfully',
            'lead_id' => $wpdb->insert_id
        ));
    } else {
        wp_send_json_error(array('message' => 'Database error: ' . $wpdb->last_error));
    }
}

/**
 * 4. Frontend Lead Capture: ALL WPForms Submissions (Contact Page & Popups)
 */
add_action('wpforms_process_complete', 'hype_crm_capture_wpforms', 10, 4);

function hype_crm_capture_wpforms($fields, $entry, $form_data, $entry_id) {
    global $wpdb;
    $table_name = $wpdb->prefix . 'amar_leads';

    $first_name = '';
    $last_name  = '';
    $email      = '';
    $phone      = '';
    $address    = '';
    $service    = '';
    $message    = '';

    foreach ($fields as $field_id => $field) {
        $name = strtolower($field['name'] ?? '');
        $val  = $field['value'] ?? '';

        if (strpos($name, 'name') !== false) {
            if (is_array($val)) {
                $first_name = $val['first'] ?? '';
                $last_name  = $val['last'] ?? '';
            } else {
                $first_name = $val;
            }
        } elseif (strpos($name, 'email') !== false) {
            $email = sanitize_email($val);
        } elseif (strpos($name, 'phone') !== false || strpos($name, 'mobile') !== false) {
            $phone = sanitize_text_field($val);
        } elseif (strpos($name, 'address') !== false) {
            if (is_array($val)) {
                $address = trim(($val['address1'] ?? '') . ', ' . ($val['city'] ?? '') . ', ' . ($val['state'] ?? ''));
            } else {
                $address = sanitize_textarea_field($val);
            }
        } elseif (strpos($name, 'service') !== false) {
            $service = sanitize_text_field($val);
        } elseif (strpos($name, 'message') !== false) {
            $message = sanitize_textarea_field($val);
        }
    }

    $customer_name = trim($first_name . ' ' . $last_name);
    if (empty($phone) && !empty($fields[3]['value'])) {
        $phone = sanitize_text_field($fields[3]['value']);
    }

    $form_title = $form_data['settings']['form_title'] ?? ('WPForms #' . intval($form_data['id'] ?? 0));
    $source = 'Contact Form (' . $form_title . ')';

    if (!empty($phone) || !empty($email)) {
        $wpdb->insert($table_name, array(
            'customer_name' => $customer_name,
            'phone'         => $phone,
            'email'         => $email,
            'address'       => $address,
            'service_type'  => $service,
            'message'       => $message,
            'source'        => $source,
            'status'        => 'new',
            'priority'      => 'normal',
            'is_spam'       => 0,
            'is_trash'      => 0,
            'ip_address'    => sanitize_text_field($_SERVER['REMOTE_ADDR'] ?? ''),
            'created_at'    => current_time('mysql'),
        ));
    }
}

/**
 * 5. Inject Frontend Script for Hero Calculator & WhatsApp Interception
 */
add_action('wp_footer', 'hype_crm_inject_frontend_script', 999);

function hype_crm_inject_frontend_script() {
    ?>
    <script>
    (function() {
        function initHypeHeroCrm() {
            const heroForm = document.getElementById('amarHeroEstimateForm');
            if (!heroForm) return;

            // Strip legacy inline onsubmit
            heroForm.removeAttribute('onsubmit');
            heroForm.onsubmit = null;

            // Size selection state (No pricing logic)
            const sizeButtons = heroForm.querySelectorAll('.amar-size-btn');
            sizeButtons.forEach(function(btn) {
                btn.addEventListener('click', function(e) {
                    e.preventDefault();
                    sizeButtons.forEach(function(b) {
                        b.classList.remove('active');
                        b.style.background = '#f8fafc';
                        b.style.borderColor = '#e2e8f0';
                        b.style.color = '#475569';
                    });
                    this.classList.add('active');
                    this.style.background = '#fff0f0';
                    this.style.borderColor = '#ee1d23';
                    this.style.color = '#ee1d23';
                });
            });

            const submitBtn = heroForm.querySelector('button:not(.amar-size-btn)') || heroForm.querySelector('button:last-of-type');
            let isSubmitting = false;

            function doSubmit(e) {
                if (e) {
                    e.preventDefault();
                    e.stopPropagation();
                }
                if (isSubmitting) return false;

                const inputs = heroForm.querySelectorAll('input');
                let nameInput = '';
                let fromInput = '';
                let toInput = '';
                let phoneInput = '';
                let dateInput = '';

                inputs.forEach(function(inp) {
                    const ph = (inp.getAttribute('placeholder') || '').toLowerCase();
                    const type = (inp.getAttribute('type') || '').toLowerCase();
                    const val = inp.value.trim();

                    if (ph.includes('name') || ph.includes('rahul')) {
                        nameInput = val;
                    } else if (ph.includes('from') || ph.includes('andheri')) {
                        fromInput = val;
                    } else if (ph.includes('to') || ph.includes('powai')) {
                        toInput = val;
                    } else if (ph.includes('mobile') || ph.includes('phone') || type === 'tel') {
                        phoneInput = val;
                    } else if (type === 'date' || ph.includes('date')) {
                        dateInput = val;
                    }
                });

                if (!nameInput && inputs[0] && inputs.length >= 5) nameInput = inputs[0].value.trim();
                if (!fromInput) fromInput = (inputs.length >= 5 ? inputs[1] : inputs[0]) ? (inputs.length >= 5 ? inputs[1] : inputs[0]).value.trim() : '';
                if (!toInput) toInput = (inputs.length >= 5 ? inputs[2] : inputs[1]) ? (inputs.length >= 5 ? inputs[2] : inputs[1]).value.trim() : '';
                if (!phoneInput) phoneInput = (inputs.length >= 5 ? inputs[3] : inputs[2]) ? (inputs.length >= 5 ? inputs[3] : inputs[2]).value.trim() : '';
                if (!dateInput) dateInput = (inputs.length >= 5 ? inputs[4] : inputs[3]) ? (inputs.length >= 5 ? inputs[4] : inputs[3]).value.trim() : '';

                const cleanPhone = phoneInput.replace(/\D/g, '');
                if (!cleanPhone || cleanPhone.length < 10) {
                    alert('Please enter a valid 10-digit mobile number.');
                    return false;
                }

                const activeBtn = heroForm.querySelector('.amar-size-btn.active');
                const selectedSize = activeBtn ? (activeBtn.getAttribute('data-size') || activeBtn.textContent.trim()) : '1 BHK';
                const estimatedPrice = 'Free Survey & Custom Quote';

                isSubmitting = true;

                if (submitBtn) {
                    submitBtn.disabled = true;
                    submitBtn.innerHTML = '<span><i class="fas fa-spinner fa-spin"></i> Booking Survey...</span>';
                }

                const customerDisplayName = nameInput || (fromInput ? (fromInput + ' (Doorstep Survey)') : 'Website Visitor');

                const formData = new FormData();
                formData.append('action', 'amar_save_hero_lead');
                formData.append('name', customerDisplayName);
                formData.append('phone', phoneInput);
                formData.append('moving_from', fromInput);
                formData.append('moving_to', toInput);
                formData.append('move_size', selectedSize);
                formData.append('move_date', dateInput);
                formData.append('estimated_price', estimatedPrice);

                let waMsg = "Hello Amar Packers! I would like to book a free doorstep survey for moving in Mumbai.%0A%0A";
                waMsg += "*Customer Details:*%0A";
                if (nameInput) waMsg += "• *Name:* " + encodeURIComponent(nameInput) + "%0A";
                waMsg += "• *Mobile:* " + encodeURIComponent(phoneInput) + "%0A";
                waMsg += "• *Move Size:* " + encodeURIComponent(selectedSize) + "%0A";
                if (fromInput) waMsg += "• *From:* " + encodeURIComponent(fromInput) + "%0A";
                if (toInput) waMsg += "• *To:* " + encodeURIComponent(toInput) + "%0A";
                if (dateInput) waMsg += "• *Move Date:* " + encodeURIComponent(dateInput) + "%0A";
                waMsg += "• *Requirement:* Free Doorstep Survey & Custom Quote%0A%0A";
                waMsg += "Please confirm survey coordinator slot.";
                const waUrl = "https://wa.me/918286864002?text=" + waMsg;
                const ajaxUrl = '<?php echo esc_url(admin_url('admin-ajax.php')); ?>';

                let redirected = false;
                function sendToWhatsApp() {
                    if (redirected) return;
                    redirected = true;
                    if (submitBtn) {
                        submitBtn.innerHTML = '<span><i class="fas fa-check"></i> Saved! Opening WhatsApp...</span>';
                    }
                    setTimeout(function() {
                        window.location.href = waUrl;
                    }, 300);
                }

                try {
                    fetch(ajaxUrl, {
                        method: 'POST',
                        body: formData,
                        keepalive: true
                    })
                    .then(function(res) { return res.json(); })
                    .then(function(data) {
                        sendToWhatsApp();
                    })
                    .catch(function(err) {
                        sendToWhatsApp();
                    });
                } catch(e) {
                    sendToWhatsApp();
                }

                setTimeout(sendToWhatsApp, 800);
                return false;
            }

            heroForm.onsubmit = doSubmit;
            heroForm.addEventListener('submit', doSubmit, true);
            if (submitBtn) {
                submitBtn.addEventListener('click', doSubmit, true);
            }
        }

        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', initHypeHeroCrm);
        } else {
            initHypeHeroCrm();
        }
    })();
    </script>
    <?php
}

/**
 * 6. Admin AJAX Handlers (Status, Priority, Spam, Trash, Notes, Bulk Actions, Manual Add)
 */
add_action('wp_ajax_hype_update_status', 'hype_ajax_update_status');
function hype_ajax_update_status() {
    check_ajax_referer('hype_crm_nonce', 'security');
    if (!current_user_can('manage_options')) wp_send_json_error();

    global $wpdb;
    $table_name = $wpdb->prefix . 'amar_leads';

    $id = intval($_POST['lead_id'] ?? 0);
    $status = sanitize_text_field($_POST['status'] ?? 'new');

    $update_data = array('status' => $status);
    if ($status === 'spam') {
        $update_data['is_spam'] = 1;
    } elseif ($status === 'trash') {
        $update_data['is_trash'] = 1;
    } else {
        $update_data['is_spam'] = 0;
        $update_data['is_trash'] = 0;
    }

    $wpdb->update($table_name, $update_data, array('id' => $id));
    wp_send_json_success(array('status' => $status));
}

add_action('wp_ajax_hype_update_priority', 'hype_ajax_update_priority');
function hype_ajax_update_priority() {
    check_ajax_referer('hype_crm_nonce', 'security');
    if (!current_user_can('manage_options')) wp_send_json_error();

    global $wpdb;
    $table_name = $wpdb->prefix . 'amar_leads';

    $id = intval($_POST['lead_id'] ?? 0);
    $priority = sanitize_text_field($_POST['priority'] ?? 'normal');

    $wpdb->update($table_name, array('priority' => $priority), array('id' => $id));
    wp_send_json_success(array('priority' => $priority));
}

add_action('wp_ajax_hype_toggle_spam', 'hype_ajax_toggle_spam');
function hype_ajax_toggle_spam() {
    check_ajax_referer('hype_crm_nonce', 'security');
    if (!current_user_can('manage_options')) wp_send_json_error();

    global $wpdb;
    $table_name = $wpdb->prefix . 'amar_leads';

    $id = intval($_POST['lead_id'] ?? 0);
    $is_spam = intval($_POST['is_spam'] ?? 1);

    $update_data = array(
        'is_spam' => $is_spam,
        'status'  => $is_spam ? 'spam' : 'new'
    );

    $wpdb->update($table_name, $update_data, array('id' => $id));
    wp_send_json_success(array('is_spam' => $is_spam));
}

add_action('wp_ajax_hype_move_to_trash', 'hype_ajax_move_to_trash');
function hype_ajax_move_to_trash() {
    check_ajax_referer('hype_crm_nonce', 'security');
    if (!current_user_can('manage_options')) wp_send_json_error();

    global $wpdb;
    $table_name = $wpdb->prefix . 'amar_leads';

    $id = intval($_POST['lead_id'] ?? 0);
    $wpdb->update($table_name, array('is_trash' => 1, 'status' => 'trash'), array('id' => $id));
    wp_send_json_success();
}

add_action('wp_ajax_hype_restore_trash', 'hype_ajax_restore_trash');
function hype_ajax_restore_trash() {
    check_ajax_referer('hype_crm_nonce', 'security');
    if (!current_user_can('manage_options')) wp_send_json_error();

    global $wpdb;
    $table_name = $wpdb->prefix . 'amar_leads';

    $id = intval($_POST['lead_id'] ?? 0);
    $wpdb->update($table_name, array('is_trash' => 0, 'is_spam' => 0, 'status' => 'new'), array('id' => $id));
    wp_send_json_success();
}

add_action('wp_ajax_hype_delete_permanent', 'hype_ajax_delete_permanent');
function hype_ajax_delete_permanent() {
    check_ajax_referer('hype_crm_nonce', 'security');
    if (!current_user_can('manage_options')) wp_send_json_error();

    global $wpdb;
    $table_name = $wpdb->prefix . 'amar_leads';

    $id = intval($_POST['lead_id'] ?? 0);
    $wpdb->delete($table_name, array('id' => $id));
    wp_send_json_success();
}

add_action('wp_ajax_hype_save_notes', 'hype_ajax_save_notes');
function hype_ajax_save_notes() {
    check_ajax_referer('hype_crm_nonce', 'security');
    if (!current_user_can('manage_options')) wp_send_json_error();

    global $wpdb;
    $table_name = $wpdb->prefix . 'amar_leads';

    $id = intval($_POST['lead_id'] ?? 0);
    $notes = sanitize_textarea_field($_POST['notes'] ?? '');

    $wpdb->update($table_name, array('notes' => $notes), array('id' => $id));
    wp_send_json_success(array('notes' => $notes));
}

add_action('wp_ajax_hype_bulk_action', 'hype_ajax_bulk_action');
function hype_ajax_bulk_action() {
    check_ajax_referer('hype_crm_nonce', 'security');
    if (!current_user_can('manage_options')) wp_send_json_error();

    global $wpdb;
    $table_name = $wpdb->prefix . 'amar_leads';

    $ids = isset($_POST['lead_ids']) ? array_map('intval', (array)$_POST['lead_ids']) : array();
    $bulk_action = sanitize_text_field($_POST['bulk_action'] ?? '');

    if (empty($ids) || empty($bulk_action)) {
        wp_send_json_error(array('message' => 'No items selected'));
    }

    $id_placeholders = implode(',', array_fill(0, count($ids), '%d'));

    if ($bulk_action === 'trash') {
        $wpdb->query($wpdb->prepare("UPDATE $table_name SET is_trash = 1, status = 'trash' WHERE id IN ($id_placeholders)", $ids));
    } elseif ($bulk_action === 'restore') {
        $wpdb->query($wpdb->prepare("UPDATE $table_name SET is_trash = 0, is_spam = 0, status = 'new' WHERE id IN ($id_placeholders)", $ids));
    } elseif ($bulk_action === 'spam') {
        $wpdb->query($wpdb->prepare("UPDATE $table_name SET is_spam = 1, status = 'spam' WHERE id IN ($id_placeholders)", $ids));
    } elseif ($bulk_action === 'priority_urgent') {
        $wpdb->query($wpdb->prepare("UPDATE $table_name SET priority = 'urgent' WHERE id IN ($id_placeholders)", $ids));
    } elseif ($bulk_action === 'priority_high') {
        $wpdb->query($wpdb->prepare("UPDATE $table_name SET priority = 'high' WHERE id IN ($id_placeholders)", $ids));
    } elseif ($bulk_action === 'mark_contacted') {
        $wpdb->query($wpdb->prepare("UPDATE $table_name SET status = 'contacted' WHERE id IN ($id_placeholders)", $ids));
    } elseif ($bulk_action === 'delete') {
        $wpdb->query($wpdb->prepare("DELETE FROM $table_name WHERE id IN ($id_placeholders)", $ids));
    }

    wp_send_json_success();
}

add_action('wp_ajax_hype_add_manual_lead', 'hype_ajax_add_manual_lead');
function hype_ajax_add_manual_lead() {
    check_ajax_referer('hype_crm_nonce', 'security');
    if (!current_user_can('manage_options')) wp_send_json_error();

    global $wpdb;
    $table_name = $wpdb->prefix . 'amar_leads';

    $phone = sanitize_text_field($_POST['phone'] ?? '');
    if (empty($phone)) {
        wp_send_json_error(array('message' => 'Phone number is required'));
    }

    $inserted = $wpdb->insert($table_name, array(
        'customer_name'   => sanitize_text_field($_POST['customer_name'] ?? 'Direct Call / Walk-in'),
        'phone'           => $phone,
        'email'           => sanitize_email($_POST['email'] ?? ''),
        'moving_from'     => sanitize_text_field($_POST['moving_from'] ?? ''),
        'moving_to'       => sanitize_text_field($_POST['moving_to'] ?? ''),
        'move_size'       => sanitize_text_field($_POST['move_size'] ?? ''),
        'move_date'       => sanitize_text_field($_POST['move_date'] ?? ''),
        'service_type'    => sanitize_text_field($_POST['service_type'] ?? 'Household Shifting'),
        'estimated_price' => sanitize_text_field($_POST['estimated_price'] ?? ''),
        'source'          => 'Manual Entry (Staff)',
        'status'          => sanitize_text_field($_POST['status'] ?? 'new'),
        'priority'        => sanitize_text_field($_POST['priority'] ?? 'normal'),
        'notes'           => sanitize_textarea_field($_POST['notes'] ?? ''),
        'created_at'      => current_time('mysql'),
    ));

    if ($inserted) {
        wp_send_json_success();
    } else {
        wp_send_error(array('message' => $wpdb->last_error));
    }
}

/**
 * 7. Export Spreadsheet to CSV
 */
add_action('admin_init', 'hype_crm_export_csv');

function hype_crm_export_csv() {
    if (!isset($_GET['action']) || $_GET['action'] !== 'hype_export_csv') {
        return;
    }
    if (!current_user_can('manage_options')) {
        wp_die('Unauthorized');
    }

    global $wpdb;
    $table_name = $wpdb->prefix . 'amar_leads';

    $tab = sanitize_text_field($_GET['tab'] ?? 'active');
    $where = array('1=1');
    if ($tab === 'trash') {
        $where[] = "(is_trash = 1 OR status = 'trash')";
    } elseif ($tab === 'spam') {
        $where[] = "(is_spam = 1 OR status = 'spam')";
    } elseif ($tab === 'new') {
        $where[] = "is_trash = 0 AND is_spam = 0 AND status = 'new'";
    } elseif ($tab === 'priority') {
        $where[] = "is_trash = 0 AND is_spam = 0 AND (priority = 'urgent' OR priority = 'high')";
    } elseif ($tab === 'converted') {
        $where[] = "is_trash = 0 AND is_spam = 0 AND status = 'converted'";
    } elseif ($tab === 'no_longer_needed') {
        $where[] = "is_trash = 0 AND is_spam = 0 AND status = 'no_longer_needed'";
    } else {
        $where[] = "is_trash = 0 AND is_spam = 0";
    }

    $where_sql = implode(' AND ', $where);
    $leads = $wpdb->get_results("SELECT * FROM $table_name WHERE $where_sql ORDER BY id DESC", ARRAY_A);

    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename=Hype-Marketer-Leads-' . date('Y-m-d-His') . '.csv');

    $output = fopen('php://output', 'w');
    fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));

    fputcsv($output, array(
        'Lead ID',
        'Date & Time',
        'Priority',
        'Customer Name',
        'Phone Number',
        'Email Address',
        'Move Size',
        'Moving From',
        'Moving To',
        'Move Date',
        'Service Type',
        'Estimated Price',
        'Address',
        'Message',
        'Source',
        'Status',
        'Staff Notes'
    ));

    if (!empty($leads)) {
        foreach ($leads as $l) {
            fputcsv($output, array(
                $l['id'],
                $l['created_at'],
                strtoupper($l['priority'] ?? 'normal'),
                $l['customer_name'],
                $l['phone'],
                $l['email'],
                $l['move_size'],
                $l['moving_from'],
                $l['moving_to'],
                $l['move_date'],
                $l['service_type'],
                $l['estimated_price'],
                $l['address'],
                $l['message'],
                $l['source'],
                ucfirst($l['status']),
                $l['notes']
            ));
        }
    }
    fclose($output);
    exit;
}

/**
 * 8. Render Modern Executive Spreadsheet UI
 */
function hype_crm_render_spreadsheet() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'amar_leads';

    // Counts for KPIs & Tabs
    $total_leads = (int)$wpdb->get_var("SELECT COUNT(*) FROM $table_name");
    $count_today = (int)$wpdb->get_var("SELECT COUNT(*) FROM $table_name WHERE DATE(created_at) = CURDATE()");
    $count_active = (int)$wpdb->get_var("SELECT COUNT(*) FROM $table_name WHERE is_trash = 0 AND is_spam = 0");
    $count_new = (int)$wpdb->get_var("SELECT COUNT(*) FROM $table_name WHERE is_trash = 0 AND is_spam = 0 AND status = 'new'");
    $count_priority = (int)$wpdb->get_var("SELECT COUNT(*) FROM $table_name WHERE is_trash = 0 AND is_spam = 0 AND (priority = 'urgent' OR priority = 'high')");
    $count_converted = (int)$wpdb->get_var("SELECT COUNT(*) FROM $table_name WHERE is_trash = 0 AND is_spam = 0 AND status = 'converted'");
    $count_no_longer_needed = (int)$wpdb->get_var("SELECT COUNT(*) FROM $table_name WHERE is_trash = 0 AND is_spam = 0 AND status = 'no_longer_needed'");
    $count_spam = (int)$wpdb->get_var("SELECT COUNT(*) FROM $table_name WHERE is_spam = 1 OR status = 'spam'");
    $count_trash = (int)$wpdb->get_var("SELECT COUNT(*) FROM $table_name WHERE is_trash = 1 OR status = 'trash'");

    $conversion_rate = $count_active > 0 ? round(($count_converted / $count_active) * 100, 1) : 0;

    // Active Tab
    $tab = sanitize_text_field($_GET['tab'] ?? 'active');

    // Filters
    $time_filter = sanitize_text_field($_GET['time_filter'] ?? 'all');
    $priority_filter = sanitize_text_field($_GET['priority_filter'] ?? '');
    $search_query = sanitize_text_field($_GET['s_lead'] ?? '');
    $sort_by = sanitize_text_field($_GET['sort_by'] ?? 'newest');

    $where = array('1=1');

    // Tab Filtering
    if ($tab === 'trash') {
        $where[] = "(is_trash = 1 OR status = 'trash')";
    } elseif ($tab === 'spam') {
        $where[] = "(is_spam = 1 OR status = 'spam')";
    } elseif ($tab === 'new') {
        $where[] = "is_trash = 0 AND is_spam = 0 AND status = 'new'";
    } elseif ($tab === 'priority') {
        $where[] = "is_trash = 0 AND is_spam = 0 AND (priority = 'urgent' OR priority = 'high')";
    } elseif ($tab === 'converted') {
        $where[] = "is_trash = 0 AND is_spam = 0 AND status = 'converted'";
    } elseif ($tab === 'no_longer_needed') {
        $where[] = "is_trash = 0 AND is_spam = 0 AND status = 'no_longer_needed'";
    } else {
        $where[] = "is_trash = 0 AND is_spam = 0";
    }

    // Time / Age Filtering
    if ($time_filter === 'today') {
        $where[] = "DATE(created_at) = CURDATE()";
    } elseif ($time_filter === '7days') {
        $where[] = "created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)";
    } elseif ($time_filter === '1month') {
        $where[] = "created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)";
    } elseif ($time_filter === '3months') {
        $where[] = "created_at >= DATE_SUB(NOW(), INTERVAL 90 DAY)";
    } elseif ($time_filter === '6months') {
        $where[] = "created_at >= DATE_SUB(NOW(), INTERVAL 180 DAY)";
    } elseif ($time_filter === 'older6months') {
        $where[] = "created_at < DATE_SUB(NOW(), INTERVAL 180 DAY)";
    }

    // Priority Filtering
    if (!empty($priority_filter)) {
        $where[] = $wpdb->prepare("priority = %s", $priority_filter);
    }

    // Search Query
    if (!empty($search_query)) {
        $like = '%' . $wpdb->esc_like($search_query) . '%';
        $where[] = $wpdb->prepare("(phone LIKE %s OR customer_name LIKE %s OR email LIKE %s OR moving_from LIKE %s OR moving_to LIKE %s OR service_type LIKE %s OR notes LIKE %s)", $like, $like, $like, $like, $like, $like, $like);
    }

    // Order By
    $order_sql = "ORDER BY id DESC";
    if ($sort_by === 'oldest') {
        $order_sql = "ORDER BY id ASC";
    } elseif ($sort_by === 'priority_desc') {
        $order_sql = "ORDER BY FIELD(priority, 'urgent', 'high', 'normal', 'low'), id DESC";
    } elseif ($sort_by === 'priority_asc') {
        $order_sql = "ORDER BY FIELD(priority, 'low', 'normal', 'high', 'urgent'), id DESC";
    } elseif ($sort_by === 'name_asc') {
        $order_sql = "ORDER BY customer_name ASC, id DESC";
    }

    $where_sql = implode(' AND ', $where);
    $leads = $wpdb->get_results("SELECT * FROM $table_name WHERE $where_sql $order_sql LIMIT 500");

    $ajax_nonce = wp_create_nonce('hype_crm_nonce');
    ?>
    <style>
    @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@500;600&display=swap');

    .hype-wrap {
        margin: 20px 20px 20px 0;
        font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
        color: #0f172a;
        -webkit-font-smoothing: antialiased;
    }

    /* Executive Top Bar */
    .hype-topbar {
        background: linear-gradient(135deg, #091322 0%, #0f1d33 50%, #172554 100%);
        color: #ffffff;
        padding: 22px 28px;
        border-radius: 16px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        box-shadow: 0 12px 30px -8px rgba(9, 19, 34, 0.4), inset 0 1px 0 rgba(255, 255, 255, 0.1);
        margin-bottom: 20px;
        border: 1px solid rgba(255, 255, 255, 0.08);
        position: relative;
        overflow: hidden;
    }
    .hype-topbar::after {
        content: '';
        position: absolute;
        top: -50%;
        right: -10%;
        width: 300px;
        height: 300px;
        background: radial-gradient(circle, rgba(238, 29, 35, 0.15) 0%, transparent 70%);
        pointer-events: none;
    }
    .hype-topbar-left {
        display: flex;
        align-items: center;
        gap: 16px;
    }
    .hype-logo-icon {
        width: 44px;
        height: 44px;
        background: linear-gradient(135deg, #ee1d23 0%, #b91c1c 100%);
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 22px;
        box-shadow: 0 6px 16px rgba(238, 29, 35, 0.35);
    }
    .hype-topbar-title h1 {
        font-size: 23px;
        font-weight: 800;
        color: #ffffff;
        margin: 0;
        letter-spacing: -0.5px;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .hype-badge-pulse {
        background: rgba(16, 185, 129, 0.15);
        color: #34d399;
        border: 1px solid rgba(52, 211, 153, 0.35);
        font-size: 11px;
        font-weight: 700;
        padding: 3px 9px;
        border-radius: 20px;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }
    .hype-badge-pulse::before {
        content: '';
        width: 6px;
        height: 6px;
        background: #10b981;
        border-radius: 50%;
        box-shadow: 0 0 10px #10b981;
        animation: hypePulse 2s infinite;
    }
    @keyframes hypePulse {
        0%, 100% { opacity: 1; transform: scale(1); }
        50% { opacity: 0.5; transform: scale(1.3); }
    }
    .hype-topbar-title p {
        color: #94a3b8;
        font-size: 13px;
        margin: 4px 0 0 0;
    }
    .hype-topbar-right {
        display: flex;
        align-items: center;
        gap: 10px;
        z-index: 2;
    }

    /* KPI Summary Cards Strip */
    .hype-kpis {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 14px;
        margin-bottom: 20px;
    }
    .hype-kpi-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 14px;
        padding: 16px 20px;
        box-shadow: 0 2px 6px rgba(0,0,0,0.03);
        display: flex;
        justify-content: space-between;
        align-items: center;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    .hype-kpi-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 18px -4px rgba(15, 23, 42, 0.08);
    }
    .hype-kpi-info h3 {
        font-size: 12px;
        font-weight: 700;
        color: #64748b;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin: 0 0 6px 0;
    }
    .hype-kpi-info .kpi-num {
        font-size: 26px;
        font-weight: 800;
        color: #0f172a;
        line-height: 1;
        letter-spacing: -0.5px;
    }
    .hype-kpi-info .kpi-sub {
        font-size: 11.5px;
        color: #94a3b8;
        margin-top: 5px;
        display: flex;
        align-items: center;
        gap: 4px;
    }
    .hype-kpi-icon {
        width: 44px;
        height: 44px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
    }
    .kpi-blue { background: #eff6ff; color: #2563eb; }
    .kpi-amber { background: #fffbeb; color: #d97706; }
    .kpi-red { background: #fef2f2; color: #dc2626; }
    .kpi-green { background: #ecfdf5; color: #059669; }

    /* Modern Tabs */
    .hype-tabs-wrap {
        display: flex;
        align-items: center;
        justify-content: space-between;
        border-bottom: 2px solid #e2e8f0;
        margin-bottom: 16px;
        padding-bottom: 0;
    }
    .hype-tabs {
        display: flex;
        gap: 4px;
        overflow-x: auto;
    }
    .hype-tab {
        padding: 10px 18px;
        text-decoration: none;
        color: #64748b;
        font-weight: 700;
        font-size: 13.5px;
        border-bottom: 3px solid transparent;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        margin-bottom: -2px;
        transition: all 0.15s ease;
        white-space: nowrap;
    }
    .hype-tab:hover {
        color: #0f172a;
    }
    .hype-tab.active {
        color: #ee1d23;
        border-bottom-color: #ee1d23;
    }
    .hype-tab-count {
        font-size: 11px;
        font-weight: 800;
        padding: 2px 7px;
        border-radius: 10px;
        background: #f1f5f9;
        color: #475569;
    }
    .hype-tab.active .hype-tab-count {
        background: #fee2e2;
        color: #b91c1c;
    }

    /* Toolbar / Filters */
    .hype-toolbar {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        padding: 14px 18px;
        display: flex;
        flex-wrap: wrap;
        gap: 12px;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 16px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.03);
    }
    .hype-tool-left {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        align-items: center;
    }
    .hype-search-box {
        position: relative;
    }
    .hype-search-box input {
        width: 250px;
        padding: 8px 12px 8px 34px;
        border: 1.5px solid #cbd5e1;
        border-radius: 8px;
        font-size: 13px;
        font-family: inherit;
        background: #ffffff;
        transition: all 0.2s ease;
    }
    .hype-search-box input:focus {
        border-color: #0f172a;
        box-shadow: 0 0 0 3px rgba(15, 23, 42, 0.08);
        outline: none;
    }
    .hype-search-icon {
        position: absolute;
        left: 11px;
        top: 50%;
        transform: translateY(-50%);
        color: #94a3b8;
        font-size: 14px;
        pointer-events: none;
    }
    .hype-select {
        padding: 8px 12px;
        border: 1.5px solid #cbd5e1;
        border-radius: 8px;
        font-size: 13px;
        font-family: inherit;
        background: #ffffff;
        color: #1e293b;
        font-weight: 600;
        cursor: pointer;
        outline: none;
        transition: border 0.2s ease;
    }
    .hype-select:focus {
        border-color: #0f172a;
    }
    .hype-tool-right {
        display: flex;
        gap: 10px;
        align-items: center;
    }

    /* Buttons */
    .hype-btn {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        padding: 8px 16px;
        border-radius: 8px;
        font-size: 13px;
        font-weight: 700;
        cursor: pointer;
        text-decoration: none;
        border: none;
        transition: all 0.15s ease;
        font-family: inherit;
    }
    .hype-btn-primary {
        background: linear-gradient(135deg, #ee1d23 0%, #c2030a 100%);
        color: #fff;
        box-shadow: 0 4px 12px rgba(238, 29, 35, 0.25);
    }
    .hype-btn-primary:hover {
        background: #c2030a;
        color: #fff;
    }
    .hype-btn-green {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        color: #fff;
        box-shadow: 0 4px 12px rgba(16, 185, 129, 0.25);
    }
    .hype-btn-green:hover {
        background: #059669;
        color: #fff;
    }
    .hype-btn-dark {
        background: #0f172a;
        color: #fff;
    }
    .hype-btn-dark:hover {
        background: #1e293b;
    }
    .hype-btn-outline {
        background: #fff;
        border: 1.5px solid #cbd5e1;
        color: #475569;
    }
    .hype-btn-outline:hover {
        border-color: #0f172a;
        color: #0f172a;
    }

    /* Spreadsheet Grid Table */
    .hype-sheet-card {
        background: #ffffff;
        border: 1.5px solid #e2e8f0;
        border-radius: 14px;
        box-shadow: 0 4px 14px -2px rgba(15, 23, 42, 0.05);
        overflow: hidden;
    }
    .hype-sheet-scroll {
        overflow-x: auto;
        max-height: calc(100vh - 310px);
    }
    .hype-sheet-table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
        font-size: 13px;
        text-align: left;
    }
    .hype-sheet-table th {
        background: #f8fafc;
        color: #475569;
        font-size: 11.5px;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.6px;
        padding: 12px 14px;
        border-bottom: 2px solid #e2e8f0;
        border-right: 1px solid #e2e8f0;
        position: sticky;
        top: 0;
        z-index: 10;
        white-space: nowrap;
        user-select: none;
    }
    .hype-sheet-table th:last-child {
        border-right: none;
    }
    .hype-sheet-table td {
        padding: 12px 14px;
        border-bottom: 1px solid #e2e8f0;
        border-right: 1px solid #e2e8f0;
        vertical-align: middle;
        background: #ffffff;
        transition: background 0.15s ease;
    }
    .hype-sheet-table td:last-child {
        border-right: none;
    }
    .hype-sheet-table tr:hover td {
        background: #f8fafc !important;
    }
    .hype-sheet-table tr.selected td {
        background: #eff6ff !important;
    }

    /* Monospace ID Badge */
    .hype-id-tag {
        font-family: 'JetBrains Mono', ui-monospace, monospace;
        font-weight: 700;
        color: #64748b;
        font-size: 12px;
        background: #f1f5f9;
        padding: 3px 7px;
        border-radius: 6px;
        border: 1px solid #e2e8f0;
    }

    /* Customer Initials & Name */
    .hype-customer-cell {
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .hype-avatar-circle {
        width: 34px;
        height: 34px;
        border-radius: 50%;
        background: linear-gradient(135deg, #0b192c 0%, #1e3a8a 100%);
        color: #fff;
        font-weight: 800;
        font-size: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        box-shadow: 0 2px 6px rgba(11,25,44,0.15);
    }
    .hype-cust-name {
        font-weight: 700;
        color: #0f172a;
        font-size: 13.5px;
    }
    .hype-cust-phone {
        font-size: 12px;
        font-weight: 700;
        color: #ee1d23;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 4px;
        margin-top: 1px;
    }
    .hype-cust-email {
        font-size: 11px;
        color: #64748b;
        margin-top: 1px;
    }

    /* Route Visual Pill */
    .hype-route-pill {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        font-weight: 600;
        color: #1e293b;
        font-size: 12.5px;
    }
    .route-dot-from { width: 7px; height: 7px; border-radius: 50%; background: #10b981; }
    .route-dot-to { width: 7px; height: 7px; border-radius: 50%; background: #ee1d23; }
    .route-arrow { color: #94a3b8; font-size: 11px; }

    /* Category Badges */
    .hype-badge-size {
        background: #eff6ff;
        color: #1d4ed8;
        border: 1px solid #bfdbfe;
        font-weight: 800;
        font-size: 11.5px;
        padding: 3px 8px;
        border-radius: 6px;
        display: inline-block;
    }
    .hype-badge-service {
        font-size: 12px;
        font-weight: 600;
        color: #475569;
        margin-top: 3px;
    }
    .hype-badge-cost {
        background: #ecfdf5;
        color: #059669;
        border: 1px solid #a7f3d0;
        font-weight: 800;
        font-size: 12px;
        padding: 3px 8px;
        border-radius: 6px;
        display: inline-block;
    }
    .hype-source-badge {
        font-size: 11px;
        font-weight: 700;
        color: #475569;
        background: #f8fafc;
        border: 1px solid #cbd5e1;
        padding: 3px 8px;
        border-radius: 6px;
        display: inline-flex;
        align-items: center;
        gap: 4px;
        white-space: nowrap;
    }

    /* Interactive Status & Priority Selectors */
    .hype-status-tag {
        padding: 6px 10px;
        border-radius: 8px;
        font-size: 12px;
        font-weight: 700;
        border: 1.5px solid transparent;
        cursor: pointer;
        outline: none;
        font-family: inherit;
        transition: all 0.2s ease;
    }
    .status-new { background: #eff6ff; color: #1d4ed8; border-color: #93c5fd; }
    .status-contacted { background: #fffbeb; color: #b45309; border-color: #fde68a; }
    .status-quoted { background: #f5f3ff; color: #6d28d9; border-color: #ddd6fe; }
    .status-converted { background: #ecfdf5; color: #047857; border-color: #a7f3d0; }
    .status-no_longer_needed { background: #f1f5f9; color: #475569; border-color: #cbd5e1; }
    .status-spam { background: #fff1f2; color: #be123c; border-color: #fecdd3; }
    .status-trash { background: #f1f5f9; color: #64748b; border-color: #cbd5e1; }

    .hype-priority-tag {
        padding: 5px 8px;
        border-radius: 6px;
        font-size: 11.5px;
        font-weight: 800;
        border: 1.5px solid transparent;
        cursor: pointer;
        outline: none;
        font-family: inherit;
    }
    .priority-urgent { background: #fef2f2; color: #991b1b; border-color: #fca5a5; }
    .priority-high { background: #fffbeb; color: #92400e; border-color: #fcd34d; }
    .priority-normal { background: #f8fafc; color: #334155; border-color: #cbd5e1; }
    .priority-low { background: #f9fafb; color: #6b7280; border-color: #e5e7eb; }

    /* Action Icon Buttons */
    .hype-actions-cell {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
    }
    .hype-act-btn {
        width: 32px;
        height: 32px;
        border-radius: 8px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        color: #fff;
        text-decoration: none;
        border: none;
        cursor: pointer;
        transition: transform 0.15s ease, box-shadow 0.15s ease;
        font-size: 13px;
        flex-shrink: 0;
    }
    .hype-act-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 10px rgba(0,0,0,0.15);
    }
    .act-wa { background: #25d366; box-shadow: 0 2px 6px rgba(37, 211, 102, 0.3); }
    .act-call { background: #2563eb; box-shadow: 0 2px 6px rgba(37, 99, 235, 0.3); }
    .act-view { background: #334155; box-shadow: 0 2px 6px rgba(51, 65, 85, 0.2); }
    .act-spam { background: #e11d48; box-shadow: 0 2px 6px rgba(225, 29, 72, 0.25); }
    .act-trash { background: #ef4444; box-shadow: 0 2px 6px rgba(239, 68, 68, 0.25); }
    .act-restore { background: #10b981; padding: 0 10px; width: auto; font-size: 12px; font-weight: 700; }

    /* Toast Notification */
    #hypeToast {
        position: fixed;
        bottom: 24px;
        right: 24px;
        background: #0f172a;
        color: #ffffff;
        padding: 12px 20px;
        border-radius: 10px;
        font-size: 13.5px;
        font-weight: 700;
        box-shadow: 0 10px 25px -5px rgba(0,0,0,0.3);
        display: none;
        align-items: center;
        gap: 10px;
        z-index: 999999;
        border: 1px solid rgba(255,255,255,0.1);
        animation: hypeToastIn 0.25s ease;
    }
    @keyframes hypeToastIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }

    /* Modal / Drawer */
    .hype-modal-overlay {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(11, 25, 44, 0.65);
        backdrop-filter: blur(5px);
        z-index: 99999;
        display: none;
        align-items: center;
        justify-content: center;
    }
    .hype-modal-box {
        background: #ffffff;
        border-radius: 16px;
        max-width: 680px;
        width: 90%;
        max-height: 90vh;
        overflow-y: auto;
        padding: 28px;
        box-shadow: 0 25px 60px -15px rgba(0,0,0,0.3);
        border: 1px solid #e2e8f0;
    }
    </style>

    <div class="hype-wrap">
        <!-- Executive Top Bar -->
        <div class="hype-topbar">
            <div class="hype-topbar-left">
                <div class="hype-logo-icon">📊</div>
                <div class="hype-topbar-title">
                    <h1>
                        <span>Hype Marketer</span>
                        <span class="hype-badge-pulse">Live Spreadsheets CRM</span>
                    </h1>
                    <p>Capturing every lead across Homepage Hero, Contact Page, Popups & Inquiries • Direct WhatsApp Dispatch</p>
                </div>
            </div>
            <div class="hype-topbar-right">
                <button type="button" class="hype-btn hype-btn-primary" onclick="hypeOpenManualModal()">
                    + Add Lead Manually
                </button>
                <a href="<?php echo esc_url(admin_url('admin.php?page=hype-marketer&action=hype_export_csv&tab=' . urlencode($tab))); ?>" class="hype-btn hype-btn-green">
                    📥 Export to CSV
                </a>
            </div>
        </div>

        <!-- 4 KPI Summary Cards -->
        <div class="hype-kpis">
            <div class="hype-kpi-card">
                <div class="hype-kpi-info">
                    <h3>Active Pipeline</h3>
                    <div class="kpi-num"><?php echo $count_active; ?></div>
                    <div class="kpi-sub">Total verified inquiries</div>
                </div>
                <div class="hype-kpi-icon kpi-blue">📋</div>
            </div>
            <div class="hype-kpi-card">
                <div class="hype-kpi-info">
                    <h3>Today's Leads</h3>
                    <div class="kpi-num"><?php echo $count_today; ?></div>
                    <div class="kpi-sub"><span style="color:#10b981; font-weight:700;">● Live</span> Today: <?php echo date('M d'); ?></div>
                </div>
                <div class="hype-kpi-icon kpi-amber">⚡</div>
            </div>
            <div class="hype-kpi-card">
                <div class="hype-kpi-info">
                    <h3>High Priority</h3>
                    <div class="kpi-num" style="color:#ee1d23;"><?php echo $count_priority; ?></div>
                    <div class="kpi-sub">Urgent attention needed</div>
                </div>
                <div class="hype-kpi-icon kpi-red">🔥</div>
            </div>
            <div class="hype-kpi-card">
                <div class="hype-kpi-info">
                    <h3>Converted Moves</h3>
                    <div class="kpi-num" style="color:#059669;"><?php echo $count_converted; ?></div>
                    <div class="kpi-sub"><span style="color:#059669; font-weight:700;"><?php echo $conversion_rate; ?>%</span> conversion rate</div>
                </div>
                <div class="hype-kpi-icon kpi-green">🏆</div>
            </div>
        </div>

        <!-- Spreadsheet Navigation Tabs -->
        <div class="hype-tabs-wrap">
            <div class="hype-tabs">
                <a href="<?php echo esc_url(admin_url('admin.php?page=hype-marketer&tab=active')); ?>" class="hype-tab <?php echo $tab === 'active' ? 'active' : ''; ?>">
                    📋 All Active <span class="hype-tab-count"><?php echo $count_active; ?></span>
                </a>
                <a href="<?php echo esc_url(admin_url('admin.php?page=hype-marketer&tab=new')); ?>" class="hype-tab <?php echo $tab === 'new' ? 'active' : ''; ?>">
                    🔵 New / Uncontacted <span class="hype-tab-count"><?php echo $count_new; ?></span>
                </a>
                <a href="<?php echo esc_url(admin_url('admin.php?page=hype-marketer&tab=priority')); ?>" class="hype-tab <?php echo $tab === 'priority' ? 'active' : ''; ?>">
                    🔥 High Priority <span class="hype-tab-count"><?php echo $count_priority; ?></span>
                </a>
                <a href="<?php echo esc_url(admin_url('admin.php?page=hype-marketer&tab=converted')); ?>" class="hype-tab <?php echo $tab === 'converted' ? 'active' : ''; ?>">
                    🏆 Converted <span class="hype-tab-count"><?php echo $count_converted; ?></span>
                </a>
                <a href="<?php echo esc_url(admin_url('admin.php?page=hype-marketer&tab=no_longer_needed')); ?>" class="hype-tab <?php echo $tab === 'no_longer_needed' ? 'active' : ''; ?>">
                    ⚪ No Longer Needed <span class="hype-tab-count"><?php echo $count_no_longer_needed; ?></span>
                </a>
                <a href="<?php echo esc_url(admin_url('admin.php?page=hype-marketer&tab=spam')); ?>" class="hype-tab <?php echo $tab === 'spam' ? 'active' : ''; ?>">
                    🛑 Spam <span class="hype-tab-count"><?php echo $count_spam; ?></span>
                </a>
                <a href="<?php echo esc_url(admin_url('admin.php?page=hype-marketer&tab=trash')); ?>" class="hype-tab <?php echo $tab === 'trash' ? 'active' : ''; ?>">
                    🗑️ Trash <span class="hype-tab-count"><?php echo $count_trash; ?></span>
                </a>
            </div>
            <div style="font-size: 12px; color: #64748b; font-weight: 700;">
                Showing <?php echo count($leads); ?> leads
            </div>
        </div>

        <!-- Toolbar / Filters -->
        <form method="get" class="hype-toolbar">
            <input type="hidden" name="page" value="hype-marketer">
            <input type="hidden" name="tab" value="<?php echo esc_attr($tab); ?>">

            <div class="hype-tool-left">
                <!-- Search Box -->
                <div class="hype-search-box">
                    <span class="hype-search-icon">🔍</span>
                    <input type="text" id="hypeLiveSearch" name="s_lead" placeholder="Search name, phone, route..." value="<?php echo esc_attr($search_query); ?>" onkeyup="hypeClientSearch(this.value)">
                </div>

                <!-- Age / Time Filter -->
                <select name="time_filter" class="hype-select" onchange="this.form.submit()">
                    <option value="all" <?php selected($time_filter, 'all'); ?>>📅 Time: All Time</option>
                    <option value="today" <?php selected($time_filter, 'today'); ?>>📅 Time: Today</option>
                    <option value="7days" <?php selected($time_filter, '7days'); ?>>📅 Time: Past 7 Days</option>
                    <option value="1month" <?php selected($time_filter, '1month'); ?>>📅 Time: 1 Month Old</option>
                    <option value="3months" <?php selected($time_filter, '3months'); ?>>📅 Time: 3 Months Old</option>
                    <option value="6months" <?php selected($time_filter, '6months'); ?>>📅 Time: 6 Months Old</option>
                    <option value="older6months" <?php selected($time_filter, 'older6months'); ?>>📅 Time: Older than 6 Months</option>
                </select>

                <!-- Priority Filter -->
                <select name="priority_filter" class="hype-select" onchange="this.form.submit()">
                    <option value="">🎯 Priority: All</option>
                    <option value="urgent" <?php selected($priority_filter, 'urgent'); ?>>🔥 Urgent</option>
                    <option value="high" <?php selected($priority_filter, 'high'); ?>>⚡ High</option>
                    <option value="normal" <?php selected($priority_filter, 'normal'); ?>>🔵 Normal</option>
                    <option value="low" <?php selected($priority_filter, 'low'); ?>>💤 Low</option>
                </select>

                <!-- Sort By -->
                <select name="sort_by" class="hype-select" onchange="this.form.submit()">
                    <option value="newest" <?php selected($sort_by, 'newest'); ?>>⚡ Sort: Newest First</option>
                    <option value="oldest" <?php selected($sort_by, 'oldest'); ?>>🕒 Sort: Oldest First</option>
                    <option value="priority_desc" <?php selected($sort_by, 'priority_desc'); ?>>🔥 Sort: Highest Priority</option>
                    <option value="name_asc" <?php selected($sort_by, 'name_asc'); ?>>🔤 Sort: Name (A-Z)</option>
                </select>

                <button type="submit" class="hype-btn hype-btn-dark" style="padding:7px 12px; font-size:12px;">Apply Filter</button>
                <?php if (!empty($search_query) || $time_filter !== 'all' || !empty($priority_filter) || $sort_by !== 'newest') : ?>
                    <a href="<?php echo esc_url(admin_url('admin.php?page=hype-marketer&tab=' . urlencode($tab))); ?>" style="font-size:12px; color:#64748b; font-weight:700; text-decoration:underline;">Reset</a>
                <?php endif; ?>
            </div>

            <!-- Bulk Actions -->
            <div class="hype-tool-right">
                <select id="hypeBulkActionSelect" class="hype-select" style="font-size:12px; padding: 7px 10px;">
                    <option value="">Bulk Actions</option>
                    <?php if ($tab !== 'trash') : ?>
                        <option value="trash">Move to Trash</option>
                        <option value="spam">Mark as Spam</option>
                        <option value="priority_urgent">Set Urgent Priority</option>
                        <option value="priority_high">Set High Priority</option>
                        <option value="mark_contacted">Mark as Contacted</option>
                    <?php else : ?>
                        <option value="restore">Restore from Trash</option>
                        <option value="delete">Delete Permanently</option>
                    <?php endif; ?>
                </select>
                <button type="button" class="hype-btn hype-btn-dark" style="font-size:12px; padding:7px 12px;" onclick="hypeExecuteBulkAction()">Apply</button>
            </div>
        </form>

        <!-- Spreadsheet Grid Table -->
        <div class="hype-sheet-card">
            <div class="hype-sheet-scroll">
                <table class="hype-sheet-table" id="hypeMainTable">
                    <thead>
                        <tr>
                            <th style="width: 34px; text-align:center;"><input type="checkbox" id="hypeSelectAll"></th>
                            <th style="width: 55px;"># ID</th>
                            <th style="width: 140px;">Date & Time</th>
                            <th style="width: 110px;">Priority</th>
                            <th style="width: 220px;">Customer & Contacts</th>
                            <th style="width: 180px;">Route (From → To)</th>
                            <th style="width: 130px;">Size / Service</th>
                            <th style="width: 120px;">Est. Range</th>
                            <th style="width: 150px;">Source</th>
                            <th style="width: 140px;">Status</th>
                            <th style="width: 170px; text-align:center;">Quick Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($leads)) : ?>
                            <tr>
                                <td colspan="11" style="text-align: center; padding: 50px 20px; color: #94a3b8;">
                                    <div style="font-size: 36px; margin-bottom: 10px;">📂</div>
                                    <div style="font-size: 15px; font-weight: 700; color: #475569;">No leads found in this view.</div>
                                    <p style="font-size: 12.5px; margin-top: 4px; color: #94a3b8;">Inquiries submitted anywhere on the website will instantly appear in this spreadsheet.</p>
                                </td>
                            </tr>
                        <?php else : ?>
                            <?php foreach ($leads as $l) : 
                                $clean_p = preg_replace('/[^0-9]/', '', $l->phone);
                                $wa_phone = strlen($clean_p) === 10 ? ('91' . $clean_p) : $clean_p;
                                $display_name = !empty($l->customer_name) ? esc_html($l->customer_name) : 'Website Visitor';
                                $pr = !empty($l->priority) ? $l->priority : 'normal';

                                // Customer initials avatar
                                $words = explode(' ', trim($display_name));
                                $initials = strtoupper(substr($words[0] ?? 'C', 0, 1) . (isset($words[1]) ? substr($words[1], 0, 1) : ''));
                                if (empty($initials)) $initials = 'CL';

                                // Source icon badge
                                $source_label = esc_html($l->source);
                                $source_icon = '📝';
                                if (stripos($source_label, 'Hero') !== false) $source_icon = '📱';
                                elseif (stripos($source_label, 'Contact') !== false) $source_icon = '📩';
                                elseif (stripos($source_label, 'Popup') !== false || stripos($source_label, 'Modal') !== false) $source_icon = '💬';
                                elseif (stripos($source_label, 'Manual') !== false) $source_icon = '✍️';
                            ?>
                            <tr id="lead-row-<?php echo esc_attr($l->id); ?>">
                                <td style="text-align:center;">
                                    <input type="checkbox" class="hype-row-cb" value="<?php echo esc_attr($l->id); ?>">
                                </td>
                                <td>
                                    <span class="hype-id-tag">#<?php echo esc_html($l->id); ?></span>
                                </td>
                                <td>
                                    <div style="font-weight: 700; color: #0f172a; font-size: 12.5px;"><?php echo date('M d, Y', strtotime($l->created_at)); ?></div>
                                    <div style="font-size: 11px; color: #64748b; margin-top: 2px;"><?php echo date('h:i A', strtotime($l->created_at)); ?></div>
                                </td>
                                <td>
                                    <select class="hype-priority-tag priority-<?php echo esc_attr($pr); ?>" onchange="hypeChangePriority(<?php echo esc_attr($l->id); ?>, this.value, this)">
                                        <option value="urgent" <?php selected($pr, 'urgent'); ?>>🔥 Urgent</option>
                                        <option value="high" <?php selected($pr, 'high'); ?>>⚡ High</option>
                                        <option value="normal" <?php selected($pr, 'normal'); ?>>🔵 Normal</option>
                                        <option value="low" <?php selected($pr, 'low'); ?>>💤 Low</option>
                                    </select>
                                </td>
                                <td>
                                    <div class="hype-customer-cell">
                                        <div class="hype-avatar-circle"><?php echo esc_html($initials); ?></div>
                                        <div>
                                            <div class="hype-cust-name"><?php echo $display_name; ?></div>
                                            <a href="tel:<?php echo esc_attr($l->phone); ?>" class="hype-cust-phone">
                                                📞 <?php echo esc_html($l->phone); ?>
                                            </a>
                                            <?php if (!empty($l->email)) : ?>
                                                <div class="hype-cust-email">✉️ <?php echo esc_html($l->email); ?></div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <?php if (!empty($l->moving_from) || !empty($l->moving_to)) : ?>
                                        <div class="hype-route-pill">
                                            <span class="route-dot-from" title="Origin"></span>
                                            <span><?php echo esc_html($l->moving_from); ?></span>
                                            <span class="route-arrow">➔</span>
                                            <span class="route-dot-to" title="Destination"></span>
                                            <span><?php echo esc_html($l->moving_to); ?></span>
                                        </div>
                                        <?php if (!empty($l->move_date)) : ?>
                                            <div style="font-size: 11px; color: #64748b; margin-top: 3px;">🗓️ Move: <?php echo esc_html($l->move_date); ?></div>
                                        <?php endif; ?>
                                    <?php else : ?>
                                        <span style="color: #cbd5e1;">—</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if (!empty($l->move_size)) : ?>
                                        <span class="hype-badge-size"><?php echo esc_html($l->move_size); ?></span>
                                    <?php endif; ?>
                                    <?php if (!empty($l->service_type)) : ?>
                                        <div class="hype-badge-service"><?php echo esc_html($l->service_type); ?></div>
                                    <?php endif; ?>
                                    <?php if (empty($l->move_size) && empty($l->service_type)) : ?>
                                        <span style="color: #cbd5e1;">—</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if (!empty($l->estimated_price)) : ?>
                                        <span class="hype-badge-cost"><?php echo esc_html($l->estimated_price); ?></span>
                                    <?php else : ?>
                                        <span style="color: #cbd5e1;">—</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <span class="hype-source-badge">
                                        <span><?php echo $source_icon; ?></span>
                                        <span><?php echo $source_label; ?></span>
                                    </span>
                                </td>
                                <td>
                                    <select class="hype-status-tag status-<?php echo esc_attr($l->status); ?>" onchange="hypeChangeStatus(<?php echo esc_attr($l->id); ?>, this.value, this)">
                                        <option value="new" <?php selected($l->status, 'new'); ?>>● New</option>
                                        <option value="contacted" <?php selected($l->status, 'contacted'); ?>>● Contacted</option>
                                        <option value="quoted" <?php selected($l->status, 'quoted'); ?>>● Quoted</option>
                                        <option value="converted" <?php selected($l->status, 'converted'); ?>>● Converted</option>
                                        <option value="no_longer_needed" <?php selected($l->status, 'no_longer_needed'); ?>>● No Longer Needed</option>
                                        <option value="spam" <?php selected($l->status, 'spam'); ?>>● Spam</option>
                                        <option value="trash" <?php selected($l->status, 'trash'); ?>>● Trash</option>
                                    </select>
                                </td>
                                <td>
                                    <div class="hype-actions-cell">
                                        <?php if ($tab !== 'trash') : ?>
                                            <!-- 1-Click WhatsApp -->
                                            <a href="https://wa.me/<?php echo esc_attr($wa_phone); ?>?text=<?php echo rawurlencode('Hello ' . $display_name . ', thank you for contacting Amar Packers & Movers. Regarding your inquiry:'); ?>" target="_blank" class="hype-act-btn act-wa" title="Open WhatsApp Chat">
                                                💬
                                            </a>
                                            <!-- 1-Click Phone Call -->
                                            <a href="tel:<?php echo esc_attr($l->phone); ?>" class="hype-act-btn act-call" title="Call Customer">
                                                📞
                                            </a>
                                            <!-- View Details / Notes -->
                                            <button type="button" class="hype-act-btn act-view" onclick='hypeOpenDetailModal(<?php echo json_encode($l); ?>)' title="View Full Details & Notes">
                                                👁️
                                            </button>
                                            <!-- Mark as Spam -->
                                            <button type="button" class="hype-act-btn act-spam" onclick="hypeToggleSpam(<?php echo esc_attr($l->id); ?>, 1)" title="Mark as Spam">
                                                🛑
                                            </button>
                                            <!-- Move to Trash -->
                                            <button type="button" class="hype-act-btn act-trash" onclick="hypeMoveToTrash(<?php echo esc_attr($l->id); ?>)" title="Move to Trash">
                                                🗑️
                                            </button>
                                        <?php else : ?>
                                            <!-- Restore -->
                                            <button type="button" class="hype-act-btn act-restore" onclick="hypeRestoreTrash(<?php echo esc_attr($l->id); ?>)" title="Restore Lead">
                                                ♻️ Restore
                                            </button>
                                            <!-- Delete Permanent -->
                                            <button type="button" class="hype-act-btn act-trash" onclick="hypeDeletePermanent(<?php echo esc_attr($l->id); ?>)" title="Delete Permanently">
                                                ❌
                                            </button>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Floating Toast Notification -->
    <div id="hypeToast">
        <span id="hypeToastMsg">Action completed successfully</span>
    </div>

    <!-- Details & Staff Notes Modal -->
    <div id="hypeLeadModal" class="hype-modal-overlay">
        <div class="hype-modal-box">
            <div style="display:flex; justify-content:space-between; align-items:center; border-bottom:1px solid #e2e8f0; padding-bottom:14px; margin-bottom:18px;">
                <div style="display:flex; align-items:center; gap:10px;">
                    <div style="font-size:24px;">📋</div>
                    <div>
                        <h3 id="hypeModalLeadTitle" style="margin:0; font-size:18px; font-weight:800; color:#0b192c;">Lead Details</h3>
                        <p id="hypeModalSubtitle" style="margin:2px 0 0 0; font-size:12px; color:#64748b;">Full inquiry submission details</p>
                    </div>
                </div>
                <button type="button" onclick="hypeCloseModal()" style="border:none; background:#f1f5f9; border-radius:8px; width:32px; height:32px; font-size:18px; cursor:pointer; color:#64748b; display:flex; align-items:center; justify-content:center;">&times;</button>
            </div>
            
            <div id="hypeModalQuickBar" style="background:#f8fafc; border:1px solid #e2e8f0; border-radius:10px; padding:12px 16px; display:flex; justify-content:space-between; align-items:center; margin-bottom:18px;">
                <div id="hypeModalCustNamePhone" style="font-weight:700; color:#0f172a;"></div>
                <div id="hypeModalQuickBtns" style="display:flex; gap:8px;"></div>
            </div>

            <div id="hypeModalContent" style="margin-bottom:18px;"></div>

            <div style="background:#f8fafc; border:1.5px solid #e2e8f0; border-radius:10px; padding:16px;">
                <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:8px;">
                    <label style="font-size:13px; font-weight:700; color:#0f172a;">Internal Staff Follow-up Notes:</label>
                    <span style="font-size:11px; color:#94a3b8;">Private team notes</span>
                </div>
                <textarea id="hypeModalNotes" style="width:100%; height:85px; padding:10px; border:1.5px solid #cbd5e1; border-radius:8px; font-size:13px; font-family:inherit; box-sizing:border-box; background:#fff;" placeholder="Add notes (e.g. Quoted 14,000 via WhatsApp, moving on Saturday, survey completed)..."></textarea>
            </div>

            <div style="display:flex; justify-content:flex-end; gap:10px; margin-top:20px;">
                <button type="button" class="hype-btn hype-btn-outline" onclick="hypeCloseModal()">Close</button>
                <button type="button" class="hype-btn hype-btn-primary" onclick="hypeSaveNotes()">Save Notes</button>
            </div>
        </div>
    </div>

    <!-- Manual Add Lead Modal -->
    <div id="hypeManualLeadModal" class="hype-modal-overlay">
        <div class="hype-modal-box">
            <div style="display:flex; justify-content:space-between; align-items:center; border-bottom:1px solid #e2e8f0; padding-bottom:14px; margin-bottom:18px;">
                <div style="display:flex; align-items:center; gap:10px;">
                    <div style="font-size:24px;">✍️</div>
                    <div>
                        <h3 style="margin:0; font-size:18px; font-weight:800; color:#0b192c;">+ Add New Lead Manually</h3>
                        <p style="margin:2px 0 0 0; font-size:12px; color:#64748b;">Record an offline phone call, walk-in, or direct inquiry</p>
                    </div>
                </div>
                <button type="button" onclick="hypeCloseManualModal()" style="border:none; background:#f1f5f9; border-radius:8px; width:32px; height:32px; font-size:18px; cursor:pointer; color:#64748b;">&times;</button>
            </div>
            <form id="hypeManualLeadForm" onsubmit="hypeSubmitManualLead(event)">
                <div style="display:grid; grid-template-columns:1fr 1fr; gap:14px; margin-bottom:14px;">
                    <div>
                        <label style="display:block; font-size:12px; font-weight:700; margin-bottom:5px;">Customer Name</label>
                        <input type="text" name="customer_name" required class="hype-search-box" style="width:100%;" placeholder="e.g. Ramesh Patel">
                    </div>
                    <div>
                        <label style="display:block; font-size:12px; font-weight:700; margin-bottom:5px;">Phone Number *</label>
                        <input type="tel" name="phone" required class="hype-search-box" style="width:100%;" placeholder="e.g. 9820012345">
                    </div>
                </div>
                <div style="display:grid; grid-template-columns:1fr 1fr; gap:14px; margin-bottom:14px;">
                    <div>
                        <label style="display:block; font-size:12px; font-weight:700; margin-bottom:5px;">Moving From</label>
                        <input type="text" name="moving_from" class="hype-search-box" style="width:100%;" placeholder="e.g. Powai">
                    </div>
                    <div>
                        <label style="display:block; font-size:12px; font-weight:700; margin-bottom:5px;">Moving To</label>
                        <input type="text" name="moving_to" class="hype-search-box" style="width:100%;" placeholder="e.g. Thane">
                    </div>
                </div>
                <div style="display:grid; grid-template-columns:1fr 1fr; gap:14px; margin-bottom:14px;">
                    <div>
                        <label style="display:block; font-size:12px; font-weight:700; margin-bottom:5px;">Move Size / Service</label>
                        <input type="text" name="move_size" class="hype-search-box" style="width:100%;" placeholder="e.g. 2 BHK / Household">
                    </div>
                    <div>
                        <label style="display:block; font-size:12px; font-weight:700; margin-bottom:5px;">Priority</label>
                        <select name="priority" class="hype-select" style="width:100%;">
                            <option value="urgent">🔥 Urgent</option>
                            <option value="high">⚡ High</option>
                            <option value="normal" selected>🔵 Normal</option>
                            <option value="low">💤 Low</option>
                        </select>
                    </div>
                </div>
                <div style="margin-bottom:16px;">
                    <label style="display:block; font-size:12px; font-weight:700; margin-bottom:5px;">Initial Notes</label>
                    <textarea name="notes" class="hype-search-box" style="width:100%; height:65px; font-family:inherit; padding:10px;" placeholder="Details about their move requirement..."></textarea>
                </div>
                <div style="display:flex; justify-content:flex-end; gap:10px;">
                    <button type="button" class="hype-btn hype-btn-outline" onclick="hypeCloseManualModal()">Cancel</button>
                    <button type="submit" class="hype-btn hype-btn-primary">Save Lead to Spreadsheet</button>
                </div>
            </form>
        </div>
    </div>

    <script>
    const ajaxurl = '<?php echo esc_url(admin_url('admin-ajax.php')); ?>';
    const ajaxNonce = '<?php echo esc_js($ajax_nonce); ?>';
    let currentModalLeadId = 0;

    // Toast Notification
    function showHypeToast(msg) {
        const t = document.getElementById('hypeToast');
        const m = document.getElementById('hypeToastMsg');
        if (!t || !m) return;
        m.innerHTML = msg;
        t.style.display = 'flex';
        setTimeout(() => { t.style.display = 'none'; }, 3000);
    }

    // Client-side Instant Filter
    function hypeClientSearch(term) {
        term = term.toLowerCase().trim();
        const rows = document.querySelectorAll('#hypeMainTable tbody tr');
        rows.forEach(tr => {
            if (tr.children.length < 5) return;
            const text = tr.innerText.toLowerCase();
            tr.style.display = text.includes(term) ? '' : 'none';
        });
    }

    // Select All
    document.getElementById('hypeSelectAll')?.addEventListener('change', function() {
        const checked = this.checked;
        document.querySelectorAll('.hype-row-cb').forEach(cb => {
            cb.checked = checked;
            const tr = cb.closest('tr');
            if (tr) checked ? tr.classList.add('selected') : tr.classList.remove('selected');
        });
    });

    document.querySelectorAll('.hype-row-cb').forEach(cb => {
        cb.addEventListener('change', function() {
            const tr = this.closest('tr');
            if (tr) this.checked ? tr.classList.add('selected') : tr.classList.remove('selected');
        });
    });

    // Update Status
    function hypeChangeStatus(leadId, newStatus, selectEl) {
        selectEl.className = 'hype-status-tag status-' + newStatus;
        fetch(ajaxurl, {
            method: 'POST',
            headers: {'Content-Type': 'application/x-www-form-urlencoded'},
            body: new URLSearchParams({
                action: 'hype_update_status',
                lead_id: leadId,
                status: newStatus,
                security: ajaxNonce
            })
        }).then(res => res.json()).then(d => {
            if (d.success) showHypeToast('✓ Status updated to <strong>' + newStatus + '</strong>');
        });
    }

    // Update Priority
    function hypeChangePriority(leadId, newPriority, selectEl) {
        selectEl.className = 'hype-priority-tag priority-' + newPriority;
        fetch(ajaxurl, {
            method: 'POST',
            headers: {'Content-Type': 'application/x-www-form-urlencoded'},
            body: new URLSearchParams({
                action: 'hype_update_priority',
                lead_id: leadId,
                priority: newPriority,
                security: ajaxNonce
            })
        }).then(res => res.json()).then(d => {
            if (d.success) showHypeToast('✓ Priority set to <strong>' + newPriority + '</strong>');
        });
    }

    // Move to Trash
    function hypeMoveToTrash(leadId) {
        if (!confirm('Move Lead #' + leadId + ' to Trash?')) return;
        fetch(ajaxurl, {
            method: 'POST',
            headers: {'Content-Type': 'application/x-www-form-urlencoded'},
            body: new URLSearchParams({
                action: 'hype_move_to_trash',
                lead_id: leadId,
                security: ajaxNonce
            })
        }).then(res => res.json()).then(d => {
            if (d.success) {
                const row = document.getElementById('lead-row-' + leadId);
                if (row) row.remove();
                showHypeToast('🗑️ Lead #' + leadId + ' moved to Trash');
            }
        });
    }

    // Restore from Trash
    function hypeRestoreTrash(leadId) {
        fetch(ajaxurl, {
            method: 'POST',
            headers: {'Content-Type': 'application/x-www-form-urlencoded'},
            body: new URLSearchParams({
                action: 'hype_restore_trash',
                lead_id: leadId,
                security: ajaxNonce
            })
        }).then(res => res.json()).then(d => {
            if (d.success) {
                const row = document.getElementById('lead-row-' + leadId);
                if (row) row.remove();
                showHypeToast('♻️ Lead #' + leadId + ' restored to Active');
            }
        });
    }

    // Delete Permanently
    function hypeDeletePermanent(leadId) {
        if (!confirm('Permanently delete Lead #' + leadId + '? This cannot be undone.')) return;
        fetch(ajaxurl, {
            method: 'POST',
            headers: {'Content-Type': 'application/x-www-form-urlencoded'},
            body: new URLSearchParams({
                action: 'hype_delete_permanent',
                lead_id: leadId,
                security: ajaxNonce
            })
        }).then(res => res.json()).then(d => {
            if (d.success) {
                const row = document.getElementById('lead-row-' + leadId);
                if (row) row.remove();
                showHypeToast('❌ Lead permanently deleted');
            }
        });
    }

    // Mark Spam
    function hypeToggleSpam(leadId, isSpam) {
        if (!confirm('Mark Lead #' + leadId + ' as Spam?')) return;
        fetch(ajaxurl, {
            method: 'POST',
            headers: {'Content-Type': 'application/x-www-form-urlencoded'},
            body: new URLSearchParams({
                action: 'hype_toggle_spam',
                lead_id: leadId,
                is_spam: isSpam,
                security: ajaxNonce
            })
        }).then(res => res.json()).then(d => {
            if (d.success) {
                const row = document.getElementById('lead-row-' + leadId);
                if (row) row.remove();
                showHypeToast('🛑 Marked as Spam');
            }
        });
    }

    // Bulk Actions
    function hypeExecuteBulkAction() {
        const action = document.getElementById('hypeBulkActionSelect').value;
        if (!action) {
            alert('Please select an action.');
            return;
        }

        const selected = Array.from(document.querySelectorAll('.hype-row-cb:checked')).map(cb => cb.value);
        if (selected.length === 0) {
            alert('Please select at least one lead from the spreadsheet.');
            return;
        }

        if (action === 'delete' && !confirm('Permanently delete ' + selected.length + ' leads?')) return;

        const formData = new URLSearchParams();
        formData.append('action', 'hype_bulk_action');
        formData.append('bulk_action', action);
        formData.append('security', ajaxNonce);
        selected.forEach(id => formData.append('lead_ids[]', id));

        fetch(ajaxurl, {
            method: 'POST',
            headers: {'Content-Type': 'application/x-www-form-urlencoded'},
            body: formData
        }).then(res => res.json()).then(d => {
            if (d.success) {
                window.location.reload();
            } else {
                alert('Action failed.');
            }
        });
    }

    // Details Modal
    function hypeOpenDetailModal(lead) {
        currentModalLeadId = lead.id;
        document.getElementById('hypeModalLeadTitle').innerText = 'Lead #' + lead.id + ' — ' + (lead.customer_name || 'Website Inquiry');
        document.getElementById('hypeModalSubtitle').innerText = 'Submitted on ' + lead.created_at;

        // Quick action buttons in modal
        const cleanP = (lead.phone || '').replace(/\D/g, '');
        const waPhone = cleanP.length === 10 ? ('91' + cleanP) : cleanP;
        const custName = lead.customer_name || 'Website Visitor';
        
        document.getElementById('hypeModalCustNamePhone').innerHTML = '<strong>' + custName + '</strong> &bull; <a href="tel:' + lead.phone + '" style="color:#ee1d23; text-decoration:none; font-weight:700;">' + lead.phone + '</a>';
        
        let quickBtnsHtml = '';
        quickBtnsHtml += '<a href="https://wa.me/' + waPhone + '?text=' + encodeURIComponent('Hello ' + custName + ', regarding your moving inquiry with Amar Packers & Movers:') + '" target="_blank" class="hype-btn hype-btn-green" style="padding:6px 12px; font-size:12px;">💬 WhatsApp</a>';
        quickBtnsHtml += '<a href="tel:' + lead.phone + '" class="hype-btn hype-btn-dark" style="padding:6px 12px; font-size:12px;">📞 Call</a>';
        document.getElementById('hypeModalQuickBtns').innerHTML = quickBtnsHtml;

        let html = '<table style="width:100%; font-size:13px; border-collapse:collapse;">';
        html += '<tr style="border-bottom:1px solid #f1f5f9;"><td style="padding:8px 0; color:#64748b; width:130px;"><strong>Status:</strong></td><td><span class="hype-status-tag status-' + lead.status + '">' + lead.status.toUpperCase() + '</span></td></tr>';
        html += '<tr style="border-bottom:1px solid #f1f5f9;"><td style="padding:8px 0; color:#64748b;"><strong>Priority:</strong></td><td><span class="hype-priority-tag priority-' + lead.priority + '">' + lead.priority.toUpperCase() + '</span></td></tr>';
        if (lead.email) html += '<tr style="border-bottom:1px solid #f1f5f9;"><td style="padding:8px 0; color:#64748b;"><strong>Email:</strong></td><td><a href="mailto:' + lead.email + '" style="color:#2563eb;">' + lead.email + '</a></td></tr>';
        if (lead.moving_from || lead.moving_to) html += '<tr style="border-bottom:1px solid #f1f5f9;"><td style="padding:8px 0; color:#64748b;"><strong>Route:</strong></td><td><strong style="color:#0f172a;">' + (lead.moving_from || '') + '</strong> ➔ <strong style="color:#0f172a;">' + (lead.moving_to || '') + '</strong></td></tr>';
        if (lead.move_size) html += '<tr style="border-bottom:1px solid #f1f5f9;"><td style="padding:8px 0; color:#64748b;"><strong>Move Size:</strong></td><td><span class="hype-badge-size">' + lead.move_size + '</span></td></tr>';
        if (lead.move_date) html += '<tr style="border-bottom:1px solid #f1f5f9;"><td style="padding:8px 0; color:#64748b;"><strong>Move Date:</strong></td><td>' + lead.move_date + '</td></tr>';
        if (lead.estimated_price) html += '<tr style="border-bottom:1px solid #f1f5f9;"><td style="padding:8px 0; color:#64748b;"><strong>Estimated Quote:</strong></td><td><span class="hype-badge-cost">' + lead.estimated_price + '</span></td></tr>';
        if (lead.service_type) html += '<tr style="border-bottom:1px solid #f1f5f9;"><td style="padding:8px 0; color:#64748b;"><strong>Service Type:</strong></td><td>' + lead.service_type + '</td></tr>';
        if (lead.address) html += '<tr style="border-bottom:1px solid #f1f5f9;"><td style="padding:8px 0; color:#64748b;"><strong>Pickup Address:</strong></td><td>' + lead.address + '</td></tr>';
        if (lead.message) html += '<tr style="border-bottom:1px solid #f1f5f9;"><td style="padding:8px 0; color:#64748b;"><strong>Message / Details:</strong></td><td style="background:#f8fafc; padding:8px 12px; border-radius:6px; color:#334155;">' + lead.message + '</td></tr>';
        html += '<tr><td style="padding:8px 0; color:#64748b;"><strong>Lead Source:</strong></td><td><span class="hype-source-badge">' + lead.source + '</span></td></tr>';
        html += '</table>';

        document.getElementById('hypeModalContent').innerHTML = html;
        document.getElementById('hypeModalNotes').value = lead.notes || '';
        document.getElementById('hypeLeadModal').style.display = 'flex';
    }

    function hypeCloseModal() {
        document.getElementById('hypeLeadModal').style.display = 'none';
    }

    function hypeSaveNotes() {
        const notes = document.getElementById('hypeModalNotes').value;
        fetch(ajaxurl, {
            method: 'POST',
            headers: {'Content-Type': 'application/x-www-form-urlencoded'},
            body: new URLSearchParams({
                action: 'hype_save_notes',
                lead_id: currentModalLeadId,
                notes: notes,
                security: ajaxNonce
            })
        }).then(res => res.json()).then(d => {
            if (d.success) {
                showHypeToast('✓ Staff notes saved successfully');
                hypeCloseModal();
            } else {
                alert('Failed to save notes.');
            }
        });
    }

    // Manual Add Modal
    function hypeOpenManualModal() {
        document.getElementById('hypeManualLeadModal').style.display = 'flex';
    }

    function hypeCloseManualModal() {
        document.getElementById('hypeManualLeadModal').style.display = 'none';
    }

    function hypeSubmitManualLead(e) {
        e.preventDefault();
        const form = document.getElementById('hypeManualLeadForm');
        const formData = new FormData(form);
        formData.append('action', 'hype_add_manual_lead');
        formData.append('security', ajaxNonce);

        fetch(ajaxurl, {
            method: 'POST',
            body: formData
        }).then(res => res.json()).then(d => {
            if (d.success) {
                showHypeToast('✓ Lead added to spreadsheet!');
                setTimeout(() => { window.location.reload(); }, 600);
            } else {
                alert(d.data?.message || 'Failed to add lead');
            }
        });
    }
    </script>
    <?php
}
