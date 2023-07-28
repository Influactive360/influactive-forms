/* global grecaptcha, ajax_object */

/**
 * @param messageDiv
 * @param {Element} form
 * @param {string|Blob} recaptchaResponse
 */
function submitFormGlobal(messageDiv, form, recaptchaResponse) {
  const xhr = new XMLHttpRequest()
  const formData = new FormData(form)
  formData.append('action', 'send_email')

  if (recaptchaResponse) {
    formData.append('recaptcha_response', recaptchaResponse)
  }

  // eslint-disable-next-line camelcase
  xhr.open('POST', ajax_object.ajaxurl, true)

  xhr.onload = function xhrOnLoad() {
    if (xhr.status === 200) {
      const response = JSON.parse(xhr.responseText)
      if (response.data) {
        // Display the success message in the div
        messageDiv.textContent = response.data.message
        form.reset()
      } else {
        // Display the error message in the div
        messageDiv.textContent = response.data.message
      }
    } else {
      // Display the AJAX error message in the div
      messageDiv.textContent = 'An error occurred with the AJAX request'
    }
  }

  xhr.send(formData)
}

document.addEventListener('DOMContentLoaded', () => {
  const forms = document.querySelectorAll('.influactive-form')

  forms.forEach((form) => { // On boucle sur chaque formulaire
    if (form.parentElement.parentElement.parentElement.classList.contains('influactive-modal-form-brochure')) {
      return
    }

    form.addEventListener('submit', (event) => {
      event.preventDefault()

      const messageDiv = form.querySelector('.influactive-form-message')
      const recaptchaInput = form.querySelector('input[name="recaptcha_site_key"]')

      if (recaptchaInput && grecaptcha) {
        const recaptchaSiteKey = recaptchaInput.value
        grecaptcha.ready(() => {
          grecaptcha.execute(recaptchaSiteKey, { action: 'submit' }).then((token) => {
            submitFormGlobal(messageDiv, form, token)
            setTimeout(() => {
              messageDiv.textContent = ''
            }, 5000)
          })
        })
      } else {
        submitFormGlobal(messageDiv, form, null)
        setTimeout(() => {
          messageDiv.textContent = ''
        }, 5000)
      }
    })
  })
})
