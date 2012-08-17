<?php

global $CFG;
require_once($CFG->dirroot . '/local/evaluations/classes/question.php');

class question_years extends question {

    public $type_name = "Years"; //loaded to database on install / update

    const averagable = false;
    const medianable = false;
    const modeable = false;
    const rangeable = false;
    const count_responses = false;

    function display(&$mform, $form, $data, $order) {
        $mform->addElement('header', "question_header_x[$order]", get_string('question', 'local_evaluations') . " $order");
        $mform->addElement('static', "question[$order]", '', '<b>' . $this->question . '</b>');

        $mform->addElement('hidden', "questionid[$order]", $this->id);
        $mform->addElement('hidden', "response[$order]", '');

        $abr = array(
            '1',
            '2',
            '3',
            '4',
            '>4'
        );
        
        $radioarray = array();

        for ($i = 0; $i < count($abr); $i++) {
            $radioarray[] = &$mform->createElement('radio', "comments[$order]", '', $abr[$i], $abr[$i]);
        }
        
        $mform->setDefault("comments[$order]", -1);
        
        $mform->addGroup($radioarray, "comment_grp[$order]", '', array('&nbsp;&nbsp;&nbsp;'), false);
        $mform->addRule("comment_grp[$order]", get_string('required'), 'required', null, 'client');
        
        
    }

    static function process_response_for_output($response, $comment) {

        $output .= $comment;

        return $output;
    }

    static function is_averagable() {
        return self::averagable;
    }

    static function is_medianable() {
        return self::medianable;
    }

    static function is_modeable() {
        return self::modeable;
    }

    static function is_rangeable() {
        return self::rangeable;
    }

    static function is_count_responses() {
        return self::count_responses;
    }

}

?>