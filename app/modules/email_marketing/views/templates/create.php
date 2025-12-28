<div id="main-modal-content">
  <div class="modal-right">
    <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content">
<<<<<<< HEAD
        <form class="form actionForm" action="<?php echo cn($module . '/ajax_template_edit/' . $template->ids); ?>" data-redirect="<?php echo cn($module . '/templates'); ?>" method="POST">
          <div class="modal-header bg-pantone">
            <h4 class="modal-title"><i class="fas fa-edit"></i> Edit Email Template</h4>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
=======
        <form class="form actionForm" action="<?php echo cn($module . '/ajax_template_create'); ?>" data-redirect="<?php echo cn($module . '/templates'); ?>" method="POST">
          <div class="modal-header bg-pantone">
            <h4 class="modal-title"><i class="fa fa-file-text"></i> Create New Email Template</h4>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            </button>
>>>>>>> dd720c81418616f5ea5455fb1a7b66ce0090eb98
          </div>
          <div class="modal-body">
            <div class="form-body">
              <div class="row justify-content-md-center">
                <div class="col-md-12 col-sm-12 col-xs-12">
                  
                  <div class="form-group">
                    <label>Template Name <span class="text-danger">*</span></label>
<<<<<<< HEAD
                    <input type="text" class="form-control square" name="name" value="<?php echo htmlspecialchars($template->name); ?>" placeholder="e.g., Welcome Email" required>
=======
                    <input type="text" class="form-control square" name="name" placeholder="e.g., Welcome Email" required>
>>>>>>> dd720c81418616f5ea5455fb1a7b66ce0090eb98
                  </div>
                  
                  <div class="form-group">
                    <label>Email Subject <span class="text-danger">*</span></label>
<<<<<<< HEAD
                    <input type="text" class="form-control square" name="subject" value="<?php echo htmlspecialchars($template->subject); ?>" placeholder="e.g., Welcome to {site_name}!" required>
=======
                    <input type="text" class="form-control square" name="subject" placeholder="e.g., Welcome to {site_name}!" required>
>>>>>>> dd720c81418616f5ea5455fb1a7b66ce0090eb98
                    <small class="text-muted">You can use variables like {username}, {email}, {site_name}</small>
                  </div>
                  
                  <div class="form-group">
                    <label>Description</label>
<<<<<<< HEAD
                    <textarea class="form-control square" name="description" rows="2" placeholder="Brief description of this template"><?php echo htmlspecialchars($template->description); ?></textarea>
                  </div>
                  
                  <div class="form-group">
                    <div class="d-flex align-items-center justify-content-between">
                      <label class="mb-0">Email Body (HTML) <span class="text-danger">*</span></label>
                      <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" id="toggle-tinymce" checked>
                        <label class="form-check-label" for="toggle-tinymce">Rich text editor</label>
                      </div>
                    </div>
                    <textarea class="form-control plugin_editor square" name="body" id="email_body" rows="15" required><?php echo htmlspecialchars($template->body); ?></textarea>
=======
                    <textarea class="form-control square" name="description" rows="2" placeholder="Brief description of this template"></textarea>
                  </div>
                  
                  <div class="form-group">
                    <label>Email Body (HTML) <span class="text-danger">*</span></label>
                    <textarea class="form-control square" name="body" id="email_body" rows="15" required></textarea>
>>>>>>> dd720c81418616f5ea5455fb1a7b66ce0090eb98
                    <small class="text-muted">HTML content of the email. Use variables: {username}, {email}, {balance}, {site_name}, {site_url}</small>
                  </div>
                  
                  <div class="alert alert-info">
                    <strong>Available Variables:</strong>
                    <ul class="mb-0">
                      <li><code>{username}</code> - User's name</li>
                      <li><code>{email}</code> - User's email address</li>
                      <li><code>{balance}</code> - User's balance</li>
                      <li><code>{site_name}</code> - Website name</li>
                      <li><code>{site_url}</code> - Website URL</li>
                      <li><code>{current_date}</code> - Current date</li>
                    </ul>
                  </div>
                  
                </div>
              </div>
            </div>
          </div>
          <div class="modal-footer">
<<<<<<< HEAD
            <button type="submit" class="btn round btn-primary btn-min-width me-1 mb-1">Submit</button>
            <button type="button" class="btn round btn-default btn-min-width me-1 mb-1" data-bs-dismiss="modal">Cancel</button>
=======
            <button type="submit" class="btn round btn-primary btn-min-width mr-1 mb-1">Submit</button>
            <button type="button" class="btn round btn-default btn-min-width mr-1 mb-1" data-dismiss="modal">Cancel</button>
>>>>>>> dd720c81418616f5ea5455fb1a7b66ce0090eb98
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
<<<<<<< HEAD

<script>
(function() {
  const selector = '#email_body';
  const toggleSelector = '#toggle-tinymce';

  function initTiny() {
    if (typeof tinymce === 'undefined') return;
    const id = selector.replace('#', '');
    if (tinymce.get(id)) return; // already initialized
    tinymce.init({
      selector: selector,
      height: 420,
      menubar: "file edit view insert format tools table help",
      plugins: "code advlist lists link image preview autolink charmap paste",
      toolbar: "code | undo redo | formatselect | bold italic underline | " +
               "alignleft aligncenter alignright alignjustify | " +
               "bullist numlist outdent indent | link image | removeformat | preview",
      branding: false,
      convert_urls: false,
      paste_auto_cleanup_on_paste: true,
      paste_remove_spans: false,
      setup: function (editor) {
        editor.on('change keyup', function () {
          editor.save(); // sync to textarea
        });
      }
    });
  }

  function removeTiny() {
    if (typeof tinymce === 'undefined') return;
    const ed = tinymce.get(selector.replace('#',''));
    if (ed) {
      ed.save(); // push content back to textarea
      ed.remove();
    }
  }

  $(document).on('change', toggleSelector, function() {
    if (this.checked) {
      initTiny();
    } else {
      removeTiny();
    }
  });

  // Initialize on ready if toggle is on
  $(document).ready(function() {
    if ($(toggleSelector).is(':checked')) {
      initTiny();
    }
  });

  // Clean up when modal closes
  $('#modal-ajax').on('hidden.bs.modal', function() {
    removeTiny();
  });
})();
</script>
=======
>>>>>>> dd720c81418616f5ea5455fb1a7b66ce0090eb98
