<?php
session_start();
include('../config.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_block_id'])) {
    $blockId = intval($_POST['edit_block_id']);
    
    // Get existing block data
    $stmt = $conn->prepare("SELECT data FROM project_blocks WHERE id = ?");
    $stmt->bind_param("i", $blockId);
    $stmt->execute();
    $result = $stmt->get_result();
    $block = $result->fetch_assoc();
    
    if ($block) {
        $blockData = json_decode($block['data'], true);
        
        // Update block data with new values
        $blockData['title'] = $_POST['title'] ?? $blockData['title'];
        $blockData['text'] = $_POST['text'] ?? $blockData['text'];
        $blockData['background_color'] = $_POST['background_color'] ?? $blockData['background_color'];
        $blockData['font_color'] = $_POST['font_color'] ?? $blockData['font_color'];
        $blockData['font_style'] = $_POST['font_style'] ?? $blockData['font_style'];
        $blockData['font_size'] = $_POST['font_size'] ?? $blockData['font_size'];
        $blockData['font_weight'] = $_POST['font_weight'] ?? $blockData['font_weight'];
        $blockData['border_radius'] = $_POST['border_radius'] ?? $blockData['border_radius'];
        
        // Handle image updates
        if (isset($_POST['logo_url'])) {
            $blockData['logo_url'] = $_POST['logo_url'];
        }
        if (isset($_POST['image_url'])) {
            $blockData['image_url'] = $_POST['image_url'];
        }
        
        // Update the database
        $stmt = $conn->prepare("UPDATE project_blocks SET data = ? WHERE id = ?");
        $stmt->bind_param("si", json_encode($blockData), $blockId);
        
        if ($stmt->execute()) {
            http_response_code(200);
            echo json_encode(['success' => true]);
        } else {
            http_response_code(500);
            echo json_encode(['success' => false, 'error' => 'Failed to update block']);
        }
    } else {
        http_response_code(404);
        echo json_encode(['success' => false, 'error' => 'Block not found']);
    }
}