<!-- Credit Notification Modal -->
<div class="modal fade" id="creditNotificationModal" tabindex="-1" role="dialog" aria-labelledby="creditNotificationModalLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title" id="creditNotificationModalLabel">
                    <i class="fa fa-check-circle mr-2"></i>
                    <span id="notificationTitle">Balance Credited Successfully!</span>
                </h5>
            </div>
            <div class="modal-body text-center py-4">
                <div class="mb-3">
                    <i class="fa fa-coins text-success" style="font-size: 3rem;"></i>
                </div>
                <div id="notificationMessage" class="mb-3" style="font-size: 1.1rem; line-height: 1.6;">
                    <!-- Message will be inserted here -->
                </div>
                <div id="notificationAmount" class="text-success font-weight-bold" style="font-size: 1.5rem;">
                    <!-- Amount will be inserted here -->
                </div>
            </div>
            <div class="modal-footer justify-content-center">
                <button type="button" class="btn btn-success px-5" id="acknowledgeNotificationBtn">
                    <i class="fa fa-check mr-2"></i>OK, Got It!
                </button>
            </div>
        </div>
    </div>
</div>

<style>
/* Credit Notification Modal Styles */
#creditNotificationModal .modal-content {
    border: none;
    border-radius: 10px;
    box-shadow: 0 5px 20px rgba(0,0,0,0.3);
}

#creditNotificationModal .modal-header {
    border-top-left-radius: 10px;
    border-top-right-radius: 10px;
    border-bottom: none;
}

#creditNotificationModal .modal-body {
    padding: 2rem;
}

#creditNotificationModal .btn-success {
    border-radius: 25px;
    font-weight: 600;
    letter-spacing: 0.5px;
}

#creditNotificationModal .btn-success:hover {
    transform: scale(1.05);
    transition: transform 0.2s ease;
}

#creditNotificationModal .modal-footer {
    border-top: none;
    padding-bottom: 2rem;
}
</style>

<script>
(function() {
    // Only run on logged-in pages
    if (typeof BASE === 'undefined' || typeof token === 'undefined') {
        return;
    }

    var currentNotificationId = null;
    var isCheckingNotifications = false;

    function checkForNotifications() {
        if (isCheckingNotifications) {
            return;
        }

        isCheckingNotifications = true;

        $.ajax({
            url: BASE + 'notifications/ajax_get_unread',
            type: 'POST',
            dataType: 'json',
            data: {
                token: token
            },
            success: function(response) {
                if (response.status === 'success' && response.has_notifications) {
                    showNotificationModal(response.notification);
                }
            },
            error: function(xhr, status, error) {
                console.log('Failed to check notifications:', error);
            },
            complete: function() {
                isCheckingNotifications = false;
            }
        });
    }

    function showNotificationModal(notification) {
        currentNotificationId = notification.id;

        // Update modal content (using .text() for XSS protection)
        $('#notificationTitle').text(notification.title);
        $('#notificationMessage').text(notification.message);
        $('#notificationAmount').text('Amount: ' + notification.amount);

        // Show the modal
        $('#creditNotificationModal').modal('show');
    }

    function markNotificationAsSeen(notificationId, callback) {
        $.ajax({
            url: BASE + 'notifications/ajax_mark_seen',
            type: 'POST',
            dataType: 'json',
            data: {
                token: token,
                notification_id: notificationId
            },
            success: function(response) {
                if (response.status === 'success') {
                    if (callback) callback(response.has_more);
                }
            },
            error: function(xhr, status, error) {
                console.log('Failed to mark notification as seen:', error);
                if (callback) callback(false);
            }
        });
    }

    // Handle notification acknowledgment
    $('#acknowledgeNotificationBtn').on('click', function() {
        if (currentNotificationId) {
            var btn = $(this);
            btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin mr-2"></i>Processing...');

            markNotificationAsSeen(currentNotificationId, function(hasMore) {
                // Hide current modal
                $('#creditNotificationModal').modal('hide');

                // Reset button after modal is hidden
                $('#creditNotificationModal').on('hidden.bs.modal', function() {
                    btn.prop('disabled', false).html('<i class="fa fa-check mr-2"></i>OK, Got It!');
                    currentNotificationId = null;

                    // Check if there are more notifications to show
                    if (hasMore) {
                        setTimeout(checkForNotifications, 500);
                    }
                });
            });
        }
    });

    // Check for notifications when page loads
    $(document).ready(function() {
        // Wait a bit for page to fully load
        setTimeout(checkForNotifications, 1000);
    });
})();
</script>
