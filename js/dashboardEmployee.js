$(document).ready(function () {
  // Variable to track unsaved changes
  let hasUnsavedChanges = false;
  // Original product data for comparison
  let originalProductData = {};

  // Charge data at start
  loadData();

  // Function to create a custom confirmation modal
  function createConfirmModal(
    title,
    message,
    confirmBtnText,
    cancelBtnText,
    confirmCallback
  ) {
    // Remove any existing confirm modal
    $("#customConfirmModal").remove();

    // Create a new modal structure
    const modalHTML = `
      <div class="modal fade" id="customConfirmModal" tabindex="-1" aria-labelledby="customConfirmModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title" id="customConfirmModalLabel">${title}</h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
              <p>${message}</p>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">${cancelBtnText}</button>
              <button type="button" class="btn btn-danger" id="confirmModalBtn">${confirmBtnText}</button>
            </div>
          </div>
        </div>
      </div>
    `;

    // Append modal to body
    $("body").append(modalHTML);

    // Initialize the modal
    const confirmModal = new bootstrap.Modal(
      document.getElementById("customConfirmModal")
    );

    // Handle confirm button click
    $("#confirmModalBtn").on("click", function () {
      confirmModal.hide();
      if (typeof confirmCallback === "function") {
        confirmCallback();
      }
    });

    // Show the modal
    confirmModal.show();

    return confirmModal;
  }

  // Function to charge data by AJAX
  function loadData() {
    $.ajax({
      url: "getDataEmployee.php",
      method: "GET",
      dataType: "json",
      success: function (data) {
        console.log(data);
        renderProducts(data);
        hasUnsavedChanges = false; // Reset the flag after loading data
        updateBeforeUnloadListener();
      },
      error: function (error) {
        console.error("Error:", error);
      },
    });
  }

  // Function to render the products on the container
  function renderProducts(data) {
    let productsContainer = $("#products-container");
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
    if ($("#productDetailModal").length === 0) {
      $("body").append(`
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

      // Add event handler for modal close attempt when changes are pending
      $("#productDetailModal").on("hide.bs.modal", function (e) {
        if (hasUnsavedChanges) {
          e.preventDefault(); // Prevent modal from closing immediately

          // Show custom confirmation modal
          createConfirmModal(
            "Unsaved Changes",
            "You have unsaved changes. Are you sure you want to close?",
            "Close Anyway",
            "Cancel",
            function () {
              // If confirmed, reset changes flag and close the modal
              hasUnsavedChanges = false;
              updateBeforeUnloadListener();
              // Use Bootstrap's API to hide the modal programmatically
              const productModal = bootstrap.Modal.getInstance(
                document.getElementById("productDetailModal")
              );
              productModal.hide();
            }
          );
        }
      });
    }
  }

  // Function to create the product card
  function createProductCard(product, totalProducts) {
    let availableQuantity = parseFloat(product.available_quantity);
    let price = parseFloat(product.price);
    let percentage =
      totalProducts !== 0 ? (availableQuantity / totalProducts) * 100 : 0;

    let productDiv = $("<div>").addClass("col-md-4 mb-6");
    productDiv.html(
      '<div class="card product-card" data-product-id="' +
        product.id_product +
        '">\
                  <div class="card-body">\
                      <h5 class="card-title" data-field="name" data-original="' +
        product.name +
        '">' +
        product.name +
        '</h5>\
                      <div class="product-image-container">\
                          <img src="../' +
        product.image +
        '" class="product-image" alt="Product Image" onerror="handleImageError(this)">\
                      </div>\
                      <p class="card-text"><span class="field-label">Quantity: </span><span data-field="quantity" data-original="' +
        availableQuantity +
        '">' +
        availableQuantity +
        '</span></p>\
                      <p class="card-text"><span class="field-label">Price: $</span><span data-field="price" data-original="' +
        price.toFixed(2) +
        '">' +
        price.toFixed(2) +
        '</span></p>\
                      <p>Percentage from the total:</p>\
                      <div class="progress">\
                          <div class="progress-bar" role="progressbar" style="width: ' +
        percentage +
        '%;" aria-valuenow="' +
        percentage +
        '" aria-valuemin="0" aria-valuemax="100">\
                              <span class="percentage-text">' +
        percentage.toFixed(2) +
        '%</span>\
                          </div>\
                      </div>\
                  </div>\
                  <div class="card-footer text-center">\
                      <small class="text-light">Click for details and options</small>\
                  </div>\
              </div>'
    );

    // Store full product data for modal
    productDiv.data("product-data", product);

    // Store original data for comparison
    originalProductData[product.id_product] = {
      name: product.name,
      description: product.description,
      quantity: product.available_quantity,
      price: product.price,
    };

    return productDiv;
  }

  // Enable inline editing on editable fields
  $(document).on("dblclick", ".editable", function (e) {
    e.stopPropagation();

    // Don't do anything if already editing
    if ($(this).find("input").length > 0) return;

    const currentValue = $(this).text();
    const field = $(this).data("field");
    const fieldType =
      field === "price" || field === "quantity" ? "number" : "text";
    const step = field === "price" ? "0.01" : "1";

    // Replace content with input
    $(this).html(
      '<input type="' +
        fieldType +
        '" class="form-control form-control-sm" value="' +
        currentValue +
        '"' +
        (fieldType === "number" ? ' step="' + step + '"' : "") +
        ">"
    );

    // Focus on the input
    $(this).find("input").focus().select();

    // Show save/cancel buttons
    const productCard = $(this).closest(".card-body");
    productCard.find(".btn-save-changes, .btn-cancel-changes").show();

    hasUnsavedChanges = true;
    updateBeforeUnloadListener();
  });

  // Handle input blur event
  $(document).on("blur", ".editable input", function (e) {
    // Don't handle blur if it's because of clicking save/cancel buttons
    if (
      $(e.relatedTarget).hasClass("btn-save-changes") ||
      $(e.relatedTarget).hasClass("btn-cancel-changes")
    ) {
      return;
    }

    // We'll just revert to the original text if clicked elsewhere
    const parent = $(this).parent();
    parent.text(parent.data("original"));
  });

  // Handle save changes button
  $(document).on("click", ".btn-save-changes", function () {
    const productId = $(this).data("product-id");
    const productCard = $(this).closest(".card-body");
    const changes = {};
    let hasChanges = false;

    // Collect all edited values
    productCard.find(".editable").each(function () {
      const field = $(this).data("field");
      const input = $(this).find("input");

      if (input.length > 0) {
        const newValue = input.val();
        const originalValue = $(this).data("original");

        if (newValue != originalValue) {
          changes[field] = newValue;
          hasChanges = true;
        }

        // Update displayed value
        $(this).text(newValue);
        $(this).data("original", newValue);
      }
    });

    // If changes were made, save them
    if (hasChanges) {
      const productData = $(`[data-product-id="${productId}"]`)
        .closest(".col-md-4")
        .data("product-data");

      $.ajax({
        url: "updateData.php",
        method: "POST",
        data: {
          id_product: productId,
          new_name: changes.name || productData.name,
          new_description: productData.description,
          new_quantity: changes.quantity || productData.available_quantity,
          new_price: changes.price || productData.price,
        },
        success: function (response) {
          console.log(response);
          loadData();
        },
        error: function (error) {
          console.error("Error:", error);
        },
      });
    }

    // Hide buttons
    productCard.find(".btn-save-changes, .btn-cancel-changes").hide();

    hasUnsavedChanges = false;
    updateBeforeUnloadListener();
  });

  // Handle cancel changes button
  $(document).on("click", ".btn-cancel-changes", function () {
    const productCard = $(this).closest(".card-body");

    // Revert all editable fields to original values
    productCard.find(".editable").each(function () {
      const originalValue = $(this).data("original");
      $(this).text(originalValue);
    });

    // Hide buttons
    productCard.find(".btn-save-changes, .btn-cancel-changes").hide();

    hasUnsavedChanges = false;
    updateBeforeUnloadListener();
  });

  // Handle card click to show modal
  $(document).on("click", ".product-card", function (e) {
    // Don't open modal if clicking on editable fields, inputs, or buttons
    if (
      $(e.target).hasClass("editable") ||
      $(e.target).closest(".editable").length ||
      $(e.target).is("input") ||
      $(e.target).hasClass("btn") ||
      $(e.target).closest(".btn").length
    ) {
      return;
    }

    const productId = $(this).data("product-id");
    const productData = $(this).closest(".col-md-4").data("product-data");

    // Store original modal data for comparison
    originalProductData[productId] = {
      name: productData.name,
      description: productData.description,
      quantity: productData.available_quantity,
      price: productData.price,
    };

    // Display full product details in modal
    let modalContent = `
  <div class="container-fluid">
      <div class="row">
          <div class="col-md-5">
              <div class="modal-product-image-container">
                  <img src="../${
                    productData.image
                  }" class="modal-product-image img-fluid" alt="Product Image" onerror="handleImageError(this)">
              </div>
              <div class="image-caption mt-3">
                  <h5 class="text-center">(Double-click product name, description, quantity or price to edit directly!)</h5>
              </div>
          </div>
          <div class="col-md-7">
              <h3 class="modal-editable" data-field="name" data-original="${
                productData.name
              }" data-product-id="${productId}">${productData.name}</h3>
              <div class="product-details">
                  <p><strong>Description:</strong> <span class="modal-editable" data-field="description" data-original="${
                    productData.description
                  }" data-product-id="${productId}">${
      productData.description
    }</span></p>
                  <p><strong>Quantity:</strong> <span class="modal-editable" data-field="quantity" data-original="${
                    productData.available_quantity
                  }" data-product-id="${productId}">${
      productData.available_quantity
    }</span></p>
                  <p><strong>Price:</strong> <span class="modal-editable" data-field="price" data-original="${parseFloat(
                    productData.price
                  ).toFixed(2)}" data-product-id="${productId}">${parseFloat(
      productData.price
    ).toFixed(2)}</span></p>
              </div>
              
              <div class="mt-4 modal-action-buttons">
                  <button class="btn btn-success btn-modal-save" data-product-id="${productId}" style="display: none;">Save Changes</button>
                  <button class="btn btn-secondary btn-modal-cancel" style="display: none;">Cancel</button>
                  <button class="btn btn-danger btn-modal-delete" data-product-id="${productId}">Delete Product</button>
              </div>
          </div>
      </div>
  </div>
  `;

    $("#productDetailContent").html(modalContent);
    const productModal = new bootstrap.Modal(
      document.getElementById("productDetailModal")
    );
    productModal.show();
  });

  // Enable inline editing on modal editable fields
  $(document).on("dblclick", ".modal-editable", function (e) {
    e.stopPropagation();

    // Don't do anything if already editing
    if ($(this).find("input, textarea").length > 0) return;

    const currentValue = $(this).text();
    const field = $(this).data("field");
    const productId = $(this).data("product-id");

    let inputHtml;
    if (field === "description") {
      inputHtml =
        '<textarea class="form-control">' + currentValue + "</textarea>";
    } else {
      const fieldType =
        field === "price" || field === "quantity" ? "number" : "text";
      const step = field === "price" ? "0.01" : "1";
      inputHtml =
        '<input type="' +
        fieldType +
        '" class="form-control" value="' +
        currentValue +
        '"' +
        (fieldType === "number" ? ' step="' + step + '"' : "") +
        ">";
    }

    // Replace content with input
    $(this).html(inputHtml);

    // Focus on the input
    $(this).find("input, textarea").focus().select();

    // Show save/cancel buttons
    $(".btn-modal-edit").hide();
    $(".btn-modal-delete").hide();
    $(".btn-modal-save, .btn-modal-cancel").show();

    hasUnsavedChanges = true;
    updateBeforeUnloadListener();
  });

  // Handle input blur event in modal
  $(document).on(
    "blur",
    ".modal-editable input, .modal-editable textarea",
    function (e) {
      // Don't handle blur if it's because of clicking save/cancel buttons
      if (
        $(e.relatedTarget).hasClass("btn-modal-save") ||
        $(e.relatedTarget).hasClass("btn-modal-cancel")
      ) {
        return;
      }

      // We'll just revert to the original text if clicked elsewhere
      const parent = $(this).parent();
      parent.text(parent.data("original"));
    }
  );

  // Handle modal edit button
  $(document).on("click", ".btn-modal-edit", function () {
    const productId = $(this).data("product-id");
    const modalBody = $("#productDetailContent");
    const productData = $(`[data-product-id="${productId}"]`)
      .closest(".col-md-4")
      .data("product-data");

    // Store original data for comparison
    originalProductData[productId] = {
      name: productData.name,
      description: productData.description,
      quantity: productData.available_quantity,
      price: productData.price,
    };

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

    // Mark as having unsaved changes
    hasUnsavedChanges = true;
    updateBeforeUnloadListener();
  });

  // Handle modal save button for inline edits
  $(document).on("click", ".btn-modal-save", function () {
    const productId = $(this).data("product-id");
    const modalBody = $("#productDetailContent");
    const changes = {};
    let hasChanges = false;

    // Collect all edited values
    modalBody.find(".modal-editable").each(function () {
      const field = $(this).data("field");
      const input = $(this).find("input, textarea");

      if (input.length > 0) {
        const newValue = input.val();
        const originalValue = $(this).data("original");

        if (newValue != originalValue) {
          changes[field] = newValue;
          hasChanges = true;
        }

        // Update displayed value
        $(this).text(newValue);
        $(this).data("original", newValue);
      }
    });

    // If changes were made, save them
    if (hasChanges) {
      const productData = $(`[data-product-id="${productId}"]`)
        .closest(".col-md-4")
        .data("product-data");

      $.ajax({
        url: "updateData.php",
        method: "POST",
        data: {
          id_product: productId,
          new_name: changes.name || productData.name,
          new_description: changes.description || productData.description,
          new_quantity: changes.quantity || productData.available_quantity,
          new_price: changes.price || productData.price,
        },
        success: function (response) {
          console.log(response);
          $("#productDetailModal").modal("hide");
          loadData();
        },
        error: function (error) {
          console.error("Error:", error);
        },
      });
    }

    // Show edit/delete buttons, hide save/cancel
    $(".btn-modal-edit").show();
    $(".btn-modal-delete").show();
    $(".btn-modal-save, .btn-modal-cancel").hide();

    hasUnsavedChanges = false;
    updateBeforeUnloadListener();
  });

  // Handle modal cancel button for inline edits
  $(document).on("click", ".btn-modal-cancel", function () {
    const modalBody = $("#productDetailContent");

    // Revert all editable fields to original values
    modalBody.find(".modal-editable").each(function () {
      const originalValue = $(this).data("original");
      $(this).text(originalValue);
    });

    // Show edit/delete buttons, hide save/cancel
    $(".btn-modal-edit").show();
    $(".btn-modal-delete").show();
    $(".btn-modal-save, .btn-modal-cancel").hide();

    hasUnsavedChanges = false;
    updateBeforeUnloadListener();
  });

  // Handle save changes from modal form
  $(document).on("click", ".btn-modal-confirm", function () {
    const productId = $(this).data("product-id");
    const newName = $("#modal-product-name").val();
    const newDescription = $("#modal-product-description").val();
    const newQuantity = $("#modal-product-quantity").val();
    const newPrice = $("#modal-product-price").val();

    const original = originalProductData[productId];

    // Check if there are actual changes
    if (
      newName !== original.name ||
      newDescription !== original.description ||
      newQuantity !== original.quantity ||
      newPrice !== original.price
    ) {
      $.ajax({
        url: "updateData.php",
        method: "POST",
        data: {
          id_product: productId,
          new_name: newName,
          new_description: newDescription,
          new_quantity: newQuantity,
          new_price: newPrice,
        },
        success: function (response) {
          console.log(response);
          // Close modal and reload data
          $("#productDetailModal").modal("hide");
          loadData();
        },
        error: function (error) {
          console.error("Error:", error);
        },
      });
    } else {
      // No changes, just close the modal
      $("#productDetailModal").modal("hide");
    }

    hasUnsavedChanges = false;
    updateBeforeUnloadListener();
  });

  // Handle delete from modal
  $(document).on("click", ".btn-modal-delete", function () {
    const productId = $(this).data("product-id");

    // Show custom confirmation modal instead of browser confirm
    createConfirmModal(
      "Delete Product",
      "Are you sure you want to delete this product?",
      "Delete",
      "Cancel",
      function () {
        // If confirmed, proceed with deletion
        $.ajax({
          url: "deleteData.php",
          method: "POST",
          data: {
            id_product: productId,
          },
          success: function (response) {
            console.log(response);
            $("#productDetailModal").modal("hide");
            loadData();
          },
          error: function (error) {
            console.error("Error:", error);
          },
        });
      }
    );
  });

  // Helper function to handle image errors
  window.handleImageError = function (img) {
    img.onerror = null;
    img.src = "../img/placeholder.png";
  };

  // Setup beforeunload event listener
  function updateBeforeUnloadListener() {
    if (hasUnsavedChanges) {
      window.addEventListener("beforeunload", beforeUnloadHandler);
    } else {
      window.removeEventListener("beforeunload", beforeUnloadHandler);
    }
  }

  // Handle beforeunload event
  function beforeUnloadHandler(e) {
    e.preventDefault();
    // Standard text will be displayed regardless of this returned value
    return "You have unsaved changes. Are you sure you want to leave?";
  }

  // Add custom style for modal overlay
  $("<style>")
    .prop("type", "text/css")
    .html(
      `
      #customConfirmModal .modal-dialog {
        max-width: 400px;
      }
      #customConfirmModal .modal-content {
        border-radius: 8px;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.5);
      }
      #customConfirmModal .modal-header {
        border-bottom: 1px solid #dee2e6;
        background-color:rgb(43, 147, 188);
        border-top-left-radius: 8px;
        border-top-right-radius: 8px;
      }
        #customConfirmModal .modal-title {
        color: white;
      }
        #customConfirmModal .modal-body {
        background-color: #63D2FF;
      }
      #customConfirmModal .modal-footer {
        border-top: 1px solid #dee2e6;
        background-color: #63D2FF;
      }
      #customConfirmModal #confirmModalBtn {
        min-width: 100px;
      }
    `
    )
    .appendTo("head");
});
