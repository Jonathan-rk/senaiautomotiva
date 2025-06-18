// ===== SENAI AUTOMOTIVO - JAVASCRIPT FUNCTIONS =====

// ===== UTILITY FUNCTIONS =====

// Toggle mobile menu
function toggleMobileMenu() {
    const menu = document.getElementById('mobileMenu');
    if (menu) {
        menu.classList.toggle('hidden');
    }
}

// Show notification
function showNotification(message, type = 'info') {
    // Remove existing notifications
    const existingNotifications = document.querySelectorAll('.notification');
    existingNotifications.forEach(notification => notification.remove());
    
    // Create new notification
    const notification = document.createElement('div');
    notification.className = `notification ${type}`;
    notification.innerHTML = `
        <i class="fas fa-${type === 'success' ? 'check-circle' : type === 'error' ? 'exclamation-triangle' : 'info-circle'} mr-2"></i>
        ${message}
    `;
    
    document.body.appendChild(notification);
    
    // Auto remove after 5 seconds
    setTimeout(() => {
        notification.remove();
    }, 5000);
}

// Smooth scroll to element
function scrollToElement(elementId) {
    const element = document.getElementById(elementId);
    if (element) {
        element.scrollIntoView({
            behavior: 'smooth',
            block: 'start'
        });
    }
}

// ===== SEARCH AND FILTER FUNCTIONS =====

// Filter fichas in real-time
function filterFichas() {
    const searchTerm = document.getElementById('searchFichas').value.toLowerCase();
    const rows = document.querySelectorAll('.ficha-row');
    
    rows.forEach(row => {
        const name = row.querySelector('.ficha-name').textContent.toLowerCase();
        if (name.includes(searchTerm)) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
}

// Advanced search functionality
function performAdvancedSearch() {
    const searchForm = document.getElementById('advancedSearchForm');
    if (searchForm) {
        const formData = new FormData(searchForm);
        const params = new URLSearchParams();
        
        for (let [key, value] of formData.entries()) {
            if (value.trim() !== '') {
                params.append(key, value);
            }
        }
        
        window.location.href = 'fichas.php?' + params.toString();
    }
}

// Clear all filters
function clearFilters() {
    const form = document.querySelector('form[method="GET"]');
    if (form) {
        const inputs = form.querySelectorAll('input, select');
        inputs.forEach(input => {
            if (input.type === 'text' || input.type === 'search') {
                input.value = '';
            } else if (input.tagName === 'SELECT') {
                input.selectedIndex = 0;
            }
        });
        
        // Redirect to clean URL
        window.location.href = window.location.pathname;
    }
}

// ===== PDF AND SHARING FUNCTIONS =====

// Download PDF using jsPDF
function downloadPDFWithJS(fichaId) {
    // This function would be called from the detalhes.php page
    // where jsPDF is already loaded
    if (typeof window.jspdf !== 'undefined') {
        const { jsPDF } = window.jspdf;
        const doc = new jsPDF();
        
        // Get vehicle data from the page
        const title = document.querySelector('h1').textContent;
        const specs = document.querySelectorAll('.spec-item');
        
        // Add title
        doc.setFontSize(20);
        doc.text(title, 20, 30);
        
        // Add specifications
        doc.setFontSize(12);
        let y = 50;
        
        specs.forEach(spec => {
            const label = spec.querySelector('.font-medium').textContent;
            const value = spec.querySelector('.font-semibold').textContent;
            
            if (y > 270) { // New page if needed
                doc.addPage();
                y = 30;
            }
            
            doc.text(`${label} ${value}`, 20, y);
            y += 10;
        });
        
        // Save the PDF
        doc.save(`${title.replace(/\s+/g, '_')}.pdf`);
    } else {
        // Fallback to server-side PDF generation
        window.open(`gerar_pdf.php?id=${fichaId}`, '_blank');
    }
}

// Share vehicle information
function shareVehicle() {
    const url = window.location.href;
    const title = document.querySelector('h1').textContent + ' - SENAI Automotivo';
    
    if (navigator.share) {
        navigator.share({
            title: title,
            url: url
        }).catch(err => {
            console.log('Error sharing:', err);
            copyToClipboard(url);
        });
    } else {
        copyToClipboard(url);
    }
}

// Copy text to clipboard
function copyToClipboard(text) {
    if (navigator.clipboard) {
        navigator.clipboard.writeText(text).then(() => {
            showNotification('Link copiado para a área de transferência!', 'success');
        }).catch(() => {
            fallbackCopyToClipboard(text);
        });
    } else {
        fallbackCopyToClipboard(text);
    }
}

// Fallback copy to clipboard
function fallbackCopyToClipboard(text) {
    const textArea = document.createElement('textarea');
    textArea.value = text;
    textArea.style.position = 'fixed';
    textArea.style.left = '-999999px';
    textArea.style.top = '-999999px';
    document.body.appendChild(textArea);
    textArea.focus();
    textArea.select();
    
    try {
        document.execCommand('copy');
        showNotification('Link copiado para a área de transferência!', 'success');
    } catch (err) {
        showNotification('Erro ao copiar link', 'error');
    }
    
    document.body.removeChild(textArea);
}

// ===== MODAL FUNCTIONS =====

// Show modal
function showModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        document.body.style.overflow = 'hidden';
    }
}

// Hide modal
function hideModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
        document.body.style.overflow = 'auto';
    }
}

// Close modal when clicking outside
function setupModalCloseOnOutsideClick(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.addEventListener('click', function(e) {
            if (e.target === modal) {
                hideModal(modalId);
            }
        });
    }
}

// ===== FORM FUNCTIONS =====

// Auto-submit form on select change
function setupAutoSubmitOnChange(selectSelector) {
    const selects = document.querySelectorAll(selectSelector);
    selects.forEach(select => {
        select.addEventListener('change', function() {
            this.form.submit();
        });
    });
}

// Form validation
function validateForm(formId) {
    const form = document.getElementById(formId);
    if (!form) return false;
    
    const requiredFields = form.querySelectorAll('[required]');
    let isValid = true;
    
    requiredFields.forEach(field => {
        if (!field.value.trim()) {
            field.classList.add('border-red-500');
            isValid = false;
        } else {
            field.classList.remove('border-red-500');
        }
    });
    
    return isValid;
}

// ===== ANIMATION FUNCTIONS =====

// Animate elements on scroll
function animateOnScroll() {
    const elements = document.querySelectorAll('.animate-on-scroll');
    
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('fade-in');
            }
        });
    });
    
    elements.forEach(element => {
        observer.observe(element);
    });
}

// Add loading animation to buttons
function addLoadingToButton(buttonId, loadingText = 'Carregando...') {
    const button = document.getElementById(buttonId);
    if (button) {
        const originalText = button.innerHTML;
        button.innerHTML = `<i class="fas fa-spinner fa-spin mr-2"></i>${loadingText}`;
        button.disabled = true;
        
        return function() {
            button.innerHTML = originalText;
            button.disabled = false;
        };
    }
}

// ===== INITIALIZATION =====

// Initialize all functions when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    // Setup auto-submit for filter selects
    setupAutoSubmitOnChange('select[name="categoria"], select[name="marca"]');
    
    // Setup modal close on outside click
    setupModalCloseOnOutsideClick('deleteModal');
    
    // Setup smooth scrolling for anchor links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            const targetId = this.getAttribute('href').substring(1);
            scrollToElement(targetId);
        });
    });
    
    // Setup search input with debounce
    const searchInput = document.getElementById('searchInput');
    if (searchInput) {
        let searchTimeout;
        searchInput.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                const activeFilter = document.querySelector('.filter-btn.active');
                const brand = activeFilter ? activeFilter.textContent.trim() : 'all';
                // This would trigger a search function if implemented
            }, 300);
        });
    }
    
    // Auto-focus on first input in forms
    const firstInput = document.querySelector('form input[type="text"], form input[type="email"]');
    if (firstInput) {
        firstInput.focus();
    }
    
    // Setup animation on scroll
    animateOnScroll();
    
    // Show success/error messages from URL parameters
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.get('success')) {
        showNotification(urlParams.get('success'), 'success');
    }
    if (urlParams.get('error')) {
        showNotification(urlParams.get('error'), 'error');
    }
    
    // Scroll to top when page loads (for detail pages)
    if (window.location.pathname.includes('detalhes.php')) {
        window.scrollTo(0, 0);
    }
});

// ===== KEYBOARD SHORTCUTS =====

// Setup keyboard shortcuts
document.addEventListener('keydown', function(e) {
    // Ctrl/Cmd + K for search
    if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
        e.preventDefault();
        const searchInput = document.getElementById('searchInput') || document.getElementById('searchFichas');
        if (searchInput) {
            searchInput.focus();
        }
    }
    
    // Escape to close modals
    if (e.key === 'Escape') {
        const visibleModal = document.querySelector('.fixed:not(.hidden)');
        if (visibleModal && visibleModal.id) {
            hideModal(visibleModal.id);
        }
    }
});

// ===== EXPORT FUNCTIONS =====

// Export data functionality
function exportData(format = 'json') {
    const loadingButton = addLoadingToButton('exportBtn', 'Exportando...');
    
    // Simulate export process
    setTimeout(() => {
        showNotification('Exportação concluída!', 'success');
        if (loadingButton) loadingButton();
    }, 2000);
}

// ===== UTILITY CLASSES =====

// Add utility classes dynamically
function addUtilityClasses() {
    const style = document.createElement('style');
    style.textContent = `
        .animate-on-scroll {
            opacity: 0;
            transform: translateY(20px);
            transition: all 0.6s ease;
        }
        
        .animate-on-scroll.fade-in {
            opacity: 1;
            transform: translateY(0);
        }
        
        .loading-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 9999;
        }
        
        .pulse-animation {
            animation: pulse 2s infinite;
        }
        
        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.5; }
        }
    `;
    document.head.appendChild(style);
}

// Initialize utility classes
addUtilityClasses();

