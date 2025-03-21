document.addEventListener("DOMContentLoaded", function () {
    const hamburger = document.getElementById("hamburger");
    const navMenu = document.getElementById("nav-menu");

    hamburger.addEventListener("click", function () {
        navMenu.classList.toggle("show");
    });

    document.querySelectorAll(".number-filter").forEach(function (input) {
        input.addEventListener("input", function (e) {
            let value = e.target.value.replace(/[^0-9.]/g, ""); // Hanya angka dan titik
            let floatValue = parseFloat(value);
            if (!isNaN(floatValue)) {
                e.target.value = new Intl.NumberFormat("en-SG", {
                    style: "currency",
                    currency: "SGD",
                    minimumFractionDigits: 0,
                }).format(floatValue);
            } else {
                e.target.value = "";
            }
        });
    });

    document.querySelectorAll(".number-only").forEach(function (input) {
        input.addEventListener("input", function (e) {
            e.target.value = e.target.value.replace(/\D/g, ""); // Hapus semua karakter selain angka
        });
    });
});

function updateCartCount(count) {
    if (count > 0) {
        $("#cart-count").text(count).show();
    } else {
        $("#cart-count").hide();
    }
}
