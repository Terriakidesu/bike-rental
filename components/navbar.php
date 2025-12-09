<nav class="navbar navbar-expand-lg navbar-light bg-light">
    <div class="container px-5">
        <a class="navbar-brand" style="font-size:xx-large;" href="/rental">
            <i class="fas fa-bicycle"></i> Bike Rental
        </a>
        <div class="flex-grow-1"></div>

        <div class="dropdown me-2">
            <button class="btn btn-outline-primary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                <i class="fas fa-user"></i>
                <?= htmlspecialchars($_SESSION["username"]); ?>
            </button>
            <ul class="dropdown-menu dropdown-menu-end">
                <li><a class="dropdown-item" href="profile.php"><i class="fas fa-id-card"></i> Profile</a></li>
                <li><a class="dropdown-item" href="my_rentals.php"><i class="fas fa-biking"></i> My Rentals</a></li>
                <li>
                    <hr class="dropdown-divider">
                </li>
                <li><a class="dropdown-item text-danger" href="logout.php"><i class="fas fa-sign-out-alt"></i>
                        Logout</a></li>
            </ul>
        </div>
    </div>
</nav>