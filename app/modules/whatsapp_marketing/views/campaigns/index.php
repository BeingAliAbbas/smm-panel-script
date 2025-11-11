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
          <i class="fe fe-arrow-left"></i> Back to Dashboard
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
          <a href="#" class="card-options-collapse" data-toggle="card-collapse"><i class="fe fe-chevron-up"></i></a>
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
                <small>
                  <i class="fe fe-check-circle text-success"></i> <?php echo $campaign->sent_messages; ?> sent<br>
                  <i class="fe fe-message-square text-info"></i> <?php echo $campaign->delivered_messages; ?> delivered<br>
                  <i class="fe fe-x-circle text-danger"></i> <?php echo $campaign->failed_messages; ?> failed
                </small>
              </td>
              <td>
                <div class="btn-group">
                  <a href="<?php echo cn($module . '/recipients/' . $campaign->ids); ?>" 
                    class="btn btn-sm btn-icon" 
                    data-toggle="tooltip" 
                    title="Recipients">
                    <i class="fe fe-users"></i>
                  </a>
                  
                  <a href="<?php echo cn($module . '/logs/' . $campaign->ids); ?>" 
                    class="btn btn-sm btn-icon" 
                    data-toggle="tooltip" 
                    title="View Logs">
                    <i class="fe fe-file-text"></i>
                  </a>
                  
                  <?php if($campaign->status == 'pending' || $campaign->status == 'paused'){ ?>
                  <a href="<?php echo cn($module . '/campaign_edit/' . $campaign->ids); ?>" 
                    class="btn btn-sm btn-icon ajaxModal" 
                    data-toggle="tooltip" 
                    title="Edit">
                    <i class="fe fe-edit"></i>
                  </a>
                  <?php } ?>
                  
                  <?php if($campaign->status == 'pending'){ ?>
                  <a href="javascript:void(0)" 
                    class="btn btn-sm btn-icon btn-success actionItem" 
                    data-id="<?php echo $campaign->ids; ?>" 
                    data-action="<?php echo cn($module . '/ajax_campaign_status'); ?>" 
                    data-params='{"action": "start"}' 
                    data-toggle="tooltip" 
                    title="Start Campaign">
                    <i class="fe fe-play"></i>
                  </a>
                  <?php } ?>
                  
                  <?php if($campaign->status == 'running'){ ?>
                  <a href="javascript:void(0)" 
                    class="btn btn-sm btn-icon btn-warning actionItem" 
                    data-id="<?php echo $campaign->ids; ?>" 
                    data-action="<?php echo cn($module . '/ajax_campaign_status'); ?>" 
                    data-params='{"action": "pause"}' 
                    data-toggle="tooltip" 
                    title="Pause Campaign">
                    <i class="fe fe-pause"></i>
                  </a>
                  <?php } ?>
                  
                  <?php if($campaign->status == 'paused'){ ?>
                  <a href="javascript:void(0)" 
                    class="btn btn-sm btn-icon btn-success actionItem" 
                    data-id="<?php echo $campaign->ids; ?>" 
                    data-action="<?php echo cn($module . '/ajax_campaign_status'); ?>" 
                    data-params='{"action": "resume"}' 
                    data-toggle="tooltip" 
                    title="Resume Campaign">
                    <i class="fe fe-play"></i>
                  </a>
                  <?php } ?>
                  
                  <?php if($campaign->status != 'running'){ ?>
                  <a href="javascript:void(0)" 
                    class="btn btn-sm btn-icon btn-danger actionItem" 
                    data-id="<?php echo $campaign->ids; ?>" 
                    data-action="<?php echo cn($module . '/ajax_campaign_delete'); ?>" 
                    data-toggle="tooltip" 
                    title="Delete" 
                    data-confirm="Are you sure you want to delete this campaign?">
                    <i class="fe fe-trash"></i>
                  </a>
                  <?php } ?>
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
