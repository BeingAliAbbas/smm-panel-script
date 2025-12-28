<div id="main-modal-content">
    <div class="modal-right">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <form class="form actionForm" action="<?=cn($module."/ajax_send_email")?>" data-redirect="<?=cn($module)?>" method="POST">
                    <div class="modal-header bg-pantone">
                        <h4 class="modal-title"><i class="fe fe-edit"></i> <?=lang('send_mail')?></h4>
<<<<<<< HEAD
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
=======
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"></button>
>>>>>>> dd720c81418616f5ea5455fb1a7b66ce0090eb98
                    </div>
                    <div class="modal-body">
                        <div class="form-body">
                            <div class="row justify-content-md-center">

                                <div class="col-md-12 col-sm-12 col-xs-12">
                                    <div class="form-group">
                                        <label><?=lang('To')?></label>
                                        <input type="text" class="form-control square" name="email_to" value="<?=(!empty($user->email) && $user->email != "") ? $user->email : ''?>">
                                    </div>
                                </div>

                                <div class="col-md-6 col-sm-12 col-xs-12">
                                    <div class="form-group">
                                        <label><?=lang("first_name")?></label>
                                        <input type="text" class="form-control square" name="first_name" value="<?=(!empty($user->first_name) && $user->first_name != "") ? $user->first_name : ''?>">
                                    </div>
                                </div>

                                <div class="col-md-6 col-sm-12 col-xs-12">
                                    <div class="form-group">
                                        <label><?=lang("last_name")?></label>
                                        <input type="text" class="form-control square" name="last_name" value="<?=(!empty($user->last_name) && $user->last_name != "") ? $user->last_name : ''?>" disabled>
                                    </div>
                                </div>

                                <div class="col-md-12 col-sm-12 col-xs-12">
                                    <div class="form-group">
                                        <label><?=lang('Subject')?></label>
                                        <input type="text" class="form-control square" name="subject">
                                    </div>
                                </div>

                                <div class="col-md-12 col-sm-12 col-xs-12">
                                    <div class="form-group">
                                        <label><?=lang('Message')?></label>
                                        <textarea rows="3" class="form-control plugin_editor square" name="email_content"></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-round btn-primary"><?=lang('Submit')?></button>
<<<<<<< HEAD
                        <button type="button" class="btn btn-round btn-default" data-bs-dismiss="modal"><?=lang('Cancel')?></button>
=======
                        <button type="button" class="btn btn-round btn-default" data-dismiss="modal"><?=lang('Cancel')?></button>
>>>>>>> dd720c81418616f5ea5455fb1a7b66ce0090eb98
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
<<<<<<< HEAD
  // Initialize Summernote for mail editor
  $(document).ready(function() {
    plugin_editor('.plugin_editor', {height: 300});
=======
  tinymce.init({
    selector: '.plugin_editor', // Target the textarea by its ID
    height: 300,
    menubar: true,
    plugins: [
      'advlist autolink lists link image charmap print preview anchor',
      'searchreplace visualblocks code fullscreen',
      'insertdatetime media table paste code help wordcount'
    ],
    toolbar: 'undo redo | formatselect | bold italic backcolor | \
              alignleft aligncenter alignright alignjustify | \
              bullist numlist outdent indent | removeformat | help',
    content_style: 'body { font-family:Helvetica,Arial,sans-serif; font-size:14px }'
>>>>>>> dd720c81418616f5ea5455fb1a7b66ce0090eb98
  });
</script>

