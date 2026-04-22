/////////////
// Swiper //
///////////

const sliderElements = document.querySelectorAll('.swiper');

sliderElements.forEach((sliderElement) => {
  new Swiper(sliderElement, {
    loop: true,
    slidesPerView: 3,
    spaceBetween: 16,
    breakpoints: {
      576: { slidesPerView: 4 },
      992: { slidesPerView: 5 },
    },
    navigation: {
      nextEl: sliderElement.querySelector('.swiper-button-next'),
      prevEl: sliderElement.querySelector('.swiper-button-prev'),
    },
    scrollbar: {
      el: sliderElement.querySelector('.swiper-scrollbar'),
      draggable: true,
    },
  });
});

const cateSliderElements = document.querySelectorAll('.swiper-category');

cateSliderElements.forEach((sliderElement) => {
  new Swiper(sliderElement, {
    loop: true,
    slidesPerView: 2,
    spaceBetween: 16,
    navigation: {
      nextEl: sliderElement.querySelector('.swiper-button-next'),
      prevEl: sliderElement.querySelector('.swiper-button-prev'),
    }
  });
});

//////////////
// Gallery //
////////////

jQuery(document).ready(function () {
  jQuery('.ecommerce-gallery').lightSlider({
    gallery: true,
    item: 1,
    loop: true,
    thumbItem: 4,
    slideMargin: 0,
    enableDrag: true,
    currentPagerPosition: 'left',
  });
});

////////////////////////////
// Products Price Filter //
//////////////////////////

const priceFilterForms = document.querySelectorAll('[data-price-filter]');

priceFilterForms.forEach((form) => {
  const minInput = form.querySelector('[data-min-price-input]');
  const maxInput = form.querySelector('[data-max-price-input]');
  const minDisplay = form.querySelector('[data-min-price-display]');
  const maxDisplay = form.querySelector('[data-max-price-display]');

  if (!minInput || !maxInput || !minDisplay || !maxDisplay) {
    return;
  }

  const formatCurrency = (value) => Number(value).toLocaleString('vi-VN');

  const syncRange = (source) => {
    const minValue = Number(minInput.value);
    const maxValue = Number(maxInput.value);

    if (minValue > maxValue) {
      if (source === 'min') {
        maxInput.value = String(minValue);
      } else {
        minInput.value = String(maxValue);
      }
    }

    minDisplay.textContent = formatCurrency(minInput.value);
    maxDisplay.textContent = formatCurrency(maxInput.value);
  };

  minInput.addEventListener('input', () => syncRange('min'));
  maxInput.addEventListener('input', () => syncRange('max'));
  syncRange('min');
});

///////////////////////////
// Cart quantity update //
/////////////////////////

document.addEventListener('DOMContentLoaded', () => {
  const cartTable = document.querySelector('.cart-table');
  if (!cartTable) {
    return;
  }

  const checkoutBtn = document.getElementById('checkout-selected-btn');
  const selectedSummaryList = document.getElementById('selected-summary-list');
  const selectedSummaryEmpty = document.getElementById('selected-summary-empty');
  const selectedSummaryTotal = document.getElementById('selected-summary-total');

  const formatCurrency = (value) => Number(value || 0).toLocaleString('vi-VN') + 'đ';

  const getSelectedIds = () => Array
    .from(document.querySelectorAll('.cart-item-checkbox:checked'))
    .map((input) => Number(input.value))
    .filter((id) => Number.isInteger(id) && id > 0);

  const refreshSelectedSummary = () => {
    const selectedIds = getSelectedIds();

    if (!selectedSummaryList || !selectedSummaryTotal) {
      return;
    }

    if (selectedIds.length === 0) {
      selectedSummaryList.innerHTML = '<li class="list-group-item text-muted" id="selected-summary-empty">Chưa chọn sản phẩm.</li>';
      selectedSummaryTotal.textContent = '0đ';
      return;
    }

    fetch('/cart/summary', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
      },
      body: JSON.stringify({ product_ids: selectedIds }),
    })
      .then((response) => response.json())
      .then((data) => {
        if (!data.success) {
          return;
        }

        const items = Array.isArray(data.items) ? data.items : [];

        if (items.length === 0) {
          selectedSummaryList.innerHTML = '<li class="list-group-item text-muted" id="selected-summary-empty">Chưa chọn sản phẩm.</li>';
          selectedSummaryTotal.textContent = '0đ';
          return;
        }

        selectedSummaryList.innerHTML = items
          .map((item) => '<li class="list-group-item d-flex justify-content-between align-items-center">'
              + item.name
              + '<span class="badge text-primary">x '
              + Number(item.quantity)
              + '</span></li>')
          .join('');

        selectedSummaryTotal.textContent = formatCurrency(data.total_amount || 0);
      })
      .catch((error) => {
        console.error('Error loading selected cart summary:', error);
      });
  };

  document.querySelectorAll('.cart-item-checkbox').forEach((checkbox) => {
    checkbox.addEventListener('change', refreshSelectedSummary);
  });

  document.querySelectorAll('.cart-table input[type="number"]').forEach((input) => {
    input.addEventListener('change', (event) => {
      const target = event.target;
      const newQuantity = Number(target.value);
      const maxQuantity = Number(target.max);
      const productId = Number(target.getAttribute('data-product-id'));

      if (newQuantity < 1) {
        target.value = '1';
        return;
      }

      if (newQuantity > maxQuantity) {
        target.value = String(maxQuantity);
        return;
      }

      fetch('/cart/update', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
        },
        body: JSON.stringify({ product_id: productId, quantity: newQuantity }),
      })
        .then((response) => response.json())
        .then((data) => {
          if (!data.success) {
            return;
          }

          const row = target.closest('tr');
          if (!row) {
            return;
          }

          const unitPriceCell = row.querySelector('td:nth-child(3)');
          const rowTotalCell = row.querySelector('td:nth-child(5)');
          if (!unitPriceCell || !rowTotalCell) {
            return;
          }

          const unitPrice = Number(unitPriceCell.textContent.replace(/[^0-9]/g, ''));
          rowTotalCell.textContent = formatCurrency(unitPrice * Number(target.value));

          refreshSelectedSummary();
        })
        .catch((error) => {
          console.error('Error updating cart quantity:', error);
        });
    });
  });

  if (checkoutBtn) {
    checkoutBtn.addEventListener('click', () => {
      const selectedIds = getSelectedIds();

      if (selectedIds.length === 0) {
        alert('Vui lòng chọn ít nhất 1 sản phẩm để thanh toán.');
        return;
      }

      window.location.href = '/checkout?selected=' + encodeURIComponent(selectedIds.join(','));
    });
  }

  if (selectedSummaryEmpty) {
    refreshSelectedSummary();
  }
});

////////////////////////////////////
// Product Specification Builder //
//////////////////////////////////

document.addEventListener('DOMContentLoaded', function () {
    const builders = document.querySelectorAll('[data-spec-builder]');

    const createSpecRow = function (name, value) {
        const row = document.createElement('div');
        row.className = 'row g-2';
        row.setAttribute('data-spec-row', '');
        row.innerHTML = '' +
            '<div class="col-5">' +
                '<input type="text" class="form-control" name="spec_name[]" data-spec-name placeholder="name (vd: ram)">' +
            '</div>' +
            '<div class="col-5">' +
                '<input type="text" class="form-control" name="spec_value[]" data-spec-value placeholder="value (vd: 8GB)">' +
            '</div>' +
            '<div class="col-2 d-grid">' +
                '<button type="button" class="btn btn-outline-danger" data-spec-remove>X</button>' +
            '</div>';

        row.querySelector('[data-spec-name]').value = name || '';
        row.querySelector('[data-spec-value]').value = value || '';
        return row;
    };

    const removeRow = function (builder, row) {
        const rowsContainer = builder.querySelector('[data-spec-rows]');
        if (!rowsContainer || !row) {
            return;
        }

        const rows = rowsContainer.querySelectorAll('[data-spec-row]');
        if (rows.length <= 1) {
            const nameInput = row.querySelector('[data-spec-name]');
            const valueInput = row.querySelector('[data-spec-value]');
            if (nameInput) {
                nameInput.value = '';
            }
            if (valueInput) {
                valueInput.value = '';
            }
            return;
        }

        row.remove();
    };

    builders.forEach(function (builder) {
        const addButton = builder.querySelector('[data-spec-add]');
        const rowsContainer = builder.querySelector('[data-spec-rows]');

        if (!addButton || !rowsContainer) {
            return;
        }

        addButton.addEventListener('click', function () {
            rowsContainer.appendChild(createSpecRow('', ''));
        });

        builder.addEventListener('click', function (event) {
            const target = event.target;
            if (!(target instanceof HTMLElement)) {
                return;
            }

            if (target.matches('[data-spec-remove]')) {
                const row = target.closest('[data-spec-row]');
                removeRow(builder, row);
            }
        });
    });

});

///////////////////////////
// Product Image Builder //
/////////////////////////

document.addEventListener('DOMContentLoaded', function () {
  const imageBuilders = document.querySelectorAll('[data-image-builder]');

  const syncPreview = function (row) {
    const fileInput = row.querySelector('[data-image-file]');
    const preview = row.querySelector('[data-image-preview]');

    if (!(fileInput instanceof HTMLInputElement) || !(preview instanceof HTMLImageElement)) {
      return;
    }

    if (!fileInput.files || fileInput.files.length === 0) {
      preview.src = '';
      preview.classList.add('d-none');
      return;
    }

    const file = fileInput.files[0];
    if (!file.type.startsWith('image/')) {
      preview.src = '';
      preview.classList.add('d-none');
      return;
    }

    preview.src = URL.createObjectURL(file);
    preview.classList.remove('d-none');
  };

  const refreshSlots = function (builder) {
    const rows = builder.querySelectorAll('[data-image-row]');
    const prefix = builder.getAttribute('data-image-prefix') || 'slot';

    rows.forEach(function (row, index) {
      const slotValue = prefix + '_' + index;
      const slotInput = row.querySelector('[data-image-slot]');
      const primaryInput = row.querySelector('[data-image-primary]');

      if (slotInput instanceof HTMLInputElement) {
        slotInput.value = slotValue;
      }

      if (primaryInput instanceof HTMLInputElement) {
        primaryInput.value = slotValue;
      }
    });
  };

  const createImageRow = function (radioName) {
    const row = document.createElement('div');
    row.className = 'row g-2 align-items-center';
    row.setAttribute('data-image-row', '');
    row.innerHTML = '' +
      '<div class="col-5">' +
        '<input type="file" class="form-control" name="product_images[]" data-image-file accept=".jpg,.jpeg,.png,.webp,.gif">' +
        '<input type="hidden" name="product_image_slot[]" data-image-slot value="">' +
      '</div>' +
      '<div class="col-3">' +
        '<img src="" alt="preview" class="img-thumbnail d-none" data-image-preview style="max-width: 72px; max-height: 72px; object-fit: cover;">' +
      '</div>' +
      '<div class="col-3">' +
        '<div class="form-check">' +
          '<input class="form-check-input" type="radio" name="' + radioName + '" data-image-primary value="">' +
          '<label class="form-check-label">Ảnh chính</label>' +
        '</div>' +
      '</div>' +
      '<div class="col-1 d-grid">' +
        '<button type="button" class="btn btn-outline-danger" data-image-remove>X</button>' +
      '</div>';

    return row;
  };

  imageBuilders.forEach(function (builder, builderIndex) {
    const rowsContainer = builder.querySelector('[data-image-rows]');
    const addButton = builder.querySelector('[data-image-add]');

    if (!rowsContainer || !addButton) {
      return;
    }

    const firstPrimary = builder.querySelector('[data-image-primary]');
    const radioName = firstPrimary instanceof HTMLInputElement
      ? firstPrimary.name
      : 'product_image_primary_' + builderIndex;

    builder.setAttribute('data-image-prefix', 'slot_' + builderIndex);
    refreshSlots(builder);

    addButton.addEventListener('click', function () {
      const row = createImageRow(radioName);
      rowsContainer.appendChild(row);
      refreshSlots(builder);
    });

    builder.addEventListener('change', function (event) {
      const target = event.target;
      if (!(target instanceof HTMLElement)) {
        return;
      }

      if (target.matches('[data-image-file]')) {
        const row = target.closest('[data-image-row]');
        if (row) {
          syncPreview(row);
        }
      }
    });

    builder.addEventListener('click', function (event) {
      const target = event.target;
      if (!(target instanceof HTMLElement) || !target.matches('[data-image-remove]')) {
        return;
      }

      const row = target.closest('[data-image-row]');
      if (!row) {
        return;
      }

      const rows = rowsContainer.querySelectorAll('[data-image-row]');
      if (rows.length <= 1) {
        const fileInput = row.querySelector('[data-image-file]');
        const preview = row.querySelector('[data-image-preview]');
        if (fileInput instanceof HTMLInputElement) {
          fileInput.value = '';
        }
        if (preview instanceof HTMLImageElement) {
          preview.src = '';
          preview.classList.add('d-none');
        }
        return;
      }

      const selectedPrimary = row.querySelector('[data-image-primary]');
      const wasPrimary = selectedPrimary instanceof HTMLInputElement ? selectedPrimary.checked : false;
      row.remove();

      refreshSlots(builder);

      if (wasPrimary) {
        const nextPrimary = rowsContainer.querySelector('[data-image-primary]');
        if (nextPrimary instanceof HTMLInputElement) {
          nextPrimary.checked = true;
        }
      }
    });
  });
});

////////////////////////
// Admin Dashboard JS //
//////////////////////

document.addEventListener('DOMContentLoaded', function () {
  const parseChartData = function (rawValue) {
    if (!rawValue) {
      return [];
    }

    try {
      const parsed = JSON.parse(rawValue);
      return Array.isArray(parsed) ? parsed : [];
    } catch (error) {
      return [];
    }
  };

  const chartCanvas = document.getElementById('dashboardRevenueOrderChart');
  if (chartCanvas && typeof Chart === 'function') {
    const labels = parseChartData(chartCanvas.dataset.chartLabels || '[]');
    const revenueData = parseChartData(chartCanvas.dataset.chartRevenue || '[]');
    const orderData = parseChartData(chartCanvas.dataset.chartOrders || '[]');

    new Chart(chartCanvas, {
      type: 'bar',
      data: {
        labels,
        datasets: [
          {
            label: 'Doanh thu (đ)',
            data: revenueData,
            backgroundColor: 'rgba(13, 110, 253, 0.5)',
            borderColor: 'rgba(13, 110, 253, 1)',
            borderWidth: 1,
            yAxisID: 'yRevenue',
          },
          {
            label: 'Số đơn',
            data: orderData,
            type: 'line',
            borderColor: 'rgba(220, 53, 69, 1)',
            backgroundColor: 'rgba(220, 53, 69, 0.2)',
            tension: 0.25,
            yAxisID: 'yOrders',
          },
        ],
      },
      options: {
        responsive: true,
        interaction: {
          mode: 'index',
          intersect: false,
        },
        scales: {
          yRevenue: {
            type: 'linear',
            position: 'left',
            beginAtZero: true,
          },
          yOrders: {
            type: 'linear',
            position: 'right',
            beginAtZero: true,
            grid: {
              drawOnChartArea: false,
            },
          },
        },
      },
    });
  }

  const groupBySelect = document.getElementById('dashboard-group-by');
  const filterForm = document.getElementById('dashboard-filter-form');
  if (groupBySelect && filterForm) {
    groupBySelect.addEventListener('change', function () {
      filterForm.submit();
    });
  }
});
