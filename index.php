<?php

include_once('./assets/include/config.php'); // Ensure config.php is included first if it sets any necessary configurations
include_once('./assets/include/db.php');

session_start();
?>
<html>

<head>

    <title>Future Study Hub </title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" type="text/css" href="./assets/css/style.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@600">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400">

    <style>
        .slide-content {
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.4s ease;

        }


        .wrapper:hover .slide-content,
        .wrapper .slide-content:hover {
            max-height: 500px;
            /* Adjust the maximum height to match the height of your content when fully expanded */
        }
    </style>

</head>

<body>
    <?php
    include("./assets/include/Header.php");
    ?>
    <section class="hero">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <h1>Future Study Hub </h1>
                    <div class="d-flex justify-content-center usp">
                        <p>In order to succeed, we must first <i><b>Believe</b></i> that we can</p>
                    </div>
                    <a href="./courses.php" class="btn purple">TAKE IELTS
                        <img class="img-fluid" src="./assets/img/arrow.webp" alt="->">
                    </a>
                </div>
            </div>
        </div>
    </section>

    <section class="py-5" style="width: 100%; margin: 0; padding: 0;">
    <div class="container my-5" style="width: 100%; padding: 0;">
        <div class="row justify-content-center" style="margin: 0;">
            <div class="col-lg-12" style="width: 100%; padding: 0;">
                <h4 style="text-align: center;">Notice Board</h4>
                <hr color="#000" style="width: 100%; margin: 0;" />
                <marquee 
                    direction="up" 
                    onmouseover="this.stop();" 
                    onmouseout="this.start();" 
                    style="width: 100%; height: auto; display: block; overflow: hidden;">
                    <ul style="list-style-type: none; padding: 0; margin: 0;">
                        <?php 
                        $sql = "SELECT * from notice";
                        $query = $dbh->prepare($sql);
                        $query->execute();
                        $results = $query->fetchAll(PDO::FETCH_OBJ);
                        $cnt = 1;
                        if ($query->rowCount() > 0) {
                            foreach ($results as $result) { ?>
                                <li style="display: flex; align-items: center; padding: 5px 0;">
                                    <span style="margin-right: 10px; color: #000;">&#8226;</span> 
                                    <a 
                                        href="notice-details.php?nid=<?php echo htmlentities($result->id); ?>" 
                                        target="_blank" 
                                        style="text-decoration: none; display: block; color: #000;">
                                        <?php echo htmlentities($result->noticeDetails); ?>
                                    </a>
                                </li>
                            <?php }
                        } ?>
                    </ul>
                </marquee>
            </div>
        </div>
    </div>
</section>

    <section class="usp">
        <div class="container">
            <div class="row">
                <div class="col-md-12 text-center spacer">
                    <h2> Online <i>Classes For <i>IELTS</i> Learning.</h2>
                    <p>
                        <i>
                            Score higher, go farther! Elevate your IELTS with a tailored course for Listening, Reading, Writing, Speaking. Achieve academic and immigration goals.
                        </i>
                    </p>
                </div>
            </div>
            <div class="row gx-5">
                <div class="col-md-6 col-lg-6">
                    <div class="wrapper">
                        <picture>
                            <img class="img-fluid" src="./assets/img/icons8-reading-48.png" alt="Reading">
                        </picture>
                        <h3>Reading</h3>
                        <p class="slide-content">
                            IELTS Reading assesses your ability to comprehend and analyze written information through various question types.
                        </p>
                    </div>
                </div>
                <div class="col-md-6 col-lg-6">
                    <div class="wrapper">
                        <picture>
                            <img class="img-fluid" src="./assets/img/icons8-listening-48.png" alt="Listening">
                        </picture>
                        <h3>Listening</h3>
                        <p class="slide-content">
                            Effective listening skills allow you to be objective and cut through the tensed emotions to really determine right from wrong.
                        </p>
                    </div>
                </div>
                <div class="col-md-6 col-lg-6">
                    <div class="wrapper">
                        <picture>
                            <img class="img-fluid" src="./assets/img/icons8-writing-48.png" alt="Writing">
                        </picture>
                        <h3>Writing</h3>
                        <p class="slide-content">
                            Plan your time, read the question, highlight the issues to address, outline your response, expand on your ideas, plan how you will connect your ideas, and write your first draft.
                        </p>
                    </div>
                </div>
                <div class="col-md-6 col-lg-6">
                    <div class="wrapper">
                        <picture>
                            <img class="img-fluid" src="./assets/img/icons8-lecturer-48.png" alt="Speaking">
                        </picture>
                        <h3>Speaking</h3>
                        <p class="slide-content">
                            IELTS Speaking evaluates your ability to communicate fluently, coherently, and effectively in English, assessing pronunciation, vocabulary, grammar, and interactive skills.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="cta">
        <div class="container">
            <div class="row gx-5">
                <div class="col-lg-7">
                    <div class="content_wrapper mb-5 mb-lg-0">
                        <h2>
                            Modules
                        </h2>
                        <p><span class="ielts">IELTS</span> has two test types - <span>IELTS General Training</span> and <span>IELTS Academic</span>.
                        <h3> IELTS Academic</h3>
                        <p>IELTS Academic is suitable for candidates who require IELTS for academic purposes, mostly to study at undergraduate or postgraduate levels or for professional registration in an English speaking environment.</p>
                        <h3>IELTS General</h3>
                        <p>IELTS General Training is suited for those who need English language proficiency to show qualifications to study below a degree level, employment and migration to English speaking countries such as Australia, the UK, the USA, Canada, New Zealand, Ireland, etc.</p>


                    </div>
                </div>
                <div class="col-lg-5 order-lg-first">
                    <div class="form_wrapper mb-5 mb-lg-0">
                        <h2>IELTS</h2>
                        <p>
                            Test takers opting for IELTS on Computer take the Listening, Reading, and Writing sections on a computer.IELTS on Computer gives you the convenience to choose from multiple test dates and slots, you can expect your results within 3-5 days.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <?php
    include("./assets/include/Footer.php");
    ?>

    <script type="text/javascript" src="./script.js"></script>
    <script>
        $(document).ready(function() {
            $('.wrapper').hover(function() {
                $(this).find('.slide-content').toggleClass('open');
            });
        });
    </script>
</body>

</html>