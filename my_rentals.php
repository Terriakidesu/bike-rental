<?php
session_start();
if (!isset($_SESSION["user_authenticated"]) || $_SESSION["user_authenticated"] !== true) {
    header("Location: login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Rentals</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://kit.fontawesome.com/3d58b793b8.js" crossorigin="anonymous"></script>
</head>

<body class="bg-body-secondary">

    <!-- NAVBAR -->
    <?php require_once "components/navbar.php" ?>

    <div class="container mt-4">
        <h2 class="mb-4">My Active Rentals</h2>

        <div id="rentalsContainer" class="row row-cols-1 row-cols-md-2 g-4">
            <div class="text-center text-muted col-12">Loading rentals...</div>
        </div>

        <div class="position-fixed bottom-0 end-0 p-3" style="z-index: 11">
            <div id="successToast" class="toast align-items-center text-bg-success border-0" role="alert"
                aria-live="assertive" aria-atomic="true">
                <div class="d-flex">
                    <div class="toast-body">Action successful!</div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"
                        aria-label="Close"></button>
                </div>
            </div>

            <div id="errorToast" class="toast align-items-center text-bg-danger border-0" role="alert"
                aria-live="assertive" aria-atomic="true">
                <div class="d-flex">
                    <div class="toast-body" id="errorMessage">Error occurred!</div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"
                        aria-label="Close"></button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="paymentModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Return Bike & Payment</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Amount due: ₱<span id="paymentAmount"></span></p>
                    <p>Do you want to pay and return this bike?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-success" id="confirmPaymentBtn">Pay & Return</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        const rentalsContainer = document.getElementById('rentalsContainer');

        function showToast(id, message = '') {
            const toastEl = document.getElementById(id);
            if (message) toastEl.querySelector('.toast-body').textContent = message;
            const toast = new bootstrap.Toast(toastEl);
            toast.show();
        }

        function loadRentals() {
            fetch('api/v1/rentals/my_rentals.php')
                .then(res => res.json())
                .then(data => {
                    if (!data.success) {
                        rentalsContainer.innerHTML = `<div class="text-center text-danger col-12">${data.message}</div>`;
                        return;
                    }

                    if (data.rentals.length === 0) {
                        rentalsContainer.innerHTML = `<div class="text-center text-muted col-12">You have no active rentals.</div>`;
                        return;
                    }

                    let output = '';
                    data.rentals.forEach(r => {
                        const start = new Date(r.date_rented);
                        const end = new Date(r.date_return);
                        const hours = Math.ceil((end - start) / 1000 / 3600);
                        const totalPrice = hours * r.price_per_hour * r.units_rented;

                        output += `
            <div class="col">
                <div class="card h-100 shadow-sm">
                    <div class="row g-0">
                        <div class="col-md-4">
                            <img src="/rental/${r.image_path}" class="img-fluid rounded-start" alt="${r.name}">
                        </div>
                        <div class="col-md-8">
                            <div class="card-body d-flex flex-column h-100 justify-content-between">
                                <div>
                                    <h5 class="card-title">${r.name}</h5>
                                    <p class="card-text text-truncate" style="max-height: 4.5em;">${r.description}</p>
                                    <p class="mb-1"><strong>Units Rented:</strong> ${r.units_rented}</p>
                                    <p class="mb-1"><strong>Rental Period:</strong> ${start.toLocaleString()} → ${end.toLocaleString()}</p>
                                    <p class="mb-1"><strong>Total Price:</strong> ₱${totalPrice.toFixed(2)}</p>
                                    <p class="mb-1"><strong>Status:</strong> ${r.status.charAt(0).toUpperCase() + r.status.slice(1)}</p>
                                </div>
                                <div>
                                    <button class="btn btn-danger w-100" onclick="returnBike(${r.rental_id})">Return Bike</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>`;
                    });

                    rentalsContainer.innerHTML = output;
                })
                .catch(err => {
                    rentalsContainer.innerHTML = `<div class="text-center text-danger col-12">Failed to load rentals.</div>`;
                    console.error(err);
                });
        }

        let selectedRentalId = null;
        let selectedPaymentAmount = 0;

        function returnBike(rentalId, pricePerHour, units, dateStart, dateEnd) {
            const start = new Date(dateStart);
            const end = new Date(dateEnd);
            const hours = Math.ceil((end - start) / 1000 / 3600);
            const totalPrice = hours * pricePerHour * units;

            selectedRentalId = rentalId;
            selectedPaymentAmount = totalPrice;

            document.getElementById('paymentAmount').textContent = totalPrice.toFixed(2);

            const paymentModal = new bootstrap.Modal(document.getElementById('paymentModal'));
            paymentModal.show();
        }

        document.getElementById('confirmPaymentBtn').addEventListener('click', () => {
            if (!selectedRentalId) return;

            fetch('api/v1/payments/pay.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    rental_id: selectedRentalId,
                    payment: selectedPaymentAmount
                })
            })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        // Mark rental as returned
                        return fetch('api/v1/bikes/return.php', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json' },
                            body: JSON.stringify({ rental_id: selectedRentalId })
                        });
                    } else {
                        throw new Error(data.message || 'Payment failed');
                    }
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        const toastEl = document.getElementById('successToast');
                        const toast = new bootstrap.Toast(toastEl);
                        toast.show();
                        loadRentals();
                    } else {
                        const toastEl = document.getElementById('errorToast');
                        toastEl.querySelector('.toast-body').textContent = data.message;
                        const toast = new bootstrap.Toast(toastEl);
                        toast.show();
                    }
                })
                .catch(err => {
                    const toastEl = document.getElementById('errorToast');
                    toastEl.querySelector('.toast-body').textContent = err.message;
                    const toast = new bootstrap.Toast(toastEl);
                    toast.show();
                });

            // Hide modal
            const modalEl = bootstrap.Modal.getInstance(document.getElementById('paymentModal'));
            modalEl.hide();
        });

        // Load rentals on page load
        window.addEventListener('DOMContentLoaded', loadRentals);
    </script>

</body>

</html>