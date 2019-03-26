$(document).ready(function() {
    $('.xe_content').addClass("tui-editor-contents");

    var editor = tui.Editor.factory({
        viewer: true,
        exts: ['chart']
    })

   
    $('.xe_content').each(function(){
        console.log(this.innerHTML);
        var converted = editor.convertor.toHTMLWithCodeHightlight(this.innerHTML);
        console.log(converted);
        this.innerHTML = converted;
    });
    
});

