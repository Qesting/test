<?php
    session_start();

    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
        header("location: ../login.php");
        exit;
    }
    require_once('../../config/config.php');

    $notice = $notice_class = "";

    $test = unserialize($_SESSION['edit_test']);

	if ($_SERVER['REQUEST_METHOD'] == "POST") {

		$ql = argStrip($_POST['ql']);

		$test->updateQuestions($ql);

		$_SESSION['notice'] = "s-Pomyślnie zaktualizowano pytania!";

		$_SESSION['edit_test'] = serialize($test);

		header('location: quest.php');
		exit;
	}

	$_POST = array();

    showNot();

?>

<!DOCTYPE html>
<html lang="pl-PL">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Edycja pytań</title>
        <link rel="stylesheet" href="../../style/main.css">
        <style>
            body{ font: 14px sans-serif; text-align: center;}
			.btn-del {
				position: relative;
				bottom: 3rem;
			}
        </style>
    </head>
    <body>
    <nav class="navbar navbar-expand navbar-dark bg-primary sticky-top">
            <div class="container-fluid">
            <a class="navbar-brand"><b>T</b>ESTOPOL</a>
                <div class="collapse navbar-collapse">
                    <div class="navbar-nav ms-auto">
                        <a class="nav-item nav-link active" href='test.php'>Powrót do wyboru testu</a>
                        <a class="nav-item nav-link active" href='../userpage.php'>Powrót do strony użytkownika</a>
                        <a class="nav-item nav-link active" href='../../index.php'>Powrót do strony głównej</a>
                    </div>
                </div>
            </div>
        </nav>
        <div class="wrapper">
            <h1 class="mt-5 mb-3">Wybrany test: <?php echo $test->testName; ?><span id='count'></span></h1>
            <div class="container">
                <nav class="nav nav-tabs my-3" id='nav-tabs'>
                    
                </nav>
                <div class='add'>
					<div class='card d-none' id='q0'>
    					<div class='card-header'>
    						<h4 class='mt-4'>Dodaj pytanie</h4>
    					</div>
    					<div class='card-body pt-4 px-4'>
      						<div class='form-group'>
        						<label class='form-label'>Treść pytania</label>
        						<input type='text' class='form-control' id='qContent' />
      						</div>
      						<div class='form-group'>
        						<label class='form-label'>Typ pytania</label>
        						<select type='text' class='form-control' id='qType'>
									<option value='0'>--Wybierz opcję--</option>
									<option value='1'>Jedokrotnego wyboru</option>
									<option value='2'>Wielokrotnego wyboru</option>
									<option value='3'>Tekstowe</option>
        						</select>
      						</div>
      						<div class='form-group'>
        						<button class='btn btn-primary' id='addQuestion'><span class='bi-file-plus'></span> Dodaj pytanie</button>
      						</div>
    					</div>
  					</div>
				</div>
                <form method='POST'>
					<div id='cards'>
					<?php

						$i = 1;

						foreach ($test->testQuestions as $question){

							$correct = $question->questionAnswer;

							$types = array('j. wybór', 'w. wybór', 'tekstowe');

							echo "<div class='card' id='q{$i}' data-type='{$question->questionType}'>
								<div class='card-header d-flex flex-column'>
									<h4 class='mt-4'>Pytanie {$i} ({$types[$question->questionType - 1]})</h4>
									<button type='button' class='btn btn-danger btn-del align-self-end'><span class='bi-file-minus'></span> Usuń pytanie</button>
								</div>
								<div class='card-body pt-4 px-4'>
								<input type='hidden' name='ql[{$i}][type]' value='{$question->questionType}' />
								<div class='form-group'>
									<label class='form-label'>Treść pytania</label>
									<input type='text' class='form-control' value='{$question->questionContent}' name='ql[{$i}][content]' />
								</div>
								<div class='form-group'>
									<label class='form-label'>Wartość odpowiedzi</label>
									<input type='number' min='1' max='5' value='1' class='form-control' name='ql[{$i}][points]' />
								</div>
								<div class='form-group'>
									<label class='form-label'>Odpowiedzi</label>";
								
									if (in_array($question->questionType, array(1, 2)))
									{
										$type = ($question->questionType == 1) ? "radio" : "checkbox";
										$nameEnd = ($question->questionType == 1) ? "" : "[]";

										for ($j = 0; $j < count($question->questionAnsList); $j++) {

											$answer = $question->questionAnsList[$j];
											$check = ($type == 1) ? (($j + 1 == $correct) ? "checked" : "") : ((in_array($j + 1, str_split($correct))) ? "checked" : "");

											echo "<div class='form-group'>
												<div class='input-group'>
													<span class='input-group-text'><input type='{$type}' {$check} name='ql[{$i}][answer]{$nameEnd}' value='". $j + 1 ."' /></span>
													<input type='text' value='{$answer}' name='ql[{$i}][answers][]' class='form-control' />";
													if ($j > 1) echo "<button type='button' class='btn btn-danger' onclick='deleteAnswer(getElementById(\"q{$i}\"),". $j + 1 .")'><span class='bi-dash-square'></span></button>";
										echo	"</div>
											</div>";

										}

									} else echo "<div class='form-group'><input type='text' name='ql[${i}][answer]' class='form-control' value='{$correct}' /></div>";


								echo "</div>
								<div class='form-group'>
									<div class='btn-group'>";
									
									if (in_array($question->questionType, array(1, 2))) echo "<button type='button' class='btn btn-secondary btn-block' onclick='addAnswer(document.getElementById(\"q{$i}\"))' type='button'><span class='bi-plus-square'></span> Dodaj odpowiedź</button>";

							echo	"</div>
								</div>
							</div>
							</div>";

							$i++;
						}
					?>
					</div>
					<div class='btn-group mt-4'>
						<button class='btn btn-secondary' type='button' id='prev'><span class='bi-caret-left-fill'></span> Poprzednie pytanie</button>
						<button class='btn btn-primary' type='button' id='add'><span class='bi-file-plus'></span> Dodaj pytanie</button>
						<button class='btn btn-secondary' type='button' id='next'>Następne pytanie <span class='bi-caret-right-fill'></span></button>
					</div>
					<div class='my-4'>
						<div class="btn-group mb-2">
							<button id='testmod' type='button' class="btn btn-secondary"><span class='bi-pencil-square'></span> Modyfikuj test</button>
							<button id='testimg' type='button' class="btn btn-secondary"><span class='bi-card-image'></span> Dodaj obraz</button>
							<button type='submit' class='btn btn-primary'><span class='bi-save2'></span> Zapisz zmiany</button>
							<button id="testdel" type='button' class="btn btn-danger"><span class='bi-bookmark-dash'></span> Usuń bieżący test</button>
						</div>
					</div>
				</form>
				
				<p id='error' class='<?php echo $notice_class ?>'><?php echo $notice ?></p>
            </div>
        </div>
		<script type='text/javascript' src='../../js/questMan.js'></script>
    </body>        
</html>