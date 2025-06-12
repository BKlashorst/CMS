<?php
// Prevent multiple inclusion
if (!defined('FOOTER_INCLUDED')) {
    define('FOOTER_INCLUDED', true);
?>
    <footer>
        <div class="foot-top">
            <img class="foot-logo" src="bk-admin-2/uploads/1744200050_bk-logo.png" alt="">
            <div class="foot-top-socials">
                <a href=""></a>
                <a href=""></a>
            </div>
        </div>
        <hr>
        <div class="foot-bottom">
            <div class="foot-bottom-content">
                <div class="foot-bottom-links"> 
                    <div class="foot-bottom-links-1">
                    <a href="page.php?slug=portfolio" class="header-item">Portfolio</a>
                    <a href="page.php?slug=over-mij" class="header-item">Over mij</a>
                    <a href="page.php?slug=contact" class="header-item">Contact</a>
                    </div>
                    <div class="foot-bottom-links-2">
                        <a href="page.php?slug=privacyverklaring" class="header-item">Privacyverklaring</a>
                        <a href="page.php?slug=algemene-voorwaarden" class="header-item">Algemene voorwaarden</a>
                        <a href="page.php?slug=cookieverklaring" class="header-item">Cookieverklaring</a>
                    </div>
                </div>
                <div class="foot-bottom-text">
                    <p>&copy; <?php echo date('Y'); ?> Bram van de Klashorst | Alle rechten voorbehouden</p>
                </div>
            </div>
            <div class="foot-bottom-logo">
                <img src="bk-admin-2/uploads/footer-img.png" alt="">
            </div>
        </div>
    </footer>
</body>
</html>
<?php
}
?> 