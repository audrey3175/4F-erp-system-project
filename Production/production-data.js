import { getWorkOrders } from "../dataconnect-generated/esm/index.esm.js";

async function loadWorkOrders() {
  const workOrderList = document.getElementById("workOrderList");

  if (!workOrderList) {
    console.warn("Element #workOrderList belum ditemukan di production.html");
    return;
  }

  workOrderList.innerHTML = `
    <div class="wo-list-item">
      <p>Loading work orders...</p>
    </div>
  `;

  try {
    const { data } = await getWorkOrders();
    const workOrders = data.workOrders || [];

    if (workOrders.length === 0) {
      workOrderList.innerHTML = `
        <div class="wo-list-item">
          <p>Belum ada data Work Order.</p>
        </div>
      `;
      return;
    }

    workOrderList.innerHTML = "";

    workOrders.forEach((wo, index) => {
      const item = document.createElement("div");
      item.className = index === 0 ? "wo-list-item selected" : "wo-list-item";

      item.innerHTML = `
        <div class="wo-no-row">
          <strong>${wo.poNumber}</strong>
          <span>${wo.status || "-"}</span>
        </div>

        <div class="wo-info">
          <p><strong>Client:</strong> ${wo.clientName || "-"}</p>
          <p><strong>Product:</strong> ${wo.product?.name || "-"}</p>
          <p><strong>Line:</strong> ${wo.line?.lineName || "-"}</p>
          <p><strong>Target:</strong> ${wo.targetQuantity || 0} ${wo.product?.unitType || ""}</p>
          <p><strong>Date:</strong> ${wo.targetDate || "-"}</p>
          <p><strong>Shift:</strong> ${wo.shift || "-"}</p>
        </div>
      `;

      item.addEventListener("click", () => {
        document.querySelectorAll(".wo-list-item").forEach((el) => {
          el.classList.remove("selected");
        });

        item.classList.add("selected");
        showWorkOrderDetail(wo);
      });

      workOrderList.appendChild(item);
    });

    showWorkOrderDetail(workOrders[0]);
  } catch (error) {
    console.error("Gagal mengambil Work Order:", error);

    workOrderList.innerHTML = `
      <div class="wo-list-item">
        <p>Gagal mengambil data Work Order dari Firebase.</p>
      </div>
    `;
  }
}

function showWorkOrderDetail(wo) {
  const title = document.getElementById("selectedWorkOrderTitle");
  const product = document.getElementById("selectedProductName");
  const target = document.getElementById("selectedTargetQuantity");
  const status = document.getElementById("selectedStatus");
  const shift = document.getElementById("selectedShift");

  if (title) title.textContent = wo.poNumber || "-";
  if (product) product.textContent = wo.product?.name || "-";
  if (target) target.textContent = wo.targetQuantity || "0";
  if (status) status.textContent = wo.status || "-";
  if (shift) shift.textContent = wo.shift || "-";
}

document.addEventListener("DOMContentLoaded", loadWorkOrders);