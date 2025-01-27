<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="description" content="HTHC - Hand to Hand Courier Services" />
    <title>HTHC - Hand to Hand Courier Services</title>
    <link rel="icon" type="image/x-icon" href="/favicon.ico" />
    <link href="https://use.fontawesome.com/releases/v5.13.0/css/all.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:ital,wght@0,300;0,400;0,600;1,700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        :root {
            --primary: #0d6efd;
            --dark: #212529;
        }

        body {
            font-family: 'Open Sans', sans-serif;
        }

        /* Navigation */
        #mainNav {
            transition: all 0.3s ease;
            background-color: transparent;
            padding: 1rem 0;
        }

        #mainNav.navbar-scrolled {
            background-color: #fff;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            padding: 0.5rem 0;
        }

        #mainNav .navbar-brand img {
            height: 50px;
            transition: height 0.3s;
        }

        #mainNav.navbar-scrolled .navbar-brand img {
            height: 40px;
        }

        .navbar-nav .nav-link {
            color: white !important;
            font-weight: 500;
            padding: 0.5rem 1rem !important;
        }

        .navbar-scrolled .nav-link {
            color: var(--dark) !important;
        }

        .btn-track {
            border-radius: 30px;
            padding: 0.5rem 1.5rem;
            font-weight: 600;
        }

        /* Header */
        .masthead {
            position: relative;
            padding: 10rem 0;
            background: linear-gradient(to bottom, rgba(0, 0, 0, 0.7) 0%, rgba(0, 0, 0, 0.7) 100%), url("images/courier-bg.jpg");
            background-position: center;
            background-repeat: no-repeat;
            background-attachment: scroll;
            background-size: cover;
            min-height: 100vh;
            display: flex;
            align-items: center;
        }

        .masthead h3 {
            font-size: 1.5rem;
            color: var(--primary);
        }

        .masthead h1 {
            font-size: 3rem;
            font-weight: 700;
            color: white;
        }

        /* About Section */
        .page-section {
            padding: 6rem 0;
        }

        .bg-primary {
            background-color: var(--primary) !important;
        }

        .vision-block {
            background: rgba(255,255,255,0.1);
            padding: 2rem;
            border-radius: 10px;
            height: 100%;
            margin-bottom: 1rem;
        }

        /* Services Section */
        .list-image {
            list-style: none;
            padding: 0;
        }

        .list-image li {
            display: flex;
            align-items: start;
            margin-bottom: 1rem;
            font-size: 1.1rem;
        }

        .list-image li img {
            width: 24px;
            margin-right: 1rem;
            margin-top: 5px;
        }

        /* Contact Section */
        .bg-black {
            background-color: #000;
            color: white;
        }

        .contact-icon {
            transition: all 0.3s ease;
        }

        .contact-icon:hover {
            transform: translateY(-5px);
        }

        .divider {
            height: 0.2rem;
            max-width: 3.25rem;
            margin: 1.5rem auto;
            background-color: var(--primary);
            opacity: 1;
        }

        /* Footer */
        .bg-dark {
            background-color: #212529 !important;
        }

        /* Media Queries */
        @media (max-width: 991.98px) {
            #mainNav {
                background-color: white;
                padding: 0.5rem 0;
            }

            .navbar-nav .nav-link {
                color: var(--dark) !important;
            }

            .navbar-toggler {
                border: none;
                padding: 0;
            }

            .masthead {
                padding: 6rem 0;
                text-align: center;
            }

            .masthead h1 {
                font-size: 2rem;
            }

            .btn-track {
                display: inline-block;
                margin: 0.5rem;
            }

            .action-buttons {
                display: flex;
                gap: 0.5rem;
                margin-top: 1rem;
            }
        }

        @media (max-width: 767.98px) {
            .page-section {
                padding: 4rem 0;
            }

            .vision-block {
                margin-bottom: 1rem;
            }

            #services img {
                margin-bottom: 2rem;
            }
        }
    </style>
</head>
<body id="page-top">
<!-- Navigation-->
<nav class="navbar navbar-expand-lg navbar-light fixed-top py-3" id="mainNav">
    <div class="container">
        <a class="navbar-brand" href="#page-top">
            <img src="images/logo.png" alt="HTHC Logo">
        </a>
        <div class="action-buttons d-lg-none">
            <a class="btn btn-primary btn-track" href="/login">Login</a>
            <a class="btn btn-primary btn-track" href="https://hthc.co.in/track">Track</a>
        </div>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarResponsive">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarResponsive">
            <ul class="navbar-nav mx-auto my-2 my-lg-0">
                <li class="nav-item"><a class="nav-link" href="#home">Home</a></li>
                <li class="nav-item"><a class="nav-link" href="#about">About</a></li>
                <li class="nav-item"><a class="nav-link" href="#services">Services</a></li>
                <li class="nav-item"><a class="nav-link" href="#contact">Contact</a></li>
            </ul>
            <div class="d-none d-lg-block">
                <a class="btn btn-primary btn-track" href="/login">Login</a>
                <a class="btn btn-primary btn-track" href="https://hthc.in/track">Track Parcel</a>
            </div>
        </div>
    </div>
</nav>

<!-- Masthead-->
<header class="masthead" id="home">
    <div class="container h-100">
        <div class="row h-100 align-items-center">
            <div class="col-lg-5 align-content-center justify-content-start">
                <h3>WELCOME TO HTHC</h3>
                <h1 class="text-left">We Provide Best Courier & Parcel Services</h1>
                <div class="d-flex gap-3 flex-wrap">
                    <a class="btn btn-primary btn-track" href="/login">Login</a>
                    <a class="btn btn-primary btn-track" href="https://hthc.in/track">Track Parcel</a>
                </div>
            </div>
        </div>
    </div>
</header>

<!-- About-->
<section class="page-section bg-primary" id="about">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-12 text-center">
                <h4 class="text-white mt-0 pb-3">
                    Hand To Hand Courier & Cargo Private Limited was incorporated as a Private Limited Company on
                    April 15, 1996 with the leadership of Mr. Umesh Narasappa, who is the Managing Director of the
                    Company having industry experience of around three decades in the field. The MD is assisted by
                    the Executive Director of the Company, Mr Thammayanna N. along with excellent team to handle
                    consignment from booking to delivery. Entire system is mechanized and the customer as well as
                    the company can track the consignment through tracking system in the web.
                </h4>
                <p class="text-white">
                    Since incorporation of the company in 1996, the company had a spectacular growth in the customer
                    base and the user of the system. In view of the study growth and the demand from the satisfied
                    customers, the company has thought fit to upgrade the technology to show the transparency in
                    their services. The existing customers include various companies and institutions of repute.
                </p>
            </div>
        </div>
        <div class="row text-center pt-5">
            <div class="col-lg-6 text-white">
                <div class="vision-block">
                    <h2>Vision</h2>
                    <h5>To provide best services to our stakeholders with quick delivery and enhance customer satisfaction.</h5>
                </div>
            </div>
            <div class="col-lg-6 text-white">
                <div class="vision-block">
                    <h2>Mission</h2>
                    <h5>Value for money to all our stakeholders with transparency with modern communication network.</h5>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Services-->
<section class="page-section" id="services">
    <div class="container">
        <h2 class="mt-0 mb-5">Our Services</h2>
        <div class="row">
            <div class="col-md-5">
                <img src="images/left-image.jpg" class="img-fluid rounded" alt="Services Image" />
            </div>
            <div class="col-md-7">
                <p>
                    Keeping up with technological advancement in the Courier & Cargo handling, HTHC has extensively
                    investing in up gradation of its infrastructure and modern apps to uphold excellence in services.
                    HTHC ensures that it identifies and deploys latest technology with a motto of enhancing the
                    customer satisfaction. HTHC offers the following
                </p>
                <ul class="list-image">
                    <li><img src="images/check.png" alt="check" /> End-to-end Consignment Tracking and Tracing functionalities</li>
                    <li><img src="images/check.png" alt="check" /> Data Accuracy</li>
                    <li><img src="images/check.png" alt="check" /> Web-based and Mobile confirmation to both consignor & Consignee.</li>
                    <li><img src="images/check.png" alt="check" /> Tech-based (digital image) Proof of delivery</li>
                    <li><img src="images/check.png" alt="check" /> Image-scan of Proof Of Delivery (POD) on the web</li>
                    <li><img src="images/check.png" alt="check" /> Mobile-based instant delivery update</li>
                    <li><img src="images/check.png" alt="check" /> Help Desk and call management applications</li>
                </ul>
            </div>
        </div>
    </div>
</section>

<!-- Contact-->
<section class="page-section bg-black" id="contact">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8 text-center">
                <h2 class="mt-0">Let's Get In Touch!</h2>
                <hr class="divider my-4" />
                <p class="mb-5">
                    We strive to provide best-in-class customer services and support to ensure a seamless shipping experience.
                </p>
            </div>
        </div>
        <div class="row text-center">
            <div class="col-lg-4 mb-5 mb-lg-0">
                <div class="contact-icon">
                    <i class="fas fa-map-marker fa-3x mb-3 text-primary"></i>
                    <div>
                        No. 5/3, Ground Floor, <br/>
                        Vokkaligara Bahvan,
                        Hudson Circle, <br/>
                        Bengaluru – 560 027 <br/>
                        Karnataka, India
                    </div>
                </div>
            </div>
            <div class="col-lg-4 mb-5 mb-lg-0">
                <div class="contact-icon">
                    <i class="fas fa-phone fa-3x mb-3 text-primary"></i>
                    <div>080 22292470/71</div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="contact-icon">
                    <i class="fas fa-envelope fa-3x mb-3 text-primary"></i>
                    <a class="d-block text-decoration-none" href="mailto:helpdeskhthc@gmail.com">helpdeskhthc@gmail.com</a>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Footer-->
<footer class="bg-dark p-4">
    <div class="container">
        <div class="small text-center text-muted">
            Copyright © 2024 HTHC - Hand to Hand Courier Services - Powered by Aviskara Solutions
        </div>
    </div>
</footer>

<!-- Scripts -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Navbar scroll behavior
    window.addEventListener('scroll', function() {
        const mainNav = document.getElementById('mainNav');
        if (window.scrollY > 50) {
            mainNav.classList.add('navbar-scrolled');
        } else {
            mainNav.classList.remove('navbar-scrolled');
        }
    });

    // Smooth scrolling
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
                // Close mobile menu if open
                const navbarCollapse = document.querySelector('.navbar-collapse');
                if (navbarCollapse.classList.contains('show')) {
                    document.querySelector('.navbar-toggler').click();
                }
            }
        });
    });
</script>
</body>
</html>
