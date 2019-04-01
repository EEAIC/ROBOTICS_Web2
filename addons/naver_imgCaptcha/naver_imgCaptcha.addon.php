<?php
  
    if(!defined("__XE__")) exit();

    if(!class_exists('NaverCaptcha', false)) 
    {

        class NaverCaptcha
        {
            var $client_id = "UGWzYGR_bPS1Bnu0W9MM";
            var $client_secret = "zw76lllcBu";
            var $target_acts = NULL;
            var $url;
            var $is_post = false;
            var $headers = array();
            private $key;

            private $addon_path;
            private $html;


        

            function setHeaders()
            {
                $this->headers[] = "X-Naver-Client-Id: ".$this->client_id;
                $this->headers[] = "X-Naver-Client-Secret: ".$this->client_secret;
            }

            function context() { return Context::getInstance(); }

            public function setPath($addon_path) { $this->addon_path = $addon_path; }

            function loadHtml() 
            {
                if (!$this->html)
                    $this->html = TemplateHandler::getInstance()->compile($this->addon_path . '/skin', 'view');
        
                return $this->html;
            }
        

            

            function before_module_proc()
            {

            }

            function before_module_init(&$ModuleHandler)
            {
                $logged_info = Context::get('logged_info');
                if($logged_info->is_admin == 'Y' || $logged_info->is_site_admin)
                {
                    return false;
                }

                if($_SESSION['captcha_authed'])
                {
                    return false;
                }

                $type = Context::get('captchaType');

                $this->target_acts = array('procBoardInsertDocument', 'procBoardInsertComment', 'procIssuetrackerInsertIssue', 'procIssuetrackerInsertHistory', 'procTextyleInsertComment');

                if(Context::getRequestMethod() != 'XMLRPC' && Context::getRequestMethod() !== 'JSON')
                {
                    if($type == 'image')
                    {
                        if(!$this->compareCaptcha())
                        {
                            Context::loadLang(_XE_PATH_ . 'addons/captcha/lang');
                            $_SESSION['XE_VALIDATOR_ERROR'] = -1;
                            $_SESSION['XE_VALIDATOR_MESSAGE'] = Context::getLang('captcha_denied');
                            $_SESSION['XE_VALIDATOR_MESSAGE_TYPE'] = 'error';
                            $_SESSION['XE_VALIDATOR_RETURN_URL'] = Context::get('error_return_url');
                            $ModuleHandler->_setInputValueToSession();
                        }
                    }
                    else
                    {
                        Context::addHtmlHeader('<script>
                            if(!captchaTargetAct) {var captchaTargetAct = [];}
                            captchaTargetAct.push("' . implode('","', $this->target_acts) . '");
                            </script>');

                        Context::loadFile(array('./addons/naver_imgCaptcha/naver_imgCaptcha.js', 'body', '', null), true);
                    }
                }

                // $this->getCaptchaKey();
                // $this->getCaptchaImage();
                // $this->compareCaptcha($this->key, "1234");

                // compare session when calling actions such as writing a post or a comment on the board/issue tracker module
                if(!$_SESSION['captcha_authed'] && in_array(Context::get('act'), $this->target_acts))
                {
                    Context::loadLang(_XE_PATH_ . 'addons/captcha/lang');
                    $ModuleHandler->error = "captcha_denied";
                }

                return true;
            }

            function before_module_init_getHtml() {
                if ($_SESSION['captcha_authed']) {
                    return false;
                }
        
                // $this->loadLang();
        
                header("Content-Type: text/xml; charset=UTF-8");
                header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
                header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
                header("Cache-Control: no-store, no-cache, must-revalidate");
                header("Cache-Control: post-check=0, pre-check=0", false);
                header("Pragma: no-cache");
        
                printf(file_get_contents($this->addon_path . '/tpl/response.view.xml'), $this->loadHtml());
        
                $this->context()->close();
                exit();
            }


            function curlInit($url) {
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_POST, $this->is_post);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_HTTPHEADER, $this->headers);
                return $ch;
            }

            function getCaptchaKey() {
                $code = "0";
                $url = "https://openapi.naver.com/v1/captcha/nkey?code=".$code;
                $ch = $this->curlInit($url);
                $response = curl_exec ($ch);
                $status_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                curl_close ($ch);                

                if($status_code == 200) {
                    $this->key = json_decode($response, true)['key'];
                } else {
                    echo "Error 내용:".$response;
                }
            }

            function getCaptchaImage() {
                $url = "https://openapi.naver.com/v1/captcha/ncaptcha.bin?key=".$this->key;
                $ch = $this->curlInit($url);
                $response = curl_exec ($ch);
                $status_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                curl_close ($ch);
                
                if($status_code == 200) {
                    //echo $response;
                    $fp = fopen("captcha.jpg", "w+");
                    fwrite($fp, $response);
                    fclose($fp);
                    // imagepng($response);
                    // imagedestroy($response);
                    echo "<img src='captcha.jpg'>";
                } else {
                    echo "Error 내용:".$response;
                }                
            }

            function compareCaptcha($key, $value) {
                $code = "1";
                $url = "https://openapi.naver.com/v1/captcha/nkey?code=".$code."&key=".$key."&value=".$value;
                $ch = $this->curlInit($url);
                $response = curl_exec ($ch);
                $status_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                echo "status_code:".$status_code."<br>";
                curl_close ($ch);

                if($status_code == 200) {
                    echo $response;
                } else {
                    echo "Error 내용:".$response;
                }
            }
        }

        $GLOBALS['__NaverCaptcha__'] = new NaverCaptcha;
        $GLOBALS['__NaverCaptcha__']->setHeaders();
        $GLOBALS['__NaverCaptcha__']->setPath(_XE_PATH_.'addons/naver_imgCaptcha');
        Context::set('oNaverCaptcha', $GLOBALS['__NaverCaptcha__']);
    }

    $oAddonNaverCaptcha = &$GLOBALS['__NaverCaptcha__'];
   
    if(method_exists($oAddonNaverCaptcha, $called_position))
    {
        if(!call_user_func_array(array(&$oAddonNaverCaptcha, $called_position), array(&$this)))
        {
            return false;
        }       
        
    }

    $addon_act = Context::get('captcha_action');
    if($addon_act && method_exists($oAddonNaverCaptcha, $called_position . '_' . $addon_act))
    {
        if(!call_user_func_array(array(&$oAddonNaverCaptcha, $called_position . '_' . $addon_act), array(&$this)))
        {
            return false;
        }
    }

/* End of file captcha.addon.php */
/* Location: ./addons/captcha/captcha.addon.php */
