<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login & Registration Page</title>
    <link rel="stylesheet" href="../css/loginstyles.css">
</head>

<body>
    <div class="login-container">
        <!-- Login Form -->
        <div class="login-box" id="login-box">
            <h2>Login</h2>
            <form action="login_handler.php" method="POST">
                <div class="input-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" placeholder="Enter your email" required
                        autocapitalize="off">
                </div>
                <div class="input-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" placeholder="Enter your password" required>
                </div>
                <button type="submit" class="login-button">Login</button>
            </form>
            <div class="switch-form-text">
                Don't have an account? <a href="#" onclick="showRegistration()">Sign Up</a>
            </div>
        </div>

        <!-- Registration Form -->
        <div class="login-box" id="registration-box" style="display: none;">
            <h2>Register</h2>
            <form action="register_handler.php" method="POST" enctype="multipart/form-data">
                <div class="input-group">
                    <label for="name">Full Name</label>
                    <input type="text" id="name" name="name" placeholder="Enter your full name" required>
                </div>
                <div class="input-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" placeholder="Enter your email" required>
                </div>
                <div class="input-group">
                    <label for="profile-pic">Profile Picture</label>
                    <input type="file" id="profile-pic" name="profile-pic" accept="image/*" required>
                </div>
                <div class="input-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" placeholder="Enter your password" required>
                </div>
                <div class="input-group">
                    <label for="confirm-password">Confirm Password</label>
                    <input type="password" id="confirm-password" name="confirm-password"
                        placeholder="Confirm your password" required>
                </div>
                <button type="submit" class="login-button">Register</button>
            </form>
            <div class="switch-form-text">
                Already have an account? <a href="#" onclick="showLogin()">Login</a>
            </div>
        </div>
    </div>

    <script>
    function showRegistration() {
        document.getElementById('login-box').style.display = 'none';
        document.getElementById('registration-box').style.display = 'block';
    }

    function showLogin() {
        document.getElementById('login-box').style.display = 'block';
        document.getElementById('registration-box').style.display = 'none';
    }

    document.querySelector('#login-box form').addEventListener('submit', function(e) {
        e.preventDefault();

        var formData = new FormData(this);

        fetch('login_handler.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                console.log('Response Data:', data); // Debugging line
                if (data.status === 'success') {
                    console.log('Redirecting to:', data.redirect); // Check the redirect URL
                    window.location.href = data.redirect; // Use the dynamic redirect
                } else {
                    alert(data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred during login.');
            });

    });


    document.getElementById('registration-box').querySelector('form').addEventListener('submit', function(e) {
        e.preventDefault(); // Prevent the default form submission

        var formData = new FormData(this); // Gather form data

        // Send the form data to the server using fetch
        fetch('register_handler.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                console.log('Response Data:', data);

                if (data.status === 'success') {
                    window.location.href = 'http://localhost/myFolder/SurfBay/Pages/login.php';
                } else {
                    // Show an alert with the error message
                    alert(data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred during registration.');
            });
    });
    </script>

</body>

</html>