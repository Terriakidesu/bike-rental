<?php
session_start();
if (!isset($_SESSION["user_authenticated"]) || $_SESSION["user_authenticated"] !== true) {
    header("Location: login.php");
    exit;
}

$bike_id = isset($_GET['bike_id']) ? intval($_GET['bike_id']) : 0;
if ($bike_id <= 0)
    die("Invalid bike ID.");
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rent Bike</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
</head>

<body class="bg-body-secondary">

    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-10">
                <div class="card shadow-sm">
                    <div class="row g-0">
                        <!-- Bike Image -->
                        <div class="col-md-5" id="bikeImageContainer">
                            <img id="bikeImage" src="" alt="Bike Image" class="img-fluid rounded-start">
                        </div>

                        <!-- Bike Info and Rent Form -->
                        <div class="col-md-7">
                            <div class="card-body d-flex flex-column h-100">
                                <h3 id="bikeName">Loading...</h3>
                                <p id="bikeDescription" class="text-muted"></p>
                                <p><strong>Available Units:</strong> <span id="bikeUnits"></span></p>
                                <p><strong>Price per Hour:</strong> ₱<span id="bikePrice"></span></p>

                                <form id="rentForm" class="mt-auto">
                                    <input type="hidden" name="bike_id" value="<?= $bike_id ?>">
                                    <input type="hidden" name="customer_id" value="<?= $_SESSION['user_id'] ?>">

                                    <div class="mb-3">
                                        <label class="form-label">Units to Rent</label>
                                        <input type="number" name="units_rented" id="unitsInput" class="form-control"
                                            min="1" required>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Start Date & Time</label>
                                        <input type="datetime-local" name="date_rented" id="dateRented"
                                            class="form-control" required>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Return Date & Time</label>
                                        <input type="datetime-local" name="date_return" id="returnTime"
                                            class="form-control" disabled required>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Total Price</label>
                                        <input type="text" id="totalPrice" class="form-control" readonly>
                                    </div>

                                    <button type="submit" class="btn btn-success w-100">Rent Now</button>
                                    <a href="index.php" class="btn btn-secondary w-100 mt-2">Cancel</a>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Toast Container -->
    <div class="position-fixed bottom-0 end-0 p-3" style="z-index: 11">
        <div id="rentToast" class="toast align-items-center text-bg-success border-0" role="alert" aria-live="assertive"
            aria-atomic="true">
            <div class="d-flex">
                <div class="toast-body" id="toastBody"></div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"
                    aria-label="Close"></button>
            </div>
        </div>
    </div>

    <script>
        const bikeId = <?= $bike_id ?>;
        const bikeImage = document.getElementById('bikeImage');
        const bikeNameEl = document.getElementById('bikeName');
        const bikeDescription = document.getElementById('bikeDescription');
        const bikeUnits = document.getElementById('bikeUnits');
        const bikePriceEl = document.getElementById('bikePrice');
        const unitsInput = document.getElementById('unitsInput');
        const dateRented = document.getElementById('dateRented');
        const returnTime = document.getElementById('returnTime');
        const totalPrice = document.getElementById('totalPrice');
        const rentToastEl = document.getElementById('rentToast');
        const toastBody = document.getElementById('toastBody');
        const toast = new bootstrap.Toast(rentToastEl);

        // Fetch bike info (single object)
        fetch(`api/v1/bikes?id=${bikeId}`)
            .then(res => res.json())
            .then(bike => {
                if (!bike || !bike.id) {
                    bikeNameEl.textContent = "Bike not found";
                    unitsInput.disabled = true;
                    return;
                }

                bikeNameEl.textContent = bike.name;
                bikeDescription.textContent = bike.description;
                bikeUnits.textContent = bike.available_units;
                bikePriceEl.textContent = bike.price_per_hour;
                bikeImage.src = `/rental/${bike.image_path}`;
                bikeImage.alt = bike.name;
                unitsInput.max = bike.available_units;

                // Set minimum start date to now (ignore seconds)
                const now = new Date();
                now.setSeconds(0, 0);
                const isoNow = now.toISOString().slice(0, 16);
                dateRented.min = isoNow;
                dateRented.value = isoNow;

                // Function to enable and update returnTime
                function updateReturnTime() {
                    const start = new Date(dateRented.value);
                    if (!start) return;

                    returnTime.disabled = false;

                    start.setSeconds(0, 0);
                    start.setMinutes(Math.floor(start.getMinutes() / 5) * 5);
                    const isoStart = start.toISOString().slice(0, 16);
                    returnTime.min = isoStart;

                    const currentReturn = new Date(returnTime.value);
                    if (!returnTime.value || currentReturn < start) {
                        const newReturn = new Date(start);
                        newReturn.setDate(newReturn.getDate() + 1);
                        returnTime.value = newReturn.toISOString().slice(0, 16);
                    }

                    updatePrice();
                }

                // Event listeners
                dateRented.addEventListener('change', updateReturnTime);
                returnTime.addEventListener('change', () => {
                    const start = new Date(dateRented.value);
                    let end = new Date(returnTime.value);
                    if (end <= start) {
                        const newReturn = new Date(start);
                        newReturn.setDate(newReturn.getDate() + 1);
                        returnTime.value = newReturn.toISOString().slice(0, 16);
                    }
                    updatePrice();
                });
                unitsInput.addEventListener('input', updatePrice);

                // Trigger once on load
                if (dateRented.value) {
                    updateReturnTime();
                }

                // Price calculation
                function updatePrice() {
                    const units = parseInt(unitsInput.value) || 0;
                    const start = new Date(dateRented.value);
                    const end = new Date(returnTime.value);
                    if (!start || !end || end <= start) return totalPrice.value = "";
                    const hours = Math.ceil((end - start) / (1000 * 60 * 60));
                    const price = units * bike.price_per_hour * hours;
                    totalPrice.value = `₱${price}`;
                }
            });

        // Rent form submission
        document.getElementById('rentForm').addEventListener('submit', function (e) {
            e.preventDefault();
            const formData = new FormData(this);

            const start = new Date(dateRented.value);
            const end = new Date(returnTime.value);
            const rentDuration = Math.ceil((end - start) / (1000 * 60 * 60));
            formData.append('rent_duration', rentDuration);

            fetch('api/v1/bikes/rent.php', { method: 'POST', body: formData })
                .then(res => res.json())
                .then(data => {
                    toastBody.textContent = data.message;
                    if (data.success) {
                        rentToastEl.classList.remove('text-bg-danger');
                        rentToastEl.classList.add('text-bg-success');
                        toast.show();
                        setTimeout(() => window.location.href = 'index.php', 1500);
                    } else {
                        rentToastEl.classList.remove('text-bg-success');
                        rentToastEl.classList.add('text-bg-danger');
                        toast.show();
                    }
                })
                .catch(err => {
                    toastBody.textContent = "Something went wrong!";
                    rentToastEl.classList.remove('text-bg-success');
                    rentToastEl.classList.add('text-bg-danger');
                    toast.show();
                    console.error(err);
                });
        });
    </script>

</body>

</html>