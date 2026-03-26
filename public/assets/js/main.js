// Horizontal slider drag-to-scroll
document.addEventListener('DOMContentLoaded', function () {
	var sliders = document.querySelectorAll('.slider');

	sliders.forEach(function (slider) {
		var isDown = false;
		var startX;
		var scrollLeft;

		slider.addEventListener('mousedown', function (e) {
			isDown = true;
			slider.classList.add('dragging');
			startX = e.pageX - slider.offsetLeft;
			scrollLeft = slider.scrollLeft;
		});

		slider.addEventListener('mouseleave', function () {
			isDown = false;
			slider.classList.remove('dragging');
		});

		slider.addEventListener('mouseup', function () {
			isDown = false;
			slider.classList.remove('dragging');
		});

		slider.addEventListener('mousemove', function (e) {
			if (!isDown) return;
			e.preventDefault();
			var x = e.pageX - slider.offsetLeft;
			var walk = (x - startX) * 1; // scroll-fast factor
			slider.scrollLeft = scrollLeft - walk;
		});

		// Touch support
		slider.addEventListener('touchstart', function (e) {
			isDown = true;
			startX = e.touches[0].pageX - slider.offsetLeft;
			scrollLeft = slider.scrollLeft;
		}, { passive: true });

		slider.addEventListener('touchend', function () {
			isDown = false;
		});

		slider.addEventListener('touchmove', function (e) {
			if (!isDown) return;
			var x = e.touches[0].pageX - slider.offsetLeft;
			var walk = (x - startX) * 1;
			slider.scrollLeft = scrollLeft - walk;
		}, { passive: true });
	});
});

