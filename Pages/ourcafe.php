<?php
session_start();
include("../Components/db.php");
// Check if the user is logged in

// Fetch distinct categories from the database
$category_query = "SELECT DISTINCT category FROM foods";
$category_result = mysqli_query($con, $category_query);

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="../css/ourcafe.css">
    <link rel="stylesheet" href="../css/index.css">
    <link rel="stylesheet" href="../css/ourcafehomestyle.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" />
    <link href="https://fonts.googleapis.com/css2?family=Lato:wght@100;300;400;700;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <title>Our Cafe</title>
</head>
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

.reserve-table {
    color: white;
    padding: 20px;
    border-radius: 10px;
    max-width: 400px;
    margin: 0 auto;
}

.reserve-table p {
    margin: 10px 0;
    font-size: 1.1em;
}

.reserve-table span {
    font-weight: bold;
}

.reserve-table .button {
    display: inline-block;
    color: white;
    padding: 10px 20px;
    text-decoration: none;
    border-radius: 5px;
    margin-top: 15px;
    transition: background-color 0.3s ease;
}

.reserve-table .button:hover {
    background-color: white;
    color: black;
}

/* Modal Styling */
.modal {
    position: fixed;
    z-index: 1;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.5);
}

.modal-content {
    background-color: white;
    margin: 10% auto;
    padding: 20px;
    border: 1px solid #888;
    width: 80%;
    max-width: 600px;
}

.close {
    float: right;
    font-size: 28px;
    font-weight: bold;
    cursor: pointer;
}

table {
    width: 100%;
    border-collapse: collapse;
}

th,
td {
    padding: 10px;
    border: 1px solid #ddd;
}

th {
    background-color: #f4f4f4;
}

.bill-footer {
    text-align: right;
    margin-top: 20px;
}
</style>

<body style="background-color: #FFF1E6; ">

    <?php include('../Components/navigationbar.php') ?>
    <section class="hero-section">
        <img src="../Images/cafewallimage.png" alt="Cafe Wall" class="hero-image">
        <div class="hero-overlay">
            <div class="hero-content">
                <div class="reserve-table">
                    <p>Beach Road, Mirissa, Sri Lanka</p>
                    <p><span>7am –10pm, Mon-Sun * Open all days</span></p>
                    <!-- <a href="../Pages/reservation.php" class="button">Reserve a Table Now</a> -->
                </div>
            </div>
        </div>
    </section>

    <!-- Menu Section -->
    <?php while ($category_row = mysqli_fetch_assoc($category_result)) : ?>
    <div class="category-section">
        <div class="section-title"><?php echo $category_row['category']; ?></div>
        <div class="food-container">
            <?php
            // Fetch food items for the current category
            $current_category = $category_row['category'];
            $food_query = "SELECT * FROM foods WHERE category = '$current_category'";
            $food_result = mysqli_query($con, $food_query);

            // Display food items for the current category
            while ($row = mysqli_fetch_assoc($food_result)) :
            ?>
            <div class="food-item">
                <img src="<?php echo $row['image']; ?>" alt="<?php echo $row['name']; ?>">
                <div class="food-item-content">
                    <div class="food-details">
                        <div class="food-item-title"><?php echo $row['name']; ?></div>
                        <div class="food-item-description"><?php echo $row['description']; ?></div>
                    </div>
                    <div class="food-actions">
                        <div class="food-item-price">LKR <?php echo $row['price']; ?></div>
                        <a href="#" class="add-to-cart-btn">Add to Cart</a>
                    </div>
                </div>
            </div>
            <?php endwhile; ?>
        </div>
    </div>
    <?php endwhile; ?>


    <?php include("../Components/footer.php"); ?>

    <!-- Cart Button -->
    <div class="my-cart-button">
        <a href="javascript:void(0);" onclick="toggleCart()" class="my-cart-icon btn btn-primary btn-lg">
            <i class="fas fa-shopping-cart"></i>
            <p>My Cart</p>
            <span id="cartNotification" class="cart-notification" style="display:none;"></span>
        </a>
    </div>

    <!-- Cart Sidebar -->
    <div id="cartSidebar" class="cart-sidebar">
        <div class="cart-sidebar-header">
            <h1>Your Basket</h1>
            <span class="close-cart" onclick="toggleCart()">&times;</span>
        </div>

        <div class="cart-sidebar-content" id="cartContent">
            <!-- Empty cart message -->
            <div id="emptyCartMessage" class="empty-cart-message" style="display: none;">
                Your basket looks a little empty. Why not check out some of our unbeatable deals?
            </div>
            <!-- Cart items will be dynamically added here -->
        </div>

        <div class="cart-sidebar-footer">
            <div class="cart-total">
                <span>Total:</span>
                <span id="cartTotal"></span>
            </div>
            <button id="checkoutBtn" class="checkout-btn">Checkout</button>
        </div>
    </div>

    <!-- Modal for e-Bill -->
    <div id="eBillModal" class="modal" style="display:none;">
        <div class="modal-content">
            <span class="close" onclick="closeModal()">&times;</span>

            <!-- E-BILL Title and Logo -->
            <div class="bill-header">
                <h2 style="display: flex; justify-content: space-between; align-items: center;">
                    <span style="font-size: 2rem; color: #b95d58; ">E-BILL</span>
                    <img src="../Images/logo.png" alt="Logo" style="height: 60px; width: auto; margin-right:10px; ">
                </h2>
            </div>

            <!-- Date and Time Row -->
            <div class="bill-header">
                <p style="display: flex; justify-content: space-between;">
                    <span>Date: <span id="orderDate"></span></span>
                    <span>Order ID: <span id="orderId"></span></span>
                </p>
            </div>

            <!-- Order ID and Customer Info -->
            <div class="bill-header">
                <p>Customer: <span id="customerName"></span></p>
            </div>

            <!-- Bill Items Table -->
            <table id="billTable">
                <thead>
                    <tr>
                        <th>Item</th>
                        <th>Price</th>
                        <th>Qty</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody id="billItems">
                    <!-- Items will be dynamically added here -->
                </tbody>
            </table>

            <!-- Grand Total -->
            <div class="bill-footer">
                <p><strong>Grand Total: LKR <span id="grandTotal"></span></strong></p>
            </div>
        </div>
    </div>


    <script>
    document.addEventListener('DOMContentLoaded', function() {

        setTimeout(function() {
            const categorySection = document.querySelector('.category-section');
            if (categorySection) {
                // Ensure the element is fully rendered and calculate its offset again
                const targetPosition = categorySection.getBoundingClientRect().top + window.pageYOffset;
                smoothScrollTo(targetPosition, 1500); // Smooth scroll over 1 second
            }
        }, 700);
    });

    function smoothScrollTo(targetPosition, duration) {
        const startPosition = window.pageYOffset;
        const distance = targetPosition - startPosition;
        let startTime = null;

        function animation(currentTime) {
            if (startTime === null) startTime = currentTime;
            const timeElapsed = currentTime - startTime;
            const run = ease(timeElapsed, startPosition, distance, duration);
            window.scrollTo(0, run);
            if (timeElapsed < duration) {
                requestAnimationFrame(animation);
            }
        }

        function ease(t, b, c, d) {
            t /= d / 2;
            if (t < 1) return c / 2 * t * t + b;
            t--;
            return -c / 2 * (t * (t - 2) - 1) + b;
        }

        requestAnimationFrame(animation);
    }


    // Cart functionality 
    const addToCartButtons = document.querySelectorAll('.add-to-cart-btn');
    addToCartButtons.forEach(button => {
        button.addEventListener('click', function(event) {
            event.preventDefault();
            addToCart(this);
        });
    });


    function toggleCart() {
        const cartSidebar = document.getElementById('cartSidebar');
        cartSidebar.classList.toggle('active');
        updateCartTotal();
    }

    function updateCartTotal() {
        const cartItems = document.querySelectorAll('.cart-item');
        const emptyCartMessage = document.getElementById('emptyCartMessage');
        const cartFooter = document.querySelector('.cart-sidebar-footer');
        let total = 0;
        let itemCount = cartItems.length;

        cartItems.forEach(item => {
            const price = parseFloat(item.querySelector('.cart-item-price').getAttribute('data-price'));
            const qty = parseInt(item.querySelector('.qty-controls span').innerText);
            const itemTotal = price * qty;
            item.querySelector('.cart-item-total-price').innerText = `LKR ${itemTotal.toFixed(2)}`;
            total += itemTotal;
        });

        const cartTotal = document.getElementById('cartTotal');
        cartTotal.innerText = `LKR ${total.toFixed(2)}`;

        if (itemCount > 0) {
            emptyCartMessage.style.display = 'none';
            cartFooter.style.display = 'block';
        } else {
            emptyCartMessage.style.display = 'block';
            cartFooter.style.display = 'none';
        }

        updateCartNotification(itemCount);
    }

    function increaseQty(button) {
        const qtySpan = button.previousElementSibling;
        let qty = parseInt(qtySpan.innerText);
        qtySpan.innerText = ++qty;
        updateCartTotal();
    }

    function decreaseQty(button) {
        const qtySpan = button.nextElementSibling;
        let qty = parseInt(qtySpan.innerText);
        if (qty > 1) {
            qtySpan.innerText = --qty;
            updateCartTotal();
        }
    }

    function removeItem(button) {
        const cartItem = button.closest('.cart-item');
        cartItem.remove();
        updateCartTotal();
    }

    document.getElementById('checkoutBtn').addEventListener('click', function() {

        toggleCart();
        showBill();
        checkout();
    });

    function isUserLoggedIn() {
        // You can check for the session username using an AJAX call or a global variable
        // Assuming you have an endpoint to check the session status
        let loggedIn = false;

        // Perform an AJAX request to check if the user is logged in
        const xhr = new XMLHttpRequest();
        xhr.open('GET', '/path/to/check-session.php', false); // Synchronous request
        xhr.onload = function() {
            if (xhr.status === 200) {
                const response = JSON.parse(xhr.responseText);
                loggedIn = response.loggedIn; // Assuming the response has a loggedIn field
            }
        };
        xhr.send();

        return loggedIn;
    }

    function showBill() {
        // Get cart items
        const cartItems = document.querySelectorAll('.cart-item');
        const billItems = document.getElementById('billItems');
        billItems.innerHTML = ''; // Clear the previous items in case it was opened before

        let grandTotal = 0;

        cartItems.forEach(item => {
            const itemName = item.querySelector('.cart-item-title').innerText;
            const itemPrice = parseFloat(item.querySelector('.cart-item-price').getAttribute('data-price'));
            const qty = parseInt(item.querySelector('.qty-controls span').innerText);
            const itemTotal = itemPrice * qty;

            // Add rows to the bill table
            const row = `
            <tr>
                <td>${itemName}</td>
                <td>LKR ${itemPrice.toFixed(2)}</td>
                <td>${qty}</td>
                <td>LKR ${itemTotal.toFixed(2)}</td>
            </tr>
        `;
            billItems.insertAdjacentHTML('beforeend', row);
            grandTotal += itemTotal;
        });

        // Set values for the modal
        document.getElementById('orderId').innerText = 'ORD' + Math.floor(Math.random() * 10000); // Dummy Order ID
        document.getElementById('customerName').innerText =
            '<?php echo $_SESSION["username"]; ?>'; // Using PHP session for customer name
        document.getElementById('orderDate').innerText = new Date().toLocaleString(); // Current date and time
        document.getElementById('grandTotal').innerText = grandTotal.toFixed(2);

        // Show the modal
        document.getElementById('eBillModal').style.display = 'block';
    }

    // Close the modal
    function closeModal() {
        document.getElementById('eBillModal').style.display = 'none';
    }

    function checkout() {
        const cartItems = []; // Array to store cart items for sending to the server
        const items = document.querySelectorAll('.cart-item');

        items.forEach(item => {
            const itemNameElement = item.querySelector('.cart-item-title');
            const itemPriceElement = item.querySelector('.cart-item-price');
            const qtySpan = item.querySelector('.qty-controls span');

            if (!itemNameElement) {
                console.error("Item name element is missing.");
            }
            if (!itemPriceElement) {
                console.error("Item price element is missing.");
            }
            if (!qtySpan) {
                console.error("Quantity span element is missing.");
            }

            // Only process if all elements exist
            if (itemNameElement && itemPriceElement && qtySpan) {
                const itemName = itemNameElement.innerText;
                const itemPrice = parseFloat(itemPriceElement.getAttribute('data-price'));
                const qty = parseInt(qtySpan.innerText);
                const total = itemPrice * qty;

                cartItems.push({
                    name: itemName,
                    price: itemPrice,
                    qty: qty,
                    total: total
                });
            }
        });


        const totalAmount = document.getElementById('cartTotal').innerText.replace('LKR', '').trim();

        // Send cart data and total to the server using AJAX
        const xhr = new XMLHttpRequest();
        xhr.open('POST', 'checkout.php', true);
        xhr.setRequestHeader('Content-Type', 'application/json');

        xhr.onload = function() {
            if (xhr.status === 200) {
                alert('Order placed successfully!');
                // You can redirect to a success page or clear the cart here
            } else {
                alert('Error placing order.');
            }
        };

        const data = {
            cartItems: cartItems,
            totalAmount: totalAmount
        };

        xhr.send(JSON.stringify(data));
    }

    function addToCart(button) {
        const foodItem = button.closest('.food-item');
        const itemTitle = foodItem.querySelector('.food-item-title').innerText;
        const itemPrice = parseFloat(foodItem.querySelector('.food-item-price').innerText.replace('LKR', ''));
        const cartContent = document.querySelector('.cart-sidebar-content');

        let existingItem = null;
        const cartItems = document.querySelectorAll('.cart-item');
        cartItems.forEach(item => {
            if (item.querySelector('.cart-item-title').innerText === itemTitle) {
                existingItem = item;
            }
        });

        if (existingItem) {
            const qtySpan = existingItem.querySelector('.qty-controls span');
            let qty = parseInt(qtySpan.innerText);
            qtySpan.innerText = ++qty;
        } else {
            const cartItem = document.createElement('div');
            cartItem.classList.add('cart-item');
            cartItem.innerHTML = `
            <img src="${foodItem.querySelector('img').src}" alt="${itemTitle}">
            <div class="cart-item-details">
                <div class="cart-item-title">${itemTitle}</div>
                <div class="cart-item-price" data-price="${itemPrice}">LKR ${itemPrice.toFixed(2)}</div>
                <div class="cart-item-total-price">LKR ${itemPrice.toFixed(2)}</div>
            </div>
            <div class="qty-controls">
                <button onclick="decreaseQty(this)">-</button>
                <span>1</span>
                <button onclick="increaseQty(this)">+</button>
            </div>
            <div class="cart-item-remove">
                <span class="remove-btn" onclick="removeItem(this)">&times;</span>
            </div>
        `;
            cartContent.appendChild(cartItem);
        }
        updateCartTotal();
    }

    function updateCartNotification(count) {
        const cartNotification = document.getElementById('cartNotification');
        if (count > 0) {
            cartNotification.innerText = count;
            cartNotification.style.display = 'block';
        } else {
            cartNotification.style.display = 'none';
        }
    }


    function showBillModal(cartData, totalAmount, orderId) {
        const modal = document.createElement('div');
        modal.classList.add('bill-modal');

        let billContent = `
            <div class="bill-content">
                <h2>Order Receipt</h2>
                <p>Order ID: ${orderId}</p>
                <p>Date: ${new Date().toLocaleString()}</p>
                <table>
                    <thead>
                        <tr>
                            <th>Item</th>
                            <th>Quantity</th>
                            <th>Price</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody>
        `;

        cartData.forEach(item => {
            billContent += `
                <tr>
                    <td>${item.title}</td>
                    <td>${item.quantity}</td>
                    <td>LKR ${item.price.toFixed(2)}</td>
                    <td>LKR ${item.itemTotal.toFixed(2)}</td>
                </tr>
            `;
        });

        billContent += `
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="3">Total Amount:</td>
                            <td>LKR ${totalAmount.toFixed(2)}</td>
                        </tr>
                    </tfoot>
                </table>
                <button onclick="closeBillModal()">Close</button>
            </div>
        `;

        modal.innerHTML = billContent;
        document.body.appendChild(modal);
    }

    function closeBillModal() {
        const modal = document.querySelector('.bill-modal');
        if (modal) {
            modal.remove();
        }
    }
    </script>

</body>

</html>
<?php
mysqli_close($con);
?>