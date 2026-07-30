/**
 * WearVault - Non-Invasive XAMPP MySQL Backend Synchronizer
 * Automatically bridges existing HTML/JS pages to the PHP API backend without modifying original code.
 */
(function() {
    console.log("⚡ WearVault XAMPP MySQL Backend Synchronizer active");

    const API_BASE = 'api/';

    // 1. Sync User Registration (signup.html)
    const signupForm = document.getElementById('signupForm');
    if (signupForm) {
        signupForm.addEventListener('submit', function(e) {
            const fname = document.getElementById('fname')?.value.trim();
            const lname = document.getElementById('lname')?.value.trim();
            const email = document.getElementById('email')?.value.trim();
            const password = document.getElementById('password')?.value.trim();
            const phone = document.getElementById('phone')?.value.trim();
            const age = document.getElementById('age')?.value.trim();
            const notify = document.querySelector('input[name="notify"]:checked')?.value || 'email';

            if (fname && lname && email && password && phone && age) {
                fetch(API_BASE + 'signup.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ fname, lname, email, password, phone, age, notify })
                })
                .then(r => r.json())
                .then(res => console.log('MySQL Signup Sync:', res))
                .catch(err => console.error('MySQL Signup Sync Error:', err));
            }
        });
    }

    // 2. Sync User Login (login.html)
    const loginForm = document.getElementById('loginForm');
    if (loginForm) {
        loginForm.addEventListener('submit', function(e) {
            const email = document.getElementById('email')?.value.trim();
            const password = document.getElementById('password')?.value.trim();

            if (email && password) {
                fetch(API_BASE + 'login.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ email, password })
                })
                .then(r => r.json())
                .then(res => console.log('MySQL User Login Sync:', res))
                .catch(err => console.error('MySQL User Login Sync Error:', err));
            }
        });
    }

    // 3. Sync Admin Registration (adsignup.html)
    const adsignupForm = document.getElementById('adsignupForm');
    if (adsignupForm) {
        adsignupForm.addEventListener('submit', function(e) {
            const fname = document.getElementById('adminFname')?.value.trim() || document.getElementById('fname')?.value.trim();
            const lname = document.getElementById('adminLname')?.value.trim() || document.getElementById('lname')?.value.trim();
            const email = document.getElementById('adminEmail')?.value.trim() || document.getElementById('email')?.value.trim();
            const password = document.getElementById('adminPassword')?.value.trim() || document.getElementById('password')?.value.trim();

            if (fname && lname && email && password) {
                fetch(API_BASE + 'adsignup.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ fname, lname, email, password })
                })
                .then(r => r.json())
                .then(res => console.log('MySQL Admin Signup Sync:', res))
                .catch(err => console.error('MySQL Admin Signup Sync Error:', err));
            }
        });
    }

    // 4. Sync Admin Login (adin.html)
    const adminLoginForm = document.getElementById('adminLoginForm');
    if (adminLoginForm) {
        adminLoginForm.addEventListener('submit', function(e) {
            const email = document.getElementById('adminEmail')?.value.trim();
            const password = document.getElementById('adminPassword')?.value.trim();

            if (email && password) {
                fetch(API_BASE + 'adin.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ email, password })
                })
                .then(r => r.json())
                .then(res => console.log('MySQL Admin Login Sync:', res))
                .catch(err => console.error('MySQL Admin Login Sync Error:', err));
            }
        });
    }

    // 5. Sync Products and Accounts Registry from MySQL on Load
    window.addEventListener('DOMContentLoaded', function() {
        // Fetch registered users to populate localStorage if missing
        fetch(API_BASE + 'get_users.php')
            .then(r => r.json())
            .then(res => {
                if (res.status === 'success' && Array.isArray(res.data)) {
                    localStorage.setItem('wearvault_mysql_users', JSON.stringify(res.data));
                }
            }).catch(() => {});

        // Fetch products to populate localStorage if missing
        fetch(API_BASE + 'products.php')
            .then(r => r.json())
            .then(res => {
                if (res.status === 'success' && Array.isArray(res.data)) {
                    localStorage.setItem('wearvault_mysql_products', JSON.stringify(res.data));
                }
            }).catch(() => {});
    });
})();
