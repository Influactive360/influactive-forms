import './form.scss'

/* global grecaptcha, ajaxObject */

/**
 * Submits a form using AJAX and displays the response message in a specified message div.
 *
 * @param {Element} messageDivParam - The message element where a response message displayed.
 * @param {Element} form - The form element to be submitted.
 * @param {string} recaptchaResponse - The response from the reCAPTCHA widget (optional).
 */
const submitFormGlobal = (messageDivParam, form, recaptchaResponse) => {
  let message
  const messageDiv = messageDivParam
  const xhr = new XMLHttpRequest()
  const formData = new FormData(form)
  formData.append('action', 'send_email')

  if (recaptchaResponse) {
    formData.append('recaptcha_response', recaptchaResponse)
  }

  xhr.open('POST', ajaxObject.ajaxurl, true)

  xhr.onload = function xhrOnLoad() {
    if (xhr.status === 200) {
      try {
        const response = JSON.parse(xhr.responseText)
        if (response.data) {
          message = response.data.message
          form.reset()
        } else {
          message = 'An error occurred while processing the response'
        }
      } catch (error) {
        message = 'An error occurred while parsing the response'
      }
      messageDiv.textContent = message
    } else {
      message = 'An error occurred with the AJAX request'
      messageDiv.textContent = message
    }
  }

  xhr.onerror = () => {
    message = 'An error occurred while making the AJAX request'
    messageDiv.textContent = message
  }

  xhr.send(formData)
}

document.addEventListener('DOMContentLoaded', () => {
  const forms = document.querySelectorAll('.influactive-form')

  forms.forEach((form) => {
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
