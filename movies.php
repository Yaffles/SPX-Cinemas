<?php
require(__DIR__.'\utilities\sessionCheck.php');
require_once("model\CinemaLocation.php");
require_once("model\Cinema.php");
require_once("model\Session.php");
require_once("model\Movie.php");


$locs = CinemaLocation::loadCinemaLocations();


?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php require('head.php');?>
    <link rel="stylesheet" href="css/movies.css">
    <script src="js/movies.js"></script>





</head>
<body>
    <?php
        require('header.php');
        require('nav.php');
    ?>

    <maincontent>
        <h1>Movies</h1>
        <div id="locationFilterContainer">
            <label for="locationFilter">Filter by Location:</label>
            <select id="locationFilter">
                <option value="all">All Locations</option>
                <?php foreach ($locs as $loc) { ?>
                    <option value="<?= htmlspecialchars($loc->getLocationName()) ?>">
                        <?= htmlspecialchars($loc->getLocationName()) ?>
                    </option>
                <?php } ?>
            </select>
        </div>


        <locations>
            <?php FOREACH($locs AS $loc) {
            ?>
                <div class="location" data-location="<?php echo($loc->getLocationName()); ?>">
                <h2><?php echo($loc->getLocationName());?></h2>
            <?php
                $cinemas = $loc->getCinemas();
                FOREACH($cinemas AS $cinema) {
            ?>
                    <h3><?php echo($cinema->getCinemaName()); ?></h3>
                    <movies>
                <?php
                    $movies = $cinema->getMovies();
                    if ($movies) {
                    foreach($movies as $movie) {
                        // call static method session.loadSessions() to get sessions for the movie
                        $sessions = Session::loadSessions(movie: $movie, cinema: $cinema);
                        
                        ?>
                        <movie>
                            <button class="movie-button" onclick="this.nextElementSibling.showModal()">
                                <h4><?php echo($movie->getMovieName()); ?></h4>
                                <div><img src='<?php echo($movie->getPosterFile()); ?>'></div>
                            </button>
                            <dialog id="popup">
                                <div id="popupdiv">
                                    <div id="popupinfo">
                                        <h4><?php echo($movie->getMovieName() . "<span class='location-name'>" . $loc->getLocationName() . " · " . $cinema->getCinemaName() . "</span>"); ?></h4>
                                        <div class="session-info"><?php echo($movie->getMovieDescription()); ?></div>
                                        <!-- 
                                        load on the fly as too many iframes can cause performance issues
                                        <div><iframe width="560" height="315" src="https://www.youtube.com/embed/<?php echo($movie->getTrailerName());?>" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" referrerpolicy="strict-origin-when-cross-origin" allowfullscreen></iframe></div> -->
                                        <div class="trailer-container" data-src="https://www.youtube.com/embed/<?php echo($movie->getTrailerName()); ?>"></div>
                                    </div>
                                    <div id="popupform">
                                        <h4>Sessions</h4>
                                        <?php foreach ($sessions as $index => $session) { ?>

                                            <h5 class="seatCost session-option <?php if ($index==0) echo(" selected")  ?>" sessionId="<?= htmlspecialchars($session->getSessionId())?>"  cost="<?= htmlspecialchars($session->getSeatCost()) ?>" data-index="<?= $index ?>">
                                                <?= htmlspecialchars($session->getTime()) ?>: $<?= htmlspecialchars($session->getSeatCost()) ?>
                                            </h5>

                                        <?php } ?>
                                        <h4>Book Now</h4>
                                        <form id="popupForm" action="basket.php" method="post">
                                        <input type="hidden" name="movieId" value="<?php echo($movie->getMovieId()); ?>">
                                        <input type="hidden" name="cinemaId" value="<?php echo($cinema->getCinemaId()); ?>">
                                        <input type="hidden" name="sessionId"  id="sessionId" value="<?php echo($sessions[0]->getSessionId())?>" required>
                                        <input type='hidden' name='_method' value='POST'>
                                        <label for="date">Date:</label>
                                        <input type="date" id="date" name="date" min="<?= date('Y-m-d') ?>" required>
                                        <label for="seats">Number of Seats:</label>
                                        <input id="seats" name="seats" type="number" min="1" max="10" value="1" required>

                                        <label>Total Cost:</label>
                                        <span id="totalCost">$<?php echo number_format($session->getSeatCost(), 2); ?></span>
                                        <input type="hidden" name="totalCost" value="<?php echo($session->getSeatCost()); ?>">

                                        <input type="submit" value="Book Now">
                                        </form>
                                        
                                    <div>

                                    
                                    
                                </div>
                                <button onclick="this.closest('dialog').close()">Close</button>
                            </dialog>
                        </movie>
                        

                        
                    
                    <?php
                    }
                    } else {
                        echo("<p>No movies available at this cinema.</p>");
                    }
                    ?>
                    </movies>
                <?php
                    
                }
                ?>
                </div>
            <?php
                }
            ?>
        </locations>
        </maincontent>
    <?php
        require('footer.php');
    ?>
</body>
</html>