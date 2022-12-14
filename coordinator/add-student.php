<?php
include_once 'head.php'; 
?>
<link rel="stylesheet" type="text/css" href="../assets/libs/jquery-nice-select/css/nice-select.css">
<link rel="stylesheet" type="text/css" href="../assets/libs/bootstrap-validator/css/bootstrapValidator.css">
<style type="text/css">
    .form-control {
        height: 42px;
        border-radius: 5px;
        box-shadow: none;
        font-family: sans-serif;
    }
</style>

<body data-sidebar="dark" onload="preloader()">
    <!-- preloader -->
    <div class="la-anim-1"></div>
    <!-- Begin page -->
    <div id="layout-wrapper">
        <?php include_once 'header.php'; ?>
        <!-- ========== Left Sidebar Start ========== -->
        <?php include_once 'sidebar.php'; ?>
        <!-- Left Sidebar End -->
        <!-- ============================================================== -->
        <!-- Start right Content here -->
        <!-- ============================================================== -->
        <div class="main-content">
            <div class="page-content">
                <div class="container-fluid">
                    <!-- start page title -->
                    <div class="row align-items-center">
                        <div class="col-sm-12 p-t-15">
                            <ul class=" breadcrumb breadcrumb-title float-right bg-transparent">
                                <li class="breadcrumb-item">
                                    <a href="index.php"><i class="mdi mdi-home"></i></a>
                                </li>
                                <li class="breadcrumb-item"><a href="#!">Students</a> </li>
                                <li class="breadcrumb-item"><a href="#">Add Students</a> </li>
                            </ul>
                        </div>
                    </div>
                    <!-- end page title -->
                    <!-- start row -->
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-body">
                                    <h4 class="card-title">Student Details</h4>
                                    <div class="dropdown-divider"></div>

                                    <form class="add-student-form">
                                        <div class="form-group form-row">
                                            <div class="col-sm-6">
                                                <label for="regno">Reg No: </label>
                                                <input type="text" class="form-control" id="regno" placeholder="Enter registration number" name="regno">
                                            </div>
                                            <div class="col-sm-6">
                                                <label for="name">Full Name:</label>
                                                <input type="text" class="form-control" id="name" placeholder="Enter full name" name="name">
                                            </div>
                                        </div>

                                        <div class="form-group form-row">
                                            <div class="col-sm-6">
                                                <label for="email">Email: </label>
                                                <input type="email" class="form-control" id="email" placeholder="john@doe.com" name="email">
                                            </div>
                                            <div class="col-sm-6">
                                                <label for="phone">Phone No:</label>
                                                <input type="text" class="form-control" id="phone" placeholder="0712345678" name="phone">
                                            </div>
                                        </div>

                                        <div class="form-group form-row">
                                            <div class="col-sm-4">
                                                <label for="school">School: </label>
                                                <select name="school" class="wide" id="school">
                                                    <option value="spas" selected>SPAS</option>
                                                </select>
                                            </div>
                                            <div class="col-sm-4">
                                                <label for="department">Department: </label>
                                                <select name="department" class="wide" id="department">
                                                    <option value="mac" selected>Maths and Computer Science</option>
                                                </select>
                                            </div>
                                            <div class="col-sm-4">
                                                <label for="course">Course: </label>
                                                <select name="course" class="wide" id="course">
                                                    <option value="cs" selected>Computer Science</option>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="text-center my-5">
                                            <button type="submit" class="btn btn-primary p-t-8 p-b-8 p-l-20 p-r-20">
                                                Save <i class="fa fa-save"></i>
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!--  end row -->
                </div> <!-- container-fluid -->
            </div>
            <!-- End Page-content -->
            <?php include_once 'footer.php'; ?>
        </div>
        <!-- end main content-->
    </div>
    <!-- END layout-wrapper -->

    <!-- Right bar overlay-->
    <div class="rightbar-overlay"></div>
    <!-- JAVASCRIPT -->
    <?php include_once 'js.php'; ?>
    <script type="text/javascript" src="../assets/libs/jquery-nice-select/js/jquery.nice-select.min.js"></script>
    <script type="text/javascript" src="../assets/libs/bootstrap-validator/js/bootstrapValidator.min.js"></script>
    <script src="assets/js/app.js" type="text/javascript"></script>
</body>

</html>
<script type="text/javascript">
    $(document).ready(function() {
        $('select').niceSelect();
    });
    $('.add-student-form').on('submit', function(event) {
        event.preventDefault();
    });

    $('.add-student-form').bootstrapValidator({
            message: 'This value is not valid',
            feedbackIcons: {
                valid: 'fa fa-check',
                invalid: 'fa fa-times',
                validating: 'fa fa-refresh'
            },
            fields:{
                'regno' : {
                    validators: {
                        //when empty it will bring this error message
                        notEmpty: {
                            message: 'The registration number is required and cannot be empty'
                        },
                        //this is a regular expression to validate registration number
                        // regexp: {
                        //     regexp: /^([a-z0-9]{4})+\/pu+\/(\d{4,5})+\/(\d{2})$/i,
                        //     message: 'Please provide a valid registration number'
                        // }
                    }
                },
                'phone':{
                    validators:{
                        notEmpty: {
                            message: 'The phone number is required and cannot be empty'
                        },
                        regexp: {
                            regexp: /^(0|\+?254)7(\d){8}$/,
                            message: 'Please provide a valid phone number'
                        }
                    }
                },
                'name' : {
                    message: 'The name is not valid',
                    validators: {
                        notEmpty: {
                            message: 'The full name is required and cannot be empty'
                        },
                        stringLength: {
                            min: 5,
                            max: 40,
                            message: 'The full name must be more than 5 and less than 40 characters long'
                        },
                        regexp: {
                            regexp: /^[a-zA-Z]+\s+[a-zA-Z\s]+$/,
                            message: 'The name can only consist of alphabetical and atlest two names'
                        }
                    }
                },
                'email' : {
                    message: 'The email is not valid',
                    validators: {
                        notEmpty: {
                            message: 'The email is required and cannot be empty'
                        },
                        regexp: {
                            regexp: /^([a-z0-9_\-\.])+\@([a-z0-9_\-\.])+\.([a-z]{2,4})$/i,
                            message: 'Please provide a valid email address'
                        }
                    }
                }

            },
            onSuccess: function (e) {
                $form = $(e.target);
                let formData = {}
                    $form.serializeArray().map((v)=> formData[v.name] = v.value)

                $.post('../api/student.php',{...formData},(data)=>{
                    toastr.success(data.success.message, "Bravoo!", {
                        showMethod: "slideDown",
                        hideMethod: "fadeOut"
                    });
                }).fail((data)=>{
                    let message = 'Some unexpected error occurred';
                    try{
                        message = data['responseJSON']['error']['message'];
                    }catch (e) {
                        console.error(message)
                    }
                    toastr.error(message, "Ooops!", {
                        showMethod: "slideDown",
                        hideMethod: "fadeOut"
                    });
                });


                $form
                    .bootstrapValidator('disableSubmitButtons', false)
                    .bootstrapValidator('resetForm', true);
            }
        })
        .on('status.field.bv', function(e, data) {
            data.bv.disableSubmitButtons(false);
    });
</script>