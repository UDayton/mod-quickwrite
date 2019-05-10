<?php
require_once "../../config.php";
require_once('../dao/QW_DAO.php');

use \Tsugi\Core\LTIX;
use \QW\DAO\QW_DAO;

$LAUNCH = LTIX::requireData();

$p = $CFG->dbprefix;

$QW_DAO = new QW_DAO($PDOX, $p);

$question_id = isset($_GET["question_id"]) ? $_GET["question_id"] : false;

if ( $USER->instructor && $question_id ) {
    $questions = $QW_DAO->getQuestions($_SESSION["qw_id"]);
    $prevQuestion = false;
    foreach ($questions as $question) {
        if ($question["question_id"] == $question_id) {
            // Move this one up
            if($question["question_num"] == 1) {
                // This was the first so put it at the end
                $QW_DAO->updateQuestionNumber($question_id, count($questions) + 1);
                $QW_DAO->fixUpQuestionNumbers($_SESSION["qw_id"]);
                break;
            } else {
                // This was one of the other questions so swap with previous
                $QW_DAO->updateQuestionNumber($question_id, $prevQuestion["question_num"]);
                $QW_DAO->updateQuestionNumber($prevQuestion["question_id"], $question["question_num"]);
                break;
            }
        }
        $prevQuestion = $question;
    }
}

$_SESSION["success"] = "Question Order Saved.";

header( 'Location: '.addSession('../instructor-home.php') ) ;