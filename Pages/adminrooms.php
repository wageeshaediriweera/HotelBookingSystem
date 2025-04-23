<?php
session_start();

// Enable error reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include('../Components/db.php'); // Ensure this file correctly establishes $con

// Handle deletion
if (isset($_GET['delete_id'])) {
    $delete_id = mysqli_real_escape_string($con, $_GET['delete_id']);
    $delete_query = "DELETE FROM rooms WHERE id = '$delete_id'";
    if (mysqli_query($con, $delete_query)) {
        echo "<script>alert('Room deleted successfully.'); window.location.href='adminrooms.php';</script>";
    } else {
        echo "<script>alert('Error: Failed to delete room. " . mysqli_error($con) . "'); window.location.href='adminrooms.php';</script>";
    }
    exit();
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['update_id'])) {
        // This is an update operation
        $update_id = mysqli_real_escape_string($con, $_POST['update_id']);
        $room_name = mysqli_real_escape_string($con, $_POST['update_name']);
        $price = mysqli_real_escape_string($con, $_POST['update_price']);
        $description = mysqli_real_escape_string($con, $_POST['update_description']);
        $room_type = mysqli_real_escape_string($con, $_POST['update_category']);

        $update_query = "UPDATE rooms SET room_name='$room_name', price_per_night='$price', description='$description', room_type='$room_type'";

        // Handle image update
        if (isset($_FILES['update_image']) && $_FILES['update_image']['error'] == UPLOAD_ERR_OK) {
            $image_tmp = $_FILES['update_image']['tmp_name'];
            $image_name = time() . '_' . basename($_FILES['update_image']['name']);
            $image_path = '../uploads/' . $image_name;

            if (move_uploaded_file($image_tmp, $image_path)) {
                $update_query .= ", image_url='$image_path'";
            } else {
                echo "<script>alert('Failed to upload new image.');</script>";
                exit;
            }
        }

        $update_query .= " WHERE id='$update_id'";

        if (mysqli_query($con, $update_query)) {
            echo json_encode(['status' => 'success', 'message' => 'Room updated successfully!']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Error updating room: ' . mysqli_error($con)]);
        }
        exit();
        
    } else {
        // This is an add operation
        $roomName = mysqli_real_escape_string($con, $_POST['name']);
        $roomDescription = mysqli_real_escape_string($con, $_POST['description']);
        $roomPrice = mysqli_real_escape_string($con, $_POST['price']);
        $roomType = mysqli_real_escape_string($con, $_POST['category']);

        // Handle file upload
        if (isset($_FILES['image']) && $_FILES['image']['error'] == UPLOAD_ERR_OK) {
            $imageTmpName = $_FILES['image']['tmp_name'];
            $imageName = time() . '_' . basename($_FILES['image']['name']);
            $imagePath = '../uploads/' . $imageName;

            if (!is_dir('../uploads')) {
                mkdir('../uploads', 0777, true);
            }

            if (move_uploaded_file($imageTmpName, $imagePath)) {
                $query = "INSERT INTO rooms (room_name, description, price_per_night, room_type, image_url) VALUES ('$roomName', '$roomDescription', '$roomPrice', '$roomType', '$imagePath')";
                
                if (mysqli_query($con, $query)) {
                    echo "<script>alert('Room added successfully'); window.location.href='adminrooms.php';</script>";
                } else {
                    echo "<script>alert('Error: Failed to add room. " . mysqli_error($con) . "');</script>";
                }
            } else {
                echo "<script>alert('Failed to upload image.');</script>";
            }
        } else {
            echo "<script>alert('No image uploaded or there was an upload error.');</script>";
        }
    }
    exit;
}

// Fetch rooms for listing or other operations
$search = isset($_GET['search']) ? mysqli_real_escape_string($con, $_GET['search']) : '';
$categoryFilter = isset($_GET['category']) ? mysqli_real_escape_string($con, $_GET['category']) : '';

$whereClause = [];
if ($search) {
    $whereClause[] = "(room_name LIKE '%$search%' OR description LIKE '%$search%' OR price_per_night LIKE '%$search%')";
}
if ($categoryFilter) {
    $whereClause[] = "room_type = '$categoryFilter'";
}
$whereSql = $whereClause ? 'WHERE ' . implode(' AND ', $whereClause) : '';

$rooms_query = "SELECT * FROM rooms $whereSql";
$rooms_result = mysqli_query($con, $rooms_query);

// Fetch categories for filter
$categories_query = "SELECT DISTINCT room_type FROM rooms";
$categories_result = mysqli_query($con, $categories_query);


?>



<!DOCTYPE html>
<html lang="en">
<title>Admin Panel - Room Management</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" />

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="../css/index.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" />
    <link href="https://fonts.googleapis.com/css2?family=Lato:wght@100;300;400;700;900&display=swap" rel="stylesheet">
    <title>Admin Panel - Room Management</title>
    <style>
    body {
        margin: 0;
        padding: 0;
        font-family: 'Lato', sans-serif;
    }

    .content {
        padding: 20px;
        background-color: #666;
        background: url('../images/background.jpg') no-repeat center center fixed;
        background-size: cover;
        position: relative;
    }

    .logo {
        position: absolute;
        top: 35px;
        right: 100px;
        height: 50px;
        z-index: 1000;
        width: auto;
    }

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
        background-color: #0056b3;
    }

    .floating-button i {
        font-size: 30px;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 20px;
        background-color: #fff;
        border: 1px solid #ddd;
        box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.2);
        text-align: center;
    }

    th,
    td {
        padding: 8px;
        text-align: center;
        min-width: 100px;

    }

    td {
        border: 1px solid #ddd;
    }

    th {
        background-color: #f2f2f2;

    }

    table tr:nth-child(even) {
        background-color: #f9f9f9;
    }

    .description-column {
        max-width: 400px;
        word-wrap: break-word;

    }

    img {
        max-width: 100px;
        height: auto;
    }




    .modal-dialog-centered {
        display: flex;
        align-items: center;
        justify-content: center;
        min-height: calc(100% - 1rem);
        padding: 30px;
    }

    .modal-lg {
        max-width: 600px;
        margin: auto;
    }

    .form-group {
        margin-bottom: 15px;
    }

    .form-control-file {
        padding: 6px;
        background-color: #f8f9fa;
        border: 1px solid #ced4da;
        border-radius: .25rem;
    }

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

    .action-column {
        white-space: nowrap;
    }

    .btn-action {
        display: inline-block;
        width: 80px;
        margin-bottom: 5px;
        padding: 10px 10px;
        color: #fff;
        border: none;
        border-radius: 5px;
        text-align: center;
        cursor: pointer;
        text-decoration: none;
        transition: background-color 0.3s, opacity 0.3s;
        font-size: 14px;
    }

    .btn-update {
        background-color: #b95d58;
    }

    .btn-delete {
        background-color: #bba586;
    }

    .btn-update:hover,
    .btn-delete:hover {
        opacity: 0.7;
        color: #ddd;
        text-decoration: none;
    }
    </style>
</head>

<body>
    <?php include("../Components/adminnavbar.php"); ?>

    <!-- Logo -->
    <img src="../images/logo.png" alt="Logo" class="logo">

    <!-- Content -->
    <div class="content">
        <div class="navbar-section">
            <h1>Manage Rooms</h1>
            <p>SURF EASE by Surf Bay</p>
        </div>

        <!-- Search bar and filters -->
        <div class="container mt-3">
            <div class="row">
                <div class="col-md-8">
                    <input type="text" id="search" class="form-control" placeholder="Search rooms..." />
                </div>
                <div class="col-md-4">
                    <select id="filterType" class="form-control">
                        <option value="">All Room Types</option>
                        <option value="Single">Single</option>
                        <option value="Double">Double</option>
                        <option value="Suite">Suite</option>
                        <?php foreach ($roomTypes as $type) { ?>
                        <option value="<?php echo $type; ?>"><?php echo $type; ?></option>
                        <?php } ?>
                    </select>
                </div>
            </div>
        </div>

        <?php
        // Query to fetch rooms
        $roomsQuery = "SELECT * FROM rooms";
        $roomsResult = mysqli_query($con, $roomsQuery);
        ?>


        <table>
            <thead>
                <tr>
                    <th>Id</th>
                    <th>Room Name</th>
                    <th>Image</th>
                    <th>Description</th>
                    <th>Room Type</th>
                    <th>Price Per Night</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (mysqli_num_rows($roomsResult) > 0) { ?>
                <?php while ($row = mysqli_fetch_assoc($roomsResult)) { ?>
                <tr>
                    <td class="id-column"><?php echo htmlspecialchars($row['id']); ?></td>
                    <td><?php echo htmlspecialchars($row['room_name']); ?></td>
                    <td><img src="<?php echo htmlspecialchars($row['image_url']); ?>" alt="Room Image"
                            style="width: 100px; height: 100px; object-fit: cover;" /></td>
                    <td class="description-column"><?php echo htmlspecialchars($row['description']); ?></td>
                    <td><?php echo htmlspecialchars($row['room_type']); ?></td>
                    <td>LKR <?php echo htmlspecialchars($row['price_per_night']); ?></td>
                    <td class="action-column">
                        <button onclick="openUpdateModal(<?php echo htmlspecialchars(json_encode($row)); ?>)"
                            class="btn-action btn-update">Update</button>
                        <button onclick="confirmDelete(<?php echo htmlspecialchars($row['id']); ?>)"
                            class="btn-action btn-delete">Delete</button>
                    </td>
                </tr>
                <?php } ?>
                <?php } else { ?>
                <tr>
                    <td colspan="7">No records found.</td>
                </tr>
                <?php } ?>
            </tbody>
        </table>



        <!-- Floating Button to trigger modal -->
        <button class="floating-button" data-toggle="modal" data-target="#addRoomModalLabel">
            <i class="fas fa-plus"></i>
        </button>

    </div>
    <!-- Modal for Adding Rooms -->
    <div class="modal fade" id="addRoomModalLabel" tabindex="-1" aria-labelledby="addRoomModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addRoomModalLabel">Add New Room</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="adminrooms.php" method="post" enctype="multipart/form-data">
                    <div class="modal-body">
                        <div class="mb-3 row">
                            <div class="col-md-6">
                                <label for="name" class="form-label">Room Name</label>
                                <input type="text" class="form-control" id="name" name="name" required>
                            </div>
                            <div class="col-md-6">
                                <label for="price" class="form-label">Price Per Night</label>
                                <input type="number" class="form-control" id="price" name="price" step="0.01" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control" id="description" name="description" rows="3"
                                required></textarea>
                        </div>
                        <div class="mb-3 row">
                            <div class="col-md-6">
                                <label for="category" class="form-label">Room Type</label>
                                <select id="category" name="category" class="form-control" required>
                                    <option value="Single">Single</option>
                                    <option value="Double">Double</option>
                                    <option value="Suite">Suite</option>
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
                        <button type="submit" class="btn btn-primary">Add Room</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="updateRoomModal" tabindex="-1" aria-labelledby="updateRoomModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="updateRoomModalLabel">Update Room</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="updateRoomForm" action="adminrooms.php" method="post" enctype="multipart/form-data">
                    <div class="modal-body">
                        <input type="hidden" id="update_id" name="update_id">
                        <div class="mb-3 row">
                            <div class="col-md-6">
                                <label for="update_name" class="form-label">Room Name</label>
                                <input type="text" class="form-control" id="update_name" name="update_name" required>
                            </div>
                            <div class="col-md-6">
                                <label for="update_price" class="form-label">Price Per Night</label>
                                <input type="number" class="form-control" id="update_price" name="update_price"
                                    step="0.01" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="update_description" class="form-label">Description</label>
                            <textarea class="form-control" id="update_description" name="update_description" rows="3"
                                required></textarea>
                        </div>
                        <div class="mb-3 row">
                            <div class="col-md-6">
                                <label for="update_category" class="form-label">Room Type</label>
                                <select id="update_category" name="update_category" class="form-control" required>
                                    <option value="Single">Single</option>
                                    <option value="Double">Double</option>
                                    <option value="Suite">Suite</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="update_image" class="form-label">Image</label>
                                <input type="file" class="form-control" id="update_image" name="update_image"
                                    accept="image/*">
                                <div id="image_preview" class="mt-2">
                                    <img id="current_image_preview" src="" alt="Current Image"
                                        style="max-width: 100px; max-height: 100px; display: none;">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Update Room</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>


    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.getElementById('search');
        const filterType = document.getElementById('filterType');
        const tableRows = document.querySelectorAll('table tbody tr');

        function filterRooms() {
            const searchValue = searchInput.value.toLowerCase();
            const typeValue = filterType.value.toLowerCase();
            console.log('Filter Type:', typeValue);

            tableRows.forEach(row => {
                const roomName = row.querySelector('td:nth-child(2)').textContent.toLowerCase();
                const roomType = row.querySelector('td:nth-child(5)').textContent.toLowerCase()
                    .trim();
                console.log('Room Type:', roomType);

                const matchesSearch = roomName.includes(searchValue);
                const matchesType = typeValue === '' || roomType.includes(typeValue);
                console.log('Matches Type:', matchesType, 'for', roomType, 'and', typeValue);

                if (matchesSearch && matchesType) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        }

        searchInput.addEventListener('input', filterRooms);
        filterType.addEventListener('change', filterRooms);
    });
    </script>

    <script>
    function confirmDelete(id) {
        if (confirm('Are you sure you want to delete this room?')) {
            window.location.href = 'adminrooms.php?delete_id=' + id;
        }
    }
    </script>


    <script>
    function openUpdateModal(roomData) {
        // Populate the form fields with the room data
        document.getElementById('update_id').value = roomData.id;
        document.getElementById('update_name').value = roomData.room_name;
        document.getElementById('update_price').value = roomData.price_per_night;
        document.getElementById('update_description').value = roomData.description;
        document.getElementById('update_category').value = roomData.room_type;

        // Set the current image preview
        var currentImagePreview = document.getElementById('current_image_preview');
        currentImagePreview.src = roomData.image_url;
        currentImagePreview.style.display = 'block';

        // Open the modal
        var updateModal = new bootstrap.Modal(document.getElementById('updateRoomModal'));
        updateModal.show();
    }

    // Handle new image preview
    document.getElementById('update_image').addEventListener('change', function(event) {
        var file = event.target.files[0];
        var reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('current_image_preview').src = e.target.result;
        };
        reader.readAsDataURL(file);
    });

    // Handle form submission
    document.getElementById('updateRoomForm').addEventListener('submit', function(event) {
        event.preventDefault();

        var formData = new FormData(this);

        fetch('adminrooms.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json()) // Parse the response as JSON
            .then(jsonResult => {
                if (jsonResult.status === 'success') {
                    alert(jsonResult.message);
                    location.reload();
                } else {
                    alert('Error: ' + jsonResult.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while updating the room.');
            });

    });
    </script>
</body>

</html>
<?php
mysqli_close($con);
?>