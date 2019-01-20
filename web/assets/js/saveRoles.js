window.addEventListener('load', saveRoles);

function saveRoles() {
    let btn = document.getElementById("btn-save-roles");

    // To apply only on concerned page.
    if (btn !== null) {
        btn.addEventListener('click', function() {
            let xhr_object = new XMLHttpRequest();
            xhr_object.open('PATCH', '/admin/users/organizer-roles', true);
            xhr_object.setRequestHeader('Content-Type', 'application/json');

            let data = [];

            let checkboxes = document.getElementsByClassName('js-has-role-organizer');

            Array.from(checkboxes).forEach(function (el) {
                if (el.checked) {
                    data.push(el.getAttribute('id').replace('user-', ''));

                    var list = el.parentElement.previousElementSibling.firstElementChild;
                    if (!list.innerHTML.includes('<li>ROLE_ORGANIZER</li>')) {
                        let li = document.createElement('li');
                        let text = document.createTextNode('ROLE_ORGANIZER');

                        li.appendChild(text);
                        list.appendChild(li);
                    }

                } else {
                    var list = el.parentElement.previousElementSibling.firstElementChild;

                    if (list.innerHTML.includes('<li>ROLE_ORGANIZER</li>')) {
                        let html = list.innerHTML.replace('<li>ROLE_ORGANIZER</li>', '');
                        list.innerHTML = html;
                    }
                }

            });

            xhr_object.send(JSON.stringify({data: data}));

            iziToast.success({
                message: 'Les modifications ont bien été enregistrées.',
                position: 'bottomRight',

            });
        });
    }
}
