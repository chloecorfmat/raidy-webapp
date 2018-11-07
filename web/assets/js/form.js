window.addEventListener('load', initForm);

function initForm (e) {
  var exceptedInputs = ['hidden', 'date', 'file', 'checkbox'];
  var forms = Array.prototype.slice.call(document.getElementsByTagName('form'));
  Array.prototype.slice.call(forms).forEach(function (form) {
    var inputs = Array.prototype.slice.call(form.getElementsByTagName('input'));

    inputs.forEach(function (input) {
      if (exceptedInputs.indexOf(input.type) === -1) {
        if (input.value !== '') {
          input.parentNode.classList.add('form--input-focused');
        }

        input.addEventListener('focusin', inputFocusIn);
        input.addEventListener('focusout', inputFocusOut);
        input.addEventListener('change', inputFocusIn);
        input.addEventListener('change', inputFocusOut);
      }
    })

    var fileInput = form.querySelector(".form-input--image");
    fileInput.addEventListener("change", imagePreview);

  })
}

function inputFocusIn (e) {
  if (!this.parentNode.classList.contains('form--input-focused')) {
    this.parentNode.classList.add('form--input-focused');
  }
}

function inputFocusOut (e) {
  if (this.value === '' && this.parentNode.classList.contains('form--input-focused')) {
    this.parentNode.classList.remove('form--input-focused');
  }
}

function imagePreview(e) {
    var input = this;
    var image = this.files[0];

    var reader = new FileReader();
    reader.addEventListener("load", function () {
        var img = input.parentNode.querySelector('img');

        if(img != null){
            img.src = reader.result;
        } else {
            /*Build image preview markup*/
            var preview = document.createElement('div');
            preview.setAttribute("class", "form--item-file-previews");

            var image = document.createElement('img');
            image.setAttribute("class", "form--item-file-preview");

            image.src = reader.result;

            preview.appendChild(image);
            input.parentNode.appendChild(preview);
        }
    }, false);
    reader.readAsDataURL(image);
}
