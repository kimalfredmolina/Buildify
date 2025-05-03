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
                    break;

                case 'forms':
                    $blockData['form_type'] = $_POST['form_type'] ?? 'contact';
                    $blockData['form_fields'] = $_POST['form_fields'] ?? 'name,email,message';
                    break;

                case 'footer':
                    $blockData['copyright_text'] = $_POST['copyright_text'] ?? 'Copyright © ' . date('Y');
                    $blockData['social_links'] = $_POST['social_links'] ?? '';
                    break;
                    
                case 'grade_computation':
                    $blockData['initial_columns'] = $_POST['initial_columns'] ?? 'Midterm,Finals';
                    $blockData['initial_subjects'] = $_POST['initial_subjects'] ?? 'Subject 1,Subject 2,Subject 3';
                    $blockData['button_text'] = $_POST['button_text'] ?? 'Calculate Grades';
                    $blockData['button_color'] = $_POST['button_color'] ?? '#4F46E5';
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
                            <option value="grade_computation">Grade Computation</option>
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

                    <!-- Grade Computation Specific Fields -->
                    <div id="grade_computation-fields" class="block-fields">
                        <div class="mb-3">
                            <label class="block text-gray-300 text-sm font-bold mb-2" for="initial_columns">
                                Initial Columns
                            </label>
                            <input class="w-full bg-gray-700 text-white rounded p-2" id="initial_columns" name="initial_columns" value="Midterm,Finals" placeholder="Comma separated column names">
                        </div>

                        <div class="mb-3">
                            <label class="block text-gray-300 text-sm font-bold mb-2" for="initial_subjects">
                                Initial Subjects
                            </label>
                            <input class="w-full bg-gray-700 text-white rounded p-2" id="initial_subjects" name="initial_subjects" value="Subject 1,Subject 2,Subject 3" placeholder="Comma separated subject names">
                        </div>

                        <div class="mb-3">
                            <label class="block text-gray-300 text-sm font-bold mb-2" for="grade_button_text">
                                Calculate Button Text
                            </label>
                            <input class="w-full bg-gray-700 text-white rounded p-2" id="grade_button_text" name="button_text" value="Calculate Grades">
                        </div>

                        <div class="mb-3">
                            <label class="block text-gray-300 text-sm font-bold mb-2" for="grade_button_color">
                                Button Color
                            </label>
                            <input class="w-full h-10" id="grade_button_color" type="color" name="button_color" value="#4F46E5">
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
                                        echo '<div class="flex items-center">';

                                        // Image/logo with positioning
                                        if (!empty($blockData['logo_url'])) {
                                            $positionClass = 'mr-4'; // default left position
                                            if ($blockData['image_position'] === 'center') {
                                                $positionClass = 'mx-auto';
                                            } elseif ($blockData['image_position'] === 'right') {
                                                $positionClass = 'ml-auto order-last';
                                            }

                                            echo '<img src="' . htmlspecialchars($blockData['logo_url']) . '" alt="Logo" style="height:40px;" class="' . $positionClass . '">';
                                        }

                                        // Title
                                        echo '<h1 style="font-family:' . $blockData['font_style'] . ';color:' . $blockData['font_color'] . ';font-size:' . $blockData['font_size'] . 'px;font-weight:' . $blockData['font_weight'] . ';">';
                                        echo htmlspecialchars($blockData['title'] ?? 'Header');
                                        echo '</h1>';

                                        // Menu items
                                        if (!empty($blockData['menu_items'])) {
                                            $items = explode(',', $blockData['menu_items']);
                                            echo '<div style="margin-left:auto;">';
                                            foreach ($items as $item) {
                                                echo '<a href="#" style="font-family:' . $blockData['font_style'] . ';color:' . $blockData['font_color'] . ';font-size:' . ($blockData['font_size'] - 2) . 'px;margin-left:15px;text-decoration:none;">';
                                                echo htmlspecialchars(trim($item));
                                                echo '</a>';
                                            }
                                            echo '</div>';
                                        }
                                        echo '</div>';
                                        break;

                                    case 'main_content':
                                        echo '<h2 style="font-family:' . $blockData['font_style'] . ';color:' . $blockData['font_color'] . ';font-size:' . ($blockData['font_size'] + 2) . 'px;font-weight:bold;margin-bottom:15px;">';
                                        echo htmlspecialchars($blockData['title'] ?? 'Main Content');
                                        echo '</h2>';

                                        echo '<div style="font-family:' . $blockData['font_style'] . ';color:' . $blockData['font_color'] . ';font-size:' . $blockData['font_size'] . 'px;font-weight:' . $blockData['font_weight'] . ';">';
                                        echo nl2br(htmlspecialchars($blockData['text'] ?? ''));
                                        echo '</div>';

                                        if (!empty($blockData['image_url'])) {
                                            echo '<div class="mt-4"><img src="' . htmlspecialchars($blockData['image_url']) . '" alt="Content Image" style="max-width:100%;"></div>';
                                        }

                                        if (!empty($blockData['button_text'])) {
                                            echo '<div class="mt-4">';
                                            echo '<button style="';
                                            echo 'background-color:' . $blockData['button_color'] . ';';
                                            echo 'font-family:' . $blockData['font_style'] . ';';
                                            echo 'color:white;';
                                            echo 'font-size:' . $blockData['font_size'] . 'px;';
                                            echo 'padding:8px 16px;';
                                            echo 'border-radius:4px;';
                                            echo 'border:none;';
                                            echo 'cursor:pointer;">';
                                            echo htmlspecialchars($blockData['button_text']);
                                            echo '</button>';
                                            echo '</div>';
                                        }
                                        break;

                                    case 'forms':
                                        echo '<h2 style="font-family:' . $blockData['font_style'] . ';color:' . $blockData['font_color'] . ';font-size:' . ($blockData['font_size'] + 2) . 'px;font-weight:bold;margin-bottom:15px;">';
                                        echo htmlspecialchars($blockData['title'] ?? 'Form');
                                        echo '</h2>';

                                        if (!empty($blockData['form_fields'])) {
                                            $fields = explode(',', $blockData['form_fields']);
                                            echo '<form style="font-family:' . $blockData['font_style'] . ';color:' . $blockData['font_color'] . ';">';
                                            foreach ($fields as $field) {
                                                $field = trim($field);
                                                echo '<div class="mb-3">';
                                                echo '<label class="block mb-1">' . ucfirst($field) . '</label>';
                                                echo '<input type="' . ($field === 'email' ? 'email' : 'text') . '" name="' . $field . '" class="w-full p-2 border rounded" style="font-family:inherit;">';
                                                echo '</div>';
                                            }
                                            echo '<button type="submit" style="';
                                            echo 'background-color:#007bff;';
                                            echo 'color:white;';
                                            echo 'font-family:inherit;';
                                            echo 'padding:8px 16px;';
                                            echo 'border-radius:4px;';
                                            echo 'border:none;">';
                                            echo 'Submit';
                                            echo '</button>';
                                            echo '</form>';
                                        }
                                        break;

                                    case 'footer':
                                        echo '<div class="flex justify-between items-center">';
                                        echo '<div style="font-family:' . $blockData['font_style'] . ';color:' . $blockData['font_color'] . ';font-size:' . $blockData['font_size'] . 'px;">';
                                        echo htmlspecialchars($blockData['copyright_text'] ?? 'Copyright © ' . date('Y'));
                                        echo '</div>';

                                        if (!empty($blockData['social_links'])) {
                                            $links = explode(',', $blockData['social_links']);
                                            echo '<div>';
                                            foreach ($links as $link) {
                                                $link = trim($link);
                                                if (!empty($link)) {
                                                    echo '<a href="' . htmlspecialchars($link) . '" target="_blank" style="';
                                                    echo 'color:' . $blockData['font_color'] . ';';
                                                    echo 'font-size:' . $blockData['font_size'] . 'px;';
                                                    echo 'margin-left:10px;';
                                                    echo 'text-decoration:none;">';
                                                    echo '<i class="fab fa-' . strtolower(parse_url($link, PHP_URL_HOST)) . '"></i>';
                                                    echo '</a>';
                                                }
                                            }
                                            echo '</div>';
                                        }
                                        echo '</div>';
                                        break;

                                    case 'grade_computation':
                                        echo '<h2 style="font-family:' . $blockData['font_style'] . ';color:' . $blockData['font_color'] . ';font-size:' . ($blockData['font_size'] + 2) . 'px;font-weight:bold;margin-bottom:15px;">';
                                        echo htmlspecialchars($blockData['title'] ?? 'Grade Computation');
                                        echo '</h2>';

                                        if (!empty($blockData['text'])) {
                                            echo '<div style="font-family:' . $blockData['font_style'] . ';color:' . $blockData['font_color'] . ';font-size:' . $blockData['font_size'] . 'px;font-weight:' . $blockData['font_weight'] . ';margin-bottom:15px;">';
                                            echo nl2br(htmlspecialchars($blockData['text']));
                                            echo '</div>';
                                        }

                                        // Parse initial columns and subjects
                                        $columns = !empty($blockData['initial_columns']) ? explode(',', $blockData['initial_columns']) : ['Midterm', 'Finals'];
                                        $subjects = !empty($blockData['initial_subjects']) ? explode(',', $blockData['initial_subjects']) : ['Subject 1', 'Subject 2', 'Subject 3'];

                                        echo '<div class="grade-computation-form" style="font-family:' . $blockData['font_style'] . ';">';

                                        // Main container
                                        echo '<div class="flex flex-wrap gap-4 mb-6">';

                                        // Generate columns
                                        foreach ($columns as $index => $column) {
                                            $columnId = 'column-' . $index;
                                            echo '<div class="flex-1 min-w-[280px] border rounded-lg shadow-sm bg-white overflow-hidden">';

                                            // Column header
                                            echo '<div class="border-b p-3 bg-gray-50">';
                                            echo '<div class="flex items-center justify-between">';
                                            echo '<input type="text" value="' . htmlspecialchars(trim($column)) . '" class="font-semibold text-lg border rounded px-2 py-1 w-full" placeholder="Column Title">';
                                            echo '<button type="button" class="ml-2 text-red-500 hover:text-red-700" title="Remove Column">';
                                            echo '<i class="fas fa-trash"></i>';
                                            echo '</button>';
                                            echo '</div>';
                                            echo '</div>';

                                            // Column content - subjects
                                            echo '<div class="p-4 space-y-3">';
                                            foreach ($subjects as $subIndex => $subject) {
                                                $subjectId = 'subject-' . $subIndex;
                                                echo '<div class="flex items-center gap-2">';
                                                echo '<div class="flex-1">';
                                                echo '<input type="text" value="' . htmlspecialchars(trim($subject)) . '" class="w-full border rounded px-2 py-1 text-sm" placeholder="Subject name">';
                                                echo '</div>';
                                                echo '<div class="w-20">';
                                                echo '<input type="text" class="w-full border rounded px-2 py-1 text-sm text-right" placeholder="Grade">';
                                                echo '</div>';
                                                echo '<button type="button" class="text-red-500 hover:text-red-700" title="Remove Subject">';
                                                echo '<i class="fas fa-trash"></i>';
                                                echo '</button>';
                                                echo '</div>';
                                            }
                                            echo '</div>';

                                            // Column footer
                                            echo '<div class="border-t p-3 bg-gray-50 flex justify-center">';
                                            echo '<button type="button" class="w-full px-3 py-1 border rounded text-sm bg-gray-100 hover:bg-gray-200">';
                                            echo '<i class="fas fa-plus mr-1"></i> Add Subject';
                                            echo '</button>';
                                            echo '</div>';

                                            echo '</div>';
                                        }

                                        // Add column button
                                        echo '<div class="flex items-center justify-center min-w-[200px] h-[300px]">';
                                        echo '<button type="button" class="h-16 w-40 border-dashed border-2 rounded-lg hover:bg-gray-50">';
                                        echo '<i class="fas fa-plus mr-1"></i> Add Column';
                                        echo '</button>';
                                        echo '</div>';

                                        echo '</div>'; // End of columns container

                                        // Results section
                                        $resultsSectionId = "results-section-" . $blockId;
                                        $columnAveragesId = "column-averages-" . $blockId;
                                        $semesterAverageId = "semester-average-" . $blockId;

                                        echo '<div class="bg-gray-50 border rounded-lg p-4 mb-6 hidden" id="' . $resultsSectionId . '">';
                                        echo '<h3 class="text-lg font-semibold mb-2">Results</h3>';
                                        echo '<div class="grid grid-cols-1 md:grid-cols-2 gap-4">';
                                        echo '<div>';
                                        echo '<h4 class="text-sm font-medium mb-2">Column Averages:</h4>';
                                        echo '<div class="flex flex-wrap gap-2" id="' . $columnAveragesId . '"></div>';
                                        echo '</div>';
                                        echo '<div class="flex items-center justify-center md:justify-end">';
                                        echo '<div class="text-center">';
                                        echo '<div class="text-sm font-medium">Semester Average</div>';
                                        echo '<div class="text-3xl font-bold" id="' . $semesterAverageId . '">0.00</div>';
                                        echo '</div>';
                                        echo '</div>';
                                        echo '</div>';
                                        echo '</div>';

                                        // Calculate button
                                        echo '<div class="flex justify-center">';
                                        echo '<button type="button" style="background-color:' . $blockData['button_color'] . ';color:white;padding:10px 20px;border-radius:4px;font-weight:bold;" class="w-full max-w-xs">';
                                        echo '<i class="fas fa-calculator mr-2"></i> ' . htmlspecialchars($blockData['button_text'] ?? 'Calculate Grades');
                                        echo '</button>';
                                        echo '</div>';

                                        echo '</div>'; // End of grade-computation-form

                                        // Add JavaScript for interactivity
                                        echo '<script>
                                        document.addEventListener("DOMContentLoaded", function() {
                                            // This is just a placeholder. In a real implementation, you would add JavaScript
                                            // to handle adding/removing columns and subjects, and calculating grades.
                                            console.log("Grade computation form loaded");
                                        });
                                        </script>';
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
            // Grade computation form interactivity
            document.addEventListener('click', function(e) {
                // Check if we're inside a grade computation block
                const gradeForm = e.target.closest('.grade-computation-form');
                if (!gradeForm) return;

                // Add column button
                if (e.target.closest('button') && e.target.closest('button').textContent.includes('Add Column')) {
                    const columnsContainer = gradeForm.querySelector('.flex.flex-wrap.gap-4');
                    const addColumnBtn = e.target.closest('div');

                    const newColumn = document.createElement('div');
                    newColumn.className = 'flex-1 min-w-[280px] border rounded-lg shadow-sm bg-white overflow-hidden';
                    newColumn.innerHTML = `
                    <div class="border-b p-3 bg-gray-50">
                        <div class="flex items-center justify-between">
                            <input type="text" value="New Column" class="font-semibold text-lg border rounded px-2 py-1 w-full" placeholder="Column Title">
                            <button type="button" class="ml-2 text-red-500 hover:text-red-700" title="Remove Column">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                    <div class="p-4 space-y-3">
                        <div class="flex items-center gap-2">
                            <div class="flex-1">
                                <input type="text" value="Subject 1" class="w-full border rounded px-2 py-1 text-sm" placeholder="Subject name">
                            </div>
                            <div class="w-20">
                                <input type="text" class="w-full border rounded px-2 py-1 text-sm text-right" placeholder="Grade">
                            </div>
                            <button type="button" class="text-red-500 hover:text-red-700" title="Remove Subject">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                    <div class="border-t p-3 bg-gray-50 flex justify-center">
                        <button type="button" class="w-full px-3 py-1 border rounded text-sm bg-gray-100 hover:bg-gray-200">
                            <i class="fas fa-plus mr-1"></i> Add Subject
                        </button>
                    </div>
                `;

                    columnsContainer.insertBefore(newColumn, addColumnBtn);
                }

                // Remove column button
                if (e.target.closest('button') && e.target.closest('button').title === 'Remove Column') {
                    const column = e.target.closest('.flex-1');
                    column.remove();
                }

                // Add subject button
                if (e.target.closest('button') && e.target.closest('button').textContent.includes('Add Subject')) {
                    const subjectsContainer = e.target.closest('.flex-1').querySelector('.p-4.space-y-3');
                    const subjectCount = subjectsContainer.querySelectorAll('.flex.items-center.gap-2').length + 1;

                    const newSubject = document.createElement('div');
                    newSubject.className = 'flex items-center gap-2';
                    newSubject.innerHTML = `
                    <div class="flex-1">
                        <input type="text" value="Subject ${subjectCount}" class="w-full border rounded px-2 py-1 text-sm" placeholder="Subject name">
                    </div>
                    <div class="w-20">
                        <input type="text" class="w-full border rounded px-2 py-1 text-sm text-right" placeholder="Grade">
                    </div>
                    <button type="button" class="text-red-500 hover:text-red-700" title="Remove Subject">
                        <i class="fas fa-trash"></i>
                    </button>
                `;

                    subjectsContainer.appendChild(newSubject);
                }

                // Remove subject button
                if (e.target.closest('button') && e.target.closest('button').title === 'Remove Subject') {
                    const subject = e.target.closest('.flex.items-center.gap-2');
                    const subjectsContainer = subject.parentElement;

                    // Only remove if there's more than one subject
                    if (subjectsContainer.querySelectorAll('.flex.items-center.gap-2').length > 1) {
                        subject.remove();
                    }
                }

                // Calculate button
                if (e.target.closest('button') && e.target.closest('button').textContent.includes('Calculate')) {
                    const columns = gradeForm.querySelectorAll('.flex-1.min-w-\\[280px\\]');
                    const resultsSection = gradeForm.querySelector('#results-section');
                    const columnAveragesContainer = gradeForm.querySelector('#column-averages');
                    const semesterAverageElement = gradeForm.querySelector('#semester-average');

                    // Show results section
                    resultsSection.classList.remove('hidden');

                    // Clear previous results
                    columnAveragesContainer.innerHTML = '';

                    const columnAverages = [];

                    // Calculate average for each column
                    columns.forEach(column => {
                        const columnTitle = column.querySelector('input.font-semibold').value;
                        const gradeInputs = column.querySelectorAll('.w-20 input');

                        let sum = 0;
                        let count = 0;

                        gradeInputs.forEach(input => {
                            const grade = parseFloat(input.value);
                            if (!isNaN(grade)) {
                                sum += grade;
                                count++;
                            }
                        });

                        const average = count > 0 ? sum / count : 0;
                        columnAverages.push(average);

                        // Add to results
                        if (average > 0) {
                            const badge = document.createElement('div');
                            badge.className = 'px-2 py-1 rounded border bg-white text-sm';
                            badge.textContent = `${columnTitle}: ${average.toFixed(2)}`;
                            columnAveragesContainer.appendChild(badge);
                        }
                    });

                    // Calculate semester average
                    const validAverages = columnAverages.filter(avg => avg > 0);
                    const semesterAverage = validAverages.length > 0 ?
                        validAverages.reduce((sum, avg) => sum + avg, 0) / validAverages.length :
                        0;

                    semesterAverageElement.textContent = semesterAverage.toFixed(2);
                }
            });
        </script>
    </body>

    </html>