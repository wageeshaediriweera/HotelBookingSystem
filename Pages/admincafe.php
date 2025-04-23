<?php
session_start();
// Enable error reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include('../Components/db.php');

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['update_id'])) {
        // This is an update operation
        $update_id = mysqli_real_escape_string($con, $_POST['update_id']);
        $name = mysqli_real_escape_string($con, $_POST['update_name'] ?? '');
        $price = mysqli_real_escape_string($con, $_POST['update_price'] ?? '');
        $description = mysqli_real_escape_string($con, $_POST['update_description'] ?? '');
        $category = mysqli_real_escape_string($con, $_POST['update_category'] ?? '');

        $update_query = "UPDATE foods SET name='$name', price='$price', description='$description', category='$category'";

        // Handle image update
        if (isset($_FILES['update_image']) && $_FILES['update_image']['error'] == UPLOAD_ERR_OK) {
            $imageTmpName = $_FILES['update_image']['tmp_name'];
            $imageName = time() . '_' . basename($_FILES['update_image']['name']);
            $imagePath = '../uploads/' . $imageName;

            if (move_uploaded_file($imageTmpName, $imagePath)) {
                $update_query .= ", image='$imagePath'";
            } else {
                echo "Failed to upload new image.";
            }
        }

        $update_query .= " WHERE id='$update_id'";

        if (mysqli_query($con, $update_query)) {
            echo "<script>alert('Food item updated successfully!'); window.location.href='admincafe.php';</script>";
        } else {
            echo "Error updating record: " . mysqli_error($con);
        }
    } else {
        $name = mysqli_real_escape_string($con, $_POST['name'] ?? '');
        $price = mysqli_real_escape_string($con, $_POST['price'] ?? '');
        $description = mysqli_real_escape_string($con, $_POST['description'] ?? '');
        $category = mysqli_real_escape_string($con, $_POST['category'] ?? '');

        // Handle file upload
        if (isset($_FILES['image']) && $_FILES['image']['error'] == UPLOAD_ERR_OK) {
            $imageTmpName = $_FILES['image']['tmp_name'];
            $imageName = basename($_FILES['image']['name']);
            $imagePath = '../uploads/' . $imageName;

            if (!is_dir('../uploads')) {
                mkdir('../uploads', 0777, true);
            }
        
            // Move the uploaded file to the 'uploads' directory
            if (move_uploaded_file($imageTmpName, $imagePath)) {
                // Prepare and execute the SQL statement
                $query = "INSERT INTO foods (name, price, description, category, image) VALUES ('$name', '$price', '$description', '$category', '$imagePath')";
                
                if (mysqli_query($con, $query)) {
                    echo "<script>alert('Food item added successfully!'); window.location.href='admincafe.php';</script>";
                } else {
                    echo "Error: " . mysqli_error($con);
                    echo "<br>Query: " . $query;
                }
            } else {
                echo "Failed to upload image.";
            }
        } else {
            echo "No image uploaded or there was an upload error.";
        }
    }
}

// Handle deletion
if (isset($_GET['delete_id'])) {
    $delete_id = mysqli_real_escape_string($con, $_GET['delete_id']);
    $delete_query = "DELETE FROM foods WHERE id = '$delete_id'";
    
    if (mysqli_query($con, $delete_query)) {
        echo "<script>alert('Food item deleted successfully!'); window.location.href='admincafe.php';</script>";
    } else {
        echo "Error: " . mysqli_error($con);
    }
}

// Handle search
$search = isset($_GET['search']) ? mysqli_real_escape_string($con, $_GET['search']) : '';
$categoryFilter = isset($_GET['category']) ? mysqli_real_escape_string($con, $_GET['category']) : '';

$whereClause = [];
if ($search) {
    $whereClause[] = "(name LIKE '%$search%' OR description LIKE '%$search%' OR price LIKE '%$search%')";
}
if ($categoryFilter) {
    $whereClause[] = "category = '$categoryFilter'";
}
$whereSql = $whereClause ? 'WHERE ' . implode(' AND ', $whereClause) : '';

$foods_query = "SELECT * FROM foods $whereSql";
$foods_result = mysqli_query($con, $foods_query);

// Fetch categories for filter
$categories_query = "SELECT DISTINCT category FROM foods";
$categories_result = mysqli_query($con, $categories_query);

// Close the database connection
mysqli_close($con);
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="../css/index.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" />
    <link href="https://fonts.googleapis.com/css2?family=Lato:wght@100;300;400;700;900&display=swap" rel="stylesheet">

    <title>Manage Foods</title>

    <style>
    /* General Styles */
    body {
        margin: 0;
        padding: 0;
        font-family: 'Lato', sans-serif;
    }

    .content {
        position: relative;
        margin-left: 80px;
        padding: 20px;
        height: 100vh;
        background: url('../images/background.jpg') no-repeat center center fixed;
        background-size: cover;
    }

    .logo {
        position: absolute;
        top: 35px;
        right: 100px;
        height: 50px;
        width: auto;
        z-index: 1000;
    }

    /* Navbar Section */
    .navbar-section {
        background-color: #000;
        color: #fff;
        padding: 20px 60px;
        height: 130px;
        display: flex;
        flex-direction: column;
        justify-content: center;
        margin-bottom: 20px;
        border-radius: 5px;
    }

    /* Floating Button */
    .floating-button {
        position: fixed;
        bottom: 60px;
        right: 40px;
        background-color: #000;
        color: white;
        border: none;
        border-radius: 50%;
        width: 80px;
        height: 80px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        cursor: pointer;
        transition: background-color 0.3s;
    }

    .floating-button:hover {
        background-color: #333;
    }

    .floating-button i {
        font-size: 30px;
    }

    /* Modal Styles */
    .modal-body {
        padding: 30px;
    }

    .modal-header {
        padding-bottom: 0;
    }

    .modal-title {
        font-weight: 700;
    }

    .modal-footer {
        display: flex;
        justify-content: flex-end;
    }

    /* styles.css */
    table {
        width: 100%;
        border-collapse: collapse;
        border: 1px solid #ddd;
        box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.2);
        text-align: center;

    }

    table tr:nth-child(even) {
        background-color: #f9f9f9;
    }

    th,
    td {
        padding: 8px;
        text-align: center;
        border: 1px solid #ddd;
        min-width: 100px;
    }

    th {
        background-color: #f2f2f2;
    }

    .description-column {
        max-width: 400px;
        word-wrap: break-word;
    }

    img {
        max-width: 100px;
        height: auto;
    }


    .manage-btn,
    .delete-btn {
        display: block;
        margin-bottom: 5px;
        padding: 10px 20px;
        font-size: 16px;
        color: #fff;
        border: none;
        border-radius: 5px;
        text-align: center;
        cursor: pointer;
        text-decoration: none;
        transition: background-color 0.3s, opacity 0.3s;
    }

    .manage-btn {
        font-size: 16px;
        background-color: #b95d58;
    }

    .manage-btn:hover {
        opacity: 0.7;
        color: #f0f0f0;
        text-decoration: none;
    }

    .delete-btn {
        background-color: #bba586;
    }

    .delete-btn:hover {
        opacity: 0.7;
        color: #f0f0f0;
        text-decoration: none;
        /* Changed from 70% to 0.7 for consistency */
    }


    /* Filter and Search Styles */
    .filter-tags {
        display: flex;
        align-items: center;
        margin-bottom: 20px;
    }

    .filter-search-bar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
    }

    .filter-tags button {
        background-color: #f0f0f0;
        color: #b95d58;
        border: 1px solid #b95d58;
        padding: 10px 20px;
        cursor: pointer;
        margin-left: 20px;
        border-radius: 20px;
        font-size: 16px;
        transition: background-color 0.3s;
    }

    .filter-tags button:hover,
    .filter-tags button:active,
    .filter-tags button:focus {
        background-color: #b95d58;
        color: #f0f0f0;
        outline: none;
    }

    .filter-tags button.active {
        background-color: #b95d58;
        color: #f0f0f0;
    }

    .search-bar {
        position: relative;
        float: right;
        margin-bottom: 20px;
    }



    .search-bar input {
        padding: 10px;
        border: 1px solid #ccc;
        border-radius: 5px;
        font-size: 16px;
        width: 250px;
    }

    .search-bar img {
        position: absolute;
        left: 210px;
        top: 10px;
        width: 20px;
        height: 20px;
    }
    </style>

</head>

<body><?php include("../Components/adminnavbar.php");

    ?><img src="../images/logo.png" alt="Logo" class="logo">
    <div class="content">
        <div class="navbar-section">
            <h1>Manage Foods</h1>
            <p>SURF EASE by Surf Bay</p>
        </div>

        <div class="filter-search-bar">
            <div class="filter-tags">
                <form action="admincafe.php" method="get">
                    <button type="submit" name="category" value=""
                        class="<?php echo $categoryFilter == '' ? 'active' : ''; ?>">All Categories</button>
                    <?php while ($category = mysqli_fetch_assoc($categories_result)) { ?>
                    <button type="submit" name="category" value="<?php echo htmlspecialchars($category['category']); ?>"
                        class="<?php echo ($categoryFilter == htmlspecialchars($category['category'])) ? 'active' : ''; ?>">
                        <?php echo htmlspecialchars($category['category']); ?>
                    </button>
                    <?php } ?>
                </form>
            </div>
            <div class="search-bar">
                <form action="admincafe.php" method="get"><input type="text" name="search" placeholder="Search..."
                        value="<?php echo htmlspecialchars($search); ?>" /><img
                        src="https://img.icons8.com/ios/50/000000/search.png" alt="Search Icon" /></form>
            </div>
        </div>

        <table>
            <thead>
                <tr>
                    <th class="">ID</th>
                    <th>Name</th>
                    <th>Image</th>
                    <th class="">Description</th>
                    <th>Category</th>
                    <th>Price</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody><?php if (mysqli_num_rows($foods_result) > 0) {?>
                <?php while ($row=mysqli_fetch_assoc($foods_result)) {?>
                <tr>
                    <td class="id-column">
                        <?php echo htmlspecialchars($row['id']);?>
                    </td>
                    <td>
                        <?php echo htmlspecialchars($row['name']);?>
                    </td>
                    <td><img src="<?php echo htmlspecialchars($row['image']); ?>" alt="Food Image"
                            style="max-width: 100px; height: 100px; object-fit: cover;" />
                    </td>
                    <td class="description-column">
                        <?php echo htmlspecialchars($row['description']);?>
                    </td>
                    <td>
                        <?php echo htmlspecialchars($row['category']);?>
                    </td>
                    <td>
                        LKR <?php echo htmlspecialchars($row['price']);?>
                    </td>
                    <td>
                        <a href="#" class="manage-btn"
                            onclick="openUpdateModal(<?php echo htmlspecialchars(json_encode($row)); ?>)">Manage</a>
                        <a href="#" class="delete-btn"
                            onclick="confirmDelete(<?php echo htmlspecialchars($row['id']); ?>)">Delete</a>
                    </td>

                </tr><?php
        }

        ?><?php
    }

    else {
        ?><tr>
                    <td colspan="7">No food items found.</td>
                </tr><?php
    }

    ?>
            </tbody>
        </table>

        <button class="floating-button" data-bs-toggle="modal" data-bs-target="#addFoodModal"><i
                class="fas fa-hamburger"></i></button>

        <div class="modal fade" id="addFoodModal" tabindex="-1" aria-labelledby="addFoodModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered">

                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="addFoodModalLabel">Add Food Item</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form action="admincafe.php" method="post" enctype="multipart/form-data">
                        <div class="modal-body">
                            <div class="mb-3 row">
                                <div class="col-md-6">
                                    <label for="name" class="form-label">Name</label>
                                    <input type="text" class="form-control" id="name" name="name" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="price" class="form-label">Price</label>
                                    <input type="number" class="form-control" id="price" name="price" step="0.01"
                                        required>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="description" class="form-label">Description</label>
                                <textarea class="form-control" id="description" name="description" rows="3"
                                    required></textarea>
                            </div>
                            <div class="mb-3 row">
                                <div class="col-md-6">
                                    <label for="category" class="form-label">Category</label>
                                    <select id="category" name="category" class="form-control" required>
                                        <option value="">Select a category</option>
                                        <option value="All Day Brekkie">All Day Brekkie</option>
                                        <option value="LunchTime Mains">LunchTime Mains</option>
                                        <option value="Burger & Tacos">Burger & Tacos</option>
                                        <option value="Desserts">Desserts</option>
                                        <option value="Liquids">Liquids</option>
                                        <!-- Add more options as needed -->
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label for="image" class="form-label">Image</label>
                                    <input type="file" class="form-control" id="image" name="image" accept="image/*"
                                        required>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-primary">Add Food Item</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Update Food Modal -->
        <div class="modal fade" id="updateFoodModal" tabindex="-1" aria-labelledby="updateFoodModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="updateFoodModalLabel">Update Food Item</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form id="updateFoodForm" action="admincafe.php" method="post" enctype="multipart/form-data">
                        <div class="modal-body">
                            <input type="hidden" id="update_id" name="update_id">
                            <div class="mb-3 row">
                                <div class="col-md-6">
                                    <label for="update_name" class="form-label">Name</label>
                                    <input type="text" class="form-control" id="update_name" name="update_name"
                                        required>
                                </div>
                                <div class="col-md-6">
                                    <label for="update_price" class="form-label">Price</label>
                                    <input type="number" class="form-control" id="update_price" name="update_price"
                                        step="0.01" required>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="update_description" class="form-label">Description</label>
                                <textarea class="form-control" id="update_description" name="update_description"
                                    rows="3" required></textarea>
                            </div>
                            <div class="mb-3 row">
                                <div class="col-md-6">
                                    <label for="update_category" class="form-label">Category</label>
                                    <select id="update_category" name="update_category" class="form-control" required>
                                        <option value="">Select a category</option>
                                        <option value="All Day Brekkie">All Day Brekkie</option>
                                        <option value="LunchTime Mains">LunchTime Mains</option>
                                        <option value="Burger & Tacos">Burger & Tacos</option>
                                        <option value="Desserts">Desserts</option>
                                        <option value="Liquids">Liquids</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label for="update_image" class="form-label">Image</label>
                                    <input type="file" class="form-control" id="update_image" name="update_image"
                                        accept="image/*">
                                    <div id="image_preview" class="mt-2">
                                        <img id="current_image_preview" src="" alt="Current Image"
                                            style="max-width: 100px; max-height: 100px;">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-primary">Update Food Item</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-xP6tMtAYyg2H4F8g8AWn2+WQt5zFUzpmbh/1O6FvBmdPiDBaoB1IuJlOXk5cMxsZ" crossorigin="anonymous">
    </script>
    <script>
    function confirmDelete(id) {
        if (confirm('Are you sure you want to delete this item?')) {
            window.location.href = 'admincafe.php?delete_id=' + id;
        }
    }

    function openUpdateModal(foodData) {
        document.getElementById('update_id').value = foodData.id;
        document.getElementById('update_name').value = foodData.name;
        document.getElementById('update_price').value = foodData.price;
        document.getElementById('update_description').value = foodData.description;
        document.getElementById('update_category').value = foodData.category;

        // Set the current image preview
        var currentImagePreview = document.getElementById('current_image_preview');
        currentImagePreview.src = foodData.image;
        currentImagePreview.style.display = 'block';

        var updateModal = new bootstrap.Modal(document.getElementById('updateFoodModal'));
        updateModal.show();
    }

    // Add this function to handle new image preview
    document.getElementById('update_image').addEventListener('change', function(event) {
        var file = event.target.files[0];
        var reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('current_image_preview').src = e.target.result;
        };
        reader.readAsDataURL(file);
    });
    </script>
</body>

</html>

</html>