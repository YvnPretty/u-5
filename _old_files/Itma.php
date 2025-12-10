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
            background-color: #f4f7f6; /* Un gris más suave para el fondo */
            color: #333;
        }
        .table-header-group th {
            background-color: #e9ecef; /* Un gris ligeramente más oscuro para cabeceras */
            color: #495057;
            font-weight: 600; /* Ligeramente más audaz */
        }
        .btn-primary {
            background-color: #007bff; /* Azul primario estándar */
            color: white;
            transition: background-color 0.3s ease;
        }
        .btn-primary:hover {
            background-color: #0056b3;
        }
        .btn-secondary {
            background-color: #6c757d;
            color: white;
            transition: background-color 0.3s ease;
        }
        .btn-secondary:hover {
            background-color: #545b62;
        }
        .btn-danger {
            background-color: #dc3545;
            color: white;
            transition: background-color 0.3s ease;
        }
        .btn-danger:hover {
            background-color: #b02a37;
        }
        .icon-button {
            padding: 0.5rem;
            border-radius: 0.375rem; /* rounded-md */
        }
        .icon-button:hover {
            background-color: #e9ecef;
        }
        .modal {
            display: none; /* Oculto por defecto */
            transition: opacity 0.3s ease;
        }
        .modal.active {
            display: flex; /* Mostrar cuando está activo */
            opacity: 1;
        }
        .modal-content {
            transform: translateY(-20px);
            transition: transform 0.3s ease-out;
        }
        .modal.active .modal-content {
            transform: translateY(0);
        }

        /* Estilos para la notificación */
        .notification {
            transition: opacity 0.5s ease-in-out, transform 0.5s ease-in-out;
            opacity: 0;
            transform: translateY(20px);
        }
        .notification.show {
            opacity: 1;
            transform: translateY(0);
        }
        .notification.success {
            background-color: #28a745; /* Verde éxito */
        }
        .notification.error {
            background-color: #dc3545; /* Rojo error */
        }
        .notification.info {
            background-color: #17a2b8; /* Azul info */
        }
        /* Mejoras en la tabla */
        .table {
            border-collapse: separate;
            border-spacing: 0;
            border-radius: 0.5rem; /* esquinas redondeadas para la tabla */
            overflow: hidden; /* para que el border-radius afecte a las celdas */
        }
        .table th, .table td {
            border-bottom: 1px solid #dee2e6; /* Línea divisoria más sutil */
        }
        .table th:first-child, .table td:first-child {
            border-left: none;
        }
        .table th:last-child, .table td:last-child {
            border-right: none;
        }
        .table tr:last-child td {
            border-bottom: none; /* No borde inferior en la última fila */
        }
         /* Estilos para placeholder de búsqueda */
        input::placeholder {
            color: #6c757d; /* Gris más oscuro para mejor contraste */
            opacity: 0.8;
        }
    </style>
</head>
<body class="bg-gray-100 text-gray-800">

    <div class="container mx-auto p-4 md:p-8">

        <header class="mb-8 text-center">
            <h1 class="text-4xl font-bold text-gray-700">Gestión de Inventario</h1>
            <p class="text-gray-500 mt-2">Administra tus productos, stock y precios. Los datos se guardan en tu navegador.</p>
        </header>

        <div class="mb-6 flex flex-col sm:flex-row justify-between items-center gap-4 p-4 bg-white shadow-md rounded-lg">
            <div class="relative w-full sm:w-auto flex-grow">
                <input
                    type="text"
                    id="searchInput"
                    placeholder="Buscar por nombre, código o categoría..."
                    class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm"
                >
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <i class="fas fa-search text-gray-400"></i>
                </div>
            </div>
            <button id="openAddProductModalBtn" class="btn-primary font-semibold py-3 px-6 rounded-lg shadow-md hover:shadow-lg transition duration-150 ease-in-out w-full sm:w-auto flex items-center justify-center text-sm">
                <i class="fas fa-plus mr-2"></i>Agregar Producto
            </button>
        </div>

        <div class="bg-white shadow-xl rounded-lg overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 table">
                <thead class="table-header-group">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Código</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nombre</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Descripción</th>
                        <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Stock</th>
                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Precio (MXN)</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Categoría</th>
                        <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                    </tr>
                </thead>
                <tbody id="productTableBody" class="bg-white divide-y divide-gray-200">
                    </tbody>
            </table>
            <div id="paginationControls" class="px-6 py-4 border-t border-gray-200 flex items-center justify-between">
                <p class="text-sm text-gray-700">
                    Mostrando <span id="showingFrom" class="font-semibold">1</span> a <span id="showingTo" class="font-semibold">0</span> de <span id="totalResults" class="font-semibold">0</span> resultados
                </p>
                <div class="flex space-x-2">
                    <button id="prevPageBtn" class="px-4 py-2 border border-gray-300 rounded-md text-sm hover:bg-gray-100 transition duration-150 ease-in-out disabled:opacity-50 disabled:cursor-not-allowed flex items-center">
                        <i class="fas fa-chevron-left mr-1 text-xs"></i> Anterior
                    </button>
                    <button id="nextPageBtn" class="px-4 py-2 border border-gray-300 rounded-md text-sm hover:bg-gray-100 transition duration-150 ease-in-out disabled:opacity-50 disabled:cursor-not-allowed flex items-center">
                        Siguiente <i class="fas fa-chevron-right ml-1 text-xs"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div id="productModal" class="modal fixed inset-0 bg-black bg-opacity-60 overflow-y-auto h-full w-full items-center justify-center z-50 px-4">
        <div class="modal-content relative mx-auto p-6 border w-full max-w-2xl shadow-2xl rounded-xl bg-white">
            <div class="flex justify-between items-center mb-6 pb-3 border-b border-gray-200">
                <h3 class="text-2xl leading-6 font-bold text-gray-900" id="modalTitle">Agregar Nuevo Producto</h3>
                <button id="closeProductModalHeaderBtn" class="text-gray-400 hover:text-gray-600 transition duration-150 ease-in-out">
                    <i class="fas fa-times fa-lg"></i>
                </button>
            </div>
            <form id="productForm" class="space-y-6">
                <input type="hidden" id="productId" name="productId">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="productCode" class="block text-sm font-medium text-gray-700 text-left mb-1">Código del Producto <span class="text-red-500">*</span></label>
                        <input type="text" name="productCode" id="productCode" required class="mt-1 block w-full border border-gray-300 rounded-lg shadow-sm py-2.5 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm" placeholder="Ej: PROD001">
                    </div>
                    <div>
                        <label for="productName" class="block text-sm font-medium text-gray-700 text-left mb-1">Nombre del Producto <span class="text-red-500">*</span></label>
                        <input type="text" name="productName" id="productName" required class="mt-1 block w-full border border-gray-300 rounded-lg shadow-sm py-2.5 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm" placeholder="Ej: Monitor Curvo 27 pulgadas">
                    </div>
                </div>

                <div>
                    <label for="productDescription" class="block text-sm font-medium text-gray-700 text-left mb-1">Descripción</label>
                    <textarea id="productDescription" name="productDescription" rows="3" class="mt-1 block w-full border border-gray-300 rounded-lg shadow-sm py-2.5 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm" placeholder="Información detallada del producto..."></textarea>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label for="productStock" class="block text-sm font-medium text-gray-700 text-left mb-1">Stock Actual <span class="text-red-500">*</span></label>
                        <input type="number" name="productStock" id="productStock" required min="0" class="mt-1 block w-full border border-gray-300 rounded-lg shadow-sm py-2.5 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm" placeholder="Ej: 50">
                    </div>
                    <div>
                        <label for="productPurchasePrice" class="block text-sm font-medium text-gray-700 text-left mb-1">Precio de Compra (MXN)</label>
                        <input type="number" name="productPurchasePrice" id="productPurchasePrice" step="0.01" min="0" class="mt-1 block w-full border border-gray-300 rounded-lg shadow-sm py-2.5 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm" placeholder="Ej: 3500.00">
                    </div>
                    <div>
                        <label for="productSalePrice" class="block text-sm font-medium text-gray-700 text-left mb-1">Precio de Venta (MXN) <span class="text-red-500">*</span></label>
                        <input type="number" name="productSalePrice" id="productSalePrice" required step="0.01" min="0" class="mt-1 block w-full border border-gray-300 rounded-lg shadow-sm py-2.5 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm" placeholder="Ej: 4999.00">
                    </div>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="productCategory" class="block text-sm font-medium text-gray-700 text-left mb-1">Categoría</label>
                        <select id="productCategory" name="productCategory" class="mt-1 block w-full border border-gray-300 rounded-lg shadow-sm py-2.5 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                            <option value="">Seleccione una categoría</option>
                            <option value="Electrónicos">Electrónicos</option>
                            <option value="Accesorios PC">Accesorios PC</option>
                            <option value="Almacenamiento">Almacenamiento</option>
                            <option value="Ropa">Ropa</option>
                            <option value="Hogar">Hogar</option>
                            <option value="Muebles">Muebles</option>
                            <option value="Otro">Otro</option>
                        </select>
                    </div>
                    <div>
                        <label for="productSupplier" class="block text-sm font-medium text-gray-700 text-left mb-1">Proveedor (Opcional)</label>
                        <input type="text" name="productSupplier" id="productSupplier" class="mt-1 block w-full border border-gray-300 rounded-lg shadow-sm py-2.5 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm" placeholder="Nombre del proveedor">
                    </div>
                </div>

                <div class="pt-6 flex flex-col sm:flex-row justify-end space-y-3 sm:space-y-0 sm:space-x-4">
                    <button type="button" id="closeProductModalBtn" class="btn-secondary font-semibold w-full sm:w-auto py-2.5 px-6 rounded-lg shadow-md hover:shadow-lg transition duration-150 ease-in-out">
                        Cancelar
                    </button>
                    <button type="submit" id="saveProductBtn" class="btn-primary font-semibold w-full sm:w-auto py-2.5 px-6 rounded-lg shadow-md hover:shadow-lg transition duration-150 ease-in-out">
                        Guardar Producto
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div id="notificationContainer" class="fixed bottom-5 right-5 z-[100] space-y-3">
        </div>

<script>
    // Esperar a que el DOM esté completamente cargado
    document.addEventListener('DOMContentLoaded', () => {
        // Referencias a elementos del DOM
        const openAddProductModalBtn = document.getElementById('openAddProductModalBtn');
        const closeProductModalBtn = document.getElementById('closeProductModalBtn');
        const closeProductModalHeaderBtn = document.getElementById('closeProductModalHeaderBtn');
        const productModal = document.getElementById('productModal');
        const productForm = document.getElementById('productForm');
        const modalTitle = document.getElementById('modalTitle');
        const productTableBody = document.getElementById('productTableBody');
        const searchInput = document.getElementById('searchInput');
        const notificationContainer = document.getElementById('notificationContainer');
        const saveProductBtn = document.getElementById('saveProductBtn');
        const hiddenProductIdField = document.getElementById('productId'); // Campo oculto para el ID

        // Elementos de paginación
        const paginationControls = document.getElementById('paginationControls');
        const showingFrom = document.getElementById('showingFrom');
        const showingTo = document.getElementById('showingTo');
        const totalResults = document.getElementById('totalResults');
        const prevPageBtn = document.getElementById('prevPageBtn');
        const nextPageBtn = document.getElementById('nextPageBtn');

        const ITEMS_PER_PAGE = 5; // Número de productos por página
        let currentPage = 1;
        let products = []; // Array principal de productos
        let currentFilteredProducts = []; // Productos después de aplicar el filtro de búsqueda

        const LOCAL_STORAGE_KEY = 'posInventoryProducts_v2'; // Clave para localStorage (versionada por si acaso)

        // --- Funciones de Persistencia con localStorage ---
        function loadProductsFromLocalStorage() {
            const storedProducts = localStorage.getItem(LOCAL_STORAGE_KEY);
            if (storedProducts) {
                try {
                    return JSON.parse(storedProducts);
                } catch (e) {
                    console.error("Error al parsear productos desde localStorage:", e);
                    return getDefaultProductsAndSave(); // Retornar y guardar datos por defecto si hay error
                }
            }
            return getDefaultProductsAndSave(); // Si no hay datos, usar los de ejemplo y guardarlos
        }

        function getDefaultProductsAndSave() {
            const defaultProducts = [
                { id: `PROD${Date.now() + 1}`, code: 'PROD001', name: 'Laptop Gamer XZ', description: 'Laptop de alto rendimiento para juegos y trabajo pesado.', stock: 15, purchasePrice: 20000, salePrice: 25000, category: 'Electrónicos', supplier: 'TechGlobal' },
                { id: `PROD${Date.now() + 2}`, code: 'PROD002', name: 'Mouse Inalámbrico Ergo', description: 'Mouse ergonómico para mayor comodidad.', stock: 3, purchasePrice: 600, salePrice: 850, category: 'Accesorios PC', supplier: 'OfficeComfort' },
                { id: `PROD${Date.now() + 3}`, code: 'PROD003', name: 'Teclado Mecánico RGB', description: 'Teclado con iluminación RGB personalizable.', stock: 25, purchasePrice: 1200, salePrice: 1700, category: 'Accesorios PC', supplier: 'GamingGear' },
                { id: `PROD${Date.now() + 4}`, code: 'PROD004', name: 'Monitor LED 24"', description: 'Monitor Full HD para trabajo y entretenimiento.', stock: 8, purchasePrice: 2500, salePrice: 3200, category: 'Electrónicos', supplier: 'ViewMax' },
                { id: `PROD${Date.now() + 5}`, code: 'PROD005', name: 'Silla Gamer Ergonómica', description: 'Silla con soporte lumbar y diseño deportivo.', stock: 0, purchasePrice: 3000, salePrice: 4500, category: 'Muebles', supplier: 'ComfortZone' },
                { id: `PROD${Date.now() + 6}`, code: 'PROD006', name: 'Webcam HD 1080p', description: 'Webcam con micrófono integrado.', stock: 12, purchasePrice: 700, salePrice: 950, category: 'Accesorios PC', supplier: 'TechGlobal' },
                { id: `PROD${Date.now() + 7}`, code: 'PROD007', name: 'Disco Duro Externo 1TB', description: 'Almacenamiento portátil USB 3.0.', stock: 7, purchasePrice: 1000, salePrice: 1300, category: 'Almacenamiento', supplier: 'StorageInc' }
            ];
            localStorage.setItem(LOCAL_STORAGE_KEY, JSON.stringify(defaultProducts));
            return defaultProducts;
        }

        function saveProductsToLocalStorage() {
            localStorage.setItem(LOCAL_STORAGE_KEY, JSON.stringify(products));
        }

        // --- Funciones del Modal ---
        function openModal(isEdit = false, product = null) {
            productForm.reset(); // Limpiar formulario
            hiddenProductIdField.value = ''; // Limpiar ID oculto
            if (isEdit && product) {
                modalTitle.textContent = 'Editar Producto';
                saveProductBtn.textContent = 'Actualizar Producto';
                hiddenProductIdField.value = product.id; // Establecer el ID del producto a editar
                
                // Llenar el formulario con los datos del producto
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
                saveProductBtn.textContent = 'Guardar Producto';
                // Sugerir un nuevo código de producto (el usuario puede cambiarlo)
                let maxCodeNum = 0;
                products.forEach(p => {
                    if (p.code && p.code.toUpperCase().startsWith('PROD')) {
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
        }

        // --- Funciones de Notificación ---
        function showNotification(message, type = 'success') {
            const notification = document.createElement('div');
            notification.className = `notification p-4 mb-3 rounded-lg shadow-lg text-white text-sm ${type}`;
            notification.textContent = message;
            
            const icon = document.createElement('i');
            icon.className = `fas ${type === 'success' ? 'fa-check-circle' : (type === 'error' ? 'fa-exclamation-triangle' : 'fa-info-circle')} mr-2`;
            notification.prepend(icon);

            notificationContainer.appendChild(notification);

            // Forzar reflujo para animación de entrada
            requestAnimationFrame(() => {
                notification.classList.add('show');
            });
            
            setTimeout(() => {
                notification.classList.remove('show');
                setTimeout(() => notification.remove(), 500); // Remover del DOM después de la animación de salida
            }, 3000);
        }
        
        // --- Funciones CRUD de Productos ---
        function renderProductsDisplay() {
            productTableBody.innerHTML = ''; // Limpiar tabla existente
            
            const productsToDisplay = currentFilteredProducts;
            const totalItems = productsToDisplay.length;

            if (totalItems === 0 && searchInput.value.trim() !== '') {
                const row = productTableBody.insertRow();
                const cell = row.insertCell();
                cell.colSpan = 7; // Ajustar al número de columnas
                cell.className = 'px-6 py-12 text-center text-sm text-gray-500';
                cell.textContent = 'No se encontraron productos que coincidan con tu búsqueda.';
                paginationControls.style.display = 'none';
                return;
            } else if (totalItems === 0) {
                const row = productTableBody.insertRow();
                const cell = row.insertCell();
                cell.colSpan = 7; // Ajustar al número de columnas
                cell.className = 'px-6 py-12 text-center text-sm text-gray-500';
                cell.innerHTML = 'Aún no hay productos en el inventario. <br/>¡Comienza agregando uno nuevo!';
                paginationControls.style.display = 'none';
                return;
            }

            paginationControls.style.display = 'flex';

            const startIndex = (currentPage - 1) * ITEMS_PER_PAGE;
            const endIndex = startIndex + ITEMS_PER_PAGE;
            const paginatedItems = productsToDisplay.slice(startIndex, endIndex);

            paginatedItems.forEach(product => {
                const row = productTableBody.insertRow();
                row.className = 'hover:bg-gray-50 transition duration-150 ease-in-out';
                if (product.stock === 0) {
                    row.classList.add('bg-red-50', 'hover:bg-red-100'); 
                } else if (product.stock > 0 && product.stock < 5) {
                     row.classList.add('bg-yellow-50', 'hover:bg-yellow-100'); 
                }

                // Formatear precio a dos decimales
                const formattedPrice = product.salePrice !== undefined && product.salePrice !== null ? parseFloat(product.salePrice).toFixed(2) : '0.00';

                row.innerHTML = `
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">${product.code || '-'}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">${product.name || '-'}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 max-w-xs truncate" title="${product.description || ''}">${product.description ? product.description.substring(0,40) + (product.description.length > 40 ? '...' : '') : '-'}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 text-center">
                        <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full ${product.stock === 0 ? 'bg-red-100 text-red-800' : (product.stock < 5 ? 'bg-yellow-100 text-yellow-800' : 'bg-green-100 text-green-800')}">
                            ${product.stock !== undefined ? product.stock : 0}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 text-right">$${formattedPrice}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${product.category || '-'}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-center space-x-2">
                        <button class="text-blue-600 hover:text-blue-800 transition duration-150 ease-in-out icon-button edit-btn" data-id="${product.id}" title="Editar Producto">
                            <i class="fas fa-edit fa-fw"></i>
                        </button>
                        <button class="text-red-600 hover:text-red-800 transition duration-150 ease-in-out icon-button delete-btn" data-id="${product.id}" title="Eliminar Producto">
                            <i class="fas fa-trash-alt fa-fw"></i>
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
                currentFilteredProducts = [...products];
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
            saveProductsToLocalStorage();
            applySearchFilter(); // Re-renderizar y aplicar filtro actual
            showNotification('Producto agregado exitosamente.', 'success');
        }

        function updateProduct(productId, productData) {
            const productIndex = products.findIndex(p => p.id === productId);
            if (productIndex > -1) {
                // Mantener el ID original, actualizar el resto de los datos
                products[productIndex] = { ...products[productIndex], ...productData, id: productId };
                saveProductsToLocalStorage();
                applySearchFilter();
                showNotification('Producto actualizado exitosamente.', 'success');
            } else {
                showNotification('Error: Producto no encontrado para actualizar.', 'error');
            }
        }

        function deleteProduct(productId) {
            const productName = products.find(p => p.id === productId)?.name || 'este producto';
            if (confirm(`¿Estás seguro de que deseas eliminar "${productName}"? Esta acción no se puede deshacer.`)) {
                products = products.filter(p => p.id !== productId);
                saveProductsToLocalStorage();
                applySearchFilter(); // Re-renderizar
                // Si la página actual queda vacía después de eliminar, ir a la anterior si es posible
                const totalPages = Math.ceil(currentFilteredProducts.length / ITEMS_PER_PAGE);
                if (currentPage > totalPages && totalPages > 0) {
                    currentPage = totalPages;
                } else if (currentFilteredProducts.length === 0 && currentPage > 1) {
                    currentPage--;
                }
                renderProductsDisplay(); // Volver a renderizar por si cambió la página
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
        closeProductModalHeaderBtn.addEventListener('click', closeModal);


        productModal.addEventListener('click', (event) => {
            // Cerrar modal si se hace clic fuera del contenido del modal
            if (event.target === productModal) {
                closeModal();
            }
        });
        document.addEventListener('keydown', (event) => {
            // Cerrar modal con la tecla Escape
            if (event.key === 'Escape' && productModal.classList.contains('active')) {
                closeModal();
            }
        });

        productForm.addEventListener('submit', function(event) {
            event.preventDefault();
            const formData = new FormData(event.target);
            const editingProductId = hiddenProductIdField.value; // Obtener ID del campo oculto

            const productData = {
                code: formData.get('productCode').trim(),
                name: formData.get('productName').trim(),
                description: formData.get('productDescription').trim(),
                stock: parseInt(formData.get('productStock')),
                purchasePrice: formData.get('productPurchasePrice') ? parseFloat(formData.get('productPurchasePrice')) : null,
                salePrice: parseFloat(formData.get('productSalePrice')),
                category: formData.get('productCategory'),
                supplier: formData.get('productSupplier').trim()
            };

            // Validación simple
            if (!productData.name || !productData.code || isNaN(productData.stock) || productData.stock < 0 || isNaN(productData.salePrice) || productData.salePrice < 0) {
                showNotification('Por favor, complete los campos requeridos (Código, Nombre, Stock y Precio Venta deben ser válidos).', 'error');
                return;
            }

            if (editingProductId) { // Si hay un ID, estamos editando
                // Verificar si el código de producto ya existe (y no es el producto actual)
                if (products.some(p => p.code === productData.code && p.id !== editingProductId)) {
                    showNotification(`El código de producto '${productData.code}' ya está en uso por otro producto.`, 'error');
                    return;
                }
                updateProduct(editingProductId, productData);
            } else { // Si no, estamos agregando
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
            // Eliminar listeners anteriores para evitar duplicados, clonando y reemplazando los botones
            // Esto es una forma sencilla de hacerlo. Para aplicaciones más complejas, se podría usar event delegation.
            productTableBody.querySelectorAll('.edit-btn').forEach(button => {
                const newButton = button.cloneNode(true);
                button.parentNode.replaceChild(newButton, button);
                newButton.addEventListener('click', function() {
                    const productId = this.dataset.id;
                    const productToEdit = products.find(p => p.id === productId);
                    if (productToEdit) {
                        openModal(true, productToEdit);
                    }
                });
            });

            productTableBody.querySelectorAll('.delete-btn').forEach(button => {
                const newButton = button.cloneNode(true);
                button.parentNode.replaceChild(newButton, button);
                newButton.addEventListener('click', function() {
                    const productId = this.dataset.id;
                    deleteProduct(productId);
                });
            });
        }

        // --- Inicialización ---
        products = loadProductsFromLocalStorage(); // Cargar productos al iniciar
        applySearchFilter(); // Aplicar filtro inicial (mostrar todos) y renderizar
    });

</script>

</body>
</html>
