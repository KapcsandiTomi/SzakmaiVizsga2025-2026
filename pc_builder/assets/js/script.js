const API_BASE = window.location.pathname.includes('/pc_builder') 
    ? '/Szak/pc_builder' 
    : '';

class PCConfigurator {
    constructor() {
        this.modal = document.getElementById('modal');
        this.modalBody = document.getElementById('modal-body');
        this.closeBtn = document.getElementById('close');
        this.configContainer = document.getElementById('config');
        this.checkoutBtn = document.getElementById('checkout');
        
        this.init();
    }
    
    init() {
        this.bindEvents();
        this.loadConfig();
    }
    
    bindEvents() {
        document.querySelectorAll('.add').forEach(button => {
            button.addEventListener('click', (e) => {
                const categoryId = e.target.dataset.cat;
                this.loadProducts(categoryId);
            });
        });

        this.closeBtn.addEventListener('click', () => this.closeModal());
        
        this.modal.addEventListener('click', (e) => {
            if (e.target === this.modal) {
                this.closeModal();
            }
        });

        if (this.checkoutBtn) {
            this.checkoutBtn.addEventListener('click', () => this.proceedToCheckout());
        }

        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && this.modal.classList.contains('active')) {
                this.closeModal();
            }
        });
    }
    
    loadProducts(categoryId) {
        this.openModal();
        
        fetch(`${API_BASE}/products?cat=${categoryId}`)
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    this.displayProducts(data.products);
                } else {
                    this.showError(data.error || 'Error loading products');
                }
            })
            .catch(error => {
                console.error('Error loading products:', error);
                this.showError('Failed to load products. Please try again.');
                this.closeModal();
            });
    }
    
    displayProducts(products) {
        if (!products || products.length === 0) {
            this.modalBody.innerHTML = `
                <div style="text-align: center; padding: 40px; color: #666;">
                    <p>⚠️ No products available in this category.</p>
                    <p>Please try another category.</p>
                </div>
            `;
            return;
        }
        
        let html = '<div class="products-list">';
        products.forEach(product => {
            html += `
                <div class="modal-product">
                    <div>
                        <strong>${this.escapeHtml(product.name)}</strong><br>
                        <small>ID: ${product.id}</small>
                    </div>
                    <div>
                        <div style="font-weight:bold; color:#00796b; margin-bottom:5px;">
                            $${parseFloat(product.price).toFixed(2)}
                        </div>
                        <button class="add-to-config" data-id="${product.id}">
                            <span class="btn-icon">➕</span>
                            <span class="btn-text">Add to Build</span>
                        </button>
                    </div>
                </div>
            `;
        });
        html += '</div>';
        
        this.modalBody.innerHTML = html;
        this.modalBody.querySelectorAll('.add-to-config').forEach(btn => {
            btn.addEventListener('click', (e) => {
                const productId = e.currentTarget.dataset.id;
                this.addToConfig(productId);
            });
        });
    }
    
    addToConfig(productId) {
        const button = event.currentTarget;
        const originalText = button.innerHTML;
        
        button.innerHTML = '<span class="btn-icon">⏳</span><span class="btn-text">Adding...</span>';
        button.disabled = true;
        
        fetch(`${API_BASE}/config/add`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ product_id: parseInt(productId) })
        })
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                this.closeModal();
                this.loadConfig();
                this.showSuccess('Product added to your build!');
            } else {
                this.showError(data.error || 'Failed to add product');
                button.innerHTML = originalText;
                button.disabled = false;
            }
        })
        .catch(error => {
            console.error('Error adding product:', error);
            this.showError('Network error. Please check your connection and try again.');
            button.innerHTML = originalText;
            button.disabled = false;
        });
    }
    
    loadConfig() {
        fetch(`${API_BASE}/config/get`)
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    this.displayConfig(data.items, data.total);
                } else {
                    console.error('Error loading config:', data.error);
                }
            })
            .catch(error => {
                console.error('Error loading config:', error);
            });
    }
    
    displayConfig(items, total) {
        if (!items || items.length === 0) {
            this.configContainer.innerHTML = `
                <div class="empty-config">
                    <div class="empty-config-icon">⚙️</div>
                    <h3>Start Building Your PC</h3>
                    <p>No components selected yet.</p>
                    <p>Choose components from the categories above to begin.</p>
                </div>
            `;
            if (this.checkoutBtn) {
                this.checkoutBtn.style.display = 'none';
            }
            return;
        }
        
        let html = '<div class="config-items">';
        items.forEach(item => {
            html += `
                <div class="config-item">
                    <div class="item-info">
                        <div class="item-name">${this.escapeHtml(item.name)}</div>
                        <div class="item-price">$${parseFloat(item.price).toFixed(2)}</div>
                    </div>
                    <button class="delete-btn" data-id="${item.item_id}">
                        <span class="btn-icon">🗑️</span>
                        <span class="btn-text">Remove</span>
                    </button>
                </div>
            `;
        });
        
        html += `</div><div class="total-price"><strong>Total: $${parseFloat(total).toFixed(2)}</strong></div>`;
        
        this.configContainer.innerHTML = html;
        
        this.configContainer.querySelectorAll('.delete-btn').forEach(btn => {
            btn.addEventListener('click', (e) => {
                const itemId = e.currentTarget.dataset.id;
                this.removeFromConfig(itemId);
            });
        });
        
        if (this.checkoutBtn) {
            this.checkoutBtn.style.display = 'block';
        }
    }
    
    removeFromConfig(itemId) {
        if (!confirm('Are you sure you want to remove this component from your build?')) {
            return;
        }
        
        fetch(`${API_BASE}/config/remove`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ item_id: parseInt(itemId) })
        })
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                this.loadConfig();
                this.showSuccess('Component removed from your build');
            } else {
                this.showError(data.error || 'Failed to remove component');
            }
        })
        .catch(error => {
            console.error('Error removing item:', error);
            this.showError('Network error. Please try again.');
        });
    }
    
    proceedToCheckout() {
        const configItems = this.configContainer.querySelectorAll('.config-item');
        if (configItems.length > 0) {
            window.location.href = `${API_BASE}/checkout`;
        } else {
            this.showError('Please add at least one component to your build before checkout.');
        }
    }
    
    openModal() {
        this.modal.classList.add('active');
        document.body.style.overflow = 'hidden';
    }
    
    closeModal() {
        this.modal.classList.remove('active');
        document.body.style.overflow = ''; 
    }
    
    showError(message) {
        alert('Error: ' + message);
    }
    
    showSuccess(message) {
        console.log('Success:', message);
        const successMsg = document.createElement('div');
        successMsg.className = 'success-message';
        successMsg.textContent = message;
        successMsg.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            background: #4CAF50;
            color: white;
            padding: 10px 20px;
            border-radius: 5px;
            z-index: 10000;
            animation: fadeInOut 3s;
        `;
        
        document.body.appendChild(successMsg);
        setTimeout(() => successMsg.remove(), 3000);
    }
    
    escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
}

const style = document.createElement('style');
style.textContent = `
    @keyframes fadeInOut {
        0% { opacity: 0; transform: translateY(-20px); }
        10% { opacity: 1; transform: translateY(0); }
        90% { opacity: 1; transform: translateY(0); }
        100% { opacity: 0; transform: translateY(-20px); }
    }
`;
document.head.appendChild(style);

document.addEventListener('DOMContentLoaded', () => {
    window.configurator = new PCConfigurator();
});