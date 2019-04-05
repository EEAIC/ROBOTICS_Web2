<?php
  
    if(!defined("__XE__")) exit();

    if(!class_exists('NaverCaptcha', false)) 
    {

        class NaverCaptcha
        {
            const CAPTCHA_AUTHED = 'google_captcha_authed';
            
            const CLIENT_ID = $this->addon_info->client_id;
            const CLIENT_SECRET = $this->addon_info->client_secret;

            var $client_id = "UGWzYGR_bPS1Bnu0W9MM";
            var $client_secret = "zw76lllcBu";
            var $target_acts = NULL;
            var $url;
            var $is_post = false;
            var $headers = array();

            var $addon_info;

            private $key;
            private $addon_path;
            private $html;

            function setInfo(&$addon_info)
            {
                $this->addon_info = $addon_info;
            }
        

            function setHeaders()
            {
                $this->headers[] = "X-Naver-Client-Id: ".self::CLIENT_ID;
                $this->headers[] = "X-Naver-Client-Secret: ".self::CLIENT_SECRET;
            }
           

            public function setPath($addon_path) { $this->addon_path = $addon_path; }

            function loadHtml() 
            {
                if (!$this->html)
                    $this->html = TemplateHandler::getInstance()->compile($this->addon_path . '/skin', 'view');
        
                return $this->html;
            }
      

            function before_module_init(&$ModuleHandler)
            {
                $logged_info = Context::get('logged_info');
                if($logged_info->is_admin == 'Y' || $logged_info->is_site_admin)
                {
                    return false;
                }

                if($this->addon_info->target != 'all' && Context::get('is_logged'))
                {
                    return false;
                }

                if($_SESSION['XE_VALIDATOR_ERROR'] == -1)
                {
                    $_SESSION[self::CAPTCHA_AUTHED] = false;
                }
                if($_SESSION[self::CAPTCHA_AUTHED])
                {
                    return false;
                }
               
                $
                $type = Context::get('captchaType');
                $value = Context:: get('captcha_value');
                $this->target_acts = array('procBoardInsertDocument', 'procBoardInsertComment', 'procIssuetrackerInsertIssue', 'procIssuetrackerInsertHistory', 'procTextyleInsertComment');
                
                if(Context::get('captcha_action') != 'captchaImage'){
                    if(Context::getRequestMethod() != 'XMLRPC' && Context::getRequestMethod() !== 'JSON')
                    {
                    
                        
                        if($type == 'image')
                        {   
                            if(!$this->compareCaptcha($_SESSION['captcha_keyword'], $value, '1'))
                            {
                                Context::loadLang(_XE_PATH_ . 'addons/naver_imgCaptcha/lang');
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
                }

             


               

                // compare session when calling actions such as writing a post or a comment on the board/issue tracker module
                if(in_array(Context::get('act'), $this->target_acts) && !$_SESSION[self::CAPTCHA_AUTHED])
                {
                    
                    Context::loadLang(_XE_PATH_ . 'addons/naver_imgCaptcha/lang');
                    $ModuleHandler->error = "captcha_denied";
                }

                return true;
            }

              

            function before_module_proc()
            {                
                if($this->addon_info->act_type == 'everytime' && $_SESSION[self::CAPTCHA_AUTHED])
                {
                    unset($_SESSION[self::CAPTCHA_AUTHED]);
                }
                
            }

            function after_module_proc($moduleObject) { }

            function before_module_init_getHtml() {
                if ($_SESSION[self::CAPTCHA_AUTHED]) {
                    return false;
                }
        
                // $this->loadLang();
        
           
                $this->getCaptchaKey();
                // $this->getCaptchaImage($_SESSION['captcha_keyword']);

                printf(file_get_contents($this->addon_path . '/tpl/response.view.xml'), $this->loadHtml(), $_SESSION['captcha_keyword']);
                Context::close();               
                exit();
            }

            function before_module_init_captchaImage()
            {
                if($_SESSION[self::CAPTCHA_AUTHED])
                {
                    return false;
                }

                // if(Context::get('renew'))
                // {
                //     $this->createKeyword();
                // }
    
                $keyword = $_SESSION['captcha_keyword'];
                
                $im = $this->getCaptchaImage($keyword);
    
                echo $im;    
                Context::close();
                exit();
            }

            function before_module_init_captchaCompare()
            {                        

                if(!$this->compareCaptcha($_SESSION['captcha_keyword'], Context:: get('captcha_value'),'2'))
                {
                    // $this->getCaptchaKey();
                    print("<response>\r\n<error>0</error>\r\n<result>0</result>\r\n<message></message>\r\n</response>");
                    Context::close();
                    exit();
                    
                } 
               
    
                header("Content-Type: text/xml; charset=UTF-8");
                header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
                header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
                header("Cache-Control: no-store, no-cache, must-revalidate");
                header("Cache-Control: post-check=0, pre-check=0", false);
                header("Pragma: no-cache");
                print("<response>\r\n<error>0</error>\r\n<result>1</result>\r\n<message>success</message>\r\n</response>");
    
                Context::close();
                exit();
            }

           


            function curlInit($url) {
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_POST, $this->is_post);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_HTTPHEADER, $this->headers);
                curl_setopt($ch, CURLOPT_REFERER, $url);
                
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
                    $_SESSION['captcha_keyword'] = json_decode($response, true)['key'];
                } else {
                    echo "Error 내용:".$response;
                }
            }

            function getCaptchaImage() {
               
                $flag = 1;
                while ($flag) {
                    $key = $_SESSION['captcha_keyword'];
                    $url = "https://openapi.naver.com/v1/captcha/ncaptcha.bin?key=".$key;
                    $ch = $this->curlInit($url);
                    $response = curl_exec ($ch);
                    $status_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                    curl_close ($ch);
               


                    if($status_code == 200) {
                        
                        //echo $response;
                        // $fp = fopen("captcha.jpg", "w+");
                        // fwrite($fp, $response);
                        // fclose($fp);
                        // imagepng($response);
                        // imagedestroy($response);
                        // echo "<img src='captcha.jpg'>";
                        $flag = 0;
                        return $response;
                    } else {                      
                        $errResponse = json_decode($response, true);                                 
                        if ($errResponse['errorCode'] == 'CT001') {
                            $this->getCaptchaKey();
                        } else {
                            $flag = 0;
                        }
                    } 
                }               
            }

            function compareCaptcha($key, $value, $w) {     
                $key = $_SESSION['captcha_keyword'];
                $value = Context:: get('captcha_value');       
                if(!in_array(Context::get('act'), $this->target_acts)) return true;

                if($_SESSION[self::CAPTCHA_AUTHED])
                {
                    return true;
                }

                $code = "1";
                $url = "https://openapi.naver.com/v1/captcha/nkey?code=".$code."&key=".$key."&value=".$value;
                $ch = $this->curlInit($url);
                $response = curl_exec ($ch);
                $status_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                               
                curl_close ($ch);

                if($status_code == 200) 
                {           
                    $result = json_decode($response, true)['result'];
                    if ($result)
                    {
                        $_SESSION[self::CAPTCHA_AUTHED] = true;
                        return true; 
                    }
                    
                        
                    unset($_SESSION[self::CAPTCHA_AUTHED]);
                    return false;
                    
                } else {
                    $errResponse = json_decode($response, true);                                 
                    if ($errResponse['errorCode'] == 'CT001' || $errResponse['errorCode'] == 'CT002' ) {
                        $this->getCaptchaKey();
                        return false;
                    }

                    echo "Error 내용:".$response;
                }
            }
        }

        $GLOBALS['__NaverCaptcha__'] = new NaverCaptcha;
        $GLOBALS['__NaverCaptcha__']->setInfo($addon_info);
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
