<?php
//======================================================================
// INFORMACIÓN DE LOS HOTELES (ARRAY COMPLETO CON GALERÍA)
//======================================================================

$hoteles = [
    "puntablanca" => [
        "nombre" => "SUNSOL PUNTA BLANCA",
        "imagen" => "Imagenes/puntablanca.jpg", 
        "descripcion" => "SUNSOL Punta Blanca es un refugio paradisíaco ubicado en la tranquila y hermosa Isla de Coche, conocido por su ambiente relajado, sus extensas playas de arena blanca y su característico mar color turquesa. Este hotel se destaca por ofrecer una experiencia tropical auténtica, ideal para quienes buscan desconectarse del ruido y sumergirse en un entorno natural único.",
        "galeria" => [
            "Img-punta blanca/sunsol-punta-blanca.jpg",
            "Img-punta blanca/643900249.jpg",
            "Img-punta blanca/pool-by-night.jpg",
            "Img-punta blanca/premium-room.jpg"
        ],
        "servicios" => [
            "Transporte marítimo El Yaque / Isla de Coche",
            "Piscina con borde infinito, cascada y jacuzzi",
            "2 Restaurantes",
            "2 Bares",
            "Club de playa",
            "Snack Bar frente al mar",
            "Parque infantil",
            "Minimarket",
            "Salón de Eventos",
            "Masajes (costo adicional)",
            "Toallas y tumbonas",
            "Wifi gratuito"
        ]
    ],

    "ecoland" => [
        "nombre" => "SUNSOL ECOLAND",
        "imagen" => "Imagenes/sunsol-ecoland-beach.jpg",
        "descripcion" => "SUNSOL Ecoland es un encantador hotel rodeado de naturaleza, diseñado para ofrecer una experiencia tranquila y armoniosa en el corazón de Margarita. Con un ambiente familiar y relajado, este alojamiento se distingue por sus amplias áreas verdes, su arquitectura caribeña y su atmósfera serena que invita a desconectarse y disfrutar del entorno natural.",
        "galeria" => [
            "Img-Ecoland/cdd629d3.avif",
            "Img-Ecoland/sunsol-ecoland-beach (1).jpg",
            "Img-Ecoland/sunsol-ecoland-beach (2).jpg",
            "Img-Ecoland/sunsol-ecoland-beach.jpg",
            "Img-Ecoland/habitacion-premium-area.jpg"
        ],
        "servicios" => [
            "Majestuosa playa",
            "4 Piscinas familiares",
            "Piscinas para niños",
            "Laguna con botes",
            "Ciclovía frente al mar",
            "Zona de duna recreativa",
            "Kayak",
            "Canchas deportivas",
            "Senderismo",
            "Golfito",
            "4 Restaurantes",
            "Bares",
            "Club de niños",
            "Gym",
            "Cine",
            "Sala de juegos"
        ]
    ],

    "hesperia" => [
        "nombre" => "HOTEL HESPERIA",
        "imagen" => "Imagenes/Hesperia.jpg",
        "descripcion" => "El Hotel Hesperia Isla Margarita es uno de los complejos más emblemáticos de la isla, reconocido por su amplia infraestructura, su ambiente elegante y su ubicación privilegiada rodeada de paisajes naturales. Situado a pocos minutos de SUNSOL Ecoland, este hotel ofrece un entorno tranquilo y espacioso, perfecto para quienes desean relajarse y disfrutar del ambiente caribeño en un espacio de gran amplitud.",
        "galeria" => [
            "Img-Hesperia/46921802.jpg",
            "Img-Hesperia/46921827.jpg",
            "Img-Hesperia/107852563.jpg",
            "Img-Hesperia/301495231.jpg",
            "Img-Hesperia/668966720.jpg"
        ],
        "servicios" => [
            "Desayuno buffet",
            "Campo de golf",
            "Bar en la playa",
            "Masajes en la playa",
            "Spa (costo adicional)",
            "Restaurantes",
            "Volleyball de playa",
            "Wifi rápido",
            "Supermercado",
            "Piscina con camas Bali",
            "Estacionamiento gratuito"
        ]
    ],

    "aguadorada" => [
        "nombre" => "HOTEL AGUA DORADA",
        "imagen" => "Imagenes/lidotel-agua-dorada-beach-htl-la-mira-pic-21.jpg",
        "descripcion" => "Ubicado en una de las zonas más exclusivas de la isla, el Hotel Agua Dorada ofrece una experiencia moderna, relajante y con un estilo boutique que combina elegancia y comodidad. Situado frente a las aguas cristalinas de Playa El Agua, este hotel destaca por su ambiente tranquilo, su diseño contemporáneo y su servicio orientado al detalle, ideal para viajeros que buscan descanso, confort y una ubicación privilegiada al borde del mar.",
        "galeria" => [
            "Img-Agua dorada/agua-dorada-isla-de-margarita (1).jpg",
            "Img-Agua dorada/agua-dorada-isla-de-margarita (2).jpg",
            "Img-Agua dorada/agua-dorada-isla-de-margarita (3).jpg",
            "Img-Agua dorada/agua-dorada-isla-de-margarita (4).jpg",
            "Img-Agua dorada/agua-dorada-isla-de-margarita.jpg"
        ],
        "servicios" => [
            "Wifi",
            "Club de playa",
            "Piscina",
            "Restaurante",
            "Benji’s bar",
            "Kids club",
            "Salón de eventos"
        ]
    ]
];

//=============================
// VALIDAR HOTEL
//=============================

$seleccion = $_GET["hotel"] ?? "";

if (!isset($hoteles[$seleccion])) {
    die("Error: hotel no encontrado.");
}

$hotel = $hoteles[$seleccion];
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title><?php echo $hotel["nombre"]; ?></title>
    <link rel="stylesheet" href="estilos.css">
    <link rel="icon" href="Imagenes/Pagina Logo 2.png">
</head>
<body>

<header>
    <h1>JJM TRAVEL</h1>
    <nav>
        <ul>
            <li><a href="agencia.php">Inicio</a></li>
            <!-- FIX IMPORTANTE -->
            <li><a href="registro.php?hotel=<?php echo $seleccion; ?>">Reservar</a></li>
        </ul>
    </nav>
</header>

<section class="contenido">

    <h2 class="nombre-hotel-grande"><?php echo $hotel["nombre"]; ?></h2>

    <div class="slider-container">
        <div class="image-slider">
            <?php foreach ($hotel["galeria"] as $img_src): ?>
                <img src="<?php echo $img_src; ?>" alt="Galería de <?php echo $hotel['nombre']; ?>">
            <?php endforeach; ?>
        </div>
        <div class="slider-nav"></div>
    </div>

    <h3 style="font-size: 3.5rem;">Descripción</h3>
    <p><?php echo $hotel["descripcion"]; ?></p>

    <h3 style="font-size: 3.5rem;">Servicios incluidos</h3>
    <ul class="servicios">
        <?php foreach ($hotel["servicios"] as $servicio): ?>
            <li><?php echo $servicio; ?></li>
        <?php endforeach; ?>
    </ul>

    <!-- FIX IMPORTANTE -->
    <a href="registro.php?hotel=<?php echo $seleccion; ?>" class="btn">Reservar ahora</a>

</section>

<footer>
    <p>© 2025 Agencia de Viajes Margarita</p>
</footer>

</body>
</html>
