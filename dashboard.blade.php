@extends('layouts.app')

@section('content')
<!-- Sidebar -->
<div class="sidebar">
    <div>
        <div class="logo-area">
            <img src="https://i.pinimg.com/736x/46/9b/93/469b9336e412738858565ccd357a3e96.jpg" alt="WashHour Logo">
            <h3>WashHour Admin</h3>
        </div>

        <ul class="menu">
            <li class="active" data-section="dashboard"><i class="fa-solid fa-chart-line"></i> Dashboard</li>
            <li data-section="orders"><i class="fa-solid fa-basket-shopping"></i> Orders</li>
            <li data-section="customers"><i class="fa-solid fa-users"></i> Customers</li>
            <li data-section="services"><i class="fa-solid fa-screwdriver-wrench"></i> Services</li>
            <li data-section="reports"><i class="fa-solid fa-file-lines"></i> Reports</li>
            <li data-section="logs"><i class="fa-solid fa-file-invoice"></i> System Logs</li>
            <li data-section="settings"><i class="fa-solid fa-gear"></i> Settings</li>
        </ul>
    </div>

    <div class="logout"><i class="fa-solid fa-right-from-bracket"></i> Logout</div>
</div>

<!-- Main Content -->
<div class="main-content">

    <div class="topbar">
        <h1 id="pageTitle">Dashboard Overview</h1>

        <div class="admin-info">
            <span>Admin</span>
            <img src="https://cdn-icons-png.flaticon.com/512/3135/3135715.png" alt="Admin Avatar">
            <input type="text" id="globalSearch" placeholder="Search...">
            <i class="fa-solid fa-moon" id="themeToggle"></i>
        </div>
    </div>

    <!-- Dashboard -->
    <section id="dashboard" class="active">
        <div class="cards">
            <div class="card"><i class="fa-solid fa-basket-shopping"></i><h3>152</h3><p>Active Orders</p></div>
            <div class="card"><i class="fa-solid fa-user"></i><h3>320</h3><p>Registered Customers</p></div>
            <div class="card"><i class="fa-solid fa-peso-sign"></i><h3>₱45,600</h3><p>Today's Income</p></div>
            <div class="card"><i class="fa-solid fa-washing-machine"></i><h3>10 / 12</h3><p>Machines Running</p></div>
        </div>

        <div class="table-container">
            <h2>Recent Laundry Orders</h2>
            <table>
                <thead>
                    <tr><th>Order ID</th><th>Customer</th><th>Service</th><th>Status</th><th>Total</th><th>Action</th></tr>
                </thead>
                <tbody>
                    <tr><td>#L00123</td><td>Maria Santos</td><td>Wash & Fold</td><td>Completed</td><td>₱350</td><td><button class="view-btn">View</button></td></tr>
                    <tr><td>#L00124</td><td>Juan Dela Cruz</td><td>Wash & Dry</td><td>In Progress</td><td>₱280</td><td><button class="view-btn">View</button></td></tr>
                    <tr><td>#L00125</td><td>Ana Lopez</td><td>Dry Clean</td><td>Pending</td><td>₱500</td><td><button class="view-btn">View</button></td></tr>
                </tbody>
            </table>
        </div>
    </section>

    <!-- Orders -->
    <section id="orders">
        <div class="table-container">
            <h2>Manage Orders</h2>
            <table>
                <thead>
                    <tr><th>Order ID</th><th>Customer</th><th>Service</th><th>Status</th><th>Action</th></tr>
                </thead>
                <tbody>
                    <tr><td>#L00128</td><td>Carlos D.</td><td>Wash & Fold</td><td>Pending</td><td><button class="view-btn">View</button></td></tr>
                </tbody>
            </table>
        </div>
    </section>

    <!-- Customers -->
    <section id="customers">
        <div class="table-container">
            <h2>Registered Customers</h2>
            <table>
                <thead>
                    <tr><th>ID</th><th>Name</th><th>Email</th><th>Phone</th></tr>
                </thead>
                <tbody>
                    <tr><td>1</td><td>Maria Santos</td><td>maria@gmail.com</td><td>09123456789</td></tr>
                </tbody>
            </table>
        </div>
    </section>

    <!-- Services -->
    <section id="services">
        <div class="table-container">
            <h2>Available Services</h2>
            <table>
                <thead>
                    <tr><th>Service ID</th><th>Name</th><th>Price</th><th>Action</th></tr>
                </thead>
                <tbody>
                    <tr><td>SVC001</td><td>Wash & Fold</td><td>₱150</td><td><button class="view-btn">Edit</button></td></tr>
                    <tr><td>SVC002</td><td>Wash & Dry</td><td>₱180</td><td><button class="view-btn">Edit</button></td></tr>
                    <tr><td>SVC003</td><td>Dry Clean</td><td>₱250</td><td><button class="view-btn">Edit</button></td></tr>
                </tbody>
            </table>
        </div>
    </section>

    <!-- Reports -->
    <section id="reports">
        <div class="table-container">
            <h2>Sales Reports</h2>
            <canvas id="salesChart" style="width:100%; max-height:350px;"></canvas>
        </div>
    </section>

    <!-- Logs -->
    <section id="logs">
        <div class="table-container">
            <h2>System Logs</h2>

            <ul style="list-style:none; padding:0; line-height:1.8;">
                <li>✔️ Admin logged in — 10:21 AM</li>
                <li>✔️ Updated service price — 10:24 AM</li>
                <li>✔️ Added new customer record — 10:30 AM</li>
                <li>✔️ Generated sales report — 10:45 AM</li>
                <li>⚠️ Failed login attempt — 11:03 AM</li>
            </ul>

        </div>
    </section>

    <!-- Settings -->
    <section id="settings">
        <div class="table-container">
            <h2>System Settings</h2>

            <label>Store Hours:</label><br>
            <input type="text" placeholder="Ex: 8:00 AM - 8:00 PM" style="width:100%; margin-bottom:10px;">

            <label>Default Wash & Fold Price:</label><br>
            <input type="number" placeholder="₱150" style="width:100%; margin-bottom:10px;">

            <label>Admin Display Name:</label><br>
            <input type="text" placeholder="Admin Name" style="width:100%; margin-bottom:10px;">

            <button style="
                padding:10px 16px;
                background:#0057e7;
                border:none;
                color:#fff;
                border-radius:6px;
                cursor:pointer;
                margin-top:10px;
            ">Save Settings</button>

        </div>
    </section>

</div>
@endsection