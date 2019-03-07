$(document ).ready(function() {
    console.log($("#tui-image-editor"));
    console.log("test");
    // Image editor
    var imageEditor = new tui.ImageEditor('#tui-image-editor', {
        includeUI: {
            theme: Theme,
            initMenu: 'filter',
            menuBarPosition: 'bottom'
        },
        cssMaxWidth: 700,
        cssMaxHeight: 1000,
        selectionStyle: {
            cornerSize: 20,
            rotatingPointOffset: 70
        }
    });

    window.onresize = function() {
        imageEditor.ui.resizeEditor();
        
    }
});