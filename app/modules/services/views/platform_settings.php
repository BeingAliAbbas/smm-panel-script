<style>
.platform-settings-container {
    max-width: 1400px;
    margin: 0 auto;
}

.platform-card {
    background: #fff;
    border: 1px solid #ddd;
    border-radius: 8px;
    padding: 20px;
    margin-bottom: 20px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.05);
}

.platform-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 15px;
    padding-bottom: 15px;
    border-bottom: 2px solid #f0f0f0;
}

.platform-info {
    display: flex;
    align-items: center;
    gap: 15px;
}

.platform-icon-display {
    width: 40px;
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 24px;
    border: 1px solid #ddd;
    border-radius: 6px;
    padding: 5px;
}

.platform-icon-display img {
    max-width: 100%;
    max-height: 100%;
    object-fit: contain;
}

.platform-details h4 {
    margin: 0 0 5px 0;
    font-size: 18px;
}

.platform-slug {
    color: #666;
    font-size: 14px;
}

.badge-status {
    padding: 5px 12px;
    border-radius: 4px;
    font-size: 12px;
    font-weight: 600;
}

.badge-active {
    background: #28a745;
    color: white;
}

.badge-inactive {
    background: #dc3545;
    color: white;
}

.keywords-section {
    margin-top: 15px;
}

.keyword-tags {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
    margin-top: 10px;
}

.keyword-tag {
    background: #e9ecef;
    border: 1px solid #ced4da;
    border-radius: 4px;
    padding: 6px 12px;
    font-size: 13px;
    display: inline-flex;
    align-items: center;
    gap: 8px;
}

.keyword-tag .priority {
    background: #007bff;
    color: white;
    border-radius: 3px;
    padding: 2px 6px;
    font-size: 11px;
}

.btn-group {
    display: flex;
    gap: 8px;
}

.action-buttons {
    display: flex;
    flex-direction: column;
    gap: 10px;
}

.modal-body .form-group {
    margin-bottom: 15px;
}

.modal-body label {
    font-weight: 600;
    margin-bottom: 5px;
    display: block;
}

.header-actions {
    display: flex;
    gap: 10px;
    margin-bottom: 20px;
    justify-content: space-between;
    align-items: center;
}
</style>

<div class="platform-settings-container">
    <div class="container-fluid">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">
                    <i class="fas fa-cog"></i> Platform & Icon Management
                </h4>
            </div>
            <div class="card-body">
                <div class="header-actions">
                    <div>
                        <button class="btn btn-primary" onclick="showAddPlatformModal()">
                            <i class="fas fa-plus"></i> Add New Platform
                        </button>
                        <button class="btn btn-info" onclick="autoAssignPlatforms()">
                            <i class="fas fa-magic"></i> Auto-Assign Categories
                        </button>
                    </div>
                    <button class="btn btn-warning" onclick="clearPlatformCache()">
                        <i class="fas fa-sync"></i> Clear Cache
                    </button>
                </div>

                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i> 
                    <strong>Instructions:</strong> Manage platforms, keywords, and icons here. Changes take effect immediately on the order/add page. 
                    Icons can be Font Awesome classes (e.g., "fa-brands fa-facebook") or image URLs (GIFs supported).
                </div>

                <?php if (!empty($platforms)): ?>
                    <?php foreach ($platforms as $platform): ?>
                        <div class="platform-card" id="platform-<?=$platform->id?>">
                            <div class="platform-header">
                                <div class="platform-info">
                                    <div class="platform-icon-display">
                                        <?php if (!empty($platform->icon_url)): ?>
                                            <img src="<?=htmlspecialchars($platform->icon_url)?>" alt="<?=htmlspecialchars($platform->name)?>">
                                        <?php elseif (!empty($platform->icon_class)): ?>
                                            <i class="<?=htmlspecialchars($platform->icon_class)?>"></i>
                                        <?php else: ?>
                                            <i class="fas fa-question"></i>
                                        <?php endif; ?>
                                    </div>
                                    <div class="platform-details">
                                        <h4><?=htmlspecialchars($platform->name)?></h4>
                                        <span class="platform-slug">Slug: <?=htmlspecialchars($platform->slug)?> | Sort: <?=$platform->sort_order?></span>
                                    </div>
                                    <span class="badge-status <?=$platform->status == 1 ? 'badge-active' : 'badge-inactive'?>">
                                        <?=$platform->status == 1 ? 'Active' : 'Inactive'?>
                                    </span>
                                </div>
                                <div class="action-buttons">
                                    <div class="btn-group">
                                        <button class="btn btn-sm btn-primary" onclick="editPlatform(<?=$platform->id?>)">
                                            <i class="fas fa-edit"></i> Edit
                                        </button>
                                        <button class="btn btn-sm btn-success" onclick="addKeyword(<?=$platform->id?>)">
                                            <i class="fas fa-plus"></i> Add Keyword
                                        </button>
                                        <?php if ($platform->slug != 'all' && $platform->slug != 'other'): ?>
                                            <button class="btn btn-sm btn-danger" onclick="deletePlatform(<?=$platform->id?>)">
                                                <i class="fas fa-trash"></i> Delete
                                            </button>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>

                            <?php if (!empty($platform->keywords)): ?>
                                <div class="keywords-section">
                                    <strong><i class="fas fa-tags"></i> Keywords:</strong>
                                    <div class="keyword-tags">
                                        <?php foreach ($platform->keywords as $keyword): ?>
                                            <span class="keyword-tag">
                                                <?=htmlspecialchars($keyword->keyword)?>
                                                <span class="priority"><?=$keyword->priority?></span>
                                                <button class="btn btn-sm btn-link p-0" onclick="deleteKeyword(<?=$keyword->id?>)" style="color: #dc3545;">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                            </span>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle"></i> No platforms found. Please run the migration SQL file first.
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Add/Edit Platform Modal -->
<div class="modal fade" id="platformModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="platformModalTitle">Add Platform</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="platformForm">
                    <input type="hidden" id="platform_id" name="id">
                    
                    <div class="form-group mb-3">
                        <label for="platform_name">Platform Name *</label>
                        <input type="text" class="form-control" id="platform_name" name="name" required>
                    </div>
                    
                    <div class="form-group mb-3">
                        <label for="platform_slug">Slug * (lowercase, no spaces)</label>
                        <input type="text" class="form-control" id="platform_slug" name="slug" required>
                    </div>
                    
                    <div class="form-group mb-3">
                        <label for="platform_icon_class">Font Awesome Icon Class</label>
                        <input type="text" class="form-control" id="platform_icon_class" name="icon_class" placeholder="e.g., fa-brands fa-facebook">
                        <small class="form-text text-muted">Leave empty if using URL</small>
                    </div>
                    
                    <div class="form-group mb-3">
                        <label for="platform_icon_url">Icon URL (GIF/Image)</label>
                        <input type="text" class="form-control" id="platform_icon_url" name="icon_url" placeholder="https://example.com/icon.gif">
                        <small class="form-text text-muted">URL takes priority over Font Awesome</small>
                    </div>
                    
                    <div class="form-group mb-3">
                        <label for="platform_sort_order">Sort Order</label>
                        <input type="number" class="form-control" id="platform_sort_order" name="sort_order" value="0">
                    </div>
                    
                    <div class="form-group mb-3">
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" id="platform_status" name="status" checked>
                            <label class="form-check-label" for="platform_status">Active</label>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="savePlatform()">Save Platform</button>
            </div>
        </div>
    </div>
</div>

<!-- Add Keyword Modal -->
<div class="modal fade" id="keywordModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add Keyword</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="keywordForm">
                    <input type="hidden" id="keyword_platform_id" name="platform_id">
                    
                    <div class="form-group mb-3">
                        <label for="keyword_keyword">Keyword *</label>
                        <input type="text" class="form-control" id="keyword_keyword" name="keyword" required>
                        <small class="form-text text-muted">Used to detect platform from category/service names</small>
                    </div>
                    
                    <div class="form-group mb-3">
                        <label for="keyword_priority">Priority</label>
                        <input type="number" class="form-control" id="keyword_priority" name="priority" value="10">
                        <small class="form-text text-muted">Higher priority = checked first</small>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="saveKeyword()">Save Keyword</button>
            </div>
        </div>
    </div>
</div>

<script>
var platformData = <?=json_encode($platforms)?>;
var platformModal, keywordModal;

// Initialize Bootstrap 5 modals on page load
document.addEventListener('DOMContentLoaded', function() {
    var platformModalEl = document.getElementById('platformModal');
    var keywordModalEl = document.getElementById('keywordModal');
    
    if (platformModalEl) {
        platformModal = new bootstrap.Modal(platformModalEl);
    }
    if (keywordModalEl) {
        keywordModal = new bootstrap.Modal(keywordModalEl);
    }
});

function showAddPlatformModal() {
    $('#platformModalTitle').text('Add Platform');
    $('#platformForm')[0].reset();
    $('#platform_id').val('');
    $('#platform_status').prop('checked', true);
    if (platformModal) {
        platformModal.show();
    }
}

function editPlatform(id) {
    var platform = platformData.find(p => p.id == id);
    if (!platform) return;
    
    $('#platformModalTitle').text('Edit Platform');
    $('#platform_id').val(platform.id);
    $('#platform_name').val(platform.name);
    $('#platform_slug').val(platform.slug);
    $('#platform_icon_class').val(platform.icon_class || '');
    $('#platform_icon_url').val(platform.icon_url || '');
    $('#platform_sort_order').val(platform.sort_order);
    $('#platform_status').prop('checked', platform.status == 1);
    if (platformModal) {
        platformModal.show();
    }
}

function savePlatform() {
    // Build form data manually to handle checkbox correctly
    var formData = {
        id: $('#platform_id').val(),
        name: $('#platform_name').val(),
        slug: $('#platform_slug').val(),
        icon_class: $('#platform_icon_class').val(),
        icon_url: $('#platform_icon_url').val(),
        sort_order: $('#platform_sort_order').val(),
        status: $('#platform_status').is(':checked') ? 1 : 0
    };
    
    console.log('Saving platform with data:', formData);
    
    $.ajax({
        url: '<?=cn("services/ajax_save_platform")?>',
        type: 'POST',
        data: formData,
        dataType: 'json',
        success: function(response) {
            console.log('Save platform response:', response);
            if (response.status === 'success') {
                show_message(response.message, 'success');
                if (platformModal) {
                    platformModal.hide();
                }
                setTimeout(function() {
                    location.reload();
                }, 1000);
            } else {
                show_message(response.message, 'error');
            }
        },
        error: function(xhr, status, error) {
            console.error('AJAX Error:', status, error);
            console.error('Response:', xhr.responseText);
            console.error('Status Code:', xhr.status);
            show_message('Failed to save platform. Check console for details.', 'error');
        }
    });
}

function deletePlatform(id) {
    if (!confirm('Are you sure you want to delete this platform? This will also delete all associated keywords.')) {
        return;
    }
    
    console.log('Deleting platform:', id);
    
    $.ajax({
        url: '<?=cn("services/ajax_delete_platform")?>',
        type: 'POST',
        data: { id: id },
        dataType: 'json',
        success: function(response) {
            console.log('Delete platform response:', response);
            if (response.status === 'success') {
                show_message(response.message, 'success');
                $('#platform-' + id).fadeOut(function() {
                    $(this).remove();
                });
            } else {
                show_message(response.message, 'error');
            }
        },
        error: function(xhr, status, error) {
            console.error('AJAX Error:', status, error);
            console.error('Response:', xhr.responseText);
            console.error('Status Code:', xhr.status);
            show_message('Failed to delete platform. Check console for details.', 'error');
        }
    });
}

function addKeyword(platformId) {
    $('#keyword_platform_id').val(platformId);
    $('#keyword_keyword').val('');
    $('#keyword_priority').val('10');
    if (keywordModal) {
        keywordModal.show();
    }
}

function saveKeyword() {
    var formData = {
        platform_id: $('#keyword_platform_id').val(),
        keyword: $('#keyword_keyword').val(),
        priority: $('#keyword_priority').val()
    };
    
    console.log('Saving keyword with data:', formData);
    
    $.ajax({
        url: '<?=cn("services/ajax_save_keyword")?>',
        type: 'POST',
        data: formData,
        dataType: 'json',
        success: function(response) {
            console.log('Save keyword response:', response);
            if (response.status === 'success') {
                show_message(response.message, 'success');
                if (keywordModal) {
                    keywordModal.hide();
                }
                setTimeout(function() {
                    location.reload();
                }, 1000);
            } else {
                show_message(response.message, 'error');
            }
        },
        error: function(xhr, status, error) {
            console.error('AJAX Error:', status, error);
            console.error('Response:', xhr.responseText);
            console.error('Status Code:', xhr.status);
            show_message('Failed to save keyword. Check console for details.', 'error');
        }
    });
}

function deleteKeyword(id) {
    if (!confirm('Delete this keyword?')) {
        return;
    }
    
    console.log('Deleting keyword:', id);
    
    $.ajax({
        url: '<?=cn("services/ajax_delete_keyword")?>',
        type: 'POST',
        data: { id: id },
        dataType: 'json',
        success: function(response) {
            console.log('Delete keyword response:', response);
            if (response.status === 'success') {
                show_message(response.message, 'success');
                setTimeout(function() {
                    location.reload();
                }, 500);
            } else {
                show_message(response.message, 'error');
            }
        },
        error: function(xhr, status, error) {
            console.error('AJAX Error:', status, error);
            console.error('Response:', xhr.responseText);
            console.error('Status Code:', xhr.status);
            show_message('Failed to delete keyword. Check console for details.', 'error');
        }
    });
}

function clearPlatformCache() {
    console.log('Clearing platform cache');
    
    $.ajax({
        url: '<?=cn("services/ajax_clear_platform_cache")?>',
        type: 'POST',
        dataType: 'json',
        success: function(response) {
            console.log('Clear cache response:', response);
            if (response.status === 'success') {
                show_message(response.message, 'success');
            } else {
                show_message(response.message, 'error');
            }
        },
        error: function(xhr, status, error) {
            console.error('AJAX Error:', status, error);
            console.error('Response:', xhr.responseText);
            console.error('Status Code:', xhr.status);
            show_message('Failed to clear cache. Check console for details.', 'error');
        }
    });
}

function autoAssignPlatforms() {
    if (!confirm('This will automatically assign platforms to all categories based on keywords. Continue?')) {
        return;
    }
    
    console.log('Auto-assigning platforms');
    
    $.ajax({
        url: '<?=cn("services/ajax_auto_assign_platforms")?>',
        type: 'POST',
        dataType: 'json',
        success: function(response) {
            console.log('Auto-assign response:', response);
            if (response.status === 'success') {
                show_message(response.message, 'success');
            } else {
                show_message(response.message, 'error');
            }
        },
        error: function(xhr, status, error) {
            console.error('AJAX Error:', status, error);
            console.error('Response:', xhr.responseText);
            console.error('Status Code:', xhr.status);
            show_message('Failed to auto-assign platforms. Check console for details.', 'error');
        }
    });
}

function show_message(message, type) {
    var alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
    var html = '<div class="alert ' + alertClass + ' alert-dismissible fade show" role="alert">' +
               '<i class="fas fa-' + (type === 'success' ? 'check' : 'times') + '-circle"></i> ' + message +
               '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>' +
               '</div>';
    
    $('.header-actions').after(html);
    
    setTimeout(function() {
        $('.alert').fadeOut(function() {
            $(this).remove();
        });
    }, 3000);
}
</script>
