import Sortable from 'sortablejs'
import tinymce from 'tinymce/tinymce'

/* global influactiveFormsTranslations */

function recalculateFieldIndexes() {
  const container = document.getElementById('influactive_form_fields_container')
  const formFields = [...container.getElementsByClassName('influactive_form_field')]

  formFields.forEach((formField, index) => {
    const fields = ['type', 'label', 'name', 'order', 'required']
    const options = [...formField.getElementsByClassName('option-field')]

    fields.forEach((field) => {
      const fieldElement = formField.querySelector(`.${field}`)
      if (fieldElement && (field !== 'required' || fieldElement.value === '1')) {
        fieldElement.name = `influactive_form_fields[${index}][${field}]`
      }
    })

    if (options.length > 0) {
      options.forEach((option, optionIndex) => {
        const optionFields = ['label', 'value']
        optionFields.forEach((optionField) => {
          const fieldElement = option.querySelector(`.${optionField}`)
          if (fieldElement) {
            fieldElement.name = `influactive_form_fields[${index}][options][${optionIndex}][${optionField}]`
          }
        })
      })
    }
  })
}

function createInputWithLabel(parent, labelText, name, type, className) {
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

function createFieldElement() {
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

function createOptionElement() {
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
 * @param {Event} e
 */
function removeOptionHandler(e) {
  e.preventDefault()
  e.target.closest('.option-field').remove()
  recalculateFieldIndexes()
}

/**
 * @param {ElementEventMap[keyof ElementEventMap]} e
 */
function addOptionHandler(e) {
  e.preventDefault()
  const optionsContainer = e.target.parentElement.previousElementSibling
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
 * @param {Element} fieldElement
 */
function fieldTypeChangeHandler(fieldElement) {
  return function eventFunction(event) {
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

      // Ajouter l'élément input à l'élément label
      NameElement.appendChild(inputElement)

      // Ajouter l'élément label à l'élément parent
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
      requiredInput.required = true

      RequiredElement.appendChild(requiredInput)

      // Append these elements to fieldElement or any other container element
      fieldElement.appendChild(LabelElement)
      fieldElement.appendChild(NameElement)
      fieldElement.appendChild(RequiredElement)
    }
    if (fieldValue === 'select') {
      // Create a paragraph element for adding options
      const addOptionElement = document.createElement('p')

      // Create an anchor element for the "Add option" link
      const addOptionLink = document.createElement('a')
      addOptionLink.href = '#'
      addOptionLink.textContent = 'Add option'
      addOptionLink.classList.add('add_option')

      // Append the anchor to the paragraph
      addOptionElement.appendChild(addOptionLink)

      // Append the paragraph to the field element
      fieldElement.appendChild(addOptionElement)

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
}

function addFieldHandler(e) {
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
 * @param {MouseEvent} e
 */
function removeFieldHandler(e) {
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
