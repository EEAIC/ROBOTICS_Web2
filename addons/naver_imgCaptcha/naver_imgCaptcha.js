/* procFilter 함수를 가로채서 captcha 이미지 및 폼을 출력 */
var calledArgs = null;
(function($){
	$(function() {
		var captchaXE = {
            html : null,

            show :  function(ret_obj) {
                if (!captchaXE.html) {
                    captchaXE.html = $(ret_obj.view);
                    captchaXE.html.appendTo(document.body);
                }

                $('.naver_captcha-dialog').show();
                $('#captcha_image').attr("src", current_url.setQuery('captcha_action','captchaImage').setQuery('rnd', (new Date).getTime()));
                $(captchaXE.html).append('<input type="hidden" name="error_return_url" value="'+current_url+'" />');
                
                captchaXE.html.find('button#reload')
                .click(function(){					
                    $("#captcha_image").attr("src", current_url.setQuery('captcha_action','captchaImage').setQuery('rnd', (new Date).getTime()));												
                });

                $('#captcha_layer form')
                .submit(function(e){
                    e.preventDefault();
                    if(!$('#captcha_value').val()){
                        $(this).find('input[type=text]').val('').focus();
                        return false;
                    }
                 
                    captchaXE.compare(); return false;
                });
             
                
            },

            compare : function(e) {
                var params = new Array();
                params['captcha_action'] = 'captchaCompare';
                params['mid'] = current_mid;
                params['captcha_value'] = $('#captcha_value').val();
                
                window.oldExecXml(calledArgs.module,calledArgs.act,params, function(response) {
					var isEqual = parseInt(response.result)
					console.log(response.result);
                    if (isEqual) {
						$("#captcha_layer").hide();
						window.oldExecXml(calledArgs.module, calledArgs.act, calledArgs.params, calledArgs.callback_func, calledArgs.response_tags, calledArgs.callback_func_arg, calledArgs.fo_obj);
					} else {
						$('#captcha_image').attr("src", current_url.setQuery('captcha_action','captchaImage').setQuery('rnd', (new Date).getTime()));
						$('#captcha_value').select();
					}
                }, new Array('result'));
          
            },

            exec : function(module, act, params, callback_func, response_tags, callback_func_arg, fo_obj) {
                var doCheck = false;

                $.each(captchaTargetAct || {}, function(key,val){ if (val == act){ doCheck = true; return false; } });

                if (doCheck) { /* captcha 를 사용하는 경우 */

                
                    if (!captchaXE.html){                       
                        calledArgs = {'module':module,'act':act,'params':params,'callback_func':callback_func,'response_tags':response_tags,'callback_func_arg':callback_func_arg,'fo_obj':fo_obj};
                        var params = new Array();
                        params['captcha_action'] = 'getHtml';
                        params['mid'] = current_mid;
                        window.oldExecXml(module, act, params, captchaXE.show, new Array('view', 'key'));

                    } else {
                        captchaXE.show('test');
                    }

                   
                } else {
                    window.oldExecXml(module, act, params, callback_func, response_tags, callback_func_arg, fo_obj);
                }

                return true;
            }


		};
		
		function xeCaptcha() {
			$('form').each(function(i)
			{
				var isSubmitHook = false;
				if (!$(this).attr('onsubmit') ||  $(this).attr('onsubmit').indexOf('procFilter') < 0)
				{
					var act = $(this).find('input[name=act]').val()
					for(var i = 0; i<captchaTargetAct.length; i++)
					{
						if(captchaTargetAct[i] == act)
						{
							isSubmitHook = true;
							break;
						}
					}
				}

				if (isSubmitHook)
				{
                    $(this).append('<input type="hidden" name="captchaType" value="image" />');
					if(!$(this).find('input[name=error_return_url]'))
						$(this).append('<input type="hidden" name="error_return_url" value="'+current_url+'" />');
					$(this).submit(function(event){
						if ($(this).find('input[name=captcha_value]').val())
						{
							return true;
						}

						event.preventDefault();
						var self = this;

						$('#captcha_layer form')
						.submit(function(e){
							e.preventDefault();
							if(!$('#captcha_value').val()){
								$(this).find('input[type=text]').val('').focus();
								return false;
							}

							
							$(self).submit();
						});
						var params = new Array();
						params['captcha_action'] = 'getHtml';
						params['mid'] = current_mid;
						window.oldExecXml('', '', params, captchaXE.show,new Array('error','message','about_captcha','captcha_reload','captcha_play','cmd_input','cmd_cancel'));
					});
				}
			});

			
		

			if (!captchaXE.html) {
				
           
				captchaXE.exec; 

			

			
				

			


				

				// var $div = $('<div style="z-index:1000;position:absolute; width:310px;' + top_left + ' background:#fff; border:3px solid #ccc;">'+
				// 	'<form method="post" action="">'+
				// 		'<div style="position:relative; margin:25px 20px 15px 20px">'+
				// 			'<img src="about:blank" id="captcha_image" alt="CAPTCHA" width="240" height="50" style="display:block; width:240px; height:50px; border:1px solid #b0b0b0" />'+
				// 			'<button type="button" class="reload" title="" style="position:absolute; top:0; left:245px; width:25px; height:25px; padding:0; overflow:visible; border:1px solid #575757; background:#747474 url('+request_uri + 'addons/captcha/img/icon.gif) no-repeat center 5px;border-radius:3px;-moz-border-radius:3px;-webkit-border-radius:3px; cursor:pointer;box-shadow:0 0 3px #444 inset;-moz-box-shadow:0 0 3px #444 inset;-webkit-box-shadow:0 0 3px #444 inset;"></button>'+
				// 			'<button type="button" class="play" title="" style="position:absolute; top:27px; left:245px; width:25px; height:25px; padding:0; overflow:visible; border:1px solid #575757; background:#747474 url('+request_uri + 'addons/captcha/img/icon.gif) no-repeat center -20px;border-radius:3px;-moz-border-radius:3px;-webkit-border-radius:3px; cursor:pointer;box-shadow:0 0 3px #444 inset;-moz-box-shadow:0 0 3px #444 inset;-webkit-box-shadow:0 0 3px #444 inset;"></button>'+
				// 		'</div>'+
				// 		'<label id="captchaAbout" for="captcha" style="display:block; border-top:1px dashed #c5c5c5; padding:15px 0; margin:0 20px; font-size:12px; color:#5f5f5f;"></label>'+
				// 		'<input name="" type="text" id="secret_text" style="ime-mode:inactive;margin:0 20px; width:232px; border:1px solid #bdbdbd; padding:3px 4px; font-size:18px; font-weight:bold;" />'+
				// 		'<div style="margin:20px; border-top:1px dashed #c5c5c5; padding:15px 0 0 0; text-align:center">'+
				// 			'<button type="submit" style="height:31px; line-height:31px; padding:0 15px; margin:0 2px; font-size:12px; font-weight:bold; color:#fff; overflow:visible; border:1px solid #5c8a16; background:#6faa13;border-radius:3px;-moz-border-radius:3px;-webkit-border-radius:3px; cursor:pointer;box-shadow:0 0 3px #666 inset;-moz-box-shadow:0 0 3px #666 inset;-webkit-box-shadow:0 0 3px #666 inset;"></button>'+
				// 			'<button type="button" class="cancel" style="height:31px; line-height:31px; padding:0 15px; margin:0 2px; font-size:12px; font-weight:bold; color:#fff; overflow:visible; border:1px solid #575757; background:#747474;border-radius:3px;-moz-border-radius:3px;-webkit-border-radius:3px; cursor:pointer;box-shadow:0 0 3px #444 inset;-moz-box-shadow:0 0 3px #444 inset;-webkit-box-shadow:0 0 3px #444 inset;"></button>'+
				// 		'</div>'+
				// 	'</form>'+_object_ +
				// 	'</div>').appendTo(captchaXE);

				// $div.find('button.cancel')
				// 	.click(function(){ $('#captcha_layer').hide(); });

				
				// $div.find('button.reload')
				// 	.click(function(){
				// 		var params = new Array();
				// 		params['captcha_action'] = 'setCaptchaSession';
				// 		params['mid'] = current_mid;
				// 		window.oldExecXml('','',params, function() {
				// 			$("#captcha_image").attr("src", current_url.setQuery('captcha_action','captchaImage').setQuery('rnd', (new Date).getTime()));
							
				// 		});
				// 	});

			}
            return captchaXE;
		}

		$(window).ready(function(){
			if(!window.oldExecXml) {
				window.oldExecXml = window.exec_xml;
				window.exec_xml = xeCaptcha().exec;
			}
		});
	});
})(jQuery);
