    <?php if($display_html){?>

    <?php } ?>
    
    <script src="<?=BASE?>assets/js/vendors/bootstrap.bundle.min.js"></script>
    <script src="<?=BASE?>assets/js/bootstrap-toast.js"></script>
    <script src="<?=BASE?>assets/js/vendors/jquery.sparkline.min.js"></script>
    <script src="<?=BASE?>assets/js/core.js"></script>
    <script type="text/javascript" src="<?=BASE?>assets/plugins/jquery-toast/js/jquery.toast.js"></script>
    <script src="<?=BASE?>themes/pergo/assets/plugins/aos/dist/aos.js"></script>
    <script>
      AOS.init();
    </script>
    <?php  if(segment(1) != 'auth'){?>
    <!-- theme Js -->
    <script src="<?=BASE?>themes/pergo/assets/js/theme.js"></script>
    <?php } ?>
    <!-- Script js -->
    <script src="<?=BASE?>assets/js/process.js"></script>
    <script src="<?=BASE?>assets/js/general.js"></script>
    
    <!-- Visual Effects System -->
    <?php if (get_option('visual_effects_enabled', 0) == 1): ?>
    <script>
      var VISUAL_EFFECTS_CONFIG = {
        enabled: true,
        type: '<?= get_option('visual_effects_type', 'snow') ?>',
        color: '<?= get_option('visual_effects_color', '#ffffff') ?>',
        size: '<?= get_option('visual_effects_size', 'medium') ?>',
        density: '<?= get_option('visual_effects_density', 'medium') ?>',
        speed: '<?= get_option('visual_effects_speed', 'normal') ?>'
      };
    </script>
    <script src="<?=BASE?>assets/js/visual-effects.js"></script>
    <?php endif; ?>
    
    <?=htmlspecialchars_decode(get_option('embed_javascript', ''), ENT_QUOTES)?>
    <script>
      $(document).ready(function(){
        var is_notification_popup = "<?=get_option('enable_notification_popup', 0)?>"
        setTimeout(function(){
            if (is_notification_popup == 1) {
              $("#notification").modal('show');
            }else{
              $("#notification").modal('hide');
            }
        },500);
     });
    </script>
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
    
    <!-- Service Worker Registration for Offline Support -->
    <script>
      if ('serviceWorker' in navigator) {
        window.addEventListener('load', function() {
          navigator.serviceWorker.register('<?=BASE?>service-worker.js')
            .then(function(registration) {
              console.log('✓ Service Worker registered successfully');
              console.log('  Scope:', registration.scope);
              console.log('  Active:', registration.active ? 'Yes' : 'No');
              
              // Log when service worker updates
              registration.addEventListener('updatefound', function() {
                console.log('Service Worker update found!');
              });
            })
            .catch(function(error) {
              console.error('✗ Service Worker registration failed:', error);
              console.log('  Make sure service-worker.js is accessible at:', '<?=BASE?>service-worker.js');
            });
            
          // Log service worker state changes
          navigator.serviceWorker.addEventListener('controllerchange', function() {
            console.log('Service Worker controller changed - page now controlled by SW');
          });
        });
      } else {
        console.warn('Service Workers are not supported in this browser');
      }
    </script>
  </body>
</html>