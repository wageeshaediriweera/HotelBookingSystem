<section>
    <nav class="navbar">
        <ul class="nav-links">
            <li><a href="../Pages/index.php">OUR STORY</a></li>
            <li><a href="../Pages/ourcafe.php">FOOD & DRINK</a></li>
            <li><a href="../Pages/index.php">
                    <img src="../Images/logo.png" alt="Logo" class="logo">
                </a></li>
            <li><a href="../Pages/rooms.php">ROOMS & VILLA</a></li>
            <li><a href="../Pages/contact.php">CONTACT</a></li>
        </ul>
    </nav>
</section>
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