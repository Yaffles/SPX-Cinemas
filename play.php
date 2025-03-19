<?php
require(__DIR__.'\utilities\sessionCheck.php');

// TMDB API Configuration
define('TMDB_API_KEY', 'c4a762a124a5cf4ad6d0d8e8fc3dd984'); // Replace with your TMDB API key

// Handle AJAX search requests
if (isset($_GET['search'])) {
    $query = urlencode($_GET['search']);
    $url = "https://api.themoviedb.org/3/search/movie?api_key=" . TMDB_API_KEY . "&query=$query";
    
    // Use cURL to handle the API request
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Disable SSL verification (not recommended for production)
    $response = curl_exec($ch);
    curl_close($ch);
    
    $data = json_decode($response, true);
    
    header('Content-Type: application/json');
    echo json_encode($data['results'] ?? []);
    exit;
}

// Handle movie playback
$imdb_id = isset($_GET['imdb_id']) ? htmlspecialchars($_GET['imdb_id']) : '';
$poster = isset($_GET['poster']) ? htmlspecialchars($_GET['poster']) : '';
$title = isset($_GET['title']) ? htmlspecialchars($_GET['title']) : '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php require('head.php'); ?>
    <style>
        .search-container {
            position: relative;
            max-width: 600px;
            margin: 20px auto;
        }
        
        #movieSearch {
            width: 100%;
            padding: 12px;
            font-size: 16px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        
        .autocomplete-results {
            position: absolute;
            width: 100%;
            max-height: 400px;
            overflow-y: auto;
            background: white;
            border: 1px solid #ddd;
            border-top: none;
            display: none;
            z-index: 1000;
        }
        
        .autocomplete-item {
            padding: 10px;
            cursor: pointer;
            display: flex;
            align-items: center;
            border-bottom: 1px solid #eee;
        }
        
        .autocomplete-item:hover {
            background-color: #f5f5f5;
        }
        
        .autocomplete-poster {
            width: 50px;
            height: 75px;
            object-fit: cover;
            margin-right: 15px;
        }
        
        .player-container {
            position: relative;
            padding-bottom: 56.25%;
            height: 0;
            overflow: hidden;
            background: #000;
            border-radius: 8px;
            margin: 20px 0;
        }
        
        #moviePlayer {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            border: none;
        }
        
        .selected-movie {
            text-align: center;
            margin: 20px 0;
        }
        
        .selected-poster {
            max-height: 300px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
        }
    </style>
</head>
<body>
    <?php
        require('header.php');
        require('nav.php');
    ?>
    
    <maincontent>
        <h1>SPX Cinemas Movie Player</h1>
        
        <div class="search-container">
            <form method="GET" action="">
                <input type="text" 
                       id="movieSearch"
                       autocomplete="off"
                       placeholder="Search for a movie...">
                <input type="hidden" name="imdb_id" id="hiddenImdbId">
                <input type="hidden" name="poster" id="hiddenPoster">
                <input type="hidden" name="title" id="hiddenTitle">
                <button type="submit" class="load-btn" style="margin-top: 10px;">Play Movie</button>
            </form>
            
            <div class="autocomplete-results" id="autocompleteResults"></div>
        </div>

        <?php if(!empty($imdb_id)): ?>
            <?php if(!empty($title)): ?>
                <div class="selected-movie">
                    <h2><?php echo $title; ?></h2>
                    <?php if(!empty($poster) && $poster !== 'N/A'): ?>
                        <img src="https://image.tmdb.org/t/p/w500<?php echo $poster; ?>" alt="Movie Poster" class="selected-poster">
                    <?php endif; ?>
                </div>
            <?php endif; ?>
            
            <div class="player-container">
                <iframe 
                    id="moviePlayer"
                    src="https://godriveplayer.com/player.php?imdb<?php echo $imdb_id; ?>"
                    allowfullscreen
                    allow="autoplay; encrypted-media"
                    scrolling="no">
                </iframe>
            </div>
        <?php endif; ?>
    </maincontent>

    <script>
        const searchInput = document.getElementById('movieSearch');
        const resultsContainer = document.getElementById('autocompleteResults');
        let debounceTimer;

        searchInput.addEventListener('input', function(e) {
            clearTimeout(debounceTimer);
            debounceTimer = setTimeout(() => {
                if (this.value.length > 2) {
                    fetchResults(this.value);
                }
            }, 300);
        });

        async function fetchResults(query) {
            try {
                const response = await fetch(`?search=${encodeURIComponent(query)}`);
                const results = await response.json();
                
                resultsContainer.innerHTML = '';
                resultsContainer.style.display = 'block';
                
                results.forEach(movie => {
                    const div = document.createElement('div');
                    div.className = 'autocomplete-item';
                    div.innerHTML = `
                        ${movie.poster_path ? 
                            `<img src="https://image.tmdb.org/t/p/w92${movie.poster_path}" class="autocomplete-poster" alt="${movie.title}">` : 
                            '<div class="autocomplete-poster"></div>'}
                        <div>
                            <strong>${movie.title}</strong><br>
                            <small>${movie.release_date ? movie.release_date.substring(0, 4) : 'N/A'}</small>
                        </div>
                    `;
                    
                    div.addEventListener('click', () => {
                        searchInput.value = movie.title;
                        document.getElementById('hiddenImdbId').value = movie.id; // TMDB ID
                        document.getElementById('hiddenPoster').value = movie.poster_path;
                        document.getElementById('hiddenTitle').value = movie.title;
                        resultsContainer.style.display = 'none';
                    });
                    
                    resultsContainer.appendChild(div);
                });
            } catch (error) {
                console.error('Error fetching results:', error);
            }
        }

        // Close autocomplete when clicking outside
        document.addEventListener('click', (e) => {
            if (!searchInput.contains(e.target)) {
                resultsContainer.style.display = 'none';
            }
        });
    </script>

    <?php
        require('footer.php');
    ?>
</body>
</html>