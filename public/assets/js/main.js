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

document.querySelectorAll('.cart-table input[type="number"]').forEach((input) => {
  input.addEventListener('change', (event) => {
    const newQuantity = event.target.value;
    const productId = event.target.getAttribute('data-product-id');

    if (newQuantity < 1) {
      event.target.value = 1;
      return;
    }
    else if (newQuantity > parseInt(event.target.max)) {
      event.target.value = event.target.max;
      return;
    }

    fetch('/cart/update', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
      },
      body: JSON.stringify({ product_id: Number(productId), quantity: Number(newQuantity) }),
    })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          const row = event.target.closest('tr');
          const price = Number(row.querySelector('td:nth-child(2)').textContent.replace(/[^0-9]/g, ''));
          const totalCell = row.querySelector('td:nth-child(4)');
          totalCell.textContent = (price * newQuantity).toLocaleString('vi-VN') + 'đ';

          const totalDisplay = document.querySelector('.cart-total');
          if (totalDisplay) {
            const totalItems = Array.from(document.querySelectorAll('.cart-table tbody tr')).reduce((sum, tr) => {
              const qty = Number(tr.querySelector('input[type="number"]').value);
              return sum + qty;
            }, 0);
            const totalPrice = Array.from(document.querySelectorAll('.cart-table tbody tr')).reduce((sum, tr) => {
              const price = Number(tr.querySelector('td:nth-child(2)').textContent.replace(/[^0-9]/g, ''));
              const qty = Number(tr.querySelector('input[type="number"]').value);
              return sum + (price * qty);
            }, 0);

            totalDisplay.querySelector('span').textContent = 'x ' + totalItems;
            totalDisplay.querySelector('.total-price').innerHTML = 'Tổng tiền: <strong class="text-primary">' + totalPrice.toLocaleString('vi-VN') + 'đ' + '</strong>';
          }
        }
      })
      .catch(error => {
        console.error('Error updating cart:', error);
      });
  });
});
