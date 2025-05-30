document.addEventListener("DOMContentLoaded", () => {
  const urlParams = new URLSearchParams(window.location.search);
  const idVenta = urlParams.get("editar");

  if (!idVenta) return;

  fetch(`/fbd/FloreriaAle_Proyecto/assets/queries/obtener_venta_por_id.php?id=${idVenta}`)
    .then((res) => res.json())
    .then((venta) => {
      if (!venta) return;

      document.getElementById("customer").value = venta.nombreCliente;
      document.getElementById("idCliente").value = venta.idCliente;
      document.getElementById("date").value = venta.fechaEmision;
      document.getElementById("payment-method").value = venta.metodoPago;
      document.getElementById("status").value = venta.estado;
      document.getElementById("notes").value = venta.comentarios;
      document.getElementById("grand-total").textContent = parseFloat(venta.total).toFixed(2);
      document.getElementById("grand-total-input").value = parseFloat(venta.total).toFixed(2);

      const tbody = document.querySelector("#sale-items tbody");
      tbody.innerHTML = "";

      venta.productos.forEach((producto) => {
        const template = document.getElementById("item-row-template");
        const row = template.cloneNode(true);
        row.id = "";

        const select = row.querySelector(".product-select");
        const precioInput = row.querySelector(".item-price");
        const cantidadInput = row.querySelector(".item-quantity");
        const totalInput = row.querySelector(".item-total");

        Array.from(select.options).forEach((opt) => {
          if (opt.value == producto.idProducto) {
            opt.selected = true;
          }
        });

        precioInput.value = parseFloat(producto.precioVenta).toFixed(2);
        cantidadInput.value = producto.cantidad;
        totalInput.value = (producto.precioVenta * producto.cantidad).toFixed(2);

        document.querySelector("#sale-items tbody").appendChild(row);
      });

      // Activar botones de edición (por si estaban desactivados)
      document.getElementById("add-item").disabled = false;
    })
    .catch((err) => console.error("Error al obtener venta:", err));
});
