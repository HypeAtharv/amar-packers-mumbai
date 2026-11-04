import re

with open('plugins/hype-marketer.php', 'r', encoding='utf-8') as f:
    code = f.read()

# 1. Update CSS: add cursor pointers, row hover, link styles
css_target = """.hype-sheet-table tr:hover td {
        background: #f8fafc !important;
    }"""

css_replacement = """.hype-sheet-table tbody tr {
        cursor: pointer;
        transition: background-color 0.15s ease;
    }
    .hype-sheet-table tbody tr:hover td {
        background: #f1f5f9 !important;
    }
    .hype-cust-name-link {
        font-weight: 700;
        color: #0f172a;
        font-size: 13.5px;
        cursor: pointer;
        display: inline-block;
        transition: color 0.15s ease;
    }
    .hype-cust-name-link:hover {
        color: #ee1d23;
        text-decoration: underline;
    }
    .hype-id-tag {
        cursor: pointer;
        transition: all 0.15s ease;
    }
    .hype-id-tag:hover {
        background: #e2e8f0;
        color: #0f172a;
        border-color: #cbd5e1;
    }
    .hype-act-btn, .hype-priority-tag, .hype-status-tag, .hype-tab, .hype-btn {
        cursor: pointer !important;
    }"""

if css_target in code:
    code = code.replace(css_target, css_replacement, 1)
    print("1. Replaced CSS hover/cursor styles")
else:
    print("WARNING: css_target not found")

# 2. Update Table Row and Click Handlers
old_tr = """<tr id="lead-row-<?php echo esc_attr($l->id); ?>">
                                <td style="text-align:center;">
                                    <input type="checkbox" class="hype-row-cb" value="<?php echo esc_attr($l->id); ?>">
                                </td>
                                <td>
                                    <span class="hype-id-tag">#<?php echo esc_html($l->id); ?></span>
                                </td>"""

new_tr = """<tr id="lead-row-<?php echo esc_attr($l->id); ?>" class="hype-lead-row" onclick="hypeHandleRowClick(event, <?php echo intval($l->id); ?>)" title="Click anywhere on row to view full lead details">
                                <td style="text-align:center;" onclick="event.stopPropagation();">
                                    <input type="checkbox" class="hype-row-cb" value="<?php echo esc_attr($l->id); ?>" onclick="event.stopPropagation();">
                                </td>
                                <td>
                                    <span class="hype-id-tag" onclick="event.stopPropagation(); hypeOpenDetailModalById(<?php echo intval($l->id); ?>);" title="Click to view details">#<?php echo esc_html($l->id); ?></span>
                                </td>"""

if old_tr in code:
    code = code.replace(old_tr, new_tr, 1)
    print("2. Replaced Table Row & Checkbox & ID")
else:
    print("WARNING: old_tr not found")

# 3. Update Priority TD
old_priority = """<td>
                                    <select class="hype-priority-tag priority-<?php echo esc_attr($pr); ?>" onchange="hypeChangePriority(<?php echo esc_attr($l->id); ?>, this.value, this)">"""

new_priority = """<td onclick="event.stopPropagation();">
                                    <select class="hype-priority-tag priority-<?php echo esc_attr($pr); ?>" onchange="hypeChangePriority(<?php echo esc_attr($l->id); ?>, this.value, this)" onclick="event.stopPropagation();">"""

if old_priority in code:
    code = code.replace(old_priority, new_priority, 1)
    print("3. Replaced Priority Select TD")
else:
    print("WARNING: old_priority not found")

# 4. Update Customer Cell
old_cust = """<div class="hype-customer-cell">
                                        <div class="hype-avatar-circle"><?php echo esc_html($initials); ?></div>
                                        <div>
                                            <div class="hype-cust-name"><?php echo $display_name; ?></div>
                                            <a href="tel:<?php echo esc_attr($l->phone); ?>" class="hype-cust-phone">
                                                📞 <?php echo esc_html($l->phone); ?>
                                            </a>"""

new_cust = """<div class="hype-customer-cell">
                                        <div class="hype-avatar-circle" onclick="event.stopPropagation(); hypeOpenDetailModalById(<?php echo intval($l->id); ?>);"><?php echo esc_html($initials); ?></div>
                                        <div>
                                            <div class="hype-cust-name-link" onclick="event.stopPropagation(); hypeOpenDetailModalById(<?php echo intval($l->id); ?>);" title="Click to view full lead details"><?php echo $display_name; ?></div>
                                            <a href="tel:<?php echo esc_attr($l->phone); ?>" class="hype-cust-phone" onclick="event.stopPropagation();">
                                                📞 <?php echo esc_html($l->phone); ?>
                                            </a>"""

if old_cust in code:
    code = code.replace(old_cust, new_cust, 1)
    print("4. Replaced Customer Cell")
else:
    print("WARNING: old_cust not found")

# 5. Update Status TD
old_status = """<td>
                                    <select class="hype-status-tag status-<?php echo esc_attr($l->status); ?>" onchange="hypeChangeStatus(<?php echo esc_attr($l->id); ?>, this.value, this)">"""

new_status = """<td onclick="event.stopPropagation();">
                                    <select class="hype-status-tag status-<?php echo esc_attr($l->status); ?>" onchange="hypeChangeStatus(<?php echo esc_attr($l->id); ?>, this.value, this)" onclick="event.stopPropagation();">"""

if old_status in code:
    code = code.replace(old_status, new_status, 1)
    print("5. Replaced Status Select TD")
else:
    print("WARNING: old_status not found")

# 6. Update Quick Actions TD and Buttons
old_actions = """<td>
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
                                </td>"""

new_actions = """<td onclick="event.stopPropagation();">
                                    <div class="hype-actions-cell">
                                        <?php if ($tab !== 'trash') : ?>
                                            <!-- 1-Click WhatsApp -->
                                            <a href="https://wa.me/<?php echo esc_attr($wa_phone); ?>?text=<?php echo rawurlencode('Hello ' . $display_name . ', thank you for contacting Amar Packers & Movers. Regarding your inquiry:'); ?>" target="_blank" class="hype-act-btn act-wa" title="Open WhatsApp Chat" onclick="event.stopPropagation();">
                                                💬
                                            </a>
                                            <!-- 1-Click Phone Call -->
                                            <a href="tel:<?php echo esc_attr($l->phone); ?>" class="hype-act-btn act-call" title="Call Customer" onclick="event.stopPropagation();">
                                                📞
                                            </a>
                                            <!-- View Details / Notes -->
                                            <button type="button" class="hype-act-btn act-view" onclick="event.stopPropagation(); hypeOpenDetailModalById(<?php echo intval($l->id); ?>);" title="View Full Details & Notes">
                                                👁️
                                            </button>
                                            <!-- Mark as Spam -->
                                            <button type="button" class="hype-act-btn act-spam" onclick="event.stopPropagation(); hypeToggleSpam(<?php echo intval($l->id); ?>, 1);" title="Mark as Spam">
                                                🛑
                                            </button>
                                            <!-- Move to Trash -->
                                            <button type="button" class="hype-act-btn act-trash" onclick="event.stopPropagation(); hypeMoveToTrash(<?php echo intval($l->id); ?>);" title="Move to Trash">
                                                🗑️
                                            </button>
                                        <?php else : ?>
                                            <!-- Restore -->
                                            <button type="button" class="hype-act-btn act-restore" onclick="event.stopPropagation(); hypeRestoreTrash(<?php echo intval($l->id); ?>);" title="Restore Lead">
                                                ♻️ Restore
                                            </button>
                                            <!-- Delete Permanent -->
                                            <button type="button" class="hype-act-btn act-trash" onclick="event.stopPropagation(); hypeDeletePermanent(<?php echo intval($l->id); ?>);" title="Delete Permanently">
                                                ❌
                                            </button>
                                        <?php endif; ?>
                                    </div>
                                </td>"""

if old_actions in code:
    code = code.replace(old_actions, new_actions, 1)
    print("6. Replaced Quick Actions Cell & StopPropagation")
else:
    print("WARNING: old_actions not found")

# 7. Output Leads Store JSON before script
old_script_start = """    <script>
    const ajaxurl = '<?php echo esc_url(admin_url('admin-ajax.php')); ?>';"""

new_script_start = """    <?php
    $leads_map = array();
    foreach ($leads as $item) {
        $leads_map[$item->id] = $item;
    }
    ?>
    <script id="hypeLeadsStore" type="application/json">
    <?php echo json_encode($leads_map, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP); ?>
    </script>

    <script>
    const ajaxurl = '<?php echo esc_url(admin_url('admin-ajax.php')); ?>';
    let hypeLeadsStore = {};
    try {
        const storeEl = document.getElementById('hypeLeadsStore');
        if (storeEl && storeEl.textContent) {
            hypeLeadsStore = JSON.parse(storeEl.textContent);
        }
    } catch(err) {
        console.warn('Failed to parse leads store:', err);
    }

    function hypeHandleRowClick(e, leadId) {
        if (!e) return;
        const target = e.target;
        if (target.closest('input') || target.closest('select') || target.closest('button') || target.closest('a')) {
            return;
        }
        hypeOpenDetailModalById(leadId);
    }

    function hypeOpenDetailModalById(leadId) {
        const lead = hypeLeadsStore[leadId];
        if (lead) {
            hypeOpenDetailModal(lead);
        }
    }"""

if old_script_start in code:
    code = code.replace(old_script_start, new_script_start, 1)
    print("7. Injected Leads Store JSON & Row Click Handlers")
else:
    print("WARNING: old_script_start not found")

# 8. Add modal backdrop click to close
old_modal_close = """    function hypeCloseModal() {
        document.getElementById('hypeLeadModal').style.display = 'none';
    }"""

new_modal_close = """    function hypeCloseModal() {
        document.getElementById('hypeLeadModal').style.display = 'none';
    }

    window.addEventListener('click', function(e) {
        const detailModal = document.getElementById('hypeLeadModal');
        const manualModal = document.getElementById('hypeManualLeadModal');
        if (e.target === detailModal) detailModal.style.display = 'none';
        if (e.target === manualModal) manualModal.style.display = 'none';
    });"""

if old_modal_close in code:
    code = code.replace(old_modal_close, new_modal_close, 1)
    print("8. Added Backdrop Click to Close")
else:
    print("WARNING: old_modal_close not found")

with open('plugins/hype-marketer.php', 'w', encoding='utf-8') as f:
    f.write(code)

with open('scripts/hype-marketer/hype-marketer.php', 'w', encoding='utf-8') as f:
    f.write(code)

print("Saved updated plugins/hype-marketer.php and scripts/hype-marketer/hype-marketer.php")
