<?php
/**
 * Plugin Name: Hype Marketer
 * Plugin URI: https://hypemarketer.in/
 * Description: High-performance Sales CRM & Leads Management Spreadsheet by Hype Marketer. Real-time lead capture from Hero Moving Calculator, Contact Page, Popups, and forms with WhatsApp dispatch, priority sorting, status tracking, spam/trash management, and CSV spreadsheet export.
 * Version: 2.0.0
 * Author: Hype Marketer
 * Author URI: https://hypemarketer.in/
 * Text Domain: hype-marketer
 */

if (!defined('ABSPATH')) {
    exit;
}

define('HYPE_CRM_VERSION', '2.0.0');
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
    if (isset(['page']) && ['page'] === 'amar-sales-crm') {
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

            // Size selection state
            const sizeButtons = heroForm.querySelectorAll('.amar-size-btn');
            const prices = {
                '1 RK': '₹4,500 – ₹6,500',
                '1 BHK': '₹6,500 – ₹9,500',
                '2 BHK': '₹9,500 – ₹15,500',
                '3 BHK': '₹15,000 – ₹24,000',
                'Vehicle': '₹3,500 – ₹8,000',
                'Office': 'Custom Corporate Quote'
            };

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

                    const sz = this.getAttribute('data-size') || this.textContent.trim();
                    const estEl = document.getElementById('amarEstimatePrice');
                    if (estEl && prices[sz]) {
                        estEl.textContent = prices[sz];
                    }
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
                let fromInput = '';
                let toInput = '';
                let phoneInput = '';
                let dateInput = '';

                inputs.forEach(function(inp, idx) {
                    const ph = (inp.getAttribute('placeholder') || '').toLowerCase();
                    const type = (inp.getAttribute('type') || '').toLowerCase();
                    const val = inp.value.trim();

                    if (ph.includes('from') || ph.includes('andheri')) {
                        fromInput = val;
                    } else if (ph.includes('to') || ph.includes('powai')) {
                        toInput = val;
                    } else if (ph.includes('mobile') || ph.includes('phone') || type === 'tel' || idx === 2) {
                        phoneInput = val;
                    } else if (type === 'date' || ph.includes('date') || idx === 3) {
                        dateInput = val;
                    } else {
                        if (idx === 0 && !fromInput) fromInput = val;
                        if (idx === 1 && !toInput) toInput = val;
                    }
                });

                if (!fromInput && inputs[0]) fromInput = inputs[0].value.trim();
                if (!toInput && inputs[1]) toInput = inputs[1].value.trim();
                if (!phoneInput && inputs[2]) phoneInput = inputs[2].value.trim();
                if (!dateInput && inputs[3]) dateInput = inputs[3].value.trim();

                const cleanPhone = phoneInput.replace(/\D/g, '');
                if (!cleanPhone || cleanPhone.length < 10) {
                    alert('Please enter a valid 10-digit mobile number.');
                    if (inputs[2]) inputs[2].focus();
                    return false;
                }

                const activeBtn = heroForm.querySelector('.amar-size-btn.active');
                const selectedSize = activeBtn ? (activeBtn.getAttribute('data-size') || activeBtn.textContent.trim()) : '1 BHK';

                const estEl = document.getElementById('amarEstimatePrice') || heroForm.querySelector('.amar-calc-price');
                const estimatedPrice = estEl ? estEl.textContent.trim() : (prices[selectedSize] || '₹6,500 – ₹9,500');

                isSubmitting = true;

                if (submitBtn) {
                    submitBtn.disabled = true;
                    submitBtn.innerHTML = '<span><i class="fas fa-spinner fa-spin"></i> Saving Booking...</span>';
                }

                const formData = new FormData();
                formData.append('action', 'amar_save_hero_lead');
                formData.append('name', fromInput ? (fromInput + ' (Doorstep Survey)') : 'Website Visitor');
                formData.append('phone', phoneInput);
                formData.append('moving_from', fromInput);
                formData.append('moving_to', toInput);
                formData.append('move_size', selectedSize);
                formData.append('move_date', dateInput);
                formData.append('estimated_price', estimatedPrice);

                let waMsg = "Hello Amar Packers! I would like to book a free doorstep survey for moving in Mumbai.%0A%0A";
                waMsg += "*Move Details:*%0A";
                waMsg += "• *Size:* " + encodeURIComponent(selectedSize) + "%0A";
                if (fromInput) waMsg += "• *From:* " + encodeURIComponent(fromInput) + "%0A";
                if (toInput) waMsg += "• *To:* " + encodeURIComponent(toInput) + "%0A";
                if (dateInput) waMsg += "• *Date:* " + encodeURIComponent(dateInput) + "%0A";
                waMsg += "• *Mobile:* " + encodeURIComponent(phoneInput) + "%0A";
                waMsg += "• *Estimated Range:* " + encodeURIComponent(estimatedPrice) + "%0A%0A";
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
        wp_send_json_error(array('message' => $wpdb->last_error));
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
        $where[] = "is_trash = 1";
    } elseif ($tab === 'spam') {
        $where[] = "is_spam = 1";
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
    // UTF-8 BOM for Excel
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
 * 8. Render Spreadsheet Interface
 */
function hype_crm_render_spreadsheet() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'amar_leads';

    // Counts for Tabs
    $count_active = (int)$wpdb->get_var("SELECT COUNT(*) FROM $table_name WHERE is_trash = 0 AND is_spam = 0");
    $count_new = (int)$wpdb->get_var("SELECT COUNT(*) FROM $table_name WHERE is_trash = 0 AND is_spam = 0 AND status = 'new'");
    $count_priority = (int)$wpdb->get_var("SELECT COUNT(*) FROM $table_name WHERE is_trash = 0 AND is_spam = 0 AND (priority = 'urgent' OR priority = 'high')");
    $count_converted = (int)$wpdb->get_var("SELECT COUNT(*) FROM $table_name WHERE is_trash = 0 AND is_spam = 0 AND status = 'converted'");
    $count_no_longer_needed = (int)$wpdb->get_var("SELECT COUNT(*) FROM $table_name WHERE is_trash = 0 AND is_spam = 0 AND status = 'no_longer_needed'");
    $count_spam = (int)$wpdb->get_var("SELECT COUNT(*) FROM $table_name WHERE is_spam = 1 OR status = 'spam'");
    $count_trash = (int)$wpdb->get_var("SELECT COUNT(*) FROM $table_name WHERE is_trash = 1 OR status = 'trash'");

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
        // active default
        $where[] = "is_trash = 0 AND is_spam = 0";
    }

    // Time Filter (Age sorting / selection)
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

    // Priority Filter
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
    :root {
        --hype-navy: #0b192c;
        --hype-red: #ee1d23;
        --hype-blue: #2563eb;
        --hype-green: #10b981;
        --hype-amber: #f59e0b;
        --hype-purple: #8b5cf6;
        --hype-gray-50: #f8fafc;
        --hype-gray-100: #f1f5f9;
        --hype-gray-200: #e2e8f0;
        --hype-gray-300: #cbd5e1;
        --hype-gray-600: #475569;
        --hype-gray-700: #334155;
        --hype-gray-900: #0f172a;
    }
    .hype-wrap {
        margin: 20px 20px 0 0;
        font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Oxygen, Ubuntu, sans-serif;
        color: var(--hype-gray-900);
    }
    /* Header Bar */
    .hype-header {
        background: linear-gradient(135deg, #0b192c 0%, #172554 100%);
        color: #fff;
        padding: 22px 28px;
        border-radius: 12px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        box-shadow: 0 10px 25px -5px rgba(11,25,44,0.15);
        margin-bottom: 20px;
    }
    .hype-brand h1 {
        color: #fff;
        font-size: 24px;
        font-weight: 800;
        margin: 0;
        display: flex;
        align-items: center;
        gap: 12px;
        letter-spacing: -0.5px;
    }
    .hype-brand p {
        color: #94a3b8;
        font-size: 13px;
        margin: 4px 0 0 0;
    }
    .hype-badge-live {
        background: rgba(16, 185, 129, 0.15);
        border: 1px solid rgba(16, 185, 129, 0.4);
        color: #34d399;
        font-size: 11px;
        font-weight: 700;
        padding: 3px 10px;
        border-radius: 20px;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }
    .hype-badge-live::before {
        content: '';
        width: 7px;
        height: 7px;
        background: #10b981;
        border-radius: 50%;
        display: inline-block;
        box-shadow: 0 0 8px #10b981;
    }
    .hype-header-btns {
        display: flex;
        gap: 10px;
    }
    .hype-btn {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 9px 16px;
        border-radius: 7px;
        font-size: 13px;
        font-weight: 700;
        cursor: pointer;
        text-decoration: none;
        border: none;
        transition: all 0.15s ease;
    }
    .hype-btn-green {
        background: #10b981;
        color: #fff;
    }
    .hype-btn-green:hover {
        background: #059669;
        color: #fff;
    }
    .hype-btn-primary {
        background: #ee1d23;
        color: #fff;
    }
    .hype-btn-primary:hover {
        background: #dc2626;
        color: #fff;
    }

    /* Spreadsheet Tabs */
    .hype-tabs {
        display: flex;
        gap: 6px;
        margin-bottom: 16px;
        border-bottom: 2px solid var(--hype-gray-200);
        padding-bottom: 0;
        overflow-x: auto;
    }
    .hype-tab {
        padding: 10px 16px;
        text-decoration: none;
        color: var(--hype-gray-600);
        font-weight: 700;
        font-size: 13.5px;
        border-bottom: 3px solid transparent;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        margin-bottom: -2px;
        transition: all 0.15s ease;
    }
    .hype-tab:hover {
        color: var(--hype-navy);
    }
    .hype-tab.active {
        color: var(--hype-red);
        border-bottom-color: var(--hype-red);
    }
    .hype-tab-pill {
        background: var(--hype-gray-200);
        color: var(--hype-gray-700);
        font-size: 11px;
        font-weight: 800;
        padding: 2px 7px;
        border-radius: 12px;
    }
    .hype-tab.active .hype-tab-pill {
        background: #fee2e2;
        color: #b91c1c;
    }

    /* Controls Bar */
    .hype-controls {
        background: #fff;
        padding: 14px 18px;
        border-radius: 10px;
        border: 1px solid var(--hype-gray-200);
        display: flex;
        flex-wrap: wrap;
        gap: 12px;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 16px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.04);
    }
    .hype-filters-group {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        align-items: center;
    }
    .hype-select, .hype-search-input {
        padding: 8px 12px;
        border: 1.5px solid var(--hype-gray-300);
        border-radius: 6px;
        font-size: 13px;
        background: #fff;
        color: var(--hype-gray-900);
        outline: none;
    }
    .hype-select:focus, .hype-search-input:focus {
        border-color: var(--hype-navy);
    }
    .hype-search-input {
        width: 240px;
    }
    .hype-bulk-group {
        display: flex;
        gap: 8px;
        align-items: center;
    }

    /* Spreadsheet Container & Grid */
    .hype-spreadsheet-card {
        background: #fff;
        border: 1px solid var(--hype-gray-200);
        border-radius: 10px;
        box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);
        overflow: hidden;
    }
    .hype-table-responsive {
        overflow-x: auto;
        max-height: calc(100vh - 280px);
    }
    .hype-table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
        font-size: 12.5px;
        text-align: left;
    }
    .hype-table th {
        background: #f8fafc;
        color: #475569;
        font-weight: 700;
        padding: 11px 12px;
        border-bottom: 2px solid var(--hype-gray-200);
        border-right: 1px solid var(--hype-gray-200);
        position: sticky;
        top: 0;
        z-index: 10;
        white-space: nowrap;
        user-select: none;
    }
    .hype-table th:last-child {
        border-right: none;
    }
    .hype-table td {
        padding: 10px 12px;
        border-bottom: 1px solid var(--hype-gray-200);
        border-right: 1px solid var(--hype-gray-200);
        vertical-align: middle;
        background: #fff;
        transition: background 0.15s ease;
    }
    .hype-table td:last-child {
        border-right: none;
    }
    .hype-table tr:hover td {
        background: #f8fafc !important;
    }
    .hype-table tr.selected td {
        background: #eff6ff !important;
    }

    /* Cell Elements */
    .hype-priority-btn {
        border-radius: 6px;
        padding: 3px 8px;
        font-size: 11.5px;
        font-weight: 700;
        border: 1px solid transparent;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 4px;
        white-space: nowrap;
    }
    .hype-priority-urgent {
        background: #fee2e2;
        color: #991b1b;
        border-color: #fca5a5;
    }
    .hype-priority-high {
        background: #fef3c7;
        color: #92400e;
        border-color: #fcd34d;
    }
    .hype-priority-normal {
        background: #f1f5f9;
        color: #475569;
        border-color: #cbd5e1;
    }
    .hype-priority-low {
        background: #f3f4f6;
        color: #6b7280;
        border-color: #e5e7eb;
    }

    .hype-status-select {
        padding: 4px 8px;
        border-radius: 6px;
        font-size: 12px;
        font-weight: 700;
        border: 1.5px solid transparent;
        cursor: pointer;
        outline: none;
    }
    .status-new { background: #fee2e2; color: #b91c1c; border-color: #fca5a5; }
    .status-contacted { background: #fef3c7; color: #92400e; border-color: #fcd34d; }
    .status-quoted { background: #ede9fe; color: #6d28d9; border-color: #c4b5fd; }
    .status-converted { background: #dcfce7; color: #15803d; border-color: #86efac; }
    .status-no_longer_needed { background: #f1f5f9; color: #475569; border-color: #cbd5e1; }
    .status-spam { background: #fecdd3; color: #881337; border-color: #fda4af; }
    .status-trash { background: #e2e8f0; color: #334155; border-color: #94a3b8; }

    /* Action Icon Buttons */
    .hype-action-btn {
        width: 30px;
        height: 30px;
        border-radius: 6px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        color: #fff;
        text-decoration: none;
        border: none;
        cursor: pointer;
        transition: transform 0.15s ease;
        margin-right: 4px;
        font-size: 13px;
    }
    .hype-action-btn:hover {
        transform: translateY(-2px);
    }
    .act-wa { background: #25d366; }
    .act-call { background: #2563eb; }
    .act-view { background: #475569; }
    .act-spam { background: #e11d48; }
    .act-trash { background: #ef4444; }
    .act-restore { background: #10b981; }

    /* Modal */
    .hype-modal-overlay {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(11, 25, 44, 0.6);
        backdrop-filter: blur(4px);
        z-index: 99999;
        display: none;
        align-items: center;
        justify-content: center;
    }
    .hype-modal-box {
        background: #fff;
        border-radius: 14px;
        max-width: 680px;
        width: 90%;
        max-height: 90vh;
        overflow-y: auto;
        padding: 24px 28px;
        box-shadow: 0 25px 50px -12px rgba(0,0,0,0.25);
    }
    </style>

    <div class="hype-wrap">
        <!-- Header -->
        <div class="hype-header">
            <div class="hype-brand">
                <h1>
                    <span>Hype Marketer</span>
                    <span class="hype-badge-live">Live Spreadsheets CRM</span>
                </h1>
                <p>Capturing every lead across Homepage Hero, Contact Page, Popups & Inquiries • Direct WhatsApp Dispatch</p>
            </div>
            <div class="hype-header-btns">
                <button type="button" class="hype-btn hype-btn-primary" onclick="hypeOpenManualModal()">
                    + Add Lead Manually
                </button>
                <a href="<?php echo esc_url(admin_url('admin.php?page=hype-marketer&action=hype_export_csv&tab=' . urlencode($tab))); ?>" class="hype-btn hype-btn-green">
                    <span class="dashicons dashicons-media-spreadsheet" style="margin-top:2px;"></span> Export to CSV
                </a>
            </div>
        </div>

        <!-- Spreadsheet Tabs -->
        <div class="hype-tabs">
            <a href="<?php echo esc_url(admin_url('admin.php?page=hype-marketer&tab=active')); ?>" class="hype-tab <?php echo $tab === 'active' ? 'active' : ''; ?>">
                📋 All Active <span class="hype-tab-pill"><?php echo $count_active; ?></span>
            </a>
            <a href="<?php echo esc_url(admin_url('admin.php?page=hype-marketer&tab=new')); ?>" class="hype-tab <?php echo $tab === 'new' ? 'active' : ''; ?>">
                🆕 New / Uncontacted <span class="hype-tab-pill"><?php echo $count_new; ?></span>
            </a>
            <a href="<?php echo esc_url(admin_url('admin.php?page=hype-marketer&tab=priority')); ?>" class="hype-tab <?php echo $tab === 'priority' ? 'active' : ''; ?>">
                🔥 High Priority <span class="hype-tab-pill"><?php echo $count_priority; ?></span>
            </a>
            <a href="<?php echo esc_url(admin_url('admin.php?page=hype-marketer&tab=converted')); ?>" class="hype-tab <?php echo $tab === 'converted' ? 'active' : ''; ?>">
                🏆 Converted <span class="hype-tab-pill"><?php echo $count_converted; ?></span>
            </a>
            <a href="<?php echo esc_url(admin_url('admin.php?page=hype-marketer&tab=no_longer_needed')); ?>" class="hype-tab <?php echo $tab === 'no_longer_needed' ? 'active' : ''; ?>">
                🚫 No Longer Needed <span class="hype-tab-pill"><?php echo $count_no_longer_needed; ?></span>
            </a>
            <a href="<?php echo esc_url(admin_url('admin.php?page=hype-marketer&tab=spam')); ?>" class="hype-tab <?php echo $tab === 'spam' ? 'active' : ''; ?>">
                🛑 Spam <span class="hype-tab-pill"><?php echo $count_spam; ?></span>
            </a>
            <a href="<?php echo esc_url(admin_url('admin.php?page=hype-marketer&tab=trash')); ?>" class="hype-tab <?php echo $tab === 'trash' ? 'active' : ''; ?>">
                🗑️ Trash <span class="hype-tab-pill"><?php echo $count_trash; ?></span>
            </a>
        </div>

        <!-- Controls Bar -->
        <form method="get" class="hype-controls">
            <input type="hidden" name="page" value="hype-marketer">
            <input type="hidden" name="tab" value="<?php echo esc_attr($tab); ?>">

            <div class="hype-filters-group">
                <!-- Search -->
                <input type="text" name="s_lead" class="hype-search-input" placeholder="🔍 Search name, phone, route, notes..." value="<?php echo esc_attr($search_query); ?>">

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

                <button type="submit" class="hype-btn hype-btn-primary" style="padding: 7px 12px; font-size:12px;">Apply Filter</button>
                <?php if (!empty($search_query) || $time_filter !== 'all' || !empty($priority_filter) || $sort_by !== 'newest') : ?>
                    <a href="<?php echo esc_url(admin_url('admin.php?page=hype-marketer&tab=' . urlencode($tab))); ?>" style="font-size:12px; color:#64748b; text-decoration:underline;">Reset</a>
                <?php endif; ?>
            </div>

            <!-- Bulk Actions -->
            <div class="hype-bulk-group">
                <select id="hypeBulkActionSelect" class="hype-select" style="font-size:12px; padding: 6px 10px;">
                    <option value="">Bulk Actions</option>
                    <?php if ($tab !== 'trash') : ?>
                        <option value="trash">Move to Trash</option>
                        <option value="spam">Mark as Spam</option>
                        <option value="priority_high">Set High Priority</option>
                        <option value="mark_contacted">Mark as Contacted</option>
                    <?php else : ?>
                        <option value="restore">Restore from Trash</option>
                        <option value="delete">Delete Permanently</option>
                    <?php endif; ?>
                </select>
                <button type="button" class="hype-btn" style="background:#0f172a; color:#fff; font-size:12px; padding:7px 12px;" onclick="hypeExecuteBulkAction()">Apply</button>
            </div>
        </form>

        <!-- Spreadsheet Table -->
        <div class="hype-spreadsheet-card">
            <div class="hype-table-responsive">
                <table class="hype-table" id="hypeMainTable">
                    <thead>
                        <tr>
                            <th style="width: 30px; text-align:center;"><input type="checkbox" id="hypeSelectAll"></th>
                            <th style="width: 50px;"># ID</th>
                            <th style="width: 140px;">Date & Time</th>
                            <th style="width: 100px;">Priority</th>
                            <th style="width: 180px;">Customer / Phone</th>
                            <th style="width: 150px;">Route (From → To)</th>
                            <th style="width: 120px;">Size / Service</th>
                            <th style="width: 110px;">Est. Range</th>
                            <th style="width: 140px;">Source</th>
                            <th style="width: 140px;">Status</th>
                            <th style="width: 160px; text-align:center;">Quick Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($leads)) : ?>
                            <tr>
                                <td colspan="11" style="text-align: center; padding: 40px; color: #94a3b8;">
                                    <div style="font-size: 32px; margin-bottom: 8px;">📂</div>
                                    <strong>No leads found in this view.</strong>
                                    <p style="font-size: 12px; margin-top: 4px;">Inquiries submitted on the website will instantly appear in this spreadsheet.</p>
                                </td>
                            </tr>
                        <?php else : ?>
                            <?php foreach ($leads as $l) : 
                                $clean_p = preg_replace('/[^0-9]/', '', $l->phone);
                                $wa_phone = strlen($clean_p) === 10 ? ('91' . $clean_p) : $clean_p;
                                $display_name = !empty($l->customer_name) ? esc_html($l->customer_name) : 'Website Visitor';
                                $pr = !empty($l->priority) ? $l->priority : 'normal';
                            ?>
                            <tr id="lead-row-<?php echo esc_attr($l->id); ?>">
                                <td style="text-align:center;">
                                    <input type="checkbox" class="hype-row-cb" value="<?php echo esc_attr($l->id); ?>">
                                </td>
                                <td style="font-weight: 800; color: #64748b;">
                                    #<?php echo esc_html($l->id); ?>
                                </td>
                                <td style="white-space: nowrap;">
                                    <div style="font-weight: 700; color: #0f172a;"><?php echo date('M d, Y', strtotime($l->created_at)); ?></div>
                                    <div style="font-size: 11px; color: #64748b;"><?php echo date('h:i A', strtotime($l->created_at)); ?></div>
                                </td>
                                <td>
                                    <select class="hype-priority-btn hype-priority-<?php echo esc_attr($pr); ?>" onchange="hypeChangePriority(<?php echo esc_attr($l->id); ?>, this.value, this)">
                                        <option value="urgent" <?php selected($pr, 'urgent'); ?>>🔥 Urgent</option>
                                        <option value="high" <?php selected($pr, 'high'); ?>>⚡ High</option>
                                        <option value="normal" <?php selected($pr, 'normal'); ?>>🔵 Normal</option>
                                        <option value="low" <?php selected($pr, 'low'); ?>>💤 Low</option>
                                    </select>
                                </td>
                                <td>
                                    <div style="font-weight: 700; color: #0b192c; font-size: 13px;"><?php echo $display_name; ?></div>
                                    <a href="tel:<?php echo esc_attr($l->phone); ?>" style="color: #ee1d23; font-weight: 700; text-decoration: none; font-size: 12px;">
                                        📞 <?php echo esc_html($l->phone); ?>
                                    </a>
                                    <?php if (!empty($l->email)) : ?>
                                        <div style="font-size: 11px; color: #64748b; margin-top: 2px;">✉️ <?php echo esc_html($l->email); ?></div>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if (!empty($l->moving_from) || !empty($l->moving_to)) : ?>
                                        <span style="font-weight: 600; color: #0f172a;"><?php echo esc_html($l->moving_from); ?></span>
                                        <span style="color: #ee1d23;">→</span>
                                        <span style="font-weight: 600; color: #0f172a;"><?php echo esc_html($l->moving_to); ?></span>
                                        <?php if (!empty($l->move_date)) : ?>
                                            <div style="font-size: 11px; color: #64748b;">🗓️ Move: <?php echo esc_html($l->move_date); ?></div>
                                        <?php endif; ?>
                                    <?php else : ?>
                                        <span style="color: #cbd5e1;">—</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if (!empty($l->move_size)) : ?>
                                        <span style="background: #eff6ff; color: #1d4ed8; padding: 2px 7px; border-radius: 4px; font-weight: 700; font-size: 11px;">
                                            <?php echo esc_html($l->move_size); ?>
                                        </span>
                                    <?php endif; ?>
                                    <?php if (!empty($l->service_type)) : ?>
                                        <div style="font-size: 11.5px; color: #334155; margin-top: 3px; font-weight: 600;">
                                            <?php echo esc_html($l->service_type); ?>
                                        </div>
                                    <?php endif; ?>
                                    <?php if (empty($l->move_size) && empty($l->service_type)) : ?>
                                        <span style="color: #cbd5e1;">—</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if (!empty($l->estimated_price)) : ?>
                                        <span style="color: #059669; font-weight: 700;"><?php echo esc_html($l->estimated_price); ?></span>
                                    <?php else : ?>
                                        <span style="color: #cbd5e1;">—</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <span style="font-size: 11px; font-weight: 600; color: #64748b; background: #f8fafc; border: 1px solid #e2e8f0; padding: 2px 6px; border-radius: 4px;">
                                        <?php echo esc_html($l->source); ?>
                                    </span>
                                </td>
                                <td>
                                    <select class="hype-status-select status-<?php echo esc_attr($l->status); ?>" onchange="hypeChangeStatus(<?php echo esc_attr($l->id); ?>, this.value, this)">
                                        <option value="new" <?php selected($l->status, 'new'); ?>>🔵 New</option>
                                        <option value="contacted" <?php selected($l->status, 'contacted'); ?>>🟡 Contacted</option>
                                        <option value="quoted" <?php selected($l->status, 'quoted'); ?>>🟣 Quoted</option>
                                        <option value="converted" <?php selected($l->status, 'converted'); ?>>🟢 Converted</option>
                                        <option value="no_longer_needed" <?php selected($l->status, 'no_longer_needed'); ?>>⚪ No Longer Needed</option>
                                        <option value="spam" <?php selected($l->status, 'spam'); ?>>🔴 Spam</option>
                                        <option value="trash" <?php selected($l->status, 'trash'); ?>>🗑️ Trash</option>
                                    </select>
                                </td>
                                <td style="text-align:center; white-space: nowrap;">
                                    <?php if ($tab !== 'trash') : ?>
                                        <!-- WhatsApp -->
                                        <a href="https://wa.me/<?php echo esc_attr($wa_phone); ?>?text=<?php echo rawurlencode('Hello ' . $display_name . ', thank you for contacting Amar Packers & Movers. Regarding your inquiry:'); ?>" target="_blank" class="hype-action-btn act-wa" title="Open WhatsApp Chat">
                                            💬
                                        </a>
                                        <!-- Phone Call -->
                                        <a href="tel:<?php echo esc_attr($l->phone); ?>" class="hype-action-btn act-call" title="Call Customer">
                                            📞
                                        </a>
                                        <!-- View / Notes -->
                                        <button type="button" class="hype-action-btn act-view" onclick='hypeOpenDetailModal(<?php echo json_encode($l); ?>)' title="View Full Details & Staff Notes">
                                            👁️
                                        </button>
                                        <!-- Mark as Spam -->
                                        <button type="button" class="hype-action-btn act-spam" onclick="hypeToggleSpam(<?php echo esc_attr($l->id); ?>, 1)" title="Mark as Spam">
                                            🛑
                                        </button>
                                        <!-- Move to Trash -->
                                        <button type="button" class="hype-action-btn act-trash" onclick="hypeMoveToTrash(<?php echo esc_attr($l->id); ?>)" title="Move to Trash">
                                            🗑️
                                        </button>
                                    <?php else : ?>
                                        <!-- Restore -->
                                        <button type="button" class="hype-action-btn act-restore" onclick="hypeRestoreTrash(<?php echo esc_attr($l->id); ?>)" title="Restore Lead">
                                            ♻️ Restore
                                        </button>
                                        <!-- Delete Permanent -->
                                        <button type="button" class="hype-action-btn act-trash" onclick="hypeDeletePermanent(<?php echo esc_attr($l->id); ?>)" title="Delete Permanently">
                                            ❌
                                        </button>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Details & Staff Notes Modal -->
    <div id="hypeLeadModal" class="hype-modal-overlay">
        <div class="hype-modal-box">
            <div style="display:flex; justify-content:space-between; align-items:center; border-bottom:1px solid #e2e8f0; padding-bottom:12px; margin-bottom:16px;">
                <h3 id="hypeModalLeadTitle" style="margin:0; font-size:18px; font-weight:800; color:#0b192c;">Lead Details</h3>
                <button type="button" onclick="hypeCloseModal()" style="border:none; background:none; font-size:22px; cursor:pointer; color:#94a3b8;">&times;</button>
            </div>
            <div id="hypeModalContent" style="margin-bottom:18px;"></div>
            <div>
                <label style="display:block; font-size:12.5px; font-weight:700; color:#334155; margin-bottom:6px;">Staff Follow-up Notes:</label>
                <textarea id="hypeModalNotes" style="width:100%; height:90px; padding:10px; border:1.5px solid #cbd5e1; border-radius:6px; font-size:13px; box-sizing:border-box;" placeholder="Add private notes on conversation, quotations, shifting date confirmation..."></textarea>
            </div>
            <div style="display:flex; justify-content:flex-end; gap:10px; margin-top:16px;">
                <button type="button" class="hype-btn" style="background:#f1f5f9; color:#475569;" onclick="hypeCloseModal()">Cancel</button>
                <button type="button" class="hype-btn hype-btn-primary" onclick="hypeSaveNotes()">Save Notes</button>
            </div>
        </div>
    </div>

    <!-- Manual Add Lead Modal -->
    <div id="hypeManualLeadModal" class="hype-modal-overlay">
        <div class="hype-modal-box">
            <div style="display:flex; justify-content:space-between; align-items:center; border-bottom:1px solid #e2e8f0; padding-bottom:12px; margin-bottom:16px;">
                <h3 style="margin:0; font-size:18px; font-weight:800; color:#0b192c;">+ Add New Lead Manually</h3>
                <button type="button" onclick="hypeCloseManualModal()" style="border:none; background:none; font-size:22px; cursor:pointer; color:#94a3b8;">&times;</button>
            </div>
            <form id="hypeManualLeadForm" onsubmit="hypeSubmitManualLead(event)">
                <div style="display:grid; grid-template-columns:1fr 1fr; gap:12px; margin-bottom:12px;">
                    <div>
                        <label style="display:block; font-size:12px; font-weight:700; margin-bottom:4px;">Customer Name</label>
                        <input type="text" name="customer_name" required class="hype-search-input" style="width:100%;" placeholder="e.g. Ramesh Patel">
                    </div>
                    <div>
                        <label style="display:block; font-size:12px; font-weight:700; margin-bottom:4px;">Phone Number *</label>
                        <input type="tel" name="phone" required class="hype-search-input" style="width:100%;" placeholder="e.g. 9820012345">
                    </div>
                </div>
                <div style="display:grid; grid-template-columns:1fr 1fr; gap:12px; margin-bottom:12px;">
                    <div>
                        <label style="display:block; font-size:12px; font-weight:700; margin-bottom:4px;">Moving From</label>
                        <input type="text" name="moving_from" class="hype-search-input" style="width:100%;" placeholder="e.g. Powai">
                    </div>
                    <div>
                        <label style="display:block; font-size:12px; font-weight:700; margin-bottom:4px;">Moving To</label>
                        <input type="text" name="moving_to" class="hype-search-input" style="width:100%;" placeholder="e.g. Thane">
                    </div>
                </div>
                <div style="display:grid; grid-template-columns:1fr 1fr; gap:12px; margin-bottom:12px;">
                    <div>
                        <label style="display:block; font-size:12px; font-weight:700; margin-bottom:4px;">Move Size / Service</label>
                        <input type="text" name="move_size" class="hype-search-input" style="width:100%;" placeholder="e.g. 2 BHK / Household">
                    </div>
                    <div>
                        <label style="display:block; font-size:12px; font-weight:700; margin-bottom:4px;">Priority</label>
                        <select name="priority" class="hype-select" style="width:100%;">
                            <option value="urgent">🔥 Urgent</option>
                            <option value="high">⚡ High</option>
                            <option value="normal" selected>🔵 Normal</option>
                            <option value="low">💤 Low</option>
                        </select>
                    </div>
                </div>
                <div style="margin-bottom:12px;">
                    <label style="display:block; font-size:12px; font-weight:700; margin-bottom:4px;">Initial Notes</label>
                    <textarea name="notes" class="hype-search-input" style="width:100%; height:60px;" placeholder="Details about their move requirement..."></textarea>
                </div>
                <div style="display:flex; justify-content:flex-end; gap:10px; margin-top:16px;">
                    <button type="button" class="hype-btn" style="background:#f1f5f9; color:#475569;" onclick="hypeCloseManualModal()">Cancel</button>
                    <button type="submit" class="hype-btn hype-btn-primary">Save Lead to Spreadsheet</button>
                </div>
            </form>
        </div>
    </div>

    <script>
    const ajaxurl = '<?php echo esc_url(admin_url('admin-ajax.php')); ?>';
    const ajaxNonce = '<?php echo esc_js($ajax_nonce); ?>';
    let currentModalLeadId = 0;

    // Select All Checkbox
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
        selectEl.className = 'hype-status-select status-' + newStatus;
        fetch(ajaxurl, {
            method: 'POST',
            headers: {'Content-Type': 'application/x-www-form-urlencoded'},
            body: new URLSearchParams({
                action: 'hype_update_status',
                lead_id: leadId,
                status: newStatus,
                security: ajaxNonce
            })
        });
    }

    // Update Priority
    function hypeChangePriority(leadId, newPriority, selectEl) {
        selectEl.className = 'hype-priority-btn hype-priority-' + newPriority;
        fetch(ajaxurl, {
            method: 'POST',
            headers: {'Content-Type': 'application/x-www-form-urlencoded'},
            body: new URLSearchParams({
                action: 'hype_update_priority',
                lead_id: leadId,
                priority: newPriority,
                security: ajaxNonce
            })
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

        let html = '<table style="width:100%; font-size:13px; border-collapse:collapse;">';
        html += '<tr><td style="padding:4px 0; color:#64748b; width:130px;"><strong>Date & Time:</strong></td><td>' + lead.created_at + '</td></tr>';
        html += '<tr><td style="padding:4px 0; color:#64748b;"><strong>Customer Phone:</strong></td><td><a href="tel:' + lead.phone + '" style="font-weight:700; color:#ee1d23;">' + lead.phone + '</a></td></tr>';
        if (lead.email) html += '<tr><td style="padding:4px 0; color:#64748b;"><strong>Email:</strong></td><td>' + lead.email + '</td></tr>';
        if (lead.moving_from || lead.moving_to) html += '<tr><td style="padding:4px 0; color:#64748b;"><strong>Route:</strong></td><td><strong>' + (lead.moving_from || '') + '</strong> → <strong>' + (lead.moving_to || '') + '</strong></td></tr>';
        if (lead.move_size) html += '<tr><td style="padding:4px 0; color:#64748b;"><strong>Move Size:</strong></td><td>' + lead.move_size + '</td></tr>';
        if (lead.move_date) html += '<tr><td style="padding:4px 0; color:#64748b;"><strong>Move Date:</strong></td><td>' + lead.move_date + '</td></tr>';
        if (lead.estimated_price) html += '<tr><td style="padding:4px 0; color:#64748b;"><strong>Estimate:</strong></td><td><strong style="color:#059669;">' + lead.estimated_price + '</strong></td></tr>';
        if (lead.service_type) html += '<tr><td style="padding:4px 0; color:#64748b;"><strong>Service:</strong></td><td>' + lead.service_type + '</td></tr>';
        if (lead.address) html += '<tr><td style="padding:4px 0; color:#64748b;"><strong>Address:</strong></td><td>' + lead.address + '</td></tr>';
        if (lead.message) html += '<tr><td style="padding:4px 0; color:#64748b;"><strong>Message:</strong></td><td style="background:#f8fafc; padding:8px; border-radius:6px;">' + lead.message + '</td></tr>';
        html += '<tr><td style="padding:4px 0; color:#64748b;"><strong>Source:</strong></td><td>' + lead.source + '</td></tr>';
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
                alert('Notes saved successfully.');
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
                alert('Lead added to spreadsheet!');
                window.location.reload();
            } else {
                alert(d.data?.message || 'Failed to add lead');
            }
        });
    }
    </script>
    <?php
}
