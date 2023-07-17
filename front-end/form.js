/* global ajax_object */
/* global grecaptcha */

document.addEventListener('DOMContentLoaded', function() {
  const form = document.querySelector('.influactive-form');
  const messageDiv = document.querySelector('.influactive-form-message');

  if (form) {
    form.addEventListener('submit', function(event) {
      event.preventDefault();

      const recaptchaInput = form.querySelector('input[name="recaptcha_site_key"]');

      if (recaptchaInput && grecaptcha) {
        const recaptcha_site_key = recaptchaInput.value;
        grecaptcha.ready(function() {
          grecaptcha.execute(recaptcha_site_key, {action: 'submit'}).then(function(token) {
            submitForm(token);
          });
        });
      } else {
        submitForm();
      }

      function submitForm(recaptchaResponse) {
        const xhr = new XMLHttpRequest();
        const formData = new FormData(form);
        formData.append('action', 'send_email');

        if (recaptchaResponse) {
          formData.append('recaptcha_response', recaptchaResponse);
        }

        xhr.open('POST', ajax_object.ajaxurl, true);

        xhr.onload = function() {
          if (xhr.status === 200) {
            const response = JSON.parse(xhr.responseText);
            if (response.data && response.data.sent) {
              // Display the success message in the div
              messageDiv.textContent = response.data.message;
              form.reset();
              setTimeout(function() {
                messageDiv.textContent = '';
              }, 2000);
            } else {
              // Display the error message in the div
              messageDiv.textContent = response.data.message;
              console.log(response.data);
            }
          } else {
            // Display the AJAX error message in the div
            messageDiv.textContent = "An error occurred with the AJAX request";
          }
        };

        xhr.send(formData);
      }
    });
  }
});
