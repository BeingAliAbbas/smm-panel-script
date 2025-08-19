<?php

include('app/config.php');


date_default_timezone_set(TIMEZONE);


$mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);


if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_service'])) {

    $uid = $_POST['uid'];
    $cate_id = $_POST['cate_id'];
    $name = $_POST['name'];
    $desc = $_POST['desc'];
    $price = $_POST['price'];
    $original_price = $_POST['original_price'];
    $min = $_POST['min'];
    $max = $_POST['max'];
    $type = $_POST['type'];
    $status = $_POST['status'];

    // Insert new service into the database
    $query = "INSERT INTO services (uid, cate_id, name, `desc`, price, original_price, min, max, type, status, created) 
              VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())";
    if ($stmt = $mysqli->prepare($query)) {
        $stmt->bind_param("iisdiiisii", $uid, $cate_id, $name, $desc, $price, $original_price, $min, $max, $type, $status);
        
        if ($stmt->execute()) {
            echo "<div class='text-green-500'>Service added successfully!</div>";
        } else {
            echo "<div class='text-red-500'>Error: " . $stmt->error . "</div>";
        }
        $stmt->close();
    } else {
        echo "<div class='text-red-500'>Error: " . $mysqli->error . "</div>";
    }
}

// Fetch categories for dropdown
$categories = [];
$category_query = "SELECT * FROM categories WHERE status = 1";  // Assuming there is a categories table
$category_result = $mysqli->query($category_query);

while ($row = $category_result->fetch_assoc()) {
    $categories[] = $row;
}

// Fetch services with add_type 'manual'
$services = [];
$service_query = "SELECT * FROM services WHERE add_type = 'manual'";
$service_result = $mysqli->query($service_query);

while ($row = $service_result->fetch_assoc()) {
    $services[] = $row;
}

$mysqli->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add New Service</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-900 text-white">

    <div class="max-w-2xl mx-auto p-4">
        <h1 class="text-2xl font-semibold text-center text-white mb-4">Add New Service</h1>

        <!-- New Service Form -->
        <form method="POST" class="bg-gray-800 p-4 rounded-lg shadow-lg space-y-4">
            <input type="hidden" name="add_service" value="1">
            
            <!-- UID -->
            <div class="flex flex-col space-y-1">
                <label for="uid" class="text-base">UID</label>
                <input type="number" id="uid" name="uid" class="bg-gray-700 text-white p-2 rounded-md" required>
            </div>

            <!-- Category -->
            <div class="flex flex-col space-y-1">
                <label for="cate_id" class="text-base">Category</label>
                <select id="cate_id" name="cate_id" class="bg-gray-700 text-white p-2 rounded-md" required>
                    <option value="">Select Category</option>
                    <?php foreach ($categories as $category): ?>
                        <option value="<?= $category['id'] ?>"><?= $category['name'] ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Service Name -->
            <div class="flex flex-col space-y-1">
                <label for="name" class="text-base">Service Name</label>
                <input type="text" id="name" name="name" class="bg-gray-700 text-white p-2 rounded-md" required>
            </div>

            <!-- Description -->
            <div class="flex flex-col space-y-1">
                <label for="desc" class="text-base">Description</label>
                <textarea id="desc" name="desc" class="bg-gray-700 text-white p-2 rounded-md" rows="3" required></textarea>
            </div>

            <!-- Price and Original Price -->
            <div class="flex space-x-2">
                <div class="flex flex-col space-y-1 w-full">
                    <label for="price" class="text-base">Price</label>
                    <input type="number" step="0.01" id="price" name="price" class="bg-gray-700 text-white p-2 rounded-md" required>
                </div>
                <div class="flex flex-col space-y-1 w-full">
                    <label for="original_price" class="text-base">Original Price</label>
                    <input type="number" step="0.01" id="original_price" name="original_price" class="bg-gray-700 text-white p-2 rounded-md">
                </div>
            </div>

            <!-- Min and Max Quantity -->
            <div class="flex space-x-2">
                <div class="flex flex-col space-y-1 w-full">
                    <label for="min" class="text-base">Min Quantity</label>
                    <input type="number" id="min" name="min" class="bg-gray-700 text-white p-2 rounded-md" required>
                </div>
                <div class="flex flex-col space-y-1 w-full">
                    <label for="max" class="text-base">Max Quantity</label>
                    <input type="number" id="max" name="max" class="bg-gray-700 text-white p-2 rounded-md" required>
                </div>
            </div>

            <!-- Service Type -->
            <div class="flex flex-col space-y-1">
                <label for="type" class="text-base">Service Type</label>
                <input type="text" id="type" name="type" value="default" class="bg-gray-700 text-white p-2 rounded-md" required>
            </div>

            <!-- Status -->
            <div class="flex flex-col space-y-1">
                <label for="status" class="text-base">Status</label>
                <select id="status" name="status" class="bg-gray-700 text-white p-2 rounded-md" required>
                    <option value="1">Active</option>
                    <option value="0">Inactive</option>
                </select>
            </div>

            <!-- Submit Button -->
            <div class="text-center mt-4">
                <button type="submit" class="bg-blue-600 text-white p-2 rounded-md w-full">Add Service</button>
            </div>
        </form>

        <!-- Display Existing Services -->
        <h2 class="text-xl font-semibold text-center text-white my-4">Existing Services</h2>

        <div class="bg-gray-800 p-4 rounded-lg shadow-lg">
            <table class="min-w-full table-auto">
                <thead class="border-b-2 border-gray-600">
                    <tr>
                        <th class="py-2 px-3 text-left">Service Name</th>
                        <th class="py-2 px-3 text-left">Category</th>
                        <th class="py-2 px-3 text-left">Price</th>
                        <th class="py-2 px-3 text-left">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($services as $service): ?>
                        <tr class="border-b border-gray-600">
                            <td class="py-2 px-3"><?= htmlspecialchars($service['name']) ?></td>
                            <td class="py-2 px-3"><?= htmlspecialchars($service['cate_id']) ?></td>
                            <td class="py-2 px-3"><?= number_format($service['price'], 2) ?></td>
                            <td class="py-2 px-3">
                                <button onclick="openEditModal(<?= $service['id'] ?>, '<?= addslashes($service['name']) ?>', '<?= addslashes($service['desc']) ?>', <?= $service['price'] ?>, <?= $service['min'] ?>, <?= $service['max'] ?>, '<?= addslashes($service['type']) ?>', <?= $service['status'] ?>)" class="bg-blue-600 text-white px-4 py-2 rounded-md text-sm">Edit</button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- Edit Modal -->
        <div id="editModal" class="fixed inset-0 bg-gray-900 bg-opacity-50 hidden justify-center items-center">
            <div class="bg-gray-800 p-6 rounded-lg shadow-lg max-w-lg w-full">
                <h2 class="text-2xl font-semibold text-center text-white mb-6">Edit Service</h2>
                <form id="editForm" method="POST" action="">
                    <input type="hidden" id="edit_service_id" name="edit_service_id">
                    
                    <!-- Edit Form Fields -->
                    <div class="flex flex-col space-y-2">
                        <label for="edit_name" class="text-lg">Service Name</label>
                        <input type="text" id="edit_name" name="edit_name" class="bg-gray-700 text-white p-3 rounded-md" required>
                    </div>

                    <div class="flex flex-col space-y-2">
                        <label for="edit_desc" class="text-lg">Description</label>
                        <textarea id="edit_desc" name="edit_desc" class="bg-gray-700 text-white p-3 rounded-md" rows="4" required></textarea>
                    </div>

                    <div class="flex flex-col space-y-2">
                        <label for="edit_price" class="text-lg">Price</label>
                        <input type="number" step="0.01" id="edit_price" name="edit_price" class="bg-gray-700 text-white p-3 rounded-md" required>
                    </div>

                    <div class="flex flex-col space-y-2">
                        <label for="edit_min" class="text-lg">Min Quantity</label>
                        <input type="number" id="edit_min" name="edit_min" class="bg-gray-700 text-white p-3 rounded-md" required>
                    </div>

                    <div class="flex flex-col space-y-2">
                        <label for="edit_max" class="text-lg">Max Quantity</label>
                        <input type="number" id="edit_max" name="edit_max" class="bg-gray-700 text-white p-3 rounded-md" required>
                    </div>

                    <div class="flex flex-col space-y-2">
                        <label for="edit_type" class="text-lg">Service Type</label>
                        <input type="text" id="edit_type" name="edit_type" value="default" class="bg-gray-700 text-white p-3 rounded-md" required>
                    </div>

                    <div class="flex flex-col space-y-2">
                        <label for="edit_status" class="text-lg">Status</label>
                        <select id="edit_status" name="edit_status" class="bg-gray-700 text-white p-3 rounded-md" required>
                            <option value="1">Active</option>
                            <option value="0">Inactive</option>
                        </select>
                    </div>

                    <div class="text-center mt-6">
                        <button type="submit" class="bg-blue-600 text-white p-3 rounded-md w-full">Save Changes</button>
                    </div>
                </form>

                <button onclick="closeEditModal()" class="absolute top-4 right-4 text-white text-xl">×</button>
            </div>
        </div>

        <script>
            function openEditModal(id, name, desc, price, min, max, type, status) {
                document.getElementById('edit_service_id').value = id;
                document.getElementById('edit_name').value = name;
                document.getElementById('edit_desc').value = desc;
                document.getElementById('edit_price').value = price;
                document.getElementById('edit_min').value = min;
                document.getElementById('edit_max').value = max;
                document.getElementById('edit_type').value = type;
                document.getElementById('edit_status').value = status;
                document.getElementById('editModal').classList.remove('hidden');
            }

            function closeEditModal() {
                document.getElementById('editModal').classList.add('hidden');
            }
        </script>
    </div>
</body>
</html>
