<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Inventario - Sistema POS</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
        /* Estilos adicionales para mejorar la apariencia */
        .table-header-group th {
            background-color: #f3f4f6; /* Tailwind gray-100 */
        }
        .btn-primary {
            background-color: #3b82f6; /* Tailwind blue-500 */
            color: white;
        }
        .btn-primary:hover {
            background-color: #2563eb; /* Tailwind blue-600 */
        }
        .btn-secondary {
            background-color: #6b7280; /* Tailwind gray-500 */
            color: white;
        }
        .btn-secondary:hover {
            background-color: #4b5563; /* Tailwind gray-600 */
        }
        .btn-danger {
            background-color: #ef4444; /* Tailwind red-500 */
            color: white;
        }
        .btn-danger:hover {
            background-color: #dc2626; /* Tailwind red-600 */
        }
        .icon-button {
            padding: 0.5rem;
        }
        .modal {
            display: none; /* Oculto por defecto */
        }
        .modal.active {
            display: flex; /* Mostrar cuando está activo */
        }
    </style>
</head>
<body class="bg-gray-100 text-gray-800">

    <div class="container mx-auto p-4 md:p-8">

        <header class="mb-8">
            <h1 class="text-3xl font-bold text-gray-700">Gestión de Inventario</h1>
            <p class="text-gray-500">Administra tus productos, stock y precios. Los datos se guardan en tu navegador.</p>
        </header>

        <div class="mb-6 flex flex-col sm:flex-row justify-between items-center gap-4">
            <div class="relative w-full sm:w-auto">
                <input
                    type="text"
                    id="searchInput"
                    placeholder="Buscar producto por nombre o código..."
                    class="w-full sm:w-64 md:w-96 pl-10 pr-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                >
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <i class="fas fa-search text-gray-400"></i>
                </div>
            </div>
            <button id="openAddProductModalBtn" class="btn-primary font-semibold py-2 px-4 rounded-lg shadow-md hover:shadow-lg transition duration-150 ease-in-out w-full sm:w-auto flex items-center justify-center">
                <i class="fas fa-plus mr-2"></i>Agregar Nuevo Producto
            </button>
        </div>

        <div class="bg-white shadow-xl rounded-lg overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="table-header-group">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Código</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nombre</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Descripción</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Stock</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Precio (MXN)</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Categoría</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                    </tr>
                </thead>
                <tbody id="productTableBody" class="bg-white divide-y divide-gray-200">
                    </tbody>
            </table>
            <div id="paginationControls" class="px-6 py-4 border-t border-gray-200 flex items-center justify-between">
                <p class="text-sm text-gray-700">
                    Mostrando <span id="showingFrom">1</span> a <span id="showingTo">0</span> de <span id="totalResults">0</span> resultados
                </p>
                <div class="flex space-x-1">
                    <button id="prevPageBtn" class="px-3 py-1 border border-gray-300 rounded-md text-sm hover:bg-gray-50 transition duration-150 ease-in-out disabled:opacity-50" disabled>Anterior</button>
                    <button id="nextPageBtn" class="px-3 py-1 border border-gray-300 rounded-md text-sm hover:bg-gray-50 transition duration-150 ease-in-out disabled:opacity-50" disabled>Siguiente</button>
                </div>
            </div>
        </div>
    </div>

    <div id="productModal" class="modal fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full items-center justify-center z-50">
        <div class="relative mx-auto p-5 border w-full max-w-2xl shadow-lg rounded-xl bg-white">
            <div class="mt-3 text-center">
                <h3 class="text-2xl leading-6 font-bold text-gray-900 mb-6" id="modalTitle">Agregar Nuevo Producto</h3>
                <form id="productForm" class="space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="productCode" class="block text-sm font-medium text-gray-700 text-left mb-1">Código del Producto</label>
                            <input type="text" name="productCode" id="productCode" class="mt-1 block w-full border border-gray-300 rounded-lg shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm" placeholder="Ej: PROD004">
                        </div>
                        <div>
                            <label for="productName" class="block text-sm font-medium text-gray-700 text-left mb-1">Nombre del Producto</label>
                            <input type="text" name="productName" id="productName" required class="mt-1 block w-full border border-gray-300 rounded-lg shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm" placeholder="Ej: Monitor Curvo 27 pulgadas">
                        </div>
                    </div>

                    <div>
                        <label for="productDescription" class="block text-sm font-medium text-gray-700 text-left mb-1">Descripción</label>
                        <textarea id="productDescription" name="productDescription" rows="3" class="mt-1 block w-full border border-gray-300 rounded-lg shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm" placeholder="Información detallada del producto..."></textarea>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                            <label for="productStock" class="block text-sm font-medium text-gray-700 text-left mb-1">Stock Actual</label>
                            <input type="number" name="productStock" id="productStock" required min="0" class="mt-1 block w-full border border-gray-300 rounded-lg shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm" placeholder="Ej: 50">
                        </div>
                        <div>
                            <label for="productPurchasePrice" class="block text-sm font-medium text-gray-700 text-left mb-1">Precio de Compra (MXN)</label>
                            <input type="number" name="productPurchasePrice" id="productPurchasePrice" step="0.01" min="0" class="mt-1 block w-full border border-gray-300 rounded-lg shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm" placeholder="Ej: 3500.00">
                        </div>
                        <div>
                            <label for="productSalePrice" class="block text-sm font-medium text-gray-700 text-left mb-1">Precio de Venta (MXN)</label>
                            <input type="number" name="productSalePrice" id="productSalePrice" required step="0.01" min="0" class="mt-1 block w-full border border-gray-300 rounded-lg shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm" placeholder="Ej: 4999.00">
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="productCategory" class="block text-sm font-medium text-gray-700 text-left mb-1">Categoría</label>
                            <select id="productCategory" name="productCategory" class="mt-1 block w-full border border-gray-300 rounded-lg shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                <option value="">Seleccione una categoría</option>
                                <option value="Electrónicos">Electrónicos</option>
                                <option value="Accesorios PC">Accesorios PC</option>
                                <option value="Ropa">Ropa</option>
                                <option value="Hogar">Hogar</option>
                                <option value="Muebles">Muebles</option>
                                </select>
                        </div>
                        <div>
                            <label for="productSupplier" class="block text-sm font-medium text-gray-700 text-left mb-1">Proveedor (Opcional)</label>
                            <input type="text" name="productSupplier" id="productSupplier" class="mt-1 block w-full border border-gray-300 rounded-lg shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm" placeholder="Nombre del proveedor">
                        </div>
                    </div>

                    <div class="pt-4 flex flex-col sm:flex-row justify-end space-y-3 sm:space-y-0 sm:space-x-3">
                        <button type="button" id="closeProductModalBtn" class="btn-secondary font-semibold w-full sm:w-auto py-2 px-6 rounded-lg shadow-md hover:shadow-lg transition duration-150 ease-in-out">
                            Cancelar
                        </button>
                        <button type="submit" class="btn-primary font-semibold w-full sm:w-auto py-2 px-6 rounded-lg shadow-md hover:shadow-lg transition duration-150 ease-in-out">
                            Guardar Producto
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div id="notificationContainer" class="fixed bottom-5 right-5 z-50">
        </div>


<script>
    // Referencias a elementos del DOM
    const openAddProductModalBtn = document.getElementById('openAddProductModalBtn');
    const closeProductModalBtn = document.getElementById('closeProductModalBtn');
    const productModal = document.getElementById('productModal');
    const productForm = document.getElementById('productForm');
    const modalTitle = document.getElementById('modalTitle');
    const productTableBody = document.getElementById('productTableBody');
    const searchInput = document.getElementById('searchInput');
    const notificationContainer = document.getElementById('notificationContainer');

    // Elementos de paginación
    const paginationControls = document.getElementById('paginationControls');
    const showingFrom = document.getElementById('showingFrom');
    const showingTo = document.getElementById('showingTo');
    const totalResults = document.getElementById('totalResults');
    const prevPageBtn = document.getElementById('prevPageBtn');
    const nextPageBtn = document.getElementById('nextPageBtn');

    const ITEMS_PER_PAGE = 5; // Número de productos por página
    let currentPage = 1;
    let currentFilteredProducts = []; // Para mantener los productos filtrados por búsqueda

    let editingProductId = null; 
    let products = []; // El array de productos se cargará desde localStorage

    // Clave para localStorage
    const LOCAL_STORAGE_KEY = 'posInventoryProducts';

    // --- Funciones de Persistencia con localStorage ---
    function loadProductsFromLocalStorage() {
        const storedProducts = localStorage.getItem(LOCAL_STORAGE_KEY);
        if (storedProducts) {
            try {
                return JSON.parse(storedProducts);
            } catch (e) {
                console.error("Error al parsear productos desde localStorage:", e);
                // Si hay error, retornar datos por defecto y guardar
                return getDefaultProductsAndSave();
            }
        }
        // Si no hay datos, usar los de ejemplo y guardarlos
        return getDefaultProductsAndSave();
    }

    function getDefaultProductsAndSave() {
        const defaultProducts = [
            { id: 'PROD1687123456789', code: 'PROD001', name: 'Laptop Gamer XZ', description: 'Laptop de alto rendimiento para juegos y trabajo pesado.', stock: 15, purchasePrice: 20000, salePrice: 25000, category: 'Electrónicos', supplier: 'TechGlobal' },
            { id: 'PROD1687123456790', code: 'PROD002', name: 'Mouse Inalámbrico Ergo', description: 'Mouse ergonómico para mayor comodidad durante horas de uso.', stock: 3, purchasePrice: 600, salePrice: 850, category: 'Accesorios PC', supplier: 'OfficeComfort' },
            { id: 'PROD1687123456791', code: 'PROD003', name: 'Teclado Mecánico RGB', description: 'Teclado mecánico con iluminación RGB personalizable.', stock: 25, purchasePrice: 1200, salePrice: 1700, category: 'Accesorios PC', supplier: 'GamingGear' },
            { id: 'PROD1687123456792', code: 'PROD004', name: 'Monitor LED 24"', description: 'Monitor Full HD para trabajo y entretenimiento.', stock: 8, purchasePrice: 2500, salePrice: 3200, category: 'Electrónicos', supplier: 'ViewMax' },
            { id: 'PROD1687123456793', code: 'PROD005', name: 'Silla Gamer Ergonómica', description: 'Silla con soporte lumbar y diseño deportivo.', stock: 0, purchasePrice: 3000, salePrice: 4500, category: 'Muebles', supplier: 'ComfortZone' },
            { id: 'PROD1687123456794', code: 'PROD006', name: 'Webcam HD 1080p', description: 'Webcam con micrófono integrado para videollamadas.', stock: 12, purchasePrice: 700, salePrice: 950, category: 'Accesorios PC', supplier: 'TechGlobal' },
            { id: 'PROD1687123456795', code: 'PROD007', name: 'Disco Duro Externo 1TB', description: 'Almacenamiento portátil USB 3.0.', stock: 7, purchasePrice: 1000, salePrice: 1300, category: 'Almacenamiento', supplier: 'StorageInc' }
        ];
        localStorage.setItem(LOCAL_STORAGE_KEY, JSON.stringify(defaultProducts));
        return defaultProducts;
    }

    function saveProductsToLocalStorage() {
        localStorage.setItem(LOCAL_STORAGE_KEY, JSON.stringify(products));
    }

    // --- Funciones del Modal ---
    function openModal(isEdit = false, product = null) {
        productForm.reset(); 
        if (isEdit && product) {
            modalTitle.textContent = 'Editar Producto';
            editingProductId = product.id;
            document.getElementById('productCode').value = product.code || '';
            document.getElementById('productName').value = product.name || '';
            document.getElementById('productDescription').value = product.description || '';
            document.getElementById('productStock').value = product.stock === undefined ? '' : product.stock;
            document.getElementById('productPurchasePrice').value = product.purchasePrice === undefined ? '' : product.purchasePrice;
            document.getElementById('productSalePrice').value = product.salePrice === undefined ? '' : product.salePrice;
            document.getElementById('productCategory').value = product.category || '';
            document.getElementById('productSupplier').value = product.supplier || '';
        } else {
            modalTitle.textContent = 'Agregar Nuevo Producto';
            editingProductId = null;
            // Generar un ID único para el nuevo producto (más robusto que el código)
            const newId = `PROD${Date.now()}`;
            // Sugerir un nuevo código de producto (el usuario puede cambiarlo)
            // Encontrar el número más alto en los códigos existentes PRODXXX y sumar 1
            let maxCodeNum = 0;
            products.forEach(p => {
                if (p.code && p.code.startsWith('PROD')) {
                    const num = parseInt(p.code.substring(4));
                    if (!isNaN(num) && num > maxCodeNum) {
                        maxCodeNum = num;
                    }
                }
            });
            document.getElementById('productCode').value = `PROD${String(maxCodeNum + 1).padStart(3, '0')}`;
        }
        productModal.classList.add('active');
    }

    function closeModal() {
        productModal.classList.remove('active');
        editingProductId = null;
    }

    // --- Funciones de Notificación ---
    function showNotification(message, type = 'success') {
        const notification = document.createElement('div');
        // Clases de Tailwind para estilo y animación de entrada/salida
        notification.className = `p-4 mb-3 rounded-lg shadow-lg text-white transform transition-all duration-300 ease-in-out ${type === 'success' ? 'bg-green-500' : (type === 'error' ? 'bg-red-500' : 'bg-blue-500')} translate-x-full opacity-0`;
        notification.textContent = message;
        notificationContainer.appendChild(notification);

        // Forzar reflujo para que la transición de entrada funcione
        requestAnimationFrame(() => {
            notification.classList.remove('translate-x-full', 'opacity-0');
            notification.classList.add('translate-x-0', 'opacity-100');
        });
        
        setTimeout(() => {
            notification.classList.remove('translate-x-0', 'opacity-100');
            notification.classList.add('translate-x-full', 'opacity-0');
            setTimeout(() => notification.remove(), 300); // Remover del DOM después de la animación
        }, 3000);
    }
    
    // --- Funciones CRUD de Productos ---
    function renderProductsDisplay() {
        productTableBody.innerHTML = ''; // Limpiar tabla existente
        
        const productsToDisplay = currentFilteredProducts; // Usar la lista filtrada (o completa si no hay filtro)
        const totalItems = productsToDisplay.length;

        if (totalItems === 0 && searchInput.value.trim() !== '') {
             const row = productTableBody.insertRow();
            const cell = row.insertCell();
            cell.colSpan = 7;
            cell.className = 'px-6 py-12 text-center text-sm text-gray-500';
            cell.textContent = 'No se encontraron productos que coincidan con tu búsqueda.';
            paginationControls.style.display = 'none'; // Ocultar paginación si no hay resultados de búsqueda
            return;
        } else if (totalItems === 0) {
            const row = productTableBody.insertRow();
            const cell = row.insertCell();
            cell.colSpan = 7;
            cell.className = 'px-6 py-12 text-center text-sm text-gray-500';
            cell.innerHTML = 'No hay productos en el inventario. <br/>¡Comienza agregando uno nuevo!';
            paginationControls.style.display = 'none'; // Ocultar paginación si no hay productos
            return;
        }

        paginationControls.style.display = 'flex'; // Mostrar paginación si hay productos

        const startIndex = (currentPage - 1) * ITEMS_PER_PAGE;
        const endIndex = startIndex + ITEMS_PER_PAGE;
        const paginatedItems = productsToDisplay.slice(startIndex, endIndex);

        paginatedItems.forEach(product => {
            const row = productTableBody.insertRow();
            row.className = 'hover:bg-gray-50 transition duration-150 ease-in-out';
            if (product.stock === 0) {
                row.classList.add('bg-red-50'); 
            } else if (product.stock > 0 && product.stock < 5) { // Alerta de bajo stock (ej. < 5)
                 row.classList.add('bg-yellow-50'); 
            }

            row.innerHTML = `
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">${product.code}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">${product.name}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 max-w-xs truncate" title="${product.description}">${product.description ? product.description.substring(0,40) + (product.description.length > 40 ? '...' : '') : '-'}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full ${product.stock === 0 ? 'bg-red-100 text-red-800' : (product.stock < 5 ? 'bg-yellow-100 text-yellow-800' : 'bg-green-100 text-green-800')}">
                        ${product.stock}
                    </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">$${parseFloat(product.salePrice).toFixed(2)}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${product.category || '-'}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                    <button class="text-blue-600 hover:text-blue-800 transition duration-150 ease-in-out icon-button edit-btn" data-id="${product.id}" title="Editar Producto">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button class="text-red-600 hover:text-red-800 transition duration-150 ease-in-out icon-button delete-btn" data-id="${product.id}" title="Eliminar Producto">
                        <i class="fas fa-trash-alt"></i>
                    </button>
                </td>
            `;
        });
        updatePaginationUI(totalItems);
        attachActionListeners(); 
    }

    function applySearchFilter() {
        const searchTerm = searchInput.value.toLowerCase().trim();
        if (searchTerm === '') {
            currentFilteredProducts = [...products]; // Mostrar todos si no hay búsqueda
        } else {
            currentFilteredProducts = products.filter(product => 
                (product.name && product.name.toLowerCase().includes(searchTerm)) || 
                (product.code && product.code.toLowerCase().includes(searchTerm)) ||
                (product.category && product.category.toLowerCase().includes(searchTerm))
            );
        }
        currentPage = 1; // Resetear a la primera página con cada nueva búsqueda
        renderProductsDisplay();
    }


    function addProduct(productData) {
        const newProduct = { 
            id: `PROD${Date.now()}`, // ID único basado en timestamp
            ...productData 
        };
        products.unshift(newProduct); // Agregar al inicio para ver el nuevo producto primero
        saveProductsToLocalStorage(); // Guardar en localStorage
        applySearchFilter(); // Re-renderizar y aplicar filtro actual (o mostrar todos)
        showNotification('Producto agregado exitosamente.', 'success');
    }

    function updateProduct(productId, productData) {
        const productIndex = products.findIndex(p => p.id === productId);
        if (productIndex > -1) {
            products[productIndex] = { ...products[productIndex], ...productData };
            saveProductsToLocalStorage(); // Guardar en localStorage
            applySearchFilter(); // Re-renderizar
            showNotification('Producto actualizado exitosamente.', 'success');
        } else {
            showNotification('Error: Producto no encontrado.', 'error');
        }
    }

    function deleteProduct(productId) {
        if (confirm(`¿Estás seguro de que deseas eliminar este producto? Esta acción no se puede deshacer.`)) {
            products = products.filter(p => p.id !== productId);
            saveProductsToLocalStorage(); // Guardar en localStorage
            applySearchFilter(); // Re-renderizar
            showNotification('Producto eliminado exitosamente.', 'info');
        }
    }

    // --- Paginación ---
    function updatePaginationUI(totalItems) {
        const totalPages = Math.ceil(totalItems / ITEMS_PER_PAGE);
        
        showingFrom.textContent = totalItems === 0 ? 0 : (currentPage - 1) * ITEMS_PER_PAGE + 1;
        showingTo.textContent = Math.min(currentPage * ITEMS_PER_PAGE, totalItems);
        totalResults.textContent = totalItems;

        prevPageBtn.disabled = currentPage === 1;
        nextPageBtn.disabled = currentPage === totalPages || totalItems === 0;

        if (totalPages <= 1) {
            paginationControls.style.display = 'none';
        } else {
            paginationControls.style.display = 'flex';
        }
    }

    prevPageBtn.addEventListener('click', () => {
        if (currentPage > 1) {
            currentPage--;
            renderProductsDisplay();
        }
    });

    nextPageBtn.addEventListener('click', () => {
        const totalPages = Math.ceil(currentFilteredProducts.length / ITEMS_PER_PAGE);
        if (currentPage < totalPages) {
            currentPage++;
            renderProductsDisplay();
        }
    });
    
    // --- Manejadores de Eventos ---
    openAddProductModalBtn.addEventListener('click', () => openModal());
    closeProductModalBtn.addEventListener('click', closeModal);

    productModal.addEventListener('click', (event) => {
        if (event.target === productModal) {
            closeModal();
        }
    });
    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape' && productModal.classList.contains('active')) {
            closeModal();
        }
    });

    productForm.addEventListener('submit', function(event) {
        event.preventDefault();
        const formData = new FormData(event.target);
        const productData = {
            code: formData.get('productCode'),
            name: formData.get('productName'),
            description: formData.get('productDescription'),
            stock: parseInt(formData.get('productStock')),
            purchasePrice: parseFloat(formData.get('productPurchasePrice')),
            salePrice: parseFloat(formData.get('productSalePrice')),
            category: formData.get('productCategory'),
            supplier: formData.get('productSupplier')
        };

        // Validación simple
        if (!productData.name || !productData.code || isNaN(productData.stock) || isNaN(productData.salePrice)) {
            showNotification('Por favor, complete los campos requeridos (Código, Nombre, Stock, Precio Venta).', 'error');
            return;
        }


        if (editingProductId) {
            updateProduct(editingProductId, productData);
        } else {
            // Verificar si el código de producto ya existe al agregar uno nuevo
            if (products.some(p => p.code === productData.code)) {
                showNotification(`El código de producto '${productData.code}' ya existe. Por favor, use uno diferente.`, 'error');
                return;
            }
            addProduct(productData);
        }
        closeModal();
    });

    searchInput.addEventListener('input', applySearchFilter);

    function attachActionListeners() {
        document.querySelectorAll('.edit-btn').forEach(button => {
            // Remover listener anterior para evitar duplicados si se llama múltiples veces
            button.replaceWith(button.cloneNode(true)); 
        });
        document.querySelectorAll('.delete-btn').forEach(button => {
            button.replaceWith(button.cloneNode(true));
        });

        // Volver a agregar listeners a los nuevos nodos clonados
        document.querySelectorAll('.edit-btn').forEach(button => {
            button.addEventListener('click', function() {
                const productId = this.dataset.id;
                const productToEdit = products.find(p => p.id === productId);
                if (productToEdit) {
                    openModal(true, productToEdit);
                }
            });
        });

        document.querySelectorAll('.delete-btn').forEach(button => {
            button.addEventListener('click', function() {
                const productId = this.dataset.id;
                deleteProduct(productId);
            });
        });
    }

    // --- Inicialización ---
    document.addEventListener('DOMContentLoaded', () => {
        products = loadProductsFromLocalStorage(); // Cargar productos
        applySearchFilter(); // Aplicar filtro inicial (mostrar todos) y renderizar
    });

</script>

</body>
</html>
