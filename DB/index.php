<?php
session_start();
include 'config.php';
if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit;
}

$sql = "SELECT c.ContentID, c.Title, c.OriginalTitle, c.ReleaseYear, 
               c.RuntimeMinutes, c.Genres, r.AverageRating, r.NumVotes
        FROM Content c 
        LEFT JOIN Rating r ON c.ContentID = r.ContentID JOIN ContentPerson cp ON c.ContentID = cp.ContentID 
        WHERE c.ContentType = 'movie' and c.ReleaseYear = '2025'and r.AverageRating > 7
        GROUP BY c.ContentID
        LIMIT 24"; 

$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $rows[] = $row; 
    }
}

?>
<head>


    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="profile.css">
    <link
        href="https://fonts.googleapis.com/css2?family=Roboto:wght@100;300;400;500;700;900&family=Sen:wght@400;700;800&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.2/css/all.min.css">
    <title>Movie Design</title>

    <style>
        /* Additional styles for modals */
        .plan-badge {
            margin-top: 12px;
            padding: 6px 12px;
            background: linear-gradient(135deg, #ff4d4f, #ff7875);
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            text-align: center;
        }

        #movie-overlay {
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,0.85);
            display: none;
            align-items: center;
            justify-content: center;
            z-index: 10000;
            backdrop-filter: blur(5px);
        }

        #movie-overlay.show {
            display: flex;
        }

        .movie-detail-modal {
            width: 90%;
            max-width: 900px;
            background: linear-gradient(135deg, #1a1a1a, #0f0f0f);
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 20px 60px rgba(0,0,0,0.9);
            animation: slideUp 0.3s ease;
            position: relative;
        }

        @keyframes slideUp {
            from { opacity: 0; transform: translateY(50px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .movie-close {
            position: absolute;
            top: 20px;
            right: 20px;
            background: rgba(0,0,0,0.7);
            border: 0;
            color: #fff;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            cursor: pointer;
            font-size: 20px;
            z-index: 10;
            transition: all 0.3s;
        }

        .movie-close:hover {
            background: #ff4d4f;
            transform: rotate(90deg);
        }

        .movie-detail-content {
            display: grid;
            grid-template-columns: 300px 1fr;
            gap: 30px;
            padding: 30px;
        }

        .movie-poster-section img {
            width: 100%;
            height: 450px;
            object-fit: cover;
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.5);
        }

        .movie-info-section {
            display: flex;
            flex-direction: column;
            justify-content: center;
            color: #fff;
        }

        .movie-info-section h2 {
            font-size: 32px;
            margin: 0 0 15px 0;
            font-weight: 700;
        }

        .movie-meta {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 20px;
            color: rgba(255,255,255,0.7);
            font-size: 14px;
        }

        .separator {
            color: #ff4d4f;
        }

        .movie-info-section p {
            line-height: 1.8;
            color: rgba(255,255,255,0.8);
            margin-bottom: 30px;
        }

        .movie-actions {
            display: flex;
            gap: 15px;
        }

        .btn-play, .btn-add-list {
            padding: 12px 30px;
            border: 0;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
            font-size: 15px;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .btn-play {
            background: #ff4d4f;
            color: #fff;
        }

        .btn-play:hover {
            background: #ff6666;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(255,77,79,0.4);
        }

        .btn-add-list {
            background: rgba(255,255,255,0.1);
            color: #fff;
            border: 1px solid rgba(255,255,255,0.2);
        }

        .btn-add-list:hover {
            background: rgba(255,255,255,0.2);
        }

        @media (max-width: 768px) {
            .movie-detail-content {
                grid-template-columns: 1fr;
            }
            .movie-poster-section img {
                height: 300px;
            }
        }

        /* Cast Section - แก้ไขให้อยู่บรรทัดเดียวกัน */
.cast-section {
    margin: 20px 0;
}

.cast-list {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 8px;
}

.cast-item {
    padding: 8px 12px;
    background: rgba(255,255,255,0.05);
    border-radius: 6px;
    border-left: 3px solid #ff4d4f;
    transition: all 0.3s;
    display: flex;
    align-items: baseline;
    gap: 6px;
}

.cast-item:hover {
    background: rgba(255,255,255,0.1);
    transform: translateX(3px);
}

.cast-name {
    font-weight: 600;
    font-size: 14px;
    color: #fff;
    white-space: nowrap;
}

.cast-character {
    font-size: 12px;
    color: rgba(255,255,255,0.6);
    font-style: italic;
}

/* แสดงแบบ: "John Doe as Detective" */
.cast-character::before {
    content: "as ";
    font-style: normal;
    color: rgba(255,255,255,0.4);
}
    </style>
</head>

<body>
    <div class="navbar">
        <div class="navbar-container">
            <div class="logo-container">
                <h1 class="logo">Luminix</h1>
            </div>
            <div class="menu-container">
                <ul class="menu-list">
                    <li class="menu-list-item active">Home</li>
                    <li class="menu-list-item">Movies</li>
                    <li class="menu-list-item">Series</li>
                    <li class="menu-list-item">Popular</li>
                    <li class="menu-list-item">Trends</li>
                   
        
                </ul>
            </div>
            <div class="profile-container">
                <img class="profile-picture" src="img/profile.jpg" alt="">
                <div class="profile-text-container">
                    <span class="profile-text">Profile</span>
                    <i class="fas fa-caret-down"></i>
                </div>
                <div class="toggle" id="dark-mode-toggle">
                    <i class="fas fa-moon toggle-icon"></i>
                    <i class="fas fa-sun toggle-icon"></i>
                    <div class="toggle-ball"></div>
                </div>
            </div>
        </div>
    </div>
    <div class="sidebar">
        <i class="left-menu-icon-first fas fa-search"></i>
        <i class="left-menu-icon fas fa-home"></i>
        <i class="left-menu-icon fas fa-users"></i>
        <i class="left-menu-icon fas fa-bookmark"></i>
        <i class="left-menu-icon fas fa-tv"></i>
        <i class="left-menu-icon fas fa-hourglass-start"></i>
        <i class="left-menu-icon fas fa-shopping-cart"></i>
    </div>
    <div class="container">
        <div class="content-container" >
            <div class="featured-content"
                style="background: linear-gradient(to bottom, rgba(0,0,0,0), #151515), url('img/f-1.jpg'); background-size: cover;
            background-position: center;
            background-repeat: no-repeat;">
                <img class="featured-title" src="img/f-t-1.png" alt="">
                <p class="featured-desc">Fueled by love and vengeance, a determined husband joins forces with a German bounty hunter to rescue his wife from a brutal Mississippi plantation.</p>
                <button class="featured-button">WATCH</button>
            </div>
            <div class="movie-list-container">
                <h1 class="movie-list-title">NEW RELEASES</h1>
                <div class="movie-list-wrapper">
                    <div class="movie-list">
                        <?php
                        $start = 0;   
                        $end = 5; 
                            for ($i = $start; $i <= $end && $i < count($rows); $i++) {
                                $movie = $rows[$i];

                                $genres = explode(',', $movie['Genres'] ?: '');
                                $genreText = isset($genres[0]) ? strtolower(trim($genres[0])) : 'film';
        
                                $description = "A compelling " . $genreText;
                                if (isset($genres[1])) {
                                   $description .= " with elements of " . strtolower(trim($genres[1]));
                                }
                                $description .= ". Released in " .$movie['ReleaseYear'];
        
                                if ($movie['AverageRating']) {
                                    $description .= ". Rated " . number_format($movie['AverageRating'], 1) . "/10";
                                         }

                                echo '<div class="movie-list-item">
                                        <img class="movie-list-item-img" src="img/'.$i.'.jpg" alt="">
                                        <span class="movie-list-item-title"> '.$movie["Title"].'</span>
                                        <p class="movie-list-item-desc">'.$description.'</p>
                                        <button class="movie-list-item-button" data-id="'.$movie["ContentID"].'" data-index="'.$i.'" >Watch</button>
                                      </div>';
                            }
                        ?>
                    </div>
                    <i class="fas fa-chevron-right arrow"></i>
                </div>
            </div>
            <div class="movie-list-container">
                <h1 class="movie-list-title">HOT IN EUROPE</h1>
                <div class="movie-list-wrapper">
                    <div class="movie-list">
                    <?php
                        $start = 6;   
                        $end = 11; 
                            for ($i = $start; $i <= $end && $i < count($rows); $i++) {
                                $movie = $rows[$i];

                                $genres = explode(',', $movie['Genres'] ?: '');
                                $genreText = isset($genres[0]) ? strtolower(trim($genres[0])) : 'film';
        
                                $description = "A compelling " . $genreText;
                                if (isset($genres[1])) {
                                   $description .= " with elements of " . strtolower(trim($genres[1]));
                                }
                                $description .= ". Released in " .$movie['ReleaseYear'];
        
                                if ($movie['AverageRating']) {
                                    $description .= ". Rated " . number_format($movie['AverageRating'], 1) . "/10";
                                         }

                                echo '<div class="movie-list-item">
                                        <img class="movie-list-item-img" src="img/'.$i.'.jpg" alt="">
                                        <span class="movie-list-item-title"> '.$movie["Title"].'</span>
                                        <p class="movie-list-item-desc">'.$description.'</p>
                                        <button class="movie-list-item-button" data-id="'.$movie["ContentID"].'" data-index="'.$i.'">Watch</button>
                                      </div>';
                            }
                        ?>
                    </div>
                    <i class="fas fa-chevron-right arrow"></i>
                </div>
            </div>
            
            <div class="movie-list-container">
                <h1 class="movie-list-title">POPULAR THIS WEEK</h1>
                <div class="movie-list-wrapper">
                    <div class="movie-list">
                    <?php
                        $start = 12;   
                        $end = 17; 
                            for ($i = $start; $i <= $end && $i < count($rows); $i++) {
                                $movie = $rows[$i];

                                $genres = explode(',', $movie['Genres'] ?: '');
                                $genreText = isset($genres[0]) ? strtolower(trim($genres[0])) : 'film';
        
                                $description = "A compelling " . $genreText;
                                if (isset($genres[1])) {
                                   $description .= " with elements of " . strtolower(trim($genres[1]));
                                }
                                $description .= ". Released in " .$movie['ReleaseYear'];
        
                                if ($movie['AverageRating']) {
                                    $description .= ". Rated " . number_format($movie['AverageRating'], 1) . "/10";
                                         }

                                echo '<div class="movie-list-item">
                                        <img class="movie-list-item-img" src="img/'.$i.'.jpg" alt="">
                                        <span class="movie-list-item-title"> '.$movie["Title"].'</span>
                                        <p class="movie-list-item-desc">'.$description.'</p>
                                        <button class="movie-list-item-button" data-id="'.$movie["ContentID"].'" data-index="'.$i.'">Watch</button>
                                      </div>';
                            }
                        ?>
                    </div>
                    <i class="fas fa-chevron-right arrow"></i>
                </div>
            </div>
            <div class="movie-list-container">
                <h1 class="movie-list-title">FROM ASIA</h1>
                <div class="movie-list-wrapper">
                    <div class="movie-list">
                    <?php
                        $start = 18;   
                        $end = 23; 
                            for ($i = $start; $i <= $end && $i < count($rows); $i++) {
                                $movie = $rows[$i];
                                
                                $genres = explode(',', $movie['Genres'] ?: '');
                                $genreText = isset($genres[0]) ? strtolower(trim($genres[0])) : 'film';
        
                                $description = "A compelling " . $genreText;
                                if (isset($genres[1])) {
                                   $description .= " with elements of " . strtolower(trim($genres[1]));
                                }
                                $description .= ". Released in " .$movie['ReleaseYear'];
        
                                if ($movie['AverageRating']) {
                                    $description .= ". Rated " . number_format($movie['AverageRating'], 1) . "/10";
                                         }

                                echo '<div class="movie-list-item">
                                        <img class="movie-list-item-img" src="img/'.$i.'.jpg" alt="">
                                        <span class="movie-list-item-title"> '.$movie["Title"].'</span>
                                        <p class="movie-list-item-desc">'.$description.'</p>
                                        <button class="movie-list-item-button" data-id="'.$movie["ContentID"].'" data-index="'.$i.'">Watch</button>
                                      </div>';
                            }
                        ?>
                    </div>
                    <i class="fas fa-chevron-right arrow"></i>
                </div>
            </div>
        </div>
    </div>
    <script src="app.js"></script>

    <!-- Profile Modal -->
    <div id="profile-overlay">
        <div class="profile-modal" role="dialog" aria-modal="true">
            <div class="profile-header">
                <h2 id="profile-title">User Information</h2>
                <button class="profile-close" id="profile-close">✕</button>
            </div>

            <div class="profile-body">
                <div class="profile-card">
                    <img id="profile-avatar" class="profile-avatar" src="img/profile.jpg" alt="Avatar">
                    <h3 id="profile-fullname">Loading...</h3>
                    <p class="small" id="profile-username">#USER0001</p>
                    <p class="small" id="profile-email">email@example.com</p>
                    <div class="plan-badge" id="plan-badge">Free Plan</div>
                </div>

                <div class="profile-details">
                    <div class="detail-row">
                        <div class="detail-label">User ID</div>
                        <div class="detail-value" id="field-userid">-</div>
                    </div>
                    <div class="detail-row">
                        <div class="detail-label">First Name</div>
                        <div class="detail-value" id="field-firstname">-</div>
                    </div>
                    <div class="detail-row">
                        <div class="detail-label">Last Name</div>
                        <div class="detail-value" id="field-lastname">-</div>
                    </div>
                    <div class="detail-row">
                        <div class="detail-label">Email</div>
                        <div class="detail-value" id="field-email">-</div>
                    </div>
                    <div class="detail-row">
                        <div class="detail-label">Subscription Plan</div>
                        <div class="detail-value" id="field-plan">-</div>
                    </div>
                    <div class="detail-row">
                        <div class="detail-label">Plan Status</div>
                        <div class="detail-value" id="field-plan-status">-</div>
                    </div>
                    <div class="detail-row">
                        <div class="detail-label">Watch History</div>
                        <div class="detail-value">
                            <div class="list-box" id="watch-history">
                                <div class="list-item">Loading...</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="profile-actions">
                <button class="btn btn-ghost" id="upgrade-plan">Upgrade Plan</button>
                <button class="btn btn-delete" id="delete-btn">Delete</button>
                <button class="btn btn-primary" id="logout-btn">Sign Out</button>
            </div>
        </div>
    </div>

    <!-- Movie Detail Modal -->
    <div id="movie-overlay">
        <div class="movie-detail-modal" role="dialog" aria-modal="true">
            <button class="movie-close" id="movie-close">✕</button>
            <div class="movie-detail-content">
                <div class="movie-poster-section">
                    <img id="movie-poster" src="img/1.jpeg" alt="Movie Poster">
                </div>
                <div class="movie-info-section">
                    <h2 id="movie-title">Movie Title</h2>
                    <div class="movie-meta">
                        <span id="movie-year">2024</span>
                        <span class="separator">•</span>
                        <span id="movie-genre">Action</span>
                        <span class="separator">•</span>
                        <span id="movie-runtime">120 min</span>
                    </div>
                    <p id="movie-description">Movie description goes here...</p>

                    <div class="cast-section" id="cast-section">
                        <h3 style="font-size: 18px; margin-bottom: 12px;">Cast</h3>
                        <div class="cast-list" id="cast-list">
                            <!-- Cast will be populated here -->
                        </div>
                    </div>
                    <div class="movie-actions">
                        <button class="btn-play" id="btn-play">
                            <i class="fas fa-play"></i> Play Now
                        </button>
                        <button class="btn-add-list" id="btn-add-list">
                            <i class="fas fa-plus"></i> Add to List
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="app.js"></script>
    <script>
    // ====== LUMINIX - FEATURED + STATIC CONTENT ======
console.log('🎬 Luminix: Initializing...');

let featuredMovieId = null;

// ====== 1. LOAD FEATURED MOVIE (เฉพาะตัวแรก) ======
async function loadFeaturedMovie() {
    try {
        const response = await fetch('get_featured_movie.php');
        const data = await response.json();

        if (!data.success) {
            console.warn('⚠️ Unable to load featured movie, using default');
            return;
        }

        const movie = data.movie;
        featuredMovieId = movie.id;

        // Update featured content (ตัวแรกเท่านั้น)
        const featuredContainer = document.querySelector('.featured-content');
        if (featuredContainer) {
            const descEl = featuredContainer.querySelector('.featured-desc');
            if (descEl) {
                descEl.textContent = movie.description;
            }
        }

        console.log('✅ Featured movie loaded:', movie.title, 'ID:', movie.id);

    } catch (error) {
        console.error('❌ Error loading featured movie:', error);
    }
}

// ====== 2. SHOW MOVIE DETAIL WITH CAST ======
async function showMovieDetail(movieId,num) {
    try {
        const response = await fetch(`get_movie_detail.php?id=${movieId}`);
        const data = await response.json();

        if (!data.success) {
            alert('Unable to load movie details');
            return;
        }

        const movie = data.movie;

        // Update modal
        document.getElementById('movie-poster').src = "img/"+num +".jpg";
        document.getElementById('movie-title').textContent = movie.title;
        document.getElementById('movie-year').textContent = movie.year || 'N/A';
        document.getElementById('movie-genre').textContent = movie.genres || 'N/A';
        document.getElementById('movie-runtime').textContent = (movie.runtime || 0) + ' min';
        
        // Description with rating and credits
        let description = '';
        if (movie.originalTitle && movie.originalTitle !== movie.title) {
            description += `Original Title: ${movie.originalTitle}\n\n`;
        }
        description += `⭐ Rating: ${movie.rating || 'N/A'}/10`;
        if (movie.votes) {
            description += ` (${Number(movie.votes).toLocaleString()} votes)`;
        }
        description += `\n🎬 Director: ${movie.directors}`;
        description += `\n✍️ Writers: ${movie.writers}`;
        
        document.getElementById('movie-description').textContent = description;

        // Render cast (นักแสดง)
        const castList = document.getElementById('cast-list');
        const castSection = document.getElementById('cast-section');
        castList.innerHTML = '';

        if (movie.actors && movie.actors.length > 0) {
            castSection.style.display = 'block';
            
            // แสดงนักแสดง 10 คนแรก
            movie.actors.slice(0, 10).forEach(actor => {
                const castItem = document.createElement('div');
                castItem.className = 'cast-item';
                 castItem.innerHTML = `
                    <div class="cast-name">${actor.name}</div>
                    <div class="cast-character">${actor.character}</div>
                `;
                castList.appendChild(castItem);
            });

            // ถ้ามีนักแสดงมากกว่า 10 คน แสดงจำนวนที่เหลือ
            if (movie.actors.length > 10) {
                const moreItem = document.createElement('div');
                moreItem.className = 'cast-item';
                moreItem.style.textAlign = 'center';
                moreItem.style.color = 'rgba(255,255,255,0.5)';
                moreItem.innerHTML = `
                    <div class="cast-name">+ ${movie.actors.length - 10} more cast members</div>
                `;
                castList.appendChild(moreItem);
            }
        } else {
            castSection.style.display = 'none';
        }
        
        // Update play button
        const btnPlay = document.getElementById('btn-play');
        btnPlay.onclick = async () => {
            // บันทึก watch history
            try {
                const formData = new FormData();
                formData.append('content_id', movieId);
                await fetch('add_watch_history.php', {
                    method: 'POST',
                    body: formData
                });
            } catch (err) {
                console.warn('Unable to save watch history:', err);
            }

            alert(`🎬 Starting playback: ${movie.title}`);
            document.getElementById('movie-overlay').classList.remove('show');
        };

        // Update add to list button
        const btnAddList = document.getElementById('btn-add-list');
        btnAddList.onclick = () => {
            alert(`✅ Added to watchlist: ${movie.title}`);
        };

        // Show modal
        document.getElementById('movie-overlay').classList.add('show');

    } catch (error) {
        console.error('❌ Error loading movie details:', error);
        alert('Unable to load movie details. Please try again.');
    }
}

// ====== 3. INITIALIZE ======
document.addEventListener('DOMContentLoaded', function() {
    // โหลด featured movie
    loadFeaturedMovie();
    
    // Attach click handler to FIRST featured button only
    const firstFeaturedButton = document.querySelector('.featured-content .featured-button');
    if (firstFeaturedButton) {
        firstFeaturedButton.addEventListener('click', function(e) {
            e.preventDefault();
            if (featuredMovieId) {
                console.log('Opening movie detail for:', featuredMovieId);
                showMovieDetail(featuredMovieId, 100);
            } else {
                alert('Movie information not available. Please refresh the page.');
            }
        });
    }

    console.log('✅ Featured content initialized!');
});

// ====== 4. PROFILE MODAL (เหมือนเดิม) ======
const profileContainer = document.querySelector('.profile-text-container');
const profileOverlay = document.getElementById('profile-overlay');
const profileCloseBtn = document.getElementById('profile-close');
const logoutBtn = document.getElementById('logout-btn');
const deleteBtn = document.getElementById('delete-btn');
const movieOverlay = document.getElementById('movie-overlay');
const movieCloseBtn = document.getElementById('movie-close');

if (profileContainer) {
    profileContainer.addEventListener('click', async function() {
        console.log('📊 Loading profile...');
        try {
            const response = await fetch('get_profile.php');
            const text = await response.text();
            const data = JSON.parse(text);

            if (data.error) {
                alert('Please log in again');
                window.location.href = 'login.html';
                return;
            }

            // Update all fields
            const updates = {
                'profile-avatar': { type: 'src', value: data.avatar || 'img/profile.jpg' },
                'profile-fullname': { type: 'text', value: (data.firstname || '') + ' ' + (data.lastname || '') },
                'profile-username': { type: 'text', value: '#' + (data.id || 'N/A') },
                'profile-email': { type: 'text', value: data.email || 'N/A' },
                'plan-badge': { type: 'text', value: data.plan || 'No Plan' },
                'field-userid': { type: 'text', value: data.id || 'N/A' },
                'field-firstname': { type: 'text', value: data.firstname || 'N/A' },
                'field-lastname': { type: 'text', value: data.lastname || 'N/A' },
                'field-email': { type: 'text', value: data.email || 'N/A' },
                'field-plan': { type: 'text', value: (data.plan || 'No Plan') + ' ($' + (data.planPrice || 0) + '/month)' },
                'field-plan-status': { type: 'text', value: data.planStatus || 'N/A' }
            };

            for (const [id, config] of Object.entries(updates)) {
                const el = document.getElementById(id);
                if (el) {
                    if (config.type === 'src') el.src = config.value;
                    else el.textContent = config.value;
                }
            }

            // Watch history
            const whBox = document.getElementById('watch-history');
            if (whBox) {
                whBox.innerHTML = '';
                if (data.watchHistory && data.watchHistory.length > 0) {
                    data.watchHistory.forEach((item, idx) => {
                        const div = document.createElement('div');
                        div.className = 'list-item';
                        div.textContent = `${idx + 1}. ${item.title} - ${item.date}`;
                        whBox.appendChild(div);
                    });
                } else {
                    whBox.innerHTML = '<div class="list-item">No watch history yet</div>';
                }
            }

            // Update navbar
            const navPic = document.querySelector('.profile-picture');
            const navText = document.querySelector('.profile-text');
            if (navPic) navPic.src = data.avatar || 'img/profile.jpg';
            if (navText) navText.textContent = data.firstname || 'Profile';

            profileOverlay.classList.add('show');

        } catch (error) {
            console.error('❌ Error:', error);
            alert('Unable to load profile');
        }
    });
}

// ====== 5. CLOSE HANDLERS ======
if (profileCloseBtn) profileCloseBtn.addEventListener('click', () => profileOverlay.classList.remove('show'));
if (movieCloseBtn) movieCloseBtn.addEventListener('click', () => movieOverlay.classList.remove('show'));

// Close on overlay click
[profileOverlay, movieOverlay].forEach(overlay => {
    if (overlay) {
        overlay.addEventListener('click', (e) => {
            if (e.target === overlay) overlay.classList.remove('show');
        });
    }
});

// ESC key
document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') {
        if (profileOverlay) profileOverlay.classList.remove('show');
        if (movieOverlay) movieOverlay.classList.remove('show');
    }
});

if (deleteBtn) {
    deleteBtn.onclick = async () => {
        if (confirm("Are you sure you want to delete your Watch History?")) {
            try {
                const res = await fetch("delete_history.php", {
                    method: "POST"
                });
                const text = await res.text();
                alert(text);
                location.reload();
            } catch (err) {
                console.warn("Error deleting history:", err);
                alert("Failed to delete history. Please try again later.");
            }
        }
    };
}


// ====== 6. LOGOUT ======
if (logoutBtn) {
    logoutBtn.addEventListener('click', async function() {
        try {
            await fetch('logout.php', { method: 'POST' });
            window.location.href = 'login.html';
        } catch (err) {
            window.location.href = 'login.html';
        }
    });
}


// Watch buttons 
document.querySelectorAll('.movie-list-item-button').forEach(button => {
    button.addEventListener('click', function(e) {
        e.preventDefault();
        e.stopPropagation();

        const movieId = this.dataset.id;
        const index = this.dataset.index;
        console.log("🎬 Clicked movie ID:", movieId, "➡️ Index:", index);

        showMovieDetail(movieId,index);
    });
});
console.log('✅ Luminix: Ready!')
    </script>
</body>
</html>