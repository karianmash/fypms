<?php
header('Access-Control-Allow-Origin: http://localhost/fyp_ms/');
header('Access-Control-Allow-Methods: POST');
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


include_once '../api/config/database.php';
include_once '../api/classes/Lecturer.php';
include_once '../api/classes/Project.php';
include_once '../api/classes/Upload.php';
include_once '../api/classes/UploadCategory.php';
include_once '../api/classes/ProjectCategory.php';

$conn = Database::getInstance();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (empty($_POST)) {
        $_POST = json_decode(file_get_contents('php://input'), true) ?: [];
    }

    $data = $_POST;
    if (isset($data['project_title'], $data['student'], $data['category'], $data['description'])) {
        $title = $data['project_title'];
        $regNo = $data['student'];
        $category = $data['category'];
        $description = $data['description'];
        $empId = empty($data['supervisor']) ? '' : $data['supervisor'];

        $project = new Project($conn);
        $project->setUsername($regNo);
        $projectCat = new ProjectCategory($conn);
        if (!$project->userExists($regNo)) {
            echo json_response(409, ' The provided registration number does not exist.
             Please choose a different one.', true);
            die();
        } elseif ($project->studentHasProject($regNo)) {
            echo json_response(409, ' That student (' . $regNo . ') already has a project.', true);
            die();
        } elseif ($project->projectTitleExists($title, $category)) {
            echo json_response(409, ' That project (' . $title . ') already exists.', true);
            die();
        } elseif ($projectCat->categoryExists($category)) {
            echo json_response(409, ' That category (' . $category . ') does not exist.', true);
            die();
        } else {
            if (!empty($empId)) {
                $lec = new Lecturer($conn);
                if (!$lec->userExists($empId)) {
                    echo json_response(409, ' The provided employee id number does not exist.
                Please choose a different one.', true);
                    die();
                }
            }
            $conn->beginTransaction();
            if ($project->addProject($title, $description, $category, '', $empId)) {
                $conn->commit();
                echo json_response(200, 'The project has been added successfully.');
                die();
            } else {
                $conn->rollBack();
                echo json_response(400, 'There was error adding the project. Please try again later.', true);
                die();
            }
        }
    } else {
        echo json_response(400, 'Please make sure you provide all the required fields. i.e project_title, description, student, category and supervisor', true);
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'PATCH') {
    if (empty($_PATCH)) {
        $_PATCH = json_decode(file_get_contents('php://input'), true) ?: [];
    }

    $data = $_PATCH;
    if (isset($data['assign'])) {
        function extractIds($project)
        {
            return $project['id'];
        }
        $data = $data['assign'];
        if (isset($data['emp_id'], $data['projects'])) {
            $empId = $data['emp_id'];
            $projectArr = $data['projects'];

            $project = new Project($conn);
            $lecturer = new Lecturer($conn);

            if (!$lecturer->userExists($empId)) {
                echo json_response(409, ' The provided employee id does not exist.
             Please provide a different one.', true);
                die();
            } elseif (!is_array($projectArr)) {
                echo json_response(409, ' The project(s) should be in an array.', true);
                die();
            } else {
                $executeAll = true;
                $conn->beginTransaction();
                $assignedArr = $project->getLecturerProjects($empId);

                $assignedArrIds = array_map('extractIds', $assignedArr);
                $removedProjects = array_diff($assignedArrIds, $projectArr);
                foreach ($projectArr as $proj) {
                    if ($project->projectExists($proj) && (!$project->isAssigned($proj) || $project->isAssignedToMe($proj, $empId))) {
                        if ($project->isAssignedToMe($proj, $empId)) {
                            continue;
                        }

                        if (!$project->setSupervisor($empId, $proj)) {
                            $executeAll = false;
                            $conn->rollBack();
                            break;
                        }
                    }
                }

                foreach ($removedProjects as $proj) {
                    if ($project->projectExists($proj) && $project->isAssignedToMe($proj, $empId)) {
                        if (!$project->setSupervisor(null, $proj)) {
                            $executeAll = false;
                            $conn->rollBack();
                            break;
                        }
                    }
                }
                if ($executeAll) {
                    $conn->commit();
                    echo json_response(200, 'The project(s) has been assigned successfully.');
                    die();
                } else {
                    $conn->rollBack();
                    echo json_response(400, 'There was error assigning some project(s). Please try again later.', true);
                    die();
                }
            }
        } else {
            echo json_response(400, 'Please make sure you provide all the required fields. i.e emp_id, and projects (array)', true);
            die();
        }
    } else {
        if (isset($data['pid'])) {
            $pid = $data['pid'];
            $project = new Project($conn);
            $projectCat = new ProjectCategory($conn);
            if ($project->projectExists($pid)) {
                $projectDetails = $project->viewProject($pid);
                $title = empty($data['project_title']) ? $projectDetails['title'] : $data['project_title'];
                $category = empty($data['category']) ? $projectDetails['cat_id'] : $data['category'];
                $description = empty($data['description']) ? $projectDetails['description'] : $data['description'];
                $empId = empty($data['supervisor']) ? $projectDetails['emp_id'] : $data['supervisor'];
                $status = !isset($data['status']) ? $projectDetails['status_code'] : $data['status'];

                if ($title  != $projectDetails['title']) {
                    if ($project->projectTitleExists($title, $category)) {
                        echo json_response(409, ' That project (' . $title . ') already exists.', true);
                        die();
                    }
                } elseif ($projectCat->categoryExists($category)) {
                    echo json_response(409, ' That category (' . $category . ') does not exist.', true);
                    die();
                }

                $conn->beginTransaction();

                if ($project->editProject($pid, $category, $title, $description)) {

                    if ((int)$status != (int)$projectDetails['status_code']) {
                        $project->statusUpdate($pid, $status);
                    }
                    $conn->commit();
                    echo json_response(200, 'The project has been edited successfully.');
                    die();
                } else {
                    $conn->rollBack();
                    echo json_response(400, 'There was error editing the project. Please try again later.', true);
                    die();
                }
            } else {
                echo json_response(400, 'That project does not exist.', true);
                die();
            }
        } else {
            echo json_response(400, 'Please make sure you provide a project id. i.e pid', true);
            die();
        }
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    if (empty($_DELETE)) {
        $_DELETE = json_decode(file_get_contents('php://input'), true) ?: [];
    }
    $data = $_DELETE;

    if (isset($data['project']) && !empty($data['project'])) {
        $pid = $data['project'];
        $proj = new Project($conn);
        if ($proj->projectExists($pid)) {
            $conn->beginTransaction();
            if ($proj->deleteProject($pid)) {
                // $conn->rollBack();
                echo json_response(201, 'The project was deleted successfully.');
                die();
            } else {
                // $conn->rollBack();
                echo json_response(400, 'There was error deleting the project. Please try again later.', true);
                die();
            }
        } else {
            echo json_response(400, 'That project id no does not exist! Please provide a correct reg no.', true);
            die();
        }
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (isset($_GET['supervisor'])) {
        $empId = $_GET['supervisor'];
        $lec = new Lecturer($conn);
        if (!$lec->userExists($empId)) {
            echo json_encode([
                'webArr' => [],
                'androidArr' => [],
                'desktopArr' => [],
                'assignedArr' => []
            ]);
            die();
        } else {
            $project = new Project($conn);
            $projectArray = $project->viewAllProjects();
            $lec->setUsername($empId);
            $lecName = $lec->getUser()['full_name'];

            $webArr = [];
            $androidArr = [];
            $desktopArr = [];
            $assignedArr = [];
            foreach ($projectArray as $proj) {
                if ($proj['category'] === 'Web App' && ($proj['supervisor'] == $lecName || empty($proj['supervisor']))) {
                    $webArr[] = $proj;
                }
                if ($proj['category'] === 'Android App'  && ($proj['supervisor'] == $lecName || empty($proj['supervisor']))) {
                    $androidArr[] = $proj;
                }
                if ($proj['category'] === 'Desktop App'  && ($proj['supervisor'] == $lecName || empty($proj['supervisor']))) {
                    $desktopArr[] = $proj;
                }
                if ($proj['supervisor'] == $lecName) {
                    $assignedArr[] = $proj;
                }
            }
            echo json_encode([
                'webArr' => $webArr,
                'androidArr' => $androidArr,
                'desktopArr' => $desktopArr,
                'assignedArr' => $assignedArr
            ]);
            die();
        }
    } elseif (isset($_GET['category'])) {
        $pid = $_GET['category'];
        $upload = new Upload($conn);
        $project = new UploadCategory($conn);
        if (!$project->categoryExists($pid)) {
            echo json_encode(array([]));
            die();
        } else {
            $uploadArr = $upload->viewAllUploads();
            $myArray = [];

            foreach ($uploadArr as $up) {
                if ($up['category_id'] == $pid) {
                    array_push($myArray, $up);
                }
            }

            echo json_encode($myArray);
            die();
        }
    } elseif (isset($_GET['project_id'])) {
        $pid = $_GET['project_id'];
        $upload = new Upload($conn);
        $project = new Project($conn);
        if (!$project->projectExists($pid)) {
            echo json_encode(array([]));
            die();
        } else {
            $uploadArr = $upload->viewAllUploads();
            $myArray = [];

            foreach ($uploadArr as $up) {
                if ($up['pid'] == $pid) {
                    array_push($myArray, $up);
                }
            }

            echo json_encode($myArray);
            die();
        }
    } else {
        $project = new Project($conn);
        $projectArray = $project->viewAllProjects();
        $webArr = [];
        $androidArr = [];
        $desktopArr = [];

        $completedArr = [];
        $onGoingArr = [];
        $rejectedArr = [];

        $assignedArr = [];
        $unAssignedArr = [];

        foreach ($projectArray as $proj) {
            if ($proj['category'] === 'Web App') {
                $webArr[] = $proj;
            }
            if ($proj['category'] === 'Android App') {
                $androidArr[] = $proj;
            }
            if ($proj['category'] === 'Desktop App') {
                $desktopArr[] = $proj;
            }

            if (empty($proj['supervisor'])) {
                $unAssignedArr[] = $proj;
            } else {
                $assignedArr[] = $proj;
            }
            if ($proj['status'] == 'in progress') {
                $onGoingArr[] = $proj;
            } elseif ($proj['status'] == 'complete') {
                $completedArr[] = $proj;
            } else {
                $rejectedArr[] = $proj;
            }
        }
        echo json_encode([
            'webArr' => $webArr,
            'androidArr' => $androidArr,
            'desktopArr' => $desktopArr,
            'unAssignedArr' => $unAssignedArr,
            'assignedArr' => $assignedArr,
            'completedArr' => $completedArr,
            'onGoingArr' => $onGoingArr,
            'rejectedArr' => $rejectedArr,
            'total' => count($projectArray)
        ]);
        die();
    }
}

function json_response($code = 200, $message = null, $error = false)
{
    // clear the old headers
    header_remove();
    // set the actual code
    http_response_code($code);
    // set the header to make sure cache is forced
    header('Cache-Control: no-transform,public,max-age=300,s-maxage=900');
    // treat this as json
    header('Content-Type: application/json');
    $status = array(
        200 => '200 OK',
        201 => '201 Created',
        400 => '400 Bad Request',
        401 => '401 Unauthorized',
        404 => '404 Not Found',
        409 => '409 Conflict',
        422 => 'Unprocessable Entity',
        500 => '500 Internal Server Error'
    );
    // ok, validation error, or failure
    header('Status: ' . $status[$code]);
    // return the encoded json
    if ($error) {
        return json_encode(array(
            'status' => $status[$code] === 200,
            'error' => array('errorCode' => 0, 'message' => $message)
        ));
    }
    return json_encode(array(
        'success' => array('message' => $message)
    ));
}
