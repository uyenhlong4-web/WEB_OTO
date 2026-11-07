<?php

//default
$host = "localhost";
$user = "root";      
$pass = "";          
$db   = "quiz_db";   


$conn = new mysqli($host, $user, $pass, $db);
$conn->set_charset("utf8");

$sql = "SELECT q.id as question_id, q.question_text, 
               a.id as answer_id, a.answer_text, a.is_correct
        FROM questions q
        JOIN answers a ON q.id = a.question_id
        ORDER BY q.id";

$result = $conn->query($sql);

$questions = [];

while ($row = $result->fetch_assoc()) {
    $qid = $row["question_id"];
    if (!isset($questions[$qid])) {
        $questions[$qid] = [
            "id" => $qid,
            "question_text" => $row["question_text"],
            "answers" => []
        ];
    }
    $questions[$qid]["answers"][] = [
        "id" => $row["answer_id"],
        "answer_text" => $row["answer_text"],
        "is_correct" => $row["is_correct"]
    ];
}

echo json_encode(array_values($questions));
