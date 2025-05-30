document.addEventListener("DOMContentLoaded", function () {
  const purchaseForm = document.getElementById("purchase-form");
  const purchaseItems = document.getElementById("purchase-items");
  const addItemBtn = document.getElementById("add-item");
  const grandTotalSpan = document.getElementById("grand-total-purchase");
  const grandTotalInput = document.getElementById("grand-total-purchase-input");
  document.getElementById("modal-compra-exitosa").classList.add("hidden");

  function initializeRow(row) {
    const productSelect = row.querySelector(".product-select");
    const priceInput = row.querySelector(".item-cost");
    const quantityInput = row.querySelector(".item-quantity");
    const totalInput = row.querySelector(".item-subtotal");
    const removeBtn = row.querySelector(".remove-item");

    productSelect.addEventListener("change", () => {
      const selectedOption = productSelect.options[productSelect.selectedIndex];
      const price = parseFloat(selectedOption.dataset.cost) || 0;
      const stock = selectedOption.dataset.stock || "";
      priceInput.value = price.toFixed(2);
      row.querySelector(".item-stock-hidden").value = stock;
      updateRowTotal(row);
    });

    quantityInput.addEventListener("input", () => updateRowTotal(row));

    removeBtn.addEventListener("click", () => {
      const rows = purchaseItems.querySelectorAll("tbody tr");
      if (rows.length > 1) {
        row.remove();
      } else {
        productSelect.value = "";
        priceInput.value = "";
        quantityInput.value = 1;
        totalInput.value = "";
      }
      updateGrandTotal();
    });
  }

  function updateRowTotal(row) {
    const price = parseFloat(row.querySelector(".item-cost").value) || 0;
    const quantity = parseInt(row.querySelector(".item-quantity").value) || 0;
    row.querySelector(".item-subtotal").value = (price * quantity).toFixed(2);
    updateGrandTotal();
  }

  function updateGrandTotal() {
    let total = 0;
    document.querySelectorAll(".item-subtotal").forEach((input) => {
      total += parseFloat(input.value) || 0;
    });
    grandTotalSpan.textContent = total.toFixed(2);
    grandTotalInput.value = total.toFixed(2);
  }

  initializeRow(purchaseItems.querySelector("tbody tr"));

  addItemBtn.addEventListener("click", () => {
    const template = document.getElementById("item-row-template");
    const newRow = template.cloneNode(true);
    newRow.id = "";
    purchaseItems.querySelector("tbody").appendChild(newRow);
    initializeRow(newRow);
  });

  window.realizarCompra = function () {
    let hayError = false;
    const filas = document.querySelectorAll("#purchase-items tbody tr");

    const metodoPago = document.getElementById("payment-method");
    if (metodoPago.value === "Selected") {
      hayError = true;
      alert(`💸 Selecciona un método de pago.`);
    }

    if (hayError) return;

    const formData = new FormData(purchaseForm);
    fetch("/fbd/FloreriaAle_Proyecto/assets/queries/realizar_compra.php", {
      method: "POST",
      body: formData,
    })
      .then((res) => (res.ok ? res.text() : Promise.reject("Error servidor")))
      .then((text) => {
        try {
          const data = JSON.parse(text);
          if (data.status === "success") {
            mostrarModalCompra();
            purchaseForm.reset();
            grandTotalSpan.textContent = "0.00";
            grandTotalInput.value = "0";
          } else {
            alert("❌ " + data.message);
          }
        } catch {
          alert("❌ Error: respuesta inválida del servidor");
          console.error("Respuesta no JSON:", text);
        }
      })
      .catch((err) => {
        alert("❌ Error de conexión");
        console.error(err);
      });
  };

  window.mostrarModalCompra = () => {
    document.getElementById("modal-compra-exitosa").classList.remove("hidden");
  };

  window.cerrarModalCompra = () => {
    document.getElementById("modal-compra-exitosa").classList.add("hidden");
  };

  // Autocompletado de proveedor
  const supplierInput = document.getElementById("supplier");
  const idProveedorInput = document.getElementById("idProveedor");
  const sugerencias = document.getElementById("sugerencias-proveedores");

  supplierInput.addEventListener("input", function () {
    const query = this.value.trim();
    sugerencias.innerHTML = "";
    idProveedorInput.value = "";

    if (query.length < 2) {
      sugerencias.classList.add("hidden");
      return;
    }

    fetch(
      `/fbd/FloreriaAle_Proyecto/assets/queries/buscar_proveedor.php?q=${encodeURIComponent(query)}`
    )
      .then((res) => res.text())
      .then((html) => {
        sugerencias.innerHTML = html;
        sugerencias.classList.remove("hidden");

        document.querySelectorAll(".opcion-proveedor").forEach((opcion) => {
          opcion.addEventListener("click", () => {
            supplierInput.value = opcion.textContent;
            idProveedorInput.value = opcion.dataset.id;
            sugerencias.classList.add("hidden");
          });
        });
      })
      .catch((err) => console.error("Error buscando proveedor:", err));
  });

  document.addEventListener("click", function (e) {
    if (!sugerencias.contains(e.target) && e.target !== supplierInput) {
      sugerencias.classList.add("hidden");
    }
  });
});
