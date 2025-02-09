document.addEventListener("DOMContentLoaded", function () {
    const stars = document.querySelectorAll(".star");

    stars.forEach(star => {
        star.addEventListener("click", function () {
            const value = this.getAttribute("data-value");
            updateStars(value);
            console.log(`Rated: ${value} stars`);
        });
    });

    function updateStars(value) {
        stars.forEach(star => {
            if (star.getAttribute("data-value") <= value) {
                star.style.color = "#FFD700";
            } else {
                star.style.color = "#ddd";
            }
        });
    }
});
document.addEventListener("DOMContentLoaded", function () {
    const searchInput = document.getElementById("searchInput");
    const menuItems = document.querySelectorAll(".card");

    searchInput.addEventListener("keyup", function () {
        const filter = searchInput.value.toLowerCase();
        menuItems.forEach(item => {
            const title = item.querySelector(".card-title").innerText.toLowerCase();
            item.style.display = title.includes(filter) ? "block" : "none";
        });
    });
});

document.addEventListener("DOMContentLoaded", function () {
    const searchInput = document.getElementById("searchInput");
    const menuItems = document.querySelectorAll(".card");

    searchInput.addEventListener("keyup", function () {
        const filter = searchInput.value.toLowerCase();
        menuItems.forEach(item => {
            const title = item.querySelector(".card-title").innerText.toLowerCase();
            item.style.display = title.includes(filter) ? "block" : "none";
        });
    });
});
document.querySelectorAll(".btn-add-cart").forEach(button => {
    button.addEventListener("click", function () {
        this.innerText = "Added!";
        this.style.backgroundColor = "#28a745";
        setTimeout(() => {
            this.innerText = "Add to Cart";
            this.style.backgroundColor = "";
        }, 2000);
    });
});

document.getElementById("searchInput").addEventListener("keyup", function () {
    let filter = this.value.toLowerCase();
    document.querySelectorAll(".menu-item").forEach(item => {
        let title = item.querySelector(".card-title").innerText.toLowerCase();
        item.style.display = title.includes(filter) ? "block" : "none";
    });
});

document.querySelectorAll(".filter-btn").forEach(btn => {
    btn.addEventListener("click", function () {
        let category = this.getAttribute("data-filter");
        document.querySelectorAll(".menu-item").forEach(item => {
            item.style.display = (category === "all" || item.getAttribute("data-category") === category) ? "block" : "none";
        });
    });
});
