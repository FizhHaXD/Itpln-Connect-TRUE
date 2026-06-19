// Carousel functionality for dashboard
let carouselPosts = [];
let currentSlide = 0;
let autoSlideTimer = null;

// Load posts
function loadCarouselPosts() {
  fetch('load_carousel_posts.php')
    .then(res => res.json())
    .then(data => {
      carouselPosts = data;
      if (carouselPosts.length > 0) {
        renderCarousel();
        startAutoSlide();
      } else {
        document.getElementById('carouselSlide').innerHTML = 
          '<p class="empty-message">Belum ada postingan eksternal.</p>';
      }
    })
    .catch(err => {
      console.error('Error loading carousel:', err);
      document.getElementById('carouselSlide').innerHTML = 
        '<p class="alert alert-error">Gagal memuat postingan.</p>';
    });
}

// Render carousel with smooth fade transition
function renderCarousel() {
  const post = carouselPosts[currentSlide];
  const slide = document.getElementById('carouselSlide');
  
  // Add fade-out class
  slide.classList.add('carousel-fade-out');
  
  // Wait for fade-out, then change content
  setTimeout(() => {
    // Build photos HTML
    let photosHtml = '';
    if (post.foto || post.foto2 || post.foto3) {
      photosHtml = '<div class="carousel-photos">';
      if (post.foto) photosHtml += `<img src="${post.foto}" alt="Post" class="carousel-img" loading="lazy">`;
      if (post.foto2) photosHtml += `<img src="${post.foto2}" alt="Post" class="carousel-img" loading="lazy">`;
      if (post.foto3) photosHtml += `<img src="${post.foto3}" alt="Post" class="carousel-img" loading="lazy">`;
      photosHtml += '</div>';
    }
    
    slide.innerHTML = `
      <div class="carousel-post">
        <div class="post-header">
          <img src="${post.foto_user}" class="post-avatar" alt="User" loading="lazy">
          <div>
            <b>${escapeHtml(post.user)}</b><br>
            <small class="text-muted">di ${escapeHtml(post.community)} • ${post.created_at}</small>
          </div>
        </div>
        
        ${post.title ? `<h3>${escapeHtml(post.title)}</h3>` : ''}
        <p>${escapeHtml(post.content).replace(/\n/g, '<br>')}</p>
        
        ${photosHtml}
        
        <div style="text-align: center; margin-top: 20px;">
          <a href="../communities/detail.php?id=${post.id_community}" class="btn btn-sm">
            Lihat Komunitas →
          </a>
        </div>
      </div>
    `;
    
    // Remove fade-out and add fade-in
    slide.classList.remove('carousel-fade-out');
    slide.classList.add('carousel-fade-in');
    
    // Remove fade-in class after animation
    setTimeout(() => {
      slide.classList.remove('carousel-fade-in');
    }, 400);
  }, 200); // Half of transition time
  
  // Render dots
  const dots = document.getElementById('carouselDots');
  dots.innerHTML = carouselPosts.map((_, idx) => 
    `<span class="carousel-dot ${idx === currentSlide ? 'active' : ''}" onclick="goToSlide(${idx})"></span>`
  ).join('');
}

// Navigation
function carouselNext() {
  currentSlide = (currentSlide + 1) % carouselPosts.length;
  renderCarousel();
  resetAutoSlide();
}

function carouselPrev() {
  currentSlide = (currentSlide - 1 + carouselPosts.length) % carouselPosts.length;
  renderCarousel();
  resetAutoSlide();
}

function goToSlide(idx) {
  currentSlide = idx;
  renderCarousel();
  resetAutoSlide();
}

// Auto slide
function startAutoSlide() {
  autoSlideTimer = setInterval(carouselNext, 5000); // 5 seconds
}

function resetAutoSlide() {
  clearInterval(autoSlideTimer);
  startAutoSlide();
}

// Helper
function escapeHtml(text) {
  const div = document.createElement('div');
  div.textContent = text || '';
  return div.innerHTML;
}

// Init
loadCarouselPosts();
