document.addEventListener("DOMContentLoaded", function () {
  const tablaBody = document.querySelector("#tabla-ventas tbody");
  const inputBusqueda = document.getElementById("sale-search");
  let ventasGlobal = [];

  function renderVentas(listaVentas) {
    tablaBody.innerHTML = "";

    listaVentas.forEach((venta) => {
      const fila = document.createElement("tr");
      fila.classList.add("border-b");

      const esPendiente = venta.status.trim().toLowerCase() === "pending";
      const botonEditar = esPendiente
        ? `<button data-id="${venta.id}" class="btn-editar rounded-full bg-yellow-100 text-yellow-800 px-2 py-1 text-xs font-medium hover:underline">Editar</button>`
        : `<span class="rounded-full px-2 py-1 text-xs font-medium bg-green-100 text-green-800">${venta.status}</span>`;

      fila.innerHTML = `
        <td class="p-3 font-medium">
          <span class="text-purple-500">#${venta.id}</span>
        </td>
        <td class="p-3">${venta.employee}</td>
        <td class="p-3">${venta.customer}</td>
        <td class="p-3">${venta.date}</td>
        <td class="p-3">$${venta.total}</td>
        <td class="p-3">${botonEditar}</td>
      `;

      tablaBody.appendChild(fila);
    });

    // Activar eventos para los botones de editar
    document.querySelectorAll(".btn-editar").forEach((btn) => {
      btn.addEventListener("click", function () {
        const id = this.dataset.id;
        if (typeof abrirModalEditarVenta === "function") {
          abrirModalEditarVenta(id);
        } else {
          console.warn("La función abrirModalEditarVenta no está definida.");
        }
      });
    });
  }

  function aplicarFiltro() {
    const termino = inputBusqueda.value.trim().toLowerCase();
    const filtradas = ventasGlobal.filter((venta) => {
      return (
        venta.id.toString().includes(termino) ||
        venta.customer.toLowerCase().includes(termino) ||
        venta.date.includes(termino)
      );
    });

    renderVentas(filtradas);
  }

  // Cargar las ventas inicialmente
  fetch("/fbd/FloreriaAle_Proyecto/assets/queries/obtener_ventas.php")
    .then((res) => res.json())
    .then((datos) => {
      ventasGlobal = datos;
      renderVentas(ventasGlobal);
    })
    .catch((err) => {
      console.error("Error al cargar ventas:", err);
    });

  inputBusqueda.addEventListener("input", aplicarFiltro);

  function abrirModalEditarVenta(idVenta) {
    fetch(`assets/queries/obtener_detalle_venta.php?id=${idVenta}`)
      .then((res) => res.json())
      .then((venta) => {
        if (!venta || !venta.productos) {
          alert("❌ No se pudieron cargar los datos de la venta.");
          return;
        }

        document.getElementById("modal-editar-venta").classList.remove("hidden");
        document.getElementById("edit-idVenta").value = venta.idVenta;
        document.getElementById("edit-cliente").value =
          venta.nombreCliente || "Cliente no registrado";

        document.getElementById("edit-fecha").value = venta.fechaEmision;
        document.getElementById("edit-metodo").value = venta.metodoPago;
        document.getElementById("edit-estado").value = venta.estado;
        document.getElementById("edit-comentarios").value = venta.comentarios || "";

        const tableBody = document.getElementById("edit-productos-body");
        tableBody.innerHTML = "";

        venta.productos.forEach((item) => {
          const row = document.createElement("tr");
          row.classList.add("border-b");
          row.innerHTML = `
          <td class="p-2">
            <select name="edit-product[]" class="product-select w-full rounded-md border px-2 py-1 text-sm">
              <option value="${item.idProducto}" selected>${item.nombreProducto}</option>
            </select>
            <input type="hidden" name="edit-stock[]" value="${item.stock}">
          </td>
          <td class="p-2">
            <input type="text" name="edit-price[]" value="${
              item.precioUnitario
            }" class="item-price w-full rounded-md border px-2 py-1 text-sm" readonly>
          </td>
          <td class="p-2">
            <input type="number" name="edit-quantity[]" value="${
              item.cantidad
            }" class="item-quantity w-full rounded-md border px-2 py-1 text-sm">
          </td>
          <td class="p-2">
            <input type="text" name="edit-total[]" value="${(
              item.precioUnitario * item.cantidad
            ).toFixed(2)}" class="item-total w-full rounded-md border px-2 py-1 text-sm" readonly>
          </td>
          <td class="p-2">
            <button type="button" class="remove-item text-red-500">✖</button>
          </td>
        `;
          tableBody.appendChild(row);
        });

        calcularTotalEditado();
      })
      .catch((err) => {
        console.error("Error al cargar detalles de la venta:", err);
        alert("❌ Error al cargar datos de la venta.");
      });
  }

  function calcularTotalEditado() {
    let total = 0;
    document.querySelectorAll("#edit-productos-body tr").forEach((row) => {
      const cantidad = parseFloat(row.querySelector("input[name='edit-quantity[]']").value) || 0;
      const precio = parseFloat(row.querySelector("input[name='edit-price[]']").value) || 0;
      total += cantidad * precio;
    });

    document.getElementById("edit-total").textContent = total.toFixed(2);
  }
});

function cerrarModalEditarVenta() {
  document.getElementById("modal-editar-venta").classList.add("hidden");
}

function calcularTotalEditado() {
  let total = 0;
  document.querySelectorAll("#edit-productos-body tr").forEach((row) => {
    const cantidad = parseFloat(row.querySelector("input[name='edit-quantity[]']").value) || 0;
    const precio = parseFloat(row.querySelector("input[name='edit-price[]']").value) || 0;
    total += cantidad * precio;
    row.querySelector("input[name='edit-total[]']").value = (cantidad * precio).toFixed(2);
  });

  document.getElementById("edit-total").textContent = total.toFixed(2);
}

document.getElementById("btn-agregar-producto-edit").addEventListener("click", () => {
  const template = document.getElementById("edit-product-template");
  const clone = template.cloneNode(true);
  clone.classList.remove("hidden");
  clone.removeAttribute("id");

  const select = clone.querySelector("select");
  const priceInput = clone.querySelector("input[name='edit-price[]']");
  const quantityInput = clone.querySelector("input[name='edit-quantity[]']");
  const totalInput = clone.querySelector("input[name='edit-total[]']");

  select.addEventListener("change", () => {
    const selected = select.options[select.selectedIndex];
    const price = selected.getAttribute("data-price") || 0;
    priceInput.value = parseFloat(price).toFixed(2);
    totalInput.value = (price * quantityInput.value).toFixed(2);
    calcularTotalEditado();
  });

  quantityInput.addEventListener("input", () => {
    const cantidad = parseFloat(quantityInput.value) || 0;
    const precio = parseFloat(priceInput.value) || 0;
    totalInput.value = (cantidad * precio).toFixed(2);
    calcularTotalEditado();
  });

  clone.querySelector(".remove-item").addEventListener("click", () => {
    const totalFilas = document.querySelectorAll("#edit-productos-body tr").length;
    if (totalFilas > 1) {
      clone.remove();
      calcularTotalEditado();
    } else {
      alert("⚠️ La venta debe contener al menos un producto.");
    }
  });

  document.getElementById("edit-productos-body").appendChild(clone);
});

function abrirModalEditarVenta(idVenta) {
  fetch(`assets/queries/obtener_detalle_venta.php?id=${idVenta}`)
    .then((res) => res.json())
    .then((venta) => {
      if (!venta || !venta.productos) {
        alert("❌ No se pudieron cargar los datos de la venta.");
        return;
      }

      document.getElementById("modal-editar-venta").classList.remove("hidden");
      document.getElementById("edit-idVenta").value = venta.idVenta;
      document.getElementById("edit-cliente").value =
        venta.nombreCliente || "Cliente no registrado";
      document.getElementById("edit-fecha").value = venta.fechaEmision;
      document.getElementById("edit-metodo").value = venta.metodoPago;
      document.getElementById("edit-estado").value = venta.estado;
      document.getElementById("edit-comentarios").value = venta.comentarios || "";

      const tableBody = document.getElementById("edit-productos-body");
      tableBody.innerHTML = "";

      venta.productos.forEach((item) => {
        const template = document.getElementById("edit-product-template");
        const row = template.cloneNode(true);
        row.classList.remove("hidden");
        row.removeAttribute("id");

        const select = row.querySelector("select");
        const priceInput = row.querySelector("input[name='edit-price[]']");
        const quantityInput = row.querySelector("input[name='edit-quantity[]']");
        const totalInput = row.querySelector("input[name='edit-total[]']");

        select.innerHTML = template.querySelector("select").innerHTML;
        select.value = item.idProducto;
        priceInput.value = parseFloat(item.precioUnitario).toFixed(2);
        quantityInput.value = item.cantidad;
        totalInput.value = (item.precioUnitario * item.cantidad).toFixed(2);

        agregarEventosAFila(row);
        tableBody.appendChild(row);
      });

      calcularTotalEditado();
    })
    .catch((err) => {
      console.error("Error al cargar detalles de la venta:", err);
      alert("❌ Error al cargar datos de la venta.");
    });
}

document.getElementById("form-editar-venta").addEventListener("submit", function (e) {
  e.preventDefault();

  const form = e.target;
  const idVenta = form.querySelector("#edit-idVenta").value;
  const fecha = form.querySelector("#edit-fecha").value;
  const metodo = form.querySelector("#edit-metodo").value;
  const estado = form.querySelector("#edit-estado").value;
  const comentarios = form.querySelector("#edit-comentarios").value;
  const total = parseFloat(document.getElementById("edit-total").textContent);

  const productos = [];
  const cantidades = [];
  const precios = [];

  document.querySelectorAll("#edit-productos-body tr").forEach((row) => {
    productos.push(row.querySelector("select").value);
    cantidades.push(row.querySelector("input[name='edit-quantity[]']").value);
    precios.push(row.querySelector("input[name='edit-price[]']").value);
  });

  const formData = new FormData();
  formData.append("idVenta", idVenta);
  formData.append("fecha", fecha);
  formData.append("metodo", metodo);
  formData.append("estado", estado);
  formData.append("comentarios", comentarios);
  formData.append("total", total);

  productos.forEach((p) => formData.append("productos[]", p));
  cantidades.forEach((c) => formData.append("cantidades[]", c));
  precios.forEach((p) => formData.append("precios[]", p));

  fetch("assets/updates/sales/actualizar_venta.php", {
    method: "POST",
    body: formData,
  })
    .then((res) => res.json())
    .then((data) => {
      if (data.success) {
        alert("✅ Venta actualizada correctamente.");
        location.reload();
      } else {
        alert("❌ Error al actualizar: " + data.error);
      }
    })
    .catch((err) => {
      console.error("Error al actualizar venta:", err);
      alert("❌ Error al actualizar venta.");
    });
});
