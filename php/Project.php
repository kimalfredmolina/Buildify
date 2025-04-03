<?php
session_start();
include '/Buildify/config.php';

// Get project ID from URL
$projectId = isset($_GET['id']) ? intval($_GET['id']) : null;

// Fetch project details
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

// Handle block configuration submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add_block'])) {
        $blockType = $_POST['block_type'] ?? '';
        $blockData = json_encode([
            'title' => $_POST['title'] ?? '',
            'background_color' => $_POST['background_color'] ?? '#ffffff',
            'font_style' => $_POST['font_style'] ?? 'Arial',
            'font_color' => $_POST['font_color'] ?? '#000000',
            'font_size' => $_POST['font_size'] ?? '16',
            'font_weight' => $_POST['font_weight'] ?? 'normal',
            'text' => $_POST['text'] ?? '',
            'logo_url' => $_POST['logo_url'] ?? '',
            'menu_items' => $_POST['menu_items'] ?? 'Home,About Us,Contact Us'
        ]);

        $stmt = $conn->prepare("INSERT INTO project_blocks (project_id, block_type, data) VALUES (?, ?, ?)");
        $stmt->bind_param("iss", $projectId, $blockType, $blockData);
        $stmt->execute();

        header("Location: Project.php?id=$projectId");
        exit();
    }

    // Delete block
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
    <title>Buildify CMS - Edit Project</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
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

        .tab-content {
            display: none;
        }

        .tab-content.active {
            display: block;
        }
    </style>
</head>

<body class="bg-gray-100 font-sans">
    <div class="flex flex-row h-screen">
        <!-- Components Panel -->
        <div class="w-64 bg-gray-800 text-white p-4">
            <h2 class="text-xl font-bold mb-6 flex items-center">
                <i class="fas fa-cubes mr-2"></i> Components
            </h2>

            <form method="POST" class="space-y-4">
                <input type="hidden" name="add_block" value="1">

                <div>
                    <label class="block text-gray-300 text-sm font-bold mb-2" for="block_type">
                        Block Type
                    </label>
                    <select class="w-full bg-gray-700 text-white rounded p-2" id="block_type" name="block_type" required>
                        <option value="">Select a block</option>
                        <option value="header">Header</option>
                        <option value="main_content">Main Content</option>
                        <option value="forms">Forms</option>
                        <option value="footer">Footer</option>
                    </select>
                </div>

                <div id="header-fields">
                    <div class="mb-3">
                        <label class="block text-gray-300 text-sm font-bold mb-2" for="title">
                            Title
                        </label>
                        <input class="w-full bg-gray-700 text-white rounded p-2" id="title" type="text" name="title">
                    </div>

                    <div class="mb-3">
                        <label class="block text-gray-300 text-sm font-bold mb-2" for="logo_url">
                            Icon Logo URL
                        </label>
                        <input class="w-full bg-gray-700 text-white rounded p-2" id="logo_url" type="text" name="logo_url" placeholder="https://example.com/logo.png">
                    </div>

                    <div class="mb-3">
                        <label class="block text-gray-300 text-sm font-bold mb-2" for="background_color">
                            Background Color
                        </label>
                        <input class="w-full h-10" id="background_color" type="color" name="background_color" value="#ffffff">
                    </div>

                    <div class="mb-3">
                        <label class="block text-gray-300 text-sm font-bold mb-2" for="font_style">
                            Font Style
                        </label>
                        <select class="w-full bg-gray-700 text-white rounded p-2" id="font_style" name="font_style">
                            <option value="Arial, sans-serif">Arial</option>
                            <option value="'Helvetica Neue', sans-serif">Helvetica</option>
                            <option value="Georgia, serif">Georgia</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="block text-gray-300 text-sm font-bold mb-2" for="font_color">
                            Font Color
                        </label>
                        <input class="w-full h-10" id="font_color" type="color" name="font_color" value="#000000">
                    </div>

                    <div class="mb-3">
                        <label class="block text-gray-300 text-sm font-bold mb-2" for="font_size">
                            Font Size (px)
                        </label>
                        <input class="w-full bg-gray-700 text-white rounded p-2" id="font_size" type="number" name="font_size" min="8" max="72" value="16">
                    </div>

                    <div class="mb-3">
                        <label class="block text-gray-300 text-sm font-bold mb-2" for="font_weight">
                            Font Weight
                        </label>
                        <select class="w-full bg-gray-700 text-white rounded p-2" id="font_weight" name="font_weight">
                            <option value="normal">Normal</option>
                            <option value="bold">Bold</option>
                            <option value="lighter">Light</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="block text-gray-300 text-sm font-bold mb-2" for="menu_items">
                            Menu Items (comma separated)
                        </label>
                        <input class="w-full bg-gray-700 text-white rounded p-2" id="menu_items" type="text" name="menu_items" value="Home,About Us,Contact Us">
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
                <h2 class="text-2xl font-bold">
                    <i class="fas fa-pencil-alt mr-2"></i>
                    <?php
                    $projectName = $project['project_name'] ?? $project['name'] ?? 'Untitled Project';
                    echo htmlspecialchars($projectName);
                    ?>
                </h2>
                <button class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded">
                    <i class="fas fa-save mr-1"></i> Save
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
                        <div class="block-container relative p-4">
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
                                    echo '<div style="background-color:' . $blockData['background_color'] . ';padding:20px;border-radius:4px;">';
                                    if (!empty($blockData['logo_url'])) {
                                        echo '<img src="' . htmlspecialchars($blockData['logo_url']) . '" alt="Logo" style="height:40px;display:inline-block;vertical-align:middle;margin-right:20px;">';
                                    }
                                    echo '<h1 style="display:inline-block;font-family:' . $blockData['font_style'] . ';color:' . $blockData['font_color'] . ';font-size:' . $blockData['font_size'] . 'px;font-weight:' . $blockData['font_weight'] . ';">';
                                    echo htmlspecialchars($blockData['title'] ?? 'Header');
                                    echo '</h1>';

                                    // Render menu items
                                    if (!empty($blockData['menu_items'])) {
                                        $items = explode(',', $blockData['menu_items']);
                                        echo '<nav style="float:right;margin-top:10px;">';
                                        foreach ($items as $item) {
                                            echo '<a href="#" style="font-family:' . $blockData['font_style'] . ';color:' . $blockData['font_color'] . ';font-size:' . ($blockData['font_size'] - 2) . 'px;margin-left:15px;text-decoration:none;">';
                                            echo htmlspecialchars(trim($item));
                                            echo '</a>';
                                        }
                                        echo '</nav>';
                                    }
                                    echo '</div>';
                                    break;

                                default:
                                    echo '<div style="background-color:' . $blockData['background_color'] . ';padding:20px;border-radius:4px;">';
                                    echo '<h3 class="font-bold mb-2">' . ucfirst($blockType) . '</h3>';
                                    echo '<div style="font-family:' . $blockData['font_style'] . ';color:' . $blockData['font_color'] . ';font-size:' . $blockData['font_size'] . 'px;font-weight:' . $blockData['font_weight'] . ';">';
                                    echo nl2br(htmlspecialchars($blockData['text'] ?? ''));
                                    echo '</div>';
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
        document.addEventListener('DOMContentLoaded', function() {
            document.getElementById('block_type').addEventListener('change', function() {
                document.getElementById('header-fields').style.display =
                    this.value === 'header' ? 'block' : 'none';
            });
            if (document.getElementById('block_type').value === '') {
                document.getElementById('header-fields').style.display = 'none';
            }
        });
    </script>
</body>

</html>