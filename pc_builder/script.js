document.addEventListener('DOMContentLoaded', function() {
    // Elementek lekérése
    const modal = document.getElementById('modal');
    const modalBody = document.getElementById('modal-body');
    const closeBtn = document.getElementById('close');
    const configContainer = document.getElementById('config');
    const checkoutBtn = document.getElementById('checkout');
    
    // DEBUG: Ellenőrizzük az elemeket
    console.log('PC Configurator initialized');
    console.log('Checkout button:', checkoutBtn);
    console.log('Config container:', configContainer);
    
    // Kategória gombokhoz eseménykezelők
    document.querySelectorAll('.add').forEach(button => {
        button.addEventListener('click', function() {
            const categoryId = this.dataset.cat;
            console.log('Opening category:', categoryId);
            loadProducts(categoryId);
        });
    });
    
    // Modal bezárása
    closeBtn.addEventListener('click', function() {
        modal.classList.remove('active');
    });
    
    // Modal bezárása kattintásra a háttérre
    modal.addEventListener('click', function(e) {
        if (e.target === modal) {
            modal.classList.remove('active');
        }
    });
    
    // Fizetés gomb eseménykezelő - Átirányít a checkout oldalra
    if (checkoutBtn) {
        checkoutBtn.addEventListener('click', function() {
            console.log('Checkout button clicked');
            
            // Egyszerűbb ellenőrzés: számoljuk meg a termékeket közvetlenül
            const products = configContainer.querySelectorAll('.product');
            console.log('Products found:', products.length);
            
            if (products.length > 0) {
                // Átirányítás a checkout oldalra
                window.location.href = 'checkout.php';
            } else {
                alert('Please add components to your configuration first.');
            }
        });
    } else {
        console.error('Checkout button not found in DOM!');
    }
    
    // Termékek betöltése
    function loadProducts(categoryId) {
        // Modal előkészítése
        modal.classList.add('active');
        modalBody.innerHTML = '<div style="text-align:center; padding:40px;"><div class="loading-spinner"></div><p>Loading products...</p></div>';
        
        fetch('load_products.php?cat=' + categoryId)
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.text();
            })
            .then(html => {
                console.log('Products loaded successfully');
                modalBody.innerHTML = html;
                
                // "Add to Configuration" gombokhoz eseménykezelők hozzáadása
                const addButtons = modalBody.querySelectorAll('.add-to-config');
                console.log('Add buttons found:', addButtons.length);
                
                addButtons.forEach(btn => {
                    btn.addEventListener('click', function() {
                        const productId = this.dataset.id;
                        console.log('Adding product:', productId);
                        addToConfig(productId);
                    });
                });
            })
            .catch(error => {
                console.error('Error loading products:', error);
                modalBody.innerHTML = '<div style="text-align:center; padding:40px; color:#e53935;"><p>Error loading products.</p><p>Please try again later.</p></div>';
            });
    }
    
    // Termék hozzáadása a konfigurációhoz
    function addToConfig(productId) {
        fetch('add_to_config.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ product_id: productId })
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.text();
        })
        .then(() => {
            console.log('Product added successfully');
            modal.classList.remove('active');
            loadConfig();
        })
        .catch(error => {
            console.error('Error adding product:', error);
            alert('Error adding product to configuration.');
        });
    }
    
    // Termék törlése a konfigurációból
    window.removeFromConfig = function(itemId) {
        if (!confirm('Are you sure you want to remove this component?')) {
            return;
        }
        
        fetch('remove_from_config.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ item_id: itemId })
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.text();
        })
        .then(() => {
            console.log('Product removed successfully');
            loadConfig();
        })
        .catch(error => {
            console.error('Error removing product:', error);
            alert('Error removing component.');
        });
    }
    
    // Konfiguráció betöltése
    function loadConfig() {
        console.log('Loading configuration...');
        fetch('get_config.php')
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.text();
            })
            .then(html => {
                console.log('Config HTML loaded successfully');
                configContainer.innerHTML = html;
                updateCheckoutButton();
                calculateTotal();
            })
            .catch(error => {
                console.error('Error loading configuration:', error);
                configContainer.innerHTML = '<div style="text-align: center; padding: 20px; color: #666;">Error loading configuration. Please refresh the page.</div>';
            });
    }
    
    // Fizetés gomb láthatósága
    function updateCheckoutButton() {
        if (!checkoutBtn) {
            console.error('Checkout button not found!');
            return;
        }
        
        // Ellenőrizzük, hogy vannak-e termékek a konfigurációban
        const productElements = configContainer.querySelectorAll('.product');
        const hasProducts = productElements.length > 0;
        
        console.log('Products in config:', productElements.length, 'Show checkout button:', hasProducts);
        
        if (hasProducts) {
            checkoutBtn.style.display = 'block';
            checkoutBtn.style.visibility = 'visible';
            checkoutBtn.style.opacity = '1';
        } else {
            checkoutBtn.style.display = 'none';
        }
        
        // Ha nincsenek termékek, üzenet megjelenítése
        if (!hasProducts) {
            const noProductsMsg = configContainer.querySelector('.no-products-message');
            if (!noProductsMsg) {
                const messageDiv = document.createElement('div');
                messageDiv.className = 'no-products-message';
                messageDiv.style.textAlign = 'center';
                messageDiv.style.padding = '30px';
                messageDiv.style.color = '#666';
                messageDiv.innerHTML = '<p>No components selected yet.</p><p>Choose components from the list above to build your PC.</p>';
                configContainer.appendChild(messageDiv);
            }
        } else {
            // Ha vannak termékek, töröljük az üzenetet
            const noProductsMsg = configContainer.querySelector('.no-products-message');
            if (noProductsMsg) {
                noProductsMsg.remove();
            }
        }
    }
    
    // Összesített ár számolása
    function calculateTotal() {
        const priceElements = configContainer.querySelectorAll('.product-price');
        
        if (priceElements.length === 0) {
            // Ha nincsenek termékek, töröljük a total elemet
            const totalElement = configContainer.querySelector('.total-price');
            if (totalElement) {
                totalElement.remove();
            }
            return;
        }
        
        let total = 0;
        
        priceElements.forEach(priceEl => {
            const priceText = priceEl.textContent;
            // Kivesszük a számokat és pontokkal helyettesítjük a vesszőket
            const priceMatch = priceText.match(/[\d,]+\.?\d*/);
            if (priceMatch) {
                // EUR formátumban lehet, cseréljük vesszőket pontokra
                const cleanPrice = priceMatch[0].replace(/,/g, '');
                const price = parseFloat(cleanPrice);
                if (!isNaN(price)) {
                    total += price;
                }
            }
        });
        
        console.log('Total calculated:', total);
        
        // Összesített ár megjelenítése vagy frissítése
        let totalElement = configContainer.querySelector('.total-price');
        if (!totalElement) {
            totalElement = document.createElement('div');
            totalElement.className = 'total-price';
            configContainer.appendChild(totalElement);
        }
        
        // Formatáljuk az árat
        const formattedTotal = total.toLocaleString('en-US', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        });
        
        totalElement.innerHTML = `<strong>Total: $${formattedTotal}</strong>`;
        totalElement.style.textAlign = 'right';
        totalElement.style.fontSize = '1.2em';
        totalElement.style.color = '#00796b';
        totalElement.style.marginTop = '20px';
        totalElement.style.paddingTop = '15px';
        totalElement.style.borderTop = '2px solid #80cbc4';
    }
    
    // Inicializálás
    console.log('Loading initial configuration...');
    loadConfig();
    
    // ESC billentyűvel is bezárható a modal
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && modal.classList.contains('active')) {
            modal.classList.remove('active');
        }
    });
    
    // Teszt
    setTimeout(() => {
        if (checkoutBtn) {
            console.log('Checkout button debug:', {
                computedDisplay: window.getComputedStyle(checkoutBtn).display,
                offsetParent: checkoutBtn.offsetParent,
                styleDisplay: checkoutBtn.style.display
            });
            
        }
    }, 1000);
});