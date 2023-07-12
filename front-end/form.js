/* global ajax_object */

document.addEventListener('DOMContentLoaded', function() {
  const form = document.querySelector('.influactive-form');
  const messageDiv = document.querySelector('.influactive-form-message');

  form.addEventListener('submit', function(event) {
    event.preventDefault();

    const xhr = new XMLHttpRequest();
    const formData = new FormData(form);
    formData.append('action', 'send_email');

    xhr.open('POST', ajax_object.ajaxurl, true);

    xhr.onload = function() {
      if (xhr.status === 200) {
        const response = JSON.parse(xhr.responseText);
        if (response.data && response.data.sent) {
          // Display the success message in the div
          messageDiv.textContent = 'Email envoyé avec succès';
          form.reset();
          // attendre 2 secondes et cacher le message
          setTimeout(function() {
            messageDiv.textContent = '';
          }, 2000);
        } else {
          // Display the error message in the div
          messageDiv.textContent = 'Une erreur s\'est produite lors de l\'envoi de l\'email';
          console.log(response, xhr)
        }
      } else {
        // Display the AJAX error message in the div
        messageDiv.textContent = 'Une erreur s\'est produite avec la requête AJAX';
      }
    };

    xhr.send(formData);
  });
});