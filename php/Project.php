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
                                <option value="normal">Normal</option>
                                <option value="bold">Bold</option>
                                <option value="lighter">Light</option>
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
                        <div class="block-container relative p-4" style="
                                background-color: <?php echo $blockData['background_color']; ?>;
                                padding: <?php echo $blockData['padding']; ?>px;
                                margin: <?php echo $blockData['margin']; ?>px;
                                border-radius: <?php echo $blockData['border_radius']; ?>px;
                            ">
                            <div class="block-actions absolute right-4 top-4 space-x-2">
                                <button class="edit-block bg-blue-500 text-white p-1 rounded" data-block-id="<?php echo $blockId; ?>">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <form method="POST" class="inline">
                                    <input type="hidden" name="block_id" value="<?php echo $blockId; ?>">
                                    <button type="submit" name="delete_block" class="bg-red-500 text-white p-1 rounded">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>

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
                <?php
                    }
                }
                ?>
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
    </script>
</body>

</html>