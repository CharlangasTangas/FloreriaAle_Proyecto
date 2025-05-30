document.addEventListener("DOMContentLoaded", function () {
  const saleForm = document.getElementById("sale-form");
  const saleItems = document.getElementById("sale-items");
  const addItemBtn = document.getElementById("add-item");
  const grandTotalSpan = document.getElementById("grand-total");
  const grandTotalInput = document.getElementById("grand-total-input");
  document.getElementById("modal-venta-exitosa").classList.add("hidden");

  function initializeRow(row) {
    const productSelect = row.querySelector(".product-select");
    const priceInput = row.querySelector(".item-price");
    const quantityInput = row.querySelector(".item-quantity");
    const totalInput = row.querySelector(".item-total");
    const removeBtn = row.querySelector(".remove-item");

    productSelect.addEventListener("change", () => {
      const selectedOption = productSelect.options[productSelect.selectedIndex];
      const price = parseFloat(selectedOption.dataset.price) || 0;
      priceInput.value = price.toFixed(2);
      updateRowTotal(row);
    });

    quantityInput.addEventListener("input", () => updateRowTotal(row));

    removeBtn.addEventListener("click", () => {
      const rows = saleItems.querySelectorAll("tbody tr");
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
    const price = parseFloat(row.querySelector(".item-price").value) || 0;
    const quantity = parseInt(row.querySelector(".item-quantity").value) || 0;
    row.querySelector(".item-total").value = (price * quantity).toFixed(2);
    updateGrandTotal();
  }

  function updateGrandTotal() {
    let total = 0;
    document.querySelectorAll(".item-total").forEach((input) => {
      total += parseFloat(input.value) || 0;
    });
    grandTotalSpan.textContent = total.toFixed(2);
    grandTotalInput.value = total.toFixed(2);
  }

  initializeRow(saleItems.querySelector("tbody tr"));

  addItemBtn.addEventListener("click", () => {
    const template = document.getElementById("item-row-template");
    const newRow = template.cloneNode(true);
    newRow.id = "";
    saleItems.querySelector("tbody").appendChild(newRow);
    initializeRow(newRow);
  });

  window.realizarVenta = function () {
    let hayError = false;
    const filas = document.querySelectorAll("#sale-items tbody tr");

    filas.forEach((fila, index) => {
      const select = fila.querySelector(".product-select");
      const cantidad = parseInt(fila.querySelector(".item-quantity").value) || 0;
      const stock = parseInt(select.selectedOptions[0]?.dataset.stock || 0);

      if (cantidad > stock) {
        hayError = true;
        alert(`🛒 No hay suficiente stock para el producto ${index + 1} en la lista.`);
      }
    });

    const metodoPago = document.getElementById("payment-method");
    if (metodoPago.value === "Selected") {
      hayError = true;
      alert(`💸 Selecciona un método de pago.`);
    }

    if (hayError) return;

    const formData = new FormData(saleForm);
    fetch("/fbd/FloreriaAle_Proyecto/assets/queries/realizar_venta.php", {
      method: "POST",
      body: formData,
    })
      .then((res) => (res.ok ? res.text() : Promise.reject("Error servidor")))
      .then((text) => {
        try {
          const data = JSON.parse(text);
          if (data.status === "success") {
            mostrarModalVenta();
            saleForm.reset();
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

  window.mostrarModalVenta = () => {
    document.getElementById("modal-venta-exitosa").classList.remove("hidden");
  };

  window.cerrarModalVenta = () => {
    document.getElementById("modal-venta-exitosa").classList.add("hidden");
  };
});

//AUTO
document.addEventListener("DOMContentLoaded", function () {
  const customerInput = document.getElementById("customer");
  const idClienteInput = document.getElementById("idCliente");
  const sugerencias = document.getElementById("sugerencias");

  if (!customerInput || !sugerencias) return;

  customerInput.addEventListener("input", function () {
    const query = this.value.trim();
    sugerencias.innerHTML = "";
    idClienteInput.value = "";

    if (query.length < 2) {
      sugerencias.classList.add("hidden");
      return;
    }

    fetch(
      `/fbd/FloreriaAle_Proyecto/assets/queries/buscar_cliente.php?q=${encodeURIComponent(query)}`
    )
      .then((res) => res.text())
      .then((html) => {
        sugerencias.innerHTML = html;
        sugerencias.classList.remove("hidden");

        document.querySelectorAll(".opcion-cliente").forEach((opcion) => {
          opcion.addEventListener("click", () => {
            customerInput.value = opcion.textContent;
            idClienteInput.value = opcion.dataset.id;
            sugerencias.classList.add("hidden");
          });
        });
      })
      .catch((err) => console.error("Error buscando cliente:", err));
  });

  document.addEventListener("click", function (e) {
    if (!sugerencias.contains(e.target) && e.target !== customerInput) {
      sugerencias.classList.add("hidden");
    }
  });
});
