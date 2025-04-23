<?php
session_start();

include("../Components/db.php");

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="../css/homestyle.css">
    <link rel="stylesheet" href="../css/index.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" />
    <link
        href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&family=Lato:wght@300;400;700&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <title>Surf Bay</title>
    <style>
    .navbar {
        position: fixed;
        width: 100%;
        top: 0;
        transition: top 0.3s ease-in-out, background-color 0.3s ease-in-out;
        z-index: 1000;
    }

    .navbar.hidden {
        top: -150px;
    }

    .navbar.scrolled {
        background: linear-gradient(to bottom, rgba(0, 0, 0, 0.8), rgba(0, 0, 0, 0));
    }
    </style>
</head>

<body>
    <?php include("../Components/navigationbar.php"); ?>
    <?php include("../Components/usertag.php"); ?>


    <!-- Hero Image -->
    <section class="hero-section">
        <img src="../Images/WallBg copy.png" alt="Home Image" class="home-image">
        <div class="hero-text">
            <button class="book-now-btn">Book Now <i class="fas fa-arrow-right" style="margin-left: 10px;"></i></button>
        </div>
    </section>

    <section class="about-section">
        <div class="about-content">
            <div class="about-text">
                <h2 class="about-title">Inspired by Destinations</h2>
                <p class="about-description">
                    The legendary ocean front "Surf Bay Hotel & Restaurant", Located 100 miles south from Capital of Sri
                    Lanka (Colombo), has been welcoming guests for over 20 years! We provide rooms for your budget
                    ranging from
                    normal to luxury with a wide range of delicious food, drinks and cocktails at your choice in our
                    beachfront
                    restaurant.
                </p>
                <a href="#" class="read-more-btn">READ MORE</a>
            </div>
            <div class="about-image">
                <img src="../Images/hotel-exterior.jpg" alt="Surf Bay Hotel Exterior" class="fade-in-image">
            </div>
        </div>
    </section>

    <section class="gallery-section">
        <h3 class="subtitle">Experience the Surf Bay</h3>
        <div class="gallery-container">
            <div class="hotel-gallery">
                <div class="gallery-item"><img src="../Images/hotel-1.jpg" alt="Hotel Exterior"></div>
                <div class="gallery-item"><img src="../Images/hotel-2.jpg" alt="Luxurious Room"></div>
                <div class="gallery-item"><img src="../Images/hotel-3.jpg" alt="Swimming Pool"></div>
                <div class="gallery-item"><img src="../Images/hotel-4.jpg" alt="Beach View"></div>
                <div class="gallery-item"><img src="../Images/hotel-5.jpg" alt="Restaurant"></div>
                <div class="gallery-item"><img src="../Images/hotel-6.jpg" alt="Spa"></div>
            </div>
        </div>
    </section>
    <?php include("../Components/footer.php"); ?>

    <!-- Flatpickr Script -->
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        flatpickr("#check-in", {
            dateFormat: "Y-m-d",
            minDate: "today",
            maxDate: new Date().fp_incr(365), // 1 year from now
        });

        flatpickr("#check-out", {
            dateFormat: "Y-m-d",
            minDate: "today",
            maxDate: new Date().fp_incr(365), // 1 year from now
        });
    });
    </script>
    <script>
    let lastScrollTop = 0;
    const navbar = document.querySelector('.navbar');
    const heroSection = document.querySelector('.hero-section');

    window.addEventListener('scroll', function() {
        let scrollTop = window.pageYOffset || document.documentElement.scrollTop;
        let heroHeight = heroSection ? heroSection.offsetHeight : 0;

        // Add 'scrolled' class for background change
        if (scrollTop > 0) {
            navbar.classList.add('scrolled');
        } else {
            navbar.classList.remove('scrolled');
        }

        // Hide/show navbar based on scroll direction and position
        if (scrollTop > heroHeight / 2) {
            if (scrollTop > lastScrollTop) {
                // Scrolling down
                navbar.classList.add('hidden');
            } else {
                // Scrolling up
                navbar.classList.remove('hidden');
            }
        } else {
            navbar.classList.remove('hidden');
        }

        lastScrollTop = scrollTop;
    });
    </script>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const fadeImage = document.querySelector('.fade-in-image');

        fadeImage.addEventListener('error', function() {
            console.error('Error loading image:', this.src);
            this.style.display = 'none';
        });

        function checkScroll() {
            const imagePosition = fadeImage.getBoundingClientRect().top;
            const screenPosition = window.innerHeight / 1.3;

            if (imagePosition < screenPosition) {
                fadeImage.classList.add('visible');
            }
        }

        window.addEventListener('scroll', checkScroll);
        checkScroll(); // Initial check in case the image is already in view
    });
    </script>
</body>

</html>