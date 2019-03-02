<?php

class calendarView extends calendar {

    /**
    * @brief 초기화
    **/
    function init() {
       // 스킨 경로를 미리 template_path 라는 변수로 설정, 스킨이 존재하지 않으면 default로 변경
       $template_path = sprintf("%sskins/%s/",$this->module_path, $this->module_info->skin);
       if(!is_dir($template_path)||!$this->module_info->skin) {
           $this->module_info->skin = 'default';
           $template_path = sprintf("%sskins/%s/",$this->module_path, $this->module_info->skin);
       }
       $this->setTemplatePath($template_path);
   }
   
   /**
    * @brief 목록
    **/
   function dispCalendarContentList() {
       // 내용을 출력하는 스킨 파일 지정 skins/스킨폴더/view.html
       $this->setTemplateFile('list');
   }
}
?>