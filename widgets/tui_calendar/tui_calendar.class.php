<?php
/* Copyright (C) NAVER <http://www.navercorp.com> */
/**
 * @class tui_calendar
 * @author NAVER (developers@xpressengine.com)
 * @version 0.1
 * @brief Widget to display log-in form
 *
 * $Pre-configured by using $logged_info
 */
class tui_calendar extends WidgetHandler
{
	/**
	 * @brief Widget execution
	 * Get extra_vars declared in ./widgets/widget/conf/info.xml as arguments
	 * After generating the result, do not print but return it.
	 */
	function proc($args)
	{
		// 템플릿의 스킨 경로를 지정 (skin, colorset에 따른 값을 설정)
		$tpl_path = sprintf('%sskins/%s', $this->widget_path, $args->skin);
		Context::set('colorset', $args->colorset);

		// 템플릿 파일명
		$tpl_file = 'calendar';
		// 템플릿 컴파일
		$oTemplate = &TemplateHandler::getInstance();
		return $oTemplate->compile($tpl_path, $tpl_file);

	}
}
/* End of file tui_calendar.class.php */
/* Location: ./widgets/tui_calendar/tui_calendar.class.php */
