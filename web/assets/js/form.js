window.addEventListener('load', initForm)

function initForm (e) {
  var forms = Array.prototype.slice.call(document.getElementsByTagName('form'))
  Array.prototype.slice.call(forms).forEach(function (form) {
    var inputs = Array.prototype.slice.call(form.getElementsByTagName('input'))

    inputs.forEach(function (input) {
      if (input.type !== 'hidden' && input.value !== '') {
        console.log(input.parentNode)
        input.parentNode.classList.add('form--input-focused')
      }

      input.addEventListener('focusin', inputFocusIn)
      input.addEventListener('focusout', inputFocusOut)
    })
  })
}

function inputFocusIn (e) {
  if (!this.parentNode.classList.contains('form--input-focused')) {
    this.parentNode.classList.add('form--input-focused')
  }
}

function inputFocusOut (e) {
  if (this.value === '' && this.parentNode.classList.contains('form--input-focused')) {
    this.parentNode.classList.remove('form--input-focused')
  }
}
