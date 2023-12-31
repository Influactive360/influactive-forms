import Sortable from 'sortablejs'
import './form.scss'

/* global influactiveFormsTranslations, tinymce */

/**
 * Recalculates the field indexes for the form fields and their options.
 * If the `form_fields_container` element doesn't exist,
 * the function will return without performing any calculations.
 * The updated field indexes will be reflected in the `name`
 * attribute of the form field elements.
 * The updated option indexes will be reflected in the `name` attribute
 * of the option field elements.
 *
 * @function recalculateFieldIndexes
 * @returns {undefined}
 */
const recalculateFieldIndexes = () => {
  const formFieldsContainer = document.getElementById('form_fields_container')

  if (!formFieldsContainer) {
    return
  }

  const formFields = Array.from(formFieldsContainer.getElementsByClassName('form_field'))

  formFields.forEach((formField, index) => {
    const fieldTypes = ['type', 'label', 'name', 'order', 'required']
    const optionFields = Array.from(formField.getElementsByClassName('option-field'))

    fieldTypes.forEach((fieldType) => {
      const formFieldElement = formField.querySelector(`.${fieldType}`)

      if (!formFieldElement || (fieldType === 'required' && formFieldElement.value !== '1')) {
        return
      }

      formFieldElement.name = `form_fields[${index}][${fieldType}]`
    })

    if (optionFields.length > 0) {
      optionFields.forEach((option, optionIndex) => {
        const optionFieldTypes = ['label', 'value']
        optionFieldTypes.forEach((optionFieldType) => {
          const optionFieldElement = option.querySelector(`.${optionFieldType}`)

          if (!optionFieldElement) {
            return
          }

          optionFieldElement.name = `form_fields[${index}][options][${optionIndex}][${optionFieldType}]`
        })
      })
    }
  })
}

/**
 * Creates an input element with an associated label and appends it to the specified parent element.
 *
 * @param {Element} parent - The parent element to append the input and label to.
 * @param {string} labelText - The text content of the label element.
 * @param {string} name - The name attribute of the input element.
 * @param {string} type - The type attribute of the input element.
 * @param {string} className - The class name to assign to the input element.
 * @returns {void} - This function does not return a value.
 */
const createInputWithLabel = (parent, labelText, name, type, className) => {
  const label = document.createElement('label')
  label.textContent = labelText
  const input = document.createElement('input')
  input.type = type
  input.name = name
  input.required = true
  input.className = className
  label.appendChild(input)
  parent.appendChild(label)
}

/**
 * Creates a new field element for a form.
 * @returns {HTMLElement} - The created field element.
 */
const createFieldElement = () => {
  const fieldElement = document.createElement('div')
  const id = document.getElementsByClassName('influactive_form_field').length
  fieldElement.className = 'influactive_form_field'

  const p = document.createElement('p')

  // Création du select pour le type
  const labelType = document.createElement('label')
  labelType.textContent = 'Type '

  const selectType = document.createElement('select')
  selectType.required = true
  selectType.className = 'field_type'
  selectType.name = `influactive_form_fields[${id}][type]`

  const optionsData = [
    ['text', influactiveFormsTranslations.Text],
    ['email', influactiveFormsTranslations.Email],
    ['number', influactiveFormsTranslations.Number],
    ['textarea', influactiveFormsTranslations.Textarea],
    ['select', influactiveFormsTranslations.Select],
    ['gdpr', influactiveFormsTranslations.GDPR],
    ['free_text', influactiveFormsTranslations.Freetext],
  ]

  optionsData.forEach(([value, text]) => {
    const option = document.createElement('option')
    option.value = value
    option.textContent = text
    selectType.appendChild(option)
  })

  labelType.appendChild(selectType)
  p.appendChild(labelType)

  createInputWithLabel(p, 'Label ', `influactive_form_fields[${id}][label]`, 'text', 'influactive_form_fields_label')
  createInputWithLabel(p, 'Name ', `influactive_form_fields[${id}][name]`, 'text', 'influactive_form_fields_name')

  const optionsContainer = document.createElement('div')
  optionsContainer.className = 'options_container'
  p.appendChild(optionsContainer)

  const labelRequired = document.createElement('label')
  labelRequired.textContent = 'Required '
  const inputRequired = document.createElement('input')
  inputRequired.type = 'checkbox'
  inputRequired.name = `influactive_form_fields[${id}][required]`
  inputRequired.value = '1'
  inputRequired.className = 'influactive_form_fields_required'
  labelRequired.appendChild(inputRequired)
  p.appendChild(labelRequired)

  const inputHidden = document.createElement('input')
  inputHidden.type = 'hidden'
  inputHidden.name = `influactive_form_fields[${id}][order]`
  inputHidden.className = 'influactive_form_fields_order'
  inputHidden.value = id
  p.appendChild(inputHidden)

  const aRemove = document.createElement('a')
  aRemove.href = '#'
  aRemove.className = 'remove_field'
  aRemove.textContent = influactiveFormsTranslations.removeFieldText
  p.appendChild(aRemove)

  fieldElement.appendChild(p)

  return fieldElement
}

/**
 * Creates an option element.
 *
 * @returns {HTMLElement} The option element.
 */
const createOptionElement = () => {
  const optionElement = document.createElement('p')
  optionElement.className = 'option-field'

  // Create the option label
  const optionLabel = document.createElement('label')
  optionLabel.textContent = `${influactiveFormsTranslations.optionLabelLabelText} `
  const optionLabelInput = document.createElement('input')
  optionLabelInput.type = 'text'
  optionLabelInput.className = 'option-label'
  optionLabelInput.name = 'influactive_form_fields[][options][][label]'
  optionLabel.appendChild(optionLabelInput)
  optionElement.appendChild(optionLabel)

  // Add space
  optionElement.appendChild(document.createTextNode(' '))

  // Create the value label
  const valueLabel = document.createElement('label')
  valueLabel.textContent = `${influactiveFormsTranslations.optionValueLabelText} `
  const valueLabelInput = document.createElement('input')
  valueLabelInput.type = 'text'
  valueLabelInput.className = 'option-value'
  valueLabelInput.name = 'influactive_form_fields[][options][][value]'
  valueLabel.appendChild(valueLabelInput)
  optionElement.appendChild(valueLabel)

  // Add space
  optionElement.appendChild(document.createTextNode(' '))

  // Create remove option link
  const removeOptionLink = document.createElement('a')
  removeOptionLink.href = '#'
  removeOptionLink.className = 'remove_option'
  removeOptionLink.textContent = influactiveFormsTranslations.removeOptionText
  optionElement.appendChild(removeOptionLink)

  return optionElement
}

/**
 * Removes the option field when triggered by an event.
 *
 * @param {Event} e - The event that triggered the removal of the option field.
 * @returns {void}
 */
const removeOptionHandler = (e) => {
  e.preventDefault()
  e.target.closest('.option-field').remove()
  recalculateFieldIndexes()
}

/**
 * Adds an option handler to a form field element.
 *
 * @param {Event} e - The event object.
 * @returns {undefined}
 */
const addOptionHandler = (e) => {
  e.preventDefault()
  const optionsContainer = e.target.previousElementSibling
  const optionElement = createOptionElement()
  const removeOptionElement = optionElement.querySelector('.remove_option')
  if (removeOptionElement) {
    removeOptionElement.addEventListener('click', removeOptionHandler)
  }
  optionsContainer.appendChild(optionElement)
  const influactiveFormFieldsOrder = optionElement.closest('.influactive_form_field')
    .querySelector('.influactive_form_fields_order').value
  let influactiveFormOptionsOrder = 0
  if (document.getElementsByClassName('option-field').length > 0) {
    influactiveFormOptionsOrder = document.getElementsByClassName('option-field').length
  }
  optionElement.querySelector('.option-label').name = `influactive_form_fields[${influactiveFormFieldsOrder}][options][${influactiveFormOptionsOrder}][label]`
  optionElement.querySelector('.option-value').name = `influactive_form_fields[${influactiveFormFieldsOrder}][options][${influactiveFormOptionsOrder}][value]`
  recalculateFieldIndexes()
}

/**
 * Handles the change event of a field element.
 * @param {HTMLElement} fieldElement - The field element to handle.
 */
const fieldTypeChangeHandler = (fieldElement) => function eventFunction(event) {
  recalculateFieldIndexes()
  const fieldValue = event.target.value
  // Remove existing elements
  const oldLabelElement = fieldElement.querySelector('.influactive_form_fields_label')
  const oldNameElement = fieldElement.querySelector('.influactive_form_fields_name')
  const oldRequiredElement = fieldElement.querySelector('.influactive_form_fields_required')
  const oldTextAreaElement = fieldElement.querySelector('.wysiwyg-editor') // Added this line
  if (oldLabelElement && oldNameElement) {
    if (oldTextAreaElement && tinymce.get(oldTextAreaElement.id)) {
      tinymce.get(oldTextAreaElement.id).remove()
    }
    oldLabelElement.parentElement.remove()
    oldNameElement.parentElement.remove()
    if (oldRequiredElement) {
      oldRequiredElement.parentElement.remove()
    }
    if (oldTextAreaElement) {
      oldTextAreaElement.remove()
    }
  }
  if (fieldValue === 'gdpr') {
    const gdprTextElement = document.createElement('label')
    const textNode = document.createTextNode(`${influactiveFormsTranslations.Text} `)
    gdprTextElement.appendChild(textNode)

    const inputElement = document.createElement('input')
    inputElement.type = 'text'
    inputElement.name = `influactive_form_fields[${fieldElement.querySelector('.influactive_form_fields_order').value}][label]`
    inputElement.className = 'influactive_form_fields_label'
    inputElement.dataset.type = 'gdpr'
    inputElement.required = true

    gdprTextElement.appendChild(inputElement)

    const gdprNameElement = document.createElement('label')
    const hiddenInputElement = document.createElement('input')
    hiddenInputElement.type = 'hidden'
    hiddenInputElement.name = `influactive_form_fields[${fieldElement.querySelector('.influactive_form_fields_order').value}][name]`
    hiddenInputElement.className = 'influactive_form_fields_name'
    hiddenInputElement.value = 'gdpr'

    gdprNameElement.appendChild(hiddenInputElement)

    // Append these elements to fieldElement or any other container element
    fieldElement.appendChild(gdprTextElement)
    fieldElement.appendChild(gdprNameElement)
  }

  if (fieldValue === 'free_text') {
    const textareaElement = document.createElement('textarea')
    textareaElement.name = `influactive_form_fields[${fieldElement.querySelector('.influactive_form_fields_order').value}][label]`
    textareaElement.className = 'influactive_form_fields_label wysiwyg-editor'
    textareaElement.rows = 10
    // Append textarea to the field element
    fieldElement.appendChild(textareaElement)
    // Initialize TinyMCE
    setTimeout(() => {
      tinymce.init({
        selector: '.wysiwyg-editor', // we use a class selector to select the new textarea
        height: 215,
        menubar: false,
        plugins: [
          'lists link image charmap',
          'fullscreen',
          'paste',
        ],
        toolbar: 'bold italic underline link unlink undo redo formatselect backcolor alignleft aligncenter alignright alignjustify bullist numlist outdent indent removeformat',
        content_style: 'body { font-family:Helvetica,Arial,sans-serif; font-size:14px }',
      })
    }, 0)
    const NameElement = document.createElement('label')
    const inputElement = document.createElement('input')

    // Attribuer les propriétés à l'élément input
    inputElement.type = 'hidden'
    inputElement.name = `influactive_form_fields[${fieldElement.querySelector('.influactive_form_fields_order').value}][name]`
    inputElement.className = 'influactive_form_fields_name'
    inputElement.value = 'free_text'

    NameElement.appendChild(inputElement)
    fieldElement.appendChild(NameElement)
  }

  const labelElement = fieldElement.querySelector('.influactive_form_fields_label')
  const nameElement = fieldElement.querySelector('.influactive_form_fields_name')
  // If they don't exist, create and append them
  if (!labelElement && !nameElement) {
    const LabelElement = document.createElement('label')
    LabelElement.textContent = 'Label '

    const labelInput = document.createElement('input')
    labelInput.type = 'text'
    labelInput.name = `influactive_form_fields[${fieldElement.querySelector('.influactive_form_fields_order').value}][label]`
    labelInput.className = 'influactive_form_fields_label'
    labelInput.required = true

    LabelElement.appendChild(labelInput)

    const NameElement = document.createElement('label')
    NameElement.textContent = 'Name '

    const nameInput = document.createElement('input')
    nameInput.type = 'text'
    nameInput.name = `influactive_form_fields[${fieldElement.querySelector('.influactive_form_fields_order').value}][name]`
    nameInput.className = 'influactive_form_fields_name'
    nameInput.required = true

    NameElement.appendChild(nameInput)

    const RequiredElement = document.createElement('label')
    RequiredElement.textContent = 'Required '

    const requiredInput = document.createElement('input')
    requiredInput.type = 'checkbox'
    requiredInput.value = '1'
    requiredInput.name = `influactive_form_fields[${fieldElement.querySelector('.influactive_form_fields_order').value}][required]`
    requiredInput.className = 'influactive_form_fields_required'

    RequiredElement.appendChild(requiredInput)

    // Append these elements to fieldElement or any other container element
    fieldElement.appendChild(LabelElement)
    fieldElement.appendChild(NameElement)
    fieldElement.appendChild(RequiredElement)
  }
  if (fieldValue === 'select') {
    // Create an anchor element for the "Add option" link
    const addOptionLink = document.createElement('a')
    addOptionLink.href = '#'
    addOptionLink.textContent = 'Add option'
    addOptionLink.classList.add('add_option')

    // Append the paragraph to the field element
    fieldElement.appendChild(addOptionLink)

    // Create and append options container
    const optionsContainer = document.createElement('div')
    optionsContainer.classList.add('options_container')
    fieldElement.appendChild(optionsContainer)

    // Add event listener to the "Add option" link
    addOptionLink.addEventListener('click', addOptionHandler)
  } else {
    const addOptionElement = fieldElement.querySelector('.add_option')
    const optionsContainer = fieldElement.querySelector('.options_container')
    if (addOptionElement) {
      addOptionElement.remove()
    }
    if (optionsContainer) {
      optionsContainer.remove()
    }
  }
}

/**
 * Adds a field handler when a form field is added.
 *
 * @param {Event} e - The event object.
 * @returns {void}
 */
const addFieldHandler = (e) => {
  e.preventDefault()
  const fieldElement = createFieldElement()
  const container = document.getElementById('influactive_form_fields_container')
  container.appendChild(fieldElement)
  const fieldType = fieldElement.querySelector('.field_type')
  fieldType.addEventListener('change', fieldTypeChangeHandler(fieldElement))
  const addOptionBtn = fieldElement.querySelector('.add_option')
  if (addOptionBtn) {
    addOptionBtn.addEventListener('click', addOptionHandler)
  }
  recalculateFieldIndexes()
}

/**
 * Removes the field element when triggered by an event.
 *
 * @param {Event} e - The event object.
 * @returns {void}
 */
const removeFieldHandler = (e) => {
  e.preventDefault()
  e.target.closest('.influactive_form_field').remove()
  recalculateFieldIndexes()
}

document.addEventListener('DOMContentLoaded', () => {
  const container = document.getElementById('influactive_form_fields_container')
  new Sortable(container, {
    animation: 150, // This class should be on the elements you want to be draggable
    handle: '.influactive_form_field',
  })
  document.getElementById('add_field').addEventListener('click', addFieldHandler)
  Array.from(container.getElementsByClassName('influactive_form_field')).forEach((formField) => {
    const fieldType = formField.querySelector('.field_type')
    fieldType.addEventListener('change', fieldTypeChangeHandler(formField)) // Added this line
    if (fieldType.value === 'select') {
      const addOptionElement = formField.querySelector('.add_option')
      const removeOptionElement = formField.querySelector('.remove_option')
      if (addOptionElement) {
        addOptionElement.addEventListener('click', addOptionHandler)
      }
      if (removeOptionElement) {
        removeOptionElement.addEventListener('click', removeOptionHandler)
      }
    }
  })
  container.addEventListener('click', (e) => {
    if (e.target && e.target.classList.contains('remove_field')) {
      removeFieldHandler(e)
    }
  })
})
