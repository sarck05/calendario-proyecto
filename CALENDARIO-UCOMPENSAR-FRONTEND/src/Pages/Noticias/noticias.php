<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include(__DIR__ . '/../../config/conexion.php');

// --- OBTENER TODAS LAS NOTICIAS ---
$sql = "SELECT id, titulo, contenido, imagen, fecha, es_urgente
        FROM noticias
        ORDER BY fecha DESC";
$result = $conn->query($sql);

$news_data = [];
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $news_data[] = [
            'id' => (int)$row['id'],
            'titulo' => htmlspecialchars($row['titulo']),
            'contenido' => htmlspecialchars($row['contenido']),
            'imagen' => $row['imagen']
                ? '../../../public/uploads/' . htmlspecialchars($row['imagen'])
                : "https://picsum.photos/seed/noticia{$row['id']}/600/400.jpg",
            'fecha' => date('d/m/Y H:i', strtotime($row['fecha'])),
            'es_urgente' => (bool)$row['es_urgente']
        ];
    }
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Noticias UCompensar</title>

    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>

    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f5f6fa;
        }

        .news-container {
            max-width: 700px;
            margin: 0 auto;
        }

        .news-card {
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
            margin-bottom: 25px;
            overflow: hidden;
            transition: transform 0.2s ease-in-out;
        }

        .news-card:hover {
            transform: translateY(-3px);
        }

        .news-urgent {
            background-color: #c62828;
            color: white;
            font-weight: bold;
            padding: 6px 10px;
            font-size: 14px;
            text-transform: uppercase;
        }

        .news-header {
            padding: 15px;
        }

        .news-title {
            font-size: 20px;
            font-weight: 600;
            margin: 0 0 8px;
            color: #333;
        }

        .news-date {
            color: #888;
            font-size: 13px;
        }

        .news-image {
            width: 100%;
            height: auto;
            border-top: 1px solid #eee;
            border-bottom: 1px solid #eee;
        }

        .news-content {
            padding: 15px;
            font-size: 15px;
            color: #555;
        }

        .no-news {
            text-align: center;
            margin-top: 50px;
            font-size: 18px;
            color: #888;
        }
    </style>
</head>
<body>

    <?php include('../../Components/menu.php'); ?>

    <div class="container news-container">
        <h2 class="text-center mb-4" style="color:black; padding-top: 20px; padding-bottom: 20px; font-size: 40px"><strong>Últimas Noticias</strong></h2>

        <?php if (empty($news_data)): ?>
            <div class="no-news">
                <p>No hay noticias disponibles en este momento.</p>
            </div>
        <?php else: ?>
            <?php foreach ($news_data as $news): ?>
                <div class="news-card">
                    <?php if ($news['es_urgente']): ?>
                        <div class="news-urgent">URGENTE</div>
                    <?php endif; ?>

                    <div class="news-header">
                        <h3 class="news-title"><?php echo $news['titulo']; ?></h3>
                        <span class="news-date">
                            <i class="glyphicon glyphicon-time"></i> <?php echo $news['fecha']; ?>
                        </span>
                    </div>

                    <img src="<?php echo $news['imagen']; ?>" class="news-image" alt="Imagen de noticia">

                    <div class="news-content">
                        <p><?php echo nl2br($news['contenido']); ?></p>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

</body>
</html>
