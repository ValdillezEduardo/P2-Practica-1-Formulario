// Al elegir categoría, carga las marcas desde obtener_marcas.php
const categoria = document.getElementById("categoria");
const marca = document.getElementById("marca");

categoria.addEventListener("change", async () => {
  marca.innerHTML = "";
  if (!categoria.value) {
    marca.disabled = true;
    marca.innerHTML = '<option value="">Primero elige una categoría</option>';
    return;
  }
  const resp = await fetch("obtener_marcas.php?categoria=" + encodeURIComponent(categoria.value));
  const marcas = await resp.json();

  marca.innerHTML = '<option value="">Elige una marca</option>';
  marcas.forEach(m => {
    const op = document.createElement("option");
    op.value = m.id;
    op.textContent = m.nombre;
    marca.appendChild(op);
  });
  marca.disabled = false;
});
