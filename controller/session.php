<!-- Session pop-up -->
<div class="session-popup" id="sessionPopup">
    <button class="close-btn" id="closeSessionPopup">&times;</button>
    <div>Logged in as <strong id="sessionEmail"></strong></div>
</div>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const sessionPopup = document.getElementById('sessionPopup');
        const closeBtn = document.getElementById('closeSessionPopup');
        const sessionEmail = document.getElementById('sessionEmail');

        <?php if (isset($_SESSION['user_email'])): ?>
            sessionEmail.textContent = '<?php echo $_SESSION['user_email']; ?>';
            sessionPopup.style.display = 'block';

            localStorage.setItem('sessionPopupVisible', 'true');
        <?php endif; ?>

        closeBtn.addEventListener('click', function() {
            sessionPopup.style.display = 'none';
            localStorage.setItem('sessionPopupVisible', 'false');
        });

        if (localStorage.getItem('sessionPopupVisible') === 'true' && sessionEmail.textContent) {
            sessionPopup.style.display = 'block';
        }
    });
</script>