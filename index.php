<?php
// Função para listar os arquivos PDF dentro de uma pasta e suas subpastas
function getFilesInFolder($folderPath) {
    $files = array_diff(scandir($folderPath), array('.', '..')); // Remove . e ..
    $allowed_extensions = ['pdf'];
    $pdfFiles = array_filter($files, function($file) use ($allowed_extensions) {
        return in_array(strtolower(pathinfo($file, PATHINFO_EXTENSION)), $allowed_extensions);
    });

    // Listando as subpastas
    $subfolders = array_filter($files, function($file) use ($folderPath) {
        return is_dir($folderPath . DIRECTORY_SEPARATOR . $file);
    });
    // Ordenando as subpastas numericamente
    sort($subfolders, SORT_NUMERIC); // Garante que as pastas sejam ordenadas numericamente

    return ['pdfFiles' => $pdfFiles, 'subfolders' => $subfolders];
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Documentos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="css/estilos.css">
    <link rel="icon" type="image/x-icon" href="#">
</head>
<body>

   <!-- Navbar -->
 <nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow">
        <div class="container-fluid">
            <a class="navbar-brand fw-bold" href="#"><i class="fas fa-user-tie"></i> Documentos</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li><a href="#"><i class="fas fa-home"></i> Início</a></li>
                    <li><a href="#"><i class="fas fa-book"></i> Documentos(Atuais)</a></li>

                </ul>
            </div>
        </div>
    </nav>

    <div class="container">
        <h1>Documentos</h1>
        <ul class="year-list">
            <?php 
            // Criando a lista de anos de 2017 até 2024
            for ($year = 2017; $year <= 2024; $year++) {
                echo "<li class='year-item' onclick='window.location.href=\"?year=$year\"'>
                        <span class='year-name'>$year</span>
                      </li>";
            }
            ?>
        </ul>
    </div>

    <?php
    // Se o ano for selecionado, exibe os arquivos da pasta correspondente
    if (isset($_GET['year'])) {
        $selectedYear = $_GET['year'];
        $folderPath = "boletim/$selectedYear/";

        // Verifica se a pasta existe
        if (is_dir($folderPath)) {
            // Obtém os arquivos PDF e as subpastas
            $items = getFilesInFolder($folderPath);
            $pdfFiles = $items['pdfFiles'];
            $subfolders = $items['subfolders'];

            echo "<div class='container'>
                    <h2>Arquivos de $selectedYear</h2>";
            
            // Exibe as subpastas
            echo "<div class='folder-list'>"; 
            foreach ($subfolders as $folder) {
                echo "<div class='folder-item'>
                        <i class='fas fa-folder folder-icon'></i>
                        <span class='file-name'>$folder</span>
                        <button onclick='toggleFolder(\"$folder\")' class='view-btn'>Abrir Pasta</button>
                        <div class='folder-content' id='folder-$folder'>";

                // Exibe os arquivos PDF dentro da subpasta
                $subfolderPath = $folderPath . $folder . '/';
                $subItems = getFilesInFolder($subfolderPath);

                // Inicia a tabela para exibir os arquivos PDF
                echo "<table class='file-table'>
                        <thead>
                            <tr>
                                <th>Ícone</th>
                                <th>Nome do Arquivo</th>
                                <th>Ação</th>
                            </tr>
                        </thead>
                        <tbody>" ;

                // Exibe cada arquivo PDF como uma linha na tabela
                foreach ($subItems['pdfFiles'] as $file) {
                    echo "<tr>
                            <td><i class='fas fa-file-pdf pdf-icon'></i></td>
                            <td class='file-name'>$file</td>
                            <td><a href='$subfolderPath$file' class='view-btn' target='_blank'>Visualizar</a></td>
                          </tr>";
                }

                echo "</tbody></table>"; // Fecha a tabela

                echo "<button onclick='closeFolder()' class='close-btn'>Fechar</button>";
                echo "</div></div>";
            }
            echo "</div>"; // Fechar a div folder-list
            echo "</div>"; // Fechar a div container
        } else {
            echo "<div class='container'>
                    <h2>Não há arquivos para o ano $selectedYear.</h2>
                  </div>";
        }
    }
    ?>

    <script>
        // Função para alternar a visibilidade das pastas
        function toggleFolder(folderName) {
            var folderContent = document.getElementById('folder-' + folderName);
            folderContent.classList.toggle('active');
        }

        // Função para fechar a visualização dos arquivos e voltar às subpastas
        function closeFolder() {
            var folderContents = document.querySelectorAll('.folder-content');
            folderContents.forEach(function(folder) {
                folder.classList.remove('active');
            });
        }
    </script>

</body>
</html>
