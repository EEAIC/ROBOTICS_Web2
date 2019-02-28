function editorStart_xe(editor_sequence, primary_key, content_key, editor_height, colorset, content_style, content_font, content_font_size) {


	var editor = new tui.Editor({
		el: document.querySelector('#tuieditor_instance_' + editor_sequence),
		initialEditType: 'markdown',
		previewStyle: 'vertical',
		height: '300px',
		

	});

	if(jQuery("input[name=" + content_key +"]").size()>0){
		saved_content=jQuery("input[name=" + content_key +"]").val();
		editor.setHtml(saved_content, 0);
	}

	$("#fo_write").on("change", function() {
		var eValue = editor.getMarkdown();
		console.log(eValue)
		$('input[name=content]').val(eValue);
	});

}











