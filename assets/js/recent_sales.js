document.addEventListener("DOMContentLoaded", function () {
  const tbody = document.getElementById("ventas-tbody");

  fetch("/fbd/FloreriaAle_Proyecto/assets/queries/obtener_ventas.php")
    .then((res) => res.json())
    .then((ventas) => {
      const tbody = document.querySelector("#tabla-ventas tbody");
      tbody.innerHTML = "";

      ventas.forEach((venta) => {
        const row = document.createElement("tr");
        row.classList.add("border-b");
        row.innerHTML = `
        <td class="p-3 font-medium">
          <a href="?page=invoices&id=${venta.id}" class="text-purple-500 hover:underline">#${
          venta.id
        }</a>
        </td>
        <td class="p-3">${venta.employee}</td>
        <td class="p-3">${venta.customer}</td>
        <td class="p-3">${venta.date}</td>
        <td class="p-3">$${venta.total}</td>
        <td class="p-3">
          <span class="rounded-full px-2 py-1 text-xs font-medium ${
            venta.status === "Completed"
              ? "bg-green-100 text-green-800"
              : "bg-yellow-100 text-yellow-800"
          }">${venta.status}</span>
        </td>
      `;
        tbody.appendChild(row);
      });
    })
    .catch((err) => {
      console.error("Error al cargar ventas:", err);
    });
});
