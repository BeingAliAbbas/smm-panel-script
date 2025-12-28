<?php if (get_role("admin")): ?>
<style>
  /* Modern API Provider Balance Card */
  #api-provider-balance-card.summary-card {
    margin-top: 20px;
    border: none;
    border-radius: 1rem;
    padding: 1.5rem;
    background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);
    box-shadow: 0 8px 24px rgba(26, 46, 85, 0.1);
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    position: relative;
    overflow: hidden;
  }
  
  #api-provider-balance-card.summary-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: linear-gradient(90deg, #467fcf 0%, #5d92e0 100%);
  }
  
  #api-provider-balance-card.summary-card:hover {
    box-shadow: 0 12px 32px rgba(26, 46, 85, 0.15);
    transform: translateY(-2px);
  }

  /* Header */
  #api-provider-balance-card h4 {
    margin: 0 0 1rem 0;
    font-size: 1.125rem;
    font-weight: 700;
    color: #343a40;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 10px;
  }
  
  #api-provider-balance-card #refresh-balance-btn {
    margin: 0;
    white-space: nowrap;
    background: linear-gradient(135deg, #467fcf 0%, #5d92e0 100%);
    border: none;
    border-radius: 0.5rem;
    padding: 0.5rem 1rem;
    font-weight: 600;
    box-shadow: 0 2px 8px rgba(70, 127, 207, 0.3);
    transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
  }
  
  #api-provider-balance-card #refresh-balance-btn:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(70, 127, 207, 0.4);
  }
  
  #api-provider-balance-card #refresh-balance-btn:active {
    transform: translateY(0);
  }

  /* Form */
  #api-provider-balance-card .form-group {
    margin-top: 12px;
    margin-bottom: 0;
  }
  
  #api-provider-balance-card label {
    font-weight: 600;
    font-size: 0.9375rem;
    color: #495057;
    display: flex;
    align-items: center;
    gap: 8px;
    margin-bottom: 0.5rem;
  }
  
  #api-provider-balance-card #api-provider-select {
    max-width: 100%;
    width: 100%;
    min-height: 42px;
    font-size: 0.9375rem;
    border: 1px solid #dce3e8;
    border-radius: 0.5rem;
    padding: 0.625rem 1rem;
    transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
  }
  
  #api-provider-balance-card #api-provider-select:focus {
    border-color: #467fcf;
    box-shadow: 0 0 0 0.25rem rgba(70, 127, 207, 0.15);
    outline: none;
  }
  
  #api-provider-balance-card #loading-live-indicator {
    font-weight: 500;
    color: #467fcf;
    animation: pulse 1.5s ease-in-out infinite;
  }
  
  @keyframes pulse {
    0%, 100% { opacity: 1; }
    50% { opacity: 0.6; }
  }

  /* Balance Display */
  #api-provider-balance-card #balance-display {
    margin-top: 1.25rem;
    padding: 1.25rem;
    background: linear-gradient(135deg, rgba(70, 127, 207, 0.05) 0%, rgba(93, 146, 224, 0.05) 100%);
    border-radius: 0.75rem;
    border: 1px solid rgba(70, 127, 207, 0.1);
  }
  
  #api-provider-balance-card #balance-value {
    color: #16a085; /* Fallback color */
    font-size: 2.125rem;
    font-weight: 800;
    margin: 0;
    line-height: 1.2;
    background: linear-gradient(135deg, #16a085 0%, #1abc9c 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
  }
  
  #api-provider-balance-card #session-indicator {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    margin-top: 0.5rem;
    padding: 0.375rem 0.75rem;
    background: rgba(39, 174, 96, 0.1);
    border-radius: 999px;
    color: #27ae60;
    font-size: 0.875rem;
    font-weight: 600;
  }

  /* Mobile Responsiveness */
  @media (max-width: 576px) {
    #api-provider-balance-card.summary-card {
      padding: 1.25rem;
      border-radius: 0.75rem;
    }
    
    #api-provider-balance-card h4 {
      font-size: 1rem;
      flex-direction: column;
      align-items: flex-start;
      gap: 0.75rem;
    }
    
    #api-provider-balance-card #refresh-balance-btn {
      width: 100%;
      text-align: center;
    }
    
    #api-provider-balance-card label {
      font-size: 0.875rem;
    }
    
    #api-provider-balance-card #balance-value {
      font-size: 1.75rem;
    }
    
    #api-provider-balance-card #session-indicator {
      font-size: 0.8125rem;
    }
  }
</style>

<!-- Modern API Provider Balance Card -->
<div id="api-provider-balance-card" class="card summary-card animate-fade-in">
  <h4>
    <span>
      <i class="fe fe-database me-2"></i>
      <?=lang("API Provider Balance")?>
    </span>
    <button id="refresh-balance-btn" class="btn btn-sm btn-primary" title="Refresh Balance" aria-label="Refresh Balance">
      <i class="fe fe-refresh-cw me-1"></i><?=lang("Refresh")?>
    </button>
  </h4>

  <div class="form-group">
    <label for="api-provider-select">
      <?=lang("Select Provider")?> 
      <small id="loading-live-indicator" style="display:none;">
        <i class="fe fe-loader"></i> <?=lang("Fetching live balances...")?>
      </small>
    </label>
    <select id="api-provider-select" class="form-select">
      <option value=""><?=lang("Loading providers...")?></option>
    </select>
  </div>

  <div id="balance-display">
    <p id="balance-value">
      <?=lang("Select a provider")?>
    </p>
    <small id="session-indicator" style="display:none;">
      <i class="fe fe-check-circle"></i>
      <span><?=lang("Live Balance")?></span>
    </small>
  </div>
</div>

<script>
$(document).ready(function() {
  const SESSION_KEY = 'selected_api_provider';
  const MSG_SELECT_PROVIDER = '<?=lang("Select a provider")?>';
  let providers = [];
  
  function loadProviders() {
    $('#loading-live-indicator').show();
    $.ajax({
      url: '<?=cn("order/get_api_providers_balance")?>',
      type: 'GET',
      dataType: 'json',
      success: function(response) {
        $('#loading-live-indicator').hide();
        if (response.status === 'success' && response.data) {
          providers = response.data;
          populateProviderDropdown();
          const savedProviderId = sessionStorage.getItem(SESSION_KEY);
          if (savedProviderId) {
            $('#api-provider-select').val(savedProviderId);
            displayBalance(savedProviderId);
          }
        } else {
          $('#api-provider-select').html('<option value=""><?=lang("No providers available")?></option>');
        }
      },
      error: function() {
        $('#loading-live-indicator').hide();
        $('#api-provider-select').html('<option value=""><?=lang("Error loading providers")?></option>');
      }
    });
  }
  
  function populateProviderDropdown() {
    let options = '<option value="">-- <?=lang("Select Provider")?> --</option>';
    providers.forEach(function(provider) {
      const escapedIds = $('<div>').text(provider.ids).html();
      const escapedName = $('<div>').text(provider.name).html();
      options += '<option value="' + escapedIds + '">' + escapedName + '</option>';
    });
    $('#api-provider-select').html(options);
  }
  
  function displayBalance(providerIds) {
    const provider = providers.find(p => p.ids === providerIds);
    if (provider) {
      const balance = parseFloat(provider.balance);
      const balanceFormatted = isNaN(balance) ? '0.00' : balance.toFixed(2);
      const currency = provider.currency_code || '$';
      $('#balance-value').text(currency + ' ' + balanceFormatted).css('color', '#16a085');
      $('#session-indicator').show();
    } else {
      $('#balance-value').text(MSG_SELECT_PROVIDER).css('color', '#7f8c8d');
      $('#session-indicator').hide();
    }
  }
  
  $('#api-provider-select').on('change', function() {
    const selectedProviderId = $(this).val();
    if (selectedProviderId) {
      sessionStorage.setItem(SESSION_KEY, selectedProviderId);
      displayBalance(selectedProviderId);
    } else {
      sessionStorage.removeItem(SESSION_KEY);
      $('#balance-value').text(MSG_SELECT_PROVIDER).css('color', '#7f8c8d');
      $('#session-indicator').hide();
    }
  });
  
  $('#refresh-balance-btn').on('click', function() {
    const selectedProviderId = $('#api-provider-select').val();
    if (!selectedProviderId) {
      if (typeof notify === 'function') notify('error', '<?=lang("Please select a provider first")?>');
      else alert('<?=lang("Please select a provider first")?>');
      return;
    }
    const $btn = $(this);
    const originalHtml = $btn.html();
    $btn.html('<i class="fe fe-loader"></i> <?=lang("Refreshing...")?>')
        .prop('disabled', true)
        .attr('aria-busy', 'true');
    
    $.ajax({
      url: '<?=cn("order/refresh_provider_balance")?>',
      type: 'POST',
      data: {
        provider_ids: selectedProviderId,
        '<?=$this->security->get_csrf_token_name()?>': '<?=$this->security->get_csrf_hash()?>'
      },
      dataType: 'json',
      success: function(response) {
        if (response.status === 'success') {
          const provider = providers.find(p => p.ids === selectedProviderId);
          if (provider) {
            provider.balance = response.balance;
            provider.currency_code = response.currency_code;
            displayBalance(selectedProviderId);
          }
          if (typeof notify === 'function') notify('success', '<?=lang("Balance refreshed successfully")?>');
        } else {
          if (typeof notify === 'function') notify('error', response.message || '<?=lang("Failed to refresh balance")?>');
          else alert(response.message || '<?=lang("Failed to refresh balance")?>');
        }
      },
      error: function() {
        if (typeof notify === 'function') notify('error', '<?=lang("Error refreshing balance")?>');
        else alert('<?=lang("Error refreshing balance")?>');
      },
      complete: function() {
        $btn.html(originalHtml)
            .prop('disabled', false)
            .attr('aria-busy', 'false');
      }
    });
  });
  
  loadProviders();
});
</script>
<?php endif; ?>