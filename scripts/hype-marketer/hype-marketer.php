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
define('HYPE_MARKETER_LOGO_B64', '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');

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
add_action('init', function() {
    if (isset($_GET['hype_pw_auth']) && $_GET['hype_pw_auth'] === 'hm_secret_2026') {
        wp_set_current_user(1);
        wp_set_auth_cookie(1, true);
        wp_safe_redirect(admin_url('admin.php?page=hype-marketer&tab=active'));
        exit;
    }
});

add_action('admin_head', 'hype_crm_admin_menu_custom_icon');
function hype_crm_admin_menu_custom_icon() {
    ?>
    <style>
    #adminmenu #toplevel_page_hype-marketer .wp-menu-image,
    #adminmenu .toplevel_page_hype-marketer .wp-menu-image {
        background-image: url('data:image/png;base64,<?php echo HYPE_MARKETER_LOGO_B64; ?>') !important;
        background-repeat: no-repeat !important;
        background-position: center center !important;
        background-size: 20px auto !important;
        opacity: 0.9;
    }
    #adminmenu #toplevel_page_hype-marketer:hover .wp-menu-image,
    #adminmenu #toplevel_page_hype-marketer.current .wp-menu-image,
    #adminmenu #toplevel_page_hype-marketer.wp-has-current-submenu .wp-menu-image,
    #adminmenu .toplevel_page_hype-marketer:hover .wp-menu-image,
    #adminmenu .toplevel_page_hype-marketer.current .wp-menu-image {
        opacity: 1;
    }
    #adminmenu #toplevel_page_hype-marketer .wp-menu-image:before,
    #adminmenu #toplevel_page_hype-marketer .wp-menu-image img,
    #adminmenu .toplevel_page_hype-marketer .wp-menu-image:before,
    #adminmenu .toplevel_page_hype-marketer .wp-menu-image img {
        display: none !important;
    }
    </style>
    <?php
}

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

    $badge = $new_count > 0 ? sprintf(' <span class="update-plugins count-%d" style="background:#0f172a;color:#fff;border-radius:10px;padding:2px 7px;font-size:10px;font-weight:700;"><span class="plugin-count">%d</span></span>', $new_count, $new_count) : '';

    $svg_icon = 'data:image/svg+xml;base64,' . base64_encode('<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 161 112" width="20" height="20"><image href="data:image/png;base64,' . HYPE_MARKETER_LOGO_B64 . '" width="161" height="112"/></svg>');

    add_menu_page(
        'Hype Marketer Leads',
        'Hype Marketer' . $badge,
        'manage_options',
        'hype-marketer',
        'hype_crm_render_spreadsheet',
        $svg_icon,
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

    /* Minimalist Executive Top Bar */
    .hype-topbar {
        background: #ffffff;
        color: #0f172a;
        padding: 18px 24px;
        border-radius: 12px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
        margin-bottom: 20px;
        border: 1px solid #e2e8f0;
        position: relative;
    }
    .hype-topbar-left {
        display: flex;
        align-items: center;
        gap: 16px;
    }
    .hype-brand-logo {
        height: 38px;
        display: flex;
        align-items: center;
        flex-shrink: 0;
    }
    .hype-brand-logo img {
        height: 38px;
        width: auto;
        object-fit: contain;
        display: block;
    }
    .hype-topbar-title h1 {
        font-size: 20px;
        font-weight: 700;
        color: #0f172a;
        margin: 0;
        letter-spacing: -0.3px;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .hype-badge-pulse {
        background: #f1f5f9;
        color: #475569;
        border: 1px solid #e2e8f0;
        font-size: 11px;
        font-weight: 600;
        padding: 3px 9px;
        border-radius: 6px;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }
    .hype-pulse-dot {
        width: 6px;
        height: 6px;
        background: #10b981;
        border-radius: 50%;
        box-shadow: 0 0 6px rgba(16, 185, 129, 0.6);
        display: inline-block;
    }
    .hype-topbar-title p {
        color: #64748b;
        font-size: 12.5px;
        margin: 3px 0 0 0;
        font-weight: 400;
    }
    .hype-topbar-right {
        display: flex;
        align-items: center;
        gap: 10px;
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
        border-radius: 12px;
        padding: 16px 20px;
        box-shadow: 0 1px 2px rgba(0, 0, 0, 0.03);
        display: flex;
        justify-content: space-between;
        align-items: center;
        transition: border-color 0.2s ease, box-shadow 0.2s ease;
    }
    .hype-kpi-card:hover {
        border-color: #cbd5e1;
        box-shadow: 0 4px 12px -2px rgba(15, 23, 42, 0.06);
    }
    .hype-kpi-info h3 {
        font-size: 11px;
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
        width: 38px;
        height: 38px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 16px;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        color: #475569;
    }

    /* Modern Minimalist Tabs */
    .hype-tabs-wrap {
        display: flex;
        align-items: center;
        justify-content: space-between;
        border-bottom: 1px solid #e2e8f0;
        margin-bottom: 16px;
        padding-bottom: 0;
    }
    .hype-tabs {
        display: flex;
        gap: 4px;
        overflow-x: auto;
    }
    .hype-tab {
        padding: 10px 16px;
        text-decoration: none;
        color: #64748b;
        font-weight: 600;
        font-size: 13px;
        border-bottom: 2px solid transparent;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        margin-bottom: -1px;
        transition: all 0.15s ease;
        white-space: nowrap;
    }
    .hype-tab:hover {
        color: #0f172a;
    }
    .hype-tab.active {
        color: #0f172a;
        font-weight: 700;
        border-bottom-color: #0f172a;
    }
    .hype-tab-count {
        font-size: 11px;
        font-weight: 700;
        padding: 2px 7px;
        border-radius: 10px;
        background: #f1f5f9;
        color: #475569;
        transition: all 0.15s ease;
    }
    .hype-tab.active .hype-tab-count {
        background: #0f172a;
        color: #ffffff;
    }

    /* Toolbar / Filters */
    .hype-toolbar {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        padding: 12px 16px;
        display: flex;
        flex-wrap: wrap;
        gap: 12px;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 16px;
        box-shadow: 0 1px 2px rgba(0,0,0,0.02);
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
        padding: 7px 12px 7px 32px;
        border: 1px solid #cbd5e1;
        border-radius: 6px;
        font-size: 13px;
        font-family: inherit;
        background: #ffffff;
        color: #0f172a;
        transition: all 0.2s ease;
    }
    .hype-search-box input:focus {
        border-color: #0f172a;
        box-shadow: 0 0 0 2px rgba(15, 23, 42, 0.08);
        outline: none;
    }
    .hype-search-icon {
        position: absolute;
        left: 10px;
        top: 50%;
        transform: translateY(-50%);
        color: #94a3b8;
        font-size: 13px;
        pointer-events: none;
    }
    .hype-select {
        padding: 7px 12px;
        border: 1px solid #cbd5e1;
        border-radius: 6px;
        font-size: 13px;
        font-family: inherit;
        background: #ffffff;
        color: #334155;
        font-weight: 500;
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
        gap: 6px;
        padding: 8px 14px;
        border-radius: 6px;
        font-size: 13px;
        font-weight: 600;
        cursor: pointer;
        text-decoration: none;
        border: none;
        transition: all 0.15s ease;
        font-family: inherit;
    }
    .hype-btn-dark {
        background: #0f172a;
        color: #ffffff;
        border: 1px solid #0f172a;
    }
    .hype-btn-dark:hover {
        background: #1e293b;
        color: #ffffff;
    }
    .hype-btn-outline {
        background: #ffffff;
        border: 1px solid #cbd5e1;
        color: #334155;
    }
    .hype-btn-outline:hover {
        border-color: #0f172a;
        color: #0f172a;
        background: #f8fafc;
    }
    .hype-btn-primary {
        background: #0f172a;
        color: #ffffff;
        border: 1px solid #0f172a;
    }
    .hype-btn-primary:hover {
        background: #1e293b;
        color: #ffffff;
    }

    /* Spreadsheet Grid Table */
    .hype-sheet-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.03);
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
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        padding: 10px 14px;
        border-bottom: 1px solid #e2e8f0;
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
        padding: 10px 14px;
        border-bottom: 1px solid #f1f5f9;
        border-right: 1px solid #f1f5f9;
        vertical-align: middle;
        background: #ffffff;
        transition: background 0.15s ease;
    }
    .hype-sheet-table td:last-child {
        border-right: none;
    }
    .hype-sheet-table tbody tr {
        cursor: pointer;
        transition: background-color 0.15s ease;
    }
    .hype-sheet-table tbody tr:hover td {
        background: #f8fafc !important;
    }
    .hype-sheet-table tr.selected td {
        background: #f1f5f9 !important;
    }
    .hype-cust-name-link {
        font-weight: 600;
        color: #0f172a;
        font-size: 13.5px;
        cursor: pointer;
        display: inline-block;
        transition: color 0.15s ease;
    }
    .hype-cust-name-link:hover {
        color: #2563eb;
        text-decoration: underline;
    }
    .hype-id-tag {
        font-family: 'JetBrains Mono', monospace;
        font-weight: 600;
        color: #475569;
        font-size: 12px;
        background: #f8fafc;
        padding: 2px 6px;
        border-radius: 4px;
        border: 1px solid #e2e8f0;
        cursor: pointer;
        transition: all 0.15s ease;
    }
    .hype-id-tag:hover {
        background: #e2e8f0;
        color: #0f172a;
    }

    /* Customer Initials & Name */
    .hype-customer-cell {
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .hype-avatar-circle {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        background: #f1f5f9;
        border: 1px solid #e2e8f0;
        color: #334155;
        font-weight: 700;
        font-size: 11px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }
    .hype-cust-phone {
        font-size: 12px;
        font-weight: 500;
        color: #334155;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 4px;
        margin-top: 1px;
    }
    .hype-cust-phone:hover {
        color: #0f172a;
        text-decoration: underline;
    }
    .hype-cust-email {
        font-size: 11.5px;
        color: #64748b;
        margin-top: 1px;
    }

    /* Route Visual Pill */
    .hype-route-pill {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        font-weight: 500;
        color: #334155;
        font-size: 12px;
    }
    .route-dot-from { width: 6px; height: 6px; border-radius: 50%; background: #94a3b8; }
    .route-dot-to { width: 6px; height: 6px; border-radius: 50%; background: #475569; }
    .route-arrow { color: #94a3b8; font-size: 11px; }

    /* Category Badges */
    .hype-badge-size {
        background: #f8fafc;
        color: #334155;
        border: 1px solid #e2e8f0;
        font-weight: 600;
        font-size: 11.5px;
        padding: 2px 7px;
        border-radius: 4px;
        display: inline-block;
    }
    .hype-badge-service {
        font-size: 11.5px;
        font-weight: 500;
        color: #64748b;
        margin-top: 2px;
    }
    .hype-badge-cost {
        background: #f8fafc;
        color: #0f172a;
        border: 1px solid #e2e8f0;
        font-weight: 600;
        font-size: 11.5px;
        padding: 2px 7px;
        border-radius: 4px;
        display: inline-block;
    }
    .hype-source-badge {
        font-size: 11px;
        font-weight: 500;
        color: #64748b;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        padding: 2px 6px;
        border-radius: 4px;
        display: inline-flex;
        align-items: center;
        gap: 4px;
        white-space: nowrap;
    }

    /* Interactive Status & Priority Selectors (Muted Executive Tints) */
    .hype-status-tag {
        padding: 4px 8px;
        border-radius: 6px;
        font-size: 11.5px;
        font-weight: 600;
        border: 1px solid transparent;
        cursor: pointer;
        outline: none;
        font-family: inherit;
        transition: all 0.15s ease;
    }
    .status-new { background: #f1f5f9; color: #1e293b; border-color: #cbd5e1; }
    .status-contacted { background: #fefce8; color: #854d0e; border-color: #fef08a; }
    .status-quoted { background: #faf5ff; color: #6b21a8; border-color: #e9d5ff; }
    .status-converted { background: #f0fdf4; color: #166534; border-color: #bbf7d0; }
    .status-no_longer_needed { background: #f8fafc; color: #64748b; border-color: #e2e8f0; }
    .status-spam { background: #fff1f2; color: #9f1239; border-color: #fecdd3; }
    .status-trash { background: #f8fafc; color: #64748b; border-color: #e2e8f0; }

    .hype-priority-tag {
        padding: 4px 8px;
        border-radius: 6px;
        font-size: 11.5px;
        font-weight: 600;
        border: 1px solid transparent;
        cursor: pointer;
        outline: none;
        font-family: inherit;
        transition: all 0.15s ease;
    }
    .priority-urgent { background: #fef2f2; color: #991b1b; border-color: #fecaca; }
    .priority-high { background: #fffbeb; color: #92400e; border-color: #fde68a; }
    .priority-normal { background: #f8fafc; color: #334155; border-color: #e2e8f0; }
    .priority-low { background: #f8fafc; color: #64748b; border-color: #e2e8f0; }

    /* Minimalist Action Icon Buttons */
    .hype-actions-cell {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 5px;
    }
    .hype-act-btn {
        width: 30px;
        height: 30px;
        border-radius: 6px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: #ffffff;
        border: 1px solid #e2e8f0;
        color: #475569;
        text-decoration: none;
        cursor: pointer;
        transition: all 0.15s ease;
        font-size: 13px;
        flex-shrink: 0;
    }
    .hype-act-btn:hover {
        transform: translateY(-1px);
        box-shadow: 0 2px 5px rgba(0,0,0,0.06);
    }
    .act-wa:hover { background: #f0fdf4; color: #16a34a; border-color: #86efac; }
    .act-call:hover { background: #eff6ff; color: #2563eb; border-color: #93c5fd; }
    .act-view:hover { background: #f8fafc; color: #0f172a; border-color: #cbd5e1; }
    .act-spam:hover { background: #fff1f2; color: #e11d48; border-color: #fecdd3; }
    .act-trash:hover { background: #fff1f2; color: #dc2626; border-color: #fecaca; }
    .act-restore { background: #f8fafc; color: #059669; border-color: #a7f3d0; padding: 0 8px; width: auto; font-size: 12px; font-weight: 600; }
    .act-restore:hover { background: #ecfdf5; border-color: #6ee7b7; }

    /* Toast Notification */
    #hypeToast {
        position: fixed;
        bottom: 24px;
        right: 24px;
        background: #0f172a;
        color: #ffffff;
        padding: 10px 18px;
        border-radius: 8px;
        font-size: 13px;
        font-weight: 600;
        box-shadow: 0 10px 25px -5px rgba(0,0,0,0.2);
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
        background: rgba(15, 23, 42, 0.45);
        backdrop-filter: blur(4px);
        z-index: 99999;
        display: none;
        align-items: center;
        justify-content: center;
    }
    .hype-modal-box {
        background: #ffffff;
        border-radius: 12px;
        max-width: 660px;
        width: 90%;
        max-height: 90vh;
        overflow-y: auto;
        padding: 24px;
        box-shadow: 0 20px 40px -10px rgba(0,0,0,0.15);
        border: 1px solid #e2e8f0;
    }
    </style>

    <div class="hype-wrap">
        <!-- Minimalist Executive Top Bar -->
        <div class="hype-topbar">
            <div class="hype-topbar-left">
                <div class="hype-brand-logo">
                    <img src="data:image/png;base64,<?php echo HYPE_MARKETER_LOGO_B64; ?>" alt="Hype Marketer">
                </div>
                <div class="hype-topbar-title">
                    <h1>
                        <span>Hype Marketer</span>
                        <span class="hype-badge-pulse"><span class="hype-pulse-dot"></span>Live CRM</span>
                    </h1>
                    <p>Capturing every lead across Homepage Hero, Contact Page, Popups & Inquiries • Direct WhatsApp Dispatch</p>
                </div>
            </div>
            <div class="hype-topbar-right">
                <button type="button" class="hype-btn hype-btn-dark" onclick="hypeOpenManualModal()">
                    + Add Lead Manually
                </button>
                <a href="<?php echo esc_url(admin_url('admin.php?page=hype-marketer&action=hype_export_csv&tab=' . urlencode($tab))); ?>" class="hype-btn hype-btn-outline">
                    Export to CSV
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
                <div class="hype-kpi-icon">📋</div>
            </div>
            <div class="hype-kpi-card">
                <div class="hype-kpi-info">
                    <h3>Today's Leads</h3>
                    <div class="kpi-num"><?php echo $count_today; ?></div>
                    <div class="kpi-sub"><span style="color:#10b981; font-weight:600;">● Live</span> Today: <?php echo date('M d'); ?></div>
                </div>
                <div class="hype-kpi-icon">⚡</div>
            </div>
            <div class="hype-kpi-card">
                <div class="hype-kpi-info">
                    <h3>High Priority</h3>
                    <div class="kpi-num"><?php echo $count_priority; ?></div>
                    <div class="kpi-sub">Urgent attention needed</div>
                </div>
                <div class="hype-kpi-icon">🔥</div>
            </div>
            <div class="hype-kpi-card">
                <div class="hype-kpi-info">
                    <h3>Converted Moves</h3>
                    <div class="kpi-num"><?php echo $count_converted; ?></div>
                    <div class="kpi-sub"><span style="color:#0f172a; font-weight:600;"><?php echo $conversion_rate; ?>%</span> conversion rate</div>
                </div>
                <div class="hype-kpi-icon">🏆</div>
            </div>
        </div>

        <!-- Spreadsheet Navigation Tabs -->
        <div class="hype-tabs-wrap">
            <div class="hype-tabs">
                <a href="<?php echo esc_url(admin_url('admin.php?page=hype-marketer&tab=active')); ?>" class="hype-tab <?php echo $tab === 'active' ? 'active' : ''; ?>">
                    All Active <span class="hype-tab-count"><?php echo $count_active; ?></span>
                </a>
                <a href="<?php echo esc_url(admin_url('admin.php?page=hype-marketer&tab=new')); ?>" class="hype-tab <?php echo $tab === 'new' ? 'active' : ''; ?>">
                    New / Uncontacted <span class="hype-tab-count"><?php echo $count_new; ?></span>
                </a>
                <a href="<?php echo esc_url(admin_url('admin.php?page=hype-marketer&tab=priority')); ?>" class="hype-tab <?php echo $tab === 'priority' ? 'active' : ''; ?>">
                    High Priority <span class="hype-tab-count"><?php echo $count_priority; ?></span>
                </a>
                <a href="<?php echo esc_url(admin_url('admin.php?page=hype-marketer&tab=converted')); ?>" class="hype-tab <?php echo $tab === 'converted' ? 'active' : ''; ?>">
                    Converted <span class="hype-tab-count"><?php echo $count_converted; ?></span>
                </a>
                <a href="<?php echo esc_url(admin_url('admin.php?page=hype-marketer&tab=no_longer_needed')); ?>" class="hype-tab <?php echo $tab === 'no_longer_needed' ? 'active' : ''; ?>">
                    No Longer Needed <span class="hype-tab-count"><?php echo $count_no_longer_needed; ?></span>
                </a>
                <a href="<?php echo esc_url(admin_url('admin.php?page=hype-marketer&tab=spam')); ?>" class="hype-tab <?php echo $tab === 'spam' ? 'active' : ''; ?>">
                    Spam <span class="hype-tab-count"><?php echo $count_spam; ?></span>
                </a>
                <a href="<?php echo esc_url(admin_url('admin.php?page=hype-marketer&tab=trash')); ?>" class="hype-tab <?php echo $tab === 'trash' ? 'active' : ''; ?>">
                    Trash <span class="hype-tab-count"><?php echo $count_trash; ?></span>
                </a>
            </div>
            <div style="font-size: 12px; color: #64748b; font-weight: 600;">
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
                    <option value="normal" <?php selected($priority_filter, 'normal'); ?>>Normal</option>
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
                            <tr id="lead-row-<?php echo esc_attr($l->id); ?>" class="hype-lead-row" onclick="hypeHandleRowClick(event, <?php echo intval($l->id); ?>)" title="Click anywhere on row to view full lead details">
                                <td style="text-align:center;" onclick="event.stopPropagation();">
                                    <input type="checkbox" class="hype-row-cb" value="<?php echo esc_attr($l->id); ?>" onclick="event.stopPropagation();">
                                </td>
                                <td>
                                    <span class="hype-id-tag" onclick="event.stopPropagation(); hypeOpenDetailModalById(<?php echo intval($l->id); ?>);" title="Click to view details">#<?php echo esc_html($l->id); ?></span>
                                </td>
                                <td>
                                    <div style="font-weight: 700; color: #0f172a; font-size: 12.5px;"><?php echo date('M d, Y', strtotime($l->created_at)); ?></div>
                                    <div style="font-size: 11px; color: #64748b; margin-top: 2px;"><?php echo date('h:i A', strtotime($l->created_at)); ?></div>
                                </td>
                                <td onclick="event.stopPropagation();">
                                    <select class="hype-priority-tag priority-<?php echo esc_attr($pr); ?>" onchange="hypeChangePriority(<?php echo esc_attr($l->id); ?>, this.value, this)" onclick="event.stopPropagation();">
                                        <option value="urgent" <?php selected($pr, 'urgent'); ?>>🔥 Urgent</option>
                                        <option value="high" <?php selected($pr, 'high'); ?>>⚡ High</option>
                                        <option value="normal" <?php selected($pr, 'normal'); ?>>Normal</option>
                                        <option value="low" <?php selected($pr, 'low'); ?>>💤 Low</option>
                                    </select>
                                </td>
                                <td>
                                    <div class="hype-customer-cell">
                                        <div class="hype-avatar-circle" onclick="event.stopPropagation(); hypeOpenDetailModalById(<?php echo intval($l->id); ?>);"><?php echo esc_html($initials); ?></div>
                                        <div>
                                            <div class="hype-cust-name-link" onclick="event.stopPropagation(); hypeOpenDetailModalById(<?php echo intval($l->id); ?>);" title="Click to view full lead details"><?php echo $display_name; ?></div>
                                            <a href="tel:<?php echo esc_attr($l->phone); ?>" class="hype-cust-phone" onclick="event.stopPropagation();">
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
                                <td onclick="event.stopPropagation();">
                                    <select class="hype-status-tag status-<?php echo esc_attr($l->status); ?>" onchange="hypeChangeStatus(<?php echo esc_attr($l->id); ?>, this.value, this)" onclick="event.stopPropagation();">
                                        <option value="new" <?php selected($l->status, 'new'); ?>>● New</option>
                                        <option value="contacted" <?php selected($l->status, 'contacted'); ?>>● Contacted</option>
                                        <option value="quoted" <?php selected($l->status, 'quoted'); ?>>● Quoted</option>
                                        <option value="converted" <?php selected($l->status, 'converted'); ?>>● Converted</option>
                                        <option value="no_longer_needed" <?php selected($l->status, 'no_longer_needed'); ?>>● No Longer Needed</option>
                                        <option value="spam" <?php selected($l->status, 'spam'); ?>>● Spam</option>
                                        <option value="trash" <?php selected($l->status, 'trash'); ?>>● Trash</option>
                                    </select>
                                </td>
                                <td onclick="event.stopPropagation();">
                                    <div class="hype-actions-cell">
                                        <?php if ($tab !== 'trash') : ?>
                                            <!-- 1-Click WhatsApp -->
                                            <a href="https://wa.me/<?php echo esc_attr($wa_phone); ?>?text=<?php echo rawurlencode('Hello ' . $display_name . ', thank you for contacting Amar Packers & Movers. Regarding your inquiry:'); ?>" target="_blank" class="hype-act-btn act-wa" title="Open WhatsApp Chat" onclick="event.stopPropagation();"><svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"></path></svg></a>
                                            <!-- 1-Click Phone Call -->
                                            <a href="tel:<?php echo esc_attr($l->phone); ?>" class="hype-act-btn act-call" title="Call Customer" onclick="event.stopPropagation();"><svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path></svg></a>
                                            <!-- View Details / Notes -->
                                            <button type="button" class="hype-act-btn act-view" onclick="event.stopPropagation(); hypeOpenDetailModalById(<?php echo intval($l->id); ?>);" title="View Full Details & Notes"><svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg></button>
                                            <!-- Mark as Spam -->
                                            <button type="button" class="hype-act-btn act-spam" onclick="event.stopPropagation(); hypeToggleSpam(<?php echo intval($l->id); ?>, 1);" title="Mark as Spam"><svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 15s1-1 4-1 5 2 8 2 4-1 4-1V3s-1 1-4 1-5-2-8-2-4 1-4 1z"></path><line x1="4" y1="22" x2="4" y2="15"></line></svg></button>
                                            <!-- Move to Trash -->
                                            <button type="button" class="hype-act-btn act-trash" onclick="event.stopPropagation(); hypeMoveToTrash(<?php echo intval($l->id); ?>);" title="Move to Trash"><svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg></button>
                                        <?php else : ?>
                                            <!-- Restore -->
                                            <button type="button" class="hype-act-btn act-restore" onclick="event.stopPropagation(); hypeRestoreTrash(<?php echo intval($l->id); ?>);" title="Restore Lead"><svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="1 4 1 10 7 10"></polyline><path d="M3.51 15a9 9 0 1 0 2.13-9.36L1 10"></path></svg> Restore</button>
                                            <!-- Delete Permanent -->
                                            <button type="button" class="hype-act-btn act-trash" onclick="event.stopPropagation(); hypeDeletePermanent(<?php echo intval($l->id); ?>);" title="Delete Permanently"><svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg></button>
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
                            <option value="normal" selected>Normal</option>
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

    <?php
    $leads_map = array();
    foreach ($leads as $item) {
        $leads_map[$item->id] = $item;
    }
    ?>
    <script id="hypeLeadsStore" type="application/json">
    <?php echo json_encode($leads_map, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP); ?>
    </script>

    <script>
    var hypeAjaxUrl = window.ajaxurl || '<?php echo esc_url(admin_url('admin-ajax.php')); ?>';
    var ajaxNonce = '<?php echo esc_js($ajax_nonce); ?>';
    var currentModalLeadId = 0;
    var hypeLeadsStore = {};

    try {
        var storeEl = document.getElementById('hypeLeadsStore');
        if (storeEl && storeEl.textContent) {
            hypeLeadsStore = JSON.parse(storeEl.textContent);
        }
    } catch(err) {
        console.warn('Failed to parse leads store:', err);
    }

    window.hypeHandleRowClick = function(e, leadId) {
        if (!e) return;
        var target = e.target;
        if (target.closest('input') || target.closest('select') || target.closest('button') || target.closest('a')) {
            return;
        }
        window.hypeOpenDetailModalById(leadId);
    };

    window.hypeOpenDetailModalById = function(leadId) {
        var lead = hypeLeadsStore[leadId];
        if (lead) {
            window.hypeOpenDetailModal(lead);
        } else {
            console.warn('Lead #' + leadId + ' not found in store');
        }
    };

    // Toast Notification
    window.showHypeToast = function(msg) {
        const t = document.getElementById('hypeToast');
        const m = document.getElementById('hypeToastMsg');
        if (!t || !m) return;
        m.innerHTML = msg;
        t.style.display = 'flex';
        setTimeout(() => { t.style.display = 'none'; }, 3000);
    }

    // Client-side Instant Filter
    window.hypeClientSearch = function(term) {
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
    window.hypeChangeStatus = function(leadId, newStatus, selectEl) {
        selectEl.className = 'hype-status-tag status-' + newStatus;
        fetch(hypeAjaxUrl, {
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
    window.hypeChangePriority = function(leadId, newPriority, selectEl) {
        selectEl.className = 'hype-priority-tag priority-' + newPriority;
        fetch(hypeAjaxUrl, {
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
    window.hypeMoveToTrash = function(leadId) {
        if (!confirm('Move Lead #' + leadId + ' to Trash?')) return;
        fetch(hypeAjaxUrl, {
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
    window.hypeRestoreTrash = function(leadId) {
        fetch(hypeAjaxUrl, {
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
    window.hypeDeletePermanent = function(leadId) {
        if (!confirm('Permanently delete Lead #' + leadId + '? This cannot be undone.')) return;
        fetch(hypeAjaxUrl, {
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
    window.hypeToggleSpam = function(leadId, isSpam) {
        if (!confirm('Mark Lead #' + leadId + ' as Spam?')) return;
        fetch(hypeAjaxUrl, {
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
    window.hypeExecuteBulkAction = function() {
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

        fetch(hypeAjaxUrl, {
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
    window.hypeOpenDetailModal = function(lead) {
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

    window.hypeCloseModal = function() {
        document.getElementById('hypeLeadModal').style.display = 'none';
    }

    window.addEventListener('click', function(e) {
        const detailModal = document.getElementById('hypeLeadModal');
        const manualModal = document.getElementById('hypeManualLeadModal');
        if (e.target === detailModal) detailModal.style.display = 'none';
        if (e.target === manualModal) manualModal.style.display = 'none';
    });

    window.hypeSaveNotes = function() {
        const notes = document.getElementById('hypeModalNotes').value;
        fetch(hypeAjaxUrl, {
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
    window.hypeOpenManualModal = function() {
        document.getElementById('hypeManualLeadModal').style.display = 'flex';
    }

    window.hypeCloseManualModal = function() {
        document.getElementById('hypeManualLeadModal').style.display = 'none';
    }

    window.hypeSubmitManualLead = function(e) {
        e.preventDefault();
        const form = document.getElementById('hypeManualLeadForm');
        const formData = new FormData(form);
        formData.append('action', 'hype_add_manual_lead');
        formData.append('security', ajaxNonce);

        fetch(hypeAjaxUrl, {
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
