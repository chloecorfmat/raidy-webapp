window.addEventListener('load', initTutorial);

function initTutorial(e) {
    if (document.getElementsByClassName('editor').length != 0) {
        MicroModal.init();

        if (tutorial) {
            MicroModal.show('tutorial_1');

            let xhr_object = new XMLHttpRequest();
            xhr_object.open('PATCH', '/organizer/checkTutorial', true);
            xhr_object.setRequestHeader('Content-Type', 'application/json');
            xhr_object.send();
            console.log('toto');
        }

        document.getElementById('editorTutorial').addEventListener('click', function () {
            MicroModal.show('tutorial_1');
        });

        document.querySelectorAll('.btn--editor-tutorial').forEach(function(btn) {
            btn.addEventListener('click', function() {
                MicroModal.close('tutorial_' + this.dataset.current);
                MicroModal.show('tutorial_' + this.dataset.id);
            })
        });
    }
}