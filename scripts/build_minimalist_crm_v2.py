import base64
import re

# 1. Read Hype Marketer official logo
with open('images/hypemarketer-logo.png', 'rb') as f:
    logo_b64 = base64.b64encode(f.read()).decode('ascii')

logo_img_tag = f'<img src="data:image/png;base64,{logo_b64}" alt="Hype Marketer" style="width: 28px; height: 28px; object-fit: contain; display: block;">'

with open('plugins/hype-marketer.php', 'r', encoding='utf-8') as f:
    code = f.read()

# 2. Update Topbar Logo
old_logo_pattern = r'<div class="hype-logo-icon">.*?</div>'
new_logo_markup = f'''<div class="hype-logo-icon" style="background: #ffffff; border: 1px solid rgba(255,255,255,0.2); border-radius: 8px; width: 38px; height: 38px; display: flex; align-items: center; justify-content: center; padding: 4px; box-sizing: border-box; flex-shrink: 0; box-shadow: 0 1px 3px rgba(0,0,0,0.15);">
                    {logo_img_tag}
                </div>'''
code = re.sub(old_logo_pattern, new_logo_markup, code, count=1)
print("1. Logo markup updated.")

# 3. Update Topbar Title Badge
old_badge_pattern = r'<span class="hype-badge-pulse">.*?</span>'
new_badge_markup = '<span class="hype-badge-pulse"><span class="hype-pulse-dot"></span>Live CRM</span>'
code = re.sub(old_badge_pattern, new_badge_markup, code, count=1)
print("2. Topbar badge updated.")

# 4. Update Topbar Buttons to Minimalist Style
new_topbar_right = """<div class="hype-topbar-right">
                <button type="button" class="hype-btn hype-btn-white" onclick="hypeOpenManualModal()">
                    + Add Lead Manually
                </button>
                <a href="<?php echo esc_url(admin_url('admin.php?page=hype-marketer&action=hype_export_csv&tab=' . urlencode($tab))); ?>" class="hype-btn hype-btn-outline-light">
                    Export to CSV
                </a>
            </div>"""
code = re.sub(r'<div class="hype-topbar-right">.*?</div>', new_topbar_right, code, count=1, flags=re.DOTALL)
print("3. Topbar buttons updated.")

# 5. Clean KPI numbers and subtext (remove inline color overrides)
code = re.sub(r'<div class="kpi-num" style="color:#[0-9a-fA-F]+;">', '<div class="kpi-num">', code)
code = code.replace('<span style="color:#10b981; font-weight:700;">● Live</span>', '<span style="color:#059669; font-weight:600;">● Live</span>')
code = code.replace('<span style="color:#059669; font-weight:700;">', '<span style="color:#0f172a; font-weight:600;">')
print("4. KPI inline colors neutralized.")

# 6. Remove distracting emojis from Navigation Tabs
tab_replacements = [
    (r'📑\s*All Active', 'All Active'),
    (r'🔵\s*New\s*/\s*Uncontacted', 'New / Uncontacted'),
    (r'🔥\s*High Priority', 'High Priority'),
    (r'🎉\s*Converted', 'Converted'),
    (r'⚪\s*No Longer Needed', 'No Longer Needed'),
    (r'🛑\s*Spam', 'Spam'),
    (r'🗑️\s*Trash', 'Trash'),
]
for pat, rep in tab_replacements:
    code = re.sub(pat, rep, code)
print("5. Tab emojis replaced with clean text.")

# 7. Replace Quick Action buttons with crisp minimalist SVGs
svg_wa = '<svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"></path></svg>'
svg_phone = '<svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path></svg>'
svg_view = '<svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>'
svg_spam = '<svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 15s1-1 4-1 5 2 8 2 4-1 4-1V3s-1 1-4 1-5-2-8-2-4 1-4 1z"></path><line x1="4" y1="22" x2="4" y2="15"></line></svg>'
svg_trash = '<svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>'
svg_restore = '<svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="1 4 1 10 7 10"></polyline><path d="M3.51 15a9 9 0 1 0 2.13-9.36L1 10"></path></svg> Restore'

# Replace inner content of quick action buttons
code = re.sub(r'<a href="https://wa\.me/.*?class="hype-act-btn act-wa".*?>\s*.*?\s*</a>', 
              f'<a href="https://wa.me/<?php echo esc_attr($wa_phone); ?>?text=<?php echo rawurlencode(\'Hello \' . $display_name . \', thank you for contacting Amar Packers & Movers. Regarding your inquiry:\'); ?>" target="_blank" class="hype-act-btn act-wa" title="Open WhatsApp Chat" onclick="event.stopPropagation();">{svg_wa}</a>', code)

code = re.sub(r'<a href="tel:.*?class="hype-act-btn act-call".*?>\s*.*?\s*</a>',
              f'<a href="tel:<?php echo esc_attr($l->phone); ?>" class="hype-act-btn act-call" title="Call Customer" onclick="event.stopPropagation();">{svg_phone}</a>', code)

code = re.sub(r'<button type="button" class="hype-act-btn act-view".*?>\s*.*?\s*</button>',
              f'<button type="button" class="hype-act-btn act-view" onclick="event.stopPropagation(); hypeOpenDetailModalById(<?php echo intval($l->id); ?>);" title="View Full Details & Notes">{svg_view}</button>', code)

code = re.sub(r'<button type="button" class="hype-act-btn act-spam".*?>\s*.*?\s*</button>',
              f'<button type="button" class="hype-act-btn act-spam" onclick="event.stopPropagation(); hypeToggleSpam(<?php echo intval($l->id); ?>, 1);" title="Mark as Spam">{svg_spam}</button>', code)

code = re.sub(r'<button type="button" class="hype-act-btn act-trash" onclick="event\.stopPropagation\(\); hypeMoveToTrash.*?title="Move to Trash">\s*.*?\s*</button>',
              f'<button type="button" class="hype-act-btn act-trash" onclick="event.stopPropagation(); hypeMoveToTrash(<?php echo intval($l->id); ?>);" title="Move to Trash">{svg_trash}</button>', code)

code = re.sub(r'<button type="button" class="hype-act-btn act-restore".*?>\s*.*?\s*</button>',
              f'<button type="button" class="hype-act-btn act-restore" onclick="event.stopPropagation(); hypeRestoreTrash(<?php echo intval($l->id); ?>);" title="Restore Lead">{svg_restore}</button>', code)

code = re.sub(r'<button type="button" class="hype-act-btn act-trash" onclick="event\.stopPropagation\(\); hypeDeletePermanent.*?title="Delete Permanently">\s*.*?\s*</button>',
              f'<button type="button" class="hype-act-btn act-trash" onclick="event.stopPropagation(); hypeDeletePermanent(<?php echo intval($l->id); ?>);" title="Delete Permanently">{svg_trash}</button>', code)
print("6. Quick action buttons converted to minimalist SVGs.")

# 8. Clean Priority and Status option labels (remove emoji prefixes)
code = code.replace('>⚡ Urgent<', '>Urgent<')
code = code.replace('>🔥 High<', '>High<')
code = code.replace('>🔵 Normal<', '>Normal<')
code = code.replace('>⚪ Low<', '>Low<')
code = code.replace('>✨ New<', '>New<')
code = code.replace('>📞 Contacted<', '>Contacted<')
code = code.replace('>📝 Quoted<', '>Quoted<')
code = code.replace('>🎉 Converted<', '>Converted<')
code = code.replace('>💤 No Longer Needed<', '>No Longer Needed<')
code = code.replace('>🛑 Spam<', '>Spam<')
code = code.replace('>🗑️ Trash<', '>Trash<')
print("7. Option emojis cleaned.")

# 9. Master Minimalist CSS
master_minimalist_css = """
    /* Master Minimalist Executive CRM Stylesheet */
    .hype-wrap {
        margin: 16px 20px 24px 0;
        font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
        color: #0f172a;
        -webkit-font-smoothing: antialiased;
    }

    /* Executive Top Bar */
    .hype-topbar {
        background: #0f172a;
        color: #ffffff;
        padding: 18px 24px;
        border-radius: 12px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
        border: 1px solid #1e293b;
        box-shadow: 0 1px 3px rgba(0,0,0,0.06);
    }
    .hype-topbar-left {
        display: flex;
        align-items: center;
        gap: 16px;
    }
    .hype-topbar-title h1 {
        font-size: 20px;
        font-weight: 700;
        color: #ffffff;
        margin: 0;
        letter-spacing: -0.3px;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .hype-badge-pulse {
        background: rgba(255, 255, 255, 0.08);
        color: #cbd5e1;
        border: 1px solid rgba(255, 255, 255, 0.15);
        font-size: 11px;
        font-weight: 500;
        padding: 2px 9px;
        border-radius: 6px;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }
    .hype-pulse-dot {
        width: 6px;
        height: 6px;
        border-radius: 50%;
        background: #10b981;
        display: inline-block;
    }
    .hype-topbar-title p {
        color: #94a3b8;
        font-size: 12.5px;
        margin: 4px 0 0 0;
        font-weight: 400;
    }
    .hype-topbar-right {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    /* Minimalist Buttons */
    .hype-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        padding: 8px 14px;
        border-radius: 7px;
        font-size: 12.5px;
        font-weight: 600;
        text-decoration: none;
        cursor: pointer !important;
        border: 1px solid transparent;
        transition: all 0.15s ease;
        line-height: 1.2;
    }
    .hype-btn-white {
        background: #ffffff;
        color: #0f172a;
        border-color: #ffffff;
    }
    .hype-btn-white:hover {
        background: #f1f5f9;
        color: #0f172a;
    }
    .hype-btn-outline-light {
        background: transparent;
        color: #e2e8f0;
        border-color: #334155;
    }
    .hype-btn-outline-light:hover {
        background: rgba(255,255,255,0.06);
        border-color: #475569;
        color: #ffffff;
    }
    .hype-btn-primary {
        background: #0f172a;
        color: #ffffff;
        border-color: #0f172a;
    }
    .hype-btn-primary:hover {
        background: #1e293b;
        color: #ffffff;
    }
    .hype-btn-outline {
        background: #ffffff;
        color: #475569;
        border-color: #cbd5e1;
    }
    .hype-btn-outline:hover {
        background: #f8fafc;
        color: #0f172a;
        border-color: #94a3b8;
    }
    .hype-btn-dark {
        background: #0f172a;
        color: #ffffff;
        border-color: #0f172a;
    }
    .hype-btn-dark:hover {
        background: #1e293b;
    }

    /* Minimalist Metric Cards Strip */
    .hype-kpis {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 14px;
        margin-bottom: 20px;
    }
    .hype-kpi-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        padding: 16px 20px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.02);
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .hype-kpi-info h3 {
        font-size: 11px;
        font-weight: 600;
        color: #64748b;
        margin: 0 0 6px 0;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    .hype-kpi-info .kpi-num {
        font-size: 26px;
        font-weight: 700;
        color: #0f172a;
        line-height: 1.1;
        margin: 0;
    }
    .hype-kpi-info .kpi-sub {
        font-size: 11.5px;
        color: #94a3b8;
        margin-top: 6px;
        font-weight: 500;
    }
    .hype-kpi-icon {
        display: none !important;
    }

    /* Minimalist Tabs Navigation */
    .hype-tabs-wrap {
        display: flex;
        align-items: center;
        justify-content: space-between;
        border-bottom: 1px solid #e2e8f0;
        margin-bottom: 16px;
    }
    .hype-tabs {
        display: flex;
        gap: 4px;
        overflow-x: auto;
    }
    .hype-tab {
        padding: 10px 14px;
        text-decoration: none;
        color: #64748b;
        font-weight: 500;
        font-size: 13px;
        border-bottom: 2px solid transparent;
        display: inline-flex;
        align-items: center;
        gap: 7px;
        margin-bottom: -1px;
        transition: all 0.15s ease;
        cursor: pointer !important;
    }
    .hype-tab:hover {
        color: #0f172a;
    }
    .hype-tab.active {
        color: #0f172a;
        border-bottom-color: #0f172a;
        font-weight: 600;
    }
    .hype-tab-count {
        font-size: 11px;
        font-weight: 600;
        padding: 1px 7px;
        border-radius: 10px;
        background: #f1f5f9;
        color: #64748b;
    }
    .hype-tab.active .hype-tab-count {
        background: #0f172a;
        color: #ffffff;
    }

    /* Minimalist Filters Toolbar */
    .hype-toolbar {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        padding: 12px 16px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        margin-bottom: 16px;
        flex-wrap: wrap;
    }
    .hype-tool-left, .hype-tool-right {
        display: flex;
        align-items: center;
        gap: 8px;
        flex-wrap: wrap;
    }
    .hype-search-box {
        position: relative;
    }
    .hype-search-box input {
        width: 220px;
        padding: 7px 12px 7px 32px;
        border: 1px solid #e2e8f0;
        border-radius: 7px;
        font-size: 12.5px;
        color: #0f172a;
        background: #ffffff;
        outline: none;
        transition: border-color 0.15s ease;
    }
    .hype-search-box input:focus {
        border-color: #0f172a;
    }
    .hype-search-box::before {
        content: "";
        position: absolute;
        left: 11px;
        top: 50%;
        transform: translateY(-50%);
        width: 12px;
        height: 12px;
        background: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="%2394a3b8" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>') no-repeat center;
        pointer-events: none;
    }
    .hype-select {
        padding: 7px 10px;
        border: 1px solid #e2e8f0;
        border-radius: 7px;
        font-size: 12.5px;
        color: #334155;
        background: #ffffff;
        outline: none;
        cursor: pointer !important;
    }
    .hype-select:focus {
        border-color: #0f172a;
    }

    /* Minimalist Spreadsheet Table */
    .hype-sheet-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        overflow: hidden;
        box-shadow: 0 1px 3px rgba(0,0,0,0.02);
    }
    .hype-sheet-scroll {
        overflow-x: auto;
        max-height: calc(100vh - 300px);
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
        font-size: 11px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        padding: 10px 14px;
        border-bottom: 1px solid #e2e8f0;
        border-right: 1px solid #f1f5f9;
        position: sticky;
        top: 0;
        z-index: 10;
        white-space: nowrap;
    }
    .hype-sheet-table th:last-child {
        border-right: none;
    }
    .hype-sheet-table td {
        padding: 11px 14px;
        border-bottom: 1px solid #f1f5f9;
        border-right: 1px solid #f8fafc;
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

    /* Monospace ID Badge */
    .hype-id-tag {
        font-family: ui-monospace, SFMono-Regular, Menlo, monospace;
        font-weight: 600;
        color: #64748b;
        font-size: 11.5px;
        background: #f1f5f9;
        padding: 3px 6px;
        border-radius: 5px;
        border: 1px solid #e2e8f0;
        cursor: pointer;
    }
    .hype-id-tag:hover {
        background: #e2e8f0;
        color: #0f172a;
    }

    /* Customer Details Cell */
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
        color: #475569;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 600;
        font-size: 11px;
        flex-shrink: 0;
        border: 1px solid #e2e8f0;
    }
    .hype-cust-name-link {
        font-weight: 600;
        color: #0f172a;
        font-size: 13px;
        cursor: pointer;
        display: inline-block;
        transition: color 0.15s ease;
    }
    .hype-cust-name-link:hover {
        color: #2563eb;
        text-decoration: underline;
    }
    .hype-cust-phone {
        font-size: 12px;
        color: #64748b;
        text-decoration: none;
        display: block;
        margin-top: 1px;
    }
    .hype-cust-phone:hover {
        color: #0f172a;
    }
    .hype-cust-email {
        font-size: 11px;
        color: #94a3b8;
        margin-top: 1px;
    }

    /* Route Pill */
    .hype-route-pill {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        padding: 4px 9px;
        border-radius: 6px;
        font-size: 12px;
        color: #334155;
    }
    .route-dot-from { width: 6px; height: 6px; border-radius: 50%; background: #64748b; }
    .route-dot-to { width: 6px; height: 6px; border-radius: 50%; background: #0f172a; }
    .route-arrow { color: #94a3b8; font-size: 11px; }

    /* Minimalist Badges */
    .hype-badge-size {
        background: #f1f5f9;
        color: #334155;
        border: 1px solid #e2e8f0;
        font-size: 11.5px;
        font-weight: 600;
        padding: 3px 8px;
        border-radius: 6px;
    }
    .hype-badge-service {
        font-size: 11.5px;
        color: #64748b;
        margin-top: 2px;
    }
    .hype-badge-cost {
        background: #f8fafc;
        color: #334155;
        border: 1px solid #e2e8f0;
        font-size: 11.5px;
        font-weight: 600;
        padding: 3px 8px;
        border-radius: 6px;
        display: inline-block;
    }
    .hype-source-badge {
        font-size: 11.5px;
        color: #64748b;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        padding: 3px 8px;
        border-radius: 6px;
        display: inline-flex;
        align-items: center;
        gap: 5px;
    }

    /* Minimalist Status & Priority Selectors */
    .hype-status-tag, .hype-priority-tag {
        font-size: 12px;
        font-weight: 500;
        padding: 4px 8px;
        border-radius: 6px;
        border: 1px solid #e2e8f0;
        background: #ffffff;
        color: #334155;
        cursor: pointer !important;
        outline: none;
    }
    .status-new { background: #f8fafc; border-color: #cbd5e1; color: #0f172a; font-weight: 600; }
    .status-contacted { background: #f8fafc; border-color: #e2e8f0; color: #475569; }
    .status-quoted { background: #f8fafc; border-color: #e2e8f0; color: #475569; }
    .status-converted { background: #f0fdf4; border-color: #bbf7d0; color: #166534; font-weight: 600; }
    .status-no_longer_needed { background: #f8fafc; border-color: #e2e8f0; color: #94a3b8; }
    .status-spam, .status-trash { background: #fef2f2; border-color: #fecaca; color: #991b1b; }

    .priority-urgent { background: #fffbeb; border-color: #fde68a; color: #92400e; font-weight: 600; }
    .priority-high { background: #fff7ed; border-color: #fed7aa; color: #9a3412; font-weight: 600; }
    .priority-normal { background: #f8fafc; border-color: #e2e8f0; color: #475569; }
    .priority-low { background: #f8fafc; border-color: #f1f5f9; color: #94a3b8; }

    /* Minimalist Action Buttons */
    .hype-actions-cell {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 5px;
    }
    .hype-act-btn {
        width: 28px;
        height: 28px;
        border-radius: 6px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        text-decoration: none;
        border: 1px solid #e2e8f0;
        background: #ffffff;
        color: #64748b !important;
        cursor: pointer !important;
        transition: all 0.15s ease;
        padding: 0;
    }
    .hype-act-btn:hover {
        border-color: #cbd5e1;
        transform: translateY(-1px);
    }
    .act-wa:hover { background: #ecfdf5; border-color: #a7f3d0; color: #059669 !important; }
    .act-call:hover { background: #eff6ff; border-color: #bfdbfe; color: #2563eb !important; }
    .act-view:hover { background: #f1f5f9; border-color: #cbd5e1; color: #0f172a !important; }
    .act-spam:hover { background: #fef2f2; border-color: #fecaca; color: #dc2626 !important; }
    .act-trash:hover { background: #fef2f2; border-color: #fecaca; color: #dc2626 !important; }
    .act-restore {
        width: auto;
        padding: 0 8px;
        font-size: 11.5px;
        font-weight: 600;
        gap: 4px;
        background: #ffffff;
    }
    .act-restore:hover {
        background: #ecfdf5;
        border-color: #a7f3d0;
        color: #059669 !important;
    }

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
        font-weight: 500;
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        display: none;
        align-items: center;
        gap: 8px;
        z-index: 999999;
    }

    /* Modal / Drawer Minimalist */
    .hype-modal-overlay {
        position: fixed;
        top: 0; left: 0; right: 0; bottom: 0;
        background: rgba(15, 23, 42, 0.45);
        backdrop-filter: blur(2px);
        z-index: 99999;
        display: none;
        align-items: center;
        justify-content: center;
    }
    .hype-modal-box {
        background: #ffffff;
        border-radius: 12px;
        max-width: 650px;
        width: 90%;
        max-height: 88vh;
        overflow-y: auto;
        padding: 24px;
        box-shadow: 0 20px 40px -10px rgba(0,0,0,0.15);
        border: 1px solid #e2e8f0;
    }
"""

# Replace the style block
css_start = code.find("<style>")
css_end = code.find("</style>")
if css_start != -1 and css_end != -1:
    code = code[:css_start + 7] + master_minimalist_css + code[css_end:]
    print("8. Master Minimalist CSS replaced.")
else:
    print("WARNING: <style> block not found.")

# Save updated files
with open('plugins/hype-marketer.php', 'w', encoding='utf-8') as f:
    f.write(code)

with open('scripts/hype-marketer/hype-marketer.php', 'w', encoding='utf-8') as f:
    f.write(code)

print("SUCCESS: Minimalist CRM generated in plugins/hype-marketer.php!")
