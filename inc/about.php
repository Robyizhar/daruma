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
            <div class="timeline-image">1</div>
            <div class="timeline-content">
                <h2>2021</h2>
                <p>Started as a passionate startup, aiming to bring the best mobile technology to customers.</p>
            </div>
        </div>

        <!-- Milestone 2 -->
        <div class="timeline-item">
            <div class="timeline-image">2</div>
            <div class="timeline-content">
                <h2>2022</h2>
                <p>Launched our e-commerce platform, making it easier for customers to buy premium smartphones.</p>
            </div>
        </div>

        <!-- Milestone 3 -->
        <div class="timeline-item">
            <div class="timeline-image">3</div>
            <div class="timeline-content">
                <h2>2023</h2>
                <p>Partnered with top smartphone brands to offer exclusive deals and better pricing.</p>
            </div>
        </div>

        <!-- Milestone 4 -->
        <div class="timeline-item">
            <div class="timeline-image">4</div>
            <div class="timeline-content">
                <h2>2024</h2>
                <p>Growing rapidly, serving thousands of happy customers while focusing on quality and service.</p>
            </div>
        </div>
    </div>

    <!-- Inspiration Section -->
    <div class="inspiration">
        <h2>"Excellence is not a skill, it's an attitude." – Ralph Marston</h2>
        <p>At Daruma!, we promise to deliver only the highest quality products with exceptional service. Your trust is our priority.</p>
    </div>
</div>

<?php include("../inc/design/footer.php"); ?>