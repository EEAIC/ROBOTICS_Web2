<?php
/* Copyright (C) LIFOsitory */
/**
 * @class imageEditor
 * @author LIFOsitory
 * @brief widget to display imageEditor
 * @version 0.1
 */
class imageEditor extends WidgetHandler
{
	/**
	 * @brief Widget execution
	 *
	 * Get extra_vars declared in ./widgets/widget/conf/info.xml as arguments
	 * After generating the result, do not print but return it.
	 */

	function proc($args)
	{
        // Set a path of the template skin (values of skin, colorset settings)
        $tpl_path = sprintf('%sskins/%s', $this->widget_path, $args->skin);

        Context::set('widget_info', $args);

        $tpl_file = 'image_editor';
        // Compile a template
		$oTemplate = &TemplateHandler::getInstance();
		return $oTemplate->compile($tpl_path, $tpl_file);
	}
}
/* End of file imageEditor.class.php */
/* Location: ./widgets/imageEditor/imageEditor.class.php */
