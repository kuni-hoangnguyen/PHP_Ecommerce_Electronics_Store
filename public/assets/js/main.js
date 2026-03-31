/////////////
// Swiper //
////////////

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
/////////////
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

//////////////////////////
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
