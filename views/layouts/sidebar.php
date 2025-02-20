<nav id="sidebar" class="col-md-3 col-lg-2 d-md-block bg-dark text-white sidebar">
                <div class="position-sticky">
                    <h5 class="sidebar-heading text-light">Admin Tools</h5>
                    <div class="user-info text-center">
                    <img src="<?php echo isset($_SESSION['profile_picture']) ? '/public/uploads/' . $_SESSION['profile_picture'] : 'default-profile.png'; ?>" alt="User Image" class="rounded-circle" style="width: 50px; height: 50px;">
                    
                        <h6 class="text-light"><?php echo $_SESSION['name']; ?></h6>
                    </div>
                    <ul class="nav flex-column">
                    <li class="nav-item">
                            <a class="nav-link active text-white" href="dashboard.php">
                                <i class="fas fa-users"></i> dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link active text-white" href="user-management.php">
                                <i class="fas fa-users"></i> Manage Users
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-white" href="menu_items.php">
                                <i class="fas fa-utensils"></i> Manage items
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-white" href="menu-management.php">
                                <i class="fas fa-utensils"></i> Manage Menu
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-white" href="reservations.php">
                                <i class="fas fa-utensils"></i> reservations
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-white" href="inventory-management.php">
                                <i class="fas fa-boxes"></i> Manage Inventory
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-white" href="reports.php">
                                <i class="fas fa-chart-bar"></i> Sales Reports
                            </a>
                        </li>
                        <li class="nav-item" style="margin-bottom: 0;">
        <button class="btn btn-danger w-100" onclick="window.location.href='logout.php'">
            <i class="fas fa-sign-out-alt"></i> Logout
        </button>
    </li>

                    </ul>
                </div>
                <div>
                    
                </div>
            </nav>