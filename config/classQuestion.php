<?php

    class question {

        // właściwości
        public $questionId = 0;             // id
        public $questionContent = "";       // treść
        public $questionType = 0;           // typ
        public $questionPoints = 0;         // ilość punktów
        public $questionAnswer = "";        // prawidłowa odpowiedź 
        public $questionAnsList = array();  // lista odpowiedzi
        public $questionImgPath = "";       // ścieżka do obrazu        // Gothic wszedł za mocno i przeczytałem "ścieżka do obozu"

        // metody

        // statyczne
        public static function get($id) {
            $subject = new self;
            $subject->getQuestion($id);
            return $subject;
        }

        public static function set($test, $content, $type, $answer, $list) {
            $subject = new self;
            $subject->addQuestion($test, $content, $type, $answer, $list);
            return $subject;
        }
        
        // pobierz informacje o pytaniu
        private function getQuestion($_id) {
            $_link = dbConnect();

            $_stmt = $_link->prepare("SELECT content, quest_type, ans, ans_text, points, img_path FROM question WHERE id=?");
            $_stmt->bind_param('i', $_id);
            $_stmt->execute();
            $_sql = $_stmt->get_result();

            $_result = $_sql->fetch_assoc();
            $_stmt->close();

            $this->questionId = $_id;
            $this->questionType = $_result['quest_type'];
            $this->questionContent = $_result['content'];
            $this->questionPoints = $_result['points'];
            $this->questionImgPath = $_result['img_path'];

            // zerowanie na wszelki wypadek
            $this->questionAnsList = array();

            if ($this->questionType != 3) {

                $this->questionAnswer = $_result['ans'];

                $_stmt = $_link->prepare("SELECT content FROM answer WHERE quest_id=?");
                $_stmt->bind_param('i', $_id);
                $_stmt->execute();
                $_sql = $_stmt->get_result();

                while ($_row = $_sql->fetch_assoc()) {
                    $this->questionAnsList[] = $_row['content'];
                }
                $_stmt->close();

            } else $this->questionAnswer = $_result['ans_text'];

            $_link->close();
        }

        // dodaj odpowiedź
        public function addAnswer($_content) {

            $_num = count($this->questionAnsList) + 1;

            if ($_num <= 6) {

                $_link = dbConnect();

                $_stmt = $_link->prepare("INSERT INTO answer (quest_id, ans_id, content) VALUES (?, ?, ?)");
                $_stmt->bind_param('iis', $this->questionId, $_num, $_content);
                $_stmt->execute();

                $_stmt->close();
                $_link->close();

                $this->questionAnsList[] = $_content;

            }

        }

        // usuń odpowiedź
        public function deleteAnswer() {

            $_num = count($this->questionAnsList);

            if ($_num > 2) {

                $_link = dbConnect();

                $_stmt = $_link->prepare("DELETE FROM answer WHERE quest_id=? AND ans_id=?");
                $_stmt->bind_param('ii', $this->questionId, $_num);
                $_stmt->execute();

                $_stmt->close();
                $_link->close();

                array_pop($this->questionAnsList);
            }
        }

        // zaktualizuj odpowiedzi
        public function updateAnswers($_list) {

            if (is_array($_list) && isNotMultidimensional($_list)) {

                // więcej odpowiedzi
                if (count($_list) > count($this->questionAnsList) && count($_list) <= 6) {

                    $_newAnswers = $_list;
                    array_splice($_newAnswers, 0, count($this->questionAnsList));

                    foreach ($_newAnswers as $_val) {
                        $this->addAnswer($_val);
                    }

                // mniej odpowiedzi
                } else if (count($_list) < count($this->questionAnsList) && count($_list) >= 2) {

                    $_num = count($this->questionAnsList) - count($_list);
                    for ($i = 0; $i < $_num; $i++) $this->deleteAnswer();
                }

                // zaktualizuj tablicę odpowiedzi
                if ($_list !== $this->questionAnsList) {

                    $_link = dbConnect();

                    $_stmt = $_link->prepare("UPDATE answer SET content=? WHERE quest_id=? AND ans_id=?");

                    for ($i = 0; $i < count($_list); $i++) {
                        $j = $i+1;
                        $_stmt->bind_param('sii', $_list[$i], $this->questionId, $j);
                        $_stmt->execute();
                    }
                    $_stmt->close();
                    $_link->close();

                }
                
            }
            
        }

        // zaktualizuj pytanie
        public function updateQuestion($_content, $_answer, $_points, $_answers, $_type) {

            $_link = dbConnect();

            $_content = (empty($_content)) ? $this->questionContent : $_content;
            $_answer = (empty($_answer)) ? $this->questionAnswer : $_answer;
            $_points = (empty($_points)) ? $this->questionPoints : $_points;

            if ($_type == $this->questionType || (($this->questionType == 1 && $_type == 2) || ($this->questionType == 2 && $_type == 1))) { // A + (BC + DE)

                if ($this->questionType == 3) {

                    $_stmt = $_link->prepare("UPDATE question SET content=?, ans_text=?, points=?, quest_type=? WHERE id=?");
                    $_stmt->bind_param('ssiii', $_content, $_answer, $_points, $_type, $this->questionId);
                    $_stmt->execute();
                    $_stmt->close();
    
                } else {
    
                    $_stmt = $_link->prepare("UPDATE question SET content=?, ans=?, points=?, quest_type=? WHERE id=?");
                    $_stmt->bind_param('ssiii', $_content, $_answer, $_points, $_type, $this->questionId);
                    $_stmt->execute();
                    $_stmt->close();
    
                    $this->updateAnswers($_answers);
                }

            } else {

                if ($this->questionType != 3 && $_type == 3) {

                    $_stmt = $_link->prepare("DELETE FROM answer WHERE quest_id = ?");
                    $_stmt->bind_param('i', $this->questionId);
                    $_stmt->execute();
                    $_stmt->close();

                    $this->questionAnsList = array();

                    $_stmt = $_link->prepare("UPDATE question SET quest_type = ? WHERE id = ?");
                    $_stmt->bind_param('ii', $_type, $this->questionId);
                    $_stmt->execute();
                    $_stmt->close();

                    $this->questionType = $_type;

                    $this->updateQuestion($_content, $_answer, $_points, $_answers, $_type);

                } else {

                    $_stmt = $_link->prepare("UPDATE question SET quest_type = ? WHERE id = ?");
                    $_stmt->bind_param('ii', $_type, $this->questionId);
                    $_stmt->execute();
                    $_stmt->close();

                    $this->questionType = $_type;

                    $this->updateAnswers($_answers);
                    $this->updateQuestion($_content, $_answer, $_points, $_answers, $_type);

                }

            }

            $_link->close();

            $this->getQuestion($this->questionId);

        }

        // dodaj pytanie
        private function addQuestion($_test, $_content, $_type, $_answer, $_list, $_points) {

            $_link = dbConnect();

            if ($_type == 3) {

                $_stmt = $_link->prepare("INSERT INTO question (test_id, content, ans_text, quest_type, points) VALUES (?, ?, ?, '3', ?)");
                $_stmt->bind_param('issi', $_test, $_content, $_answer, $_points);
                $_stmt->execute();
                
                $this->questionId = $_stmt->insert_id;
                $_stmt->close();

                $this->getQuestion($this->questionId);

            } else if (is_array($_list)) {

                $_stmt = $_link->prepare("INSERT INTO question (test_id, content, ans, quest_type, points) VALUES (?, ?, ?, ?, ?)");
                $_stmt->bind_param('issii', $_test, $_content, $_answer, $_type, $_points);
                $_stmt->execute();
                
                $this->questionId = $_stmt->insert_id;
                $_stmt->close();

                foreach ($_list as $_element) {
                    
                    $this->addAnswer($_element);
                    
                }
                
                $this->getQuestion($this->questionId);
            }

            $_link->close();

        }

        public function setPath(string $path) :bool {

            $this->questionImgPath = $path;

            $_link = dbConnect();
            $_stmt = $_link->prepare("UPDATE question SET img_path=? WHERE id=?");
            $_stmt->bind_param('si', $path, $this->questionId);
            $_stmt->execute();

            if (!$_stmt) return false;

            $_stmt->close();
            $_link->close();

            return true;
        }
    }