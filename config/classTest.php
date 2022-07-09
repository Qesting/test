<?php 

    class test {

        // właściwości
        public $testId = 0;                 // id testu
        public $testName = "";              // nazwa testu
        public $testTime = 0;               // czas per pytanie
        public $testLAA = 0;                // czy można patrzyć na odpowiedzi
        public $testCT = 0;                 // czy można podejść do testu
        public $testVert = 0;               // czy test jest przewijalny
        public $testPoints = 0;

        public $testQuestions = array();    // lista pytań

        // metody

        public static function get($id) {
            $subject = new self;
            $subject->getTest($id);
            return $subject;
        }

        public static function set($module, $name) {
            $link = dbConnect();
            $stmt = $link->prepare("INSERT INTO test (name, module_id) VALUES (?, ?)");
            $stmt->bind_param('si', $name, $module);
            $stmt->execute();

            $id = $stmt->insert_id;

            $stmt->close();
            $link->close();

            return self::get($id);
        }

        // pobierz informacje o teście
        private function getTest($_id) {

            $_link = dbConnect();

            $_stmt = $_link->prepare("SELECT * FROM test WHERE id=?");
            $_stmt->bind_param('i', $_id);
            $_stmt->execute();
            $_sql = $_stmt->get_result();
            $_stmt->close();

            $_res = $_sql->fetch_assoc();

            $this->testId = $_id;
            $this->testName = $_res['name'];
            $this->testTime = $_res['time'];
            $this->testVert = $_res['vert'];
            $this->testLAA = $_res['can_laa'];
            $this->testCT = $_res['can_take'];

            $this->testQuestions = array();

            $_stmt = $_link->prepare("SELECT id FROM question WHERE test_id=?");
            $_stmt->bind_param('i', $_id);
            $_stmt->execute();
            $_sql = $_stmt->get_result();
            $_stmt->close();

            while ($_row = $_sql->fetch_assoc()) {

                $newQ = question::get($_row['id']);

                $this->testPoints += ($newQ->questionType == 2) ? count($newQ->questionAnsList) * $newQ->questionPoints : $newQ->questionPoints;
                $this->testQuestions[] = $newQ;

            }

        }

        // usuń pytanie
        public function deleteQuestion($_num) {

            $_delete = array_splice($this->testQuestions, $_num - 1, 1)[0];
            
            $_id = $_delete->questionId;

            $_link = dbConnect();
            $_stmt = $_link->prepare("DELETE FROM question WHERE id=?");
            $_stmt->bind_param('i', $_id);
            $_stmt->execute();
            $_stmt->close();
            $_link->close();

        }

        // dodaj pytanie
        public function addQuestion($_content, $_type, $_answer, $_list) {

            $this->testQuestions[] = question::set($this->testId, $_content, $_type, $_answer, $_list);

        }

        // zaktualizuj pytanie
        public function updateQuestion($_num, $_content, $_answer, $_points, $_answers, $_type) {
            $_num--;
            $this->testQuestions[$_num]->updateQuestion($_content, $_answer, $_points, $_answers, $_type);
        }

        // zaktualizuj test
        public function updateTest($_time, $_vert, $_laa, $_ct, $_ql) {

            $_link = dbConnect();
            $_stmt = $_link->prepare("UPDATE test SET time=?, vert=?, can_laa=?, can_take=? WHERE id=?");
            $_stmt->bind_param('iiiii', $_time, $_vert, $_laa, $_ct, $this->testId);

            $content = $answer = "";
            $type = $points = 0;
            $answers = array();

            if (count($_ql) < count($this->testQuestions)) array_splice($this->testQuestions, count($_ql), count($this->testQuestions) - count($_ql));
            for ($i = 0; $i < count($_ql); $i++) {

                $question = $_ql[$i];

                $content = $question['content'];
                $answer = $question['answer'];
                $type = $question['type'];
                $points = $question['points'];
                $answers = $question['answers'];

                if ($this->testQuestions[$i] instanceof question) $this->updateQuestion($i + 1, $content, $answer, $points, $answers, $type);
                else $this->addQuestion($content, $type, $answer, $answers); 

            }
        }

         // usuń test
        public function deleteTest() {
            
            $_link = dbConnect();
            $_stmt = $_link->prepare("DELETE FROM test WHERE id=?");
            $_stmt->bind_param('i', $this->testId);
            $_stmt->execute();

            $_stmt->close();
            $_link->close();

        }

    }

?>