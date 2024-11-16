<?php
session_start();
header('Content-Type: application/json');

$action = $_POST['action'] ?? null;

if ($action == 'sendOffer') {
    // Store the offer in the session
    $_SESSION['offer'] = $_POST['offer'];
    echo json_encode(['message' => 'Offer stored successfully.']);
} elseif ($action == 'getOffer') {
    if (isset($_SESSION['offer'])) {
        // Return the stored offer
        echo json_encode(['offer' => $_SESSION['offer']]);
    } else {
        echo json_encode(['error' => 'No offer found.']);
    }
} elseif ($action == 'sendAnswer') {
    // Store the answer in the session
    $_SESSION['answer'] = $_POST['answer'];
    echo json_encode(['message' => 'Answer stored successfully.']);
} elseif ($action == 'getAnswer') {
    if (isset($_SESSION['answer'])) {
        // Return the stored answer
        echo json_encode(['answer' => $_SESSION['answer']]);
    } else {
        echo json_encode(['error' => 'No answer found.']);
    }
} elseif ($action == 'sendIceCandidate') {
    // Store the ICE candidate
    $_SESSION['iceCandidates'][] = $_POST['candidate'];
    echo json_encode(['message' => 'ICE candidate stored.']);
} elseif ($action == 'getIceCandidates') {
    if (isset($_SESSION['iceCandidates'])) {
        // Return all stored ICE candidates
        echo json_encode(['candidates' => $_SESSION['iceCandidates']]);
    } else {
        echo json_encode(['error' => 'No ICE candidates found.']);
    }
} else {
    echo json_encode(['error' => 'Invalid action.']);
}
