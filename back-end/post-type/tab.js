document.addEventListener('DOMContentLoaded', function() {
  const tabsContainer = document.querySelector('.tabs');

  tabsContainer.addEventListener('click', function(e) {
    const target = e.target;

    if (target.matches('.tab-links a')) {
      e.preventDefault();

      const currentAttrValue = target.getAttribute('href');

      // Hide all tabs
      const allTabs = document.querySelectorAll('.tabs .tab');
      allTabs.forEach(tab => tab.style.display = 'none');

      // Show the current tab
      const currentTab = document.querySelector(currentAttrValue);
      currentTab.style.display = 'block';

      // Remove active class from all tab links
      const allTabLinks = document.querySelectorAll('.tabs .tab-links a');
      allTabLinks.forEach(link => link.parentElement.classList.remove('active'));

      // Add active class to the current tab link
      target.parentElement.classList.add('active');
    }
  });
});
