/* global influactiveFormsTranslations */

document.addEventListener('DOMContentLoaded', () => {
  // Add a new layout
  document.getElementById('add_new_layout').addEventListener('click', (e) => {
    e.preventDefault()

    const layoutCount = parseInt(document.getElementById('layoutCount').value, 10)

    // Get the first layout and clone it
    const firstLayout = document.querySelector('.influactive_form_layout_container')
    const newLayout = firstLayout.cloneNode(true)

    // Modify the attributes and values of the new layout
    newLayout.id = `influactive_form_layout_container_${layoutCount}`
    newLayout.dataset.layout = layoutCount.toString()

    // Update the names and values of inputs and textarea in the new layout
    const inputs = newLayout.querySelectorAll('input, textarea')
    inputs.forEach((input) => {
      const clonedInput = input.cloneNode(true) // Clone the input/textarea element
      clonedInput.name = clonedInput.name.replace(/\[0]/, `[${layoutCount}]`)
      clonedInput.value = '' // reset the value of each cloned input field
      input.replaceWith(clonedInput) // Replace the original input with the cloned one
    })

    // Add a deleted button if not already present
    if (!newLayout.querySelector('.delete_layout')) {
      const deleteButton = document.createElement('button')
      deleteButton.type = 'button'
      deleteButton.className = 'delete_layout'
      deleteButton.textContent = influactiveFormsTranslations.delete_layout
      newLayout.appendChild(deleteButton)
    }

    // Add the new layout to the end of the layout container
    document.getElementById('layout_container').appendChild(newLayout)

    // Update the layout counter
    document.getElementById('layoutCount').value = layoutCount + 1
  })

  // Remove an existing layout
  document.getElementById('layout_container').addEventListener('click', (e) => {
    if (e.target && e.target.classList.contains('delete_layout')) {
      e.preventDefault()

      // Do not allow deletion of the first layout
      const layoutContainer = e.target.closest('.influactive_form_layout_container')
      if (layoutContainer.id === 'influactive_form_layout_container_0') {
        return
      }

      // Remove the layout container associated with this button
      layoutContainer.remove()

      // Update the layout counter
      const layoutCount = parseInt(document.getElementById('layoutCount').value, 10)
      document.getElementById('layoutCount').value = layoutCount - 1
    }
  })
})
