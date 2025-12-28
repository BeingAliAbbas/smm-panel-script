
<div id="main-modal-content">
  <div class="modal-right">
    <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content">
        <div class="modal-header bg-pantone">
          <h4 class="modal-title"><i class="fe fe-book-open"></i> <?=$service->name?></h4>
<<<<<<< HEAD
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
=======
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
>>>>>>> dd720c81418616f5ea5455fb1a7b66ce0090eb98
          </button>
        </div>
        <div class="modal-body">
          <div class="form-body">
            <div class="row justify-content-md-center">
              <div class="col-md-12 col-sm-12 col-xs-12">
                <div class="form-group">
                  <div class="content">
                    <?php
                      if (!empty($service->desc)) {
                        $desc = html_entity_decode($service->desc, ENT_QUOTES);
                        $desc = str_replace("\n", "<br>", $desc);
                        echo $desc;
                      }else{
                        echo Modules::run("blocks/empty_data");
                      }
                    ?>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer">
<<<<<<< HEAD
          <button type="button" class="btn round btn-default btn-min-width me-1 mb-1" data-bs-dismiss="modal"><?=lang("Cancel")?></button>
=======
          <button type="button" class="btn round btn-default btn-min-width mr-1 mb-1" data-dismiss="modal"><?=lang("Cancel")?></button>
>>>>>>> dd720c81418616f5ea5455fb1a7b66ce0090eb98
        </div>
      </div>
    </div>
  </div>
</div>
