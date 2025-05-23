<?php
session_start();
include('../config.php');

$projectId = isset($_GET['id']) ? intval($_GET['id']) : null;

// para sa fetching ng project details sa database
$project = [];
if ($projectId) {
    $stmt = $conn->prepare("SELECT * FROM projects WHERE id = ?");
    $stmt->bind_param("i", $projectId);
    $stmt->execute();
    $result = $stmt->get_result();
    $project = $result->fetch_assoc();

    if (!$project) {
        die("Project not found");
    }
}


if (isset($_POST['update_project_name']) && isset($_GET['id'])) {
    $newName = trim($_POST['project_name']);
    $projectId = $_GET['id'];

    if (!empty($newName)) {
        $stmt = $conn->prepare("UPDATE projects SET project_name = ? WHERE id = ? AND user_id = ?");
        $stmt->bind_param("sii", $newName, $projectId, $_SESSION['user_id']);
        $stmt->execute();

        header("Location: Project.php?id=$projectId");
        exit();
    }
}

// handle the block creation and deletion
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add_block'])) {
        $blockType = $_POST['block_type'] ?? '';

        // constant blocks, designs and styles
        $blockData = [
            'title' => $_POST['title'] ?? '',
            'background_color' => $_POST['background_color'] ?? '#ffffff',
            'font_style' => $_POST['font_style'] ?? 'Arial',
            'font_color' => $_POST['font_color'] ?? '#000000',
            'font_size' => $_POST['font_size'] ?? '16',
            'font_weight' => $_POST['font_weight'] ?? 'normal',
            'text' => $_POST['text'] ?? '',
            'padding' => $_POST['padding'] ?? '20',
            'margin' => $_POST['margin'] ?? '0',
            'border_radius' => $_POST['border_radius'] ?? '0'
        ];

        // specific blocks components
        switch ($blockType) {
            case 'header':
                $blockData['logo_url'] = $_POST['logo_url'] ?? '';
                $blockData['menu_items'] = $_POST['menu_items'] ?? 'Home,About Us,Contact Us';
                $blockData['image_position'] = $_POST['image_position'] ?? 'left';
                break;

            case 'main_content':
                $blockData['content_type'] = $_POST['content_type'] ?? 'text';
                $blockData['image_url'] = $_POST['image_url'] ?? '';
                $blockData['button_text'] = $_POST['button_text'] ?? '';
                $blockData['button_color'] = $_POST['button_color'] ?? '#007bff';
                $blockData['background_image'] = $_POST['background_image'] ?? '';
                $blockData['background_opacity'] = $_POST['background_opacity'] ?? '0.5';
                $blockData['content_position'] = $_POST['content_position'] ?? 'left';
                $blockData['button_position'] = $_POST['button_position'] ?? 'left';
                $blockData['button_url'] = $_POST['button_url'] ?? '#';
                $blockData['content_position'] = $_POST['content_position'] ?? 'left';
                $blockData['button_position'] = $_POST['button_position'] ?? 'left';
                $blockData['background_image'] = $_POST['background_image'] ?? '';
                $blockData['background_opacity'] = (intval($_POST['background_opacity'] ?? 50) / 100);
                break;

            case 'forms':
                $blockData['form_type'] = $_POST['form_type'] ?? 'contact';
                $blockData['form_fields'] = $_POST['form_fields'] ?? 'name,email,message';
                break;

            case 'footer':
                $blockData['copyright_text'] = $_POST['copyright_text'] ?? 'Copyright © ' . date('Y');
                $blockData['social_links'] = $_POST['social_links'] ?? '';
                break;

            // Add this to your block creation handler in the switch statement
            case 'cards':
                $cards = [];
                $cardCount = min(3, intval($_POST['card_count']));
                
                for ($i = 1; $i <= $cardCount; $i++) {
                    $cards[] = [
                        'title' => $_POST["card_title_$i"] ?? '',
                        'description' => $_POST["card_description_$i"] ?? '',
                        'image' => $_POST["card_image_$i"] ?? '',
                        'button_text' => $_POST["card_button_text_$i"] ?? '',
                        'button_url' => $_POST["card_button_url_$i"] ?? ''
                    ];
                }
                $blockData['cards'] = $cards;
                break;
        }

        $stmt = $conn->prepare("INSERT INTO project_blocks (project_id, block_type, data) VALUES (?, ?, ?)");
        $stmt->bind_param("iss", $projectId, $blockType, json_encode($blockData));
        $stmt->execute();

        header("Location: Project.php?id=$projectId");
        exit();
    }

    if (isset($_POST['delete_block'])) {
        $blockId = intval($_POST['block_id']);
        $stmt = $conn->prepare("DELETE FROM project_blocks WHERE id = ?");
        $stmt->bind_param("i", $blockId);
        $stmt->execute();
        header("Location: Project.php?id=$projectId");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buildify - Edit Project</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="icon" type="image/png" href="/images/buildifylogo2.png">
    <style>
        .block-container {
            margin-bottom: 20px;
            border: 1px solid #e2e8f0;
            border-radius: 4px;
            background-color: white;
        }

        .block-actions {
            display: none;
        }

        .block-container:hover .block-actions {
            display: flex;
        }

        .block-fields {
            display: none;
        }

        .block-fields.active {
            display: block;
        }

        .header-image-left {
            order: 0;
            margin-right: 20px;
        }

        .header-image-center {
            order: 0;
            margin-left: auto;
            margin-right: auto;
        }

        .header-image-right {
            order: 1;
            margin-left: 20px;
        }

        /* Add to your existing styles section */
        .prose {
            max-width: 65ch;
            line-height: 1.75;
        }

        .prose p {
            margin-bottom: 1.5em;
        }

        .block-container {
            backdrop-filter: blur(10px);
            transition: all 0.3s ease;
        }

        .block-container:hover {
            transform: translateY(-2px);
        }

        input:focus,
        textarea:focus {
            outline: none;
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.2);
        }

        .nav-link {
            position: relative;
            overflow: hidden;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .animate-fade-in {
            animation: fadeIn 0.5s ease-out forwards;
        }

        #preview-modal {
            backdrop-filter: blur(5px);
        }

        #preview-container {
            min-height: 100%;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
        }

        #preview-container .block-container {
            margin: 0;
            border-radius: 0;
        }

        /* Preview device-specific styles */
        @media (max-width: 375px) {
            #preview-container {
                width: 375px;
            }
        }

        @media (max-width: 768px) {
            #preview-container {
                width: 768px;
            }
        }

        .block-container {
            position: relative;
            z-index: 1;
            margin-bottom: 1rem;
        }

        .block-container:hover {
            z-index: 2;
        }

        .block-actions {
            z-index: 3;
        }

        .block-edit-form {
            position: relative;
            z-index: 2;
            margin-top: 1rem;
        }
    </style>
</head>

<body class="bg-gray-100 font-sans">
    <div class="flex flex-row h-screen">
        <!-- Components Panel -->
        <div class="w-70 bg-gray-800 text-white p-4 overflow-y-auto">
            <a href="Homepage.php" class="text-xl font-bold mb-6 flex items-center ml-24">
                <i class="fas fa-home mr-2"></i> Home
            </a>
            <h2 class="text-xl font-bold mb-6 flex items-center mt-12">
                <i class="fas fa-cubes mr-2"></i> Components
            </h2>

            <form method="POST" class="space-y-4">
                <input type="hidden" name="add_block" value="1">

                <div>
                    <label class="block text-gray-300 text-sm font-bold mb-2" for="block_type">
                        Block Type
                    </label>
                    <select class="w-full bg-gray-700 text-white rounded p-2" id="block_type" name="block_type" required
                        onchange="showBlockFields(this.value)">
                        <option value="">Select a block</option>
                        <option value="header">Header</option>
                        <option value="main_content">Main Content</option>
                        <option value="cards">Cards</option> 
                        <option value="forms">Forms</option>
                        <option value="footer">Footer</option>
                    </select>
                </div>

                <!-- Common Fields for All Blocks -->
                <div class="common-fields">
                    <div class="mb-3">
                        <label class="block text-gray-300 text-sm font-bold mb-2" for="title">
                            Title
                        </label>
                        <input class="w-full bg-gray-700 text-white rounded p-2" id="title" name="title">
                    </div>

                    <div class="mb-3">
                        <label class="block text-gray-300 text-sm font-bold mb-2" for="text">
                            Content
                        </label>
                        <textarea class="w-full bg-gray-700 text-white rounded p-2" id="text" name="text" rows="4"></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="block text-gray-300 text-sm font-bold mb-2" for="background_color">
                            Background Color
                        </label>
                        <input class="w-full h-10" id="background_color" type="color" name="background_color" value="#ffffff">
                    </div>

                    <div class="mb-3 grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-gray-300 text-sm font-bold mb-2" for="font_style">
                                Font Style
                            </label>
                            <select class="w-full bg-gray-700 text-white rounded p-2" id="font_style" name="font_style">
                                <option value="Arial, sans-serif">Arial</option>
                                <option value="'Helvetica Neue', sans-serif">Helvetica</option>
                                <option value="Georgia, serif">Georgia</option>
                                <option value="'Times New Roman', serif">Times New Roman</option>
                                <option value="Verdana, sans-serif">Verdana</option>
                                <option value="'Courier New', monospace">Courier New</option>
                                <option value="'Trebuchet MS', sans-serif">Trebuchet MS</option>
                                <option value="Impact, sans-serif">Impact</option>
                                <option value="'Open Sans', sans-serif">Open Sans</option>
                                <option value="'Roboto', sans-serif">Roboto</option>
                                <option value="'Lato', sans-serif">Lato</option>
                                <option value="'Montserrat', sans-serif">Montserrat</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-gray-300 text-sm font-bold mb-2" for="font_color">
                                Font Color
                            </label>
                            <input class="w-full h-10" id="font_color" type="color" name="font_color" value="#000000">
                        </div>
                    </div>

                    <div class="mb-3 grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-gray-300 text-sm font-bold mb-2" for="font_size">
                                Font Size (px)
                            </label>
                            <input class="w-full bg-gray-700 text-white rounded p-2" id="font_size" type="number" name="font_size" min="8" max="72" value="16">
                        </div>
                        <div>
                            <label class="block text-gray-300 text-sm font-bold mb-2" for="font_weight">
                                Font Weight
                            </label>
                            <select class="w-full bg-gray-700 text-white rounded p-2" id="font_weight" name="font_weight">
                                <option value="100">Thin</option>
                                <option value="100 italic">Thin Italic</option>
                                <option value="200">Extra Light</option>
                                <option value="200 italic">Extra Light Italic</option>
                                <option value="300">Light</option>
                                <option value="300 italic">Light Italic</option>
                                <option value="normal">Normal (400)</option>
                                <option value="normal italic">Normal Italic</option>
                                <option value="500">Medium</option>
                                <option value="500 italic">Medium Italic</option>
                                <option value="600">Semi Bold</option>
                                <option value="600 italic">Semi Bold Italic</option>
                                <option value="bold">Bold (700)</option>
                                <option value="bold italic">Bold Italic</option>
                                <option value="800">Extra Bold</option>
                                <option value="800 italic">Extra Bold Italic</option>
                                <option value="900">Black</option>
                                <option value="900 italic">Black Italic</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-3 grid grid-cols-3 gap-4">
                        <div>
                            <label class="block text-gray-300 text-sm font-bold mb-2" for="padding">
                                Padding
                            </label>
                            <input class="w-full bg-gray-700 text-white rounded p-2" id="padding" type="number" name="padding" min="0" max="100" value="20">
                        </div>
                        <div>
                            <label class="block text-gray-300 text-sm font-bold mb-2" for="margin">
                                Margin
                            </label>
                            <input class="w-full bg-gray-700 text-white rounded p-2" id="margin" type="number" name="margin" min="0" max="100" value="0">
                        </div>
                        <div>
                            <label class="block text-gray-300 text-sm font-bold mb-2" for="border_radius">
                                Border Radius
                            </label>
                            <input class="w-full bg-gray-700 text-white rounded p-2" id="border_radius" type="number" name="border_radius" min="0" max="50" value="0">
                        </div>
                    </div>
                </div>

                <!-- Header Specific Fields -->
                <div id="header-fields" class="block-fields">
                    <div class="mb-3">
                        <label class="block text-gray-300 text-sm font-bold mb-2" for="logo_url">
                            Logo/Image
                        </label>
                        <input class="w-full bg-gray-700 text-white rounded p-2" id="logo_url" name="logo_url" placeholder="PNG/JPG">
                        <small class="text-gray-400">Or upload an image:</small>
                        <input type="file" id="image_upload" class="hidden" accept="image/*">
                        <button type="button" onclick="document.getElementById('image_upload').click()"
                            class="mt-1 bg-gray-600 text-white px-3 py-1 rounded text-sm">
                            <i class="fas fa-upload mr-1"></i> Upload Image
                        </button>
                    </div>

                    <div class="mb-3">
                        <label class="block text-gray-300 text-sm font-bold mb-2" for="image_position">
                            Image Position
                        </label>
                        <div class="flex space-x-2">
                            <label class="flex items-center">
                                <input type="radio" name="image_position" value="left" checked class="mr-1">
                                <span>Left</span>
                            </label>
                            <label class="flex items-center">
                                <input type="radio" name="image_position" value="center" class="mr-1">
                                <span>Center</span>
                            </label>
                            <label class="flex items-center">
                                <input type="radio" name="image_position" value="right" class="mr-1">
                                <span>Right</span>
                            </label>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="block text-gray-300 text-sm font-bold mb-2" for="menu_items">
                            Menu Items (comma separated)
                        </label>
                        <input class="w-full bg-gray-700 text-white rounded p-2" id="menu_items" name="menu_items" value="Home,About Us,Contact Us">
                    </div>
                </div>

                <!-- Main Content Specific Fields -->
                <div id="main_content-fields" class="block-fields">

                    <div class="mb-3">
                        <label class="block text-gray-300 text-sm font-bold mb-2" for="image_url">
                            Image
                        </label>
                        <input class="w-full bg-gray-700 text-white rounded p-2" id="image_url" name="image_url" placeholder="PNG/JPG">
                        <small class="text-gray-400">Or upload an image:</small>
                        <input type="file" id="main_content_image_upload" class="hidden" accept="image/*">
                        <button type="button" onclick="document.getElementById('main_content_image_upload').click()"
                            class="mt-1 bg-gray-600 text-white px-3 py-1 rounded text-sm">
                            <i class="fas fa-upload mr-1"></i> Upload Image
                        </button>
                    </div>

                    <div class="mb-3 grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-gray-300 text-sm font-bold mb-2" for="button_text">
                                Button Text
                            </label>
                            <input class="w-full bg-gray-700 text-white rounded p-2" id="button_text" name="button_text">
                        </div>
                        <div>
                            <label class="block text-gray-300 text-sm font-bold mb-2" for="button_url">
                                Button URL
                            </label>
                            <input class="w-full bg-gray-700 text-white rounded p-2" id="button_url" name="button_url">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="block text-gray-300 text-sm font-bold mb-2" for="button_color">
                            Button Color
                        </label>
                        <input class="w-full h-10" id="button_color" type="color" name="button_color" value="#007bff">
                    </div>

                    <div class="mb-3">
                        <label class="block text-gray-300 text-sm font-bold mb-2">
                            Content Position
                        </label>
                        <div class="flex space-x-4">
                            <label class="flex items-center">
                                <input type="radio" name="content_position" value="left" checked class="mr-1">
                                <span class="text-white">Left</span>
                            </label>
                            <label class="flex items-center">
                                <input type="radio" name="content_position" value="center" class="mr-1">
                                <span class="text-white">Center</span>
                            </label>
                            <label class="flex items-center">
                                <input type="radio" name="content_position" value="right" class="mr-1">
                                <span class="text-white">Right</span>
                            </label>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="block text-gray-300 text-sm font-bold mb-2">
                            Button Position
                        </label>
                        <div class="flex space-x-4">
                            <label class="flex items-center">
                                <input type="radio" name="button_position" value="left" checked class="mr-1">
                                <span class="text-white">Left</span>
                            </label>
                            <label class="flex items-center">
                                <input type="radio" name="button_position" value="center" class="mr-1">
                                <span class="text-white">Center</span>
                            </label>
                            <label class="flex items-center">
                                <input type="radio" name="button_position" value="right" class="mr-1">
                                <span class="text-white">Right</span>
                            </label>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="block text-gray-300 text-sm font-bold mb-2">
                            Background Image
                        </label>
                        <input class="w-full bg-gray-700 text-white rounded p-2" id="background_image" name="background_image" placeholder="Image URL">
                        <small class="text-gray-400">Or upload an image:</small>
                        <input type="file" id="background_image_upload" class="hidden" accept="image/*">
                        <button type="button" onclick="document.getElementById('background_image_upload').click()"
                            class="mt-1 bg-gray-600 text-white px-3 py-1 rounded text-sm">
                            <i class="fas fa-upload mr-1"></i> Upload Background
                        </button>
                    </div>

                    <div class="mb-3">
                        <label class="block text-gray-300 text-sm font-bold mb-2">
                            Background Opacity
                        </label>
                        <input type="range" min="0" max="100" value="50" class="w-full"
                            id="background_opacity" name="background_opacity"
                            oninput="this.nextElementSibling.value = this.value + '%'">
                        <output>50%</output>
                    </div>
                </div>

                <!-- Forms Specific Fields -->
                <div id="forms-fields" class="block-fields">
                    <div class="mb-3">
                        <label class="block text-gray-300 text-sm font-bold mb-2" for="form_type">
                            Form Type
                        </label>
                        <select class="w-full bg-gray-700 text-white rounded p-2" id="form_type" name="form_type">
                            <option value="contact">Contact Form</option>
                            <option value="newsletter">Newsletter</option>
                            <option value="survey">Survey</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="block text-gray-300 text-sm font-bold mb-2" for="form_fields">
                            Form Fields (comma separated)
                        </label>
                        <input class="w-full bg-gray-700 text-white rounded p-2" id="form_fields" name="form_fields" value="name,email,message">
                    </div>
                </div>

                <!-- Footer Specific Fields -->
                <div id="footer-fields" class="block-fields">
                    <div class="mb-3">
                        <label class="block text-gray-300 text-sm font-bold mb-2" for="copyright_text">
                            Copyright Text
                        </label>
                        <input class="w-full bg-gray-700 text-white rounded p-2" id="copyright_text" name="copyright_text" value="Copyright © <?php echo date('Y'); ?>">
                    </div>

                    <div class="mb-3">
                        <label class="block text-gray-300 text-sm font-bold mb-2" for="social_links">
                            Social Links (comma separated)
                        </label>
                        <input class="w-full bg-gray-700 text-white rounded p-2" id="social_links" name="social_links" placeholder="facebook.com, twitter.com, instagram.com">
                    </div>
                </div>

                <!-- Cards Specific Fields -->
                <div id="cards-fields" class="block-fields">
                    <div class="mb-3">
                        <label class="block text-gray-300 text-sm font-bold mb-2">
                            Number of Cards (max 3)
                        </label>
                        <select class="w-full bg-gray-700 text-white rounded p-2" name="card_count" id="card_count" onchange="updateCardFields(this.value)">
                            <option value="1">1 Card</option>
                            <option value="2">2 Cards</option>
                            <option value="3">3 Cards</option>
                        </select>
                    </div>

                    <div id="cards-container">
                        <!-- Card 1 -->
                        <div class="card-fields mb-6 p-4 border border-gray-600 rounded">
                            <h4 class="text-white mb-4">Card 1</h4>
                            <div class="mb-3">
                                <label class="block text-gray-300 text-sm font-bold mb-2">Card Title</label>
                                <input class="w-full bg-gray-700 text-white rounded p-2" name="card_title_1">
                            </div>
                            <div class="mb-3">
                                <label class="block text-gray-300 text-sm font-bold mb-2">Card Description</label>
                                <textarea class="w-full bg-gray-700 text-white rounded p-2" name="card_description_1" rows="3"></textarea>
                            </div>
                            <div class="mb-3">
                                <label class="block text-gray-300 text-sm font-bold mb-2">Card Image</label>
                                <input type="text" class="w-full bg-gray-700 text-white rounded p-2" name="card_image_1" placeholder="Image URL">
                                <input type="file" id="card_image_upload_1" class="hidden" accept="image/*">
                                <button type="button" onclick="document.getElementById('card_image_upload_1').click()" 
                                    class="mt-1 bg-gray-600 text-white px-3 py-1 rounded text-sm">
                                    <i class="fas fa-upload mr-1"></i> Upload Image
                                </button>
                            </div>
                            <div class="mb-3">
                                <label class="block text-gray-300 text-sm font-bold mb-2">Button Text</label>
                                <input class="w-full bg-gray-700 text-white rounded p-2" name="card_button_text_1">
                            </div>
                            <div class="mb-3">
                                <label class="block text-gray-300 text-sm font-bold mb-2">Button URL</label>
                                <input class="w-full bg-gray-700 text-white rounded p-2" name="card_button_url_1">
                            </div>
                        </div>
                        
                        <!-- Card 2 and 3 containers will be dynamically shown/hidden -->
                    </div>
                </div>

                <button class="w-full bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded" type="submit">
                    Add Block
                </button>
            </form>
        </div>

        <!-- Editor Area -->
        <div class="flex-1 p-6 overflow-auto">
            <div class="flex justify-between items-center mb-6">
                <form method="POST" class="flex items-center space-x-2">
                    <i class="fas fa-pencil-alt text-gray-600"></i>
                    <input
                        type="text"
                        name="project_name"
                        value="<?php echo htmlspecialchars($project['project_name'] ?? $project['name'] ?? 'Untitled Project'); ?>"
                        class="border border-gray-300 rounded px-3 py-1 text-xl font-bold focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <button
                        type="submit"
                        name="update_project_name"
                        class="bg-blue-500 text-white px-4 py-1 rounded hover:bg-blue-600 text-sm">
                        Save
                    </button>
                </form>

                <button
                    onclick="previewWebsite()"
                    class="bg-green-500 text-white px-6 py-2 rounded hover:bg-green-600 flex items-center">
                    <i class="fas fa-eye mr-2"></i> Preview Website
                </button>
            </div>

            <!-- Blocks Container -->
            <div id="blocks-container" class="space-y-6">
                <?php
                if ($projectId) {
                    $stmt = $conn->prepare("SELECT * FROM project_blocks WHERE project_id = ? ORDER BY id ASC");
                    $stmt->bind_param("i", $projectId);
                    $stmt->execute();
                    $result = $stmt->get_result();

                    while ($block = $result->fetch_assoc()) {
                        $blockData = json_decode($block['data'], true);
                        $blockId = $block['id'];
                        $blockType = $block['block_type'];
                ?>
                        <div class="block-container relative p-4 mb-4"
                            data-block-id="<?php echo $blockId; ?>"
                            data-block-data='<?php echo htmlspecialchars(json_encode($blockData), ENT_QUOTES, 'UTF-8'); ?>'
                            style="
                                background-color: <?php echo $blockData['background_color']; ?>;
                                padding: <?php echo $blockData['padding']; ?>px;
                                margin: <?php echo $blockData['margin']; ?>px;
                                border-radius: <?php echo $blockData['border_radius']; ?>px;
                                position: relative;
                                z-index: 1;
                            ">
                            <div class="block-content" data-block-type="<?php echo $blockType; ?>"
                                style="font-family: <?php echo $blockData['font_style']; ?>; 
                                       font-weight: <?php echo strpos($blockData['font_weight'], 'italic') !== false ? explode(' ', $blockData['font_weight'])[0] : $blockData['font_weight']; ?>; 
                                       font-style: <?php echo strpos($blockData['font_weight'], 'italic') !== false ? 'italic' : 'normal'; ?>;">
                                <?php
                                // Render block based on type
                                switch ($blockType) {
                                    case 'header':
                                        echo '<div class="flex items-center justify-between p-4 transition-all duration-300 hover:shadow-lg">';

                                        // Logo/Image with enhanced positioning and animation
                                        if (!empty($blockData['logo_url'])) {
                                            $positionClass = 'transform hover:scale-105 transition-transform duration-300 ';
                                            $positionClass .= $blockData['image_position'] === 'center' ? 'mx-auto' : ($blockData['image_position'] === 'right' ? 'ml-auto order-last' : 'mr-4');

                                            echo '<img src="' . htmlspecialchars($blockData['logo_url']) . '" 
                                                      alt="Logo" 
                                                      class="h-12 ' . $positionClass . '">';
                                        }

                                        // Title with enhanced typography
                                        echo '<h1 class="text-3xl font-bold tracking-tight transition-colors duration-300 hover:text-indigo-600" 
                                                  style="font-family:' . $blockData['font_style'] . ';
                                                         color:' . $blockData['font_color'] . ';
                                                         font-size:' . $blockData['font_size'] . 'px;
                                                         font-weight:' . $blockData['font_weight'] . ';">';
                                        echo htmlspecialchars($blockData['title'] ?? 'Header');
                                        echo '</h1>';

                                        // Modern navigation menu
                                        if (!empty($blockData['menu_items'])) {
                                            $items = explode(',', $blockData['menu_items']);
                                            echo '<nav class="hidden md:flex space-x-6">';
                                            foreach ($items as $item) {
                                                echo '<a href="#" class="relative group px-2 py-1 transition-all duration-300 hover:text-indigo-600" 
                                                          style="font-family:' . $blockData['font_style'] . ';
                                                                 color:' . $blockData['font_color'] . ';
                                                                 font-size:' . ($blockData['font_size'] - 2) . 'px;">
                                                          <span class="relative z-10">' . htmlspecialchars(trim($item)) . '</span>
                                                          <span class="absolute bottom-0 left-0 w-full h-0.5 bg-indigo-600 transform scale-x-0 group-hover:scale-x-100 transition-transform duration-300"></span>
                                                          </a>';
                                            }
                                            echo '</nav>';

                                            // Mobile menu button
                                            echo '<button class="md:hidden p-2 rounded-lg hover:bg-gray-100">
                                                      <i class="fas fa-bars text-xl"></i>
                                                      </button>';
                                        }
                                        echo '</div>';
                                        break;

                                    case 'main_content':
                                        echo '<div class="p-6 transition-all duration-300 hover:shadow-xl rounded-xl relative overflow-hidden">';

                                        // Background Image with Opacity
                                        if (!empty($blockData['background_image'])) {
                                            echo '<div class="absolute inset-0 z-0" style="
                                                background-image: url(\'' . htmlspecialchars($blockData['background_image']) . '\');
                                                background-size: cover;
                                                background-position: center;
                                                opacity: ' . ($blockData['background_opacity'] ?? '0.5') . ';">
                                            </div>';
                                        }

                                        // Content Container with position classes
                                        $contentPosition = $blockData['content_position'] ?? 'left';
                                        $positionClasses = [
                                            'left' => 'text-left',
                                            'center' => 'text-center mx-auto',
                                            'right' => 'text-right ml-auto'
                                        ];

                                        echo '<div class="relative z-10 ' . $positionClasses[$contentPosition] . '" style="max-width: 800px;">';

                                        // Title
                                        echo '<h2 class="text-4xl font-bold mb-6 relative inline-block" 
                                            style="font-family:' . $blockData['font_style'] . ';
                                            color:' . $blockData['font_color'] . ';">
                                            <span class="relative z-10">' . htmlspecialchars($blockData['title'] ?? 'Main Content') . '</span>
                                            <span class="absolute bottom-0 left-0 w-full h-2 bg-indigo-200 -z-10"></span>
                                        </h2>';

                                        // Content Text
                                        echo '<div class="prose prose-lg max-w-none mb-8" 
                                            style="font-family:' . $blockData['font_style'] . ';
                                            color:' . $blockData['font_color'] . ';
                                            font-size:' . $blockData['font_size'] . 'px;
                                            font-weight:' . $blockData['font_weight'] . ';">';
                                        echo nl2br(htmlspecialchars($blockData['text'] ?? ''));
                                        echo '</div>';

                                        // Content Image
                                        if (!empty($blockData['image_url'])) {
                                            echo '<div class="mt-8 rounded-xl overflow-hidden shadow-lg transform hover:scale-[1.02] transition-transform duration-300">
                                                <img src="' . htmlspecialchars($blockData['image_url']) . '" 
                                                    alt="Content Image" 
                                                    class="w-full h-auto object-cover">
                                            </div>';
                                        }

                                        // Button with position control
                                        if (!empty($blockData['button_text'])) {
                                            $buttonPosition = $blockData['button_position'] ?? 'left';
                                            $buttonPositionClasses = [
                                                'left' => 'text-left',
                                                'center' => 'text-center',
                                                'right' => 'text-right'
                                            ];

                                            echo '<div class="mt-8 ' . $buttonPositionClasses[$buttonPosition] . '">';
                                            echo '<a href="' . htmlspecialchars($blockData['button_url'] ?? '#') . '"
                                                class="group relative inline-flex items-center px-6 py-3 rounded-lg overflow-hidden transition-all duration-300" 
                                                style="background-color:' . ($blockData['button_color'] ?? '#4F46E5') . ';">
                                                <span class="relative z-10 text-white font-medium">' .
                                                htmlspecialchars($blockData['button_text']) .
                                                '</span>
                                                <div class="absolute inset-0 bg-white opacity-0 group-hover:opacity-20 transition-opacity duration-300"></div>
                                            </a>';
                                            echo '</div>';
                                        }

                                        echo '</div>'; // Close content container
                                        echo '</div>'; // Close main container
                                        break;

                                    case 'forms':
                                        echo '<div class="max-w-2xl mx-auto p-6 bg-white rounded-xl shadow-lg transition-shadow duration-300 hover:shadow-xl">';
                                        echo '<h2 class="text-3xl font-bold mb-8 text-center" 
                                                  style="font-family:' . $blockData['font_style'] . ';
                                                         color:' . $blockData['font_color'] . ';">' .
                                            htmlspecialchars($blockData['title'] ?? 'Contact Us') .
                                            '</h2>';

                                        if (!empty($blockData['form_fields'])) {
                                            $fields = explode(',', $blockData['form_fields']);
                                            echo '<form class="space-y-6" style="font-family:' . $blockData['font_style'] . ';">';
                                            foreach ($fields as $field) {
                                                $field = trim($field);
                                                echo '<div class="relative">
                                                          <label class="block text-sm font-medium mb-2 transition-colors duration-300 hover:text-indigo-600">' .
                                                    ucfirst($field) .
                                                    '</label>
                                                          <input type="' . ($field === 'email' ? 'email' : 'text') . '" 
                                                                 name="' . $field . '" 
                                                                 class="w-full px-4 py-3 rounded-lg border border-gray-200 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition-all duration-300" 
                                                                 placeholder="Enter your ' . strtolower($field) . '">
                                                          </div>';
                                            }
                                            echo '<button type="submit" 
                                                      class="w-full py-3 px-6 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transform hover:-translate-y-0.5 transition-all duration-300">
                                                      Submit
                                                      </button>';
                                            echo '</form>';
                                        }
                                        echo '</div>';
                                        break;

                                    case 'footer':
                                        echo '<footer class="bg-gradient-to-r from-slate-800 to-slate-900 text-white py-12">';
                                        echo '<div class="max-w-7xl mx-auto px-6 grid grid-cols-1 md:grid-cols-4 gap-12">';

                                        // Company Info Section
                                        echo '<div class="space-y-4">';
                                        echo '<h3 class="text-xl font-bold relative inline-block pb-2 after:content-[\'\'] after:absolute after:bottom-0 after:left-0 after:h-1 after:w-1/3 after:bg-blue-500">';
                                        echo htmlspecialchars($blockData['title'] ?? 'BuildiFy');
                                        echo '</h3>';
                                        echo '<p class="text-gray-300" style="font-family:' . $blockData['font_style'] . ';font-size:' . $blockData['font_size'] . 'px;">';
                                        echo nl2br(htmlspecialchars($blockData['text'] ?? ''));
                                        echo '</p>';
                                        echo '</div>';

                                        // Quick Links
                                        echo '<div class="space-y-4">';
                                        echo '<h3 class="text-xl font-bold">Quick Links</h3>';
                                        echo '<ul class="space-y-2">';
                                        echo '<li><a href="#" class="text-gray-300 hover:text-white transition-colors duration-300">Home</a></li>';
                                        echo '<li><a href="#" class="text-gray-300 hover:text-white transition-colors duration-300">About</a></li>';
                                        echo '<li><a href="#" class="text-gray-300 hover:text-white transition-colors duration-300">Services</a></li>';
                                        echo '<li><a href="#" class="text-gray-300 hover:text-white transition-colors duration-300">Contact</a></li>';
                                        echo '</ul>';
                                        echo '</div>';

                                        // Contact Info
                                        echo '<div class="space-y-4">';
                                        echo '<h3 class="text-xl font-bold">Contact Us</h3>';
                                        echo '<div class="space-y-2 text-gray-300">';
                                        echo '<p class="flex items-center"><i class="fas fa-envelope mr-2"></i> info@buildify.com</p>';
                                        echo '<p class="flex items-center"><i class="fas fa-phone mr-2"></i> +1 234 567 890</p>';
                                        echo '<p class="flex items-center"><i class="fas fa-map-marker-alt mr-2"></i> City, Country</p>';
                                        echo '</div>';
                                        echo '</div>';

                                        // Social Links & Newsletter
                                        echo '<div class="space-y-4">';
                                        echo '<h3 class="text-xl font-bold">Connect With Us</h3>';

                                        // Social Media Links
                                        if (!empty($blockData['social_links'])) {
                                            echo '<div class="flex space-x-4">';
                                            $links = explode(',', $blockData['social_links']);
                                            foreach ($links as $link) {
                                                $link = trim($link);
                                                $platform = strtolower(parse_url($link, PHP_URL_HOST));
                                                $platform = str_replace('.com', '', $platform);
                                                echo '<a href="' . htmlspecialchars($link) . '" target="_blank" 
                                                        class="text-gray-300 hover:text-white transition-colors duration-300 transform hover:scale-110">
                                                        <i class="fab fa-' . $platform . ' text-xl"></i>
                                                      </a>';
                                            }
                                            echo '</div>';
                                        }

                                        // Newsletter Form
                                        echo '<div class="mt-6">';
                                        echo '<form class="flex flex-col space-y-2">';
                                        echo '<input type="email" placeholder="Enter your email" 
                                                class="bg-gray-700 text-white rounded px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">';
                                        echo '<button type="submit" 
                                                class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition-colors duration-300">
                                                Subscribe
                                              </button>';
                                        echo '</form>';
                                        echo '</div>';
                                        echo '</div>';

                                        // Bottom Bar
                                        echo '<div class="col-span-full mt-8 pt-8 border-t border-gray-700 text-center text-gray-400">';
                                        echo '<p style="font-family:' . $blockData['font_style'] . ';">' .
                                            htmlspecialchars($blockData['copyright_text'] ?? 'Copyright © ' . date('Y') . ' BuildiFy. All rights reserved.') .
                                            '</p>';
                                        echo '</div>';

                                        echo '</div>';
                                        echo '</footer>';
                                        break;

                                    case 'cards':
                                        echo '<div class="grid grid-cols-1 md:grid-cols-' . min(3, count($blockData['cards'])) . ' gap-6 p-6">';
                                        foreach ($blockData['cards'] as $index => $card) {
                                            echo '<div class="bg-white rounded-lg shadow-lg overflow-hidden transform transition-transform duration-300 hover:-translate-y-2">
                                                    <div class="relative pb-48 overflow-hidden">
                                                        <img class="absolute inset-0 h-full w-full object-cover transform transition-transform duration-300 hover:scale-105" 
                                                             src="' . htmlspecialchars($card['image']) . '" 
                                                             alt="' . htmlspecialchars($card['title']) . '">
                                                    </div>
                                                    <div class="p-6">
                                                        <h3 class="text-xl font-bold mb-2" style="font-family:' . $blockData['font_style'] . ';
                                                                                             color:' . $blockData['font_color'] . ';">
                                                            ' . htmlspecialchars($card['title']) . '
                                                        </h3>
                                                        <p class="text-gray-600 mb-4" style="font-family:' . $blockData['font_style'] . ';">
                                                            ' . htmlspecialchars($card['description']) . '
                                                        </p>
                                                        ' . (!empty($card['button_text']) ? '
                                                        <a href="' . htmlspecialchars($card['button_url']) . '" 
                                                           class="inline-block px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700 transition-colors duration-300">
                                                            ' . htmlspecialchars($card['button_text']) . '
                                                        </a>' : '') . '
                                                    </div>
                                                </div>';
                                        }
                                        echo '</div>';
                                        break;
                                    
                                    default:
                                        echo '<h3 style="font-family:' . $blockData['font_style'] . ';color:' . $blockData['font_color'] . ';font-size:' . ($blockData['font_size'] + 2) . 'px;font-weight:bold;margin-bottom:10px;">';
                                        echo ucfirst($blockType);
                                        echo '</h3>';
                                        echo '<div style="font-family:' . $blockData['font_style'] . ';color:' . $blockData['font_color'] . ';font-size:' . $blockData['font_size'] . 'px;font-weight:' . $blockData['font_weight'] . ';">';
                                        echo nl2br(htmlspecialchars($blockData['text'] ?? ''));
                                        echo '</div>';
                                }
                                ?>
                            </div>
                            <div class="block-actions absolute right-4 top-4 space-x-2 z-10">
                                <button onclick="editBlock(<?php echo $blockId; ?>)"
                                    class="edit-block bg-blue-500 text-white p-1 rounded hover:bg-blue-600">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <form method="POST" class="inline">
                                    <input type="hidden" name="block_id" value="<?php echo $blockId; ?>">
                                    <button type="submit" name="delete_block"
                                        class="bg-red-500 text-white p-1 rounded hover:bg-red-600">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                <?php
                    }
                }
                ?>
            </div>
        </div>
    </div>

    <!-- Preview Modal -->
    <div id="preview-modal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden">
        <div class="fixed inset-4 bg-white rounded-lg flex flex-col">
            <div class="flex justify-between items-center p-4 border-b">
                <div class="flex items-center space-x-4">
                    <h3 class="text-xl font-bold">Website Preview</h3>
                    <div class="flex space-x-2">
                        <button onclick="setPreviewWidth('mobile')" class="p-2 rounded hover:bg-gray-100">
                            <i class="fas fa-mobile-alt text-gray-600"></i>
                        </button>
                        <button onclick="setPreviewWidth('tablet')" class="p-2 rounded hover:bg-gray-100">
                            <i class="fas fa-tablet-alt text-gray-600"></i>
                        </button>
                        <button onclick="setPreviewWidth('desktop')" class="p-2 rounded hover:bg-gray-100">
                            <i class="fas fa-desktop text-gray-600"></i>
                        </button>
                    </div>
                </div>
                <button onclick="closePreview()" class="text-gray-500 hover:text-gray-700">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            <div class="flex-1 overflow-auto p-4">
                <div id="preview-container" class="mx-auto transition-all duration-300 bg-white">
                    <div id="preview-content"></div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function showBlockFields(blockType) {
            document.querySelectorAll('.block-fields').forEach(el => {
                el.classList.remove('active');
            });
            if (blockType) {
                const blockId = blockType.replace('-', '_') + '-fields';
                const blockElement = document.getElementById(blockId);
                if (blockElement) {
                    blockElement.classList.add('active');
                }
            }
        }

        // Image upload handling for header
        document.getElementById('image_upload').addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(event) {
                    document.getElementById('logo_url').value = event.target.result;

                    const preview = document.getElementById('image-preview');
                    if (!preview) {
                        const preview = document.createElement('img');
                        preview.id = 'image-preview';
                        preview.src = event.target.result;
                        preview.style.maxHeight = '100px';
                        preview.style.marginTop = '10px';
                        document.getElementById('logo_url').insertAdjacentElement('afterend', preview);
                    } else {
                        preview.src = event.target.result;
                    }
                };
                reader.readAsDataURL(file);
            }
        });

        // image upload handling for main content
        document.getElementById('main_content_image_upload').addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(event) {
                    document.getElementById('image_url').value = event.target.result;

                    const preview = document.getElementById('main-content-image-preview');
                    if (!preview) {
                        const preview = document.createElement('img');
                        preview.id = 'main-content-image-preview';
                        preview.src = event.target.result;
                        preview.style.maxHeight = '100px';
                        preview.style.marginTop = '10px';
                        document.getElementById('image_url').insertAdjacentElement('afterend', preview);
                    } else {
                        preview.src = event.target.result;
                    }
                };
                reader.readAsDataURL(file);
            }
        });

        // Add this to your existing script section
        document.getElementById('background_image_upload').addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(event) {
                    document.getElementById('background_image').value = event.target.result;
                };
                reader.readAsDataURL(file);
            }
        });

        //Preview function
        function previewWebsite() {
            const modal = document.getElementById('preview-modal');
            const previewContent = document.getElementById('preview-content');
            const blocksContainer = document.getElementById('blocks-container');

            const previewHtml = blocksContainer.cloneNode(true);

            previewHtml.querySelectorAll('.block-actions').forEach(el => el.remove());

            modal.classList.remove('hidden');
            previewContent.innerHTML = '';
            previewContent.appendChild(previewHtml);

            setPreviewWidth('desktop');

            document.body.style.overflow = 'hidden';
        }

        function closePreview() {
            const modal = document.getElementById('preview-modal');
            modal.classList.add('hidden');
            document.body.style.overflow = 'auto';
        }

        function setPreviewWidth(device) {
            const container = document.getElementById('preview-container');
            const widths = {
                mobile: '375px',
                tablet: '768px',
                desktop: '100%'
            };

            container.style.width = widths[device];

            document.querySelectorAll('#preview-modal .fa-mobile-alt, #preview-modal .fa-tablet-alt, #preview-modal .fa-desktop')
                .forEach(icon => icon.parentElement.classList.remove('bg-gray-100'));

            document.querySelector(`#preview-modal .fa-${device === 'mobile' ? 'mobile-alt' : 
                                                   device === 'tablet' ? 'tablet-alt' : 
                                                   'desktop'}`).parentElement.classList.add('bg-gray-100');
        }
        // Close modal when clicking outside of it
        document.getElementById('preview-modal').addEventListener('click', function(e) {
            if (e.target === this) {
                closePreview();
            }
        });
        // Close modal when pressing X key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closePreview();
            }
        });

        // Update the editBlock function in the script section
        function editBlock(blockId) {
            const blockElement = document.querySelector(`[data-block-id="${blockId}"]`).closest('.block-container');
            const blockData = JSON.parse(blockElement.dataset.blockData);
            const blockType = blockElement.querySelector('.block-content').dataset.blockType;

            // Create edit form with additional fields
            const editForm = document.createElement('form');
            editForm.classList.add('block-edit-form', 'p-4', 'bg-white', 'shadow-lg', 'rounded-lg');
            editForm.innerHTML = `
                <input type="hidden" name="edit_block_id" value="${blockId}">
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium mb-1">Title</label>
                        <input type="text" name="title" value="${blockData.title || ''}" 
                            class="w-full p-2 border rounded">
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Content</label>
                        <textarea name="text" rows="4" 
                            class="w-full p-2 border rounded">${blockData.text || ''}</textarea>
                    </div>
                    
                    <!-- Font Settings -->
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium mb-1">Font Style</label>
                            <select name="font_style" class="w-full p-2 border rounded">
                                <option value="Arial, sans-serif" ${blockData.font_style === 'Arial, sans-serif' ? 'selected' : ''}>Arial</option>
                                <option value="'Helvetica Neue', sans-serif" ${blockData.font_style === "'Helvetica Neue', sans-serif" ? 'selected' : ''}>Helvetica</option>
                                <option value="Georgia, serif" ${blockData.font_style === 'Georgia, serif' ? 'selected' : ''}>Georgia</option>
                                <option value="'Times New Roman', serif" ${blockData.font_style === "'Times New Roman', serif" ? 'selected' : ''}>Times New Roman</option>
                                <option value="Verdana, sans-serif" ${blockData.font_style === 'Verdana, sans-serif' ? 'selected' : ''}>Verdana</option>
                                <option value="'Courier New', monospace" ${blockData.font_style === "'Courier New', monospace" ? 'selected' : ''}>Courier New</option>
                                <option value="'Trebuchet MS', sans-serif" ${blockData.font_style === "'Trebuchet MS', sans-serif" ? 'selected' : ''}>Trebuchet MS</option>
                                <option value="Impact, sans-serif" ${blockData.font_style === 'Impact, sans-serif' ? 'selected' : ''}>Impact</option>
                                <option value="'Open Sans', sans-serif" ${blockData.font_style === "'Open Sans', sans-serif" ? 'selected' : ''}>Open Sans</option>
                                <option value="'Roboto', sans-serif" ${blockData.font_style === "'Roboto', sans-serif" ? 'selected' : ''}>Roboto</option>
                                <option value="'Lato', sans-serif" ${blockData.font_style === "'Lato', sans-serif" ? 'selected' : ''}>Lato</option>
                                <option value="'Montserrat', sans-serif" ${blockData.font_style === "'Montserrat', sans-serif" ? 'selected' : ''}>Montserrat</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-1">Font Size (px)</label>
                            <input type="number" name="font_size" value="${blockData.font_size || '16'}"
                                class="w-full p-2 border rounded" min="8" max="72">
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium mb-1">Font Weight</label>
                            <select name="font_weight" class="w-full p-2 border rounded">
                                <option value="100" ${blockData.font_weight === '100' ? 'selected' : ''}>Thin</option>
                                <option value="100 italic" ${blockData.font_weight === '100 italic' ? 'selected' : ''}>Thin Italic</option>
                                <option value="200" ${blockData.font_weight === '200' ? 'selected' : ''}>Extra Light</option>
                                <option value="200 italic" ${blockData.font_weight === '200 italic' ? 'selected' : ''}>Extra Light Italic</option>
                                <option value="300" ${blockData.font_weight === '300' ? 'selected' : ''}>Light</option>
                                <option value="300 italic" ${blockData.font_weight === '300 italic' ? 'selected' : ''}>Light Italic</option>
                                <option value="400" ${blockData.font_weight === '400' || blockData.font_weight === 'normal' ? 'selected' : ''}>Normal</option>
                                <option value="400 italic" ${blockData.font_weight === '400 italic' ? 'selected' : ''}>Normal Italic</option>
                                <option value="500" ${blockData.font_weight === '500' ? 'selected' : ''}>Medium</option>
                                <option value="500 italic" ${blockData.font_weight === '500 italic' ? 'selected' : ''}>Medium Italic</option>
                                <option value="600" ${blockData.font_weight === '600' ? 'selected' : ''}>Semi Bold</option>
                                <option value="600 italic" ${blockData.font_weight === '600 italic' ? 'selected' : ''}>Semi Bold Italic</option>
                                <option value="700" ${blockData.font_weight === '700' || blockData.font_weight === 'bold' ? 'selected' : ''}>Bold</option>
                                <option value="700 italic" ${blockData.font_weight === '700 italic' ? 'selected' : ''}>Bold Italic</option>
                                <option value="800" ${blockData.font_weight === '800' ? 'selected' : ''}>Extra Bold</option>
                                <option value="800 italic" ${blockData.font_weight === '800 italic' ? 'selected' : ''}>Extra Bold Italic</option>
                                <option value="900" ${blockData.font_weight === '900' ? 'selected' : ''}>Black</option>
                                <option value="900 italic" ${blockData.font_weight === '900 italic' ? 'selected' : ''}>Black Italic</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-1">Font Color</label>
                            <input type="color" name="font_color" 
                                value="${blockData.font_color || '#000000'}" 
                                class="w-full">
                        </div>
                    </div>

                    <!-- Image Upload -->
                    ${blockType === 'header' || blockType === 'main_content' ? `
                        <div>
                            <label class="block text-sm font-medium mb-1">
                                ${blockType === 'header' ? 'Logo/Image' : 'Content Image'}
                            </label>
                            <input type="text" name="${blockType === 'header' ? 'logo_url' : 'image_url'}" 
                                value="${blockType === 'header' ? blockData.logo_url || '' : blockData.image_url || ''}"
                                class="w-full p-2 border rounded mb-2" placeholder="Image URL">
                            <input type="file" class="hidden" id="edit_image_upload_${blockId}" accept="image/*">
                            <button type="button" 
                                onclick="document.getElementById('edit_image_upload_${blockId}').click()"
                                class="bg-gray-600 text-white px-3 py-1 rounded text-sm hover:bg-gray-700">
                                <i class="fas fa-upload mr-1"></i> Upload New Image
                            </button>
                            ${(blockData.logo_url || blockData.image_url) ? `
                                <div class="mt-2">
                                    <img src="${blockData.logo_url || blockData.image_url}" 
                                        alt="Current Image" class="max-h-20 mt-2">
                                </div>
                            ` : ''}
                        </div>
                    ` : ''}

                    <!-- Background Settings -->
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium mb-1">Background Color</label>
                            <input type="color" name="background_color" 
                                value="${blockData.background_color || '#ffffff'}" 
                                class="w-full">
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-1">Border Radius (px)</label>
                            <input type="number" name="border_radius" 
                                value="${blockData.border_radius || '0'}"
                                class="w-full p-2 border rounded" min="0" max="50">
                        </div>
                    </div>

                    <div class="flex justify-end space-x-2 mt-4">
                        <button type="button" onclick="cancelEdit(this)" 
                            class="px-4 py-2 bg-gray-500 text-white rounded hover:bg-gray-600">
                            Cancel
                        </button>
                        <button type="submit" 
                            class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">
                            Save Changes
                        </button>
                    </div>
                </div>
            `;

            // Add image upload handler
            if (blockType === 'header' || blockType === 'main_content') {
                const imageInput = editForm.querySelector(`#edit_image_upload_${blockId}`);
                const imageUrlInput = editForm.querySelector(`[name="${blockType === 'header' ? 'logo_url' : 'image_url'}"]`);

                imageInput.addEventListener('change', function(e) {
                    const file = e.target.files[0];
                    if (file) {
                        const reader = new FileReader();
                        reader.onload = function(event) {
                            imageUrlInput.value = event.target.result;
                            // Update preview
                            const preview = editForm.querySelector('img');
                            if (preview) {
                                preview.src = event.target.result;
                            } else {
                                const newPreview = document.createElement('img');
                                newPreview.src = event.target.result;
                                newPreview.alt = 'Preview';
                                newPreview.className = 'max-h-20 mt-2';
                                imageUrlInput.parentNode.appendChild(newPreview);
                            }
                        };
                        reader.readAsDataURL(file);
                    }
                });
            }

            // Replace block content with edit form
            const blockContent = blockElement.querySelector('.block-content');
            blockContent.style.display = 'none';
            blockElement.appendChild(editForm);

            // Handle form submission
            editForm.addEventListener('submit', async (e) => {
                e.preventDefault();
                const formData = new FormData(editForm);

                try {
                    const response = await fetch('update_block.php', {
                        method: 'POST',
                        body: formData
                    });

                    if (response.ok) {
                        location.reload();
                    } else {
                        alert('Failed to update block');
                    }
                } catch (error) {
                    console.error('Error:', error);
                    alert('Failed to update block');
                }
            });
        }

        function cancelEdit(button) {
            const editForm = button.closest('.block-edit-form');
            const blockElement = editForm.closest('.block-container');
            const blockContent = blockElement.querySelector('.block-content');

            editForm.remove();
            blockContent.style.display = 'block';
        }

        function updateCardFields(count) {
            const container = document.getElementById('cards-container');
            const existingCards = container.children.length;
            
            // Remove extra cards if needed
            while (container.children.length > count) {
                container.removeChild(container.lastChild);
            }
            
            // Add new cards if needed
            for (let i = existingCards + 1; i <= count; i++) {
                const cardHtml = `
                    <div class="card-fields mb-6 p-4 border border-gray-600 rounded">
                        <h4 class="text-white mb-4">Card ${i}</h4>
                        <div class="mb-3">
                            <label class="block text-gray-300 text-sm font-bold mb-2">Card Title</label>
                            <input class="w-full bg-gray-700 text-white rounded p-2" name="card_title_${i}">
                        </div>
                        <div class="mb-3">
                            <label class="block text-gray-300 text-sm font-bold mb-2">Card Description</label>
                            <textarea class="w-full bg-gray-700 text-white rounded p-2" name="card_description_${i}" rows="3"></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="block text-gray-300 text-sm font-bold mb-2">Card Image</label>
                            <input type="text" class="w-full bg-gray-700 text-white rounded p-2" name="card_image_${i}" placeholder="Image URL">
                            <input type="file" id="card_image_upload_${i}" class="hidden" accept="image/*">
                            <button type="button" onclick="handleCardImageUpload(${i})" 
                                class="mt-1 bg-gray-600 text-white px-3 py-1 rounded text-sm">
                                <i class="fas fa-upload mr-1"></i> Upload Image
                            </button>
                            <div id="card_image_preview_${i}" class="mt-2"></div>
                        </div>
                        <div class="mb-3">
                            <label class="block text-gray-300 text-sm font-bold mb-2">Button Text</label>
                            <input class="w-full bg-gray-700 text-white rounded p-2" name="card_button_text_${i}">
                        </div>
                        <div class="mb-3">
                            <label class="block text-gray-300 text-sm font-bold mb-2">Button URL</label>
                            <input class="w-full bg-gray-700 text-white rounded p-2" name="card_button_url_${i}">
                        </div>
                    </div>
                `;
                container.insertAdjacentHTML('beforeend', cardHtml);
            }
        }

        // Add this new function to handle card image uploads
        function handleCardImageUpload(cardIndex) {
            const fileInput = document.getElementById(`card_image_upload_${cardIndex}`);
            const imageUrlInput = document.querySelector(`[name="card_image_${cardIndex}"]`);
            const previewContainer = document.getElementById(`card_image_preview_${cardIndex}`);

            fileInput.click();

            fileInput.onchange = function(e) {
                const file = e.target.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(event) {
                        imageUrlInput.value = event.target.result;
                        
                        // Update preview
                        previewContainer.innerHTML = `
                            <img src="${event.target.result}" 
                                 alt="Card ${cardIndex} Preview" 
                                 class="max-h-32 mt-2 rounded shadow-sm">
                        `;
                    };
                    reader.readAsDataURL(file);
                }
            };
        }

        // Add this to initialize image upload handlers for existing cards
        document.addEventListener('DOMContentLoaded', function() {
            const container = document.getElementById('cards-container');
            const cardCount = container.children.length;
            
            // Initialize handlers for existing cards
            for (let i = 1; i <= cardCount; i++) {
                const fileInput = document.getElementById(`card_image_upload_${i}`);
                if (fileInput) {
                    fileInput.addEventListener('change', function(e) {
                        const file = e.target.files[0];
                        if (file) {
                            const reader = new FileReader();
                            reader.onload = function(event) {
                                const imageUrlInput = document.querySelector(`[name="card_image_${i}"]`);
                                imageUrlInput.value = event.target.result;
                                
                                // Update preview
                                const previewContainer = document.getElementById(`card_image_preview_${i}`);
                                if (previewContainer) {
                                    previewContainer.innerHTML = `
                                        <img src="${event.target.result}" 
                                             alt="Card ${i} Preview" 
                                             class="max-h-32 mt-2 rounded shadow-sm">
                                    `;
                                }
                            };
                            reader.readAsDataURL(file);
                        }
                    });
                }
            }
        });
    </script>
</body>

</html>