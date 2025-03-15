document.addEventListener("DOMContentLoaded", function () {
    const hamburger = document.getElementById("hamburger");
    const navMenu = document.getElementById("nav-menu");

    hamburger.addEventListener("click", function () {
        navMenu.classList.toggle("show");
    });

    document.querySelectorAll(".number-filter").forEach(function (input) {
        input.addEventListener("input", function (e) {
            let value = e.target.value.replace(/[^0-9.]/g, ""); // Hanya angka & titik
            let floatValue = parseFloat(value);
            if (!isNaN(floatValue)) {
                e.target.value = new Intl.NumberFormat("en-US", {
                    style: "currency",
                    currency: "USD",
                    minimumFractionDigits: 0,
                }).format(floatValue);
            } else {
                e.target.value = "";
            }
        });
    });
});



