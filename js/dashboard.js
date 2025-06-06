$(document).ready(function () {
  let hasUnsavedChanges = false;
  let originalProductData = {};

  loadData();

  function createConfirmModal(
    title,
    message,
    confirmBtnText,
    cancelBtnText,
    confirmCallback
  ) {
    $("#customConfirmModal").remove();

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

    $("body").append(modalHTML);

    const confirmModal = new bootstrap.Modal(
      document.getElementById("customConfirmModal")
    );

    $("#confirmModalBtn").on("click", function () {
      confirmModal.hide();
      if (typeof confirmCallback === "function") {
        confirmCallback();
      }
    });

    confirmModal.show();

    return confirmModal;
  }

  function loadData() {
    $.ajax({
      url: "../model/getData.php",
      method: "GET",
      dataType: "json",
      success: function (data) {
        console.log(data);
        renderProducts(data);
        hasUnsavedChanges = false; 
        updateBeforeUnloadListener();
      },
      error: function (error) {
        console.error("Error:", error);
      },
    });
  }

  function renderProducts(data) {
    let productsContainer = $("#products-container");
    productsContainer.empty();

    let totalProducts = 0;

    data.forEach(function (product) {
      totalProducts += parseFloat(product.available_quantity);
    });

    data.forEach(function (product) {
      console.log("Image:", product.image);
      let productDiv = createProductCard(product, totalProducts);
      productsContainer.append(productDiv);
    });

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

      $("#productDetailModal").on("hide.bs.modal", function (e) {
        if (hasUnsavedChanges) {
          e.preventDefault(); 

          createConfirmModal(
            "Unsaved Changes",
            "You have unsaved changes. Are you sure you want to close?",
            "Close Anyway",
            "Cancel",
            function () {
              hasUnsavedChanges = false;
              updateBeforeUnloadListener();
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

    productDiv.data("product-data", product);

    originalProductData[product.id_product] = {
      name: product.name,
      description: product.description,
      quantity: product.available_quantity,
      price: product.price,
    };

    return productDiv;
  }

  $(document).on("dblclick", ".editable", function (e) {
    e.stopPropagation();

    if ($(this).find("input").length > 0) return;

    const currentValue = $(this).text();
    const field = $(this).data("field");
    const fieldType =
      field === "price" || field === "quantity" ? "number" : "text";
    const step = field === "price" ? "0.01" : "1";

    $(this).html(
      '<input type="' +
        fieldType +
        '" class="form-control form-control-sm" value="' +
        currentValue +
        '"' +
        (fieldType === "number" ? ' step="' + step + '"' : "") +
        ">"
    );

    $(this).find("input").focus().select();

    const productCard = $(this).closest(".card-body");
    productCard.find(".btn-save-changes, .btn-cancel-changes").show();

    hasUnsavedChanges = true;
    updateBeforeUnloadListener();
  });

  $(document).on("blur", ".editable input", function (e) {
    if (
      $(e.relatedTarget).hasClass("btn-save-changes") ||
      $(e.relatedTarget).hasClass("btn-cancel-changes")
    ) {
      return;
    }

    const parent = $(this).parent();
    parent.text(parent.data("original"));
  });

  $(document).on("click", ".btn-save-changes", function () {
    const productId = $(this).data("product-id");
    const productCard = $(this).closest(".card-body");
    const changes = {};
    let hasChanges = false;

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

        $(this).text(newValue);
        $(this).data("original", newValue);
      }
    });

    if (hasChanges) {
      const productData = $(`[data-product-id="${productId}"]`)
        .closest(".col-md-4")
        .data("product-data");

      $.ajax({
        url: "../model/updateData.php",
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

    productCard.find(".btn-save-changes, .btn-cancel-changes").hide();

    hasUnsavedChanges = false;
    updateBeforeUnloadListener();
  });

  $(document).on("click", ".btn-cancel-changes", function () {
    const productCard = $(this).closest(".card-body");

    productCard.find(".editable").each(function () {
      const originalValue = $(this).data("original");
      $(this).text(originalValue);
    });

    productCard.find(".btn-save-changes, .btn-cancel-changes").hide();

    hasUnsavedChanges = false;
    updateBeforeUnloadListener();
  });

  $(document).on("click", ".product-card", function (e) {
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

    originalProductData[productId] = {
      name: productData.name,
      description: productData.description,
      quantity: productData.available_quantity,
      price: productData.price,
    };

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

  $(document).on("dblclick", ".modal-editable", function (e) {
    e.stopPropagation();

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

    $(this).html(inputHtml);

    $(this).find("input, textarea").focus().select();

    $(".btn-modal-edit").hide();
    $(".btn-modal-delete").hide();
    $(".btn-modal-save, .btn-modal-cancel").show();

    hasUnsavedChanges = true;
    updateBeforeUnloadListener();
  });

  $(document).on(
    "blur",
    ".modal-editable input, .modal-editable textarea",
    function (e) {
      if (
        $(e.relatedTarget).hasClass("btn-modal-save") ||
        $(e.relatedTarget).hasClass("btn-modal-cancel")
      ) {
        return;
      }

      const parent = $(this).parent();
      parent.text(parent.data("original"));
    }
  );

  $(document).on("click", ".btn-modal-edit", function () {
    const productId = $(this).data("product-id");
    const modalBody = $("#productDetailContent");
    const productData = $(`[data-product-id="${productId}"]`)
      .closest(".col-md-4")
      .data("product-data");

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
        url: "../model/updateData.php",
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

  $(document).on("click", ".btn-modal-cancel", function () {
    const modalBody = $("#productDetailContent");

    modalBody.find(".modal-editable").each(function () {
      const originalValue = $(this).data("original");
      $(this).text(originalValue);
    });

    $(".btn-modal-edit").show();
    $(".btn-modal-delete").show();
    $(".btn-modal-save, .btn-modal-cancel").hide();

    hasUnsavedChanges = false;
    updateBeforeUnloadListener();
  });

  $(document).on("click", ".btn-modal-confirm", function () {
    const productId = $(this).data("product-id");
    const newName = $("#modal-product-name").val();
    const newDescription = $("#modal-product-description").val();
    const newQuantity = $("#modal-product-quantity").val();
    const newPrice = $("#modal-product-price").val();

    const original = originalProductData[productId];

    if (
      newName !== original.name ||
      newDescription !== original.description ||
      newQuantity !== original.quantity ||
      newPrice !== original.price
    ) {
      $.ajax({
        url: "../model/updateData.php",
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
          $("#productDetailModal").modal("hide");
          loadData();
        },
        error: function (error) {
          console.error("Error:", error);
        },
      });
    } else {
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
          url: "../model/deleteData.php",
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

document.addEventListener("DOMContentLoaded", function () {
  document
    .getElementById("showUnverifiedPanelBtn")
    .addEventListener("click", function () {
      document.getElementById("unverifiedProductsPanel").classList.add("open");
      loadUnverifiedProducts();
    });

  document.getElementById("closePanel").addEventListener("click", function () {
    document.getElementById("unverifiedProductsPanel").classList.remove("open");
  });
});

function loadUnverifiedProducts() {
  fetch("../model/getNotVerifiedData.php")
    .then((response) => response.json())
    .then((products) => {
      const productListContainer = document.getElementById(
        "unverifiedProductsList"
      );
      productListContainer.innerHTML = "";
      products.forEach((product) => {
        const productItem = document.createElement("div");
        productItem.classList.add("product-item");
        productItem.innerHTML = `
                  <h5>${product.name}</h5>
                  <img src="../${product.image}"></img>
                  <p>${product.description}</p>
                  <button onclick="verifyProduct(${product.id_product})">Verify</button>
                  <button onclick="deleteProduct(${product.id_product})">Delete</button>
              `;
        productListContainer.appendChild(productItem);
      });
    })
    .catch((error) => console.error("Error loading products:", error));
}

function showConfirmModal({
  title,
  message,
  confirmBtnText = "Confirmar",
  cancelBtnText = "Cancelar",
  onConfirm,
}) {
  const existingModal = document.getElementById("customConfirmModal");
  if (existingModal) existingModal.remove();

  const modalHTML = `
    <div class="modal fade" id="customConfirmModal" tabindex="-1" aria-labelledby="customConfirmModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="customConfirmModalLabel">${title}</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
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

  document.body.insertAdjacentHTML("beforeend", modalHTML);

  const modal = new bootstrap.Modal(
    document.getElementById("customConfirmModal")
  );
  modal.show();

  document.getElementById("confirmModalBtn").addEventListener("click", () => {
    modal.hide();
    onConfirm();
  });
}

$("<style>")
    .prop("type", "text/css")
    .html(
      `
      #messageModal .modal-dialog {
        max-width: 400px;
      }
      #messageModal .modal-content {
        border-radius: 8px;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.5);
      }
      #messageModal .modal-header {
        border-bottom: 1px solid #dee2e6;
        background-color:rgb(43, 147, 188);
        border-top-left-radius: 8px;
        border-top-right-radius: 8px;
      }
        #messageModal .modal-title {
        color: white;
      }
        #messageModal .modal-body {
        background-color: #63D2FF;
      }
      #messageModal .modal-footer {
        border-top: 1px solid #dee2e6;
        background-color: #63D2FF;
      }
      #messageModal #confirmModalBtn {
        min-width: 100px;
      }
    `
    )
    .appendTo("head");

function showMessageModal(message, title = "Información") {
  const existingModal = document.getElementById("messageModal");
  if (existingModal) existingModal.remove();

  const modalHTML = `
    <div class="modal fade" id="messageModal" tabindex="-1" aria-labelledby="messageModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="messageModalLabel">${title}</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
          </div>
          <div class="modal-body">
            <p>${message}</p>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-primary" data-bs-dismiss="modal">OK</button>
          </div>
        </div>
      </div>
    </div>
  `;

  document.body.insertAdjacentHTML("beforeend", modalHTML);

  const modal = new bootstrap.Modal(document.getElementById("messageModal"));
  modal.show();
}

function verifyProduct(productId) {
  fetch("../model/verify_product.php", {
    method: "POST",
    headers: {
      "Content-Type": "application/json",
    },
    body: JSON.stringify({ id_product: productId, verified: true }),
  })
    .then((response) => response.json())
    .then((result) => {
      if (result.success) {
        showMessageModal("Product verified", "Verified");
        loadUnverifiedProducts();
      }
    })
    .catch((error) => {
      console.error("Error verifying product:", error);
      showMessageModal("Error verifying product.", "Error");
    });
}

function deleteProduct(productId) {
  showConfirmModal({
    title: "Confirm elimination",
    message: "Are you sure that you want to delte that rpoduct?",
    confirmBtnText: "Delete",
    cancelBtnText: "Cancel",
    onConfirm: () => {
      fetch("../model/deleteData.php", {
        method: "POST",
        headers: {
          "Content-Type": "application/x-www-form-urlencoded",
        },
        body: new URLSearchParams({ id_product: productId }),
      })
        .then((response) => response.text())
        .then((result) => {
          if (result.includes("Successfully deleted")) {
            console.log("Deleted succesfully");
            loadUnverifiedProducts();
          } else {
            console.log(result);
          }
        })
        .catch((error) => {
          console.error("Error deleting a product:", error);
        });
    },
  });
}
