/* global Sortable */
/* global influactiveFormsTranslations */
/* global tinymce */
document.addEventListener('DOMContentLoaded', function() {
  const container = document.getElementById("influactive_form_fields_container");
  new Sortable(container, {
    animation: 150, // This class should be on the elements you want to be draggable
    handle: '.influactive_form_field',
    onChoose: () => {
      recalculateFieldIndexes();
    },
    onClone: () => {
      recalculateFieldIndexes();
    },
    onEnd: () => {
      recalculateFieldIndexes();
    },
    onMove: () => {
      recalculateFieldIndexes();
    },
    onStart: () => {
      recalculateFieldIndexes();
    },
  });
  document.getElementById("add_field").addEventListener("click", addFieldHandler);
  Array.from(container.getElementsByClassName("influactive_form_field")).forEach(function(formField) {
    const fieldType = formField.querySelector(".field_type");
    fieldType.addEventListener("change", fieldTypeChangeHandler(formField)); // Added this line
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
  });
  container.addEventListener("click", function(e) {
    if (e.target && e.target.classList.contains("remove_field")) {
      removeFieldHandler(e);
    }
  });
});

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
  recalculateFieldIndexes();
}

function fieldTypeChangeHandler(fieldElement) {
  recalculateFieldIndexes();
  return function(event) {
    let fieldValue = event.target.value;
    // Remove existing elements
    const oldLabelElement = fieldElement.querySelector(".influactive_form_fields_label");
    const oldNameElement = fieldElement.querySelector(".influactive_form_fields_name");
    const oldTextAreaElement = fieldElement.querySelector(".wysiwyg-editor"); // Added this line
    if (oldLabelElement && oldNameElement) {
      if (oldTextAreaElement && tinymce.get(oldTextAreaElement.id)) {
        tinymce.get(oldTextAreaElement.id).remove();
      }
      oldLabelElement.parentElement.remove();
      oldNameElement.parentElement.remove();
      if (oldTextAreaElement) {
        oldTextAreaElement.remove();
      }
    }
    if (fieldValue === "gdpr") {
      const gdprTextElement = document.createElement('label')
      gdprTextElement.innerHTML = "Text <input type='text' name='influactive_form_fields[" + fieldElement.querySelector(".influactive_form_fields_order").value + "][label]' class='influactive_form_fields_label' data-type='gdpr'>";
      const gdprNameElement = document.createElement('label')
      gdprNameElement.innerHTML = "<input type='hidden' name='influactive_form_fields[" + fieldElement.querySelector(".influactive_form_fields_order").value + "][name]' class='influactive_form_fields_name' value='gdpr'>";
      // Append these elements to fieldElement or any other container element
      fieldElement.appendChild(gdprTextElement);
      fieldElement.appendChild(gdprNameElement);
    }
    if (fieldValue === "free_text") {
      const textareaElement = document.createElement('textarea');
      textareaElement.name = 'influactive_form_fields[' + fieldElement.querySelector(".influactive_form_fields_order").value + '][label]';
      textareaElement.className = 'influactive_form_fields_label wysiwyg-editor'; // class 'wysiwyg-editor' is for TinyMCE selector
      textareaElement.rows = 10;
      // Append textarea to the field element
      fieldElement.appendChild(textareaElement);
      // Initialize TinyMCE
      setTimeout(function() {
        tinymce.init({
          selector: '.wysiwyg-editor',  // we use a class selector to select the new textarea
          height: 215,
          menubar: false,
          plugins: [
            'lists link image charmap',
            'fullscreen',
            'paste',
          ],
          toolbar: 'bold italic underline link unlink undo redo formatselect ' +
            'backcolor alignleft aligncenter ' +
            'alignright alignjustify bullist numlist outdent indent ' +
            'removeformat',
          content_style: 'body { font-family:Helvetica,Arial,sans-serif; font-size:14px }',
        });
      }, 0);
      const NameElement = document.createElement('label')
      NameElement.innerHTML = "<input type='hidden' name='influactive_form_fields[" + fieldElement.querySelector(".influactive_form_fields_order").value + "][name]' class='influactive_form_fields_name' value='free_text'>";
      fieldElement.appendChild(NameElement);
    }

    let labelElement = fieldElement.querySelector(".influactive_form_fields_label");
    let nameElement = fieldElement.querySelector(".influactive_form_fields_name");
    // If they don't exist, create and append them
    if (!labelElement && !nameElement) {
      const LabelElement = document.createElement('label')
      LabelElement.innerHTML = "Label <input type='text' name='influactive_form_fields[" + fieldElement.querySelector(".influactive_form_fields_order").value + "][label]' class='influactive_form_fields_label'>";
      const NameElement = document.createElement('label')
      NameElement.innerHTML = "Name <input type='text' name='influactive_form_fields[" + fieldElement.querySelector(".influactive_form_fields_order").value + "][name]' class='influactive_form_fields_name'>";
      // Append these elements to fieldElement or any other container element
      fieldElement.appendChild(LabelElement);
      fieldElement.appendChild(NameElement);
    }
    if (fieldValue === "select") {
      const addOptionElement = document.createElement('p')
      addOptionElement.innerHTML = "<a href='#' class='add_option'>Add option</a>";
      fieldElement.appendChild(addOptionElement);
      const optionsContainer = document.createElement('div')
      optionsContainer.classList.add("options_container");
      fieldElement.appendChild(optionsContainer);
      addOptionElement.addEventListener("click", addOptionHandler);
    } else {
      const addOptionElement = fieldElement.querySelector(".add_option");
      const optionsContainer = fieldElement.querySelector(".options_container");
      if (addOptionElement) {
        addOptionElement.remove();
      }
      if (optionsContainer) {
        optionsContainer.remove();
      }
    }
  }
}

function addOptionHandler(e) {
  e.preventDefault();
  const optionsContainer = e.target.parentElement.previousElementSibling;
  const optionElement = createOptionElement();
  const removeOptionElement = optionElement.querySelector('.remove_option');
  if (removeOptionElement) {
    removeOptionElement.addEventListener("click", removeOptionHandler);
  }
  optionsContainer.appendChild(optionElement);
  let influactive_form_fields_order = optionElement.closest(".influactive_form_field")
    .querySelector(".influactive_form_fields_order").value;
  let influactive_form_options_order = 0;
  if (document.getElementsByClassName("option-field").length > 0) {
    influactive_form_options_order = document.getElementsByClassName("option-field").length;
  }
  optionElement.querySelector(".option-label").name = "influactive_form_fields[" + influactive_form_fields_order + "][options][" + influactive_form_options_order + "][label]";
  optionElement.querySelector(".option-value").name = "influactive_form_fields[" + influactive_form_fields_order + "][options][" + influactive_form_options_order + "][value]";
  recalculateFieldIndexes();
}

function removeFieldHandler(e) {
  e.preventDefault();
  e.target.closest(".influactive_form_field").remove();
  recalculateFieldIndexes();
}

function removeOptionHandler(e) {
  e.preventDefault();
  e.target.closest(".option-field").remove();
  recalculateFieldIndexes();
}

function createFieldElement() {
  let fieldElement = document.createElement('div');
  let id = 0;
  if (document.getElementsByClassName("influactive_form_field").length > 0) {
    id = document.getElementsByClassName("influactive_form_field").length;
  }
  fieldElement.className = "influactive_form_field";
  fieldElement.innerHTML =
    `<p><label>${influactiveFormsTranslations.typeLabelText} <select class="field_type" name="influactive_form_fields[${id}][type]">` +
    `<option value="text">${influactiveFormsTranslations.Text}</option>` +
    `<option value="email">${influactiveFormsTranslations.Email}</option>` +
    `<option value="number">${influactiveFormsTranslations.Number}</option>` +
    `<option value="textarea">${influactiveFormsTranslations.Textarea}</option>` +
    `<option value="select">${influactiveFormsTranslations.Select}</option>` +
    `<option value="gdpr">${influactiveFormsTranslations.GDPR}</option>` +
    `<option value="free_text">${influactiveFormsTranslations.Freetext}</option>` +
    `</select></label> ` +
    `<label>${influactiveFormsTranslations.labelLabelText} <input type="text" name="influactive_form_fields[${id}][label]" class="influactive_form_fields_label"></label> ` +
    `<label>${influactiveFormsTranslations.nameLabelText} <input type="text" name="influactive_form_fields[${id}][name]" class="influactive_form_fields_name"></label> ` +
    `<div class="options_container"></div>` +
    `<input type="hidden" name="influactive_form_fields[${id}][order]" class="influactive_form_fields_order" value="${id}">` +
    `<a href="#" class="remove_field">${influactiveFormsTranslations.removeFieldText}</a> ` +
    `</p>`;
  return fieldElement;
}

function createOptionElement() {
  let optionElement = document.createElement('p');
  optionElement.className = "option-field";
  optionElement.innerHTML = `<label>${influactiveFormsTranslations.optionLabelLabelText} ` +
    `<input type="text" class="option-label" name="influactive_form_fields[][options][][label]">` +
    `</label> ` +
    `<label>${influactiveFormsTranslations.optionValueLabelText} ` +
    `<input type="text" class="option-value" name="influactive_form_fields[][options][][value]">` +
    `</label> ` +
    `<a href="#" class="remove_option">${influactiveFormsTranslations.removeOptionText}</a>`;
  return optionElement;
}

function recalculateFieldIndexes() {
  const container = document.getElementById("influactive_form_fields_container");
  const formFields = Array.from(container.getElementsByClassName("influactive_form_field"));
  formFields.forEach((fieldField, index) => {
    const fieldType = fieldField.querySelector(".field_type");
    const fieldLabel = fieldField.querySelector(".influactive_form_fields_label");
    const fieldName = fieldField.querySelector(".influactive_form_fields_name");
    const fieldOrder = fieldField.querySelector(".influactive_form_fields_order");
    const options = Array.from(fieldField.getElementsByClassName("option-field"));
    if (fieldType && fieldLabel && fieldName && fieldOrder) {
      fieldType.name = `influactive_form_fields[${index}][type]` ?? "";
      fieldLabel.name = `influactive_form_fields[${index}][label]` ?? "";
      fieldName.name = `influactive_form_fields[${index}][name]` ?? ""
      fieldOrder.name = `influactive_form_fields[${index}][order]` ?? "";
      if (options.length > 0) {
        options.forEach((option, optionIndex) => {
          const optionLabel = option.querySelector(".option-label");
          const optionValue = option.querySelector(".option-value");
          if (optionLabel && optionValue) {
            optionLabel.name = `influactive_form_fields[${index}][options][${optionIndex}][label]` ?? "";
            optionValue.name = `influactive_form_fields[${index}][options][${optionIndex}][value]` ?? "";
          }
        });
      }
    }
  });
}
