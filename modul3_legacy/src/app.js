// URL endpoint publik API Media/Anime (Kitsu API: Cepat, Gratis, Tanpa API Key, dan Bebas Blokir)
const ANIME_API_URL = 'https://kitsu.io/api/edge/anime?page[limit]=20&sort=-userCount&include=categories';

// State aplikasi
let allMediaData = [];
let activeAura = 'all';
let searchQuery = '';
let isFetchLoading = false;

// Inisialisasi Aplikasi saat DOM selesai dimuat
document.addEventListener('DOMContentLoaded', () => {
  initThemeToggle();
  initMobileMenu();
  initEventListeners();
  fetchMediaData();
});

/**
 * 1. PENGAMBILAN DATA (Fetch API)
 * Memenuhi syarat: "Pengambilan data menggunakan Fetch API",
 * "Loading state saat data sedang diambil", dan "Error message apabila pengambilan data gagal".
 */
async function fetchMediaData() {
  setLoadingState(true);
  hideError();

  // AbortController untuk timeout 8 detik
  const controller = new AbortController();
  const timeoutId = setTimeout(() => controller.abort(), 8000);

  try {
    const response = await fetch(ANIME_API_URL, { signal: controller.signal });
    clearTimeout(timeoutId);

    if (!response.ok) {
      throw new Error(`HTTP ${response.status}: ${response.statusText || 'Gagal memuat respon dari server API'}`);
    }

    const payload = await response.json();
    if (!payload.data || !Array.isArray(payload.data) || payload.data.length === 0) {
      throw new Error('Data yang diterima dari API publik tidak lengkap.');
    }

    // Memetakan kategori / genre dari payload.included
    const categoryMap = new Map();
    if (Array.isArray(payload.included)) {
      payload.included.forEach(item => {
        if (item.type === 'categories' && item.attributes?.title) {
          categoryMap.set(item.id, item.attributes.title);
        }
      });
    }

    // Normalisasi struktur data anime agar seragam & kompatibel dengan seluruh fitur The Vibe Grid
    allMediaData = payload.data.map(item => {
      const attr = item.attributes || {};
      const rawScore = parseFloat(attr.averageRating);
      const score = !isNaN(rawScore) && rawScore > 0 ? parseFloat((rawScore / 10).toFixed(1)) : 8.5;

      const categoryRefs = Array.isArray(item.relationships?.categories?.data)
        ? item.relationships.categories.data
        : [];

      const genres = categoryRefs
        .map(ref => ({ name: categoryMap.get(ref.id) }))
        .filter(g => g.name);

      if (genres.length === 0) {
        genres.push({ name: 'Action' }, { name: 'Adventure' });
      }

      const posterUrl = attr.posterImage?.large || 
                        attr.posterImage?.original || 
                        attr.posterImage?.medium || 
                        attr.posterImage?.small || 
                        'https://images.unsplash.com/photo-1578632767115-351597cf2477?w=600&auto=format&fit=crop';

      return {
        mal_id: parseInt(item.id, 10),
        title: attr.canonicalTitle || attr.titles?.en || attr.titles?.en_jp || 'Judul Anime',
        title_english: attr.titles?.en || attr.canonicalTitle || '',
        images: {
          webp: {
            large_image_url: posterUrl,
            image_url: posterUrl
          }
        },
        type: (attr.subtype || 'TV').toUpperCase(),
        episodes: attr.episodeCount || 24,
        status: attr.status === 'finished' ? 'Selesai Tayang' : 'Sedang Tayang',
        score: score,
        synopsis: attr.synopsis || attr.description || 'Sinopsis belum tersedia untuk judul ini.',
        genres: genres,
        year: attr.startDate ? new Date(attr.startDate).getFullYear() : 2023
      };
    });

    setLoadingState(false);
    renderGrid();
    updateVibeStats();
  } catch (error) {
    clearTimeout(timeoutId);
    console.warn('Gagal memuat dari API publik:', error);
    setLoadingState(false);

    let pesanError = error.message;
    if (error.name === 'AbortError') {
      pesanError = 'Batas waktu koneksi habis (Timeout 8s). Silakan periksa jaringan internet Anda.';
    } else if (error instanceof TypeError && error.message.includes('fetch')) {
      pesanError = 'Gagal terhubung ke server API publik (Koneksi terputus atau jaringan internet offline).';
    }

    showError(pesanError);
  }
}

/**
 * 2. KONTROL TAMPILAN LOADING & ERROR
 */
function setLoadingState(loading) {
  isFetchLoading = loading;
  const loadingEl = document.getElementById('vibe-loading');
  const gridEl = document.getElementById('vibe-grid-cards');
  const emptyEl = document.getElementById('vibe-empty');

  if (loading) {
    if (loadingEl) loadingEl.classList.remove('hidden');
    if (gridEl) gridEl.classList.add('hidden');
    if (emptyEl) emptyEl.classList.add('hidden');
  } else {
    if (loadingEl) loadingEl.classList.add('hidden');
    if (gridEl) gridEl.classList.remove('hidden');
  }
}

function showError(message) {
  const errorContainer = document.getElementById('vibe-error');
  const errorText = document.getElementById('vibe-error-message');
  const gridEl = document.getElementById('vibe-grid-cards');
  const emptyEl = document.getElementById('vibe-empty');

  if (errorText) errorText.textContent = message;
  if (errorContainer) errorContainer.classList.remove('hidden');
  if (gridEl) gridEl.classList.add('hidden');
  if (emptyEl) emptyEl.classList.add('hidden');
}

function hideError() {
  const errorContainer = document.getElementById('vibe-error');
  if (errorContainer) errorContainer.classList.add('hidden');
}

/**
 * 3. LOGIKA AURA-BASED DISCOVERY
 * Mengelompokkan anime ke subset suasana hati berdasarkan genre, skor, dan episode.
 * Menggunakan Array Method: .map() dan .some()
 */
function matchesAuraFilter(anime, aura) {
  if (aura === 'all') return true;

  // Array Method: .map() untuk mengambil daftar genre lowercase
  const genres = anime.genres ? anime.genres.map(g => g.name.toLowerCase()) : [];
  const score = anime.score || 0;
  const episodes = anime.episodes || 0;

  switch (aura) {
    case 'high-energy':
      // Cepat, penuh aksi & semangat: Action, Sports, Shounen, Adventure, Superhero, Super Power
      return genres.some(g => ['action', 'sports', 'adventure', 'shounen', 'sci-fi', 'superhero', 'super power', 'violence'].includes(g));

    case 'melancholy':
      // Emosional, puitis, menyentuh batin: Drama, Romance, Slice of Life, Supernatural, School Life
      return genres.some(g => ['drama', 'romance', 'slice of life', 'supernatural', 'school life'].includes(g));

    case 'binge-mode':
      // Adiktif & epik untuk maraton: Skor tinggi (>= 8.3) atau episode banyak (>= 24)
      return score >= 8.3 || episodes >= 24;

    case 'mind-bending':
      // Memeras otak & intrik psikologis: Mystery, Suspense, Psychological, Sci-Fi, Thriller, Post Apocalypse
      return genres.some(g => ['mystery', 'suspense', 'psychological', 'sci-fi', 'thriller', 'post apocalypse'].includes(g));

    default:
      return true;
  }
}

/**
 * 4. PENGOLAHAN DATA DENGAN ARRAY METHODS
 * Memenuhi syarat: Minimal satu array method (.filter, .map, .find, .forEach, .reduce).
 */
function getFilteredAnime() {
  const query = searchQuery.trim().toLowerCase();

  // Array Method: .filter() untuk memfilter pencarian judul dan filter Aura
  return allMediaData.filter(anime => {
    const title = (anime.title || '').toLowerCase();
    const titleEng = (anime.title_english || '').toLowerCase();
    const genreNames = anime.genres ? anime.genres.map(g => g.name.toLowerCase()).join(' ') : '';

    const matchesSearch = !query || title.includes(query) || titleEng.includes(query) || genreNames.includes(query);
    const matchesAura = matchesAuraFilter(anime, activeAura);

    return matchesSearch && matchesAura;
  });
}

/**
 * 5. RENDERING GRID KARTU DENGAN TEMPLATE LITERAL
 * Memenuhi syarat: "Penggunaan template literal untuk menyusun tampilan data secara dinamis",
 * "Render data ke dalam bentuk card", dan "Array method (map, forEach)".
 */
function renderGrid() {
  const gridContainer = document.getElementById('vibe-grid-cards');
  const emptyState = document.getElementById('vibe-empty');
  const countText = document.getElementById('vibe-count');

  if (!gridContainer) return;

  const filteredList = getFilteredAnime();

  // Perbarui indikator jumlah item
  if (countText) {
    countText.textContent = `Menampilkan ${filteredList.length} dari ${allMediaData.length} judul`;
  }

  // Tampilkan state kosong jika tidak ada hasil
  if (filteredList.length === 0) {
    gridContainer.innerHTML = '';
    if (emptyState) emptyState.classList.remove('hidden');
    return;
  }

  if (emptyState) emptyState.classList.add('hidden');

  // Array Method: .map() dengan Template Literal untuk menyusun HTML setiap kartu
  const cardsHtml = filteredList.map(anime => {
    const imageUrl = anime.images?.webp?.large_image_url || 
                     anime.images?.jpg?.large_image_url || 
                     anime.images?.webp?.image_url || 
                     anime.images?.jpg?.image_url || 
                     'https://images.unsplash.com/photo-1578632767115-351597cf2477?w=600&auto=format&fit=crop';
                     
    const scoreVal = typeof anime.score === 'number' ? anime.score.toFixed(1) : '8.0';
    const episodesVal = anime.episodes ? `${anime.episodes} Eps` : 'Ongoing';
    const typeVal = anime.type || 'TV';
    const titleVal = anime.title || 'Judul Anime';
    const synopsisVal = anime.synopsis 
      ? anime.synopsis.replace(/\[Written by MAL Rewrite\]/g, '').trim()
      : 'Sinopsis belum tersedia untuk judul ini.';

    // Array Method: .map() untuk badge genre
    const genreBadges = (anime.genres || []).slice(0, 2).map(g => 
      `<span class="px-2 py-0.5 text-[11px] rounded font-semibold bg-brand-jingga/10 text-brand-jingga dark:bg-brand-jingga/25 dark:text-brand-kuning border border-brand-jingga/20">${g.name}</span>`
    ).join('');

    return `
      <article 
        class="vibe-card group bg-white dark:bg-brand-gelap rounded-xl overflow-hidden border border-gray-200 dark:border-zinc-800 hover:border-brand-jingga dark:hover:border-brand-jingga shadow-md hover:shadow-2xl dark:shadow-none transition-all duration-300 flex flex-col justify-between transform hover:-translate-y-1.5 cursor-pointer relative"
        data-id="${anime.mal_id}">
        
        <div>
          <!-- Poster Image & Badges -->
          <div class="relative aspect-[3/4] overflow-hidden bg-gray-100 dark:bg-zinc-900">
            <img 
              src="${imageUrl}" 
              alt="${titleVal}" 
              loading="lazy" 
              class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
              onerror="this.src='https://images.unsplash.com/photo-1578632767115-351597cf2477?w=600&auto=format&fit=crop'"
            />
            
            <div class="absolute inset-0 bg-gradient-to-t from-brand-hitam/90 via-brand-hitam/20 to-transparent opacity-80 group-hover:opacity-60 transition-opacity"></div>

            <!-- Skor Rating -->
            <div class="absolute top-3 left-3 flex items-center gap-1.5 bg-brand-hitam/85 backdrop-blur-md text-brand-kuning px-2.5 py-1 rounded-md text-xs font-black border border-brand-kuning/40 shadow-md">
              <svg class="w-3.5 h-3.5 fill-brand-kuning" viewBox="0 0 24 24">
                <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/>
              </svg>
              <span>${scoreVal}</span>
            </div>

            <!-- Tipe Format & Episode -->
            <div class="absolute top-3 right-3 flex items-center gap-1.5">
              <span class="bg-brand-jingga/90 backdrop-blur-md text-brand-putih px-2 py-0.5 rounded text-[11px] font-bold shadow">
                ${typeVal}
              </span>
              <span class="bg-brand-hitam/70 backdrop-blur-md text-gray-200 px-2 py-0.5 rounded text-[11px] font-medium border border-zinc-700">
                ${episodesVal}
              </span>
            </div>

            <!-- Hover Quick View Action -->
            <div class="absolute inset-0 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity duration-300 pointer-events-none">
              <span class="bg-brand-jingga text-brand-putih px-4 py-2 rounded-full font-bold text-xs uppercase tracking-wider shadow-lg flex items-center gap-1.5 transform translate-y-3 group-hover:translate-y-0 transition-transform">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                </svg>
                Lihat Detail
              </span>
            </div>
          </div>

          <!-- Informasi Konten -->
          <div class="p-5">
            <div class="flex flex-wrap gap-1.5 mb-2.5">
              ${genreBadges}
            </div>

            <h3 class="text-base font-bold text-gray-900 dark:text-brand-putih line-clamp-1 group-hover:text-brand-jingga transition" title="${titleVal}">
              ${titleVal}
            </h3>

            <p class="text-xs text-gray-500 dark:text-brand-abu line-clamp-2 mt-2 leading-relaxed">
              ${synopsisVal}
            </p>
          </div>
        </div>

        <!-- Kartu Footer -->
        <div class="px-5 pb-4 pt-3 border-t border-gray-100 dark:border-zinc-800/80 flex justify-between items-center text-xs">
          <span class="text-gray-400 dark:text-zinc-500 font-medium">FlipPlay Catalog</span>
          <span class="text-brand-jingga dark:text-brand-kuning font-semibold group-hover:underline flex items-center gap-1">
            Buka Info &rarr;
          </span>
        </div>
      </article>
    `;
  }).join('');

  gridContainer.innerHTML = cardsHtml;

  // Array Method: .forEach() untuk memasang event handling 'click' pada setiap kartu
  gridContainer.querySelectorAll('.vibe-card').forEach(card => {
    card.addEventListener('click', () => {
      const id = parseInt(card.dataset.id, 10);
      openAnimeDetailModal(id);
    });
  });
}

/**
 * 6. MODAL DETAIL POPUP
 * Menggunakan Array Method: .find() untuk mencari anime berdasarkan mal_id.
 */
function openAnimeDetailModal(malId) {
  // Array Method: .find()
  const anime = allMediaData.find(item => item.mal_id === malId);
  if (!anime) return;

  const modal = document.getElementById('anime-modal');
  const modalImg = document.getElementById('modal-img');
  const modalTitle = document.getElementById('modal-title');
  const modalScore = document.getElementById('modal-score');
  const modalType = document.getElementById('modal-type');
  const modalEpisodes = document.getElementById('modal-episodes');
  const modalStatus = document.getElementById('modal-status');
  const modalSynopsis = document.getElementById('modal-synopsis');
  const modalGenres = document.getElementById('modal-genres');

  const imageUrl = anime.images?.webp?.large_image_url || 
                   anime.images?.jpg?.large_image_url || 
                   anime.images?.webp?.image_url || 
                   anime.images?.jpg?.image_url;

  if (modalImg) modalImg.src = imageUrl;
  if (modalTitle) modalTitle.textContent = anime.title || 'Anime Details';
  if (modalScore) modalScore.textContent = typeof anime.score === 'number' ? anime.score.toFixed(1) : '8.0';
  if (modalType) modalType.textContent = anime.type || 'TV';
  if (modalEpisodes) modalEpisodes.textContent = anime.episodes ? `${anime.episodes} Episode` : 'Masih Berjalan (Ongoing)';
  if (modalStatus) modalStatus.textContent = anime.status || 'Selesai Tayang';
  if (modalSynopsis) {
    modalSynopsis.textContent = anime.synopsis 
      ? anime.synopsis.replace(/\[Written by MAL Rewrite\]/g, '').trim()
      : 'Sinopsis belum tersedia untuk judul ini.';
  }

  if (modalGenres) {
    modalGenres.innerHTML = (anime.genres || []).map(g => 
      `<span class="px-2.5 py-1 text-xs rounded-full font-semibold bg-brand-jingga/15 text-brand-jingga dark:text-brand-kuning border border-brand-jingga/30">${g.name}</span>`
    ).join('');
  }

  if (modal) {
    modal.classList.remove('hidden');
    document.body.style.overflow = 'hidden';
  }
}

function closeAnimeDetailModal() {
  const modal = document.getElementById('anime-modal');
  if (modal) {
    modal.classList.add('hidden');
    document.body.style.overflow = '';
  }
}

/**
 * 7. STATISTIK KURASI THE VIBE GRID
 * Menggunakan Array Method: .reduce() untuk menghitung rata-rata skor anime.
 */
function updateVibeStats() {
  const statsEl = document.getElementById('vibe-stat-average');
  if (!statsEl || allMediaData.length === 0) return;

  // Array Method: .reduce()
  const validScores = allMediaData.filter(a => typeof a.score === 'number' && a.score > 0);
  if (validScores.length > 0) {
    const totalScore = validScores.reduce((acc, curr) => acc + curr.score, 0);
    const avgScore = (totalScore / validScores.length).toFixed(1);
    statsEl.textContent = `Rata-rata Rating: ★ ${avgScore}`;
  }
}

/**
 * 8. EVENT HANDLING
 * Memenuhi syarat: Minimal 2 event handling (kita menerapkan 'input', 'click', 'submit', 'keydown').
 */
function initEventListeners() {
  // A. Event 'input': Unified Search secara real-time saat user mengetik
  const searchInput = document.getElementById('vibe-search-input');
  const clearSearchBtn = document.getElementById('vibe-search-clear');

  if (searchInput) {
    searchInput.addEventListener('input', (e) => {
      searchQuery = e.target.value;
      if (clearSearchBtn) {
        if (searchQuery.length > 0) {
          clearSearchBtn.classList.remove('hidden');
        } else {
          clearSearchBtn.classList.add('hidden');
        }
      }
      renderGrid();
    });
  }

  if (clearSearchBtn) {
    clearSearchBtn.addEventListener('click', () => {
      if (searchInput) {
        searchInput.value = '';
        searchQuery = '';
        clearSearchBtn.classList.add('hidden');
        searchInput.focus();
        renderGrid();
      }
    });
  }

  // B. Event 'click': Aura-Based Discovery Buttons
  const auraButtons = document.querySelectorAll('.aura-filter-btn');
  auraButtons.forEach(btn => {
    btn.addEventListener('click', () => {
      const aura = btn.dataset.aura;
      activeAura = aura;

      // Perbarui style tombol aktif
      auraButtons.forEach(b => {
        if (b === btn) {
          b.className = 'aura-filter-btn px-4 py-2 rounded-full text-xs md:text-sm font-bold transition flex items-center gap-1.5 shadow-md bg-brand-jingga text-brand-putih border border-brand-jingga ring-2 ring-brand-kuning/50';
        } else {
          b.className = 'aura-filter-btn px-4 py-2 rounded-full text-xs md:text-sm font-bold transition flex items-center gap-1.5 bg-white dark:bg-brand-gelap text-gray-700 dark:text-brand-abu border border-gray-300 dark:border-zinc-800 hover:border-brand-jingga hover:text-brand-jingga dark:hover:text-brand-kuning';
        }
      });

      renderGrid();
    });
  });

  // C. Event 'click': Tombol Coba Lagi di Error Banner
  const retryBtn = document.getElementById('vibe-retry-btn');
  if (retryBtn) {
    retryBtn.addEventListener('click', () => {
      fetchMediaData();
    });
  }

  // D. Event 'click': Tombol Reset Filter saat hasil kosong
  const resetFilterBtn = document.getElementById('vibe-reset-filter');
  if (resetFilterBtn) {
    resetFilterBtn.addEventListener('click', () => {
      if (searchInput) {
        searchInput.value = '';
        searchQuery = '';
        if (clearSearchBtn) clearSearchBtn.classList.add('hidden');
      }
      activeAura = 'all';
      auraButtons.forEach(b => {
        if (b.dataset.aura === 'all') {
          b.className = 'aura-filter-btn px-4 py-2 rounded-full text-xs md:text-sm font-bold transition flex items-center gap-1.5 shadow-md bg-brand-jingga text-brand-putih border border-brand-jingga ring-2 ring-brand-kuning/50';
        } else {
          b.className = 'aura-filter-btn px-4 py-2 rounded-full text-xs md:text-sm font-bold transition flex items-center gap-1.5 bg-white dark:bg-brand-gelap text-gray-700 dark:text-brand-abu border border-gray-300 dark:border-zinc-800 hover:border-brand-jingga hover:text-brand-jingga dark:hover:text-brand-kuning';
        }
      });
      renderGrid();
    });
  }

  // E. Event 'click' & 'keydown': Tutup Modal
  const modalCloseBtn = document.getElementById('modal-close-btn');
  const modalBackdrop = document.getElementById('modal-backdrop');
  if (modalCloseBtn) modalCloseBtn.addEventListener('click', closeAnimeDetailModal);
  if (modalBackdrop) modalBackdrop.addEventListener('click', closeAnimeDetailModal);

  window.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') {
      closeAnimeDetailModal();
    }
  });

  // F. Event 'submit': Form Kontak
  const contactForm = document.getElementById('contact-form');
  if (contactForm) {
    contactForm.addEventListener('submit', (e) => {
      e.preventDefault();
      const nama = document.getElementById('nama')?.value || 'Sahabat FlipPlay';
      showToast(`Terima kasih ${nama}, pesan Anda berhasil dikirim!`);
      contactForm.reset();
    });
  }
}

/**
 * 9. PENGALIH TEMA (Dark / Light Mode)
 * Menjaga warna tema FlipPlay dan menyesuaikan ikon tombol
 */
function initThemeToggle() {
  const themeToggleBtn = document.getElementById('theme-toggle');
  const htmlElement = document.documentElement;

  if (themeToggleBtn) {
    themeToggleBtn.addEventListener('click', () => {
      htmlElement.classList.toggle('dark');
      updateThemeIcon();
    });
  }
  updateThemeIcon();
}

function updateThemeIcon() {
  const icon = document.getElementById('theme-icon');
  if (!icon) return;
  const isDark = document.documentElement.classList.contains('dark');
  
  if (isDark) {
    // Ikon Matahari (untuk berpindah ke terang)
    icon.innerHTML = `
      <circle cx="12" cy="12" r="5"></circle>
      <line x1="12" y1="1" x2="12" y2="3"></line>
      <line x1="12" y1="21" x2="12" y2="23"></line>
      <line x1="4.22" y1="4.22" x2="5.64" y2="5.64"></line>
      <line x1="18.36" y1="18.36" x2="19.78" y2="19.78"></line>
      <line x1="1" y1="12" x2="3" y2="12"></line>
      <line x1="21" y1="12" x2="23" y2="12"></line>
      <line x1="4.22" y1="19.78" x2="5.64" y2="18.36"></line>
      <line x1="18.36" y1="5.64" x2="19.78" y2="4.22"></line>
    `;
  } else {
    // Ikon Bulan (untuk berpindah ke gelap)
    icon.innerHTML = `
      <path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"></path>
    `;
  }
}

/**
 * Notifikasi Toast Sederhana
 */
function showToast(message) {
  let toast = document.getElementById('app-toast');
  if (!toast) {
    toast = document.createElement('div');
    toast.id = 'app-toast';
    toast.className = 'fixed bottom-6 right-6 z-50 bg-brand-gelap text-brand-putih border border-brand-jingga px-5 py-3 rounded-lg shadow-2xl flex items-center gap-2 transform transition-all duration-300 translate-y-16 opacity-0';
    document.body.appendChild(toast);
  }
  
  toast.innerHTML = `
    <span class="text-brand-kuning font-bold">✓</span>
    <span class="text-sm font-medium">${message}</span>
  `;
  
  toast.classList.remove('translate-y-16', 'opacity-0');
  toast.classList.add('translate-y-0', 'opacity-100');

  setTimeout(() => {
    toast.classList.remove('translate-y-0', 'opacity-100');
    toast.classList.add('translate-y-16', 'opacity-0');
  }, 3200);
}

/**
 * 10. MENU NAVIGASI MOBILE (Hamburger Menu)
 * Event 'click': Toggle buka/tutup menu hamburger pada layar mobile
 */
function initMobileMenu() {
  const mobileMenuBtn = document.getElementById('mobile-menu-btn');
  const mobileMenu = document.getElementById('mobile-menu');
  const hamburgerIcon = document.getElementById('hamburger-icon');
  const mobileLinks = document.querySelectorAll('.mobile-nav-link');

  if (!mobileMenuBtn || !mobileMenu) return;

  function toggleMenu(isOpen) {
    const shouldOpen = isOpen !== undefined ? isOpen : mobileMenu.classList.contains('hidden');
    if (shouldOpen) {
      mobileMenu.classList.remove('hidden');
      mobileMenuBtn.setAttribute('aria-expanded', 'true');
      if (hamburgerIcon) {
        // Ikon 'X' (Tutup)
        hamburgerIcon.innerHTML = `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>`;
      }
    } else {
      mobileMenu.classList.add('hidden');
      mobileMenuBtn.setAttribute('aria-expanded', 'false');
      if (hamburgerIcon) {
        // Ikon 3 Garis (Hamburger)
        hamburgerIcon.innerHTML = `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>`;
      }
    }
  }

  // Toggle saat tombol hamburger diklik
  mobileMenuBtn.addEventListener('click', (e) => {
    e.stopPropagation();
    toggleMenu();
  });

  // Tutup menu saat salah satu link navigasi diklik
  mobileLinks.forEach(link => {
    link.addEventListener('click', () => {
      toggleMenu(false);
    });
  });

  // Tutup menu jika pengguna mengklik di luar area menu
  document.addEventListener('click', (e) => {
    if (!mobileMenu.contains(e.target) && !mobileMenuBtn.contains(e.target)) {
      toggleMenu(false);
    }
  });

  // Tutup menu saat menekan tombol Escape
  window.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') {
      toggleMenu(false);
    }
  });
}

