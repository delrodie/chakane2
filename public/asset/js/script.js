document.addEventListener("DOMContentLoaded", function(){
	let topBtn = document.getElementById('btnToTop');
	window.addEventListener('scroll', function() {
		if (window.scrollY > 50) {
			document.getElementById('menuTop').classList.add('fixed-top');
			// add padding top to show content behind navbar
			navbar_height = document.querySelector('.navbar').offsetHeight;
			document.body.style.paddingTop = navbar_height + 'px';
			document.getElementById('menuTop').classList.remove('bg-transparent');
			document.getElementById('menuTop').classList.add('bgmenu-scrol');
			topBtn.style.display = "block";
			document.getElementById("menuTop").classList.remove('invisible');
		} else {
			document.getElementById('menuTop').classList.remove('fixed-top');
			// remove padding top from body
			document.body.style.paddingTop = '0';
			//document.getElementById('menuTop').classList.add('bg-transparent');
			document.getElementById('menuTop').classList.remove('bgmenu-scrol');
			topBtn.style.display = "none";
			//document.querySelector(".navbar-brand").style.display = "none";
			//document.getElementById("navbar_logo").classList.add('invisible');
		} 
	});

	if (window.innerWidth > 992) {

		document.querySelectorAll('.navbar .dropdown').forEach(function(everyitem){

			everyitem.addEventListener('mouseover', function(e){

				let el_link = this.querySelector('a[data-bs-toggle]');

				if(el_link != null){
					let nextEl = el_link.nextElementSibling;
					el_link.classList.add('show');
					nextEl.classList.add('show');
				}

			});
			everyitem.addEventListener('mouseleave', function(e){
				let el_link = this.querySelector('a[data-bs-toggle]');

				if(el_link != null){
					let nextEl = el_link.nextElementSibling;
					el_link.classList.remove('show');
					nextEl.classList.remove('show');
				}


			})
		});

	}
});



$(document).ready(function () {
	$("#btnToTop").on('click', function topFunction() {
		document.body.scrollTop = 0;
  		document.documentElement.scrollTop = 0;
	})
})

$(document).ready(function () {
	if (window.matchMedia("(max-width: 425px)").matches) {		
		document.getElementById('inputGroup').classList.add('input-group-sm');
	  } else {
		/* the view port is less than 400 pixels wide */
	  }
	  
})

$(document).ready(function () {
	new VenoBox({
		selector: '.creation',
		numeration: true,
		infinigall: true,
		share: true,
		spinner: 'rotating-plane'
	});
});

