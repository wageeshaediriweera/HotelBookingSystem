<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="../css/index.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" />
    <link href="https://fonts.googleapis.com/css2?family=Lato:wght@100;300;400;700;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">

    <title>Admin Panel - Hotel Management</title>

    <style>
    /* Sidebar styles */
    .sidebar {
        height: 100vh;
        width: 80px;
        background-color: #f8f9fa;
        position: fixed;
        top: 0;
        left: 0;
        display: flex;
        flex-direction: column;
        align-items: center;
        padding-top: 20px;
        padding-bottom: 20px;
        z-index: 1000;
    }

    .sidebar i {
        font-size: 24px;
        margin-bottom: 20px;
        color: #000;
    }

    .sidebar a {
        text-decoration: none;
        color: #000;
        display: flex;
        align-items: center;
        width: 100%;
        justify-content: center;
        padding: 15px;
        font-size: 18px;
        transition: background-color 0.3s, border-left 0.3s;
        position: relative;
    }

    .sidebar a:hover {
        background-color: #ddd;
    }

    /* Active tab styles */
    .sidebar a.active {
        color: #007bff;
        border-left: 4px solid #007bff;
    }

    /* Tooltip styles */
    .sidebar a[title] {
        position: relative;
    }

    .sidebar a[title]::after {
        content: attr(title);
        position: absolute;
        left: 100%;
        top: 50%;
        transform: translateY(-50%);
        white-space: nowrap;
        background-color: #000;
        color: #fff;
        padding: 5px 10px;
        border-radius: 5px;
        opacity: 0;
        transition: opacity 0.3s;
        pointer-events: none;
    }

    .sidebar a:hover[title]::after {
        opacity: 1;
    }

    /* Content area */
    .content {
        margin-left: 80px;
        padding: 20px;
    }

    /* Push bottom links to the bottom */
    .bottom-links {
        margin-top: auto;
        display: flex;
        flex-direction: column;
        align-items: center;
        width: 100%;
    }

    .bottom-links a {
        margin-bottom: 15px;
        text-decoration: none;
        color: #000;
        display: flex;
        align-items: center;
        justify-content: center;
        width: 100%;
        padding: 15px;
        font-size: 18px;
    }

    .footer {
        background-color: black;
        color: white;
        text-align: center;
        padding: 10px 0;
        position: fixed;
        bottom: 0;
        left: 0;
        width: 100%;
        font-size: 14px;
        box-shadow: 0 -1px 5px rgba(0, 0, 0, 0.1);
        z-index: 1000;
    }

    .footer p {
        margin: 0;
    }
    </style>
</head>

<body>

    <!-- Sidebar -->
    <div class="sidebar">
        <a href="adminhome.php" title="Dashboard">
            <i class="fas fa-home"></i>
        </a>
        <a href="admincafe.php" title="Manage Food Orders">
            <i class="fas fa-utensils"></i>
        </a>
        <a href="adminrooms.php" title="Manage Rooms">
            <i class="fas fa-bed"></i>
        </a>
        <a href="adminbookings.php" title="Manage Bookings">
            <i class="fas fa-calendar-check"></i>
        </a>
        <a href="adminorders.php" title="Food Orders">
            <i class="fas fa-concierge-bell"></i>
        </a>
        <a href=" adminmsg.php" title="Messages">
            <i class="fas fa-message"></i>
        </a>

        <style>
        .user-profile img {
            height: 40px;
            width: 40px;
            border-radius: 50%;
            margin-right: 10px;
            object-fit: cover;
        }

        .profile-icon {
            height: 40px;
            width: 40px;
            border-radius: 50%;
            object-fit: cover;
        }
        </style>

        <!-- Bottom Links -->
        <dv class="bottom-links">
            <?php 
    // Check if user is logged in
    if (isset($_SESSION['user_id'])) {
        // User is logged in, retrieve username and image
        $username = $_SESSION['username'];
        $image = $_SESSION['image'];

        // Ensure the image path is not empty and set a default image if necessary
        if (!empty($image)) {
            // Display user profile image
            echo '<a href="profile.php" title="' . htmlspecialchars($username) . '">
            <img src="../uploads/' . htmlspecialchars($image) . '" alt="User Image" class="profile-icon">
            </a>';


        } else {
            // Fallback if no image is available
            echo '<a href="profile.php" title="' . htmlspecialchars($username) . '">
                    <i class="fas fa-user-circle default-icon"></i>
                  </a>';
        }

    } else {
        // User is not logged in, display default icon
        echo '<a href="profile.php" title="Profile">
                <i class="fas fa-user-circle default-icon"></i>
              </a>';
    }
    ?>

            <a href="#" title="Settings"><i class="fas fa-cog"></i></a><a href="logout.php" title="Logout"><i
                    class="fas fa-sign-out-alt"></i></a>
    </div>
    </div>

    <!-- Bootstrap JS and dependencies -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.2/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous">
    </script>

    <!-- JavaScript to set active link -->
    <script>
    document.addEventListener('DOMContentLoaded', function() {
            // Get the current page URL
            const currentPage = window.location.pathname.split('/').pop();

            // Get all sidebar links
            const sidebarLinks = document.querySelectorAll('.sidebar a');

            sidebarLinks.forEach(link => {
                    // Get the href attribute of each link
                    const linkHref = link.getAttribute('href');

                    // If the link's href matches the current page URL, add 'active' class
                    if (currentPage === linkHref) {
                        link.classList.add('active');
                    }
                }

            );
        }

    );
    </script>

    <footer class="footer">
        <p>&copy; <?php echo date("Y"); ?> SurfBay Hotel. All rights reserved.</p>
    </footer>
</body>

</html>