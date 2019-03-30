(function(root, factory) {
    if (typeof define === 'function' && define.amd) {
      define(['tui-editor'], factory);
    } else if (typeof exports === 'object') {
      factory(require('tui-editor'));
    } else {
      factory(root['tui']['Editor']);
    }
  })(this, function(Editor) {
    Editor.defineExtension('component', function() {
      Editor.codeBlockManager.setReplacer('component', function(content) {
        var wrapperId = 'cpt' + Math.random().toString(36).substr(2, 10);
        setTimeout(renderComponent.bind(null, wrapperId, content), 0);
  
        return '<div id="' + wrapperId + '"></div>';
      });
    });
  
    function renderComponent(wrapperId, content) {
      var el = document.querySelector('#' + wrapperId);
      el.innerHTML = content;
      // MODIFIED By LIFOsitory
      $('[data-language="component"]').unwrap();
    }
  });