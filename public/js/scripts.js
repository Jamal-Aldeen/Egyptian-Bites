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
        this.innerHTML = '<i class="fas fa-check"></i> Added!';
        this.style.backgroundColor = "#28a745";
        setTimeout(() => {
            this.innerHTML = '<i class="fas fa-shopping-cart "></i>  Add to Cart';
            this.style.backgroundColor = "";
        }, 2000);
    });
});
document.querySelectorAll(".increase-qty").forEach(btn => {
    btn.addEventListener("click", function () {
        let input = this.previousElementSibling;
        input.value = parseInt(input.value) + 1;
    });
});

document.querySelectorAll(".decrease-qty").forEach(btn => {
    btn.addEventListener("click", function () {
        let input = this.nextElementSibling;
        if (input.value > 1) {
            input.value = parseInt(input.value) - 1;
        }
    });
});

document.getElementById("searchInput").addEventListener("keyup", function () {
    let filter = this.value.toLowerCase();
    document.querySelectorAll(".menu-item").forEach(item => {
        let title = item.querySelector(".card-title").innerText.toLowerCase();
        item.style.display = title.includes(filter) ? "block" : "none";
    });
});
document.addEventListener("DOMContentLoaded", function () {
    const filterButtons = document.querySelectorAll(".filter-btn");

    filterButtons.forEach(button => {
        button.addEventListener("click", function () {
            // Remove 'active' class from all buttons
            filterButtons.forEach(btn => btn.classList.remove("active"));

            // Add 'active' class to the clicked button
            this.classList.add("active");
        });
    });
});


