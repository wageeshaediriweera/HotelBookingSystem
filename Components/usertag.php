<div class="half-circle-rectangle">
    <style>
    .half-circle-rectangle {
        width: 110px;
        height: 90px;
        background-color: #b95d58;
        position: fixed;
        top: 200px;
        right: 0;
        z-index: 1000;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 2px 3px 4px rgba(0, 0, 0, 0.3), 4px 7px 15px rgba(0, 0, 0, 0.3), 9px 15px 25px rgba(0, 0, 0, 0.2);
        transition: transform 0.3s ease-in-out;
    }

    .half-circle-rectangle::before {
        content: "";
        width: 90px;
        height: 90px;
        background-color: #b95d58;
        border-radius: 45px 0 0 45px;
        position: absolute;
        left: -40px;
        top: 0;
        z-index: 999;
    }

    .profile-icon {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        width: 90px;
        height: 90px;
        border-radius: 50%;
        background-color: #b95d58;
        color: #fff;
        border: none;
        cursor: pointer;
        font-size: 24px;
        text-decoration: none;
        z-index: 1001;
    }

    .user-profile {
        display: flex;
        align-items: center;
        justify-content: flex-start;
    }

    .user-profile img {
        height: 40px;
        width: 40px;
        border-radius: 50%;
        margin-right: 10px;
        object-fit: cover;
    }

    .login-txt,
    .username {
        font-size: 18px;
        margin-right: 20px;
        color: #fffcda;
    }

    /* On hover, make the element visible */
    .half-circle-rectangle:hover {
        transform: translateX(0) !important;
    }
    </style>
    <a class="profile-icon"
        href="<?php echo isset($_SESSION['username']) ? '../Pages/userprofile.php' : '../Pages/login.php'; ?>">
        <div class="user-profile">
            <?php
            if (isset($_SESSION['username'])) {
                $username = $_SESSION['username'];
                $query = "SELECT image FROM userdetails WHERE username = ?";
                $stmt = mysqli_prepare($con, $query);
                mysqli_stmt_bind_param($stmt, "s", $username);
                mysqli_stmt_execute($stmt);
                mysqli_stmt_bind_result($stmt, $image);
                mysqli_stmt_fetch($stmt);
                mysqli_stmt_close($stmt);
                
                if (!empty($image)) {
                    echo "<img src='../uploads/$image' alt='User Image'>";
                } else {
                    echo "<img src='../Images/def_user.png' alt='default-user-image' class='default-user-image'>";
                }
                echo "<div class='user-info'>";
                echo "<div class='username'>$username</div>";
                echo "</div>";
            } else {
                echo "<img src='../Images/def_user.png' alt='default-user-image' class='default-user-image'>";
                echo "<div class='login-txt'>Login</div>";
            }
            ?>
        </div>
    </a>
</div>

<script>
window.addEventListener('scroll', () => {
    const halfCircleRect = document.querySelector('.half-circle-rectangle');
    const halfwayPoint = document.body.scrollHeight / 5;
    const currentScrollY = window.scrollY;

    // If scrolling past the halfway point, hide it
    if (currentScrollY > halfwayPoint) {
        halfCircleRect.style.transform = 'translateX(110px)'; // Slide out (right)
    } else {
        halfCircleRect.style.transform = 'translateX(0)'; // Slide back (visible)
    }
});

// Add hover functionality to reveal the element while hovering
const halfCircleRect = document.querySelector('.half-circle-rectangle');

halfCircleRect.addEventListener('mouseleave', () => {
    // Delay hiding to make it smoother after mouse leaves
    setTimeout(() => {
        const currentScrollY = window.scrollY;
        const halfwayPoint = document.body.scrollHeight / 5;

        if (currentScrollY > halfwayPoint) {
            halfCircleRect.style.transform = 'translateX(110px)'; // Slide back out
        }
    }, 300); // 300ms delay before hiding again
});
</script>