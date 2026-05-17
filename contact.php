<?php
include 'includes/db.php';
include 'includes/functions.php';

renderHeader($conn, 'Contact Us');
?>

<div class="centered-page">
    <iframe title="Shop location map"
        src="https://maps.google.com/maps?q=Dammam%20Saudi%20Arabia&t=&z=13&ie=UTF8&iwloc=&output=embed" width="600"
        height="350" style="border:0;" loading="lazy" referrerpolicy="no-referrer-when-downgrade">
    </iframe>

    <h2>Contact Us</h2>
    <p><strong>Shop Address:</strong> King Fahd Road, Dammam, Saudi Arabia</p>
    <p><strong>Phone:</strong> +966-13-000-0000</p>
    <p><strong>Email:</strong> support@onlineshop.local</p>
    <h3>Ask us</h3>
    <form action="post">
        <label for="customerName">Name:</label>
        <input id="customerName" type="text">
        <label for="customerEmail">Email:</label>
        <input id="customerEmail" type="email">

        <label for="customerMessage">Your Question:</label>
        <textarea name="customerMessage" id="customerMessage" rows="10" cols="50"></textarea>
    </form>

    <?php renderFooter(); ?>