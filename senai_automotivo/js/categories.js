// Category Management System for SENAI Automotivo

class CategoryManager {
    constructor() {
        this.defaultCategories = ['Hatch', 'Sedan', 'SUV', 'Picape', 'Conversível', 'Esportivo'];
        this.customCategories = this.loadFromStorage();
        console.log('CategoryManager initialized with categories:', this.getAllCategories());
    }

    // Load categories from localStorage
    loadFromStorage() {
        console.log('Loading categories from localStorage...');
        try {
            const stored = localStorage.getItem('senai_customCategories');
            const categories = stored ? JSON.parse(stored) : [];
            console.log('Loaded custom categories:', categories);
            return categories;
        } catch (error) {
            console.error('Error loading categories from storage:', error);
            return [];
        }
    }

    // Save categories to localStorage
    saveToStorage() {
        console.log('Saving categories to localStorage:', this.customCategories);
        try {
            localStorage.setItem('senai_customCategories', JSON.stringify(this.customCategories));
            console.log('Categories saved successfully');
        } catch (error) {
            console.error('Error saving categories to storage:', error);
        }
    }

    // Get all categories (default + custom)
    getAllCategories() {
        return [...this.defaultCategories, ...this.customCategories];
    }

    // Add a new custom category
    addCategory(categoryName) {
        console.log('Adding new category:', categoryName);
        
        if (!categoryName || typeof categoryName !== 'string') {
            throw new Error('Nome da categoria é obrigatório');
        }

        const trimmedName = categoryName.trim();
        if (!trimmedName) {
            throw new Error('Nome da categoria não pode estar vazio');
        }

        // Check if category already exists (case insensitive)
        const allCategories = this.getAllCategories();
        const exists = allCategories.some(cat => 
            cat.toLowerCase() === trimmedName.toLowerCase()
        );

        if (exists) {
            throw new Error('Esta categoria já existe');
        }

        // Add to custom categories
        this.customCategories.push(trimmedName);
        this.saveToStorage();
        
        console.log('Category added successfully:', trimmedName);
        return trimmedName;
    }

    // Remove a custom category
    removeCategory(categoryName) {
        console.log('Removing category:', categoryName);
        
        const index = this.customCategories.indexOf(categoryName);
        if (index === -1) {
            throw new Error('Categoria não encontrada');
        }

        this.customCategories.splice(index, 1);
        this.saveToStorage();
        
        console.log('Category removed successfully:', categoryName);
        return categoryName;
    }

    // Get only custom categories
    getCustomCategories() {
        return [...this.customCategories];
    }

    // Get only default categories
    getDefaultCategories() {
        return [...this.defaultCategories];
    }

    // Check if a category is custom
    isCustomCategory(categoryName) {
        return this.customCategories.includes(categoryName);
    }

    // Update select options in forms
    updateSelectOptions(selectElement) {
        if (!selectElement) {
            console.warn('Select element not provided for category update');
            return;
        }

        console.log('Updating select options for categories');
        
        const allCategories = this.getAllCategories();
        const currentValue = selectElement.value;
        
        // Clear existing options except the first one (placeholder)
        const firstOption = selectElement.firstElementChild;
        selectElement.innerHTML = '';
        if (firstOption) {
            selectElement.appendChild(firstOption);
        }

        // Add category options
        allCategories.forEach(category => {
            const option = document.createElement('option');
            option.value = category;
            option.textContent = category;
            selectElement.appendChild(option);
        });

        // Restore selected value if it still exists
        if (currentValue && allCategories.includes(currentValue)) {
            selectElement.value = currentValue;
        }

        console.log('Select options updated with', allCategories.length, 'categories');
    }

    // Create category display elements
    createCategoryDisplayElements(container) {
        if (!container) {
            console.warn('Container element not provided for category display');
            return;
        }

        console.log('Creating category display elements');
        
        container.innerHTML = '';
        
        this.customCategories.forEach(category => {
            const categoryElement = document.createElement('span');
            categoryElement.className = 'category-tag';
            categoryElement.innerHTML = `
                ${category}
                <span class="remove-category" onclick="categoryManager.handleRemoveCategory('${category}')" title="Remover categoria">
                    &times;
                </span>
            `;
            container.appendChild(categoryElement);
        });

        if (this.customCategories.length === 0) {
            container.innerHTML = '<p class="text-gray-500 text-sm">Nenhuma categoria personalizada criada ainda.</p>';
        }

        console.log('Category display updated with', this.customCategories.length, 'custom categories');
    }

    // Handle add category from UI
    handleAddCategory() {
        console.log('Handling add category from UI');
        
        const input = document.getElementById('newCategoryInput');
        if (!input) {
            console.error('Category input element not found');
            return;
        }

        const categoryName = input.value.trim();
        
        try {
            const addedCategory = this.addCategory(categoryName);
            input.value = '';
            
            // Update UI
            this.updateAllCategorySelects();
            this.updateCategoryDisplay();
            
            // Show success notification
            if (typeof showNotification === 'function') {
                showNotification(`Categoria "${addedCategory}" adicionada com sucesso!`, 'success');
            }
            
            console.log('Category added successfully from UI:', addedCategory);
        } catch (error) {
            console.error('Error adding category:', error.message);
            
            // Show error notification
            if (typeof showNotification === 'function') {
                showNotification(error.message, 'error');
            }
        }
    }

    // Handle remove category from UI
    handleRemoveCategory(categoryName) {
        console.log('Handling remove category from UI:', categoryName);
        
        if (!confirm(`Tem certeza que deseja remover a categoria "${categoryName}"?`)) {
            return;
        }

        try {
            const removedCategory = this.removeCategory(categoryName);
            
            // Update UI
            this.updateAllCategorySelects();
            this.updateCategoryDisplay();
            
            // Show success notification
            if (typeof showNotification === 'function') {
                showNotification(`Categoria "${removedCategory}" removida com sucesso!`, 'success');
            }
            
            console.log('Category removed successfully from UI:', removedCategory);
        } catch (error) {
            console.error('Error removing category:', error.message);
            
            // Show error notification
            if (typeof showNotification === 'function') {
                showNotification(error.message, 'error');
            }
        }
    }

    // Update all category select elements
    updateAllCategorySelects() {
        console.log('Updating all category select elements');
        
        const selects = document.querySelectorAll('select[name="category"]');
        selects.forEach(select => this.updateSelectOptions(select));
        
        console.log('Updated', selects.length, 'category select elements');
    }

    // Update category display
    updateCategoryDisplay() {
        console.log('Updating category display');
        
        const container = document.getElementById('categoriesList');
        if (container) {
            this.createCategoryDisplayElements(container);
        }
    }

    // Initialize category management on page load
    initialize() {
        console.log('Initializing category management');
        
        // Update selects
        this.updateAllCategorySelects();
        
        // Update display
        this.updateCategoryDisplay();
        
        // Add event listener for Enter key on category input
        const input = document.getElementById('newCategoryInput');
        if (input) {
            input.addEventListener('keypress', (e) => {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    this.handleAddCategory();
                }
            });
        }
        
        console.log('Category management initialized successfully');
    }

    // Export data for backup
    exportData() {
        return {
            defaultCategories: this.defaultCategories,
            customCategories: this.customCategories,
            exportDate: new Date().toISOString()
        };
    }

    // Import data from backup
    importData(data) {
        if (!data || !Array.isArray(data.customCategories)) {
            throw new Error('Dados de importação inválidos');
        }

        this.customCategories = [...data.customCategories];
        this.saveToStorage();
        this.updateAllCategorySelects();
        this.updateCategoryDisplay();
        
        console.log('Categories imported successfully:', this.customCategories);
    }

    // Get statistics
    getStats() {
        return {
            totalCategories: this.getAllCategories().length,
            defaultCategories: this.defaultCategories.length,
            customCategories: this.customCategories.length
        };
    }
}

// Create global instance
const categoryManager = new CategoryManager();

// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    categoryManager.initialize();
});

// Export for use in other modules
if (typeof module !== 'undefined' && module.exports) {
    module.exports = CategoryManager;
}