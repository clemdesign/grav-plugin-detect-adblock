(function($){
    $(function(){
        $('body').on('grav-editor-ready', function() {
            var Instance = Grav.default.Forms.Fields.EditorField.Instance;
            Instance.addButton({
                detectAdblock: {
                    identifier: 'detectAdblock',
                    title: 'Detect AdBlock: Block the selected content',
                    label: '<i class="fa fa-fw fa-ban"></i>',
                    modes: ['gfm', 'markdown'],
                    action: function(_ref) {
                        var codemirror = _ref.codemirror, button = _ref.button;
                        button.on('click.editor.detectAdblock',function() {
                            var textStart = '\n---dab---\n\n';
                            var textEnd   = '\n---/dab---\n';

                            //Add text to the editor
                            var pos     = codemirror.getDoc().getCursor(true);
                            var posend  = codemirror.getDoc().getCursor(false);
                            var lines   = '';
                            var nbLines = 0;

                            for (var i=pos.line; i<(posend.line+1);i++) {
                                lines += codemirror.getLine(i) + '\n';
                                nbLines++;
                            }
                            codemirror.replaceRange(textStart+lines+textEnd, { line: pos.line, ch: 0 }, { line: posend.line, ch: posend.ch });

                            nbLines += 6; //Added by tags (textStart & textEnd)

                            codemirror.setCursor({ line: posend.line+nbLines, ch: codemirror.getLine(posend.line+nbLines).length });
                            codemirror.focus();
                        });
                    }
                }
            });
        });
    });
})(jQuery);