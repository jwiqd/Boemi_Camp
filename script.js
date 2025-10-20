// ===== HERO SLIDESHOW =====
let currentSlide = 0;
const slides = document.querySelectorAll('.hero .slide');

function showSlide(index) {
  slides.forEach((slide, i) => {
    slide.classList.toggle('active', i === index);
  });
}
function nextSlide() {
  currentSlide = (currentSlide + 1) % slides.length;
  showSlide(currentSlide);
}
setInterval(nextSlide, 5000); // Ganti tiap 5 detik

// ===== SLIDER KATALOG =====
const katalogContainer = document.querySelector('.katalog-container');
const katalogPrev = document.querySelector('.slider-btn.prev');
const katalogNext = document.querySelector('.slider-btn.next');

if (katalogPrev && katalogNext && katalogContainer) {
  katalogPrev.addEventListener('click', () => {
    katalogContainer.scrollBy({ left: -300, behavior: 'smooth' });
  });
  katalogNext.addEventListener('click', () => {
    katalogContainer.scrollBy({ left: 300, behavior: 'smooth' });
  });
}

// ===== TESTIMONI SLIDER =====
const testiContainer = document.querySelector('.testimoni-container');
const testiPrev = document.querySelector('.testi-btn.prev');
const testiNext = document.querySelector('.testi-btn.next');

if (testiPrev && testiNext && testiContainer) {
  testiPrev.addEventListener('click', () => {
    testiContainer.scrollBy({ left: -300, behavior: 'smooth' });
  });
  testiNext.addEventListener('click', () => {
    testiContainer.scrollBy({ left: 300, behavior: 'smooth' });
  });
}

// ===== AUTO SLIDER TESTIMONI =====
let testiIndex = 0;
const testiCards = document.querySelectorAll('.testimoni-card');
function showTesti(index) {
  testiCards.forEach((card, i) => {
    card.classList.toggle('active', i === index);
  });
}
function nextTesti() {
  testiIndex = (testiIndex + 1) % testiCards.length;
  showTesti(testiIndex);
}
setInterval(nextTesti, 4000); // Auto slide setiap 4 detik

// ===== ANIMASI SCROLL (FADE UP) =====
const fadeEls = document.querySelectorAll('.fade-up');
function checkFade() {
  fadeEls.forEach(el => {
    const rect = el.getBoundingClientRect();
    if (rect.top < window.innerHeight - 100) {
      el.classList.add('visible');
    }
  });
}
window.addEventListener('scroll', checkFade);
window.addEventListener('load', checkFade);
