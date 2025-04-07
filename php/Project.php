<?php
session_start();
include '/Buildify/config.php';

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
                break;

            case 'main_content':
                $blockData['columns'] = $_POST['columns'] ?? '1';
                $blockData['content_type'] = $_POST['content_type'] ?? 'text';
                $blockData['image_url'] = $_POST['image_url'] ?? '';
                $blockData['button_text'] = $_POST['button_text'] ?? '';
                $blockData['button_url'] = $_POST['button_url'] ?? '';
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
                            Logo URL
                        </label>
                        <input class="w-full bg-gray-700 text-white rounded p-2" id="logo_url" name="logo_url">
                    </div>

                    <div class="mb-3">
                        <label class="block text-gray-300 text-sm font-bold mb-2" for="menu_items">
                            Menu Items (comma separated)
                        </label>
                        <input class="w-full bg-gray-700 text-white rounded p-2" id="menu_items" name="menu_items" value="Home,About Us,Contact Us">
                    </div>
                </div>

                <!-- Main Content Specific Fields -->
                <div id="main-content-fields" class="block-fields">
                    <div class="mb-3">
                        <label class="block text-gray-300 text-sm font-bold mb-2" for="content_type">
                            Content Type
                        </label>
                        <select class="w-full bg-gray-700 text-white rounded p-2" id="content_type" name="content_type">
                            <option value="text">Text</option>
                            <option value="image">Image</option>
                            <option value="text_image">Text + Image</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="block text-gray-300 text-sm font-bold mb-2" for="columns">
                            Columns
                        </label>
                        <select class="w-full bg-gray-700 text-white rounded p-2" id="columns" name="columns">
                            <option value="1">1 Column</option>
                            <option value="2">2 Columns</option>
                            <option value="3">3 Columns</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="block text-gray-300 text-sm font-bold mb-2" for="image_url">
                            Image URL
                        </label>
                        <input class="w-full bg-gray-700 text-white rounded p-2" id="image_url" name="image_url">
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
                                    echo '<div class="flex items-center">';
                                    if (!empty($blockData['logo_url'])) {
                                        echo '<img src="' . htmlspecialchars($blockData['logo_url']) . '" alt="Logo" style="height:40px;margin-right:20px;">';
                                    }
                                    echo '<h1 style="font-family:' . $blockData['font_style'] . ';color:' . $blockData['font_color'] . ';font-size:' . $blockData['font_size'] . 'px;font-weight:' . $blockData['font_weight'] . ';">';
                                    echo htmlspecialchars($blockData['title'] ?? 'Header');
                                    echo '</h1>';

                                    // Render menu items
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
                                        echo '<a href="' . htmlspecialchars($blockData['button_url'] ?? '#') . '" style="';
                                        echo 'background-color:' . $blockData['button_color'] . ';';
                                        echo 'font-family:' . $blockData['font_style'] . ';';
                                        echo 'color:white;';
                                        echo 'font-size:' . $blockData['font_size'] . 'px;';
                                        echo 'padding:8px 16px;';
                                        echo 'border-radius:4px;';
                                        echo 'display:inline-block;';
                                        echo 'text-decoration:none;">';
                                        echo htmlspecialchars($blockData['button_text']);
                                        echo '</a>';
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
                document.getElementById(blockType + '-fields').classList.add('active');
            }
        }
        document.addEventListener('DOMContentLoaded', function() {
            showBlockFields('');
        });
    </script>
</body>

</html>