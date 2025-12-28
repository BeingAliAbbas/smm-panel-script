<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<style>
.transaction-stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
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
.stat-card.blue { background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); }
.stat-card.orange { background: linear-gradient(135deg, #fa709a 0%, #fee140 100%); }
.stat-card.red { background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); }
.stat-card h3 { margin: 0; font-size: 2rem; font-weight: bold; }
.stat-card p { margin: 0.5rem 0 0; opacity: 0.9; font-size: 0.9rem; }
.filters-card { background: #f8f9fa; padding: 1.5rem; border-radius: 12px; margin-bottom: 1.5rem; }
.filter-row { display: flex; gap: 1rem; flex-wrap: wrap; align-items: end; }
.filter-group { flex: 1; min-width: 180px; }
.export-buttons { display: flex; gap: 0.5rem; margin-top: 1rem; }
</style>

<div class="page-header">
    <h1 class="page-title">
        <i class="fe fe-credit-card"></i> <?=lang("Advanced Transaction Dashboard")?>
    </h1>
    <div class="page-options">
        <a href="<?=cn('transactions')?>" class="btn btn-outline-secondary">
            <i class="fe fe-list"></i> <?=lang("Simple View")?>
        </a>
    </div>
</div>

<!-- Statistics Summary Boxes -->
<div class="transaction-stats-grid">
    <div class="stat-card blue">
        <h3><?=$currency_symbol?><?=number_format($stats['total_amount'], 2)?></h3>
        <p><?=lang("Total Transactions")?></p>
        <small><?=$stats['total_count']?> <?=lang("transactions")?></small>
    </div>
    <div class="stat-card orange">
        <h3><?=$stats['pending_count']?></h3>
        <p><?=lang("Pending")?></p>
        <small><?=$currency_symbol?><?=number_format($stats['pending_amount'], 2)?></small>
    </div>
    <div class="stat-card green">
        <h3><?=$stats['completed_count']?></h3>
        <p><?=lang("Completed")?></p>
        <small><?=$currency_symbol?><?=number_format($stats['completed_amount'], 2)?></small>
    </div>
    <div class="stat-card red">
        <h3><?=$stats['failed_count']?></h3>
        <p><?=lang("Failed")?></p>
        <small><?=$currency_symbol?><?=number_format($stats['failed_amount'], 2)?></small>
    </div>
    <div class="stat-card">
        <h3><?=$currency_symbol?><?=number_format($stats['earnings'], 2)?></h3>
        <p><?=lang("Total Earnings")?></p>
        <small><?=lang("Completed - Fees")?></small>
    </div>
</div>

<!-- Advanced Filters -->
<div class="filters-card">
    <h5 class="mb-3"><i class="fe fe-filter"></i> <?=lang("Advanced Filters")?></h5>
    <form method="GET" action="<?=cn('transactions/admin_dashboard')?>" id="filterForm">
        <div class="filter-row">
            <div class="filter-group">
                <label><?=lang("User Email")?></label>
                <input type="text" name="user_email" class="form-control" 
                       value="<?=get('user_email')?>" placeholder="<?=lang("Filter by email")?>">
            </div>
            <div class="filter-group">
                <label><?=lang("Payment Method")?></label>
                <select name="payment_method" class="form-control">
                    <option value=""><?=lang("All Methods")?></option>
                    <?php foreach ($payment_methods as $method): ?>
                        <option value="<?=$method?>" <?=get('payment_method') == $method ? 'selected' : ''?>>
                            <?=ucfirst($method)?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="filter-group">
                <label><?=lang("Status")?></label>
                <select name="status" class="form-control">
                    <option value=""><?=lang("All Status")?></option>
                    <option value="1" <?=get('status') === '1' ? 'selected' : ''?>><?=lang("Paid")?></option>
                    <option value="0" <?=get('status') === '0' ? 'selected' : ''?>><?=lang("Pending")?></option>
                    <option value="-1" <?=get('status') === '-1' ? 'selected' : ''?>><?=lang("Failed")?></option>
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
                <a href="<?=cn('transactions/admin_dashboard')?>" class="btn btn-secondary ms-2">
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

<!-- Transactions Table -->
<?php if (!empty($transactions)): ?>
<div class="card">
    <div class="card-header">
        <h3 class="card-title"><?=lang("Transactions List")?></h3>
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
                    <th><?=lang("Transaction ID")?></th>
                    <th><?=lang("Method")?></th>
                    <th><?=lang("Amount")?></th>
                    <th><?=lang("Fee")?></th>
                    <th><?=lang("Note")?></th>
                    <th><?=lang("Date")?></th>
                    <th><?=lang("Status")?></th>
                    <th class="text-center"><?=lang("Actions")?></th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $i = $offset + 1;
                foreach ($transactions as $row): 
                ?>
                <tr>
                    <td class="text-center"><?=$i++?></td>
                    <td>
                        <div><?=$row->email?></div>
                        <?php if ($row->payer_email): ?>
                            <small class="text-muted">Payer: <?=$row->payer_email?></small>
                        <?php endif; ?>
                    </td>
                    <td><?=$row->transaction_id?></td>
                    <td>
                        <?php if (in_array(strtolower($row->type), ["bonus","manual","other"])): ?>
                            <?=ucfirst($row->type)?>
                        <?php else: ?>
                            <img class="payment" src="<?=BASE?>/assets/images/payments/<?=strtolower($row->type)?>.png" 
                                 alt="<?=$row->type?>" style="height: 20px;">
                        <?php endif; ?>
                    </td>
                    <td><?=$currency_symbol?><?=number_format($row->amount, 2)?></td>
                    <td><?=$currency_symbol?><?=number_format($row->txn_fee, 2)?></td>
                    <td><?=$row->note?></td>
                    <td><?=convert_timezone($row->created, 'user')?></td>
                    <td>
                        <?php if ($row->status == 1): ?>
                            <span class="badge bg-success"><?=lang("Paid")?></span>
                        <?php elseif ($row->status == 0): ?>
                            <span class="badge bg-warning text-dark"><?=lang("Pending")?></span>
                        <?php else: ?>
                            <span class="badge bg-danger"><?=lang("Failed")?></span>
                        <?php endif; ?>
                    </td>
                    <td class="text-center">
                        <a href="javascript:void(0)" onclick="viewDetails('<?=$row->ids?>')" 
                           class="btn btn-sm btn-info" title="<?=lang("View Details")?>">
                            <i class="fe fe-eye"></i>
                        </a>
                        <a href="<?=cn("transactions/update/".$row->ids)?>" class="btn btn-sm btn-primary ajaxModal">
                            <i class="fe fe-edit"></i>
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

<!-- Transaction Details Modal -->
<div class="modal fade" id="detailsModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><?=lang("Transaction Details")?></h5>
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
    const url = new URL('<?=cn("transactions/export")?>');
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
        url: '<?=cn("transactions/ajax_view_details")?>/' + ids,
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
