
<<<<<<< HEAD
<style>
.logo-section {
  background: #fff;
  border-radius: 8px;
  padding: 25px;
  margin-bottom: 25px;
  box-shadow: 0 2px 4px rgba(0,0,0,0.05);
}

.logo-section-title {
  font-size: 18px;
  font-weight: 600;
  color: #2c3e50;
  margin-bottom: 8px;
  display: flex;
  align-items: center;
}

.logo-section-title i {
  margin-right: 8px;
  color: #5b6e84;
}

.logo-section-desc {
  font-size: 13px;
  color: #6c757d;
  margin-bottom: 20px;
  line-height: 1.5;
}

.logo-preview-container {
  display: flex;
  align-items: center;
  gap: 20px;
  margin-bottom: 15px;
  padding: 15px;
  background: #f8f9fa;
  border-radius: 6px;
  border: 1px solid #e9ecef;
}

.logo-preview-box {
  flex-shrink: 0;
  width: 100px;
  height: 100px;
  border: 2px dashed #cbd5e0;
  border-radius: 6px;
  display: flex;
  align-items: center;
  justify-content: center;
  background: #fff;
  overflow: hidden;
}

.logo-preview-box.favicon {
  width: 64px;
  height: 64px;
}

.logo-preview-box img {
  max-width: 100%;
  max-height: 100%;
  object-fit: contain;
}

.logo-preview-placeholder {
  color: #a0aec0;
  font-size: 12px;
  text-align: center;
  padding: 10px;
}

.logo-input-group {
  flex: 1;
}

.help-hint {
  display: block;
  margin-top: 6px;
  font-size: 12px;
  color: #718096;
}

.help-hint i {
  margin-right: 4px;
  color: #4299e1;
}

.format-badge {
  display: inline-block;
  padding: 2px 8px;
  background: #e6f2ff;
  color: #2c5aa0;
  border-radius: 3px;
  font-size: 11px;
  font-weight: 500;
  margin-right: 4px;
}

.divider {
  height: 1px;
  background: #e9ecef;
  margin: 30px 0;
}
</style>

<div class="card p-0 content">
  <div class="card-header">
    <h3 class="card-title" style="color:#fff !important;">
      <i class="fe fe-image"></i> <?=lang("website_logo")?>
    </h3>
  </div>
  <div class="card-body">
    <form class="actionForm" action="<?=cn("$module/ajax_general_settings")?>" method="POST" data-redirect="<?php echo get_current_url(); ?>">
      
      <!-- Favicon Section -->
      <div class="logo-section">
        <div class="logo-section-title">
          <i class="fe fe-globe"></i>
          <?=lang("website_favicon")?>
        </div>
        <div class="logo-section-desc">
          The favicon appears in browser tabs, bookmarks, and address bars. It helps users identify your site quickly.
        </div>
        
        <div class="logo-preview-container">
          <div class="logo-preview-box favicon">
            <img src="<?=get_option('website_favicon', BASE."assets/images/favicon.png")?>" 
                 alt="Favicon Preview" 
                 id="favicon-preview"
                 onerror="this.style.display='none'; this.nextElementSibling.style.display='block';">
            <div class="logo-preview-placeholder" style="display:none;">No favicon</div>
          </div>
          <div class="logo-input-group">
            <label class="form-label mb-2">Favicon URL</label>
            <div class="input-group">
              <input type="text" 
                     name="website_favicon" 
                     class="form-control favicon-url-input" 
                     value="<?=get_option('website_favicon', BASE."assets/images/favicon.png")?>"
                     placeholder="Enter favicon URL">
              <span class="">
                <button class="btn btn-info" type="button" title="Upload favicon">
                  <i class="fe fe-upload">
                    <input class="settings_fileupload" type="file" name="files[]" accept=".png,.jpg,.jpeg,.ico,.svg">
                  </i>
                </button>
              </span>
            </div>
            <small class="help-hint">
              <i class="fe fe-info"></i>
              <strong>Recommended:</strong> 32x32px or 64x64px | 
              <span class="format-badge">PNG</span>
              <span class="format-badge">ICO</span>
              <span class="format-badge">SVG</span>
            </small>
          </div>
        </div>
      </div>

      <div class="divider"></div>

      <!-- Logo Section -->
      <div class="logo-section">
        <div class="logo-section-title">
          <i class="fe fe-image"></i>
          Main Logo (Dark Backgrounds)
        </div>
        <div class="logo-section-desc">
          The primary logo for your brand, typically used on light or colored backgrounds. This logo appears on the login page, admin panel, and user dashboard.
        </div>
        
        <div class="logo-preview-container">
          <div class="logo-preview-box">
            <img src="<?=get_option('website_logo', BASE."assets/images/logo.png")?>" 
                 alt="Logo Preview" 
                 id="logo-preview"
                 onerror="this.style.display='none'; this.nextElementSibling.style.display='block';">
            <div class="logo-preview-placeholder" style="display:none;">No logo</div>
          </div>
          <div class="logo-input-group">
            <label class="form-label mb-2"><?=lang("website_logo")?></label>
            <div class="input-group">
              <input type="text" 
                     name="website_logo" 
                     class="form-control logo-url-input" 
                     value="<?=get_option('website_logo', BASE."assets/images/logo.png")?>"
                     placeholder="Enter logo URL">
              <span class="">
                <button class="btn btn-info" type="button" title="Upload logo">
                  <i class="fe fe-upload">
                    <input class="settings_fileupload" type="file" name="files[]" accept=".png,.jpg,.jpeg,.svg">
                  </i>
                </button>
              </span>
            </div>
            <small class="help-hint">
              <i class="fe fe-info"></i>
              <strong>Recommended:</strong> 200x50px to 400x100px | 
              <span class="format-badge">PNG</span>
              <span class="format-badge">JPG</span>
              <span class="format-badge">SVG</span>
            </small>
          </div>
        </div>
      </div>

      <div class="divider"></div>

      <!-- White Logo Section -->
      <div class="logo-section">
        <div class="logo-section-title">
          <i class="fe fe-image"></i>
          White Logo (Light Backgrounds)
        </div>
        <div class="logo-section-desc">
          A white or light-colored version of your logo for use on dark backgrounds. This ensures your branding is visible across different themes and sections.
        </div>
        
        <div class="logo-preview-container" style="background: #2c3e50;">
          <div class="logo-preview-box" style="background: #1a252f; border-color: #4a5568;">
            <img src="<?=get_option('website_logo_white', BASE."assets/images/logo-white.png")?>" 
                 alt="White Logo Preview" 
                 id="logo-white-preview"
                 onerror="this.style.display='none'; this.nextElementSibling.style.display='block';">
            <div class="logo-preview-placeholder" style="display:none; color: #fff;">No white logo</div>
          </div>
          <div class="logo-input-group">
            <label class="form-label mb-2" style="color: #fff;"><?=lang("website_logo_white")?></label>
            <div class="input-group">
              <input type="text" 
                     name="website_logo_white" 
                     class="form-control logo-white-url-input" 
                     value="<?=get_option('website_logo_white', BASE."assets/images/logo-white.png")?>"
                     placeholder="Enter white logo URL">
              <span class="">
                <button class="btn btn-light" type="button" title="Upload white logo">
                  <i class="fe fe-upload">
                    <input class="settings_fileupload" type="file" name="files[]" accept=".png,.jpg,.jpeg,.svg">
                  </i>
                </button>
              </span>
            </div>
            <small class="help-hint" style="color: #cbd5e0;">
              <i class="fe fe-info"></i>
              <strong>Recommended:</strong> Same size as main logo | 
              <span class="format-badge">PNG</span>
              <span class="format-badge">SVG</span>
            </small>
          </div>
        </div>
      </div>

      <div class="row mt-4">
        <div class="col-md-12">
          <div class="alert alert-info" style="background: #e6f7ff; border-color: #91d5ff; color: #0050b3;">
            <i class="fe fe-alert-circle"></i>
            <strong>Note:</strong> After saving, your changes will be applied across all pages including login, dashboard, and email templates. Clear your browser cache if you don't see the changes immediately.
          </div>
        </div>
      </div>

      <div class="row">
        <div class="col-md-12">
          <div class="form-footer">
            <button class="btn btn-primary btn-min-width btn-lg text-uppercase" type="submit">
              <i class="fe fe-save"></i> <?=lang("Save")?>
            </button>
=======
<div class="card content">
  <div class="card-header" style="border: 0.1px solid #05d0a0; border-radius: 3.5px 3.5px 0px 0px; background: #05d0a0;">
    <h3 class="card-title"><i class="fe fe-life-buoy"></i> <?=lang("website_logo")?></h3>
  </div>
  <div class="card-body">
    <form class="actionForm" action="<?=cn("$module/ajax_general_settings")?>" method="POST" data-redirect="<?php echo get_current_url(); ?>">
      <div class="row">
        <div class="col-md-12 col-lg-12">

          <div class="form-group">
            <label class="form-label"><?=lang("website_favicon")?></label>
            <div class="input-group">
              <input type="text" name="website_favicon" class="form-control" value="<?=get_option('website_favicon', BASE."assets/images/favicon.png")?>">
              <span class="input-group-append">
                <button class="btn btn-info" type="button">
                  <i class="fe fe-image">
                    <input class="settings_fileupload" type="file" name="files[]" multiple="">
                  </i>
                </button>
              </span>
            </div>
          </div>  
          
          <div class="form-group">
            <label class="form-label"><?=lang("website_logo")?></label>
            <div class="input-group">
              <input type="text" name="website_logo" class="form-control" value="<?=get_option('website_logo', BASE."assets/images/logo.png")?>">
              <span class="input-group-append">
                <button class="btn btn-info" type="button">
                  <i class="fe fe-image">
                    <input class="settings_fileupload" type="file" name="files[]" multiple="">
                  </i>
                </button>
              </span>
            </div>
          </div> 

          <div class="form-group">
            <label class="form-label"><?=lang("website_logo_white")?></label>
            <div class="input-group">
              <input type="text" name="website_logo_white" class="form-control" value="<?=get_option('website_logo_white', BASE."assets/images/logo-white.png")?>">
              <span class="input-group-append">
                <button class="btn btn-info" type="button">
                  <i class="fe fe-image">
                    <input class="settings_fileupload" type="file" name="files[]" multiple="">
                  </i>
                </button>
              </span>
            </div>
          </div> 

        </div>
        <div class="col-md-8">
          <div class="form-footer">
            <button class="btn btn-primary btn-min-width btn-lg text-uppercase"><?=lang("Save")?></button>
>>>>>>> dd720c81418616f5ea5455fb1a7b66ce0090eb98
          </div>
        </div>
      </div>
    </form>
  </div>
</div>
<<<<<<< HEAD

<script>
// Real-time preview updates when URL changes
document.addEventListener('DOMContentLoaded', function() {
  // Favicon preview update
  const faviconInput = document.querySelector('.favicon-url-input');
  const faviconPreview = document.getElementById('favicon-preview');
  
  if (faviconInput && faviconPreview) {
    faviconInput.addEventListener('input', function() {
      const url = this.value.trim();
      if (url) {
        faviconPreview.src = url;
        faviconPreview.style.display = 'block';
        faviconPreview.nextElementSibling.style.display = 'none';
      }
    });
  }
  
  // Logo preview update
  const logoInput = document.querySelector('.logo-url-input');
  const logoPreview = document.getElementById('logo-preview');
  
  if (logoInput && logoPreview) {
    logoInput.addEventListener('input', function() {
      const url = this.value.trim();
      if (url) {
        logoPreview.src = url;
        logoPreview.style.display = 'block';
        logoPreview.nextElementSibling.style.display = 'none';
      }
    });
  }
  
  // White logo preview update
  const logoWhiteInput = document.querySelector('.logo-white-url-input');
  const logoWhitePreview = document.getElementById('logo-white-preview');
  
  if (logoWhiteInput && logoWhitePreview) {
    logoWhiteInput.addEventListener('input', function() {
      const url = this.value.trim();
      if (url) {
        logoWhitePreview.src = url;
        logoWhitePreview.style.display = 'block';
        logoWhitePreview.nextElementSibling.style.display = 'none';
      }
    });
  }
});
</script>
=======
>>>>>>> dd720c81418616f5ea5455fb1a7b66ce0090eb98
