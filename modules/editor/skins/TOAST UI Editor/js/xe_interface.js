function editorGetIFrame(editor_sequence) {
    return editor_sequence;
}


function editorReplaceHTML(srl, content) {
    // src, href, url의 XE 상대경로를 http로 시작하는 full path로 변경
    var cont = "```component\n" + content + "\n```";     
    editorRelKeys[srl].pasteHTML(cont);
}
