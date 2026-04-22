<?php
include 'includes/db.php';
include 'includes/functions.php';

renderHeader($conn, 'Contact Us');
?>

<h2>Contact Us</h2>
<p><strong>Shop Address:</strong> King Fahd Road, Dammam, Saudi Arabia</p>
<p><strong>Phone:</strong> +966-13-000-0000</p>
<p><strong>Email:</strong> support@onlineshop.local</p>

<iframe
    title="Shop location map"
    src="https://maps.google.com/maps?q=Dammam%20Saudi%20Arabia&t=&z=13&ie=UTF8&iwloc=&output=embed"
    width="600"
    height="350"
    style="border:0;"
    loading="lazy"
    referrerpolicy="no-referrer-when-downgrade">
</iframe>

<?php renderFooter(); ?>
