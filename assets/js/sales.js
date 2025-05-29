document.addEventListener("DOMContentLoaded", function () {
  const saleForm = document.getElementById("sale-form");
  const saleItems = document.getElementById("sale-items");
  const addItemBtn = document.getElementById("add-item");
  const grandTotalSpan = document.getElementById("grand-total");
  const grandTotalInput = document.getElementById("grand-total-input");

  // Función para inicializar una fila
  function initializeRow(row) {
    const productSelect = row.querySelector(".product-select");
    const priceInput = row.querySelector(".item-price");
    const quantityInput = row.querySelector(".item-quantity");
    const totalInput = row.querySelector(".item-total");
    const removeBtn = row.querySelector(".remove-item");

    productSelect.addEventListener("change", function () {
      const selectedOption = this.options[this.selectedIndex];
      const price = parseFloat(selectedOption.dataset.price) || 0;
      priceInput.value = price.toFixed(2);
      updateRowTotal(row);
    });

    quantityInput.addEventListener("input", function () {
      updateRowTotal(row);
    });

    removeBtn.addEventListener("click", function () {
      if (saleItems.querySelectorAll("tbody tr").length > 1) {
        row.remove();
        updateGrandTotal();
      } else {
        productSelect.value = "";
        priceInput.value = "";
        quantityInput.value = 1;
        totalInput.value = "";
        updateGrandTotal();
      }
    });
  }

  // Función para actualizar el total de una fila
  function updateRowTotal(row) {
    const priceInput = row.querySelector(".item-price");
    const quantityInput = row.querySelector(".item-quantity");
    const totalInput = row.querySelector(".item-total");

    const price = parseFloat(priceInput.value) || 0;
    const quantity = parseInt(quantityInput.value) || 0;
    const total = price * quantity;

    totalInput.value = total.toFixed(2);
    updateGrandTotal();
  }

  // Función para actualizar el total general
  function updateGrandTotal() {
    const totalInputs = document.querySelectorAll(".item-total");
    let grandTotal = 0;

    totalInputs.forEach((input) => {
      grandTotal += parseFloat(input.value) || 0;
    });

    grandTotalSpan.textContent = grandTotal.toFixed(2);
    grandTotalInput.value = grandTotal.toFixed(2);
  }

  // Inicializar la primera fila
  initializeRow(saleItems.querySelector("tbody tr"));

  // Agregar nueva fila
  addItemBtn.addEventListener("click", function () {
    const template = document.getElementById("item-row-template");
    const newRow = template.cloneNode(true);
    newRow.id = "";
    saleItems.querySelector("tbody").appendChild(newRow);
    initializeRow(newRow);
  });

  // Función para realizar la venta
  window.realizarVenta = function () {
    const formData = new FormData(saleForm);

    fetch("/fbd/FloreriaAle_Proyecto/assets/queries/realizar_venta.php", {
      method: "POST",
      body: formData,
    })
      .then((res) => res.json())
      .then((data) => {
        if (data.status === "success") {
          mostrarModalVenta();
          saleForm.reset();
          grandTotalSpan.textContent = "0.00";
          grandTotalInput.value = "0";
        } else {
          alert("❌ " + data.message);
        }
      })
      .catch((error) => {
        alert("❌ Error al conectar con el servidor");
        console.error(error);
      });
  };

  // Función para mostrar el modal de venta exitosa
  window.mostrarModalVenta = function () {
    document.getElementById("modal-venta-exitosa").classList.remove("hidden");
  };

  // Función para cerrar el modal de venta exitosa
  window.cerrarModalVenta = function () {
    document.getElementById("modal-venta-exitosa").classList.add("hidden");
  };
});
