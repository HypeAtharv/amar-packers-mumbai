import subprocess

with open('scripts/hype_logo_b64.txt', 'r', encoding='utf-8') as f:
    logo_b64 = f.read().strip()

with open('plugins/hype-marketer.php', 'r', encoding='utf-8') as f:
    content = f.read()

# 1. Add HYPE_MARKETER_LOGO_B64 constant
const_target = "define('HYPE_CRM_URL', plugin_dir_url(__FILE__));"
const_replacement = "define('HYPE_CRM_URL', plugin_dir_url(__FILE__));\ndefine('HYPE_MARKETER_LOGO_B64', '" + logo_b64 + "');"

if const_target in content:
    content = content.replace(const_target, const_replacement, 1)
    print("1. Added HYPE_MARKETER_LOGO_B64 constant")
else:
    print("WARNING: const_target not found")

# 2. Add admin_head hook for custom sidebar icon
menu_hook_target = "add_action('admin_menu', 'hype_crm_register_admin_menu');"
admin_head_hook = """add_action('admin_head', 'hype_crm_admin_menu_custom_icon');
function hype_crm_admin_menu_custom_icon() {
    ?>
    <style>
    #adminmenu #toplevel_page_hype-marketer .wp-menu-image {
        background-image: url('data:image/png;base64,<?php echo HYPE_MARKETER_LOGO_B64; ?>') !important;
        background-repeat: no-repeat !important;
        background-position: center 8px !important;
        background-size: 18px auto !important;
    }
    #adminmenu #toplevel_page_hype-marketer .wp-menu-image:before {
        display: none !important;
    }
    </style>
    <?php
}

add_action('admin_menu', 'hype_crm_register_admin_menu');"""

if menu_hook_target in content:
    content = content.replace(menu_hook_target, admin_head_hook, 1)
    print("2. Added admin_head hook for sidebar icon")
else:
    print("WARNING: menu_hook_target not found")

# 3. Update menu badge style to clean slate
badge_target = """$badge = $new_count > 0 ? sprintf(' <span class="update-plugins count-%d" style="background:#ee1d23;color:#fff;border-radius:10px;padding:2px 7px;font-size:11px;font-weight:700;"><span class="plugin-count">%d</span></span>', $new_count, $new_count) : '';"""
badge_replacement = """$badge = $new_count > 0 ? sprintf(' <span class="update-plugins count-%d" style="background:#0f172a;color:#fff;border-radius:10px;padding:2px 7px;font-size:10px;font-weight:700;"><span class="plugin-count">%d</span></span>', $new_count, $new_count) : '';"""

if badge_target in content:
    content = content.replace(badge_target, badge_replacement, 1)
    print("3. Updated sidebar menu badge to clean slate")
else:
    print("WARNING: badge_target not found")

# 4. Replace CSS block
style_start = content.find("<style>", content.find("function hype_crm_render_spreadsheet"))
style_end = content.find("</style>", style_start) + len("</style>")

clean_css = """<style>
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
    </style>"""

content = content[:style_start] + clean_css + content[style_end:]
print("4. Replaced complete style block cleanly")

# 5. Replace Topbar HTML
topbar_start = content.find("<!-- Executive Top Bar -->")
topbar_end = content.find("<!-- 4 KPI Summary Cards -->")

clean_topbar = """<!-- Minimalist Executive Top Bar -->
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

        """

if topbar_start != -1 and topbar_end != -1:
    content = content[:topbar_start] + clean_topbar + content[topbar_end:]
    print("5. Replaced Topbar HTML")
else:
    print(f"WARNING: topbar_start={topbar_start}, topbar_end={topbar_end}")

# 6. Replace KPI HTML
kpi_start = content.find("<!-- 4 KPI Summary Cards -->")
kpi_end = content.find("<!-- Spreadsheet Navigation Tabs -->")

clean_kpi = """<!-- 4 KPI Summary Cards -->
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

        """

if kpi_start != -1 and kpi_end != -1:
    content = content[:kpi_start] + clean_kpi + content[kpi_end:]
    print("6. Replaced KPI HTML")
else:
    print(f"WARNING: kpi_start={kpi_start}, kpi_end={kpi_end}")

# 7. Replace Tabs HTML (remove emoji badges & red underline)
tabs_start = content.find("<!-- Spreadsheet Navigation Tabs -->")
tabs_end = content.find("<!-- Toolbar / Filters -->")

clean_tabs = """<!-- Spreadsheet Navigation Tabs -->
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

        """

if tabs_start != -1 and tabs_end != -1:
    content = content[:tabs_start] + clean_tabs + content[tabs_end:]
    print("7. Replaced Tabs HTML")
else:
    print(f"WARNING: tabs_start={tabs_start}, tabs_end={tabs_end}")

# Save updated files
with open('plugins/hype-marketer.php', 'w', encoding='utf-8') as f:
    f.write(content)

with open('scripts/hype-marketer/hype-marketer.php', 'w', encoding='utf-8') as f:
    f.write(content)

print("Saved updated plugins/hype-marketer.php and scripts/hype-marketer/hype-marketer.php")

res = subprocess.run(['php', '-l', 'plugins/hype-marketer.php'], capture_output=True, text=True)
print("PHP lint result:", res.stdout.strip() or res.stderr.strip())
