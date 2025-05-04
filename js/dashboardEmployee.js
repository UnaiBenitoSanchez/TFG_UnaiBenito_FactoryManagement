$(document).ready(function () {
    // Charge data at start
    loadData();

    // Function to charge data by AJAX
    function loadData() {
        $.ajax({
            url: 'getDataEmployee.php',
            method: 'GET',
            dataType: 'json',
            success: function (data) {
                console.log(data);
                renderProducts(data);
            },
            error: function (error) {
                console.error('Error:', error);
            }
        });
    }

    // Function to render the products on the container
    function renderProducts(data) {
        let productsContainer = $('#products-container');
        productsContainer.empty();

        let totalProducts = 0;

        // Calculate the total of the products
        data.forEach(function (product) {
            totalProducts += parseFloat(product.available_quantity);
        });

        // Render every product
        data.forEach(function (product) {
            console.log("Image:", product.image);
            let productDiv = createProductCard(product, totalProducts);
            productsContainer.append(productDiv);
        });

        // Add modal to body if not exists
        if ($('#productDetailModal').length === 0) {
            $('body').append(`
                <div class="modal fade" id="productDetailModal" tabindex="-1" aria-labelledby="productDetailModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="productDetailModalLabel">Product Details</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body" id="productDetailContent">
                                <!-- Product details will be injected here -->
                            </div>
                        </div>
                    </div>
                </div>
            `);
        }
    }

    // Function to create the product card
    function createProductCard(product, totalProducts) {
        let availableQuantity = parseFloat(product.available_quantity);
        let price = parseFloat(product.price);
        let percentage = totalProducts !== 0 ? (availableQuantity / totalProducts) * 100 : 0;

        let productDiv = $('<div>').addClass('col-md-4 mb-6');
        productDiv.html('<div class="card product-card" data-product-id="' + product.id_product + '">\
                <div class="card-body">\
                    <h5 class="card-title">' + product.name + '</h5>\
                    <div class="product-image-container">\
                        <img src="../' + product.image + '" class="product-image" alt="Product Image" onerror="handleImageError(this)">\
                    </div>\
                    <p class="card-text">Quantity: ' + availableQuantity + '</p>\
                    <p class="card-text">Price: $<span class="price-editable" data-product-id="' + product.id_product + '">' + price.toFixed(2) + '</span></p>\
                    <p>Percentage from the total:</p>\
                    <div class="progress">\
                        <div class="progress-bar" role="progressbar" style="width: ' + percentage + '%;" aria-valuenow="' + percentage + '" aria-valuemin="0" aria-valuemax="100">\
                            <span class="percentage-text">' + percentage.toFixed(2) + '%</span>\
                        </div>\
                    </div>\
                    <!-- Description and buttons hidden in card view -->\
                </div>\
                <div class="card-footer text-center">\
                    <small class="text-light">Click for details and options</small>\
                </div>\
            </div>');

        // Store full product data for modal
        productDiv.data('product-data', product);

        return productDiv;
    }    

    // Handle card click to show modal
    $(document).on('click', '.product-card', function(e) {
        // Don't open modal if clicking on buttons
        if ($(e.target).hasClass('btn') || $(e.target).closest('.btn').length) {
            return;
        }
        
        const productId = $(this).data('product-id');
        const productData = $(this).closest('.col-md-4').data('product-data');
        
        // Display full product details in modal
        let modalContent = `
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-5">
                        <div class="modal-product-image-container">
                            <img src="../${productData.image}" class="modal-product-image" alt="Product Image" onerror="handleImageError(this)">
                        </div>
                    </div>
                    <div class="col-md-7">
                        <h3>${productData.name}</h3>
                        <div class="product-details">
                            <p><strong>Description:</strong> ${productData.description}</p>
                            <p><strong>Quantity:</strong> ${productData.available_quantity}</p>
                            <p><strong>Price:</strong> ${parseFloat(productData.price).toFixed(2)}</p>
                        </div>
                        
                        <div class="mt-4 modal-action-buttons">
                            <button class="btn btn-primary btn-modal-edit" data-product-id="${productData.id_product}">Edit Product</button>
                            <button class="btn btn-danger btn-modal-delete" data-product-id="${productData.id_product}">Delete Product</button>
                        </div>
                    </div>
                </div>
            </div>
        `;
        
        $('#productDetailContent').html(modalContent);
        const productModal = new bootstrap.Modal(document.getElementById('productDetailModal'));
        productModal.show();
    });

    // Handle edit from modal
    $(document).on('click', '.btn-modal-edit', function() {
        const productId = $(this).data('product-id');
        const modalBody = $('#productDetailContent');
        const productData = $(`[data-product-id="${productId}"]`).closest('.col-md-4').data('product-data');
        
        let editForm = `
            <div class="container-fluid">
                <div class="row">
                    <div class="col-12">
                        <div class="mb-3">
                            <label for="modal-product-name" class="form-label">Product Name</label>
                            <input type="text" class="form-control" id="modal-product-name" value="${productData.name}">
                        </div>
                        <div class="mb-3">
                            <label for="modal-product-description" class="form-label">Product Description</label>
                            <textarea class="form-control" id="modal-product-description" rows="3">${productData.description}</textarea>
                        </div>
                        <div class="mb-3">
                            <label for="modal-product-quantity" class="form-label">Quantity</label>
                            <input type="number" class="form-control" id="modal-product-quantity" value="${productData.available_quantity}">
                        </div>
                        <div class="mb-3">
                            <label for="modal-product-price" class="form-label">Price</label>
                            <input type="number" step="0.01" class="form-control" id="modal-product-price" value="${productData.price}">
                        </div>
                        <button class="btn btn-success btn-modal-confirm" data-product-id="${productId}">Save Changes</button>
                        <button class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    </div>
                </div>
            </div>
        `;
        
        modalBody.html(editForm);
    });

    // Handle save changes from modal
    $(document).on('click', '.btn-modal-confirm', function() {
        const productId = $(this).data('product-id');
        const newName = $('#modal-product-name').val();
        const newDescription = $('#modal-product-description').val();
        const newQuantity = $('#modal-product-quantity').val();
        const newPrice = $('#modal-product-price').val();
        
        $.ajax({
            url: 'updateData.php',
            method: 'POST',
            data: {
                id_product: productId,
                new_name: newName,
                new_description: newDescription,
                new_quantity: newQuantity,
                new_price: newPrice
            },
            success: function(response) {
                console.log(response);
                // Close modal and reload data
                $('#productDetailModal').modal('hide');
                loadData();
            },
            error: function(error) {
                console.error('Error:', error);
            }
        });
    });

    // Handle delete from modal
    $(document).on('click', '.btn-modal-delete', function() {
        const productId = $(this).data('product-id');
        
        if (confirm('Are you sure you want to delete this product?')) {
            $.ajax({
                url: 'deleteData.php',
                method: 'POST',
                data: {
                    id_product: productId
                },
                success: function(response) {
                    console.log(response);
                    $('#productDetailModal').modal('hide');
                    loadData();
                },
                error: function(error) {
                    console.error('Error:', error);
                }
            });
        }
    });

    // Manage the click on the edit button
    $(document).on('click', '.btn-edit', function(e) {
        e.stopPropagation(); // Prevent card click
        handleEditButtonClick($(this));
    });

    // Manage the click on the confirm button
    $(document).on('click', '.btn-confirm', function(e) {
        e.stopPropagation(); // Prevent card click
        handleConfirmButtonClick($(this));
    });

    // Manage the click on the delete button
    $(document).on('click', '.btn-delete', function(e) {
        e.stopPropagation(); // Prevent card click
        handleDeleteButtonClick($(this));
    });

    // Function to manage the click on the edit button
    function handleEditButtonClick(button) {
        let productDiv = button.closest('.card-body');
        let name = productDiv.find('.card-title').text();
        let description = productDiv.find('.description-text').text();
        let quantity = productDiv.find('.card-text:eq(1)').text().trim().split(':')[1];
        let price = productDiv.find('.price-editable').text();

        let descriptionPart = description.replace(/^Product description:\s*/, '');
        let quantityPart = quantity.replace(/^Quantity:\s*/, '');

        productDiv.find('.card-title').html('<input type="text" class="form-control" value="' + name + '">');
        productDiv.find('.description-text').html('<p>Product description:</p><textarea class="form-control">' + descriptionPart + '</textarea>');
        productDiv.find('.card-text:eq(1)').html('<p>Quantity:</p><input type="text" class="form-control" value="' + quantityPart + '">');
        productDiv.find('.price-editable').html('<input type="text" class="form-control" value="' + price + '">');

        button.removeClass('btn-edit').addClass('btn-confirm').text('Confirm');
    }    

    // Function to manage the click on the confirm button
    function handleConfirmButtonClick(button) {
        let productDiv = button.closest('.card-body');
        let productId = button.data('product-id');
        let newName = productDiv.find('.card-title input').val();
        let newDescription = productDiv.find('.card-text textarea').val();
        let newQuantity = productDiv.find('.card-text:eq(1) input').val();
        let newPrice = productDiv.find('.price-editable input').val();

        $.ajax({
            url: 'updateData.php',
            method: 'POST',
            data: {
                id_product: productId,
                new_name: newName,
                new_description: newDescription,
                new_quantity: newQuantity,
                new_price: newPrice
            },
            success: function (response) {
                console.log(response);
                loadData();
            },
            error: function (error) {
                console.error('Error:', error);
            }
        });
    }

    // Function to manage the click on the delete button
    function handleDeleteButtonClick(button) {
        let productId = button.data('product-id');

        if (confirm('Are you sure you want to delete this product?')) {
            $.ajax({
                url: 'deleteData.php',
                method: 'POST',
                data: {
                    id_product: productId
                },
                success: function (response) {
                    console.log(response);
                    loadData();
                },
                error: function (error) {
                    console.error('Error:', error);
                }
            });
        }
    }
    
    // Helper function to handle image errors
    window.handleImageError = function(img) {
        img.onerror = null;
        img.src = '../img/placeholder.png';
    }
});