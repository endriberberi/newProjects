<?php
INCLUDE_ONCE ASP_FRONT_PATH."php/FE/examination.base.funcionality.class.php";
class examinationBase extends examinationBaseFuncionality
{
	function examinationBase() {
		parent::examinationBaseFuncionality();
	}
    function saveCertificate()
    {
    }
    function saveAutentificationInfo()
    {
    }  
    function updateUserElearningProgress($evaluation_test_id)
    {
    	//NESE DO DUHET TE BEHET UPDATE NJE STRUKTURE SPECIFIKE NE LIDHJE ME USERIN DO TE BEHET NE KLASEN EXTEND TE APLIKIMIT SPECIFIK
		global $session;
		
		$existRekordDone = "SELECT test_state, results_state,date_of_test,date_of_test_end,user_id,examination_id, history_id
        				  	  FROM z_EccE_user_examination
						 	 WHERE test_id = '".$evaluation_test_id."'";

        $rsDataDone = WebApp::execQuery($existRekordDone);
		//////trigger_error(print_r($rsDataDone, true));
        if (!$rsDataDone->EOF()) {
            
            $test_state 			= $rsDataDone->Field("test_state");
            $user_id  				= $rsDataDone->Field("user_id");
            $examination_id			= $rsDataDone->Field("examination_id");
            $results_state  		= $rsDataDone->Field("results_state");
            $date_of_test  			= $rsDataDone->Field("date_of_test");
            $date_of_test_end  		= $rsDataDone->Field("date_of_test_end");
            
            
            if ($test_state=="readyToEvaluate" || $test_state=="Evaluated") {
            
				$history_id = $rsDataDone->Field("history_id");
				if (($this->player_flag==21 || $this->player_flag==11) || ($this->player_flag==1 && $history_id==1)) {
					
					if ($this->externalLockState["examination_mode_default"]=='locked' && $this->externalLockState["history_id"]>0)
						$this->selfLockExamination($examination_id,$user_id,"lock");
						$history_id= $this->externalLockState["history_id"];
                       
                       //function lockUnlockUserExamination($examination_id="",$user_id="",$request_action=""
                }             
				

				$params["player_flag"]			= $this->player_flag; //UserCannotRedoTest|UserCanRedoTest 
				$params["user_id"] 				= $user_id;
				$params["item_type"] 			= $this->ci_type_configuration;
				$params["item_id"] 				= $this->cidFlow;
				$params["cordIds"] 				= $this->appRelSt["coord"][$this->cidFlow];
				$params["results_state"] 		= $results_state;	// `results_state` enum('passed','not_passed','new')
				$params["date_of_test"] 		= $date_of_test;	// `results_state` enum('passed','not_passed','new')
				$params["date_of_test_end"]		= $date_of_test_end;	// `results_state` enum('passed','not_passed','new')
				$params["date_of_test_end"]		= $date_of_test_end;	// `results_state` enum('passed','not_passed','new')
				$params["history_id"]			= $history_id;	// `results_state` enum('passed','not_passed','new')
				
				$debugVars = "\n updateUserElearningProgress \n";
				$debugVars .= print_r($params, true);
				//WebApp::writeLogError($debugVars,"ExamActions");	       
				
				INCLUDE_ONCE INC_PATH."elearning.user.progress.class.php";
				eLearningProgressUserBase::updateUserExaminationElearningProgress($params);
            }
        }
    }	
			
    function ajaxEventHandler()
    {
    	global $session, $paramsToControlByModule,$VS;
    	
			$cis = "";
			$player_test_id = "";
			$question_id = "";

			if (isset($_REQUEST["cis"]) && $_REQUEST["cis"] != "" && $_REQUEST["cis"] > 0) {
				$cis = $_REQUEST["cis"];
				$player_test_id = $cis;
				//  $question_id = $cis;
			}


			if (isset($paramsToControlByModule["CID"])
				&& $paramsToControlByModule["CID"] != ""
				&& $paramsToControlByModule["CID"] > 0
			) {
				$question_id = $cis;
				$cis = $paramsToControlByModule["CID"];
				$player_test_id = $cis;
			}

			$this->initCiReference($cis);
			$debugVAR = "\n\n EccELrnAjx\n\n "; 
			$debugVAR .= print_r($this->appRelSt, true); 
			$VS->writeDebugInfoAjax($debugVAR,$session->Vars["uni"],"yes");
			//   $dataToReturn["read_write"]

			$this->initPlayerConfiguration();
			$this->request_action = $_REQUEST["action"];
			$this->mainEntry = "no";
			
			/*echo "<textarea>";
			echo "_GET";
			print_r($_GET);
			echo "_REQUEST";
			print_r($_REQUEST);
			echo "paramsToControlByModule";
			print_r($paramsToControlByModule);
			echo "</textarea>";*/

			if (isset($_REQUEST["action"]) && $_REQUEST["action"] == "reset") {
				if ($question_id == "") {
					$this->resetAllUserDataForExamination($player_test_id);
					return;
				} else {
					$this->resetQuestionForExamination($player_test_id, $question_id, $paramsToControlByModule["oid"]);
				}
			}

			if (isset($_REQUEST["action"]) && ($_REQUEST["action"] == "lock" || $_REQUEST["action"] == "unlock")) {
				$this->lockUnlockUserExamination();
				return;
			}

			if (isset($_REQUEST["action"]) && $_REQUEST["action"] == "rfrTmr") {

				$this->evaluation_test_id = $paramsToControlByModule["oid"];
				if (isset($_REQUEST["timer_response"]))
					$timer_response = $_REQUEST["timer_response"];
				$this->refreshTimer($timer_response);
				$this->getTimerAverage();

				$f_name = NEMODULES_PATH . "learningEvaluation/TimerAverage.html";
				echo examinationBase::constructHtmlPage($f_name);
				return;
			}

			if (isset($_REQUEST["action"]) && $_REQUEST["action"] == "goToInit") {
				$f_name = NEMODULES_PATH . "learningEvaluation/EvaluationTestInit.html";
			} elseif (isset($_REQUEST["action"]) && $_REQUEST["action"] == "rfr") {

				if ($question_id != "") {

					$this->evaluation_test_id = $paramsToControlByModule["oid"];
					$this->controllTestInstance("rfrAfterReview", $question_id);

					$this->getProgressBarGrid();

					if ($this->evaluation_state_flag > 0 &&
						$this->stateInterfaceOfQuestion != "ready_to_be_evaluated"
						&& $this->stateInterfaceOfQuestion != "ready_to_be_finalized"
					) {

						WebApp::addVar("refreshQuestionResult", "yes");

						$this->getFullTestStructureToViewResults();
						$userResults["data"] = $this->report_assesement_user_Results["data"];
						$userResults["AllRecs"] = count($userResults["data"]);
						WebApp::addVar("userResultsGrid", $userResults);

						$this->getQuestionResult($question_id);

						$f_name = NEMODULES_PATH . "learningEvaluation/EvaluationTestResult.html";

					} elseif ($this->actual_question_state == "finished" && $this->flow_model["feedback_model"] != "sessionEnd") // &&
							$f_name = NEMODULES_PATH."learningEvaluation/".$this->templateDefaultQuestionResults;
					else    $f_name = NEMODULES_PATH."learningEvaluation/".$this->templateDefaultQuestion;

				} else {
					if ($this->ci_type_configuration == "RQ"
						|| $this->ci_type_configuration == "CQ"
						|| $this->ci_type_configuration == "ES"
					) {

						WebApp::addVar("firstPage", "yes");
						WebApp::addVar("refreshFromAjax", "yes");
						$f_name = NEMODULES_PATH . "learningEvaluation/EvaluationTestInit.html";
					}
				}

			} else {

				//	rfrTmr


				if (isset($_REQUEST["action"]) && ($_REQUEST["action"] == "goBack" || $_REQUEST["action"] == "goNext" || $_REQUEST["action"] == "goTo")) {
					$this->evaluation_test_id = $paramsToControlByModule["oid"];
					$this->controllTestInstance($_REQUEST["action"], $question_id);
				} else

				if (isset($_REQUEST["action"]) && ($_REQUEST["action"] == "init" || $_REQUEST["action"] == "nxtQuestion")) {
					if ($_REQUEST["action"] == "init" && ($this->player_flag == 0 || $this->player_flag == 10 || $this->player_flag == 20))
						$this->initEvaluation();
					$this->controllTestInstance("init");
				} else

				if (isset($_REQUEST["action"]) && ($_REQUEST["action"] == "finalize" || $_REQUEST["action"] == "finalizeFrc")) {
					$this->evaluation_test_id = $paramsToControlByModule["oid"];

					if ($_REQUEST["action"] == "finalizeFrc")
						$this->refreshTimerForced();
					$this->controllTestInstance("finalize", $question_id);
				} else

				if (isset($_REQUEST["action"]) && ($_REQUEST["action"] == "finalizeR")) {
					$this->evaluation_test_id = $paramsToControlByModule["oid"];
					$this->controllTestInstance($_REQUEST["action"], $question_id);
				} else

				if (isset($_REQUEST["action"]) && ($_REQUEST["action"] == "ViewResults")) {
					$this->evaluation_test_id = $paramsToControlByModule["oid"];
					$this->controllTestInstance($_REQUEST["action"], $question_id);
					WebApp::addVar("displayResultsOfOneEvaluation", "yes");
				}


				if (isset($_REQUEST["action"]) && $_REQUEST["action"] == "nxt") {

					$this->evaluation_test_id = $paramsToControlByModule["oid"];

					if (isset($_REQUEST["timer_response"]))
						$values_posted["timer_response"] = $_REQUEST["timer_response"];

					if (isset($paramsToControlByModule["usdTm"]))
						$values_posted["time_used"] = $paramsToControlByModule["usdTm"];

					if (isset($paramsToControlByModule["all_bck"]))
						$values_posted["all_bck"] = $paramsToControlByModule["all_bck"];

					if (isset($paramsToControlByModule["sID"]) && count($paramsToControlByModule["sID"]) > 0) {
						reset($paramsToControlByModule["sID"]);
						while (list($key, $questionId) = each($paramsToControlByModule["sID"]))
							$values_posted["questions"][$questionId] = $questionId;
					}
					//qid:1092
					$optionsFilled = 0;
					reset($paramsToControlByModule);
					while (list($key, $vl) = each($paramsToControlByModule)) {
						$temp = explode("_", $key);
						if ($temp["0"] == "opt") {
							$questionIDopt = $temp["1"];
							for ($i = 0; $i < count($vl); $i++) {
								$value = $vl[$i];
								$optionsFilled++;
								$elFormValues[$questionIDopt][$value] = $value;
							}
						}
					}


			/*echo "<textarea>";

			print_r($paramsToControlByModule);
			print_r($_REQUEST);

			print_r($values_posted);
			print_r($elFormValues);


			echo "</textarea>";*/              




				   //echo $optionsFilled."optionsFilled";
					if ($optionsFilled > 0) {
						$this->doSubmitInstance($values_posted, $elFormValues);
						if ($this->flow_model["feedback_model"] == "sessionEnd") {
							$question_id = $this->actual_question_id;
							$this->actual_question_id = "";
							$this->controllTestInstance("goNext", $question_id);
							WebApp::addVar("displayResultsOfOneEvaluation", "yes");
						}
					} else {
						//$question_id = $this->actual_question_id;
						$this->actual_question_id = "";
						$this->controllTestInstance("goNext", $paramsToControlByModule["qid"]);
						/*echo "<textarea>";
						print_r($paramsToControlByModule["qid"]);
						print_r($this->actual_question_id);
						echo "</textarea>";*/
					}
				}

				if ($this->evaluation_state_flag > 0 &&
					$this->stateInterfaceOfQuestion != "ready_to_be_evaluated"
					&& $this->stateInterfaceOfQuestion != "ready_to_be_finalized"
				) {
					$this->getFullTestStructureToViewResults();
					$userResults["data"] = $this->report_assesement_user_Results["data"];
					$userResults["AllRecs"] = count($userResults["data"]);
					WebApp::addVar("userResultsGrid", $userResults);

					$f_name = NEMODULES_PATH."learningEvaluation/EvaluationTestResult.html";
					if (isset($this->EvaluationState["surveyRelated"]["enabled"]) && $this->EvaluationState["surveyRelated"]["enabled"]=="yes") {
						WebApp::addVar("enableSurvey", 		"yes");				
						WebApp::addVar("targetSurvey", 		$this->EvaluationState["surveyRelated"]["target"]);				
						WebApp::addVar("survey_ci_title", 	$this->EvaluationState["surveyRelated"]["survey_ci_title"]);				
					}            
				} else {
					$this->getProgressBarGrid();
					if ($this->stateInterfaceOfQuestion == "ready_to_be_finalized" || $this->stateInterfaceOfQuestion == "ready_to_be_evaluated") {
						$f_name = NEMODULES_PATH . "learningEvaluation/ready_to_be_finalized.html";
						WebApp::addVar("isLastQuestion", "yes");
					} else {
						if ($this->actual_question_state == "finished" && $this->flow_model["feedback_model"] != "sessionEnd") {
							$f_name = NEMODULES_PATH."learningEvaluation/".$this->templateDefaultQuestionResults;
						} else {
							$f_name = NEMODULES_PATH."learningEvaluation/".$this->templateDefaultQuestion;
						}
					}
					WebApp::addVar("timerGlobalTest", "yes");
				}
			}
			WebApp::addVar("is_disabled", '');

			if (isset($this->stateInterfaceOfQuestion))
				WebApp::addVar("stateInterfaceOfQuestion", $this->stateInterfaceOfQuestion);
			/*echo "<textarea>";
			echo $session->Vars["uni"].":uni\n";
			echo $this->stateInterfaceOfQuestion.":stateInterfaceOfQuestion\n";
			echo $this->evaluation_test_state["test_state"].":test_state\n";

			echo $this->evaluation_test_id.":evaluation_test_id\n";
			echo $this->evaluation_state_flag.":evaluation_state_flag\n";
			echo $this->actual_question_state.":actual_question_state\n";
			echo $this->EvaluationTestContainer.":EvaluationTestContainer\n";
			print_r($paramsToControlByModule);
			print_r($session);
			print_r($objEccELrn);
			echo "</textarea>";*/

			$this->constructDetailsOfCiGrid($this->cidFlow);
			$this->AddNeddedVar();


			$Module_Html_inside = "<Include SRC=\"/" . $f_name . "\"/>";
			WebApp::addVar("IncludeModuleInside", $Module_Html_inside);
			
			/*echo "<textarea>";
			print_r($this->evaluation_test_state);	
			print_r($this->controllUserTestData);	
			print_r($this->debug);	
			echo "</textarea>";	*/		

			$f_name_container = NEMODULES_PATH . "learningEvaluation/".$this->EvaluationTestContainer;
			echo examinationBase::constructHtmlPage($f_name_container);

			include AUDIT_TRAIL_PATH."audit_trail.php";    	
    } 
    function constructGuideHtml()
    {
		global $session;
		$session->Vars["parseBox"] = "true";
		$session->Vars["callBox"] = "y";

        $tmp["data"] = array();
        $i = 0;
        if (isset($this->guideToHtmlToSave) && $this->guideToHtmlToSave>0) {
        while (list($indexG, $valueG) = each($this->guideToHtmlToSave)) {
            $tmp["data"][$i++]["guideLab"] = $valueG; //WebApp::replaceVars($valueG);
        }}

        $tmp["AllRecs"] = count($tmp["data"]);
        WebApp::addVar("AutomaticallyGuideToGrid", $tmp);
        $f_name_container = NEMODULES_PATH."learningEvaluation/AutomaticallyGuideToGrid.html";
        
		WebApp::collectHtmlPage();
		WebApp::constructHtmlPage($f_name_container);
		$guideHtml = WebApp::getHtmlPage();       
        return $guideHtml;
    }    
    
	function constructHtmlPage($f_name,$incMesg="yes",$head_file="")
	{
		global $session;
		
		$session->Vars["parseBox"] = "true";
		$session->Vars["callBox"] = "y";
		WebApp::collectHtmlPage();
		$messg_file = "";

		if ($incMesg=="yes") {
			if (isset($session->Vars["lang"])) {
				$lang_sel = strtoupper($session->Vars["lang"]) . "_Name";
				$name_lang = constant($lang_sel);
				if ($name_lang != "")
					$messg_file = TPL_PATH . $name_lang . ".mesg";
				else
					$messg_file = TPL_PATH . "messages.mesg";
			}
		}
		if(is_file($f_name) ) {
			WebApp::constructHtmlPage($f_name, $head_file, $messg_file);
			$contentForm = WebApp::getHtmlPage();
			return $contentForm;		
		} else {
			return "";
		}
	}    
    
  
    
    
    
}