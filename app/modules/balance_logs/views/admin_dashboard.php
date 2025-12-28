<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<style>
.balance-stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
    gap: 1rem;
    margin-bottom: 2rem;
}
.stat-card {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border-radius: 12px;
    padding: 1.5rem;
    text-align: center;
}
.stat-card.green { background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%); }
.stat-card.red { background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); }
.stat-card.blue { background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); }
.stat-card h3 { margin: 0; font-size: 2rem; font-weight: bold; }
.stat-card p { margin: 0.5rem 0 0; opacity: 0.9; font-size: 0.9rem; }
.filters-card { background: #f8f9fa; padding: 1.5rem; border-radius: 12px; margin-bottom: 1.5rem; }
.filter-row { display: flex; gap: 1rem; flex-wrap: wrap; align-items: end; }
.filter-group { flex: 1; min-width: 180px; }
.export-buttons { display: flex; gap: 0.5rem; margin-top: 1rem; }
</style>

<div class="page-header">
    <h1 class="page-title">
        <i class="fe fe-activity"></i> <?=lang("Advanced Balance Logs Dashboard")?>
    </h1>
    <div class="page-options">
        <a href="<?=cn('balance_logs')?>" class="btn btn-outline-secondary">
            <i class="fe fe-list"></i> <?=lang("Simple View")?>
        </a>
    </div>
</div>

<!-- Statistics Summary Boxes -->
<div class="balance-stats-grid">
    <div class="stat-card blue">
        <h3><?=$stats['total_count']?></h3>
        <p><?=lang("Total Logs")?></p>
        <small><?=lang("All balance changes")?></small>
    </div>
    <div class="stat-card green">
        <h3><?=$currency_symbol?><?=number_format($stats['total_credited'], 2)?></h3>
        <p><?=lang("Total Credited")?></p>
        <small><?=$stats['credit_count']?> <?=lang("transactions")?></small>
    </div>
    <div class="stat-card red">
        <h3><?=$currency_symbol?><?=number_format($stats['total_debited'], 2)?></h3>
        <p><?=lang("Total Debited")?></p>
        <small><?=$stats['debit_count']?> <?=lang("transactions")?></small>
    </div>
    <div class="stat-card <?=$stats['net_change'] >= 0 ? 'green' : 'red'?>">
        <h3><?=$currency_symbol?><?=number_format(abs($stats['net_change']), 2)?></h3>
        <p><?=lang("Net Change")?></p>
        <small><?=$stats['net_change'] >= 0 ? lang("Positive") : lang("Negative")?></small>
    </div>
</div>

<!-- Advanced Filters -->
<div class="filters-card">
    <h5 class="mb-3"><i class="fe fe-filter"></i> <?=lang("Advanced Filters")?></h5>
    <form method="GET" action="<?=cn('balance_logs/admin_dashboard')?>" id="filterForm">
        <div class="filter-row">
            <div class="filter-group">
                <label><?=lang("User Email")?></label>
                <input type="text" name="user_email" class="form-control" 
                       value="<?=get('user_email')?>" placeholder="<?=lang("Filter by email")?>">
            </div>
            <div class="filter-group">
                <label><?=lang("Action Type")?></label>
                <select name="action_type" class="form-control">
                    <option value=""><?=lang("All Types")?></option>
                    <option value="addition" <?=get('action_type') == 'addition' ? 'selected' : ''?>><?=lang("Addition")?></option>
                    <option value="deduction" <?=get('action_type') == 'deduction' ? 'selected' : ''?>><?=lang("Deduction")?></option>
                    <option value="refund" <?=get('action_type') == 'refund' ? 'selected' : ''?>><?=lang("Refund")?></option>
                    <option value="manual_add" <?=get('action_type') == 'manual_add' ? 'selected' : ''?>><?=lang("Manual Add")?></option>
                    <option value="manual_deduct" <?=get('action_type') == 'manual_deduct' ? 'selected' : ''?>><?=lang("Manual Deduct")?></option>
                </select>
            </div>
            <div class="filter-group">
                <label><?=lang("Related Type")?></label>
                <select name="related_type" class="form-control">
                    <option value=""><?=lang("All Types")?></option>
                    <?php foreach ($related_types as $type): ?>
                        <option value="<?=$type?>" <?=get('related_type') == $type ? 'selected' : ''?>>
                            <?=ucfirst($type)?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="filter-group">
                <label><?=lang("Date From")?></label>
                <input type="date" name="date_from" class="form-control" value="<?=get('date_from')?>">
            </div>
            <div class="filter-group">
                <label><?=lang("Date To")?></label>
                <input type="date" name="date_to" class="form-control" value="<?=get('date_to')?>">
            </div>
            <div class="filter-group">
                <label><?=lang("Min Amount")?></label>
                <input type="number" name="min_amount" class="form-control" step="0.01" 
                       value="<?=get('min_amount')?>" placeholder="0.00">
            </div>
            <div class="filter-group">
                <label><?=lang("Max Amount")?></label>
                <input type="number" name="max_amount" class="form-control" step="0.01" 
                       value="<?=get('max_amount')?>" placeholder="0.00">
            </div>
            <div class="filter-group">
                <label><?=lang("Sort By")?></label>
                <select name="sort_by" class="form-control">
                    <option value="created_desc" <?=get('sort_by') == 'created_desc' ? 'selected' : ''?>><?=lang("Date (Newest First)")?></option>
                    <option value="created_asc" <?=get('sort_by') == 'created_asc' ? 'selected' : ''?>><?=lang("Date (Oldest First)")?></option>
                    <option value="amount_desc" <?=get('sort_by') == 'amount_desc' ? 'selected' : ''?>><?=lang("Amount (High to Low)")?></option>
                    <option value="amount_asc" <?=get('sort_by') == 'amount_asc' ? 'selected' : ''?>><?=lang("Amount (Low to High)")?></option>
                </select>
            </div>
            <div class="filter-group" style="flex: 0 0 auto;">
                <button type="submit" class="btn btn-primary">
                    <i class="fe fe-search"></i> <?=lang("Apply Filters")?>
                </button>
                <a href="<?=cn('balance_logs/admin_dashboard')?>" class="btn btn-secondary ms-2">
                    <i class="fe fe-x"></i> <?=lang("Clear")?>
                </a>
            </div>
        </div>
    </form>
    
    <!-- Export Buttons -->
    <div class="export-buttons">
        <button class="btn btn-success btn-sm" onclick="exportData('csv')">
            <i class="fe fe-download"></i> <?=lang("Export CSV")?>
        </button>
        <button class="btn btn-info btn-sm" onclick="exportData('excel')">
            <i class="fe fe-download"></i> <?=lang("Export Excel")?>
        </button>
    </div>
</div>

<!-- Balance Logs Table -->
<?php if (!empty($balance_logs)): ?>
<div class="card">
    <div class="card-header">
        <h3 class="card-title"><?=lang("Balance Logs List")?></h3>
        <div class="card-options">
            <span class="badge bg-primary"><?=$total_rows?> <?=lang("records")?></span>
        </div>
    </div>
    <div class="table-responsive">
        <table class="table table-hover table-bordered table-vcenter card-table">
            <thead>
                <tr>
                    <th class="text-center"><?=lang("No.")?></th>
                    <th><?=lang("User")?></th>
                    <th><?=lang("Action Type")?></th>
                    <th><?=lang("Amount")?></th>
                    <th><?=lang("Balance Before")?></th>
                    <th><?=lang("Balance After")?></th>
                    <th><?=lang("Description")?></th>
                    <th><?=lang("Related ID")?></th>
                    <th><?=lang("Related Type")?></th>
                    <th><?=lang("Date")?></th>
                    <th class="text-center"><?=lang("Actions")?></th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $i = $offset + 1;
                foreach ($balance_logs as $row): 
                    $is_positive = in_array($row->action_type, ['addition', 'refund', 'manual_add']);
                    $amount_class = $is_positive ? 'text-success' : 'text-danger';
                    $amount_prefix = $is_positive ? '+' : '-';
                ?>
                <tr>
                    <td class="text-center"><?=$i++?></td>
                    <td>
                        <div><?=trim($row->first_name . ' ' . $row->last_name) ?: $row->email?></div>
                        <small class="text-muted">ID: <?=$row->uid?> | <?=$row->email?></small>
                    </td>
                    <td>
                        <?php
                            $badge_class = 'bg-primary';
                            if ($row->action_type == 'addition' || $row->action_type == 'manual_add') $badge_class = 'bg-success';
                            if ($row->action_type == 'deduction' || $row->action_type == 'manual_deduct') $badge_class = 'bg-danger';
                            if ($row->action_type == 'refund') $badge_class = 'bg-info';
                        ?>
                        <span class="badge <?=$badge_class?>"><?=ucwords(str_replace('_', ' ', $row->action_type))?></span>
                    </td>
                    <td class="<?=$amount_class?> fw-bold">
                        <?=$amount_prefix?><?=$currency_symbol?><?=number_format($row->amount, 2)?>
                    </td>
                    <td><?=$currency_symbol?><?=number_format($row->balance_before, 2)?></td>
                    <td><?=$currency_symbol?><?=number_format($row->balance_after, 2)?></td>
                    <td><?=htmlspecialchars($row->description)?></td>
                    <td><?=$row->related_id ?: '-'?></td>
                    <td><?=$row->related_type ?: '-'?></td>
                    <td><?=convert_timezone($row->created, 'user')?></td>
                    <td class="text-center">
                        <a href="javascript:void(0)" onclick="viewDetails('<?=$row->ids?>')" 
                           class="btn btn-sm btn-info" title="<?=lang("View Details")?>">
                            <i class="fe fe-eye"></i>
                        </a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <div class="card-footer">
        <div class="float-end">
            <?=$links?>
        </div>
    </div>
</div>
<?php else: ?>
    <?=Modules::run("blocks/empty_data")?>
<?php endif; ?>

<!-- Balance Log Details Modal -->
<div class="modal fade" id="detailsModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><?=lang("Balance Log Details")?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="detailsContent">
                <div class="text-center py-5">
                    <i class="fe fe-loader fa-spin fa-3x"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function exportData(format) {
    const form = document.getElementById('filterForm');
    const url = new URL('<?=cn("balance_logs/export")?>');
    const formData = new FormData(form);
    formData.append('format', format);
    
    // Add all form data to URL
    for (const [key, value] of formData.entries()) {
        if (value) url.searchParams.append(key, value);
    }
    
    window.location.href = url.toString();
}

function viewDetails(ids) {
    const modal = new bootstrap.Modal(document.getElementById('detailsModal'));
    modal.show();
    
    $.ajax({
        url: '<?=cn("balance_logs/ajax_view_details")?>/' + ids,
        type: 'GET',
        success: function(response) {
            $('#detailsContent').html(response);
        },
        error: function() {
            $('#detailsContent').html('<div class="alert alert-danger">Failed to load details</div>');
        }
    });
}
</script>
