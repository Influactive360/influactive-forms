/* global Sortable */

document.addEventListener('DOMContentLoaded', function() {
  const container = document.getElementById("influactive_form_fields_container");
  let state = Array.from(document.querySelectorAll('.influactive_form_field')).map(field => field.dataset.key);

  new Sortable(container, {
    handle: '.influactive_form_field',
    animation: 150,
    onEnd: function(evt) {
      const itemKey = evt.item.dataset.key;
      const oldIndex = evt.oldIndex;
      const newIndex = evt.newIndex;

      state.splice(oldIndex, 1);  // remove itemKey from old position
      state.splice(newIndex, 0, itemKey);  // insert itemKey at new position
    },
  });

  new Sortable(container, {
    handle: '.influactive_form_field', // This class should be on the elements you want to be draggable
    animation: 150,
  });

  document.getElementById("add_field").addEventListener("click", addFieldHandler);

  Array.from(container.getElementsByClassName("influactive_form_field")).forEach(function(formField) {
    const fieldType = formField.querySelector(".field_type");

    if (fieldType.value === "select") {
      const addOptionElement = formField.querySelector(".add_option");
      const removeOptionElement = formField.querySelector(".remove_option");

      if (addOptionElement) {
        addOptionElement.addEventListener("click", addOptionHandler);
      }

      if (removeOptionElement) {
        removeOptionElement.addEventListener("click", removeOptionHandler);
      }
    }
    addSwapButtons(formField);
  });

  container.addEventListener("click", function(e) {
    if (e.target && e.target.classList.contains("remove_field")) {
      removeFieldHandler(e);
    }

    if (e.target && (e.target.classList.contains("swap_up") || e.target.classList.contains("swap_down"))) {
      swapFieldHandler(e);
    }
  });
});

function addSwapButtons(formField) {
  // Check if the swap buttons already exist
  let existingSwapButtons = formField.querySelector(".swap_buttons_container");

  if (existingSwapButtons) {
    // If they exist, remove them
    existingSwapButtons.remove();
  }

  // Create new swap buttons
  let swapButtonsElement = document.createElement('div');
  swapButtonsElement.className = 'swap_buttons_container';
  swapButtonsElement.innerHTML = "<a href='#' class='swap_up'>Swap up</a> | <a href='#' class='swap_down'>Swap down</a>";

  // Append the new swap buttons to the form field
  formField.appendChild(swapButtonsElement);
}

function swapFieldHandler(e) {
  e.preventDefault();
  const formField = e.target.closest(".influactive_form_field");
  const siblingField = e.target.classList.contains("swap_up") ? formField.previousElementSibling : formField.nextElementSibling;

  if (siblingField) {
    const formFieldClone = formField.cloneNode(true);
    siblingField.parentNode.insertBefore(formFieldClone, e.target.classList.contains("swap_up") ? siblingField : siblingField.nextSibling);
    formField.remove();

    // Detach the events from the original field and attach them to the cloned field
    attachEventsToField(formFieldClone);
  }
}

function attachEventsToField(formField) {
  const fieldType = formField.querySelector(".field_type");

  if (fieldType.value === "select") {
    const addOptionElement = formField.querySelector(".add_option");
    const removeOptionElement = formField.querySelector(".remove_option");

    if (addOptionElement) {
      addOptionElement.addEventListener("click", addOptionHandler);
    }

    if (removeOptionElement) {
      removeOptionElement.addEventListener("click", removeOptionHandler);
    }
  }

  addSwapButtons(formField);
}

function addFieldHandler(e) {
  e.preventDefault();

  let fieldElement = createFieldElement();
  let container = document.getElementById("influactive_form_fields_container");
  container.appendChild(fieldElement);

  const fieldType = fieldElement.querySelector(".field_type");
  fieldType.addEventListener("change", fieldTypeChangeHandler(fieldElement));

  const addOptionBtn = fieldElement.querySelector(".add_option");
  if (addOptionBtn) {
    addOptionBtn.addEventListener("click", addOptionHandler);
  }
}

function fieldTypeChangeHandler(fieldElement) {
  return function(event) {
    let fieldValue = event.target.value;

    if (fieldValue === "select") {
      const addOptionElement = document.createElement('p')
      addOptionElement.innerHTML = "<a href='#' class='add_option'>Add option</a>";
      const options_container = fieldElement.querySelector(".options_container");
      options_container.parentNode.insertBefore(addOptionElement, options_container.nextSibling);

      addOptionElement.addEventListener("click", addOptionHandler);
    }
  }
}

function addOptionHandler(e) {
  e.preventDefault();
  const optionsContainer = e.target.parentElement.previousElementSibling;
  const optionElement = createOptionElement();

  // Here, add event listener to the "Remove option" link
  const removeOptionElement = optionElement.querySelector('.remove_option');
  if (removeOptionElement) {
    removeOptionElement.addEventListener("click", removeOptionHandler);
  }

  optionsContainer.appendChild(optionElement);

  let influactive_form_fields_name = optionElement.closest(".influactive_form_field").querySelector("input[name*='influactive_form_fields_name']").value;
  optionElement.querySelector("input[name*='influactive_form_fields_option']").name = "influactive_form_fields_option[" + influactive_form_fields_name + "][]";
}

function removeFieldHandler(e) {
  e.preventDefault();
  e.target.closest(".influactive_form_field").remove();
  recalculateFieldIndexes();
}

function removeOptionHandler(e) {
  e.preventDefault();
  e.target.closest(".option-field").remove();
}

function createFieldElement() {
  let fieldElement = document.createElement('div');
  fieldElement.className = "influactive_form_field";
  fieldElement.innerHTML =
    "<p><label>Type <select class='field_type' name='influactive_form_fields_type[]'>" +
    "<option value='text'>Text</option>" +
    "<option value='email'>Email</option>" +
    "<option value='number'>Number</option>" +
    "<option value='textarea'>Textarea</option>" +
    "<option value='select'>Select</option>" +
    "</select></label> " +
    "<label>Label <input type='text' name='influactive_form_fields_label[]'></label> " +
    "<label>Name <input type='text' name='influactive_form_fields_name[]'></label> " +
    "<a href='#' class='remove_field'>Remove</a> " +
    "</p>" +
    "<div class='options_container'></div>";
  return fieldElement;
}


function createOptionElement() {
  let optionElement = document.createElement('p');
  optionElement.className = "option-field";
  // use appendChild for optionElement to
  optionElement.innerHTML = "<label>Option <input type='text' name='influactive_form_fields_option[]'></label> <a href='#' class='remove_option'>Remove option</a>";

  return optionElement;
}

function recalculateFieldIndexes() {
  let container = document.getElementById("influactive_form_fields_container");
  Array.from(container.getElementsByClassName("influactive_form_field")).forEach(function(formField, index) {
    formField.querySelector(".field_type").name = "influactive_form_fields_type[" + index + "]";
    formField.querySelector(".influactive_form_fields_label").name = "influactive_form_fields_label[" + index + "]";
    formField.querySelector(".influactive_form_fields_name").name = "influactive_form_fields_name[" + index + "]";
    formField.querySelector(".influactive_form_fields_option").name = "influactive_form_fields_option[" + formField.querySelector(".influactive_form_fields_name").name + "][]";
  });
}
