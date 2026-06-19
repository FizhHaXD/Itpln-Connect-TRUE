// Posts page - Dual column (Community + Global)

// Community Posts
let communityPage = 1;
let communityTotalPages = 1;
let isLoadingCommunity = false;

// Global Posts
let globalPage = 1;
let globalTotalPages = 1;
let isLoadingGlobal = false;

// Load Community Posts
function loadCommunityPosts(reset = true) {
  if (reset) {
    communityPage = 1;
    document.getElementById('communityPostsContainer').innerHTML = '<p class="text-center">⏳ Memuat...</p>';
  }
  
  if (isLoadingCommunity) return;
  isLoadingCommunity = true;
  
  const filterMyCommunities = document.getElementById('filterMyCommunities').checked ? 1 : 0;
  
  fetch(`load_posts.php?page=${communityPage}&my_communities=${filterMyCommunities}`)
    .then(res => res.json())
    .then(data => {
      if (reset) {
        document.getElementById('communityPostsContainer').innerHTML = data.html;
      } else {
        document.getElementById('communityPostsContainer').innerHTML += data.html;
      }
      
      communityTotalPages = data.totalPages;
      
      // Show/hide load more
      if (communityPage < communityTotalPages) {
        document.getElementById('loadMoreCommunity').style.display = 'block';
      } else {
        document.getElementById('loadMoreCommunity').style.display = 'none';
      }
      
      isLoadingCommunity = false;
    })
    .catch(err => {
      console.error('Error loading community posts:', err);
      document.getElementById('communityPostsContainer').innerHTML = '<p class="alert alert-error">Gagal memuat postingan komunitas</p>';
      isLoadingCommunity = false;
    });
}

function loadMoreCommunityPosts() {
  communityPage++;
  loadCommunityPosts(false);
}

// Load Global Posts
function loadGlobalPosts(reset = true) {
  if (reset) {
    globalPage = 1;
    document.getElementById('globalPostsContainer').innerHTML = '<p class="text-center">⏳ Memuat...</p>';
  }
  
  if (isLoadingGlobal) return;
  isLoadingGlobal = true;
  
  fetch(`../dashboard/load_global_posts.php`)
    .then(res => res.json())
    .then(data => {
      const container = document.getElementById('globalPostsContainer');
      
      if (data.length === 0) {
        container.innerHTML = '<p class="posts-empty">Belum ada postingan global. <a href="create_global.php" class="btn btn-sm btn-success">✏️ Buat Sekarang!</a></p>';
        isLoadingGlobal = false;
        return;
      }
      
      let html = '';
      data.forEach(post => {
        html += `
          <div class="global-post">
            <div class="global-post-header">
              <img src="${post.foto}" class="global-post-avatar" alt="${escapeHtml(post.nama)}" loading="lazy">
              <div style="flex: 1;">
                <div class="global-post-name">${escapeHtml(post.nama)}</div>
                <div class="global-post-time">${post.created_at}</div>
              </div>
            </div>
            <p class="global-post-content">${escapeHtml(post.content).replace(/\\n/g, '<br>')}</p>
          </div>
        `;
      });
      
      container.innerHTML = html;
      isLoadingGlobal = false;
      
      // Hide load more for now (since we're showing all)
      document.getElementById('loadMoreGlobal').style.display = 'none';
    })
    .catch(err => {
      console.error('Error loading global posts:', err);
      document.getElementById('globalPostsContainer').innerHTML = '<p class="alert alert-error">Gagal memuat postingan global</p>';
      isLoadingGlobal = false;
    });
}

function loadMoreGlobalPosts() {
  // Future implementation for pagination
}

// Helper
function escapeHtml(text) {
  const div = document.createElement('div');
  div.textContent = text || '';
  return div.innerHTML;
}

// Filter change
document.getElementById('filterMyCommunities').addEventListener('change', () => loadCommunityPosts());

// Initial load
loadCommunityPosts();
loadGlobalPosts();
