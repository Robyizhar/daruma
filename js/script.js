document.addEventListener("DOMContentLoaded", function () {
    const hamburger = document.getElementById("hamburger");
    const navMenu = document.getElementById("nav-menu");

    if (hamburger) {
        hamburger.addEventListener("click", function () {
            navMenu.classList.toggle("show");
        });
    }

    document.querySelectorAll(".number-filter").forEach(function (input) {
        input.addEventListener("input", function (e) {
            let value = e.target.value.replace(/[^0-9.]/g, ""); // Only digits and dots
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
            e.target.value = e.target.value.replace(/\D/g, "");
            let max = parseInt(this.max, 10);
            let min = parseInt(this.min, 10);
            let value = parseInt(this.value, 10);

            if (value > max) this.value = max;
            if (value < min) this.value = min;
        });
    });

    // ============ NEW: Confirm Password Check ============
    const registrationForm = document.getElementById("registrationForm");
    if (registrationForm) {
        registrationForm.addEventListener("submit", function (e) {
            const password = document.getElementById("password").value.trim();
            const confirmPassword = document.getElementById("confirm_password").value.trim();

            if (password !== confirmPassword) {
                e.preventDefault();

                // If you have SweetAlert2 included (CDN or local), you can do:
                Swal.fire({
                    icon: "warning",
                    title: "Password Mismatch",
                    text: "Your passwords do not match. Please try again."
                });
            }
        });
    }
    // =========== END NEW CODE ===========

});

// Optionally, if you use jQuery for updateCartCount:
function updateCartCount(count) {
    if (count > 0) {
        $("#cart-count").text(count).show();
    } else {
        $("#cart-count").hide();
    }
}
