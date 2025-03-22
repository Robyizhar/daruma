<?php 
    include("../inc/design/head.php"); 
    include("../inc/design/header.php"); 
    include("../inc/design/nav.php"); 
    session_start(); 
    include("./sql/db.php");
?>
<div class="container">
    <h1>About Us</h1>
    <p>Learn more about Daruma! and our journey.</p>

    <div class="timeline">
        <!-- Milestone 1 -->
        <div class="timeline-item">
            <div class="timeline-image">
                <img src="../images/milestones/ms1.jpg" alt="Milestone 1" />
            </div>
            <div class="timeline-content">
                <h2>2021</h2>
                <p>Started as a passionate startup, aiming to bring the best mobile technology to customers.</p>
            </div>
        </div>

        <!-- Milestone 2 -->
        <div class="timeline-item">
            <div class="timeline-image">
                <img src="../images/milestones/ms2.jpeg" alt="Milestone 2" />
            </div>
            <div class="timeline-content">
                <h2>2022</h2>
                <p>Launched our e-commerce platform, making it easier for customers to buy premium smartphones.</p>
            </div>
        </div>

        <!-- Milestone 3 -->
        <div class="timeline-item">
            <div class="timeline-image">
                <img src="../images/milestones/ms3.png" alt="Milestone 3" />
            </div>
            <div class="timeline-content">
                <h2>2023</h2>
                <p>Partnered with top smartphone brands to offer exclusive deals and better pricing.</p>
            </div>
        </div>

        <!-- Milestone 4 -->
        <div class="timeline-item">
            <div class="timeline-image">
                <img src="../images/milestones/ms4.jpg" alt="Milestone 4" />
            </div>
            <div class="timeline-content">
                <h2>2024</h2>
                <p>Growing rapidly, serving thousands of happy customers while focusing on quality and service.</p>
            </div>
        </div>
    </div>

    <!-- Contact Us Section -->
    <div class="contact-info" style="margin-top: 40px;">
        <h2>Contact Us</h2>
        <p>Email: <a href="mailto:darumawebsys@gmail.com">darumawebsys@gmail.com</a></p>
        <p>Phone: +65 6453 7777</p>
    </div>

    <!-- FAQ Section -->
    <div class="faq-section" style="margin-top: 40px;">
        <h2>Frequently Asked Questions</h2>
        
        <div class="faq-item">
            <button class="faq-question" style="width: 100%; text-align: left; background: none; border: none; font-size: 1.1em; padding: 10px; cursor: pointer;">
                What is Daruma!? <span class="arrow" style="float: right;">&#9660;</span>
            </button>
            <div class="faq-answer" style="display: none; padding: 0 10px 10px;">
                <p>Daruma! is a leading mobile technology provider focused on offering premium smartphones and exceptional service.</p>
            </div>
        </div>
    
        <div class="faq-item">
            <button class="faq-question" style="width: 100%; text-align: left; background: none; border: none; font-size: 1.1em; padding: 10px; cursor: pointer;">
                How do I place an order? <span class="arrow" style="float: right;">&#9660;</span>
            </button>
            <div class="faq-answer" style="display: none; padding: 0 10px 10px;">
                <p>You can place an order directly on our website by browsing our products and adding them to your cart.</p>
            </div>
        </div>
    
        <div class="faq-item">
            <button class="faq-question" style="width: 100%; text-align: left; background: none; border: none; font-size: 1.1em; padding: 10px; cursor: pointer;">
                What payment methods do you accept? <span class="arrow" style="float: right;">&#9660;</span>
            </button>
            <div class="faq-answer" style="display: none; padding: 0 10px 10px;">
                <p>We only accept credit card payments.</p>
            </div>
        </div>
    </div>
</div>

<script>
    // Toggle FAQ answer visibility on button click
    document.querySelectorAll('.faq-question').forEach(function(button) {
        button.addEventListener('click', function() {
            var answer = this.nextElementSibling;
            if (answer.style.display === "none" || answer.style.display === "") {
                answer.style.display = "block";
                this.querySelector('.arrow').innerHTML = "&#9650;"; // Up arrow
            } else {
                answer.style.display = "none";
                this.querySelector('.arrow').innerHTML = "&#9660;"; // Down arrow
            }
        });
    });
</script>

<?php include("../inc/design/footer.php"); ?>
