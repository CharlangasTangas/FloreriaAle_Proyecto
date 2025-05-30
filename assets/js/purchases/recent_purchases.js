document.addEventListener("DOMContentLoaded", function () {
  const tablaBody = document.querySelector("#tabla-compras tbody");
  const inputBusqueda = document.getElementById("purchase-search");
  let comprasGlobal = [];

  function renderCompras(listaCompras) {
    tablaBody.innerHTML = "";

    listaCompras.forEach((compra) => {
      const fila = document.createElement("tr");
      fila.classList.add("border-b");

      fila.innerHTML = `
        <td class="p-3 font-medium">
          <span class="text-purple-500">#${compra.id}</span>
        </td>
        <td class="p-3">${compra.supplier}</td>
        <td class="p-3">${compra.date}</td>
        <td class="p-3">$${compra.total}</td>
        <td class="p-3">
          <span class="rounded-full px-2 py-1 text-xs font-medium bg-green-100 text-green-800">Completed</span>
        </td>
      `;

      tablaBody.appendChild(fila);
    });
  }

  function aplicarFiltro() {
    const termino = inputBusqueda.value.trim().toLowerCase();
    const filtradas = comprasGlobal.filter((compra) => {
      return (
        compra.id.toString().includes(termino) ||
        compra.supplier.toLowerCase().includes(termino) ||
        compra.date.includes(termino)
      );
    });

    renderCompras(filtradas);
  }

  // Cargar las compras al inicio
  fetch("/fbd/FloreriaAle_Proyecto/assets/queries/obtener_compras.php")
    .then((res) => res.json())
    .then((datos) => {
      comprasGlobal = datos;
      renderCompras(comprasGlobal);
    })
    .catch((err) => {
      console.error("Error al cargar compras:", err);
    });

  inputBusqueda.addEventListener("input", aplicarFiltro);
});
