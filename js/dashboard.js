$(document).ready(function () {
    // Charge data at start
    loadData();

    // Function to charge data by AJAX
    function loadData() {
        $.ajax({
            url: 'getData.php',
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
    }

    // Function to create the product card
    function createProductCard(product, totalProducts) {
        let availableQuantity = parseFloat(product.available_quantity);
        let price = parseFloat(product.price);
        let percentage = totalProducts !== 0 ? (availableQuantity / totalProducts) * 100 : 0;

        let productDiv = $('<div>').addClass('col-md-4 mb-6');
        productDiv.html('<div class="card">\
                <div class="card-body">\
                    <h5 class="card-title">' + product.name + '</h5>\
                    <img src="../' + product.image + '" style="max-width: 150px; max-height: 100px; margin-bottom: 10px" class="card-img-top" alt="Product Image" onerror="handleImageError(this)">\
                    <p class="card-text">Product description: ' + product.description + '</p>\
                    <p class="card-text">Quantity: ' + availableQuantity + '</p>\
                    <p class="card-text">Price: $<span class="price-editable" data-product-id="' + product.id_product + '">' + price.toFixed(2) + '</span></p>\
                    <p>Percentage from the total:</p>\
                    <div class="progress">\
                        <div class="progress-bar" role="progressbar" style="width: ' + percentage + '%;" aria-valuenow="' + percentage + '" aria-valuemin="0" aria-valuemax="100">\
                            <span class="percentage-text">' + percentage.toFixed(2) + '%</span>\
                        </div>\
                    </div>\
                    <button style="margin-top: 10px" class="btn btn-primary btn-edit" data-product-id="' + product.id_product + '">Edit</button>\
                    <button style="margin-top: 10px" class="btn btn-danger btn-delete" data-product-id="' + product.id_product + '">Delete</button>\
                </div>\
            </div>');

        return productDiv;
    }    

    // Manage the click on the edit button
    $(document).on('click', '.btn-edit', function () {
        handleEditButtonClick($(this));
    });

    // Manage the click on the confirm button
    $(document).on('click', '.btn-confirm', function () {
        handleConfirmButtonClick($(this));
    });

    // Manage the click on the delete button
    $(document).on('click', '.btn-delete', function () {
        handleDeleteButtonClick($(this));
    });

    // Function to manage the click on the edit button
    function handleEditButtonClick(button) {
        let productDiv = button.closest('.card-body');
        let name = productDiv.find('.card-title').text();
        let description = productDiv.find('.card-text:eq(0)').text();
        let quantity = productDiv.find('.card-text:eq(1)').text().trim().split(':')[1];
        let price = productDiv.find('.price-editable').text();

        let descriptionPart = description.replace(/^Product description:\s*/, '');
        let quantityPart = quantity.replace(/^Quantity:\s*/, '');

        productDiv.find('.card-title').html('<input type="text" class="form-control" value="' + name + '">');
        productDiv.find('.card-text:eq(0)').html('<p>Product description:</p><textarea class="form-control">' + descriptionPart + '</textarea>');
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
                location.reload();
            },
            error: function (error) {
                console.error('Error:', error);
            }
        });
    }

    // Function to manage the click on the delete button
    function handleDeleteButtonClick(button) {
        let productId = button.data('product-id');

        $.ajax({
            url: 'deleteData.php',
            method: 'POST',
            data: {
                id_product: productId
            },
            success: function (response) {
                console.log(response);
                location.reload();
            },
            error: function (error) {
                console.error('Error:', error);
            }
        });
    }
});
