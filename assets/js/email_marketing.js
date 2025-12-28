/**
 * Email Marketing Module - Interactive Features
 * Handles campaign controls, cron URL copying, and responsive behaviors
 */

(function($) {
    'use strict';
    
    // Initialize when document is ready
    $(document).ready(function() {
        initCampaignControls();
        initCronUrlFeatures();
        initResponsiveLogsTables();
        initTooltips();
    });
    
    /**
     * Campaign Pause/Run Toggle
     */
    function initCampaignControls() {
        $('.campaign-toggle-status').on('click', function(e) {
            e.preventDefault();
            
            var $btn = $(this);
            var campaignId = $btn.data('campaign-id');
            var currentStatus = $btn.data('current-status');
            var newStatus = (currentStatus === 'running') ? 'paused' : 'running';
            var endpoint = $btn.data('endpoint');
            
            // Disable button and show loading
            $btn.prop('disabled', true);
            var originalHtml = $btn.html();
            $btn.html('<span class="spinner-border spinner-border-sm me-1"></span>Processing...');
            
            // Make AJAX request
            $.ajax({
                url: endpoint,
                type: 'POST',
                dataType: 'json',
                data: {
                    ids: campaignId,
                    csrf_token: $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.status === 'success' || response.success) {
                        // Update button state
                        var status = response.campaign_status || newStatus;
                        updateCampaignToggleButton($btn, status);
                        
                        // Show success toast
                        showToast('Campaign ' + (status === 'running' ? 'resumed' : 'paused') + ' successfully', 'success');
                        
                        // Update status badge if exists
                        updateStatusBadge(status);
                    } else {
                        // Restore button and show error
                        $btn.html(originalHtml);
                        $btn.prop('disabled', false);
                        showToast(response.message || 'Failed to update campaign status', 'error');
                    }
                },
                error: function(xhr, status, error) {
                    // Restore button and show error
                    $btn.html(originalHtml);
                    $btn.prop('disabled', false);
                    showToast('Error: Unable to update campaign status', 'error');
                    console.error('Campaign toggle error:', error);
                }
            });
        });
    }
    
    /**
     * Update campaign toggle button appearance
     */
    function updateCampaignToggleButton($btn, status) {
        $btn.data('current-status', status);
        
        if (status === 'running') {
            $btn.removeClass('btn-success')
                .addClass('btn-warning')
                .html('<i class="fe fe-pause me-1"></i>Pause Campaign');
        } else {
            $btn.removeClass('btn-warning')
                .addClass('btn-success')
                .html('<i class="fe fe-play me-1"></i>Run Campaign');
        }
        
        $btn.prop('disabled', false);
    }
    
    /**
     * Update status badge in campaign info
     */
    function updateStatusBadge(status) {
        var $badge = $('.campaign-status-badge');
        if ($badge.length) {
            var badgeClass = 'badge-secondary';
            var statusText = status.charAt(0).toUpperCase() + status.slice(1);
            
            switch(status) {
                case 'running':
                    badgeClass = 'badge-success';
                    break;
                case 'paused':
                    badgeClass = 'badge-warning';
                    break;
                case 'completed':
                    badgeClass = 'badge-info';
                    break;
                case 'cancelled':
                    badgeClass = 'badge-danger';
                    break;
            }
            
            $badge.attr('class', 'badge campaign-status-badge ' + badgeClass)
                  .text(statusText);
        }
    }
    
    /**
     * Cron URL Copy & Open Features
     */
    function initCronUrlFeatures() {
        // Copy to clipboard
        $('.cron-url-copy').on('click', function(e) {
            e.preventDefault();
            
            var $btn = $(this);
            var $input = $btn.closest('.cron-url-container').find('.cron-url-input');
            var url = $input.val();
            
            copyToClipboard(url, $btn);
        });
        
        // Open in new tab
        $('.cron-url-open').on('click', function(e) {
            e.preventDefault();
            
            var $input = $(this).closest('.cron-url-container').find('.cron-url-input');
            var url = $input.val();
            
            if (url) {
                window.open(url, '_blank', 'noopener,noreferrer');
                showToast('Cron URL opened in new tab', 'info');
            }
        });
    }
    
    /**
     * Copy text to clipboard with fallback
     */
    function copyToClipboard(text, $btn) {
        // Modern approach
        if (navigator.clipboard && navigator.clipboard.writeText) {
            navigator.clipboard.writeText(text).then(function() {
                showCopySuccess($btn);
            }).catch(function(err) {
                // Fallback
                copyToClipboardFallback(text, $btn);
            });
        } else {
            // Fallback for older browsers
            copyToClipboardFallback(text, $btn);
        }
    }
    
    /**
     * Fallback clipboard copy method
     */
    function copyToClipboardFallback(text, $btn) {
        var $temp = $('<textarea>');
        $('body').append($temp);
        $temp.val(text).select();
        
        try {
            document.execCommand('copy');
            showCopySuccess($btn);
        } catch(err) {
            showToast('Failed to copy to clipboard', 'error');
            console.error('Copy fallback error:', err);
        }
        
        $temp.remove();
    }
    
    /**
     * Show copy success feedback
     */
    function showCopySuccess($btn) {
        var originalHtml = $btn.html();
        var originalClass = $btn.attr('class');
        
        $btn.html('<i class="fe fe-check"></i> Copied!')
            .addClass('copy-btn-success btn-success')
            .removeClass('btn-secondary');
        
        setTimeout(function() {
            $btn.html(originalHtml)
                .attr('class', originalClass);
        }, 2000);
        
        showToast('Cron URL copied to clipboard', 'success');
    }
    
    /**
     * Responsive Logs Table - Mobile row expansion
     */
    function initResponsiveLogsTables() {
        $('.log-row-header').on('click', function() {
            var $header = $(this);
            var $details = $header.next('.log-row-details');
            var $icon = $header.find('.expand-icon');
            
            // Toggle details
            $details.slideToggle(200);
            $icon.toggleClass('expanded');
        });
        
        // Initially hide all details on mobile
        if ($(window).width() <= 768) {
            $('.log-row-details').hide();
        }
    }
    
    /**
     * Initialize Bootstrap tooltips
     */
    function initTooltips() {
        if (typeof bootstrap !== 'undefined' && bootstrap.Tooltip) {
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });
        }
    }
    
    /**
     * Show toast notification
     */
    function showToast(message, type) {
        // Check if toast container exists, create if not
        var $container = $('.toast-container');
        if ($container.length === 0) {
            $container = $('<div class="toast-container position-fixed bottom-0 end-0 p-3"></div>');
            $('body').append($container);
        }
        
        // Determine toast style
        var bgClass = 'bg-info';
        var icon = 'fe-info';
        
        switch(type) {
            case 'success':
                bgClass = 'bg-success';
                icon = 'fe-check-circle';
                break;
            case 'error':
            case 'danger':
                bgClass = 'bg-danger';
                icon = 'fe-x-circle';
                break;
            case 'warning':
                bgClass = 'bg-warning';
                icon = 'fe-alert-triangle';
                break;
        }
        
        // Create toast HTML
        var toastId = 'toast-' + Date.now();
        var toastHtml = '<div id="' + toastId + '" class="toast align-items-center text-white ' + bgClass + ' border-0" role="alert" aria-live="assertive" aria-atomic="true">' +
                          '<div class="d-flex">' +
                            '<div class="toast-body">' +
                              '<i class="fe ' + icon + ' me-2"></i>' + message +
                            '</div>' +
                            '<button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>' +
                          '</div>' +
                        '</div>';
        
        $container.append(toastHtml);
        
        // Initialize and show toast
        var toastElement = document.getElementById(toastId);
        if (typeof bootstrap !== 'undefined' && bootstrap.Toast) {
            var toast = new bootstrap.Toast(toastElement, {
                autohide: true,
                delay: 3000
            });
            toast.show();
            
            // Remove from DOM after hidden
            toastElement.addEventListener('hidden.bs.toast', function() {
                $(toastElement).remove();
            });
        } else {
            // Fallback if Bootstrap Toast not available
            var $toast = $('#' + toastId);
            $toast.fadeIn();
            setTimeout(function() {
                $toast.fadeOut(function() {
                    $(this).remove();
                });
            }, 3000);
        }
    }
    
    /**
     * Responsive table helper
     */
    function makeTableResponsive() {
        $('.table-responsive-custom').each(function() {
            var $table = $(this).find('table');
            if ($table.length) {
                // Add horizontal scroll indicator on mobile
                if ($(window).width() <= 768) {
                    if (!$(this).find('.scroll-indicator').length) {
                        $(this).append('<div class="scroll-indicator text-muted text-center py-2"><small><i class="fe fe-chevrons-right"></i> Scroll to see more</small></div>');
                    }
                }
            }
        });
    }
    
    // Handle window resize
    $(window).on('resize', function() {
        makeTableResponsive();
    });
    
    // Initial call
    makeTableResponsive();
    
})(jQuery);
