<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI"
        crossorigin="anonymous"></script>

    <script src="https://kit.fontawesome.com/3d58b793b8.js" crossorigin="anonymous"></script>
</head>

<body class="bg-body-secondary">

    <?php session_start(); ?>

    <!-- NAVBAR -->
    <?php require_once "components/navbar.php" ?>

    <!-- MAIN CONTAINER -->
    <div class="container bg-body p-4 rounded mt-4 shadow-sm">

        <!-- SEARCH -->
        <div class="row mb-3">
            <div class="col-md-12">
                <div class="input-group">
                    <input type="text" id="searchInput" class="form-control" placeholder="Search">
                    <button class="btn btn-primary" id="searchBtn"><i class="fas fa-search"></i></button>
                </div>
            </div>
        </div>

        <!-- SORT AND DIRECTION -->
        <div class="row mb-4 g-2 align-items-center">
            <div class="col-md-6">
                <div class="input-group">
                    <span class="input-group-text">Sort</span>
                    <div class="btn-group" role="group">
                        <input type="radio" class="btn-check" name="sort" id="sort-id" value="id" autocomplete="off"
                            checked>
                        <label class="btn btn-outline-secondary" for="sort-id">ID</label>

                        <input type="radio" class="btn-check" name="sort" id="sort-name" value="name"
                            autocomplete="off">
                        <label class="btn btn-outline-secondary" for="sort-name">Name</label>

                        <input type="radio" class="btn-check" name="sort" id="sort-price" value="price"
                            autocomplete="off">
                        <label class="btn btn-outline-secondary" for="sort-price">Price</label>

                        <input type="radio" class="btn-check" name="sort" id="sort-units" value="units"
                            autocomplete="off">
                        <label class="btn btn-outline-secondary" for="sort-units">Units</label>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="input-group">
                    <span class="input-group-text">Direction</span>
                    <div class="btn-group" role="group">
                        <input type="radio" class="btn-check" name="direction" id="dir-asc" value="asc"
                            autocomplete="off" checked>
                        <label class="btn btn-outline-secondary" for="dir-asc">ASC</label>

                        <input type="radio" class="btn-check" name="direction" id="dir-desc" value="desc"
                            autocomplete="off">
                        <label class="btn btn-outline-secondary" for="dir-desc">DESC</label>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <!-- RESULTS CARDS -->
    <div id="resultsContainer" class="row row-cols-1 row-cols-md-2 g-4 mt-3 px-4 justify-content-center">
        <div class="text-center text-muted col-12">Loading bikes...</div>
    </div>

    <!-- PAGINATION -->
    <nav>
        <ul id="pagination" class="pagination justify-content-center mt-4"></ul>
    </nav>

    <div class="position-fixed bottom-0 end-0 p-3" style="z-index: 11">
        <div id="loginToast" class="toast align-items-center text-bg-warning border-0" role="alert"
            aria-live="assertive" aria-atomic="true">
            <div class="d-flex">
                <div class="toast-body">
                    Sign in first to rent a bike!
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"
                    aria-label="Close"></button>
            </div>
        </div>
    </div>

    <script>
        const searchInput = document.getElementById("searchInput");
        const searchBtn = document.getElementById("searchBtn");
        const resultsContainer = document.getElementById("resultsContainer");
        const pagination = document.getElementById("pagination");

        let currentPage = 1;
        const limit = 6; // bikes per page

        function getSelectedRadio(name) {
            return document.querySelector(`input[name="${name}"]:checked`).value;
        }

        function loadBikes(page = 1) {
            const q = searchInput.value;
            const sort = getSelectedRadio("sort");
            const dir = getSelectedRadio("direction");
            currentPage = page;

            fetch(`api/v1/bikes/search.php?q=${q}&sort=${sort}&dir=${dir}&page=${page}&limit=${limit}`)
                .then(res => res.json())
                .then(data => {
                    let output = '';
                    if (data.bikes && data.bikes.length === 0) {
                        output = `<div class="text-center text-muted col-12">No results found</div>`;
                    } else if (data.bikes) {
                        data.bikes.forEach(row => {
                            output += `
                    <div class="col">
                        <div class="card h-100 shadow-sm">
                            <div class="row g-0">
                                <div class="col-md-4">
                                    <img src="/rental/${row.image_path}" class="img-fluid rounded-start" alt="${row.name}">
                                </div>
                                <div class="col-md-8">
                                    <div class="card-body d-flex flex-column h-100 justify-content-between">
                                        <div>
                                            <h5 class="card-title">${row.name}</h5>
                                            <p class="card-text text-truncate" style="max-height: 4.5em;">${row.description}</p>
                                            <p class="mb-1"><strong>Available Units:</strong> ${row.available_units}</p>
                                            <p class="mb-1"><strong>Price per Hour:</strong> ₱${row.price_per_hour}</p>
                                        </div>
                                        <div>
                                            <button class="btn btn-success w-100" onclick="rentBike(${row.id})">Rent</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>`;
                        });
                    }
                    resultsContainer.innerHTML = output;

                    // Pagination
                    renderPagination(data.totalPages || 1);
                });
        }

        function renderPagination(totalPages) {
            let output = '';
            for (let i = 1; i <= totalPages; i++) {
                output += `<li class="page-item ${i === currentPage ? 'active' : ''}">
            <a class="page-link" href="#" onclick="loadBikes(${i}); return false;">${i}</a>
        </li>`;
            }
            pagination.innerHTML = output;
        }

        function rentBike(bikeId) {
            <?php if (isset($_SESSION["user_authenticated"]) && $_SESSION["user_authenticated"] === true): ?>
                // Logged in → redirect to rent page
                window.location.href = `rent.php?bike_id=${bikeId}`;
            <?php else: ?>
                // Not logged in → show toast
                const toastEl = document.getElementById('loginToast');
                const toast = new bootstrap.Toast(toastEl);
                toast.show();
            <?php endif; ?>
        }

        // Event listeners
        searchBtn.addEventListener("click", () => loadBikes(1));
        searchInput.addEventListener("keyup", e => { if (e.key === "Enter") loadBikes(1); });
        document.querySelectorAll('input[name="sort"]').forEach(r => r.addEventListener("change", () => loadBikes(1)));
        document.querySelectorAll('input[name="direction"]').forEach(r => r.addEventListener("change", () => loadBikes(1)));

        // Load bikes on page load
        window.addEventListener("DOMContentLoaded", () => loadBikes(1));
    </script>

</body>

</html>