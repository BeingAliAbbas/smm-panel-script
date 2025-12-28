function General(){
    var self= this;
    this.init= function(){
        //Callback
        self.generalOption();
        self.uploadSettings();
        self.scriptLicense();
        self.users();
        self.add_funds();
       
        self.services();
        if ($("#order_resume").length > 0) {
            self.order();
            self.calculateOrderCharge();
        }
        
        if ($(".sidebar").length > 0) {
            _url = window.location.href;
            _url = _url.split("?t=");
            if(_url.length == 2){
                $('[data-content="'+_url[1]+'"]').trigger("click");
            }
        }        
        
    };

    this.add_funds = function(){
      $(document).on("submit", ".actionAddFundsForm", function(){
        pageOverlay.show();
        event.preventDefault();
        _that         = $(this);
        _action       = PATH + 'add_funds/process';
        _redirect     = _that.data("redirect");
        _data         = _that.serialize();
        _data         = _data + '&' + $.param({token:token});
        $.post(_action, _data, function(_result){
            setTimeout(function(){
              pageOverlay.hide();
            },1500)
            if (is_json(_result)) {
                _result = JSON.parse(_result);

                if (_result.status == 'success' && typeof _result.redirect_url != "undefined") {
                    window.location.href = _result.redirect_url;
                }

                setTimeout(function(){
                    notify(_result.message, _result.status);
                },1500)

                setTimeout(function(){
                    if(_result.status == 'success' && typeof _redirect != "undefined"){
                        reloadPage(_redirect);
                    }
                }, 2000)
            }else{
                setTimeout(function(){
                    $(".add-funds-form-content").html(_result);
                }, 100)
            }
        })
        return false;
      })
    }

    this.users = function(){

        $(document).on("click", ".btnEditCustomRate", function(){
            _that = $(this);
            _url = _that.data("action");
            $('#customRate').load(_url, function(){
                $('#customRate').modal({
                    backdrop: 'static',
                    keyboard: false 
                });
                $('#customRate').modal('show');
            });
            return false;
        });

        /*----------  View user and back to admin  ----------*/
        $(document).on("click", ".ajaxViewUser" , function(){
            event.preventDefault();
            pageOverlay.show();
            _that       = $(this);
            _action     = _that.attr("href");
            _data       = $.param({token:token});
            $.post( _action, _data, function(_result){
                setTimeout(function () {
                    pageOverlay.hide();
                    notify(_result.message, _result.status);
                    if (_result.status == 'success') {
                        _redirect = PATH + 'statistics';
                        reloadPage(_redirect);
                    }
                }, 2000);
            },'json')
        }) 

        $(document).on("click", ".ajaxBackToAdmin" , function(){
            event.preventDefault();
            pageOverlay.show();
            _that       = $(this);
            _action     = _that.attr("href");
            _data       = $.param({token:token});
            $.post( _action, _data, function(_result){
                setTimeout(function () {
                    pageOverlay.hide();
                    notify(_result.message, _result.status);
                    if (_result.status == 'success') {
                        _redirect = PATH + 'users';
                        reloadPage(_redirect);
                    }
                }, 2000);
            },'json')
        }) 

    }

    this.services = function(){

        /*----------  Get Service details  ----------*/
        $(document).on("click", ".ajaxGetServiceDescription", function(){
            event.preventDefault();
            _that     = $(this);
            _url = PATH + 'services/desc/' + _that.data("ids");
            $('#modal-ajax').load(_url, function(){
                $('#modal-ajax').modal({
                    backdrop: 'static',
                    keyboard: false 
                });
                $('#modal-ajax').modal('show');
            });
            return false;
        })


        $(document).on('click', '.check-all', function(){
            _that      = $(this);
            _checkName = _that.data('name');
            $('.'+_checkName+'').prop('checked', this.checked);
        })

        $(document).on("change", ".ajaxChangeServiceType", function(){
            event.preventDefault();
            _that   = $(this);
            _type    = _that.val();
            switch(_type) {
              case "default":
                $("#add_edit_service .dripfeed-form").removeClass("d-none");
                break;  
              default:
                $("#add_edit_service .dripfeed-form").addClass("d-none");
                break;
            }
        })

        $(document).on("click", ".ajaxActionOptions" , function(){
            event.preventDefault();
            _that       = $(this);
            _type       = _that.data("type");

            if ((_type == 'delete' || _type == 'all_deactive' || _type == 'clear_all')) {
                if(!confirm_notice('deleteItems')){
                    return;
                }
            }
            _action     = _that.attr("href");
            _form       = _that.closest('form');
            _ids        = _form.serialize();
            _data       = _ids + '&' +$.param({token:token, type:_type});

            pageOverlay.show();
            $.post( _action, _data, function(_result){
                setTimeout(function () {
                    pageOverlay.hide();
                    notify(_result.message, _result.status);
                    if (_result.status == 'success') {
                        _redirect = '';
                        reloadPage(_redirect);
                    }
                }, 2000);
            },'json')
        }) 
    }

    this.scriptLicense = function(){
        $(document).on("click", ".ajaxUpgradeVersion", function(){
            pageOverlay.show();
            event.preventDefault();
            _that   = $(this);
            _action = _that.attr("href");
            _data   = $.param({token:token});
            $.post(_action, _data, function(_result){
                setTimeout(function () {
                    pageOverlay.hide();
                    notify(_result.message, _result.status);
                    if (_result.status == 'success') {
                        _redirect = '';
                        reloadPage(_redirect);
                    }
                }, 2000);
            },'json')
        })
    }

    this.order = function (){
        _total_quantity = 0;
        _service_price  = 0;

        $(document).on("input", ".ajaxQuantity" , function(){
            _that           = $(this);
            _quantity       = _that.val();
            _service_id     = $("#service_id").val();
            _service_max    = $("#order_resume input[name=service_max]").val();
            _service_min    = $("#order_resume input[name=service_min]").val();
            _service_price  = $("#order_resume input[name=service_price]").val();
            _is_drip_feed   = $("#new_order input[name=is_drip_feed]:checked").val();
            if (_is_drip_feed) {
                _runs           = $("#new_order input[name=runs]").val();
                _interval       = $("#new_order input[name=interval]").val();
                _total_quantity = _runs * _quantity;
                if (_total_quantity != "") {
                    $("#new_order input[name=total_quantity]").val(_total_quantity);
                }
            }else{
                _total_quantity = _quantity;
            }
            _total_charge = (_total_quantity != "" && _service_price != "") ? (_total_quantity * _service_price)/1000 : 0;
            _currency_symbol = $("#new_order input[name=currency_symbol]").val();
            $("#new_order input[name=total_charge]").val(_total_charge);
            $("#new_order .total_charge span").html(_currency_symbol + _total_charge);
        })

        // callback ajaxDripFeedRuns
        $(document).on("input", ".ajaxDripFeedRuns" , function(){
            _that           = $(this);
            _runs           = _that.val();
            _service_id     = $("#service_id").val();
            _quantity       = $("#new_order input[name=quantity]").val();
            _service_max    = $("#order_resume input[name=service_max]").val();
            _service_min    = $("#order_resume input[name=service_min]").val();
            _service_price  = $("#order_resume input[name=service_price]").val();
            _is_drip_feed   = $("#new_order input[name=is_drip_feed]:checked").val();
            if (_is_drip_feed) {
                _interval       = $("#new_order input[name=interval]").val();
                _total_quantity = _runs * _quantity;
                if (_total_quantity != "") {
                    $("#new_order input[name=total_quantity]").val(_total_quantity);
                }
            }else{
                _total_quantity = _quantity;
            }
            _total_charge = (_total_quantity != "" && _service_price != "") ? (_total_quantity * _service_price)/1000 : 0;
            _currency_symbol = $("#new_order input[name=currency_symbol]").val();
            $("#new_order input[name=total_charge]").val(_total_charge);
            $("#new_order .total_charge span").html(_currency_symbol + _total_charge);
        })

        $(document).on("click", ".is_drip_feed" , function(){
            _that           = $(this);
            _service_id     = $("#service_id").val();
            _quantity       = $("#new_order input[name=quantity]").val();
            _service_max    = $("#order_resume input[name=service_max]").val();
            _service_min    = $("#order_resume input[name=service_min]").val();
            _service_price  = $("#order_resume input[name=service_price]").val();
            if (_that.is(":checked")) {
                _runs           = $("#new_order input[name=runs]").val();
                _interval       = $("#new_order input[name=interval]").val();
                _total_quantity = _runs * _quantity;
                if (_total_quantity != "") {
                    $("#new_order input[name=total_quantity]").val(_total_quantity);
                }
            }else{
                _total_quantity = _quantity;
            }
            _total_charge = (_total_quantity != "" && _service_price != "") ? (_total_quantity * _service_price)/1000 : 0;
            _currency_symbol = $("#new_order input[name=currency_symbol]").val();
            $("#new_order input[name=total_charge]").val(_total_charge);
            $("#new_order .total_charge span").html(_currency_symbol + _total_charge);
        })

    }

    this.calculateOrderCharge = function(){

        // callback ajax_custom_comments
        $(document).on("keyup", ".ajax_custom_comments" , function(){
            _quantity = $("#new_order .order-comments textarea[name=comments]").val();
            if (_quantity == "") {
                _quantity = 0;
            }else{
                _quantity = _quantity.split("\n").length;
            }
            _service_id     = $("#service_id").val();
            $("#new_order .order-default-quantity input[name=quantity]").val(_quantity);
            _service_max    = $("#order_resume input[name=service_max]").val();
            _service_min    = $("#order_resume input[name=service_min]").val();
            _service_price  = $("#order_resume input[name=service_price]").val();

            _total_charge = (_quantity != "" && _service_price != "") ? (_quantity * _service_price)/1000 : 0;
            _currency_symbol = $("#new_order input[name=currency_symbol]").val();
            $("#new_order input[name=total_charge]").val(_total_charge);
            $("#new_order .total_charge span").html(_currency_symbol + _total_charge);
        })

        // callback ajax_custom_lists
        $(document).on("keyup", ".ajax_custom_lists" , function(){
            _quantity = $("#new_order .order-usernames-custom textarea[name=usernames_custom]").val();
            console.log(_quantity);
            if (_quantity == "") {
                _quantity = 0;
            }else{
                _quantity = _quantity.split("\n").length;
            }

            _service_id     = $("#service_id").val();
            $("#new_order .order-default-quantity input[name=quantity]").val(_quantity);
            _service_max    = $("#order_resume input[name=service_max]").val();
            _service_min    = $("#order_resume input[name=service_min]").val();
            _service_price  = $("#order_resume input[name=service_price]").val();

            _total_charge = (_quantity != "" && _service_price != "") ? (_quantity * _service_price)/1000 : 0;
            _currency_symbol = $("#new_order input[name=currency_symbol]").val();
            $("#new_order input[name=total_charge]").val(_total_charge);
            $("#new_order .total_charge span").html(_currency_symbol + _total_charge);
        })

    }

    this.generalOption = function(){
        // ajaxToggleItemStatus
        $(document).on("click", ".ajaxToggleItemStatus" , function(){
            var _that           = $(this),
            _id                 =  _that.data('id'),
            _action             = _that.data('action') + _id;
            if (_that.is(":checked")) {
               var _status           = 1;
            }else{
                var _status           = 0;
            }
            _data     = $.param({token:token, status:_status});
            $.post(_action, _data, function(_result){
                console.log(_result.message);
            },'json')
            
        })

        // Insert hyper-link
        $(document).on('focusin', function(e) {
            if ($(event.target).closest(".mce-window").length) {
              e.stopImmediatePropagation();
            }
        });

        // load ajax-Modal
        $(document).on("click", ".ajaxModal", function(){
            _that = $(this);
            _url = _that.attr("href");
            $('#modal-ajax').load(_url, function(){
                $('#modal-ajax').modal({
                    backdrop: 'static',
                    keyboard: false 
                });
                $('#modal-ajax').modal('show');
            });
            return false;
        });

        /*----------  ajaxChangeTicketSubject  ----------*/
        $(document).on("change", ".ajaxChangeTicketSubject", function(){
            event.preventDefault();
            _that   = $(this);
            _type    = _that.val();
            switch(_type) {

              case "subject_order":
                $("#add_new_ticket .subject-order").removeClass("d-none");
                $("#add_new_ticket .subject-payment").addClass("d-none");
                break;  
                              
              case "subject_payment":
                $("#add_new_ticket .subject-order").addClass("d-none");
                $("#add_new_ticket .subject-payment").removeClass("d-none");
                break;

              default:
                $("#add_new_ticket .subject-order").addClass("d-none");
                $("#add_new_ticket .subject-payment").addClass("d-none");
                break;
            }
        })

        // ajaxChangeLanguage
        $(document).on("change", ".ajaxChangeLanguage", function(){
            event.preventDefault();
            _that     = $(this);
            _ids      = _that.val();
            _action   = _that.data("url") + _ids;
            _redirect = _that.data("redirect");
            _data     = $.param({token:token, redirect:_redirect});
            $.post(_action, _data, function(_result){
                pageOverlay.show();
                setTimeout(function () {
                    pageOverlay.hide();
                    location.reload();
                }, 1000);
            },'json')
        })

        // ajaxChangeStatus ticket
        $(document).on("click", ".ajaxChangeStatus", function(){
            event.preventDefault();
            _that   = $(this);
            _action = _that.data("url");
            _status = _that.data("status");
            _data   = $.param({token:token, status:_status});
            $.post(_action, _data, function(_result){
                pageOverlay.show();
                setTimeout(function () {
                    pageOverlay.hide();
                    notify(_result.message, _result.status);
                }, 2000);
                if (_status == 'new' || _status == 'unread') {
                    _redirect = PATH + 'tickets';
                }else{
                    _redirect = '';
                }
                reloadPage(_redirect);
            },'json')
        })

        // callback ajaxChange
        $(document).on("change", ".ajaxChange" , function(){
            pageOverlay.show();
            event.preventDefault();
            _that       = $(this);
            _id         = _that.val();

            if (_id == "") {
                pageOverlay.hide();
                return false;
            }
            _action     = _that.data("url") + _id;
            _data       = $.param({token:token});
            $.post( _action, _data,function(_result){
                pageOverlay.hide();
                setTimeout(function () {
                    $("#result_ajaxSearch").html(_result);
                }, 100);
            });
        })  

        // callback ajaxChangeCategory
        $(document).on("change", ".ajaxChangeCategory" , function(){
            event.preventDefault();
            $("#new_order .drip-feed-option").addClass("d-none");
            if ($("#order_resume").length > 0) {
                $("#order_resume input[name=service_name]").val("");
                $("#order_resume input[name=service_min]").val("");
                $("#order_resume input[name=service_max]").val("");
                $("#order_resume input[name=service_price]").val("");
                $("#order_resume textarea[name=service_desc]").val("");
                $("#order_resume #service_desc").val("");

                $("#new_order input[name=service_price]").val("");
                $("#new_order input[name=service_min]").val("");
                $("#new_order input[name=service_max]").val("");
            }
            _that       = $(this);
            _id         = _that.val();
            if (_id == "") {
                return;
            }
            _action     = _that.data("url") + _id;
            _data       = $.param({token:token});
            $.post( _action, _data,function(_result){
                setTimeout(function () {
                    $("#result_onChange").html(_result);
                }, 100);
            });
        })  

        $(document).on("change", ".ajaxChangeService" , function(){
            event.preventDefault();
            _that         = $(this);
            _id           = _that.val();
            _dripfeed     = _that.children("option:selected").data("dripfeed");
            _service_type = _that.children("option:selected").data("type");

            $("#new_order .order-default-quantity input[name=quantity]").attr("disabled", false);
            $("#new_order .order-usernames-custom").addClass("d-none");
            $("#new_order .order-comments-custom-package").addClass("d-none");

            /*----------  reset quantity  ----------*/
            $("#new_order input[name=service_price]").val();
            $("#new_order input[name=service_min]").val();
            $("#new_order input[name=service_max]").val();

            $("#new_order .order-default-quantity input[name=quantity]").val('');
            _total_charge = 0;
            _currency_symbol = $("#new_order input[name=currency_symbol]").val();
            $("#new_order input[name=total_charge]").val(_total_charge);
            $("#new_order .total_charge span").html(_currency_symbol + _total_charge);
            switch(_service_type) {
              case "subscriptions":
                $("#new_order input[name=sub_expiry]").val('');
                
                $("#new_order .order-default-link").addClass("d-none");
                $("#new_order .order-default-quantity").addClass("d-none");
                $("#new_order #result_total_charge").addClass("d-none");

                $("#new_order .order-comments").addClass("d-none");
                $("#new_order .order-usernames").addClass("d-none");
                $("#new_order .order-hashtags").addClass("d-none");
                $("#new_order .order-username").addClass("d-none");
                $("#new_order .order-hashtag").addClass("d-none");
                $("#new_order .order-media").addClass("d-none");
                
                $("#new_order .order-subscriptions").removeClass("d-none");
                break;

              case "custom_comments":
                $("#new_order .order-default-link").removeClass("d-none");
                $("#new_order .order-comments").removeClass("d-none");
                $("#new_order #result_total_charge").removeClass("d-none");

                $("#new_order .order-usernames").addClass("d-none");
                $("#new_order .order-hashtags").addClass("d-none");
                $("#new_order .order-username").addClass("d-none");
                $("#new_order .order-hashtag").addClass("d-none");
                $("#new_order .order-media").addClass("d-none");

                $("#new_order .order-default-quantity").removeClass("d-none");
                $("#new_order .order-default-quantity input[name=quantity]").attr("disabled", true);
                
                $("#new_order .order-subscriptions").addClass("d-none");
                break; 

              case "custom_comments_package":
                $("#new_order .order-default-link").removeClass("d-none");
                $("#new_order .order-comments-custom-package").removeClass("d-none");
                $("#new_order #result_total_charge").removeClass("d-none");

                $("#new_order .order-comments").addClass("d-none");
                $("#new_order .order-default-quantity").addClass("d-none");
                $("#new_order .order-usernames").addClass("d-none");
                $("#new_order .order-hashtags").addClass("d-none");
                $("#new_order .order-username").addClass("d-none");
                $("#new_order .order-hashtag").addClass("d-none");
                $("#new_order .order-media").addClass("d-none");
                $("#new_order .order-subscriptions").addClass("d-none");
                break; 

              case "mentions_with_hashtags":
                $("#new_order .order-default-link").removeClass("d-none");
                $("#new_order .order-default-quantity").removeClass("d-none");
                $("#new_order .order-usernames").removeClass("d-none");
                $("#new_order .order-hashtags").removeClass("d-none");
                $("#new_order #result_total_charge").removeClass("d-none");

                $("#new_order .order-comments").addClass("d-none");
                $("#new_order .order-username").addClass("d-none");
                $("#new_order .order-hashtag").addClass("d-none");
                $("#new_order .order-media").addClass("d-none");
                
                $("#new_order .order-subscriptions").addClass("d-none");

                break;

              case "mentions_custom_list":
                $("#new_order .order-default-link").removeClass("d-none");
                $("#new_order .order-usernames-custom").removeClass("d-none");
                $("#new_order #result_total_charge").removeClass("d-none");
                $("#new_order .order-default-quantity").removeClass("d-none");
                $("#new_order .order-default-quantity input[name=quantity]").attr("disabled", true);
                
                $("#new_order .order-usernames").addClass("d-none");
                $("#new_order .order-comments").addClass("d-none");
                $("#new_order .order-username").addClass("d-none");
                $("#new_order .order-hashtags").addClass("d-none");
                $("#new_order .order-hashtag").addClass("d-none");
                $("#new_order .order-media").addClass("d-none");
                
                $("#new_order .order-subscriptions").addClass("d-none");

                break;  

              case "mentions_hashtag":
                $("#new_order .order-default-link").removeClass("d-none");
                $("#new_order .order-default-quantity").removeClass("d-none");
                $("#new_order .order-hashtag").removeClass("d-none");
                $("#new_order #result_total_charge").removeClass("d-none");

                $("#new_order .order-comments").addClass("d-none");
                $("#new_order .order-usernames").addClass("d-none");
                $("#new_order .order-hashtags").addClass("d-none");
                $("#new_order .order-username").addClass("d-none");
                $("#new_order .order-media").addClass("d-none");
                $("#new_order .order-subscriptions").addClass("d-none");

                break;

              case "mentions_user_followers":
                $("#new_order .order-default-link").removeClass("d-none");
                $("#new_order .order-default-quantity").removeClass("d-none");
                $("#new_order .order-username").removeClass("d-none");
                $("#new_order #result_total_charge").removeClass("d-none");

                $("#new_order .order-comments").addClass("d-none");
                $("#new_order .order-usernames").addClass("d-none");
                $("#new_order .order-hashtags").addClass("d-none");
                $("#new_order .order-hashtag").addClass("d-none");
                $("#new_order .order-media").addClass("d-none");
                $("#new_order .order-subscriptions").addClass("d-none");
                break;

              case "mentions_media_likers":
                $("#new_order .order-default-link").removeClass("d-none");
                $("#new_order .order-default-quantity").removeClass("d-none");
                $("#new_order .order-media").removeClass("d-none");
                $("#new_order #result_total_charge").removeClass("d-none");

                $("#new_order .order-comments").addClass("d-none");
                $("#new_order .order-usernames").addClass("d-none");
                $("#new_order .order-hashtags").addClass("d-none");
                $("#new_order .order-username").addClass("d-none");
                $("#new_order .order-hashtag").addClass("d-none");
                $("#new_order .order-subscriptions").addClass("d-none");

                break;  

              case "package":
                $("#new_order .order-default-link").removeClass("d-none");
                $("#new_order #result_total_charge").removeClass("d-none");
                

                $("#new_order .order-default-quantity").addClass("d-none");
                $("#new_order .order-comments").addClass("d-none");
                $("#new_order .order-usernames").addClass("d-none");
                $("#new_order .order-hashtags").addClass("d-none");
                $("#new_order .order-username").addClass("d-none");
                $("#new_order .order-hashtag").addClass("d-none");
                $("#new_order .order-media").addClass("d-none");
                $("#new_order .order-subscriptions").addClass("d-none");

                break;

              case "comment_likes":
                $("#new_order .order-default-link").removeClass("d-none");
                $("#new_order .order-default-quantity").removeClass("d-none");
                $("#new_order .order-username").removeClass("d-none");
                $("#new_order #result_total_charge").removeClass("d-none");

                $("#new_order .order-comments").addClass("d-none");
                $("#new_order .order-usernames").addClass("d-none");
                $("#new_order .order-hashtags").addClass("d-none");
                $("#new_order .order-hashtag").addClass("d-none");
                $("#new_order .order-media").addClass("d-none");
                $("#new_order .order-subscriptions").addClass("d-none");
                break;

              default:
                $("#new_order .order-default-link").removeClass("d-none");
                $("#new_order .order-default-quantity").removeClass("d-none");
                $("#new_order #result_total_charge").removeClass("d-none");

                
                $("#new_order .order-comments").addClass("d-none");
                $("#new_order .order-usernames").addClass("d-none");
                $("#new_order .order-hashtags").addClass("d-none");
                $("#new_order .order-username").addClass("d-none");
                $("#new_order .order-hashtag").addClass("d-none");
                $("#new_order .order-media").addClass("d-none");

                $("#new_order .order-subscriptions").addClass("d-none");

                break;
            }

            if (_dripfeed) {
                $("#new_order .drip-feed-option").removeClass("d-none");
            } else {
                $("#new_order .drip-feed-option").addClass("d-none");
            }

            _action     = _that.data("url") + _id;
            _data       = $.param({token:token});
            $.post( _action, _data,function(_result){
                $("#result_onChangeService").html(_result);
                // display min-max on Mobile Reponsive
                _service_price  = $("#order_resume input[name=service_price]").val();
                _service_min    = $("#order_resume input[name=service_min]").val();
                _service_max    = $("#order_resume input[name=service_max]").val();
                $("#new_order input[name=service_price]").val(_service_price);
                $("#new_order input[name=service_min]").val(_service_min);
                $("#new_order input[name=service_max]").val(_service_max);

                setTimeout(function () {
                    if (_service_type == "package" || _service_type == "custom_comments_package" ) {
                        _total_charge   = _service_price;
                        _currency_symbol = $("#new_order input[name=currency_symbol]").val();
                        $("#new_order input[name=total_charge]").val(_total_charge);
                        $("#new_order .total_charge span").html(_currency_symbol + _total_charge);
                    }
                }, 100);
            });
        }) 

        // callback ajaxSearch
        $(document).on("submit", ".ajaxSearchItem" , function(){
            pageOverlay.show();
            event.preventDefault();
            var _that       = $(this),
                _action     = _that.attr("action"),
                _data       = _that.serialize();

            _data       = _data + '&' + $.param({token:token});
            $.post( _action, _data, function(_result){
                setTimeout(function () {
                    pageOverlay.hide();
                    $("#result_ajaxSearch").html(_result);
                }, 300);
            });
        })

        // callback ajaxSearchItemsKeyUp with keyup and Submit from
        var typingTimer;                //timer identifier
        $(document).on("keyup", ".ajaxSearchItemsKeyUp" , function(){
            $(window).keydown(function(event){
                if(event.keyCode == 13) {
                  event.preventDefault();
                  return false;
                }
            });
            event.preventDefault();
            clearTimeout(typingTimer);
            $(".ajaxSearchItemsKeyUp .btn-searchItem").addClass("btn-loading");
            var _that       = $(this),
                _form       = _that.closest('form'),
                _action     = _form.attr("action"),
                _data       = _form.serialize();
            _data       = _data + '&' + $.param({token:token});

            // if ( $("input:text").val().length < 2 ) {
            //     $(".ajaxSearchItemsKeyUp .btn-searchItem").removeClass("btn-loading");
            //     return;
            // }

            typingTimer = setTimeout(function () {
                $.post( _action, _data, function(_result){
                    setTimeout(function () {
                        $(".ajaxSearchItemsKeyUp .btn-searchItem").removeClass("btn-loading");
                        $("#result_ajaxSearch").html(_result);
                    }, 10);
                });
            }, 1500);

        })

        $(document).on("submit", ".ajaxSearchItemsKeyUp" , function(){
            event.preventDefault();
        })

        /*----------  Add a service from API provider  ----------*/
        $(document).on("click", ".ajaxAddService", function(){
            event.preventDefault();
            _that = $(this);
            _serviceid          = _that.data("serviceid");
            _name               = _that.data("name");
            _min                = _that.data("min");
            _max                = _that.data("max");
            _price              = _that.data("price");
            _dripfeed           = _that.data("dripfeed");
            _api_provider_id    = _that.data("api_provider_id");
            _type               = _that.data("type");
            _service_desc       = _that.data("service_desc");
            $("#modal-add-service input[name=dripfeed]").val(_dripfeed);
            $("#modal-add-service input[name=service_id]").val(_serviceid);
            $("#modal-add-service input[name=name]").val(_name);
            $("#modal-add-service input[name=min]").val(_min);
            $("#modal-add-service input[name=max]").val(_max);
            $("#modal-add-service input[name=price]").val(_price);
            $("#modal-add-service input[name=api_provider_id]").val(_api_provider_id);
            $("#modal-add-service input[name=type]").val(_type);
            $("#modal-add-service textarea[name=service_desc]").val(_service_desc);
            $('#modal-add-service').modal('show');
        })

        $(document).on("click", ".ajaxUpdateApiProvider", function(){
            $("#result_notification").html("");
            pageOverlay.show();
            event.preventDefault();
            _that   = $(this);
            _action = _that.attr("href");
            _redirect   = _that.data("redirect");
            _data   = $.param({token:token});
            $.post(_action, _data, function(_result){
                setTimeout(function () {
                    pageOverlay.hide();
                    notify(_result.message, _result.status);
                    if(_result.status == 'success' && typeof _redirect != "undefined"){
                        reloadPage(_redirect);
                    }
                }, 2000);
            },'json')
        })


        /*----------  Sync Services  ----------*/
        $(document).on("submit", ".actionSyncApiServices", function(){
            $("#result_notification").html("");
            pageOverlay.show();
            event.preventDefault();
            _that       = $(this);
            _action     = _that.attr("action");
            _redirect   = _that.data("redirect");

            if ($("#mass_order").hasClass("active")) {
                _data = $("#mass_order").find("input[name!=mass_order]").serialize();
                _mass_order_array = [];
                _mass_orders = $("#mass_order").find("textarea[name=mass_order]").val();
                if (_mass_orders.length > 0 ) {
                    _mass_orders = _mass_orders.split(/\n/);
                    for (var i = 0; i < _mass_orders.length; i++) {
                        // only push this line if it contains a non whitespace character.
                        if (/\S/.test(_mass_orders[i])) {
                            _mass_order_array.push($.trim(_mass_orders[i]));
                        }
                    }
                }
                _data       = _data + '&' + $.param({mass_order:_mass_order_array, token:token});
            }else{
                _data       = _that.serialize();
                _data       = _data + '&' + $.param({token:token});
            }

            $.post(_action, _data, function(_result){
                if (is_json(_result)) {
                    _result = JSON.parse(_result);
                    if(_result.status == 'success' && typeof _redirect != "undefined"){
                        reloadPage(_redirect);
                    }
                    setTimeout(function(){
                        pageOverlay.hide();
                    },2000)
                    setTimeout(function () {
                        notify(_result.message, _result.status);
                    }, 3000);
                }else{
                    setTimeout(function(){
                        pageOverlay.hide();
                        $('#modal-ajax').modal('hide');
                        $("#result_notification").html(_result);
                    },2000)
                }
            })
            return false;
        })

        // callback actionForm
        $(document).on("submit", ".actionForm", function(){
            pageOverlay.show();
            event.preventDefault();
            var _that       = $(this),
                _action     = _that.attr("action"),
                _redirect   = _that.data("redirect");
            if ($("#mass_order").hasClass("active")) {
                _data = $("#mass_order").find("input[name!=mass_order]").serialize();
                _mass_order_array = [];
                _mass_orders = $("#mass_order").find("textarea[name=mass_order]").val();
                if (_mass_orders.length > 0 ) {
                    _mass_orders = _mass_orders.split(/\n/);
                    for (var i = 0; i < _mass_orders.length; i++) {
                        // only push this line if it contains a non whitespace character.
                        if (/\S/.test(_mass_orders[i])) {
                            _mass_order_array.push($.trim(_mass_orders[i]));
                        }
                    }
                }

                _data       = _data + '&' + $.param({mass_order:_mass_order_array, token:token});
            }else{
                var _token = $(".actionForm").find("input[name=token]").val();
                _data       = _that.serialize();
                if (typeof _token == "undefined") {
                    _data       = _data + '&' + $.param({token:token});
                }
            }
            
            $.post(_action, _data, function(_result){
                setTimeout(function(){
                    pageOverlay.hide();
                },1500)

                if (is_json(_result)) {
                    _result = JSON.parse(_result);
<<<<<<< HEAD
                    
                    // Check if this is a duplicate order error
                    if (_result.status == 'error' && _result.order_exists === true) {
                        // Show duplicate order modal instead of just notification
                        setTimeout(function(){
                            showDuplicateOrderModal(_result);
                        }, 1500);
                        return;
                    }
                    
                    // Check if this is a successful order with confirmation modal
                    if (_result.status == 'success' && _result.show_confirmation_modal === true) {
                        setTimeout(function(){
                            showOrderConfirmationModal(_result.order_details);
                        }, 1500);
                        return;
                    }
                    
=======
>>>>>>> dd720c81418616f5ea5455fb1a7b66ce0090eb98
                    setTimeout(function(){
                        notify(_result.message, _result.status);
                    },1500)
                    setTimeout(function(){
                        if(_result.status == 'success' && typeof _redirect != "undefined"){
                            reloadPage(_redirect);
                        }
                    }, 2000)
                }else{
                    setTimeout(function(){
                        $("#result_notification").html(_result);
                    }, 1500)
                }
            })
            return false;
        })

        // actionFormWithoutToast
        $(document).on("submit", ".actionFormWithoutToast", function(){
            alertMessage.hide();
            event.preventDefault();
            var _that       = $(this),
                _action     = _that.attr("action"),
                _data       = _that.serialize();
                _data       = _data + '&' + $.param({token:token});
                _redirect   = _that.data("redirect");

                _that.find(".btn-submit").addClass('btn-loading');
            
            $.post(_action, _data, function(_result){
                if (is_json(_result)) {
                    _result = JSON.parse(_result);
                    setTimeout(function(){
                        alertMessage.show(_result.message, _result.status);
                    }, 1500)

                    setTimeout(function(){
                        if(_result.status == 'success' && typeof _redirect != "undefined"){
                            reloadPage(_redirect);
                        }
                    }, 2000)

                }else{
                    setTimeout(function(){
                        $("#resultActionForm").html(_result);
                    }, 1500)
                }

                setTimeout(function(){
                    _that.find(".btn-submit").removeClass('btn-loading');
                }, 1500)
            })
            return false;
        })

        // callback Delete item
        $(document).on("click", ".ajaxDeleteItem", function(){
            event.preventDefault();
            if(!confirm_notice('deleteItem')){
                return;
            }
            _that       = $(this);
            _action     = _that.attr("href");
            _data       = $.param({token:token});

            $.post(_action, _data, function(_result){
                pageOverlay.show();
                if(_result.status =='success'){
                    $(".tr_" + _result.ids).remove();
                }
                setTimeout(function () {
                    pageOverlay.hide();
                    notify(_result.message, _result.status);
                }, 2000);
            },'json')
        })

        /*----------  callback Change status itme  ----------*/
        $(document).on("click", ".ajaxChangeStatusItem", function(){
            event.preventDefault();
            _that       = $(this);
            _action     = _that.attr("href");
            _status     = _that.data("status");
            _redirect   = _that.data("redirect");
            _data       = $.param({token:token, status:_status});
            $.post(_action, _data, function(_result){
                pageOverlay.show();
                setTimeout(function () {
                    pageOverlay.hide();
                    notify(_result.message, _result.status);
                }, 2000);
                if (_result.status == 'success' && typeof _redirect != "undefined") {
                    reloadPage(_redirect);
                }
            },'json')
        })

        // callback ajaxGetContents
        $(document).on("click", ".ajaxGetContents" , function(){
            pageOverlay.show();
            _that       = $(this);
            $(".settings .sidebar li").removeClass("active");
            _that.parent().addClass("active");

            _type       = _that.data("content");
            _action     = _that.attr("href");
            _data       = $.param({token:token, type:_type});
            $.post( _action, _data, function(_result){
                $("#result_get_contents").html(_result);
                history.pushState(null, "", _action.replace("/ajax_get_contents/","?t="));
                setTimeout(function () {
                    pageOverlay.hide();
                }, 300);
            });
            return false;
        }) 

    }

    // Upload media on Settings page
    this.uploadSettings = function () {
        var url = PATH + "file_manager/upload_files";
        $(document).on('click','.settings_fileupload',function(){
            _that = $(this);
            _closest_div = _that.closest('div');
            $('.settings .settings_fileupload').fileupload({
                url: url,
                formData: {token:token},
                dataType: 'json',
<<<<<<< HEAD
                maxFileSize: 5000000, // 5MB
                add: function (e, data) {
                    // Validate file type
                    var uploadErrors = [];
                    var acceptFileTypes = /^image\/(gif|jpe?g|png|svg\+xml|x-icon|vnd\.microsoft\.icon)$/i;
                    if(data.originalFiles[0]['type'].length && !acceptFileTypes.test(data.originalFiles[0]['type'])) {
                        uploadErrors.push('Not an accepted file type (only images allowed)');
                    }
                    // Validate file size
                    if(data.originalFiles[0]['size'] && data.originalFiles[0]['size'] > 5000000) {
                        uploadErrors.push('File is too big (max 5MB)');
                    }
                    if(uploadErrors.length > 0) {
                        if (typeof notify === 'function') {
                            notify(uploadErrors.join("\n"), 'error');
                        } else {
                            alert(uploadErrors.join("\n"));
                        }
                    } else {
                        data.submit();
                    }
                },
                done: function (e, data) {
                    if (data.result.status == "success") {
                        _img_link = data.result.link;
                        _closest_div.children('input').val(_img_link);
                        
                        // Update preview if exists
                        var previewId = null;
                        if (_closest_div.find('input').hasClass('favicon-url-input')) {
                            previewId = 'favicon-preview';
                        } else if (_closest_div.find('input').hasClass('logo-url-input')) {
                            previewId = 'logo-preview';
                        } else if (_closest_div.find('input').hasClass('logo-white-url-input')) {
                            previewId = 'logo-white-preview';
                        }
                        
                        if (previewId) {
                            var previewImg = document.getElementById(previewId);
                            if (previewImg) {
                                previewImg.src = _img_link;
                                previewImg.style.display = 'block';
                                if (previewImg.nextElementSibling && previewImg.nextElementSibling.classList.contains('logo-preview-placeholder')) {
                                    previewImg.nextElementSibling.style.display = 'none';
                                }
                            }
                        }
                        
                        // Show success message
                        if (typeof notify === 'function') {
                            notify('Image uploaded successfully!', 'success');
                        }
                    } else if (data.result.status == "error") {
                        if (typeof notify === 'function') {
                            notify(data.result.message || 'Upload failed', 'error');
                        } else {
                            alert(data.result.message || 'Upload failed');
                        }
                    }
                },
                fail: function (e, data) {
                    if (typeof notify === 'function') {
                        notify('Upload failed. Please try again.', 'error');
                    } else {
                        alert('Upload failed. Please try again.');
                    }
                }
=======
                done: function (e, data) {
                if (data.result.status == "success") {
                  _img_link = data.result.link;
                  _closest_div.children('input').val(_img_link);
                }
              },
>>>>>>> dd720c81418616f5ea5455fb1a7b66ce0090eb98
            });
        });
    }

    // Check post type
    $(document).on("change","input[type=radio][name=email_protocol_type]", function(){
      _that = $(this);
      _type = _that.val();
      if(_type == 'smtp'){
        $('.smtp-configure').removeClass('d-none');
      }else{
        $('.smtp-configure').addClass('d-none');
      }
    });
}
<<<<<<< HEAD

// Helper function to show duplicate order modal
function showDuplicateOrderModal(data) {
    // Helper function to escape HTML
    function escapeHtml(text) {
        var map = {
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#039;'
        };
        return String(text).replace(/[&<>"']/g, function(m) { return map[m]; });
    }
    
    // Sanitize data
    var orderId = escapeHtml(data.existing_order_id || '');
    // Convert to upper before escaping to avoid HTML entity case issues
    var orderStatus = String(data.existing_order_status || 'unknown').toUpperCase();
    var orderStatusEscaped = escapeHtml(orderStatus);
    var orderCreated = escapeHtml(data.existing_order_created || '');
    
    // Escape PATH to prevent XSS
    var safePath = escapeHtml(typeof PATH !== 'undefined' ? PATH : '');
    
    var modalHtml = `
<div class="modal fade" id="duplicateOrderModal" tabindex="-1" role="dialog" aria-labelledby="duplicateOrderModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content" style="border-radius: 10px; border: none; box-shadow: 0 4px 20px rgba(0,0,0,0.2);">
      <div class="modal-header" style="background: linear-gradient(135deg, #e74c3c 0%, #c0392b 100%); color: white; border-radius: 10px 10px 0 0;">
        <h5 class="modal-title" id="duplicateOrderModalLabel">
          <i class="fe fe-alert-triangle"></i> Order Already Exists
          <br><small>یہ آرڈر پہلے سے موجود ہے</small>
        </h5>
        <!-- include both attributes for BS4 and BS5 -->
        <button type="button" class="close text-white" data-dismiss="modal" data-bs-dismiss="modal" aria-label="Close">

        </button>
      </div>

      <div class="modal-body" style="padding: 30px;">
        <div class="alert alert-warning" role="alert" style="border-left: 4px solid #f39c12;">
          <h6 class="alert-heading">
            <i class="fe fe-info"></i> Why can't I place this order?
            <br><small>میں یہ آرڈر کیوں نہیں دے سکتا؟</small>
          </h6>
          <p class="mb-0">
            An order with the same link is already being processed.
            <br><small>اسی لنک کے ساتھ ایک آرڈر پہلے سے پراسیس ہو رہا ہے۔</small>
          </p>
        </div>

        <div class="existing-order-details mt-4">
          <h6 class="mb-3"><strong>Existing Order Details:</strong><br><small>موجودہ آرڈر کی تفصیلات</small></h6>
          <table class="table table-bordered">
            <tbody>
              <tr>
                <td><strong>Order ID:</strong></td>
                <td>#${orderId}</td>
              </tr>
              <tr>
                <td><strong>Status:</strong></td>
                <td><span class="badge badge-info">${orderStatusEscaped}</span></td>
              </tr>
              <tr>
                <td><strong>Created:</strong></td>
                <td>${orderCreated}</td>
              </tr>
            </tbody>
          </table>
        </div>

        <div class="alert alert-info mt-3" role="alert" style="border-left: 4px solid #3498db;">
          <p class="mb-0"><i class="fe fe-check-circle"></i> <strong>What should I do?</strong><br><small>مجھے کیا کرنا چاہیے؟</small></p>
          <p class="mb-0 mt-2">Please wait for the existing order to complete before placing a new order for the same link.<br><small>براہ کرم اسی لنک کے لیے نیا آرڈر دینے سے پہلے موجودہ آرڈر مکمل ہونے کا انتظار کریں۔</small></p>
        </div>
      </div>

      <div class="modal-footer" style="border-top: 1px solid #e0e0e0;">
        <!-- include both attributes for BS4 and BS5 -->
        <button type="button" class="btn btn-secondary" data-dismiss="modal" data-bs-dismiss="modal">Close</button>
        <a href="${safePath}order/log" class="btn btn-primary">View My Orders</a>
      </div>
    </div>
  </div>
</div>
    `;

    // Remove any existing modal
    $('#duplicateOrderModal').remove();
    
    // Append modal
    $('body').append(modalHtml);

    // Compatibility: use Bootstrap 5's Modal API if available, otherwise fall back to jQuery/BS4
    var bsModalInstance = null;
    if (typeof bootstrap !== 'undefined' && typeof bootstrap.Modal === 'function') {
        // Bootstrap 5
        bsModalInstance = new bootstrap.Modal(document.getElementById('duplicateOrderModal'), {
            backdrop: true,
            keyboard: true
        });
        bsModalInstance.show();
    } else if (typeof $ !== 'undefined' && typeof $.fn.modal === 'function') {
        // Bootstrap 4 / jQuery
        $('#duplicateOrderModal').modal({ backdrop: true, keyboard: true, show: true });
    } else {
        // No bootstrap detected — attempt to just show the element
        $('#duplicateOrderModal').show();
    }

    // Ensure close controls always hide the modal (covers edge cases)
    // Use a one-time delegated handler bound to the modal element
    $('#duplicateOrderModal').on('click', '[data-dismiss], [data-bs-dismiss], .close, .btn-close', function (e) {
        e.preventDefault();
        if (bsModalInstance && typeof bsModalInstance.hide === 'function') {
            bsModalInstance.hide();
        } else if (typeof $ !== 'undefined' && typeof $.fn.modal === 'function') {
            $('#duplicateOrderModal').modal('hide');
        } else {
            $('#duplicateOrderModal').hide();
            $('#duplicateOrderModal').remove();
        }
    });

    // Remove modal from DOM when fully hidden
    // works for both BS4 and BS5 (they use the same event names)
    $('#duplicateOrderModal').on('hidden.bs.modal', function () {
        $(this).remove();
    });
}


// Helper function to show order confirmation modal after successful order placement
function showOrderConfirmationModal(orderDetails) {
    // Check if modal was already shown for this order to prevent duplicates
    var lastShownOrderId = localStorage.getItem('lastConfirmationModalOrderId');
    if (lastShownOrderId == orderDetails.order_id) {
        // Modal already shown for this order, just reload
        reloadPage(PATH + 'order/add');
        return;
    }

    // Store order ID to prevent showing again
    localStorage.setItem('lastConfirmationModalOrderId', orderDetails.order_id);

    // Helper function to escape HTML
    function escapeHtml(text) {
        var map = {
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#039;'
        };
        return String(text).replace(/[&<>"']/g, function(m) { return map[m]; });
    }

    // Sanitize data
    var orderId = escapeHtml(orderDetails.order_id || '');
    var serviceName = escapeHtml(orderDetails.service_name || '');
    var link = escapeHtml(orderDetails.link || '');
    var quantity = escapeHtml(orderDetails.quantity || '');
    var charge = escapeHtml(orderDetails.charge || '');
    var currencySymbol = escapeHtml(orderDetails.currency_symbol || '$');
    var status = escapeHtml(orderDetails.status || 'Pending');
    var estimatedTime = escapeHtml(orderDetails.estimated_time || '30 minutes to 24 hours');

    var modalHtml = `
        <div class="modal fade" id="orderConfirmationModal" tabindex="-1" role="dialog" aria-labelledby="orderConfirmationModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content" style="border-radius: 10px; border: none; box-shadow: 0 4px 20px rgba(0,0,0,0.2);">
                    <div class="modal-header" style="background: linear-gradient(135deg, #27ae60 0%, #229954 100%); color: white; border-radius: 10px 10px 0 0;">
                        <h5 class="modal-title" id="orderConfirmationModalLabel">
                            <i class="fe fe-check-circle"></i> Order Placed Successfully
                        </h5>
                        <!-- added js-close-modal class to ensure explicit handler works across bootstrap versions -->
                        <button type="button" class="close text-white js-close-modal" aria-label="Close">
                        </button>
                    </div>
                    <div class="modal-body" style="padding: 30px;">
                        <div class="alert alert-success" role="alert" style="border-left: 4px solid #27ae60;">
                            <h6 class="alert-heading"><i class="fe fe-check"></i> Your order has been placed!</h6>
                            <p class="mb-0">Your order has been successfully submitted and is now being processed.</p>
                        </div>
                        <div class="order-details mt-4">
                            <h6 class="mb-3"><strong>Order Details:</strong></h6>
                            <table class="table table-bordered">
                                <tbody>
                                    <tr>
                                        <td><strong>Order ID:</strong></td>
                                        <td><span class="badge badge-primary" style="font-size: 1em;">#${orderId}</span></td>
                                    </tr>
                                    <tr>
                                        <td><strong>Service:</strong></td>
                                        <td>${serviceName}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Link:</strong></td>
                                        <td style="word-break: break-all;">${link}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Quantity:</strong></td>
                                        <td>${quantity}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Total Charge:</strong></td>
                                        <td>${currencySymbol}${charge}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Status:</strong></td>
                                        <td><span class="badge badge-info">${status}</span></td>
                                    </tr>
                                    <tr>
                                        <td><strong>Estimated Time:</strong></td>
                                        <td><i class="fe fe-clock"></i> Approximately ${estimatedTime}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="alert alert-info mt-3" role="alert" style="border-left: 4px solid #3498db;">
                            <p class="mb-0"><i class="fe fe-info"></i> <strong>What's next?</strong></p>
                            <p class="mb-0 mt-2">You can track your order status in the "Orders" section. You will be notified once the order is completed.</p>
                        </div>
                    </div>
                    <div class="modal-footer" style="border-top: 1px solid #e0e0e0;">
                        <!-- added js-close-modal class here too -->
                        <button type="button" class="btn btn-secondary js-close-modal">Close</button>
                        <a href="${PATH}order/log" class="btn btn-primary">View My Orders</a>
                    </div>
                </div>
            </div>
        </div>
    `;

    // Remove any existing modal (and its handlers) before adding new
    $('#orderConfirmationModal').remove();

    // Append modal
    $('body').append(modalHtml);

    // Get modal element
    var modalEl = document.getElementById('orderConfirmationModal');

    // Try to show modal in a way that works for both Bootstrap 5 (vanilla) and Bootstrap 4 (jQuery)
    var bsModalInstance = null;
    if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
        // Bootstrap 5+ (vanilla JS)
        bsModalInstance = bootstrap.Modal.getOrCreateInstance(modalEl);
        bsModalInstance.show();
    } else if (window.jQuery && typeof jQuery.fn.modal === 'function') {
        // Bootstrap 3/4 with jQuery
        $('#orderConfirmationModal').modal('show');
    } else {
        // Fallback: simple show
        modalEl.classList.add('show');
        modalEl.style.display = 'block';
        document.body.classList.add('modal-open');
    }

    // Remove previous delegated handlers in this namespace to avoid duplicates
    $(document).off('click.orderConf', '#orderConfirmationModal .js-close-modal');

    // Add explicit click handler for close controls (works regardless of data-dismiss/data-bs-dismiss)
    $(document).on('click.orderConf', '#orderConfirmationModal .js-close-modal', function (e) {
        e.preventDefault();
        if (bsModalInstance && typeof bsModalInstance.hide === 'function') {
            bsModalInstance.hide();
        } else if (window.jQuery && typeof jQuery.fn.modal === 'function') {
            $('#orderConfirmationModal').modal('hide');
        } else {
            // Fallback hide
            modalEl.classList.remove('show');
            modalEl.style.display = 'none';
            document.body.classList.remove('modal-open');
            // trigger hidden event manually so cleanup runs
            $(modalEl).trigger('hidden.bs.modal');
        }
    });

    // Clean up and reload when modal is fully hidden
    // Remove previous handler first to prevent duplicate actions
    $(modalEl).off('hidden.bs.modal.orderConf').on('hidden.bs.modal.orderConf', function () {
        $(this).remove();
        // Reload the page to clear the form
        reloadPage(PATH + 'order/add');
    });
}

=======
>>>>>>> dd720c81418616f5ea5455fb1a7b66ce0090eb98
General= new General();
$(function(){
    General.init();
});