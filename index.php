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
    <link rel="icon" type="image/x-icon" href="#">
    <style>
        /* Estilos para boa exibição */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Arial', sans-serif;
            background-color: #f4f7fc;
            color: #333;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding-top: 0px; /* Espaço acima do topo para o menu de navegação */
        }

        /* Menu fixo no topo */
        nav {
            background-color: #343a40;
            width: 100%;
            padding: 15px 0;
            position: fixed;
            top: 0;
            left: 0;
            z-index: 1000;
        }
        nav ul {
            display: flex;
            justify-content: center;
            list-style: none;
            padding: 0;
        }
        nav ul li {
            margin: 0 20px;
        }
        nav ul li a {
            color: white;
            text-decoration: none;
            font-size: 18px;
            font-weight: bold;
        }
        nav ul li a:hover {
            text-decoration: underline;
        }

        .container {
            width: 90%;
            max-width: 1200px;
            padding: 40px;
            text-align: center;
        }

        h1 {
            color: #007BFF;
            font-size: 36px;
            margin-bottom: 30px;
        }

        .year-list {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
            gap: 20px;
            list-style: none;
            padding: 0;
        }

        .year-item {
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            padding: 20px;
            cursor: pointer;
            transition: transform 0.3s ease, background-color 0.3s ease;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 120px;
        }

        .year-item:hover {
            transform: translateY(-5px);
            background-color: #e7f1ff;
        }

        .year-name {
            font-size: 24px;
            font-weight: bold;
            color: #007BFF;
        }

        .folder-list {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr)); /* Aumentando a largura das subpastas */
            gap: 40px; /* Aumentando o espaço entre as subpastas */
            padding: 20px;
            list-style: none;
            justify-content: center;
        }

        .folder-item {
            min-width: 395px;      /* Largura do Box das subpastas */
            height: 200px;
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            text-align: center;
            display: flex;
            flex-direction: column;
            align-items: center;
            transition: transform 0.3s ease, background-color 0.3s ease;
            justify-content: space-between; /* Espaça os elementos dentro da subpasta */
        }

        .folder-item:hover {
            transform: translateY(-5px);
            background-color: #e7f1ff;
        }

        .folder-icon {
            font-size: 40px;
            color: #28a745;
        }

        .file-table {
            width: 100%;
            margin-top: 20px;
            border-collapse: collapse;
        }

        .file-table th,
        .file-table td {
            border: 1px solid #ddd;
            padding: 12px;
            text-align: center;
        }

        .file-table th {
            background-color: #007BFF;
            color: white;
            font-weight: bold;
        }

        .pdf-icon {
            font-size: 24px;
            color: #dc3545;
        }

        .file-name {
            font-size: 16px;
            font-weight: bold;
            color: #333;
            word-wrap: break-word;
        }

        .folder-content {
            display: none;
            flex-wrap: wrap;
            gap: 20px;
            justify-content: center;
            margin-top: 20px;
            background-color: #fff; /* Fundo branco para maior destaque */
            padding: 20px;
            border-radius: 8px; /* Bordas arredondadas */
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        .folder-content.active {
            display: flex;
        }

        .footer {
            position: relative;
            bottom: 0;
            width: 100%;
            text-align: center;
            font-size: 14px;
            color: #888;
            padding: 20px 0;
        }

        .view-btn {
            background-color: #007BFF;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            margin-top: 10px;
            text-decoration: none;
        }

        .close-btn {
            background-color: #dc3545;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            margin-top: 10px;
        }

        @media (max-width: 768px) {
            .year-item {
                height: 100px;
            }

            .file-item, .folder-item {
                width: 150px;
            }

            .file-table td,
            .file-table th {
                font-size: 12px;
            }
        }
    </style>
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
