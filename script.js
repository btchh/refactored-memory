const menuItems = document.querySelectorAll(".menu li");
const sections = document.querySelectorAll("section");
const pageTitle = document.getElementById("pageTitle");

menuItems.forEach(item => {
    item.addEventListener("click", () => {
        menuItems.forEach(i => i.classList.remove("active"));
        item.classList.add("active");

        sections.forEach(sec => sec.classList.remove("active"));
        const sectionToShow = document.getElementById(item.getAttribute("data-section"));
        if (sectionToShow) sectionToShow.classList.add("active");

        pageTitle.textContent = item.textContent.trim();
    });
});

// Theme Toggle
const themeToggle = document.getElementById("themeToggle");

themeToggle.addEventListener("click", () => {
    if (document.body.getAttribute("data-theme") === "dark") {
        document.body.setAttribute("data-theme", "light");
        themeToggle.className = "fa-solid fa-moon";
    } else {
        document.body.setAttribute("data-theme", "dark");
        themeToggle.className = "fa-solid fa-sun";
    }
});

// Global Search
const globalSearch = document.getElementById("globalSearch");

globalSearch.addEventListener("keyup", () => {
    const filter = globalSearch.value.toLowerCase();

    document.querySelectorAll("table tbody").forEach(tbody => {
        tbody.querySelectorAll("tr").forEach(row => {
            row.style.display = row.textContent.toLowerCase().includes(filter) ? "" : "none";
        });
    });

    menuItems.forEach(item => {
        item.style.background = item.textContent.toLowerCase().includes(filter) && filter !== "" 
            ? "rgba(30,144,255,0.6)" 
            : "";
    });
});

// View button
document.querySelectorAll(".view-btn").forEach(btn => {
    btn.addEventListener("click", e => {
        const row = e.target.closest("tr");
        alert(Viewing details for ${row.cells[1].textContent});
    });
});

/* SALES CHART (AUG–DEC ONLY) */
const ctx = document.getElementById('salesChart');

if (ctx) {
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: ['Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
            datasets: [{
                label: 'Monthly Sales (₱)',
                data: [25000, 28000, 32000, 34000, 36000],
                borderWidth: 3,
                borderColor: '#00aaff',
                backgroundColor: 'rgba(0, 170, 255, 0.3)',
                tension: 0.4,
                fill: true
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false
        }
    });
}