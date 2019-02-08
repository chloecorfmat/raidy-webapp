let ctcs = document.querySelectorAll('.copytoclipboard');

for(let ctc of ctcs){
    ctc.addEventListener('click', function (e) {
        e.preventDefault();
        let area = ctc.querySelector('.copytoclipboard--tocopy');
        area.select();
        document.execCommand('copy');

        iziToast.success({
            message: 'Le lien d\'invitation a été copié',
            position: 'bottomRight',
        });
    });
}