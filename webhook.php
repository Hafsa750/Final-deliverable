<?php

require_once 'db.php';
header("Content-Type: application/json");
$request = file_get_contents("php://input");
$data = json_decode($request, true);

file_put_contents("log.txt", "--- New Request ---\n" . date("Y-m-d H:i:s") . "\n" . print_r($data, true) . "\n\n", FILE_APPEND);

$intent = $data['queryResult']['intent']['displayName'] ?? '';
$params = $data['queryResult']['parameters'] ?? [];
$sessionPath = $data['session'] ?? '';
$userInput = $data['queryResult']['queryText'] ?? '';
$contexts = $data['queryResult']['outputContexts'] ?? [];

$sessionParts = explode('/', $sessionPath);
$dialogflowSessionId = end($sessionParts);

$userId = null;
if (strpos($dialogflowSessionId, 'user_') === 0) {
    $parts = explode('_', $dialogflowSessionId);
    if (isset($parts[1]) && is_numeric($parts[1])) {
        $userId = (int) $parts[1];
    }
}

$responsePayload = [];

try {
    switch ($intent) {
        // --- RESERVATION FLOW ---
        case 'MakeReservation.Start':
            $responsePayload = startReservationProcess($con, $userId, $sessionPath);
            break;
        case 'MakeReservation.CollectDetails':
            $responsePayload = ['fulfillmentText' => collectAndFinalizeReservation($data, $con, $userId)];
            break;

        // --- ORDERING FLOW ---
        case 'Order.Start':
            $responsePayload = startOrder($con, $userId, $sessionPath);
            break;
        case 'Order.SelectCategory':
            $responsePayload = listItemsInCategory($params, $con, $sessionPath);
            break;
        case 'Order.SelectItem':
            $responsePayload = ['fulfillmentText' => finalizeOrder($params, $con, $userId)];
            break;
        case 'CancelOrder':
             if (!$userId) {
                $responsePayload = ['fulfillmentText' => "You must be logged in to cancel an order. Please log in first."];
             } else {
                $responsePayload = ['fulfillmentText' => cancelOrder($params, $con, $userId)];
             }
             break;

        // --- OTHER USER-SPECIFIC INTENTS ---
        case 'RequestSupport': 
        case 'CheckSupportStatus':
        case 'ModifyReservation': 
        case 'CancelReservation':
            if (!$userId) {
                 $responsePayload = ['fulfillmentText' => "For this action, you need to be logged in. Please log in to your account, and I'll be happy to assist you further."];
            } else {
                 if ($intent == 'RequestSupport') $responsePayload = ['fulfillmentText' => handleSupportRequest($params, $con, $userId)];
                 if ($intent == 'CheckSupportStatus') $responsePayload = checkSupportStatus($params, $con, $userId, $sessionPath);
                 if ($intent == 'ModifyReservation') $responsePayload = ['fulfillmentText' => modifyReservation($params, $con, $userId)];
                 if ($intent == 'CancelReservation') $responsePayload = ['fulfillmentText' => cancelReservation($params, $con, $userId)];
            }
            break;

        case 'Support.AddReply': $responsePayload = ['fulfillmentText' => addSupportReply($params, $con, $userId, $contexts)]; break;
        
        // --- PUBLIC INTENTS ---
        case 'ViewMenu': $responsePayload = ['fulfillmentText' => getMenuItems($con)]; break;
        case 'AskFAQ': $responsePayload = ['fulfillmentText' => answerFAQ($userInput, $con)]; break;
        case 'CheckBusinessHours': $responsePayload = ['fulfillmentText' => getBusinessHours($con)]; break;
        case 'CheckLocation': $responsePayload = ['fulfillmentText' => getBusinessLocation($con)]; break;
        
        default: $responsePayload = ['fulfillmentText' => "Sorry, I am unable to process the intent: '$intent'."]; break;
    }
    
    $logResponse = $responsePayload['fulfillmentText'] ?? ($responsePayload['fulfillmentMessages'][0]['text']['text'][0] ?? 'No text response');
    logChatbotInteraction($con, $dialogflowSessionId, $userId, $intent, $userInput, $logResponse);
    
} catch (Exception $e) {
    $responsePayload = ['fulfillmentText' => "Sorry, I encountered a system error: " . $e->getMessage()];
    error_log("Chatbot Error: " . $e->getMessage());
}

echo json_encode($responsePayload);
exit;


//  ORDERING SYSTEM FUNCTIONS

function startOrder($con, $userId, $sessionPath) {
    if (!$userId) {
        return ['fulfillmentText' => "To place an order, you need to be logged in. Please log in and I'll be happy to take your order."];
    }

    $stmt = $con->prepare("SELECT name FROM menu_categories ORDER BY name ASC");
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        return ['fulfillmentText' => "I'm sorry, our menu categories aren't available right now. Please check back later."];
    }

    $categories = [];
    while ($row = $result->fetch_assoc()) {
        $categories[] = $row['name'];
    }

    $responseText = "I can help with that! What would you like to order from? Our categories are: " . implode(', ', $categories) . ".";
    
    $contextName = $sessionPath . "/contexts/awaiting_category_choice";
    return [
        "fulfillmentText" => $responseText,
        "outputContexts" => [[ "name" => $contextName, "lifespanCount" => 2 ]]
    ];
}

function listItemsInCategory($params, $con, $sessionPath) {
    $categoryName = $params['category'] ?? '';
    if (empty($categoryName)) {
        return ['fulfillmentText' => "I'm sorry, which category did you want to see?"];
    }

    $stmt = $con->prepare("SELECT mi.name, mi.price FROM menu_items mi JOIN menu_categories mc ON mi.category_id = mc.id WHERE mc.name = ? ORDER BY mi.name ASC");
    $stmt->bind_param("s", $categoryName);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        return ['fulfillmentText' => "I'm sorry, I couldn't find any items in the '$categoryName' category. Would you like to see another category?"];
    }
    
    $items = [];
    while ($row = $result->fetch_assoc()) {
        $items[] = $row['name'] . " ($" . number_format($row['price'], 2) . ")";
    }

    $responseText = "In the $categoryName category, we have: " . implode(', ', $items) . ". What would you like?";
    
    $contextName = $sessionPath . "/contexts/awaiting_item_choice";
    return [
        "fulfillmentText" => $responseText,
        "outputContexts" => [[ "name" => $contextName, "lifespanCount" => 2 ]]
    ];
}

function finalizeOrder($params, $con, $userId) {
    if (!$userId) return "You must be logged in to finalize an order. Please log in.";

    $itemName = $params['menu_item'] ?? '';
    $quantity = (int)($params['quantity'] ?? 0); // Check for 0 as quantity is required

    if (empty($itemName) || $quantity <= 0) {
        return "I'm sorry, I didn't catch that. What item would you like to order and how many?";
    }

    // Find the menu item in the database
    $stmt = $con->prepare("SELECT id, price FROM menu_items WHERE name = ?");
    $stmt->bind_param("s", $itemName);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        return "I'm sorry, I couldn't find '$itemName' on our menu. Please try a different item.";
    }
    $item = $result->fetch_assoc();
    $itemId = $item['id'];
    $unitPrice = $item['price'];
    $totalPrice = $unitPrice * $quantity;

    // Use a transaction to ensure data integrity
    mysqli_begin_transaction($con);

    try {
        // 1. Create the main order record
        $orderStmt = $con->prepare("INSERT INTO orders (user_id, total_amount, status) VALUES (?, ?, 'confirmed')");
        $orderStmt->bind_param("id", $userId, $totalPrice);
        $orderStmt->execute();
        $orderId = $con->insert_id;

        // 2. Create the order item record
        $itemStmt = $con->prepare("INSERT INTO order_items (order_id, menu_item_id, quantity, unit_price, total_price) VALUES (?, ?, ?, ?, ?)");
        $itemStmt->bind_param("iiidd", $orderId, $itemId, $quantity, $unitPrice, $totalPrice);
        $itemStmt->execute();

        // If all good, commit the transaction
        mysqli_commit($con);
        
        $orderCode = "ORD-" . $orderId;
        return "✅ Order placed! I've added $quantity x $itemName to your order. Your order number is **$orderCode**. You can say 'cancel order $orderCode' if you need to.";

    } catch (Exception $e) {
        // If anything fails, roll back
        mysqli_rollback($con);
        error_log("Order transaction failed: " . $e->getMessage());
        return "I'm sorry, there was a technical problem placing your order. Please try again.";
    }
}

function cancelOrder($params, $con, $userId) {
    $orderCode = $params['order_id'] ?? '';
    if (empty($orderCode)) {
        return "Please provide the order number you wish to cancel (e.g., 'cancel order ORD-123').";
    }
    
    // Extract the numeric ID from the order code (e.g., "ORD-123" -> 123)
    $orderId = (int) preg_replace('/[^0-9]/', '', $orderCode);

    if ($orderId <= 0) {
        return "That doesn't seem to be a valid order number. Please provide the full order number.";
    }
    
    // Update the status, ensuring the order belongs to the current user for security
    $stmt = $con->prepare("UPDATE orders SET status = 'cancelled' WHERE id = ? AND user_id = ? AND status != 'cancelled'");
    $stmt->bind_param("ii", $orderId, $userId);
    $stmt->execute();
    
    if ($stmt->affected_rows > 0) {
        return "✅ Your order **$orderCode** has been successfully cancelled.";
    } else {
        // This can happen if the order ID is wrong, doesn't belong to the user, or was already cancelled.
        return "I couldn't find an active order with that number associated with your account. Please double-check the order number.";
    }
}


//  RESERVATION SYSTEM FUNCTIONS

function startReservationProcess($con, $userId, $sessionPath) {
    if (!$userId) {
        return ['fulfillmentText' => "To make a reservation, you need to be logged in first. Please log in to your account and I can help you book a table."];
    } else {
        $contextName = $sessionPath . "/contexts/awaiting_reservation_details";
        return [
            "fulfillmentText" => "Great, I can help with that. For what date would you like to make a reservation?",
            "outputContexts" => [[ "name" => $contextName, "lifespanCount" => 5 ]]
        ];
    }
}

function collectAndFinalizeReservation($data, $con, $userId) {
    $params = $data['queryResult']['parameters'];

    if (empty($params['reservation_date'])) { return "I see. And for what date would you like to make the reservation?"; }
    if (empty($params['reservation_time'])) { return "Got it. And what time would you like to book for?"; }
    if (empty($params['number_of_guests']) || (int)$params['number_of_guests'] <= 0) { return "Perfect. How many guests will be in your party?"; }
    if (empty($params['occasion'])) { return "And what is the occasion? (e.g., birthday, casual, anniversary)"; }
    if (empty($params['contact_phone'])) { return "Almost done! What is the best contact phone number for the reservation?"; }

    $dateStr = is_array($params['reservation_date']) ? ($params['reservation_date']['date_time'] ?? '') : $params['reservation_date'];
    $timeStr = is_array($params['reservation_time']) ? ($params['reservation_time']['date_time'] ?? '') : $params['reservation_time'];
    $guests = (int)$params['number_of_guests'];
    $occasion = $params['occasion'];
    $phone = is_array($params['contact_phone']) ? ($params['contact_phone']['number'] ?? '') : $params['contact_phone'];
    $specialRequests = $params['special_requests'] ?? '';

    $reservationDate = date('Y-m-d', strtotime($dateStr));
    $reservationTime = date('H:i:s', strtotime($timeStr));

    $reservationCode = strtoupper(bin2hex(random_bytes(4)));

    $stmt = $con->prepare(
        "INSERT INTO reservations (user_id, reservation_date, reservation_time, number_of_guests, occasion, special_requests, contact_phone, reservation_code, status)
         VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'confirmed')"
    );

    if (!$stmt) {
        error_log("DB Prepare Error: " . $con->error);
        return "Sorry, there was a technical problem preparing your reservation.";
    }

    $stmt->bind_param("ississss", $userId, $reservationDate, $reservationTime, $guests, $occasion, $specialRequests, $phone, $reservationCode);

    if ($stmt->execute()) {
        return "✅ Excellent! Your reservation is confirmed. Your reservation code is **$reservationCode**. We look forward to seeing you on " . date('F jS', strtotime($reservationDate)) . " at " . date('g:i A', strtotime($reservationTime)) . ".";
    } else {
        error_log("DB Execute Error: " . $stmt->error);
        return "I'm sorry, I was unable to book your table at this time. Please try again or contact us directly.";
    }
}


//  EXISTING SUPPORT & FAQ FUNCTIONS

function answerFAQ($userQuestion, $con) {
    if (empty($userQuestion)) { return "I'm sorry, I didn't understand your question. Please try rephrasing."; }
    $stmt = $con->prepare("SELECT question, answer, MATCH(question, answer) AGAINST(? IN NATURAL LANGUAGE MODE) AS relevance FROM faqs WHERE MATCH(question, answer) AGAINST(? IN NATURAL LANGUAGE MODE) > 0 ORDER BY relevance DESC LIMIT 1");
    $stmt->bind_param("ss", $userQuestion, $userQuestion);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $faq = $result->fetch_assoc();
        return $faq['answer'];
    } else {
        return "I'm sorry, I couldn't find an answer to your question in our FAQ. Would you like me to create a support ticket for you so our team can assist you directly?";
    }
}

function handleSupportRequest($params, $con, $userId) {
    $message = $params['message'] ?? '';
    if (empty($message)) return "Please describe your support request.";
    $stmt = $con->prepare("INSERT INTO support_messages (user_id, message) VALUES (?, ?)");
    $stmt->bind_param("is", $userId, $message);
    if ($stmt->execute()) {
        $ticketId = $con->insert_id;
        return "✅ Support ticket #$ticketId has been created. A member of our team will review it shortly. You can check its status by saying 'check ticket $ticketId'.";
    }
    return "Failed to submit your support request. Please try again later.";
}

function checkSupportStatus($params, $con, $userId, $sessionPath) {
    $ticketId = $params['ticket_id'] ?? 0;
    if ($ticketId <= 0) return ['fulfillmentText' => "Please provide a valid ticket ID."];
    $query = "SELECT s.*, u.name as responder_name FROM support_messages s LEFT JOIN users u ON s.responder_id = u.id WHERE (s.id = ? OR s.parent_id = ?) AND EXISTS (SELECT 1 FROM support_messages p WHERE p.id = ? AND p.user_id = ?) ORDER BY s.created_at ASC";
    $stmt = $con->prepare($query);
    $stmt->bind_param("iiii", $ticketId, $ticketId, $ticketId, $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows === 0) { return ['fulfillmentText' => "Sorry, I couldn't find ticket #$ticketId associated with your account."]; }
    $thread = "--- Ticket #$ticketId ---\n\n";
    $ticketStatus = 'closed';
    while($row = $result->fetch_assoc()) {
        if ($row['user_id'] == $userId) { $thread .= "You (" . date('M j, g:i a', strtotime($row['created_at'])) . "):\n"; } else { $responderName = $row['responder_name'] ?? 'Support Staff'; $thread .= $responderName . " (" . date('M j, g:i a', strtotime($row['created_at'])) . "):\n"; }
        $thread .= $row['message'] . "\n\n";
        if ($row['parent_id'] === null) { $ticketStatus = $row['status']; }
    }
    $thread .= "------------------------\nStatus: " . ucfirst($ticketStatus);
    $response = [];
    if ($ticketStatus === 'open' || $ticketStatus === 'in_progress') {
        $thread .= "\n\nWould you like to add a reply to this ticket?";
        $contextName = $sessionPath . "/contexts/awaiting_support_reply";
        $response['outputContexts'] = [[ "name" => $contextName, "lifespanCount" => 2, "parameters" => ["ticket_id" => $ticketId] ]];
    }
    $response['fulfillmentText'] = $thread;
    return $response;
}

function addSupportReply($params, $con, $userId, $contexts) {
    if (!$userId) return "Please log in to reply to a support ticket.";
    $replyMessage = $params['reply_message'] ?? '';
    if(empty($replyMessage)) return "What would you like to add to the ticket?";
    $parentId = null;
    foreach($contexts as $context) { if(strpos($context['name'], '/contexts/awaiting_support_reply') !== false) { $parentId = $context['parameters']['ticket_id'] ?? null; break; } }
    if(!$parentId) return "I'm sorry, I've lost track of which ticket we were discussing. Please ask to check the ticket status again.";
    $stmt = $con->prepare("INSERT INTO support_messages (user_id, parent_id, message) VALUES (?, ?, ?)");
    $stmt->bind_param("iis", $userId, $parentId, $replyMessage);
    if ($stmt->execute()) {
        $updateStmt = $con->prepare("UPDATE support_messages SET status = 'in_progress' WHERE id = ?");
        $updateStmt->bind_param("i", $parentId);
        $updateStmt->execute();
        return "✅ Thank you. Your reply has been added to ticket #$parentId.";
    }
    return "Sorry, there was a problem adding your reply. Please try again.";
}

// --- OTHER UTILITY FUNCTIONS ---
function getMenuItems($con) { /* ... function logic ... */ return "Here is our menu..."; }
function modifyReservation($params, $con, $userId) { /* ... function logic ... */ return "Modifying reservation..."; }
function cancelReservation($params, $con, $userId) { /* ... function logic ... */ return "Cancelling reservation..."; }
function getBusinessHours($con) { /* ... function logic ... */ return "We are open from..."; }
function getBusinessLocation($con) { /* ... function logic ... */ return "We are located at..."; }
function logChatbotInteraction($con, $sessionId, $userId, $intent, $userInput, $botResponse) { if (!$con) return; $userId = $userId ? $userId : null; $stmt = $con->prepare("INSERT INTO chatbot_logs (user_id, session_id, intent_name, user_input, bot_response) VALUES (?, ?, ?, ?, ?)"); if ($stmt) { $stmt->bind_param("issss", $userId, $sessionId, $intent, $userInput, $botResponse); $stmt->execute(); $stmt->close(); }}