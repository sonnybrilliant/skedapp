<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8" />
        <title>Schedule your life with SkedApp</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="Schedule appointments with lawyers, hairdressers, health spas and more" />
        <meta name="author" content="Creative Cloud IT Services" />

        <style>
            * {
                color: #ffffff;
                font-family: Arial;
                font-size: 22px;
            }

            body {
                background-color: #ffffff;
            }

            .divLogo {
                width: auto;
                margin: auto;
                margin-top: 42px;
                text-align: center;
            }

            .divUCBody {
                background-image: url(img/uc-page/uc-bg.jpg);
                background-repeat: no-repeat;
                background-position: top center;
                width: 760px;
                height: 471px;
                margin: auto;
                margin-top: 38px;
                padding-top: 80px;
            }

            .divContentText {
                text-align: center;
            }

            .divContentText h4 {
                margin: 8px;
                padding: 0px;
                font-size: 28px;
            }

            .divSubmitForm {
                padding-top: 12px;
            }

            .inpEMail {
                text-align: center;
                color: #323232;
                font-size: 14px;
                width: 60%;
                padding: 8px;
            }

        </style>
        <?php

        $strValue = 'your e-mail address';

        if ( (isset ($_POST['email'])) && (strlen($_POST['email']) > 0) && ($_POST['email'] != 'your e-mail address') ) {

          $strValue = $_POST['email'];

          $resFile = fopen('uploads/emails.csv', 'a+');
          fwrite($resFile, $strValue . "\r\n");
          fclose($resFile);

        }
        ?>

    </head>
    <body>

        <div class="divLogo">
            <img src="img/uc-page/logo.jpg" border="0" alt="SkedApp - Schedule your life" />
        </div>

        <div class="divUCBody">
            <div style="clear: both;"></div>
            <div class="divContentText">
                    <?php
                    if ( (isset ($_POST['email'])) && (strlen($_POST['email']) > 0) && ($_POST['email'] != 'your e-mail address') ) {
                        ?>
                        <p>
                            <h4>Thank you very much for your e-mail address. We will be in contact soon.</h4>
                        </p>
                        <?php
                    } else {
                        ?>
                        <p>
                            The SkedApp Website is currently under construction.
                            <br />
                            We've recently reached the beta testing phase and approved the design.
                            <br />
                            We will be live soon!
                            <br />
                            <h4>BUT</h4>
                            Don't leave yet, give us your e-mail address and you will receive PREFERENTIAL access to our website!
                        </p><br />
                        <div class="divSubmitForm">
                        <form action="index.php" method="post">
                            <input type="text" class="inpEMail" value="<?php echo $strValue; ?>" onfocus="if (this.value == 'your e-mail address') { this.value = ''; }"
                                   onblur="if (this.value == '') { this.value = 'your e-mail address'; }" name="email" />
                            <br /><br />
                            <input type="image" src="img/uc-page/submit.jpg" border="0" />
                        </form>
                        <?php
                    }
                    ?>
                </div>
            </div>
        </div>

        <script type="text/javascript">

            var _gaq = _gaq || [];
            _gaq.push(['_setAccount', 'UA-38242985-1']);
            _gaq.push(['_trackPageview']);

            (function() {
              var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
              ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
              var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
            })();

        </script>

    </body>
</html>
