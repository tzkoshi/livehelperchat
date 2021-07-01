<?php

class erLhcoreClassChatExport {

	public static function chatExportXML(erLhcoreClassModelChat $chat) {
		$tpl = new erLhcoreClassTemplate('lhexport/xml.tpl.php');
		$tpl->set('chat', $chat);
		return $tpl->fetch();
	}

	public static function chatExportJSON(erLhcoreClassModelChat $chat) {
		$tpl = new erLhcoreClassTemplate('lhexport/json.tpl.php');
		$tpl->set('chat', $chat);
		return $tpl->fetch();
	}

	public static function exportCannedMessages($messages) {
        $filename = "canned-messages-".date('Y-m-d').".csv";
        $fp = fopen('php://output', 'w');

        header('Content-type: application/csv');
        header('Content-Disposition: attachment; filename='.$filename);

        $counter = 0;
        foreach ($messages as $message) {
            $values = $message->getState();
            $values['tags_plain'] = $message->tags_plain;
            $values['department_ids_front'] = implode(',',$message->department_ids_front);
            if ($counter == 0) {
                fputcsv($fp, array_keys($values));
            }
            fputcsv($fp, $values);
            $counter++;
        }
        exit;
    }

	public static function exportDepartmentStats($departments) {
	    include 'lib/core/lhform/PHPExcel.php';
			$cacheMethod = PHPExcel_CachedObjectStorageFactory::cache_to_phpTemp;
			$cacheSettings = array( 'memoryCacheSize ' => '64MB');
			PHPExcel_Settings::setCacheStorageMethod($cacheMethod, $cacheSettings);

	    $objPHPExcel = new PHPExcel();
	    $objPHPExcel->setActiveSheetIndex(0);
	    $objPHPExcel->getActiveSheet()->getStyle('A1:AW1')->getFont()->setBold(true);
	    $objPHPExcel->getActiveSheet()->setTitle('Report');
	    
	    $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(0, 1, "ID");
	    $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(1, 1, erTranslationClassLhTranslation::getInstance()->getTranslation('chat/chatexport','Department name'));
	    $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(2, 1, erTranslationClassLhTranslation::getInstance()->getTranslation('chat/chatexport','Pending chats number'));
	    $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(3, 1, erTranslationClassLhTranslation::getInstance()->getTranslation('chat/chatexport','Active chats number'));
	    
	    $attributes = array(
	        'id',
	        'name',
	        'pending_chats_counter',
	        'active_chats_counter',
	    );
	    
	    $i = 2;
	    foreach ($departments as $item) {
	        foreach ($attributes as $key => $attr) {
	            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($key, $i, (string)$item->{$attr});
	        }
	        $i++;
	    }
	    
	    $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
	    
	    // We'll be outputting an excel file
	    header('Content-type: application/vnd.ms-excel');
	    
	    // It will be called file.xls
	    header('Content-Disposition: attachment; filename="report.xlsx"');
	    
	    // Write file to the browser
	    $objWriter->save('php://output');
	}
	
	public static function chatListExportXLS($chats, $params = array()) {

		include 'lib/core/lhform/PHPExcel.php';
		$cacheMethod = PHPExcel_CachedObjectStorageFactory::cache_to_phpTemp;
		$cacheSettings = array( 'memoryCacheSize ' => '64MB');
		PHPExcel_Settings::setCacheStorageMethod($cacheMethod, $cacheSettings);
		
		$chatArray = array();
		
		$id = "ID";
		$name = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/chatexport','Visitor Name');
		$email = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/chatexport','E-mail');
		$phone = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/chatexport','Phone');
		$wait = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/chatexport','Wait time');
		$country = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/chatexport','Country');
		$countryCode = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/chatexport','Country Code');
		$city = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/chatexport','City');
		$ip = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/chatexport','IP');
		$operator = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/chatexport','Operator');
		$operatorName = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/chatexport','Operator Name');
		$dept = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/chatexport','Department');
		$date = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/chatexport','Date');
		$minutes = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/chatexport','Minutes');
		$vote = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/chatexport','Vote status');
		$subjects = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/chatexport','Subjects');
		$mail = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/chatexport','Mail send');
		$page = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/chatexport','Page');
		$from = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/chatexport','Came from');
		$link = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/chatexport','Link');
		$remarks = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/chatexport','Remarks');
		$device = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/chatexport','Device');
		$visitorID = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/chatexport','Visitor ID');
		$duration = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/chatexport','Duration');
		$chat_initiator = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/chatexport','Started by');
		$browser = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/chatexport','Browser');
        $user_id_op = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/chatexport','User ID');
        $referrer = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/chatexport','Chat start page');      // Page visitor started a chat
        $session_referrer = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/chatexport','Referer page'); // Page from which visitor come to website
        $chat_start_time = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/chatexport','Chat start time');
        $chat_end_time = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/chatexport','Chat end time');
        $is_unread = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/chatexport','Is unread by operator');
        $is_unread_visitor = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/chatexport','Is unread by visitor');
        $is_abandoned = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/chatexport','Is abandoned');
        $bot = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/chatexport','Bot');

		$additionalDataPlain = array();
		for ($i = 1; $i <= 20; $i++) {
            $additionalDataPlain[] = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/chatexport','Additional data').' - '.$i;
        }

		$additionalData = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/chatexport','Additional data');

        $survey = array();
        for ($i = 1; $i <= 20; $i++) {
            $survey[] = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/chatexport','Survey data').' - '.$i;
        }

        $surveyData = array();

		$mainColumns = array($id, $name, $email, $phone, $wait, $country, $countryCode, $city, $ip, $operator, $operatorName, $user_id_op, $dept, $date, $minutes, $vote, $mail, $page, $from, $link, $remarks, $subjects, $is_unread, $is_unread_visitor, $is_abandoned, $bot, $device, $visitorID, $duration, $chat_initiator, $browser, $referrer, $session_referrer, $chat_start_time, $chat_end_time);

		if (isset($params['type']) && in_array(2,$params['type'])) {
            $mainColumns[] = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/chatexport','Chat content');
        }

        if (isset($params['type']) && in_array(4,$params['type'])) {
            $mainColumns[] = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/chatexport','Messages');
            $mainColumns[] = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/chatexport','Visitor messages');
            $mainColumns[] = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/chatexport','Bot messages');
            $mainColumns[] = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/chatexport','Operator messages');
            $mainColumns[] = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/chatexport','System messages');
            $mainColumns[] = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/chatexport','Visitor messages to bot');
            $mainColumns[] = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/chatexport','Visitor messages to operator');

            $mainColumns[] = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/chatexport','Maximum agent response time');
            $mainColumns[] = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/chatexport','Maximum bot response time');
            $mainColumns[] = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/chatexport','Average agent response time');
            $mainColumns[] = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/chatexport','Average bot response time');
            $mainColumns[] = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/chatexport','First agent response time');
            $mainColumns[] = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/chatexport','First bot response time');
            $mainColumns[] = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/chatexport','Wait time till first operator message');
        }

        if (isset($params['type']) && in_array(5,$params['type'])) {
            $mainColumns[] = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/chatexport','Subject');
        }

        if (isset($params['type']) && in_array(6, $params['type'])) {
            $chatVariables = erLhAbstractModelChatVariable::getList();
            foreach ($chatVariables as $chatVariable){
                $mainColumns[] = $chatVariable->var_name;
            }
        }

		if (isset($params['type']) && in_array(3,$params['type'])) {
            $mainColumns = array_merge($mainColumns,$survey);
            $surveyData = erLhAbstractModelSurveyItem::getList(array_merge(array('filterin' => array('chat_id' => array_keys($chats)), 'offset' => 0, 'limit' => 100000)));
        }

        $chatArray[] = array_merge($mainColumns, $additionalDataPlain, array($additionalData));

        $exportChatData = array();
        foreach ($surveyData as $surveyItem)
        {
            $survey = erLhAbstractModelSurvey::fetch($surveyItem->survey_id);
            $exported = erLhcoreClassSurveyExporter::exportRAW(array($surveyItem),$survey);

            $pairs = array_fill(0,20,'');

            $i = 0;
            foreach ($exported['value'] as $chatId => $valueItems) {
                foreach ($exported['title'] as $indexColumn => $columnName) {
                    $pairs[$i] = $columnName . ' - ' . $valueItems[$indexColumn];
                    $i++;
                }
            }

            $exportChatData[$surveyItem->chat_id] = $pairs;
        }

        foreach ($chats as $item) {
                $id = (string)$item->{'id'};
                $nick = (string)$item->{'nick'};
                $email = (string)$item->{'email'};
                $phone = (string)$item->{'phone'};
                $wait = (string)$item->{'wait_time'};
                $country = (string)$item->{'country_name'};
                $countryCode = (string)$item->{'country_code'};
                $city = (string)$item->{'city'};
                $ip = (string)$item->{'ip'};
                $user = (string)$item->{'user'};
                $operatorName = (string)$item->{'n_off_full'};
                $user_id_op = (string)$item->{'user_id'};
                $dept = (string)$item->{'department'};
                $remarks = (string)$item->{'remarks'};
                $device = (string)$item->{'device_type'} == 0 ? erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Computer') : ((string)$item->{'device_type'} == 1 ? erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Mobile') : erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Tablet'));
                $visitorID = (string)$item->online_user_id;
                $duration = (string)$item->chat_duration;
                $chat_initiator = $item->chat_initiator == erLhcoreClassModelChat::CHAT_INITIATOR_DEFAULT ? 'visitor' : 'proactive';
                $browser = (string)$item->uagent;
                $referrer = (string)$item->referrer;
                $session_referrer = (string)$item->session_referrer;
                $chat_start_time = date('Y-m-d H:i:s',$item->time);
                $chat_end_time = $item->cls_time > 0 ? date('Y-m-d H:i:s',$item->cls_time) : '';

                $subjects = implode(',',erLhAbstractModelSubjectChat::getList(array('filter' => array('chat_id' => $item->id))));
                $is_unread = (int)$item->has_unread_messages;
                $is_unread_visitor = (int)$item->has_unread_op_messages;
                $is_abandoned = (int)(($item->user_id == 0 && in_array($item->status_sub,[ erLhcoreClassModelChat::STATUS_SUB_USER_CLOSED_CHAT , erLhcoreClassModelChat::STATUS_SUB_SURVEY_COMPLETED])) || ($item->lsync < ($item->pnd_time + $item->wait_time)));
                $bot = (string)$item->bot;

                $date = date(erLhcoreClassModule::$dateFormat,$item->time);
                $minutes = date('H:i:s',$item->time);
                $vote = ($item->fbst == 1 ? 'UP' : ($item->fbst == 2 ? 'DOWN' : 'NONE'));
                $mail = $item->mail_send == 1 ? 'Yes' : 'No';
                $page = $item->referrer;
                $additionalDataContent = $item->additional_data;

                // Create empty array of 20 to make sure all are filled
                $urlData = array();
                $pairsRegular = array();
                if (!empty($additionalDataContent)) {
                    foreach (json_decode($additionalDataContent,true) as $index => $additionalItem) {
                        if (isset($additionalItem['url']) && $additionalItem['url'] == true) {
                            $urlData[] = $additionalItem['key'] . ' - ' . $additionalItem['value'];
                        } else {
                            $pairsRegular[] = (isset($additionalItem['key']) ? $additionalItem['key'] : '') . ' - ' . (isset($additionalItem['value']) ? $additionalItem['value'] : '');
                        }

                    }
                }
                       
                // Put URL arguments always first
                $additionalPairs = array_merge($urlData,$pairsRegular);
                $additionalPairs = array_merge($additionalPairs,array_fill(count($additionalPairs),20-count($additionalPairs),''));

                if ($item->session_referrer != '') {
                        $referer = parse_url($item->session_referrer);                    
                        if (isset($referer['host'])) {
                            $from = $referer['host'];
                        } else {
                        	$from = null;
                        }
                } else {
                	$from = null;
                }

                $url = erLhcoreClassXMP::getBaseHost() . $_SERVER['HTTP_HOST'] . erLhcoreClassDesign::baseurl('user/login').'/(r)/'.rawurlencode(base64_encode('chat/single/'.$item->id));

                $itemData = array($id, $nick, $email, $phone, $wait, $country, $countryCode, $city, $ip, $user, $operatorName, $user_id_op, $dept, $date, $minutes, $vote, $mail, $page, $from, $url, $remarks, $subjects, $is_unread, $is_unread_visitor, $is_abandoned, $bot, $device, $visitorID, $duration, $chat_initiator, $browser, $referrer, $session_referrer, $chat_start_time, $chat_end_time);

                // Print chat content to last column
                if (isset($params['type']) && in_array(2,$params['type'])) {

                    $messages = erLhcoreClassModelmsg::getList(array('limit' => 10000,'sort' => 'id ASC','filter' => array('chat_id' => $item->id)));                       
                    $messagesContent = '';

                    foreach ($messages as $msg ) {
                        if ($msg->user_id == -1) {
                                $messagesContent .= date(erLhcoreClassModule::$dateDateHourFormat,$msg->time).' '. erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncadmin','System assistant').': '.htmlspecialchars($msg->msg)."\n";
                        } else {
                                $messagesContent .= date(erLhcoreClassModule::$dateDateHourFormat,$msg->time).' '. ($msg->user_id == 0 ? htmlspecialchars($item->nick) : htmlspecialchars($msg->name_support)).': '.htmlspecialchars($msg->msg)."\n";
                        }
                    }
                    $itemData[] = trim($messagesContent);
                }

                if (isset($chatVariables)) {
                    foreach ($chatVariables as $chatVariable) {
                        if ($chatVariable->inv == true) {
                            $chatVariablesPassed = $item->chat_variables_array;
                        } else {
                            foreach ($item->additional_data_array as $chatVariablePassed) {
                                $chatVariablesPassed[$chatVariablePassed['identifier']] = $chatVariablePassed['value'];
                            }
                        }

                        $valueVariable = '';

                        if (isset($chatVariablesPassed[$chatVariable->var_identifier])){
                            $valueVariable = $chatVariablesPassed[$chatVariable->var_identifier];
                        }

                        $itemData[] = $valueVariable;
                    }
                }

                if (isset($params['type']) && in_array(4,$params['type'])) {
                    $itemData[] = erLhcoreClassModelmsg::getCount(array('limit' => false,'filter' => array('chat_id' => $item->id))); // Total messages
                    $visitorMessagesCount = erLhcoreClassModelmsg::getCount(array('limit' => false,'filter' => array('user_id' => 0, 'chat_id' => $item->id)));
                    $itemData[] =  $visitorMessagesCount; // Visitor messages
                    $itemData[] = erLhcoreClassModelmsg::getCount(array('limit' => false,'filter' => array('user_id' => -2, 'chat_id' => $item->id))); // Bot messages
                    $itemData[] = erLhcoreClassModelmsg::getCount(array('limit' => false,'filtergt' => array('user_id' => 0),'filter' => array('chat_id' => $item->id))); // Operator messages
                    $itemData[] = erLhcoreClassModelmsg::getCount(array('limit' => false,'filter' => array('user_id' => -1,'chat_id' => $item->id))); // System messages
                    // We have a bot assigned
                    // Chat does not have an operator OR it has operator and message time is less than chat become pending
                    $visitorMessagesBotCount = 0;
                    $botMessages = [];
                    $agentMessages = [];

                    if ($item->gbot_id > 0) {
                        // All visitor messages were interactions with bot
                        if ($item->user_id == 0) {
                            $visitorMessagesBotCount = $visitorMessagesCount;
                            $itemData[] = $visitorMessagesBotCount;
                            // All interactions were with a bot
                            $botMessages = erLhcoreClassModelmsg::getList(array('limit' => false, 'filter' => array('chat_id' => $item->id)));
                        } else {
                            $botMessages = erLhcoreClassModelmsg::getList(array('limit' => false, 'filterlte' => array('time' => $item->pnd_time),'filter' => array('chat_id' => $item->id)));
                            $agentMessages =  erLhcoreClassModelmsg::getList(array('limit' => false, 'filtergt' => array('time' => $item->pnd_time),'filter' => array('chat_id' => $item->id)));
                            $visitorMessagesBotCount = erLhcoreClassModelmsg::getCount(array('limit' => false, 'filterlte' => array('time' => $item->pnd_time),'filter' => array('user_id' => 0, 'chat_id' => $item->id)));
                            $itemData[] = $visitorMessagesBotCount;
                        }
                    } else { // There was no bot assigned
                        $itemData[] = 0;
                        $agentMessages = erLhcoreClassModelmsg::getList(array('limit' => false, 'filter' => array('chat_id' => $item->id)));
                    }

                    $itemData[] = $visitorMessagesCount - $visitorMessagesBotCount;

                    $timesResponse = [];
                    $startTime = 0;
                    $firstBotResponseTime = 0;

                    foreach ($botMessages as $messageWithABot) {
                        if ($messageWithABot->user_id == 0) {
                            if ($startTime == 0) {
                                $startTime = $messageWithABot->time;
                            }
                        } elseif ($messageWithABot->user_id == -2) {
                            if ($startTime > 0) {
                                if (empty($timesResponse)){
                                    $firstBotResponseTime = $messageWithABot->time - $startTime;
                                    $timesResponse[] = $firstBotResponseTime;
                                } else {
                                    $timesResponse[] = $messageWithABot->time - $startTime;
                                }

                                $startTime = 0;
                            }
                        }
                    }

                    $tillFirstOperatorMessage = 0;
                    $firstAgentResponseTime = 0;
                    $timesResponseAgent = [];
                    $startTime = $item->pnd_time;
                    foreach ($agentMessages as $agentMessage) {
                        if ($agentMessage->user_id == 0) {
                            if ($startTime == 0) {
                                $startTime = $agentMessage->time;
                            }
                        } elseif ($agentMessage->user_id > 0) {
                            if ($tillFirstOperatorMessage == 0) {
                                $tillFirstOperatorMessage = $agentMessage->time - $item->pnd_time;
                            }
                            if ($startTime > 0) {
                                // It's first agent response
                                if (empty($timesResponseAgent)) {
                                    $firstAgentResponseTime = $agentMessage->time - ($item->wait_time + $item->pnd_time);
                                    $timesResponseAgent[] = $firstAgentResponseTime;
                                } else {
                                    $timesResponseAgent[] = $agentMessage->time - $startTime;
                                }
                                $startTime = 0;
                            }
                        }
                    }


                    $itemData[] = max($timesResponseAgent);
                    $itemData[] = max($timesResponse);
                    $itemData[] = array_sum($timesResponseAgent)/count($timesResponseAgent);
                    $itemData[] = array_sum($timesResponse)/count($timesResponse);
                    $itemData[] = $firstAgentResponseTime;
                    $itemData[] = $firstBotResponseTime;
                    $itemData[] = $tillFirstOperatorMessage;
                }

                if (isset($params['type']) && in_array(5,$params['type'])) {
                    $subjects = erLhAbstractModelSubjectChat::getList(array('filter' => array('chat_id' => $item->id)));
                    $subjectValue = [];
                    foreach ($subjects as $subject) {
                        $subjectValue[] = (string)$subject->subject;
                    }
                    $itemData[] = implode("\n",$subjectValue);
                }

                if (isset($params['type']) && in_array(3,$params['type'])) {
                    $itemData = array_merge($itemData, isset($exportChatData[$item->id]) ? $exportChatData[$item->id] : array_fill(0,20,''));
                }

                $itemData = array_merge($itemData, $additionalPairs, array($additionalDataContent));

                $chatArray[] = $itemData;
        }

        if ($params['csv'] && $params['csv'] == true) {

            $now = gmdate("D, d M Y H:i:s");
            header("Expires: Tue, 03 Jul 2001 06:00:00 GMT");
            header("Cache-Control: max-age=0, no-cache, must-revalidate, proxy-revalidate");
            header("Last-Modified: {$now} GMT");

            // force download
            header("Content-Type: application/force-download");
            header("Content-Type: application/octet-stream");
            header("Content-Type: application/download");

            // disposition / encoding on response body
            header("Content-Disposition: attachment;filename=report.csv");
            header("Content-Transfer-Encoding: binary");

            $df = fopen("php://output", 'w');
            /*fputcsv($df, array_keys(reset($array)));*/
            foreach ($chatArray as $row) {
                fputcsv($df, $row);
            }
            fclose($df);

        } else {
            // Create new PHPExcel object
            $objPHPExcel = new PHPExcel();
            $objPHPExcel->setActiveSheetIndex(0);

            // Set the starting point and array of data
            $objPHPExcel->getActiveSheet()->fromArray($chatArray, null, 'A1');

            // Set style for top row
            $objPHPExcel->getActiveSheet()->getStyle('A1:AW1')->getFont()->setBold(true);

            // Set file type and name of file
            header('Content-Type: application/vnd.ms-excel');
            header('Content-Disposition: attachment;filename="report.xlsx"');
            header('Cache-Control: max-age=0');

            $writer = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');

            $writer->save('php://output');
        }
	}
}

?>
