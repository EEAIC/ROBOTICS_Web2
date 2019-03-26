$(document).ready(function() {
    $('.xe_content').addClass("tui-editor-contents");

    var editor = tui.Editor.factory({
        viewer: true,
        exts: ['chart', 'uml', 'table', 'youtube']
    })

   
    $('.xe_content').each(function(){
        console.log($(this).text());
        var converted = editor.convertor.toHTMLWithCodeHightlight($(this).text());
        console.log(converted);
        $(this).html(converted);
    });
    
});

