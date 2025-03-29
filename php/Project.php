<?php
session_start();
include '/Buildify/config.php';

// Get project ID from URL
$projectId = isset($_GET['id']) ? intval($_GET['id']) : null;

// Fetch project details
$project = [];
if ($projectId) {
    $stmt = $pdo->prepare("SELECT * FROM projects WHERE id = ?");
    $stmt->execute([$projectId]);
    $project = $stmt->fetch(PDO::FETCH_ASSOC);
}

// Handle block configuration submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get block data
    $blockType = $_POST['block_type'];
    $blockData = json_encode([
        'background_color' => $_POST['background_color'],
        'font_style' => $_POST['font_style'],
        'font_color' => $_POST['font_color'],
        'font_size' => $_POST['font_size'],
        'font_weight' => $_POST['font_weight'],
        'text' => $_POST['text'],
    ]);

    // Insert or update block
    $stmt = $pdo->prepare("INSERT INTO project_blocks (project_id, block_type, data) VALUES (?, ?, ?)");
    $stmt->execute([$projectId, $blockType, $blockData]);

    // Redirect back to editor
    header("Location: edit_project.php?id=$projectId");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Project</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>

<body class="bg-gray-100 font-sans leading-normal tracking-normal">
    <div class="flex flex-row h-screen">
        <!-- Sidebar -->
        <div class="w-64 bg-gray-200 p-4">
            <h2 class="text-lg font-bold mb-4">Components</h2>
            <form action="" method="POST">
                <!-- Header -->
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="block_type">
                        Header
                    </label>
                    <select class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="block_type" name="block_type">
                        <option value="header">Header</option>
                        <option value="main_content">Main Content</option>
                        <option value="sidebar">Sidebar</option>
                        <option value="forms">Forms</option>
                        <option value="footer">Footer</option>
                    </select>
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="background_color">
                        Background Color
                    </label>
                    <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="background_color" type="color" name="background_color" required>
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="font_style">
                        Font Style
                    </label>
                    <select class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="font_style" name="font_style">
                        <option value="Arial">Arial</option>
                        <option value="Sans-serif">Sans-serif</option>
                        <option value="Serif">Serif</option>
                    </select>
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="font_color">
                        Font Color
                    </label>
                    <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="font_color" type="color" name="font_color" required>
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="font_size">
                        Font Size
                    </label>
                    <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="font_size" type="number" name="font_size" min="8" max="72" value="16" required>
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="font_weight">
                        Font Weight
                    </label>
                    <select class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="font_weight" name="font_weight">
                        <option value="normal">Normal</option>
                        <option value="bold">Bold</option>
                        <option value="light">Light</option>
                    </select>
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="text">
                        Text
                    </label>
                    <textarea class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="text" name="text" rows="4"></textarea>
                </div>
                <button class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline" type="submit">
                    Add Block
                </button>
            </form>
        </div>

        <!-- Editor Area -->
        <div class="flex-1 bg-gray-100 p-4">
            <h2 class="text-lg font-bold mb-4">Project: <?php echo htmlspecialchars($project['name']); ?></h2>
            <div class="bg-white p-4 rounded shadow">
                <!-- Display blocks here -->
                <?php
                if ($projectId) {
                    $stmt = $pdo->prepare("SELECT * FROM project_blocks WHERE project_id = ?");
                    $stmt->execute([$projectId]);
                    $blocks = $stmt->fetchAll(PDO::FETCH_ASSOC);

                    foreach ($blocks as $block) {
                        $blockData = json_decode($block['data'], true);
                ?>
                        <div class="mb-4">
                            <h3 class="text-lg font-bold"><?php echo ucfirst($block['block_type']); ?></h3>
                            <div style="
                                background-color: <?php echo $blockData['background_color']; ?>;
                                font-family: <?php echo $blockData['font_style']; ?>;
                                color: <?php echo $blockData['font_color']; ?>;
                                font-size: <?php echo $blockData['font_size']; ?>px;
                                font-weight: <?php echo $blockData['font_weight']; ?>;
                            ">
                                <?php echo htmlspecialchars($blockData['text']); ?>
                            </div>
                        </div>
                <?php
                    }
                }
                ?>
            </div>
        </div>
    </div>
</body>

</html>