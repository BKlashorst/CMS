<?php
session_start();
include 'bk-admin-2/includes/config.php';
include 'bk-admin-2/includes/database.php';
include 'bk-admin-2/includes/header.php';

$db = new Database();
$slug = $_GET['page_slug'] ?? 'home';
$pslug = $_GET['post_slug'] ?? 'home';

// Check if it's a post or a page
$post = $db->query(
    "SELECT * FROM post WHERE post_slug = ? AND post_status = 1",
    [$pslug]
)->fetch(PDO::FETCH_ASSOC);

if ($post) {
    // It's a post, get post contents
    $contents = $db->query(
        "SELECT c.*, pc.post_id 
         FROM content c 
         JOIN postcontent pc ON c.cont_id = pc.cont_id 
         WHERE pc.post_id = ? 
         ORDER BY c.cont_order",
        [$post['post_id']]
    )->fetchAll(PDO::FETCH_ASSOC);
} else {
    // It's a page, get page content
    $page = $db->query(
        "SELECT * FROM page WHERE page_slug = ? AND page_status = 1",
        [$slug]
    )->fetch(PDO::FETCH_ASSOC);

    if (!$page) {
        // Page not found
        header("HTTP/1.0 404 Not Found");
        echo "<h1>404 - Page Not Found</h1>";
        echo "<p>The page you are looking for does not exist.</p>";
        exit;
    }

    // Get page contents
    $contents = $db->getPageContent($page['page_id']);
}
?>

<main>
    <?php foreach ($contents as $content): ?>
        <section>
            <div class="content-block <?php echo $content['cont_block']; ?>-block">
                <?php if ($content['cont_block'] === 'hero1'): ?>
                    <div class="hero-section">
                        <div class="hero1-dl-wrap">
                            <?php echo $content['cont_content']; ?>
                        </div>
                        <div class="hero1-dr-wrap">
                            <div class="hero1-d-r-img-wrap">
                                <img src="<?php echo htmlspecialchars(json_decode($content['cont_settings'], true)['bg_image'] ?? ''); ?>" alt="Hero Image">
                                
                            </div>
                        </div>
                    </div>
                <?php elseif ($content['cont_block'] === 'text'): ?>
                    <div class="text-content">
                        <?php echo $content['cont_content']; ?>
                    </div>
                <?php elseif($content['cont_block'] === 'text-img'): ?>
                    <?php 
                    $settings = json_decode($content['cont_settings'], true) ?? [];
                    $imageLeft = isset($settings['image_left']) ? $settings['image_left'] : false;
                    $imageLeftClass = $imageLeft ? ' image-left' : '';
                    ?>
                    <div class="text-img-section<?php echo $imageLeftClass; ?>">
                        <div class="dl-t-img-wrap">
                            <?php echo $content['cont_content']; ?>
                        </div>
                        <div class="dr-t-img-wrap">
                            <img src="<?php echo htmlspecialchars($settings['bg_image'] ?? ''); ?>" alt="Image">
                        </div>
                    </div>
                <?php elseif($content['cont_block'] === 'image'): ?>
                    <div class="image-section">
                        <img class="image-section-img" src="<?php echo htmlspecialchars(json_decode($content['cont_settings'], true)['bg_image'] ?? ''); ?>" alt="Image">
                    </div>
                <?php elseif ($content['cont_block'] === 'ovz-klein'): ?>
                    <?php
                    $settings = json_decode($content['cont_settings'], true) ?? [];
                    $selected_posts = $settings['selected_posts'] ?? [];
                    $show_portfolio_button = isset($settings['show_portfolio_button']) ? $settings['show_portfolio_button'] : false;
                    ?>
                    <div class="overview-container">
                        <?php if (!empty($content['cont_content'])): ?>
                            <div class="ovz-tekst-btn-wrap">
                                <div class="overview-text">
                                    <?php echo $content['cont_content']; ?>
                                </div>
                                <?php if ($show_portfolio_button): ?>
                                    <div class="overview-button">
                                        <a href="page.php?page_slug=portfolio" class="sec-cta">Bekijk portfolio</a>
                                    </div>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>

                        <?php if (!empty($selected_posts)) {
                            $posts = $db->query(
                                "SELECT post_id, post_name, post_slug, post_image 
                                 FROM post 
                                 WHERE post_id IN (" . implode(',', array_map('intval', $selected_posts)) . ")
                                 AND post_status = 1"
                            )->fetchAll(PDO::FETCH_ASSOC);
                        ?>
                            <div class="row">
                                <?php foreach ($posts as $post): ?>
                                    <div class="col-md-4">
                                        <div class="overview-item">
                                            <a href="page.php?slug=<?php echo htmlspecialchars($post['post_slug']); ?>" class="overview-link">
                                                <div class="overview-image">
                                                    <img src="<?php echo htmlspecialchars($post['post_image'] ?? ''); ?>" alt="<?php echo htmlspecialchars($post['post_name']); ?>" class="img-fluid">
                                                </div>
                                            </a>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php } ?>
                    </div>
                <?php elseif ($content['cont_block'] === 'ovz-groot'): ?>
                    <?php
                    // Fetch all published posts from the database
                    $query = "SELECT post_id, post_name, post_slug, post_image FROM Post WHERE post_status = 1 ORDER BY post_name";
                    $posts = $db->query($query)->fetchAll(PDO::FETCH_ASSOC);
                    ?>
                    <div class="overview-container">
                        <?php if (!empty($posts)):
                            // Display posts in the grid
                        ?>
                            <div class="row">
                                <?php foreach ($posts as $post): ?>
                                    <div class="col-md-4">
                                        <div class="overview-item">
                                            <a href="page.php?post_slug=<?php echo htmlspecialchars($post['post_slug']); ?>" class="overview-link">
                                                <div class="overview-image">
                                                    <img src="<?php echo htmlspecialchars($post['post_image'] ?? ''); ?>" alt="<?php echo htmlspecialchars($post['post_name']); ?>" class="img-fluid">
                                                </div>
                                            </a>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php elseif ($content['cont_block'] === 'contact'): ?>
                    <?php
                    $settings = json_decode($content['cont_settings'], true) ?? [];
                    $block_content = $content['cont_content'] ?? '';
                    
                    // Generate CSRF token if it doesn't exist
                    if (empty($_SESSION['csrf_token'])) {
                        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
                    }
                    ?>
                    <div class="contact-section">
                        <div class="contact-left">
                            <?php if (!empty($settings['bg_image'])): ?>
                                <div class="contact-image">
                                    <img src="<?php echo htmlspecialchars($settings['bg_image']); ?>" alt="Contact Image">
                                </div>
                            <?php endif; ?>
                            <div class="contact-text">
                                <?php echo $block_content; ?>
                            </div>
                        </div>
                        <div class="contact-right">
                            <h2>Contact opnemen</h2>
                            <?php if (isset($_GET['status'])): ?>
                                <?php if ($_GET['status'] === 'success'): ?>
                                    <div class="alert alert-success">
                                        Bedankt voor je bericht! We nemen zo snel mogelijk contact met je op.
                                    </div>
                                <?php elseif ($_GET['status'] === 'error'): ?>
                                    <div class="alert alert-danger">
                                        Er is een fout opgetreden bij het versturen van je bericht. Probeer het later opnieuw.
                                    </div>
                                <?php endif; ?>
                            <?php endif; ?>
                            <form action="contact.php" method="POST" class="contact-form">
                                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                                <input type="hidden" name="recipient_email" value="<?php echo htmlspecialchars($settings['recipient_email'] ?? ''); ?>">
                                
                                <div class="form-group">
                                    <input type="text" id="name" name="name" class="form-control" required placeholder="Naam">
                                </div>
                                
                                <div class="form-group">
                                    <input type="email" id="email" name="email" class="form-control" required placeholder="Email">
                                </div>
                                
                                <div class="form-group">
                                    <input type="tel" id="phone" name="phone" class="form-control" placeholder="Telefoonnummer">
                                </div>
                                
                                <div class="form-group">
                                    <textarea id="message" name="message" class="form-control" rows="5" required placeholder="Bericht"></textarea>
                                </div>
                                
                                <button type="submit" class="btn btn-secondary">Versturen</button>
                            </form>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </section>
    <?php endforeach; ?>
</main>

<?php include 'bk-admin-2/includes/footer.php'; ?>