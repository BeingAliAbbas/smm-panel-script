
<div class="card p-0 content">
  <div class="card-header">
    <h3 class="card-title" style="color:#fff !important;"><i class="fe fe-map"></i> Sitemap Manager</h3>
  </div>
  <div class="card-body">
    
    <!-- Sitemap Status & Info -->
    <div class="row mb-4">
      <div class="col-md-12">
        <h4 class="border-bottom pb-2 mb-3"><i class="fe fe-info"></i> Sitemap Information</h4>
      </div>
      <div class="col-md-12">
        <?php
        $CI = &get_instance();
        $CI->load->database();
        $sitemap = $CI->db->order_by('id', 'DESC')->limit(1)->get('sitemaps')->row();
        
        if ($sitemap) {
          $sitemap_url = PATH . 'sitemap.xml';
          $sitemap_type = $sitemap->is_custom ? 'Custom (Uploaded)' : 'Auto-Generated';
          $last_modified = $sitemap->last_modified ? date('F j, Y g:i A', strtotime($sitemap->last_modified)) : 'Never';
        ?>
        <div class="alert alert-success">
          <strong><i class="fe fe-check-circle"></i> Sitemap Active</strong>
          <div class="mt-2">
            <p class="mb-1"><strong>Type:</strong> <?=$sitemap_type?></p>
            <p class="mb-1"><strong>URLs Count:</strong> <?=$sitemap->urls_count?></p>
            <p class="mb-1"><strong>File Size:</strong> <?=number_format($sitemap->file_size / 1024, 2)?> KB</p>
            <p class="mb-1"><strong>Last Modified:</strong> <?=$last_modified?></p>
            <p class="mb-1"><strong>Sitemap URL:</strong> <a href="<?=$sitemap_url?>" target="_blank" class="text-primary"><?=$sitemap_url?></a></p>
          </div>
        </div>
        <?php } else { ?>
        <div class="alert alert-warning">
          <strong><i class="fe fe-alert-triangle"></i> No Sitemap Found</strong>
          <p class="mb-0 mt-2">Upload a sitemap or generate one automatically to improve your site's SEO.</p>
        </div>
        <?php } ?>
      </div>
    </div>

    <!-- Sitemap Actions -->
    <div class="row mb-4">
      <div class="col-md-12">
        <h4 class="border-bottom pb-2 mb-3"><i class="fe fe-upload"></i> Upload Custom Sitemap</h4>
      </div>
      <div class="col-md-12">
        <form id="sitemap-upload-form" method="POST" enctype="multipart/form-data">
          <div class="form-group">
            <label class="form-label">Upload Sitemap XML File</label>
            <input type="file" class="form-control" id="sitemap_file" name="sitemap_file" accept=".xml" required>
            <small class="text-muted">Upload a valid sitemap.xml file (max 10MB)</small>
          </div>
          <div class="form-group">
            <label class="custom-switch">
              <input type="checkbox" name="validate_sitemap" class="custom-switch-input" checked value="1">
              <span class="custom-switch-indicator"></span>
              <span class="custom-switch-description">Validate sitemap before upload</span>
            </label>
            <small class="text-muted d-block">Automatically validates XML format and sitemap structure</small>
          </div>
          <button type="button" onclick="uploadSitemap()" class="btn btn-primary"><i class="fe fe-upload"></i> Upload Sitemap</button>
        </form>
      </div>
    </div>

    <!-- Auto-Generate Sitemap -->
    <div class="row mb-4">
      <div class="col-md-12">
        <h4 class="border-bottom pb-2 mb-3"><i class="fe fe-zap"></i> Auto-Generate Sitemap</h4>
      </div>
      <div class="col-md-12">
        <form class="actionForm" action="<?=cn("$module/ajax_sitemap_generate")?>" method="POST" data-redirect="<?=cn($module.'/sitemap_manager')?>">
          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                <label class="form-label">Default Change Frequency</label>
                <select class="form-control" name="changefreq">
                  <option value="daily" <?=(get_option('seo_sitemap_changefreq', 'daily') == 'daily') ? 'selected' : ''?>>Daily</option>
                  <option value="weekly" <?=(get_option('seo_sitemap_changefreq', 'daily') == 'weekly') ? 'selected' : ''?>>Weekly</option>
                  <option value="monthly" <?=(get_option('seo_sitemap_changefreq', 'daily') == 'monthly') ? 'selected' : ''?>>Monthly</option>
                  <option value="yearly" <?=(get_option('seo_sitemap_changefreq', 'daily') == 'yearly') ? 'selected' : ''?>>Yearly</option>
                  <option value="always" <?=(get_option('seo_sitemap_changefreq', 'daily') == 'always') ? 'selected' : ''?>>Always</option>
                  <option value="never" <?=(get_option('seo_sitemap_changefreq', 'daily') == 'never') ? 'selected' : ''?>>Never</option>
                </select>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label class="form-label">Default Priority</label>
                <select class="form-control" name="priority">
                  <option value="1.0" <?=(get_option('seo_sitemap_priority', '0.8') == '1.0') ? 'selected' : ''?>>1.0 (Highest)</option>
                  <option value="0.9" <?=(get_option('seo_sitemap_priority', '0.8') == '0.9') ? 'selected' : ''?>>0.9</option>
                  <option value="0.8" <?=(get_option('seo_sitemap_priority', '0.8') == '0.8') ? 'selected' : ''?>>0.8 (Default)</option>
                  <option value="0.7" <?=(get_option('seo_sitemap_priority', '0.8') == '0.7') ? 'selected' : ''?>>0.7</option>
                  <option value="0.5" <?=(get_option('seo_sitemap_priority', '0.8') == '0.5') ? 'selected' : ''?>>0.5</option>
                  <option value="0.3" <?=(get_option('seo_sitemap_priority', '0.8') == '0.3') ? 'selected' : ''?>>0.3</option>
                </select>
              </div>
            </div>
          </div>
          <div class="form-group">
            <label class="custom-switch">
              <input type="checkbox" name="include_custom_pages" class="custom-switch-input" checked value="1">
              <span class="custom-switch-indicator"></span>
              <span class="custom-switch-description">Include Custom Pages</span>
            </label>
          </div>
          <div class="form-group">
            <label class="custom-switch">
              <input type="checkbox" name="include_services" class="custom-switch-input" value="1">
              <span class="custom-switch-indicator"></span>
              <span class="custom-switch-description">Include Services</span>
            </label>
          </div>
          <button type="submit" class="btn btn-success"><i class="fe fe-zap"></i> Generate Sitemap</button>
          <small class="text-muted d-block mt-2">This will generate a fresh sitemap based on your current pages and settings</small>
        </form>
      </div>
    </div>

    <!-- View Current Sitemap -->
    <?php if ($sitemap && !empty($sitemap->content)) { ?>
    <div class="row mb-4">
      <div class="col-md-12">
        <h4 class="border-bottom pb-2 mb-3"><i class="fe fe-file-text"></i> Current Sitemap Content</h4>
      </div>
      <div class="col-md-12">
        <div class="card bg-light">
          <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-2">
              <span class="font-weight-bold">sitemap.xml</span>
              <button class="btn btn-sm btn-primary" onclick="copySitemapContent()"><i class="fe fe-copy"></i> Copy</button>
            </div>
            <pre id="sitemap-content" style="max-height: 400px; overflow-y: auto; background: #fff; padding: 15px; border-radius: 5px;"><code><?=htmlspecialchars($sitemap->content)?></code></pre>
          </div>
        </div>
      </div>
    </div>
    <?php } ?>

    <!-- Robots.txt Editor -->
    <div class="row mb-4">
      <div class="col-md-12">
        <h4 class="border-bottom pb-2 mb-3"><i class="fe fe-file-text"></i> Robots.txt Editor</h4>
      </div>
      <div class="col-md-12">
        <form class="actionForm" action="<?=cn("$module/ajax_robots_txt")?>" method="POST" data-redirect="<?=cn($module.'/sitemap_manager')?>">
          <div class="form-group">
            <label class="form-label">Robots.txt Content</label>
            <textarea rows="10" name="seo_robots_txt" class="form-control" style="font-family: monospace;"><?=get_option('seo_robots_txt', "User-agent: *\nDisallow: /app/\nDisallow: /install/\nAllow: /\n\nSitemap: " . PATH . "sitemap.xml")?></textarea>
            <small class="text-muted">Configure robots.txt to control search engine crawling</small>
          </div>
          <button type="submit" class="btn btn-primary"><i class="fe fe-save"></i> Save Robots.txt</button>
          <a href="<?=PATH?>robots.txt" target="_blank" class="btn btn-secondary"><i class="fe fe-external-link"></i> View Current Robots.txt</a>
        </form>
      </div>
    </div>

    <!-- Instructions -->
    <div class="row">
      <div class="col-md-12">
        <h4 class="border-bottom pb-2 mb-3"><i class="fe fe-book"></i> How to Submit Your Sitemap</h4>
      </div>
      <div class="col-md-12">
        <div class="card border-primary">
          <div class="card-body">
            <h5 class="text-primary mb-3">Submission Instructions</h5>
            <ol class="mb-0">
              <li class="mb-2">
                <strong>Google Search Console:</strong> 
                <a href="https://search.google.com/search-console" target="_blank">Visit Google Search Console</a>, 
                add your property, then go to "Sitemaps" and submit: <code><?=PATH?>sitemap.xml</code>
              </li>
              <li class="mb-2">
                <strong>Bing Webmaster Tools:</strong> 
                <a href="https://www.bing.com/webmasters" target="_blank">Visit Bing Webmaster</a>, 
                add your site, then submit your sitemap URL
              </li>
              <li class="mb-2">
                <strong>Yandex Webmaster:</strong> 
                <a href="https://webmaster.yandex.com" target="_blank">Visit Yandex</a> 
                and submit your sitemap in the indexing section
              </li>
              <li class="mb-0">
                <strong>Direct Submission:</strong> You can also ping search engines directly with your sitemap URL
              </li>
            </ol>
          </div>
        </div>
      </div>
    </div>

  </div>
</div>

<script>
function copySitemapContent() {
  const content = document.getElementById('sitemap-content').innerText;
  navigator.clipboard.writeText(content).then(function() {
    alert('Sitemap content copied to clipboard!');
  }, function(err) {
    console.error('Could not copy text: ', err);
  });
}

function uploadSitemap() {
  var formData = new FormData(document.getElementById('sitemap-upload-form'));
  
  // Show loading
  var btn = event.target;
  var originalText = btn.innerHTML;
  btn.innerHTML = '<i class="fe fe-loader"></i> Uploading...';
  btn.disabled = true;
  
  $.ajax({
    url: '<?=cn("$module/ajax_sitemap_upload")?>',
    type: 'POST',
    data: formData,
    processData: false,
    contentType: false,
    dataType: 'JSON',
    success: function(response) {
      btn.innerHTML = originalText;
      btn.disabled = false;
      
      if (response.status == 'success') {
        show_message(response.message, 'success');
        setTimeout(function() {
          location.reload();
        }, 1500);
      } else {
        show_message(response.message, 'error');
      }
    },
    error: function(xhr, status, error) {
      btn.innerHTML = originalText;
      btn.disabled = false;
      show_message('Upload failed. Please try again.', 'error');
    }
  });
}

function show_message(message, type) {
  if (typeof notify !== 'undefined') {
    notify(message, type);
  } else {
    alert(message);
  }
}
</script>
