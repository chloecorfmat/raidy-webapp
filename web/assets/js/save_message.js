window.addEventListener('load', message);

let quill;

function message(e) {

    if (document.getElementsByClassName('container-messages').length !== 0) {
        quill = new Quill('#editmessage', {
            theme: 'snow'
        });

        let btns = document.getElementsByClassName('btn--edit-message');

        for (let btn of btns) {
            btn.addEventListener('click', editMessage);
        }
    }
}

function editMessage(e) {
    let content = this.parentNode.parentNode.querySelector('.message-content').innerHTML;
    let id = this.dataset.messageId;
    let raidid = this.dataset.raidid;
    quill.root.innerHTML = content.trim();

    document.getElementById('btn--edit-message').addEventListener('click', function() {
        let xhr_object = new XMLHttpRequest();

        xhr_object.onreadystatechange = function() {
            if (xhr_object.readyState === 4 && xhr_object.status === 200) {
                var response = JSON.parse(xhr_object.response);
                var message = document.querySelector(".message[data-id='" + response.message + "']");
                message.querySelector('.message-content').innerHTML = response.content;

                quill.root.innerHTML = '';

                iziToast.success({
                    message: 'Le message a bien été modifié.',
                    position: 'bottomRight',
                });

                MicroModal.close('edit-message');
            } else if (xhr_object.readyState === 4) {
                iziToast.error({
                    message: 'Un problème est survenu, veuillez réessayer plus tard.',
                    position: 'bottomRight',
                });
            }
        }

        xhr_object.open('PATCH', base_url + '/organizer/raid/' + raidid + '/message/' + id + '/edit', true);
        xhr_object.setRequestHeader('Content-Type', 'application/json');
        xhr_object.send(JSON.stringify({content: quill.root.innerHTML.trim()}));
    });
}
