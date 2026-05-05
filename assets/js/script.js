/**
 * StudySync Main JavaScript File
 * Handles client-side interactions, form validation, and UI enhancements
 * 
 * @package StudySync
 * @subpackage Assets
 */

// Fallback BASE_URL in case the page does not define it before this script loads
if (typeof BASE_URL === 'undefined') {
    var BASE_URL = '/';
}

// ==================== DOM Ready ====================
document.addEventListener('DOMContentLoaded', function() {
    initializeApp();
});

/**
 * Initialize application
 * Sets up event listeners and initializes components
 */
function initializeApp() {
    // Initialize tooltips and popovers
    initializeBootstrapComponents();

    // Attach event listeners
    attachEventListeners();

    // Setup form validation
    setupFormValidation();

    // Setup auto-dismiss alerts
    setupAlertDismissal();

    // Setup task interactions
    setupTaskInteractions();
}

/**
 * Initialize Bootstrap components (tooltips, popovers)
 */
function initializeBootstrapComponents() {
    // Initialize all tooltips
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });

    // Initialize all popovers
    const popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'));
    popoverTriggerList.map(function (popoverTriggerEl) {
        return new bootstrap.Popover(popoverTriggerEl);
    });
}

/**
 * Attach event listeners to elements
 */
function attachEventListeners() {
    // Close alert buttons
    const alertCloseButtons = document.querySelectorAll('.alert-close');
    alertCloseButtons.forEach(button => {
        button.addEventListener('click', function() {
            this.closest('.alert').remove();
        });
    });

    // Task completion checkboxes
    const taskCheckboxes = document.querySelectorAll('.task-checkbox');
    taskCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            handleTaskStatusChange(this);
        });
    });

    // Delete buttons with confirmation
    const deleteButtons = document.querySelectorAll('[data-action="delete"]');
    deleteButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            showDeleteConfirmation(this);
        });
    });

    // Filter form submission
    const filterForm = document.getElementById('filterForm');
    if (filterForm) {
        filterForm.addEventListener('submit', function(e) {
            // Form submission will happen naturally
        });

        // Filter form change listeners
        const filterInputs = filterForm.querySelectorAll('input, select');
        filterInputs.forEach(input => {
            input.addEventListener('change', function() {
                // Auto-submit filter form on change (optional)
                // filterForm.submit();
            });
        });
    }

    // Search input with debounce
    const searchInput = document.querySelector('[data-action="search"]');
    if (searchInput) {
        let searchTimeout;
        searchInput.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                // Trigger search
                if (document.getElementById('filterForm')) {
                    document.getElementById('filterForm').submit();
                }
            }, 500);
        });
    }
}

/**
 * Setup form validation
 * Bootstrap form validation with custom error messages
 */
function setupFormValidation() {
    const forms = document.querySelectorAll('form[data-validate="true"]');
    
    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            if (!form.checkValidity()) {
                e.preventDefault();
                e.stopPropagation();
            }
            form.classList.add('was-validated');
        });
    });

    // Custom validation for password match
    const passwordField = document.getElementById('password');
    const confirmPasswordField = document.getElementById('confirm_password');

    if (passwordField && confirmPasswordField) {
        confirmPasswordField.addEventListener('blur', function() {
            if (this.value !== passwordField.value) {
                this.setCustomValidity('Passwords do not match');
                this.classList.add('is-invalid');
            } else {
                this.setCustomValidity('');
                this.classList.remove('is-invalid');
            }
        });
    }

    // Custom validation for email
    const emailField = document.querySelector('input[type="email"]');
    if (emailField) {
        emailField.addEventListener('blur', function() {
            if (!isValidEmail(this.value)) {
                this.setCustomValidity('Invalid email format');
                this.classList.add('is-invalid');
            } else {
                this.setCustomValidity('');
                this.classList.remove('is-invalid');
            }
        });
    }
}

/**
 * Validate email format
 * 
 * @param {string} email Email to validate
 * @returns {boolean} True if valid email
 */
function isValidEmail(email) {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return emailRegex.test(email);
}

/**
 * Setup auto-dismiss for alerts
 * Automatically removes alert messages after 5 seconds
 */
function setupAlertDismissal() {
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(alert => {
        setTimeout(() => {
            const bsAlert = new bootstrap.Alert(alert);
            bsAlert.close();
        }, 5000);
    });
}

/**
 * Setup task interactions
 * Handles task-related UI interactions
 */
function setupTaskInteractions() {
    // Task priority color indicators
    const priorityBadges = document.querySelectorAll('.badge-priority-high, .badge-priority-medium, .badge-priority-low');
    priorityBadges.forEach(badge => {
        const priority = badge.textContent.trim();
        badge.classList.add('badge-priority-' + priority.toLowerCase());
    });

    // Task status indicators
    const statusBadges = document.querySelectorAll('[class*="badge-status"]');
    statusBadges.forEach(badge => {
        const status = badge.getAttribute('data-status') || badge.textContent.trim();
        badge.classList.add('badge-status-' + status.toLowerCase());
    });
}

/**
 * Handle task status change (mark as completed/pending)
 * 
 * @param {HTMLElement} checkbox The checkbox element
 */
function handleTaskStatusChange(checkbox) {
    const taskId = checkbox.getAttribute('data-task-id');
    const taskItem = checkbox.closest('.task-item');
    const newStatus = checkbox.checked ? 'Completed' : 'Pending';

    // Send AJAX request to update status
    updateTaskStatus(taskId, newStatus, function(success) {
        if (success) {
            // Update UI
            const taskTitle = taskItem.querySelector('.task-title');
            if (newStatus === 'Completed') {
                taskTitle.classList.add('completed');
                taskItem.style.opacity = '0.7';
            } else {
                taskTitle.classList.remove('completed');
                taskItem.style.opacity = '1';
            }

            // Show success message
            showSuccessAlert('Task updated successfully');
        } else {
            // Revert checkbox on error
            checkbox.checked = !checkbox.checked;
            showErrorAlert('Failed to update task');
        }
    });
}

/**
 * Update task status via AJAX
 * 
 * @param {int} taskId Task ID
 * @param {string} status New status
 * @param {function} callback Callback function
 */
function updateTaskStatus(taskId, status, callback) {
    const formData = new FormData();
    formData.append('task_id', taskId);
    formData.append('status', status);

    fetch(BASE_URL + 'api/task/update-status.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        callback(data.success);
    })
    .catch(error => {
        console.error('Error:', error);
        callback(false);
    });
}

/**
 * Show delete confirmation modal
 * 
 * @param {HTMLElement} button The delete button
 */
function showDeleteConfirmation(button) {
    const taskId = button.getAttribute('data-task-id');
    const taskTitle = button.getAttribute('data-task-title');

    // Create and show confirmation modal
    const confirmText = `Are you sure you want to delete the task "${taskTitle}"? This action cannot be undone.`;
    
    if (confirm(confirmText)) {
        deleteTask(taskId);
    }
}

/**
 * Delete task via AJAX
 * 
 * @param {int} taskId Task ID to delete
 */
function deleteTask(taskId) {
    const formData = new FormData();
    formData.append('task_id', taskId);

    fetch(BASE_URL + 'api/task/delete.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showSuccessAlert('Task deleted successfully');
            // Reload page after 1 second
            setTimeout(() => {
                location.reload();
            }, 1000);
        } else {
            showErrorAlert('Failed to delete task');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showErrorAlert('An error occurred');
    });
}

/**
 * Format date to readable format
 * 
 * @param {string} dateString Date string to format
 * @returns {string} Formatted date string
 */
function formatDate(dateString) {
    const options = { year: 'numeric', month: 'short', day: 'numeric' };
    const date = new Date(dateString);
    return date.toLocaleDateString('en-US', options);
}

/**
 * Format date and time to readable format
 * 
 * @param {string} dateString Date string to format
 * @returns {string} Formatted date and time string
 */
function formatDateTime(dateString) {
    const options = { year: 'numeric', month: 'short', day: 'numeric', hour: '2-digit', minute: '2-digit' };
    const date = new Date(dateString);
    return date.toLocaleDateString('en-US', options);
}

/**
 * Calculate days until due date
 * 
 * @param {string} dueDate Due date string
 * @returns {int} Number of days until due date
 */
function daysUntilDue(dueDate) {
    const due = new Date(dueDate);
    const today = new Date();
    const diffTime = due - today;
    const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
    return diffDays;
}

/**
 * Show success alert message
 * 
 * @param {string} message Alert message
 */
function showSuccessAlert(message) {
    showAlert(message, 'success');
}

/**
 * Show error alert message
 * 
 * @param {string} message Alert message
 */
function showErrorAlert(message) {
    showAlert(message, 'danger');
}

/**
 * Show warning alert message
 * 
 * @param {string} message Alert message
 */
function showWarningAlert(message) {
    showAlert(message, 'warning');
}

/**
 * Show info alert message
 * 
 * @param {string} message Alert message
 */
function showInfoAlert(message) {
    showAlert(message, 'info');
}

/**
 * Show alert message
 * Creates and displays a Bootstrap alert
 * 
 * @param {string} message Alert message
 * @param {string} type Alert type (success, danger, warning, info)
 */
function showAlert(message, type = 'info') {
    const alertContainer = document.createElement('div');
    alertContainer.className = `alert alert-${type} alert-dismissible fade show`;
    alertContainer.innerHTML = `
        <i class="bi bi-exclamation-circle"></i>
        <span>${message}</span>
        <button type="button" class="alert-close" aria-label="Close"></button>
    `;

    // Find alert container or create one at top of body
    let container = document.querySelector('.alert-container');
    if (!container) {
        container = document.createElement('div');
        container.className = 'alert-container';
        document.body.insertBefore(container, document.body.firstChild);
    }

    container.appendChild(alertContainer);

    // Auto-dismiss alert after 5 seconds
    setTimeout(() => {
        const bsAlert = new bootstrap.Alert(alertContainer);
        bsAlert.close();
    }, 5000);

    // Close button functionality
    const closeButton = alertContainer.querySelector('.alert-close');
    closeButton.addEventListener('click', function() {
        const bsAlert = new bootstrap.Alert(alertContainer);
        bsAlert.close();
    });
}

/**
 * Toggle sidebar visibility (mobile)
 */
function toggleSidebar() {
    const sidebar = document.querySelector('.sidebar');
    if (sidebar) {
        sidebar.classList.toggle('show');
    }
}

/**
 * Format number with commas
 * 
 * @param {number} num Number to format
 * @returns {string} Formatted number string
 */
function formatNumber(num) {
    return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ',');
}

/**
 * Throttle function execution
 * 
 * @param {function} func Function to throttle
 * @param {number} limit Time limit in milliseconds
 * @returns {function} Throttled function
 */
function throttle(func, limit) {
    let inThrottle;
    return function() {
        const args = arguments;
        const context = this;
        if (!inThrottle) {
            func.apply(context, args);
            inThrottle = true;
            setTimeout(() => inThrottle = false, limit);
        }
    }
}

/**
 * Debounce function execution
 * 
 * @param {function} func Function to debounce
 * @param {number} delay Delay in milliseconds
 * @returns {function} Debounced function
 */
function debounce(func, delay) {
    let timeoutId;
    return function() {
        const context = this;
        const args = arguments;
        clearTimeout(timeoutId);
        timeoutId = setTimeout(() => func.apply(context, args), delay);
    }
}

/**
 * Deep clone object
 * 
 * @param {object} obj Object to clone
 * @returns {object} Cloned object
 */
function deepClone(obj) {
    return JSON.parse(JSON.stringify(obj));
}

/**
 * Check if element is in viewport
 * 
 * @param {HTMLElement} element Element to check
 * @returns {boolean} True if element is in viewport
 */
function isInViewport(element) {
    const rect = element.getBoundingClientRect();
    return (
        rect.top >= 0 &&
        rect.left >= 0 &&
        rect.bottom <= (window.innerHeight || document.documentElement.clientHeight) &&
        rect.right <= (window.innerWidth || document.documentElement.clientWidth)
    );
}

/**
 * Smooth scroll to element
 * 
 * @param {HTMLElement} element Element to scroll to
 */
function smoothScrollTo(element) {
    element.scrollIntoView({
        behavior: 'smooth',
        block: 'start'
    });
}

// Export functions for use in HTML (if needed)
window.StudySync = {
    formatDate,
    formatDateTime,
    daysUntilDue,
    showAlert,
    showSuccessAlert,
    showErrorAlert,
    updateTaskStatus,
    deleteTask,
    toggleSidebar
};
