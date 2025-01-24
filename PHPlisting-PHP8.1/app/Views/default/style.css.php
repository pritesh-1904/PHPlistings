/**
 * @package    phpListings
 * @author     phpListings Team <info@phplistings.com>
 * @copyright  2024 phpListings.com
 * @license    https://www.phplistings.com/eula
 */

:root {
    font-size: <?php echo $view->get('bodyFontSize'); ?>px;
}

html {
    scroll-behavior: smooth;
}

body {
    margin: 0;
    font-family: '<?php echo $view->get('bodyFontFamily'); ?>', sans-serif;
    font-weight: <?php echo $view->get('bodyFontWeight'); ?>;
    font-size: <?php echo $view->get('bodyFontSize'); ?>px;
    color: <?php echo $view->get('bodyFontColor'); ?>;
    background-color: rgb(255, 255, 255);
}

/* Typography */

h1,
.h1 {
    margin: 0;
    font-size: 2.6rem;
}

h2,
.h2 {
    margin: 0;
    font-size: 2.3rem;
}

h3,
.h3 {
    margin: 0;
    font-size: 2.1rem;
}

h4,
.h4 {
    margin: 0;
    font-size: 1.9rem;
}

h5,
.h5 {
    margin: 0;
    font-size: 1.9rem;
}

h6,
.h6 {
    margin: 0;
    font-size: 1.5rem;
}

.lead {
    font-weight: 400;
    font-size: 1rem;
}

/* Font Size Utlities */

.display-0 {
    font-size: 2.9rem;
}

.display-1 {
    font-size: 2.7rem;
}

.display-2 {
    font-size: 2.5rem;
}

.display-3 {
    font-size: 2.3rem;
}

.display-4 {
    font-size: 2.1rem;
}

.display-5 {
    font-size: 1.9rem;
}

.display-6 {
    font-size: 1.7rem;
}

.display-7 {
    font-size: 1.5rem;
}

.display-8 {
    font-size: 1.3rem;
}

.display-9 {
    font-size: 1.1rem;
}

.display-10 {
    font-size: 1rem;
}

.display-11 {
    font-size: 0.9rem;
}

.display-12 {
    font-size: .7rem;
}

.display-100 {
    font-size: 5.9rem;
}

/* Text Weight */

.text-thin {
    font-weight: 300;
}

.text-regular {
    font-weight: 400;
}

.text-medium {
    font-weight: 700;
}

.text-bold {
    font-weight: 800;
}

.text-black {
    font-weight: 900;
}

/* Text Colors */

.text-primary {
    color: rgb(41, 147, 239) !important;
}

.text-dark {
    color: rgb(73, 76, 89) !important;
}

.text-super-dark {
    color: rgb(24, 25, 29) !important;
}

.text-success {
    color: rgb(10, 204, 92) !important;
}

.text-danger {
    color: rgb(233, 53, 88);
}

.text-warning {
    color: rgb(252, 175, 51) !important;
}

.text-shadow {
    text-shadow: 0px 0px 2px rgba(0, 0, 0, 1);
}

.link-success,
.link-success a,
.link-success a:active,
.link-success a:hover,
.link-success a:visited {
    color: rgb(10, 204, 92) !important;
}

/* Fills & Gradients */

.shadow-md {
    -moz-box-shadow: 2px 2px 20px rgba(0, 0, 0, 0.1);
    -webkit-box-shadow: 2px 2px 20px rgba(0, 0, 0, 0.1);
    box-shadow: 2px 2px 20px rgba(0, 0, 0, 0.1);
}

.bg-danger {
    background-color: rgb(237, 78, 107);
}

.bg-danger-light {
    background-color: rgb(249, 227, 230);
}

.bg-success-light {
    background-color: rgb(243, 254, 248) !important;
}

.bg-primary-light {
    background-color: rgb(212, 233, 253) !important;
}

.bg-secondary {
    background-color: rgb(225, 227, 229) !important;
}

.bg-light {
    background-color: rgb(250, 250, 250) !important;
}

.bg-dark {
    background-color: rgb(35, 36, 41) !important;
}

.bg-warning-light {
    background-color: rgb(254, 249, 220) !important;
}

.dark-fill {
    position: relative;
    overflow: hidden;
}

.dark-fill::after {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    content: ' ';
    background: rgba(0, 0, 0, 0.5);
}

.dark-fill-light {
    position: relative;
    overflow: hidden;
}

.dark-fill-light::after {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    content: ' ';
    background: rgba(0, 0, 0, 0.4);
}


.duo-fill {
    position: relative;
}

.duo-fill::after {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    content: ' ';
    background: -webkit-linear-gradient(top, rgba(0, 0, 0, 0) 0%, rgba(0, 0, 0, 0.35) 55%, rgba(0, 0, 0, 0.55) 100%);
    background: -o-linear-gradient(top, rgba(0, 0, 0, 0) 0%, rgba(0, 0, 0, 0.35) 55%, rgba(0, 0, 0, 0.55) 100%);
    background: linear-gradient(to bottom, rgba(0, 0, 0, 0) 0%, rgba(0, 0, 0, 0.35) 55%, rgba(0, 0, 0, 0.55) 100%);
}

.duo-fill-light {
    position: relative;
}

.duo-fill-light::after {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    content: ' ';
    background: -moz-linear-gradient(top, rgba(0, 0, 0, 0) 0%, rgba(0, 0, 0, 0.25) 65%, rgba(0, 0, 0, 0.35) 100%);
    background: -webkit-linear-gradient(top, rgba(0, 0, 0, 0) 0%, rgba(0, 0, 0, 0.25) 65%, rgba(0, 0, 0, 0.35) 100%);
    background: linear-gradient(to bottom, rgba(0, 0, 0, 0) 0%, rgba(0, 0, 0, 0.25) 65%, rgba(0, 0, 0, 0.35) 100%);
}

.border-primary {
    border: 1px solid rgba(2, 123, 227);
}

.border-success {
    border: 1px solid rgb(62, 207, 144);
}

.border-danger {
    border: 1px solid rgb(235, 76, 45);
}

.border-warning {
    border: 1px solid rgb(255, 180, 59);
}

.border-light {
    border: 1px solid rgb(214, 214, 214);
}

/* Spacing */

.vh-100 {
    height: 100vh;
}

.ht-auto {
    height: auto;
}

.m-height-200 {
    min-height: 200px;
}

.l-space-1 {
    letter-spacing: 1.5px;
}

.l-space-2 {
    letter-spacing: 1px;
}

.l-space-3 {
    letter-spacing: 0.5px;
}

.l-space-0 {
    letter-spacing: -0.5px;
}

.py-6 {
    padding-top: 4rem !important;
    padding-bottom: 4rem !important;
}

.py-7 {
    padding-top: 5rem !important;
    padding-bottom: 5rem !important;
}

.py-8 {
    padding-top: 6rem !important;
    padding-bottom: 6rem !important;
}

.py-9 {
    padding-top: 7rem !important;
    padding-bottom: 7rem !important;
}

.py-10 {
    padding-top: 8rem !important;
    padding-bottom: 8rem !important;
}

.pb-6 {
    padding-bottom: 4rem !important;
}

@media (min-width:768px){
    .py-md-6 {
        padding-top: 4rem !important;
        padding-bottom: 4rem !important;
    }

    .py-md-7 {
        padding-top: 5rem !important;
        padding-bottom: 5rem !important;
    }

    .py-md-8 {
        padding-top: 6rem !important;
        padding-bottom: 6rem !important;
    }

    .py-md-9 {
        padding-top: 7rem !important;
        padding-bottom: 7rem !important;
    }

    .py-md-10 {
        padding-top: 8rem !important;
        padding-bottom: 8rem !important;
    }
}

@media (min-width:992px){
    .py-lg-6 {
        padding-top: 4rem !important;
        padding-bottom: 4rem !important;
    }

    .py-lg-7 {
        padding-top: 5rem !important;
        padding-bottom: 5rem !important;
    }

    .py-lg-8 {
        padding-top: 6rem !important;
        padding-bottom: 6rem !important;
    }

    .py-lg-9 {
        padding-top: 7rem !important;
        padding-bottom: 7rem !important;
    }

    .py-lg-10 {
        padding-top: 8rem !important;
        padding-bottom: 8rem !important;
    }

    .py-lg-13 {
        padding-top: 11rem !important;
        padding-bottom: 11rem !important;
    }
}

/* Buttons */

.btn-primary {
    color: rgb(255, 255, 255);
    background-color: rgb(2, 123, 227);
    border-color: rgb(2, 123, 227);
}

.btn-primary:hover,
.btn-primary:focus {
    background-color: rgb(3, 104, 190);
    border-color: rgb(3, 104, 190);
    color: white;
}

.btn-light {
    color: rgb(41, 45, 50);
    background-color: rgb(235, 237, 239);
    border-color: rgb(235, 237, 239);
}

.btn-light:hover,
.btn-light:focus {
    background-color: rgb(213, 215, 217);
    border-color: rgb(213, 215, 217);
    color: rgb(34, 37, 41);
}

.btn-round {
    border-radius: 2rem;
    padding: .4rem 1.3rem;
}

.btn-round-sm {
    border-radius: 2rem;
    padding: .3rem 0.85rem;
}

.btn-secondary {
    color: rgb(255, 255, 255);
    background-color: rgb(159, 155, 155);
    border-color: rgb(159, 155, 155);
}

.btn-secondary:hover,
.btn-secondary:focus {
    color: rgb(255, 255, 255);
    background-color: rgb(149, 145, 145);
    border-color: rgb(149, 145, 145);
}

.btn-success {
    color: rgb(255, 255, 255);
    background-color: rgb(10, 204, 92);
    border-color: rgb(10, 204, 92);
}

.btn-success:hover,
.btn-success:focus {
    color: rgb(255, 255, 255);
    background-color: rgb(10, 188, 85);
    border-color: rgb(10, 188, 85);
}

.btn-info {
    color: rgb(255, 255, 255);
    background-color: rgb(63, 209, 202);
    border-color: rgb(63, 209, 202);
}

.btn-info:hover,
.btn-info:focus {
    color: rgb(255, 255, 255);  
    background-color: rgb(4, 183, 177);
    border-color: rgb(4, 183, 177);
}

.btn-warning {
    color: rgb(255, 255, 255);
    background-color: rgb(242, 191, 87);
    border-color: rgb(242, 191, 87);
}

.btn-warning:hover,
.btn-warning:focus  {
    background-color: rgb(225, 177, 79);
    border-color: rgb(225, 177, 79);
    color: white;
}

.btn-danger {
    color: rgb(255, 255, 255);  
    background-color: rgb(237, 78, 107);
    border-color: rgb(237, 78, 107);
}

.btn-danger:hover,
.btn-danger:focus {
    color: rgb(255, 255, 255);  
    background-color: rgb(209, 70, 97);
    border-color: rgb(209, 70, 97);
}

.btn-float {
    display: none;
    position: fixed;
    bottom: 20px;
    right: 20px;
    z-index: 99;
}

/* Buttons Outline */

.btn-outline-primary {
    color: rgb(2, 123, 227);
    background-color: transparent;
    border-color: rgb(2, 123, 227);
}

.btn-outline-primary:hover,
.btn-outline-primary:focus {
    color: white;
    background-color: rgb(3, 104, 190);
    border-color: rgb(3, 104, 190);
}

.btn-outline-success {
    color: rgb(10, 204, 92);
    background-color: transparent;
    border-color: rgb(10, 204, 92);
}

.btn-outline-success:hover,
.btn-outline-success:focus {
    color: white;
    background-color: rgb(10, 188, 85);
    border-color: rgb(10, 188, 85);
}

.btn-outline-danger {
    color: rgb(214, 51, 70);
    background-color: transparent;
    border-color: rgb(214, 51, 70);
}

.btn-outline-danger:hover,
.btn-outline-danger:focus {
    color: white;
    background-color: rgb(191, 46, 63);
    border-color: rgb(191, 46, 63);
}

.btn-outline-warning {
    color: rgb(242, 191, 87);
    background-color: transparent;
    border-color: rgb(242, 191, 87);
}

.btn-outline-warning:hover,
.btn-outline-warning:focus {
    color: white;
    background-color: rgb(225, 177, 79);
    border-color: rgb(225, 177, 79);
}

.btn-outline-light {
    color: rgb(33, 37, 41);
    background-color: transparent;
    border-color: transparent;
}

.btn-outline-light:hover,
.btn-outline-light:focus {
    color: rgb(23, 25, 28);
    background-color: transparent;
    border-color: transparent;
}

/* Buttons  Social */

.btn-social {
    position: relative;
    text-align: left;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.btn-social > :first-child {
    position: absolute;
    left: 0;
    top: 0;
    bottom: 0;
    text-align: center;
    border-right: 1px solid rgba(0, 0, 0, 0.2);
}

.btn-social.btn-lg {
    padding-left: 61px;
}

.btn-social.btn-lg > :first-child {
    line-height: 45px;
    width: 45px;
    font-size: 1.2rem;
}

.btn.btn-icn {
    width: 2rem;
    height: 2rem;
    padding: .4rem;
    display: inline-flex;
    align-items: center;
    justify-content: center;
}

.btn-circle {
    border-radius: 50%;
}

.btn-icn i {
    padding: 0;
    margin: 0;
}

.btn-facebook {
    background-color: rgb(61, 89, 146);
    color: white;
    box-shadow: none;
    border: 0 none;
}

.btn-facebook:hover,
.btn-facebook:focus {
    background-color: rgb(54, 79, 130);
    color: white;
    box-shadow: none;
    border: 0 none;
}

.btn-twitter {
    background-color: rgb(14, 145, 227);
    color: white;
    box-shadow: none;
    border: 0 none;
}

.btn-twitter:hover,
.btn-twitter:focus {
    background-color: rgb(14, 139, 216);
    color: white;
    box-shadow: none;
    border: 0 none;
}

.btn-google {
    background-color: rgb(218, 55, 40);
    color: white;
    box-shadow: none;
    border: 0 none;
}

.btn-google:hover,
.btn-google:focus {
    background-color: rgb(205, 52, 39);
    color: white;
    box-shadow: none;
    border: 0 none;
}

.btn-linkedin-in {
    background-color: rgb(45, 123, 175);
    color: white;
    box-shadow: none;
    border: 0 none;
}

.btn-linkedin-in:hover,
.btn-linkedin-in:focus {
    background-color: rgb(41, 108, 153);
    color: white;
    box-shadow: none;
    border: 0 none;
}

.btn-instagram {
    background-color: rgb(34, 85, 131);
    color: white;
    box-shadow: none;
    border: 0 none;
}

.btn-instagram:hover,
.btn-instagram:focus {
    background-color: rgb(30, 74, 113);
    color: white;
    box-shadow: none;
    border: 0 none;
}

.btn-youtube {
    background-color: rgb(173, 34, 22);
    color: white;
    box-shadow: none;
    border: 0 none;
}

.btn-youtube:hover,
.btn-youtube:focus {
    background-color: rgb(154, 31, 21);
    color: white;
    box-shadow: none;
    border: 0 none;
}

.btn-vimeo {
    background-color: rgb(98, 185, 248);
    color: white;
    box-shadow: none;
    border: 0 none;
}

.btn-vimeo:hover,
.btn-vimeo:focus {
    background-color: rgb(89, 169, 227);
    color: white;
    box-shadow: none;
    border: 0 none;
}

.btn-flickr {
    background-color: rgb(225, 51, 129);
    color: white;
    box-shadow: none;
    border: 0 none;
}

.btn-flickr:hover,
.btn-flickr:focus {
    background-color: rgb(202, 46, 116);
    color: white;
    box-shadow: none;
    border: 0 none;
}

.btn-pinterest {
    background-color: rgb(178, 55, 52);
    color: white;
    box-shadow: none;
    border: 0 none;
}

.btn-pinterest:hover,
.btn-pinterest:focus {
    background-color: rgb(170, 46, 43);
    color: white;
    box-shadow: none;
    border: 0 none;
}

.btn-reddit {
    background-color: rgb(255, 69, 0);
    color: white;
    box-shadow: none;
    border: 0 none;
}

.btn-reddit:hover,
.btn-reddit:focus {
    background-color: rgb(229, 62, 0);
    color: white;
    box-shadow: none;
    border: 0 none;
}

/* Badges */

.badge {
    font-weight: 400;
    padding: .3em .5em;
}

.badge-primary {
    background-color: rgb(2, 123, 227);
}

.badge-primary[href]:hover,
.badge-primary[href]:focus {
    background-color: rgb(49, 97, 191);
}

.badge-success {
    background-color: rgb(10, 204, 92);
}

.badge-success[href]:hover,
.badge-success[href]:focus {
    background-color: rgb(10, 188, 85);
}

.badge-danger {
    background-color: rgb(233, 53, 88);
}

.badge-danger[href]:hover,
.badge-danger[href]:focus {
    background-color: rgb(219, 49, 82);
}

.badge-warning {
    background-color: rgb(242, 191, 87);
}

.badge-warning[href]:hover,
.badge-warning[href]:focus {
    background-color: rgb(225, 177, 79);
}

.badge-secondary {
    background-color: rgb(159, 155, 155);
}

.badge-secondary[href]:hover,
.badge-secondary[href]:focus {
    background-color: rgb(149, 145, 145);
}

.badge-info {
    background-color: rgb(63, 209, 202);
}

.badge-info[href]:hover,
.badge-info[href]:focus {
    background-color: rgb(4, 183, 177);
}

/* Alerts */

.alert-icon {
    width: 35px;
    height: 35px;
}

.alert-primary {
    color: rgb(2, 123, 227);
    background-color: rgb(248, 251, 254);
    border-color: rgb(95, 172, 238);
}

.alert-success {
    color: rgb(9, 164, 74);
    background-color: rgb(243, 254, 248);
    border-color: rgb(91, 221, 146);
}

.alert-danger {
    color: rgb(214, 51, 70);
    background-color: rgb(253, 231, 235);
    border-color: rgb(244, 141, 151);
}

.alert-warning {
    color: rgb(184, 125, 4);
    background-color: rgb(255, 244, 222);
    border-color: rgb(245, 193, 89);
}

.alert-secondary {
    color: rgb(60, 60, 60);
    background-color: rgb(250, 250, 250);
    border-color: rgb(212, 209, 209);
}

.alert-primary .alert-link {
    color: rgb(2, 123, 227);
}

.alert-success .alert-link {
    color: rgb(9, 164, 74);
}

.alert-danger .alert-link {
    color: rgb(214, 51, 70);
}

.alert-warning .alert-link {
    color: rgb(184, 125, 4);
}

.alert-secondary .alert-link {
    color: rgb(60, 60, 60);
}

/* Cards */

.card-row {
    flex-direction: row;
    align-items: flex-end;
    min-height: 150px;
}

.card-row .card-link {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    cursor: pointer;
    z-index: 30;
}

.card-badges {
    position: absolute;
    top: 5%;
    left: 5%;
}

.card-badges .badge {
    opacity: 0.85;
}

img.bg-img {
    object-fit: cover;
    object-position: center;
    font-family: 'object-fit: cover; object-position: center;';
}

.bg-img {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    z-index: 0;
}

.card-hot {
    margin-top: -2rem;
    padding-top: 2rem;
    padding-bottom: 4rem;
}

@media (max-width: 992px) {
    .card-hot-sm {
        margin-top: 0.3rem;
    }
}

.card-top {
    position: absolute;
    height: 7px;
    top: -1px;
    right: -1px;
    left: -1px;
    border-radius: calc(.4rem - 1px) calc(.4rem - 1px) 0 0;
}

.card-m {
    min-height: 150px;
}


/* Tables */

.table-primary,
.table-primary>th,
.table-primary>td {
    background-color: rgb(230, 241, 252);
    border-top: 1px solid rgb(198, 212, 230);
}

.table-hover .table-primary:hover {
    background-color: rgb(198, 212, 230);
}

.table-hover .table-primary:hover>td,
.table-hover .table-primary:hover>th {
    background-color: rgb(198, 212, 230);
}

.table-secondary,
.table-secondary>th,
.table-secondary>td {
    background-color: rgb(250, 250, 250);
    border-top: 1px solid rgb(215, 215, 215);
}

.table-hover .table-secondary:hover {
    background-color: rgb(237, 237, 237);
}

.table-hover .table-secondary:hover>td,
.table-hover .table-secondary:hover>th {
    background-color: rgb(237, 237, 237);
}

.table-success,
.table-success>th,
.table-success>td {
    background-color: rgb(220, 248, 233);
    border-top: 1px solid rgb(178, 221, 198);
}

.table-hover .table-success:hover {
    background-color: rgb(178, 221, 198);
}

.table-hover .table-success:hover>td,
.table-hover .table-success:hover>th {
    background-color: rgb(178, 221, 198);
}

.table-warning,
.table-warning>th,
.table-warning>td {
    background-color: rgb(255, 244, 222);
    border-top: 1px solid rgb(245, 223, 178);
}

.table-hover .table-warning:hover {
    background-color: rgb(252, 233, 194);
}

.table-hover .table-warning:hover>td,
.table-hover .table-warning:hover>th {
    background-color: rgb(252, 233, 194);
}

.table-danger,
.table-danger>th,
.table-danger>td {
    background-color: rgb(253, 231, 235);
    border-top: 1px solid rgb(235, 195, 202);
}

.table-hover .table-danger:hover {
    background-color: rgb(242, 207, 213);
}

.table-hover .table-danger:hover>td,
.table-hover .table-danger:hover>th {
    background-color: rgb(242, 207, 213);
}

/* Breadcrumb */

.breadcrumb {
    background: transparent;
    border-radius: 0;
    padding-left: 0 !important;
}

.breadcrumb > .breadcrumb-item > a {
    color: rgb(2, 123, 227);
}

.breadcrumb > .breadcrumb-item > a:hover {
    color: rgb(49, 97, 191);
    text-decoration: underline;
}

/* Form */

.form-control {
    height: calc(2.2em + .75rem + 2px);
    padding: .375rem .75rem;
}

.form-control:focus {
    color: #495057;
    background-color: #fff;
    border-color: rgb(3, 104, 190);
    outline: 0;
    box-shadow: none;
}

.custom-file {
    height: calc(2em + .75rem + 2px);
}

.custom-file-input:focus {
    box-shadow: none;
    outline: none;
}

.custom-file-label {
    height: calc(2em + .75rem + 2px);
    padding: 0.7rem .75rem;
}

.custom-file-label::after {
    height: calc(2em + .75rem);
    padding: 0.7rem .75rem;
}

.custom-select {
    height: calc(2.2em + .75rem + 2px);
}

.custom-select:focus {
    box-shadow: none;
    border-color: rgb(3, 104, 190);
}

.custom-file-input:focus~.custom-file-label {
    box-shadow: none;
    border-color: rgb(3, 104, 190);
}

.custom-select-sm {
    height: calc(1.7em + .8rem + 2px);
}



/* Top Navigation Widget */
/*
.navbar-top .navbar .navbar-nav a.nav-link {
    color: rgb(74, 80, 92);
}

.navbar-top .navbar .navbar-nav .active a.nav-link {
    color: rgb(2, 123, 227);
}

.navbar-top .navbar .navbar-nav a.nav-link:hover,
.navbar-top .navbar .navbar-nav a.nav-link:focus,
.navbar-top .navbar .navbar-nav .active a.nav-link:hover,
.navbar-top .navbar .navbar-nav .active a.nav-link:focus {
    color: rgb(38, 137, 224);
}
*/

@media (min-width: 992px) {
    .navbar-top .navbar .navbar-nav .nav-link {
        padding-right: 1rem;
        padding-left: 1rem;
    }
}

.navbar-top .dropdown-menu {
    margin-top: 1.3rem;
    border: 0 none;
    border-radius: 8px;
}

.navbar-top .dropdown-menu .dropdown-item {
    padding: .5rem 1.5rem;
}

/* Hero Widget */

.top-hero .header-hero {
    left: 0;
    top: 0;
}

.top-hero .swiper-container {
    width: 100%;
    height: 100%;
}

.top-hero .swiper-slide {
    background-repeat: no-repeat;
    background-position: center;
    background-size: cover;
}

/* Top Searchform */

.search-form-top {
    border-radius: 10rem;
}

.search-form-top .btn {
    border-radius: 10rem;
    padding: .5rem 1.4rem;
    line-height: 1.5;
}

.search-form-top .form-control,
.search-form-top .custom-select {
    border: 0 none;
    border-radius: 0;
    box-shadow: none;
    height: calc(1.8rem + .75rem + 2px);
    padding: .375rem .75rem;
    line-height: 2;
}

@media (min-width: 992px) {
    .search-form-top .field-line::after {
        display: block;
        width: 1px;
        height: 80%;
        content: '';
        background: rgb(221, 221, 221);
        position: absolute;
        top: 10%;
        right: 0;
    }

    .rtl .search-form-top .field-line::after {
        left: 0;
        right: auto;
    }
}

@media (max-width: 992px) {
    .search-form-top {
        border-radius: 25px;
    }

    .search-form-top .btn {
        border-radius: 25px;
    }

    .search-form-top .field-line::after {
        display: block;
        width: calc(100% - 2rem);
        height: 1px;
        content: '';
        background: rgb(222, 226, 230);
        position: absolute;
        top: auto;
        right: auto;
        bottom: 0;
        left: 50%;
        -webkit-transform: translateX(-50%);
        -ms-transform: translateX(-50%);
        -o-transform: translateX(-50%);
        -moz-transform: translateX(-50%);
        transform: translateX(-50%);
    }

    .search-form-top .form-control,
    .search-form-top .custom-select {
        height: calc(2rem + .75rem + 2px);
    }

}

/* Text Info Widget */

.trio-section-text .icon-top img {
    width: 60px;
    height: 60px;
}

/* Locations Widget */

.card-img-overlay {
    top: initial;
    bottom: 3%;
    left: 3%;
    padding: 10px;
    z-index: 30;
}

.location-main-featured .dark-fill::after {
    border-radius: calc(.25rem - 1px);
}

/* Listing Top Slider Widget */

.hero-base {
    max-height: 300px;
    overflow: hidden;
}

/* Other */

.map {
    height: 400px;
}

.box-overlay {
    position: relative;
    z-index: 20;
}

.w-15 {
    width: 15%;
}

.w-35 {
    width: 35%;
}

.w-85 {
    width: 85%;
}

.w-95 {
    width: 95%;
}


@media (min-width: 992px) {
    .bd-box {
        border-left: 1px solid rgb(222, 226, 230);
    }

    .rtl .bd-box {
        border-left: 0;
        border-right: 1px solid rgb(222, 226, 230);
    }

    .bd-box-right {
        border-right: 1px solid rgb(222, 226, 230);
    }
}

.nav-pills .nav-link.active,
.nav-pills .show>.nav-link {
    color: #fff;
    background-color: rgb(2, 123, 227);
}


/* User Side Menu Widget */

.side-menu li.nav-item a {
    color:rgb(73, 76, 89);
}


.side-menu li.nav-item a:hover {
    background: rgb(2, 123, 227);
    border-radius: .25rem;
    color: white;
}

.side-menu li.nav-item a:hover {
    color: white;
}



/* User Hero Widget */

.user-img img {
    width: 140px;
    height: 140px;
    padding: 2px;
}

/* Rating Page */

.rating-page .progress {
    border-radius: 1rem;
}

.rating-page .progress-sm {
    height: .7rem;
}

@media (min-width: 992px) {
    .rating-page .w-85 {
        width: 85%;
    }
}

/* Reviews */

.media .user-profile-icon {
    width: 5rem;
    height: 5rem;
}

/* Comments */

.comments-box::before {
    content: "";
    position: absolute;
    border: 20px solid transparent;
    border-bottom-color: rgb(250, 250, 250);
    top: -40px;
    left: 25px;
}

/* Misc */

.cropper.modal .modal-dialog {
    max-width: 60%;
}

.cropper.modal .image-container img {
    max-width: 100%;
}

.cropper-container {
    margin: 0 auto;
}

ul.fancytree-container {
    width: 100%;
    max-height: 400px;
    overflow: auto;
    position: relative;
    padding: .375rem .75rem;
    font-size: 1rem;
    font-weight: 400;
    line-height: 1.5;
    background-color: #fff;
    background-clip: padding-box;
    border: 1px solid #ced4da;
    border-radius: .25rem;
}

.tree-highlight .fancytree-title {
    background-color: rgb(247, 246, 246);
    color: rgb(33, 37, 41);
    padding: 2px 8px;
    margin: 3px;
    border-radius: 4px;
    font-size: 1.1rem !important;
}

.fancytree-ext-filter-dimm span.fancytree-node.fancytree-match span.fancytree-title,
.fancytree-ext-filter-dimm tr.fancytree-match span.fancytree-title {
  font-weight: 400;
}

.fancytree-ext-filter span.fancytree-title mark {
  background-color: #E00000;
  font-weight: 400;
  color: white;
  padding: .1em
}

.dropzone {
    border: .12rem dashed #ced4da;
    border-radius: .25rem;
}

.dropzone .dz-preview .dz-image,
.dropzone .dz-preview.dz-file-preview .dz-image {
    width: auto;
    height: auto;
    border-radius: 5px;
}

.dropzone .dz-preview {
    margin: 16px;
    max-width: 100%;
}

.dropzone .dz-message {
    font-size: 1rem;
}

.dropzone .dz-message i {
    font-size: 7rem;
    color: #EFEFEF;
}

.dropzone .dz-preview .dz-tools-cropper,
.dropzone .dz-preview .dz-tools-description,
.dropzone .dz-preview .dz-remove {
    font-size: 14px;
    text-align: center;
    display: inline;
    cursor: pointer;
    border: none;
    margin-left: 5px;
}

.dropzone .dz-preview .dz-tools-cropper {
    display: none;
}

.dropzone .dz-preview .dz-remove:hover {
    text-decoration: none;
}

.dropzone .dz-preview .dz-details .dz-filename {
    white-space: normal;
    margin-top: 15px;
    padding: 5px;
}

.invalid-feedback {
    display: block;
}

.form-control:disabled, .form-control[readonly] {
    background-color: #fff;
}

.form-control-readonly.form-control:disabled, .form-control-readonly.form-control[readonly] {
    background-color: rgb(250, 250, 250);
}

/* Switch */

.custom-switch {
    padding-left: 0;
}

.switch-content .custom-switch .custom-switch-input {
    box-sizing: border-box;
}

.switch-content .custom-switch .custom-switch-input {
    position: absolute;
    z-index: -1;
    opacity: 0;
}

.switch-content .custom-switch .custom-switch-input + .custom-switch-btn {
    outline: 0;
    display: inline-block;
    position: relative;
    -webkit-user-select: none;
    -moz-user-select: none;
    -ms-user-select: none;
    user-select: none;
    cursor: pointer;
    width: 48px;
    height: 28px;
    margin: 0;
    padding: 4px;
    background: rgb(216, 218, 221);
    border-radius: 76px;
    transition: all 100ms ease;
    box-sizing: border-box;
    cursor: pointer;
}

.switch-content .custom-switch .custom-switch-input + .custom-switch-btn::after {
    position: relative;
    display: block;
    content: "";
    width: 20px;
    height: 20px;
    left: 2px;
    border-radius: 50%;
    background: rgb(255, 255, 255);
    transition: all 150ms ease;
}

.switch-content .custom-switch .custom-switch-input:checked + .custom-switch-btn {
    background: rgb(2, 123, 227);
}

.switch-content .custom-switch .custom-switch-input:checked + .custom-switch-btn::after {
    left: 20px;
}

/* Star Rating */

div.svg div.rateit-range {
    background: url('data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHhtbDpzcGFjZT0icHJlc2VydmUiIHdpZHRoPSIxOXB4IiBoZWlnaHQ9IjE5cHgiIHZpZXdCb3g9IjAgMCAyMC4yMiAyMC4yNSIgeG1sbnM6eGxpbms9Imh0dHA6Ly93d3cudzMub3JnLzE5OTkveGxpbmsiPiA8ZyBpZD0iTGF5ZXJfeDAwMjBfMSI+ICA8cG9seWdvbiBmaWxsPSJ3aGl0ZSIgc3Ryb2tlPSIjRjVCMzAwIiBzdHJva2Utd2lkdGg9IjEuNzkiIHBvaW50cz0iMTAuMTEsMS44MSAxMy4wNCw2Ljk3IDE4LjYxLDguMyAxNC44NiwxMi44MyAxNS4zNiwxOC44MiAxMC4xMSwxNi40NiA0Ljg1LDE4LjgyIDUuMzYsMTIuODMgMS42MSw4LjMgNy4xNyw2Ljk3ICIvPiA8L2c+PC9zdmc+');
}

div.svg div.rateit-hover {
    background: url('data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSIxOXB4IiBoZWlnaHQ9IjE5cHgiIHZpZXdCb3g9IjAgMCAyMC4yMiAyMC4yNSIgeG1sbnM6eGxpbms9Imh0dHA6Ly93d3cudzMub3JnLzE5OTkveGxpbmsiPiA8ZyBpZD0iTGF5ZXJfeDAwMjBfMSI+ICA8cG9seWdvbiBmaWxsPSIjRjVCMzAwIiBzdHJva2U9IiNGNUIzMDAiIHN0cm9rZS13aWR0aD0iMS43OSIgcG9pbnRzPSIxMC4xMSwxLjgxIDEzLjA0LDYuOTcgMTguNjEsOC4zIDE0Ljg2LDEyLjgzIDE1LjM2LDE4LjgyIDEwLjExLDE2LjQ2IDQuODUsMTguODIgNS4zNiwxMi44MyAxLjYxLDguMyA3LjE3LDYuOTcgIi8+IDwvZz48L3N2Zz4=');
}

div.svg div.rateit-selected {
    background: url('data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSIxOXB4IiBoZWlnaHQ9IjE5cHgiIHZpZXdCb3g9IjAgMCAyMC4yMiAyMC4yNSIgeG1sbnM6eGxpbms9Imh0dHA6Ly93d3cudzMub3JnLzE5OTkveGxpbmsiPiA8ZyBpZD0iTGF5ZXJfeDAwMjBfMSI+ICA8cG9seWdvbiBmaWxsPSIjRjVCMzAwIiBzdHJva2U9IiNGNUIzMDAiIHN0cm9rZS13aWR0aD0iMS43OSIgcG9pbnRzPSIxMC4xMSwxLjgxIDEzLjA0LDYuOTcgMTguNjEsOC4zIDE0Ljg2LDEyLjgzIDE1LjM2LDE4LjgyIDEwLjExLDE2LjQ2IDQuODUsMTguODIgNS4zNiwxMi44MyAxLjYxLDguMyA3LjE3LDYuOTcgIi8+IDwvZz48L3N2Zz4=');
}

div.svg div.rateit-preset {
    background: url('data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHhtbDpzcGFjZT0icHJlc2VydmUiIHdpZHRoPSIxOXB4IiBoZWlnaHQ9IjE5cHgiIHZpZXdCb3g9IjAgMCAyMC4yMiAyMC4yNSIgeG1sbnM6eGxpbms9Imh0dHA6Ly93d3cudzMub3JnLzE5OTkveGxpbmsiPiA8ZyBpZD0iTGF5ZXJfeDAwMjBfMSI+ICA8cG9seWdvbiBmaWxsPSJ3aGl0ZSIgc3Ryb2tlPSIjRjVCMzAwIiBzdHJva2Utd2lkdGg9IjEuNzkiIHBvaW50cz0iMTAuMTEsMS44MSAxMy4wNCw2Ljk3IDE4LjYxLDguMyAxNC44NiwxMi44MyAxNS4zNiwxOC44MiAxMC4xMSwxNi40NiA0Ljg1LDE4LjgyIDUuMzYsMTIuODMgMS42MSw4LjMgNy4xNyw2Ljk3ICIvPiA8L2c+PC9zdmc+');
}

/* Pagination */

.page-item .page-link,
.page-item span {
    margin: 0 3px;
    font-size: 1rem;
    border: 0 none;
    color: rgb(53, 64, 73);
}

.page-item.active .page-link {
    z-index: 1;
    color: #fff;
    background-color: rgb(41, 147, 239);
    border-color: rgb(41, 147, 239);
    border-radius: .25rem;
}

.page-link:hover {
    color: rgb(53, 64, 73);
    text-decoration: none;
    border-radius: .25rem;
}

.grey-bg {
    background-color: rgb(250, 250, 255);
    border-radius: .25rem;
}

/* Share Popup */

.share-popup-wrapper {
    position: absolute;
    display: none;
    z-index: 999;
    top: -80px;
    right: 0px;
}

.share-popup {
    position: relative;
    border-radius: .25rem;
    background: white;
    width: auto;
    padding: 15px;
    text-align: center;
    box-sizing: border-box;
    white-space : nowrap;
}

.share-popup::after {
    content: '';
    position: absolute;
    width: 0;
    height: 0;
    top: 100%;
    right: 10%;
    border: 10px solid transparent;
    border-top-color: white;
}

.share-button,
.bookmark-button {
    cursor: pointer;
}

.share-popup-screen {
    display: none;
    position: fixed;
    z-index: 998;
    height: 100%;
    width: 100%;
    opacity: 0.5; 
    background-color: black;
    top: 0;
    left: 0;
}


#progress {
    display: none;
    position: absolute;
    z-index: 1000;
    left: 50%;
    top: 300px;
    width: 200px;
    height: 20px;
    margin-top: -20px;
    margin-left: -100px;
    background-color: #fff;
    background-color: rgba(255, 255, 255, 0.7);
    border-radius: 4px;
    padding: 2px;
}

#progress-bar {
    width: 0;
    height: 100%;
    background-color: #76A6FC;
    border-radius: 4px;
}

iframe {
    max-width: 100%;
}

.widget-image-caption {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
}        

.widget-two-column-teaser-image {
    background-size: cover;
    background-position: center;
}

@media (max-width: 768px) {
    .widget-two-column-teaser-image {
        min-height: 25vh;
    }
}

.bg-opacity {
    background: rgba(255, 255, 255, 0.15);
    transition: all 1s ease-out;
}

.bg-opacity-active {
    background: rgba(255, 255, 255, 0.3);
    transition: all 1s ease-out;
}

.bg-opacity:hover,
.bg-opacity:focus,
.bg-opacity-active:hover,
.bg-opacity-active:focus {
    text-decoration: none;
    background: rgba(255, 255, 255, 0.3);
}

.card-square {
    min-height: 230px;
}

.card-img-overlay-center {
    position: absolute;
    top: initial;
    left: 50%;
    top: 50%;
    transform: translate(-50%, -50%);
    padding: 10px;
    z-index: 30;
}

.card.duo-fill:hover::after,
.card.duo-fill:focus::after,
.card .duo-fill:hover::after,
.card .duo-fill:focus::after {
    background: rgba(0, 0, 0, 0);
}

.card .duo-fill-light:hover::after,
.card .duo-fill-light:focus::after {
    background: rgba(0, 0, 0, 0);
}

.three-column-teaser-widget .text-caption,
.two-column-teaser-widget .text-caption,
.quad-box-teaser-widget .text-caption,
.add-account-teaser-widget .text-caption,
.categories-widget .text-caption,
.categories-slider-widget .text-caption,
.listing-widget .text-caption {
    color: rgb(10, 204, 92) !important;
}

.reviews-widget .text-caption,
.reviews-slider-widget .text-caption,
.listing-reviews-widget .text-caption,
.listings-widget .text-caption,
.listings-slider-widget .text-caption,
.locations-widget .text-caption,
.locations-slider-widget .text-caption,
.pricing-widget .text-caption,
.user-widget .text-caption,
.contact-form-widget .text-caption,
.listing-add-review-form-widget .text-caption,
.listing-claim-form-widget .text-caption,
.listing-send-message-form-widget .text-caption {
    color: rgb(41, 147, 239) !important;
}

.widget.header-widget .highlighted-item {
    font-weight: 700;
}

.widget.footer-widget .highlighted-item {
    font-weight: 700;
}

.social-profile img {
    vertical-align: baseline;
    width: 2rem;
    height: auto;
    margin-right: 0.25rem;
    margin-bottom: 0.25rem;
}

.widget.listing-gallery-slider-widget .caption,
.widget.slider-widget .caption {
    position: absolute;
    width: 100%;
    bottom: 0px;
    left: 0px;
    background: linear-gradient(to top, rgba(0,0,0,1), rgba(0,0,0,0.25));
}

.widget-management-wrapper {
    position: relative;
}

.widget-management-wrapper .widget-management {
    display: none;
    position: absolute;
    top: 0;
    right: 0;
    z-index: 10000;
}

@media (min-width: 992px) {
    .widget-management-wrapper:hover .widget-management {
        display: block;
    }
}

.navbar-toggler:focus,
.navbar-toggler:active,
.navbar-toggler-icon:focus {
    outline: none;
    box-shadow: none;
}

.listing-badge {
    vertical-align: baseline;
    width: 3.5rem;
    height: auto;
    margin-right: 0.25rem;
    margin-bottom: 0.25rem;
}

.alert {
    text-wrap: wrap;
}

.card-img-top {
    height: auto;
}
