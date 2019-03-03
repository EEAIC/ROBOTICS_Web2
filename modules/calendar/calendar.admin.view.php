<?php
class calendarAdminView extends calendar {

        /**
     * @brief 초기화
     **/
    function init() {

        // module_srl이 있으면 미리 체크하여 존재하는 모듈이면 module_info 세팅
        $module_srl = Context::get('module_srl');
        if(!$module_srl && $this->module_srl) {
            $module_srl = $this->module_srl;
            Context::set('module_srl', $module_srl);
        }

        // module model 객체 생성 
        $oModuleModel = &getModel('module');

        // module_srl이 넘어오면 해당 모듈의 정보를 미리 구해 놓음
        // 브라우져 타이틀, 관리자, 레이아웃 등 xe_modules table의 값과 정보
        if($module_srl) {
            $module_info = $oModuleModel->getModuleInfoByModuleSrl($module_srl);
            $this->module_info = $module_info;
            Context::set('module_info',$module_info);
        }

        // 관리자 템플릿 파일의 경로 설정 (tpl)
        $template_path = sprintf("%stpl/",$this->module_path);
        $this->setTemplatePath($template_path);
    }

    /**
     * @brief 관리자 목록
     **/
    function dispCalendarAdminList() {
        // 페이지 네비게시션을 위한 설정
        $page = Context::get('page');
        if(!$page) $page = 1;
        $args->page = $page;

        // calendar admin model 객체 생성
        $oCalendarAdminModel = &getAdminModel('calendar');
        // calendar module_srl 목록 가져옴
        $output = $oCalendarAdminModel->getCalendarAdminList($args);

        // 템플릿에 전해주기 위해 set함
        Context::set('calendar_list', $output->data);
        Context::set('page_navigation', $output->page_navigation);

        // exec_json 으로 전달
        $this->add('output', $output);
        $this->setMessage('success_registed');

        // 관리자 목록(mid) 보기 템플릿 지정(tpl/index.html)
        $this->setTemplateFile('list');
    }


}
?>