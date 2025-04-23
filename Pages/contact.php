<?php
session_start();
include("../Components/db.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = mysqli_real_escape_string($con, $_POST['name']);
    $email = mysqli_real_escape_string($con, $_POST['email']);
    $subject = mysqli_real_escape_string($con, $_POST['subject']);
    $message = mysqli_real_escape_string($con, $_POST['message']);

    $query = "INSERT INTO contact_messages (name, email, subject, message) VALUES (?, ?, ?, ?)";
    $stmt = mysqli_prepare($con, $query);
    mysqli_stmt_bind_param($stmt, "ssss", $name, $email, $subject, $message);

    if (mysqli_stmt_execute($stmt)) {
        $_SESSION['message'] = "Your message has been sent successfully!";
    } else {
        $_SESSION['error'] = "Sorry, there was an error sending your message. Please try again.";
    }

    mysqli_stmt_close($stmt);
    mysqli_close($con);

    // Redirect to the same page to avoid form resubmission
    header("Location: contact.php");
    exit();
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Contact Us</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/contact.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="stylesheet" href="../css/index.css">
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

    .hero-button {
        background-color: transparent;
        font-size: 16px;
        text-decoration: none;
        color: white;
        border: 1px solid white;
        padding: 10px 20px;
        border-radius: 20px;
        cursor: pointer;
        transition: background-color 0.3s ease;
    }

    .hero-button:hover {
        background-color: white;
        color: black;
    }
    </style>
</head>

<body>
    <?php include('../Components/navigationbar.php'); ?>

    <section class="hero-section">
        <img src="../Images/contactwall.png" alt="Contact Us" class="hero-image">
        <div class="hero-overlay">
            <div class="hero-content">
                <p class="hero-subtitle">Get in touch with Surf Bay Restaurant</p>
                <a href="#" class="hero-button" onclick="scrollToGetInTouch()">Get in Touch</a>
            </div>
        </div>
    </section>

    <section id="get-in-touch" class="get-in-touch">
        <div class="container">
            <h3>Send us a message</h3>
            <p>Share your thoughts, inquiries, or plan your stay. We're eager to assist and make your Mirissa visit
                unforgettable. Let's connect and start crafting your luxury escape.</p>
        </div>
    </section>

    <div class="container">
        <div class="contact-content">
            <div class="contact-info">
                <h2>Contact Information</h2>
                <p><i class="fas fa-map-marker-alt"></i> Beach Road, Mirissa, Sri Lanka</p>
                <p><i class="fas fa-phone"></i> +94 123 456 789</p>
                <p><i class="fas fa-envelope"></i> info@surfbayrestaurant.com</p>
                <p><i class="fas fa-clock"></i> 7am – 10pm, Mon-Sun * Open all days</p>
            </div>
            <div class="contact-form">
                <h2>Leave it here</h2>
                <form action="contact.php" method="POST">
                    <input type="text" name="name" placeholder="Your Name" required>
                    <input type="email" name="email" placeholder="Your Email" required>
                    <input type="text" name="subject" placeholder="Subject" required>
                    <textarea name="message" placeholder="Your Message" required></textarea>
                    <button type="submit" class="submit-btn">Send Message</button>
                </form>
            </div>
        </div>
    </div>

    <?php include('../Components/footer.php'); ?>

    <script>
    function scrollToGetInTouch() {
        document.getElementById('get-in-touch').scrollIntoView({
            behavior: 'smooth'
        });
    }
    </script>

    <script>
    function scrollToGetInTouch() {
        document.getElementById('get-in-touch').scrollIntoView({
            behavior: 'smooth'
        });
    }

    // Display message or error alert if set
    window.onload = function() {
        <?php if (isset($_SESSION['message'])): ?>
        alert("<?php echo $_SESSION['message']; ?>");
        <?php unset($_SESSION['message']); ?>
        <?php endif; ?>

        <?php if (isset($_SESSION['error'])): ?>
        alert("<?php echo $_SESSION['error']; ?>");
        <?php unset($_SESSION['error']); ?>
        <?php endif; ?>
    };
    </script>
</body>

</html>