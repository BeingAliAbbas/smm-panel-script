<div class="row justify-content-md-center">
  <div class="col-md-12">
    <div class="page-header">
      <h1 class="page-title">
        <a href="<?php echo cn($module . '/campaign_create'); ?>" class="ajaxModal">
          <span class="add-new" data-toggle="tooltip" data-placement="bottom" title="Add New Campaign">
            <i class="fa fa-plus-square text-primary" aria-hidden="true"></i>
          </span>
        </a>
        WhatsApp Campaigns
      </h1>
      <div class="page-subtitle">
        <a href="<?php echo cn($module); ?>" class="btn btn-sm btn-secondary">
          <i class="fa fa-arrow-left"></i> Back to Dashboard
        </a>
      </div>
    </div>
  </div>
</div>

<div class="row" id="result_ajaxSearch">
  <?php if(!empty($campaigns)){ ?>
  <div class="col-md-12">
    <div class="card">
      <div class="card-header">
        <h3 class="card-title">Campaign List</h3>
        <div class="card-options">
          <a href="#" class="card-options-collapse" data-toggle="card-collapse"><i class="fa fa-chevron-up"></i></a>
        </div>
      </div>
      <div class="table-responsive">
        <table class="table table-hover table-vcenter card-table">
          <thead>
            <tr>
              <th class="w-1">No.</th>
              <th>Campaign Name</th>
              <th>API Config</th>
              <th>Status</th>
              <th>Progress</th>
              <th>Statistics</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php 
            $i = ($page - 1) * $per_page;
            foreach ($campaigns as $campaign) {
              $i++;
              
              // Calculate progress
              $progress = 0;
              if($campaign->total_messages > 0){
                $progress = round(($campaign->sent_messages / $campaign->total_messages) * 100);
              }
              
              // Status badge
              $status_class = 'secondary';
              switch($campaign->status){
                case 'running':
                  $status_class = 'success';
                  break;
                case 'completed':
                  $status_class = 'info';
                  break;
                case 'paused':
                  $status_class = 'warning';
                  break;
                case 'cancelled':
                  $status_class = 'danger';
                  break;
              }
            ?>
            <tr class="tr_<?php echo $campaign->id; ?>">
              <td class="w-1"><?php echo $i; ?></td>
              <td>
                <strong><?php echo htmlspecialchars($campaign->name); ?></strong>
                <br><small class="text-muted">Created: <?php echo date('M d, Y', strtotime($campaign->created_at)); ?></small>
              </td>
              <td><?php echo htmlspecialchars($campaign->api_name); ?></td>
              <td>
                <span class="badge badge-<?php echo $status_class; ?>">
                  <?php echo ucfirst($campaign->status); ?>
                </span>
              </td>
              <td>
                <div class="clearfix">
                  <div class="float-left">
                    <strong><?php echo $progress; ?>%</strong>
                  </div>
                  <div class="float-right">
                    <small class="text-muted"><?php echo $campaign->sent_messages; ?> / <?php echo $campaign->total_messages; ?></small>
                  </div>
                </div>
                <div class="progress progress-sm">
                  <div class="progress-bar bg-success" role="progressbar" style="width: <?php echo $progress; ?>%" 
                    aria-valuenow="<?php echo $progress; ?>" aria-valuemin="0" aria-valuemax="100"></div>
                </div>
              </td>
              <td>
                <div class="small">
                  <span class="badge badge-success" title="Sent"><?php echo $campaign->sent_messages; ?></span>
                  <span class="badge badge-danger" title="Failed"><?php echo $campaign->failed_messages; ?></span>
                  <span class="badge badge-info" title="Delivered"><?php echo $campaign->delivered_messages; ?></span>
                </div>
              </td>
              <td>
                <div class="item-action dropdown">
                  <a href="javascript:void(0)" data-toggle="dropdown" class="icon">
                    <i class="fa fa-ellipsis-v"></i>
                  </a>
                  <div class="dropdown-menu dropdown-menu-right">
                    <?php if($campaign->status == 'pending' || $campaign->status == 'paused'){ ?>
                    <a href="javascript:void(0)" class="dropdown-item actionCampaignStatus" data-ids="<?php echo $campaign->ids; ?>" data-action="start">
                      <i class="dropdown-icon fa fa-play text-success"></i> Start Sending
                    </a>
                    <?php } ?>
                    
                    <?php if($campaign->status == 'running'){ ?>
                    <a href="javascript:void(0)" class="dropdown-item actionCampaignStatus" data-ids="<?php echo $campaign->ids; ?>" data-action="pause">
                      <i class="dropdown-icon fa fa-pause text-warning"></i> Pause
                    </a>
                    <?php } ?>
                    
                    <?php if($campaign->status == 'paused'){ ?>
                    <a href="javascript:void(0)" class="dropdown-item actionCampaignStatus" data-ids="<?php echo $campaign->ids; ?>" data-action="resume">
                      <i class="dropdown-icon fa fa-play text-success"></i> Resume
                    </a>
                    <?php } ?>
                    
                    <a href="<?php echo cn($module . '/recipients/' . $campaign->ids); ?>" class="dropdown-item">
                      <i class="dropdown-icon fa fa-users"></i> Recipients
                    </a>
                    
                    <a href="<?php echo cn($module . '/logs/' . $campaign->ids); ?>" class="dropdown-item">
                      <i class="dropdown-icon fa fa-file-text"></i> View Logs
                    </a>
                    
                    <div class="dropdown-divider"></div>
                    
                    <a href="<?php echo cn($module . '/campaign_edit/' . $campaign->ids); ?>" class="dropdown-item ajaxModal">
                      <i class="dropdown-icon fa fa-edit"></i> Edit
                    </a>
                    
                    <a href="javascript:void(0)" class="dropdown-item actionCampaignDelete" data-ids="<?php echo $campaign->ids; ?>">
                      <i class="dropdown-icon fa fa-trash text-danger"></i> Delete
                    </a>
                  </div>
                </div>
              </td>
            </tr>
            <?php } ?>
          </tbody>
        </table>
      </div>
      
      <?php if($total > $per_page){ ?>
      <div class="card-footer">
        <?php echo pagination($module . '/campaigns', $total, $per_page, $page); ?>
      </div>
      <?php } ?>
    </div>
  </div>
  <?php }else{ ?>
  <div class="col-md-12">
    <div class="card">
      <div class="card-body text-center">
        <div class="empty">
          <div class="empty-icon"><i class="fa fa-paper-plane"></i></div>
          <p class="empty-title">No campaigns found</p>
          <p class="empty-subtitle text-muted">
            Get started by creating your first WhatsApp campaign.
          </p>
          <div class="empty-action">
            <a href="<?php echo cn($module . '/campaign_create'); ?>" class="btn btn-primary ajaxModal">
              <i class="fa fa-plus"></i> Create Campaign
            </a>
          </div>
        </div>
      </div>
    </div>
  </div>
  <?php } ?>
</div>

<script>
$(document).ready(function(){
  
  // Campaign status actions
  $(document).on('click', '.actionCampaignStatus', function(e){
    e.preventDefault();
    var ids = $(this).data('ids');
    var action = $(this).data('action');
    
    var actionText = action.charAt(0).toUpperCase() + action.slice(1);
    
    swal({
      title: actionText + ' Campaign?',
      text: "Are you sure you want to " + action + " this campaign?",
      type: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#3085d6',
      cancelButtonColor: '#d33',
      confirmButtonText: 'Yes, ' + action + ' it!'
    }).then((result) => {
      if (result.value) {
        $.ajax({
          url: '<?php echo cn($module . '/ajax_campaign_status'); ?>/' + ids,
          type: 'POST',
          dataType: 'json',
          data: {
            action: action
          },
          success: function(data){
            if(data.status == 'success'){
              swal('Success!', data.message, 'success');
              setTimeout(function(){
                location.reload();
              }, 1000);
            } else {
              swal('Error!', data.message, 'error');
            }
          }
        });
      }
    });
  });
  
  // Delete campaign
  $(document).on('click', '.actionCampaignDelete', function(e){
    e.preventDefault();
    var ids = $(this).data('ids');
    
    swal({
      title: 'Delete Campaign?',
      text: "This will delete the campaign and all associated data. This action cannot be undone!",
      type: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#d33',
      cancelButtonColor: '#3085d6',
      confirmButtonText: 'Yes, delete it!'
    }).then((result) => {
      if (result.value) {
        $.ajax({
          url: '<?php echo cn($module . '/ajax_campaign_delete'); ?>/' + ids,
          type: 'POST',
          dataType: 'json',
          success: function(data){
            if(data.status == 'success'){
              swal('Deleted!', data.message, 'success');
              setTimeout(function(){
                location.reload();
              }, 1000);
            } else {
              swal('Error!', data.message, 'error');
            }
          }
        });
      }
    });
  });
  
});
</script>
