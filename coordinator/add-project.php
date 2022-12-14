<?php
include_once 'head.php';
include_once '../api/classes/ProjectCategory.php';

$lecArray = $lec->getAllUsers();
$student = new Student($conn);
$studentArray = $student->getAllUsers();
$pc = new ProjectCategory($conn);

?>
<link rel="stylesheet" type="text/css" href="../assets/libs/slimselect/slimselect.min.css">
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
                            <li class="breadcrumb-item"><a href="#!"><i class="fa fa-cloud-upload-alt"></i> Projects</a> </li>
                            <li class="breadcrumb-item"><a href="#">Add Project</a> </li>
                        </ul>
                    </div>
                </div>
                <!-- end page title -->
                <!-- start row -->
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">
                                <h4 class="card-title">Project Details</h4>
                                <div class="dropdown-divider"></div>

                                <form class="add-project-form">
                                    <div class="form-group form-row">
                                        <div class="col-sm-12">
                                            <label for="project_title">Title: </label>
                                            <input type="text" class="form-control" id="project_title" placeholder="e.g. Church management system " name="project_title">
                                        </div>
                                    </div>
                                    <div class="form-group form-row">
                                        <div class="col-sm-12">
                                            <label for="student">Student:</label>
                                            <select id="student" name="student">
                                                <option value="" disabled selected>--Select Student--</option>
                                                <?php
                                                foreach ($studentArray as $student) {
                                                    if (empty($student['project_title'])) { ?>
                                                        <option value="<?= $student['reg_no'] ?>">
                                                            <?= ucwords($student['full_name']).' - '. $student['reg_no'] ?>
                                                        </option>
                                                <?php } }
                                                ?>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="form-group form-row">
                                        <div class="col-sm-6">
                                            <label for="supervisor">Supervisor:</label>
                                            <select id="supervisor" name="supervisor">
                                                <option value="" disabled selected>--Select Supervisor--</option>
                                                <?php
                                                if ($_SESSION['level'] === 1) {
                                                    foreach ($lecArray as $lec) { ?>
                                                        <option value="<?= $lec['emp_id'] ?>"><?= ucwords($lec['full_name']) ?></option>
                                                    <?php }
                                                }else{
                                                    ?>
                                                        <option value="<?= $_SESSION['username'] ?>"><?= ucwords($lecDetails['full_name']) ?></option>
                                                    <?php 
                                                }
                                                
                                                ?>
                                            </select>
                                        </div>
                                        <div class="col-sm-6">
                                            <label for="p-cat">Project Category:</label>
                                            <select id="p-cat" name="category">
                                                <?php
                                                $categories = $pc->viewAllCategories();
                                                foreach ($categories as $category) { ?>
                                                    <option value="<?= $category['id'] ?>"><?= $category['name'] ?></option>
                                                <?php }
                                                ?>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="form-group form-row">
                                        <div class="col-sm-12">
                                            <label for="description">Description:</label>
                                            <textarea name="description" id="description" cols="30" rows="6" class="form-control"></textarea>
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
<script type="text/javascript" src="../assets/libs/slimselect/slimselect.min.js"></script>
<script type="text/javascript" src="../assets/libs/bootstrap-validator/js/bootstrapValidator.min.js"></script>
<script src="assets/js/app.js" type="text/javascript"></script>
</body>

</html>
<script type="text/javascript">
    $(document).ready(function() {
        // $('select').niceSelect();
        let select = new SlimSelect({
            select: '#student',
            allowDeselect: true,
            closeOnSelect: true,
            searchHighlight: true
        });

        let select2 = new SlimSelect({
            select: '#supervisor',
            closeOnSelect: true,
            allowDeselect: true
        });

        let select3 = new SlimSelect({
            select: '#p-cat',
            closeOnSelect: true,
            allowDeselect: true
        });

        $('.add-project-form').on('submit', function(event) {
            event.preventDefault();
        });

        $('.add-project-form').bootstrapValidator({
            message: 'This value is not valid',
            excluded:':disabled',
            feedbackIcons: {
                valid: 'fa fa-check',
                invalid: 'fa fa-times',
                validating: 'fa fa-refresh'
            },
            fields:{
                'project_title' : {
                    message: 'The title is not valid',
                    validators: {
                        notEmpty: {
                            message: 'The title of the project is required and cannot be empty. '
                        },
                        stringLength: {
                            min: 5,
                            max: 40,
                            message: 'The title must be more than 5 and less than 40 characters long'
                        },
                        regexp: {
                            regexp: /^[a-zA-Z0-9'\s]+$/,
                            message: 'The title can only consist of alphabetical, numbers, underscores and hyphen'
                        }
                    }
                },
                'description' : {
                    message: 'The description is not valid',
                    validators: {
                        notEmpty: {
                            message: 'The description is required and cannot be empty'
                        }
                    }
                },
                'student':{
                    message: 'The description is not valid',
                    validators: {
                        notEmpty: {
                            message: 'Please select a student'
                        }
                    }
                },
                'category':{
                    message: 'The description is not valid',
                    validators: {
                        notEmpty: {
                            message: 'Please select a project category'
                        }
                    }
                }
            },
            onSuccess: function (e) {
                $form = $(e.target);
                let formData = {}
                $form.serializeArray().map((v)=> formData[v.name] = v.value)

                $.post('../api/project.php',{...formData},(data)=>{
                    console.log(data)
                    toastr.success(data.success.message, "Bravoo!", {
                        showMethod: "slideDown",
                        hideMethod: "fadeOut"
                    });
                }).fail((data)=>{
                    console.log(data)
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
                select.set('')
                select2.select('')
                select3.select('')
            }
        })
            .on('status.field.bv', function(e, data) {
                data.bv.disableSubmitButtons(false);
            });
    });
</script>