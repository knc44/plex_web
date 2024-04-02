<?php
exit;
use Plex\Template\Layout\Footer;
use Plex\Template\Layout\Header;
require_once '_config.inc.php';

Layout::Header();

?>


<main role="main" class="container">

    <div class="row">

        <div class="shadow-lg box-shadow col-sm-7 px-5 m-5 bg-light rounded align-self-center verify-message">

            <form action="includes/sendverificationemail.inc.php" method="post">

                <?php echo insert_csrf_token(); ?>

                <h5 class="text-center mb-5 text-primary">Verify Your Email Address</h5>

                <p>
                    Before proceeding, please check your email for a verification link. If you did not receive the email,
                    <button type="submit" name="verifysubmit">click here to send another</button>.
                </p>
                <br>
                <div class="text-center mt-5">
                    <h6 class="text-success">
                        <?php
                            if (isset($_SESSION['STATUS']['verify'])) {
                                echo $_SESSION['STATUS']['verify'];
                            }

?>
                    </h6>
                </div>

            </form>

        </div>
    </div>
</main>


<?php


Layout::Footer();

?>
