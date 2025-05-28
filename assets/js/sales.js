document.addEventListener("DOMContentLoaded", function () {
  const input = document.getElementById("customer");
  const sugerencias = document.getElementById("sugerencias");

  input.addEventListener("input", function () {
    const valor = input.value;

    if (valor.length < 2) {
      sugerencias.innerHTML = "";
      return;
    }

    fetch("assets/queries/buscar_cliente.php?q=" + encodeURIComponent(valor))
      .then((res) => res.text())
      .then((data) => {
        sugerencias.innerHTML = data;
      });
  });

  document.addEventListener("click", function (e) {
    if (e.target.classList.contains("opcion-cliente")) {
      input.value = e.target.textContent;
      sugerencias.innerHTML = "";
    } else if (!sugerencias.contains(e.target)) {
      sugerencias.innerHTML = "";
    }
  });
});
