var naverImgCaptcha = {
    hookedDelegateArgs: null,
    $html             : null,

    bindAllForm: function () {
		$('form').each(function () {
			var $forms = $(this);

			$forms.each(function () {
				var $form = $(this);

				if ($form.hasOnSubmitAndOnProcFilter($form)) {
					if ($form.isRequrieSubmitHook($form)) {
						$form.submit(naverImgCaptcha.submitEvent);
					}
				}
			});
		});
	},

	submitEvent: function (e) {
		e.preventDefault();

		alert('지원안함');
		return false;
	},


    isRequiredCaptcha: function (act) {
        return jQuery.inArray(act, captchaTargetAct) > -1;
    }, 
    
    exec_xml: function(module, act, params, delegate, responseTags, delegateArgs, formObject) {
        if (naverImgCaptcha.isRequiredCaptcha(act)) {
            naverImgCaptcha.hookedDelegateArgs = {
                'module'           : module,
				'act'              : act,
				'params'           : params,
				'callback_func'    : delegate,
				'response_tags'    : responseTags,
				'callback_func_arg': delegateArgs,
				'fo_obj'           : naverImgCaptcha
            };

            params = {
				'captcha_action': 'getHtml',
				'mid': current_mid
            };
            
            if (!naverImgCaptcha.$html)
            {               
                window.oldExecXml(module, act, params, naverImgCaptcha.onRecvHtml, ['view', 'key'], naverImgCaptcha.hookedDelegateArgs, formObject);
            }
        } else {
            window.oldExecXml(module, act, params, delegate, responseTags, naverImgCaptcha.hookedDelegateArgs, formObject);
        }

        return true;
    },

    onRecvHtml: function (returnObject, responseTags, delegateArgs) {
       
        naverImgCaptcha.$html = $(returnObject.view);
        var $html             = naverImgCaptcha.$html;
        $(document.body).append($html);      
        $('#captcha_key').attr('value', returnObject.key);
        $("#captcha_image").attr("src", current_url.setQuery('captcha_action','captchaImage').setQuery('rnd', (new Date).getTime()));
        $(returnObject.view).append('<input type="hidden" name="error_return_url" value="'+current_url+'" />');
    },

    submit: function () {
		var args = naverImgCaptcha.hookedDelegateArgs;

		naverImgCaptcha.$html.hide();
		window.oldExecXml(args.module, args.act, args.params, args.callback_func, args.response_tags, args.callback_func_arg, args.fo_obj);

		naverImgCaptcha.hookedDelegateArgs = null;
	}
}


function onLoadGoogleReCaptcha() {
	
	naverImgCaptcha.bindAllForm();	
	naverImgCaptcha.ready = true;
}

jQuery(function ($) {	
    jQuery.fn.extend({
		hasOnSubmitAndOnProcFilter: function () {
			if (this.length > 1)
				throw "Only one element can be selected.";

			var $f = $(this);

			return !$f.attr('onsubmit') || $f.attr('onsubmit').indexOf('procFilter') < 0;
		},

		isRequrieSubmitHook: function () {
			if (this.length > 1)
				throw "Only one element can be selected.";

			var $f = $(this);

			var act      = $f.find('input[name=act]').val();
			var onsubmit = $f.attr('onsubmit');

			for (var k in captchaTargetAct) {
				var captchaTargetAct = captchaTargetAct[k];

				if ((!onsubmit || onsubmit.indexOf(captchaTargetAct) < 0) && act.length > 0) {
					return captchaTargetAct == act;
				}
			}

			return false;
		}
    });
    
    captchaTargetAct.push("IS");

    if (!window.oldExecXml) {
		window.oldExecXml = window.exec_xml;
		window.exec_xml   = naverImgCaptcha.exec_xml;
	}
});