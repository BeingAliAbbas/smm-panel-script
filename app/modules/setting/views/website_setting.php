
<<<<<<< HEAD
    <div class="card p-0 content">
      <div class="card-header">
        <h3 class="card-title" style="color:#fff !important;"><i class="fe fe-globe"></i> <?=lang("website_setting")?> - Advanced SEO</h3>
      </div>
      <div class="card-body">
        <form class="actionForm" action="<?=cn("$module/ajax_general_settings")?>" method="POST" data-redirect="<?=cn($module)?>">
          
          <!-- Basic Settings Section -->
          <div class="row mb-4">
            <div class="col-md-12">
              <h4 class="border-bottom pb-2 mb-3"><i class="fe fe-settings"></i> Basic Settings</h4>
            </div>
=======
    <div class="card content">
      <div class="card-header" style="border: 0.1px solid #05d0a0; border-radius: 3.5px 3.5px 0px 0px; background: #05d0a0;">
        <h3 class="card-title"><i class="fe fe-globe"></i> <?=lang("website_setting")?></h3>
      </div>
      <div class="card-body">
        <form class="actionForm" action="<?=cn("$module/ajax_general_settings")?>" method="POST" data-redirect="<?=cn($module)?>">
          <div class="row">
>>>>>>> dd720c81418616f5ea5455fb1a7b66ce0090eb98
            <div class="col-md-12 col-lg-12">
              <div class="form-group">
                <div class="form-label"><?=lang("Maintenance_mode")?></div>
                <label class="custom-switch">
                  <input type="hidden" name="is_maintenance_mode" value="0">
                  <input type="checkbox" name="is_maintenance_mode" class="custom-switch-input" <?=(get_option("is_maintenance_mode", 0) == 1) ? "checked" : ""?> value="1">
                  <span class="custom-switch-indicator"></span>
                  <span class="custom-switch-description"><?=lang("Active")?></span>
                </label>
                <br>
                <small class="text-danger"><strong><?=lang("note")?></strong> <?=lang("link_to_access_the_maintenance_mode")?></small> <br>
                <a href="<?=cn('maintenance/access')?>"><span class="text-link"><?=PATH?>maintenance/access</span></a>
              </div>
              <div class="form-group">
                <label class="form-label"><?=lang("website_name")?></label>
                <input class="form-control" name="website_name" value="<?=get_option('website_name', "SmartPanel")?>">
              </div>  

              <div class="form-group">
                <label class="form-label"><?=lang("website_description")?></label>
                <textarea rows="3" name="website_desc" class="form-control"><?=get_option('website_desc', "SmartPanel - #1 SMM Reseller Panel - Best SMM Panel for Resellers. Also well known for SmartPanel and Cheap SMM Panel for all kind of Social Media Marketing Services. SMM Panel for Facebook, Instagram, YouTube and more services!")?>
                </textarea>
              </div>

              <div class="form-group">
                <label class="form-label"><?=lang("website_keywords")?></label>
                <textarea rows="3" name="website_keywords" class="form-control"><?=get_option('website_keywords', "smm panel, SmartPanel, smm reseller panel, smm provider panel, reseller panel, instagram panel, resellerpanel, social media reseller panel, smmpanel, panelsmm, smm, panel, socialmedia, instagram reseller panel")?>
                </textarea>
              </div>
              <div class="form-group">
                <label class="form-label"><?=lang("website_title")?></label>
                <input class="form-control" name="website_title" value="<?=get_option('website_title', "SmartPanel - SMM Panel Reseller Tool")?>">
              </div>
            </div>
<<<<<<< HEAD
          </div>

          <!-- Advanced Meta Tags Section -->
          <div class="row mb-4">
            <div class="col-md-12">
              <h4 class="border-bottom pb-2 mb-3"><i class="fe fe-tag"></i> Advanced Meta Tags</h4>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label class="form-label">Meta Author</label>
                <input class="form-control" name="seo_meta_author" value="<?=get_option('seo_meta_author', '')?>">
                <small class="text-muted">Author name for meta tags</small>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label class="form-label">Meta Robots</label>
                <select class="form-control" name="seo_meta_robots">
                  <option value="index, follow" <?=(get_option('seo_meta_robots', 'index, follow') == 'index, follow') ? 'selected' : ''?>>Index, Follow (Default)</option>
                  <option value="noindex, follow" <?=(get_option('seo_meta_robots', 'index, follow') == 'noindex, follow') ? 'selected' : ''?>>No Index, Follow</option>
                  <option value="index, nofollow" <?=(get_option('seo_meta_robots', 'index, follow') == 'index, nofollow') ? 'selected' : ''?>>Index, No Follow</option>
                  <option value="noindex, nofollow" <?=(get_option('seo_meta_robots', 'index, follow') == 'noindex, nofollow') ? 'selected' : ''?>>No Index, No Follow</option>
                </select>
                <small class="text-muted">Default robots directive for pages</small>
              </div>
            </div>
            <div class="col-md-12">
              <div class="form-group">
                <label class="form-label">Canonical URL (Base URL)</label>
                <input class="form-control" name="seo_canonical_url" value="<?=get_option('seo_canonical_url', PATH)?>" placeholder="https://yourdomain.com/">
                <small class="text-muted">Base canonical URL for your site (leave blank to use default)</small>
              </div>
            </div>
          </div>

          <!-- Open Graph (OG) Tags Section -->
          <div class="row mb-4">
            <div class="col-md-12">
              <h4 class="border-bottom pb-2 mb-3"><i class="fab fa-facebook"></i> Open Graph (OG) Tags - Facebook</h4>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label class="form-label">OG Site Name</label>
                <input class="form-control" name="seo_og_site_name" value="<?=get_option('seo_og_site_name', get_option('website_name', 'SmartPanel'))?>">
                <small class="text-muted">Site name for social sharing</small>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label class="form-label">OG Type</label>
                <select class="form-control" name="seo_og_type">
                  <option value="website" <?=(get_option('seo_og_type', 'website') == 'website') ? 'selected' : ''?>>Website</option>
                  <option value="article" <?=(get_option('seo_og_type', 'website') == 'article') ? 'selected' : ''?>>Article</option>
                  <option value="business.business" <?=(get_option('seo_og_type', 'website') == 'business.business') ? 'selected' : ''?>>Business</option>
                </select>
              </div>
            </div>
            <div class="col-md-12">
              <div class="form-group">
                <label class="form-label">OG Title (Default)</label>
                <input class="form-control" name="seo_og_title" value="<?=get_option('seo_og_title', get_option('website_title', ''))?>">
                <small class="text-muted">Default title for social sharing previews</small>
              </div>
            </div>
            <div class="col-md-12">
              <div class="form-group">
                <label class="form-label">OG Description</label>
                <textarea rows="2" name="seo_og_description" class="form-control"><?=get_option('seo_og_description', get_option('website_desc', ''))?></textarea>
                <small class="text-muted">Default description for social sharing</small>
              </div>
            </div>
            <div class="col-md-12">
              <div class="form-group">
                <label class="form-label">OG Image URL</label>
                <input class="form-control" name="seo_og_image" value="<?=get_option('seo_og_image', '')?>" placeholder="https://yourdomain.com/images/og-image.jpg">
                <small class="text-muted">Default image for social sharing (recommended: 1200x630px)</small>
              </div>
            </div>
          </div>

          <!-- Twitter Card Tags Section -->
          <div class="row mb-4">
            <div class="col-md-12">
              <h4 class="border-bottom pb-2 mb-3"><i class="fab fa-twitter"></i> Twitter Card Tags</h4>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label class="form-label">Twitter Card Type</label>
                <select class="form-control" name="seo_twitter_card">
                  <option value="summary" <?=(get_option('seo_twitter_card', 'summary') == 'summary') ? 'selected' : ''?>>Summary</option>
                  <option value="summary_large_image" <?=(get_option('seo_twitter_card', 'summary') == 'summary_large_image') ? 'selected' : ''?>>Summary Large Image</option>
                  <option value="app" <?=(get_option('seo_twitter_card', 'summary') == 'app') ? 'selected' : ''?>>App</option>
                  <option value="player" <?=(get_option('seo_twitter_card', 'summary') == 'player') ? 'selected' : ''?>>Player</option>
                </select>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label class="form-label">Twitter Site Handle</label>
                <input class="form-control" name="seo_twitter_site" value="<?=get_option('seo_twitter_site', '')?>" placeholder="@yourhandle">
                <small class="text-muted">Your Twitter @username</small>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label class="form-label">Twitter Creator Handle</label>
                <input class="form-control" name="seo_twitter_creator" value="<?=get_option('seo_twitter_creator', '')?>" placeholder="@creator">
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label class="form-label">Twitter Image URL</label>
                <input class="form-control" name="seo_twitter_image" value="<?=get_option('seo_twitter_image', get_option('seo_og_image', ''))?>" placeholder="https://yourdomain.com/images/twitter-card.jpg">
              </div>
            </div>
          </div>

          <!-- Structured Data / Schema Section -->
          <div class="row mb-4">
            <div class="col-md-12">
              <h4 class="border-bottom pb-2 mb-3"><i class="fe fe-code"></i> Structured Data & Schema</h4>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label class="form-label">Schema Type</label>
                <select class="form-control" name="seo_schema_type">
                  <option value="Organization" <?=(get_option('seo_schema_type', 'Organization') == 'Organization') ? 'selected' : ''?>>Organization</option>
                  <option value="LocalBusiness" <?=(get_option('seo_schema_type', 'Organization') == 'LocalBusiness') ? 'selected' : ''?>>Local Business</option>
                  <option value="Corporation" <?=(get_option('seo_schema_type', 'Organization') == 'Corporation') ? 'selected' : ''?>>Corporation</option>
                  <option value="WebSite" <?=(get_option('seo_schema_type', 'Organization') == 'WebSite') ? 'selected' : ''?>>WebSite</option>
                </select>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label class="form-label">Organization Logo URL</label>
                <input class="form-control" name="seo_schema_logo" value="<?=get_option('seo_schema_logo', '')?>" placeholder="https://yourdomain.com/logo.png">
              </div>
            </div>
            <div class="col-md-12">
              <div class="form-group">
                <label class="custom-switch">
                  <input type="hidden" name="seo_breadcrumb_enabled" value="0">
                  <input type="checkbox" name="seo_breadcrumb_enabled" class="custom-switch-input" <?=(get_option("seo_breadcrumb_enabled", 1) == 1) ? "checked" : ""?> value="1">
                  <span class="custom-switch-indicator"></span>
                  <span class="custom-switch-description">Enable Breadcrumb Schema</span>
                </label>
                <small class="text-muted d-block">Automatically adds breadcrumb structured data to pages</small>
              </div>
            </div>
          </div>

          <!-- Favicon Manager Section -->
          <div class="row mb-4">
            <div class="col-md-12">
              <h4 class="border-bottom pb-2 mb-3"><i class="fe fe-image"></i> Favicon Manager</h4>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label class="form-label">Favicon (ICO/PNG)</label>
                <input class="form-control" name="seo_favicon" value="<?=get_option('seo_favicon', '')?>" placeholder="https://yourdomain.com/favicon.ico">
                <small class="text-muted">Standard favicon (16x16 or 32x32)</small>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label class="form-label">Apple Touch Icon</label>
                <input class="form-control" name="seo_apple_touch_icon" value="<?=get_option('seo_apple_touch_icon', '')?>" placeholder="https://yourdomain.com/apple-touch-icon.png">
                <small class="text-muted">For iOS devices (180x180px recommended)</small>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label class="form-label">Favicon 32x32</label>
                <input class="form-control" name="seo_favicon_32" value="<?=get_option('seo_favicon_32', '')?>" placeholder="https://yourdomain.com/favicon-32x32.png">
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label class="form-label">Favicon 16x16</label>
                <input class="form-control" name="seo_favicon_16" value="<?=get_option('seo_favicon_16', '')?>" placeholder="https://yourdomain.com/favicon-16x16.png">
              </div>
            </div>
          </div>

          <!-- Custom Code Injection Section -->
          <div class="row mb-4">
            <div class="col-md-12">
              <h4 class="border-bottom pb-2 mb-3"><i class="fe fe-terminal"></i> Custom Code Injection</h4>
            </div>
            <div class="col-md-12">
              <div class="form-group">
                <label class="form-label">Custom Header Code (Before &lt;/head&gt;)</label>
                <textarea rows="5" name="seo_header_code" class="form-control" placeholder="<!-- Google Analytics, verification tags, etc. -->"><?=get_option('seo_header_code', '')?></textarea>
                <small class="text-muted">Add custom meta tags, analytics code, or verification tags</small>
              </div>
            </div>
            <div class="col-md-12">
              <div class="form-group">
                <label class="form-label">Custom Footer Code (Before &lt;/body&gt;)</label>
                <textarea rows="5" name="seo_footer_code" class="form-control" placeholder="<!-- Additional scripts, tracking pixels, etc. -->"><?=get_option('seo_footer_code', '')?></textarea>
                <small class="text-muted">Add custom scripts or tracking pixels at the end of pages</small>
              </div>
            </div>
          </div>

          <!-- SEO Tools Section -->
          <div class="row mb-4">
            <div class="col-md-12">
              <h4 class="border-bottom pb-2 mb-3"><i class="fe fe-tool"></i> SEO Tools & Helpers</h4>
            </div>
            <div class="col-md-12">
              <div class="form-group">
                <label class="custom-switch">
                  <input type="hidden" name="seo_auto_alt_text" value="0">
                  <input type="checkbox" name="seo_auto_alt_text" class="custom-switch-input" <?=(get_option("seo_auto_alt_text", 0) == 1) ? "checked" : ""?> value="1">
                  <span class="custom-switch-indicator"></span>
                  <span class="custom-switch-description">Enable Auto Image Alt-Text Helper</span>
                </label>
                <small class="text-muted d-block">Automatically suggests alt-text for images based on filename</small>
              </div>
            </div>
          </div>

          <!-- SEO Preview Widget -->
          <div class="row mb-4">
            <div class="col-md-12">
              <h4 class="border-bottom pb-2 mb-3"><i class="fe fe-eye"></i> SEO Preview</h4>
            </div>
            <div class="col-md-12">
              <div class="card bg-light">
                <div class="card-body">
                  <h5 class="mb-3">Google Search Preview:</h5>
                  <div class="seo-preview-google">
                    <div class="seo-preview-url text-success" style="font-size: 14px;"><?=PATH?></div>
                    <div class="seo-preview-title text-primary" style="font-size: 20px; font-weight: 500;">
                      <span id="preview_title"><?=get_option('website_title', 'SmartPanel - SMM Panel Reseller Tool')?></span>
                    </div>
                    <div class="seo-preview-desc text-muted" style="font-size: 14px;">
                      <span id="preview_desc"><?=substr(get_option('website_desc', 'SmartPanel - #1 SMM Reseller Panel'), 0, 160)?></span>
                    </div>
                  </div>
                  
                  <h5 class="mt-4 mb-3">Facebook Preview:</h5>
                  <div class="seo-preview-facebook border p-2">
                    <div style="background-color: #f0f2f5; height: 200px; display: flex; align-items: center; justify-content: center;">
                      <span class="text-muted">OG Image Preview</span>
                    </div>
                    <div class="p-2">
                      <div class="font-weight-bold" id="preview_og_title"><?=get_option('seo_og_title', get_option('website_title', ''))?></div>
                      <div class="text-muted small" id="preview_og_desc"><?=substr(get_option('seo_og_description', get_option('website_desc', '')), 0, 100)?></div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <div class="col-md-12">
            <div class="">
              <button class="btn btn-primary btn-min-width btn-lg text-uppercase"><?=lang("Save")?></button>
            </div>
=======
            <div class="col-md-12">
              <div class="">
                <button class="btn btn-primary btn-min-width btn-lg text-uppercase"><?=lang("Save")?></button>
              </div>
            </div>
>>>>>>> dd720c81418616f5ea5455fb1a7b66ce0090eb98
          </div>
        </form>
      </div>
    </div>
<<<<<<< HEAD

    <script>
    // Live SEO preview updates
    $(document).ready(function() {
      $('input[name="website_title"], textarea[name="website_desc"]').on('input', function() {
        var title = $('input[name="website_title"]').val() || 'Your Website Title';
        var desc = $('textarea[name="website_desc"]').val() || 'Your website description';
        $('#preview_title').text(title);
        $('#preview_desc').text(desc.substring(0, 160));
      });
      
      $('input[name="seo_og_title"], textarea[name="seo_og_description"]').on('input', function() {
        var ogTitle = $('input[name="seo_og_title"]').val() || $('input[name="website_title"]').val();
        var ogDesc = $('textarea[name="seo_og_description"]').val() || $('textarea[name="website_desc"]').val();
        $('#preview_og_title').text(ogTitle);
        $('#preview_og_desc').text(ogDesc.substring(0, 100));
      });
    });
    </script>
=======
>>>>>>> dd720c81418616f5ea5455fb1a7b66ce0090eb98
