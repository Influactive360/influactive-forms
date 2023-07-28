document.addEventListener('DOMContentLoaded', () => {
  const tabsContainer = document.querySelector('.tabs')

  if (tabsContainer) {
    tabsContainer.addEventListener('click', (e) => {
      const { target } = e

      if (target.matches('.tab-links a')) {
        e.preventDefault()

        const currentAttrValue = target.getAttribute('href')

        // Hide all tabs
        const allTabs = document.querySelectorAll('.tabs .tab')
        // eslint-disable-next-line no-param-reassign,no-return-assign
        allTabs.forEach((tab) => tab.style.display = 'none')

        // Show the current tab
        const currentTab = document.querySelector(currentAttrValue)
        if (currentTab) {
          currentTab.style.display = 'block'
        }

        // Remove active class from all tab links
        const allTabLinks = document.querySelectorAll('.tabs .tab-links a')
        allTabLinks.forEach((link) => link.parentElement.classList.remove('active'))

        // Add active class to the current tab link
        target.parentElement.classList.add('active')
      }
    })
  }
})
