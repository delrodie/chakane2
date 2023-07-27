// Fonction pour gérer le scroll et le bouton "To Top"
function setupScrollAndToTop() {
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

	$("#btnToTop").on('click', function topFunction() {
		document.body.scrollTop = 0;
		document.documentElement.scrollTop = 0;
	});
}

// Fonction pour gérer les événements de hover sur les éléments du menu
function setupMenuHover() {
	if (window.innerWidth > 992) {
		document.querySelectorAll('.navbar .dropdown').forEach(function(everyitem) {
			everyitem.addEventListener('mouseover', function(e) {
				let el_link = this.querySelector('a[data-bs-toggle]');
				if(el_link != null){
					let nextEl = el_link.nextElementSibling;
					el_link.classList.add('show');
					nextEl.classList.add('show');
				}
			});
			everyitem.addEventListener('mouseleave', function(e) {
				let el_link = this.querySelector('a[data-bs-toggle]');
				if(el_link != null){
					let nextEl = el_link.nextElementSibling;
					el_link.classList.remove('show');
					nextEl.classList.remove('show');
				}
			});
		});
	}
}

// Fonction pour ajouter la classe active au menu cliqué
function setActiveMenu() {
	const menuLinks = document.querySelectorAll('.menu-link');

	// Supprimer la classe active de tous les liens du menu
	menuLinks.forEach(link => {
		link.classList.remove('active');
	});

	// Ajouter la classe active au lien cliqué
	this.classList.add('active');

	// Stocker l'index de l'élément actif dans le localStorage
	const activeIndex = Array.from(menuLinks).indexOf(this);
	localStorage.setItem('activeMenuIndex', activeIndex);
}

document.addEventListener('DOMContentLoaded', () => {
	setupScrollAndToTop();
	setupMenuHover();

	const menuLinks = document.querySelectorAll('.menu-link');

	// Récupérer l'index de l'élément actif à partir du localStorage
	const activeIndex = localStorage.getItem('activeMenuIndex');

	// Si un index est stocké, ajouter la classe active à l'élément correspondant
	if (activeIndex !== null) {
		menuLinks[activeIndex].classList.add('active');
	}

	// Associer l'événement de clic à chaque lien du menu
	menuLinks.forEach(link => {
		link.addEventListener('click', setActiveMenu);
	});

});

