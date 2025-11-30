<?php
//======================================================================
// INFORMACIÓN DE LOS HOTELES (ARRAY COMPLETO CON GALERÍA)
//======================================================================

$hoteles = [

    "puntablanca" => [
        "nombre" => "SUNSOL PUNTA BLANCA", // Usamos mayúsculas para un mejor impacto visual
        "imagen" => "Imagenes/puntablanca.jpg", 
        "descripcion" => "El hotel ofrece exuberantes jardines exóticos con un toque caribeño de elegancia. Las salas de baño al aire libre brindan un auténtico placer, ya sea bajo el cálido sol o la brillante luz de las estrellas. Es el destino perfecto para familias, parejas en busca de disfrute y amantes del Windsurf y Kitesurf.",
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
            "Snack Bar en una mágica churuata frente al mar",
            "Parque infantil",
            "Minimarket (costo adicional)",
            "Salón para Eventos y Convenciones",
            "Servicio de Masaje (costo adicional)",
            "Servicio de toallas y tumbonas en playa y piscina",
            "Servicio de Wifi gratuito en áreas comunes"
        ]
    ],

    "ecoland" => [
        "nombre" => "SUNSOL ECOLAND",
        "imagen" => "Imagenes/sunsol-ecoland-beach.jpg",
        "descripcion" => "Un complejo hotelero de categoría 4 estrellas con servicio todo incluido. Ofrece una fusión única de 4 ecosistemas: Laguna, Mar, Duna de Arena y Montaña. Situado en una impresionante bahía de aguas azules y arenas blancas que se extiende a lo largo de un kilómetro.",
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
            "2 Piscina para niños",
            "Laguna y manglares con paseo en bote a remos",
            "Boulevard con ciclovía frente al mar",
            "Zona de duna recreativa",
            "Paseos en Kayak",
            "Canchas de Mini-futbol y futbol 5",
            "Cancha de Basket 3x3",
            "Senderismo de montaña hasta el faro",
            "Golfito con 09 hoyos",
            "4 Restaurantes buffet y snack",
            "Bares de playa y piscina",
            "Parque infantil y club de niños",
            "Bodegón",
            "GYM",
            "Game Room",
            "Sala de Cine",
            "Cancha de usos múltiples",
            "Salones para conferencias",
            "Servicio de toallas y tumbonas en playa y piscina",
            "Servicio de Wifi gratuito en áreas comunes y habitaciones (área Laguna)",
            "Servicio de Taxi"
        ]
    ],

    "hesperia" => [
        "nombre" => "HOTEL HESPERIA",
        "imagen" => "Imagenes/Hesperia.jpg",
        "descripcion" => "Hotel todo incluido para familias. Aprovecha la calidad de servicios, como desayuno buffet incluido, campo de golf y bar en la playa. Es el lugar ideal para relajarte bajo el sol, ya que cuenta con ubicación frente a la playa, camas de playa gratuitas y masajes en la playa.",
        "galeria" => [
            "Img-Hesperia/46921802.jpg",
            "Img-Hesperia/46921827.jpg",
            "Img-Hesperia/107852563.jpg",
            "Img-Hesperia/301495231.jpg",
             "Img-Hesperia/668966720.jpg"
        ],
        "servicios" => [
            "Desayuno buffet incluido",
            "Campo de golf",
            "Bar en la playa",
            "Ubicación frente a la playa, camas de playa gratuitas",
            "Masajes en la playa (costo adicional)",
            "Aromaterapia, masajes y hidroterapia en el spa (costo adicional)",
            "3 Restaurantes",
            "Yoga en la playa y vóleibol de playa",
            "Wifi gratis en las habitaciones (25+ Mbps)",
            "Bar junto a la alberca",
            "Supermercado o tienda de conveniencia",
            "Alberca al aire libre y chapoteadero con camas balinesas gratuitas",
            "Estacionamiento gratis",
            "Club de playa con acceso gratuito",
            "Salón de baile, servicio de concierge y personal multilingüe"
        ]
    ],

    "aguadorada" => [
        "nombre" => "HOTEL AGUA DORADA",
        "imagen" => "Imagenes/lidotel-agua-dorada-beach-htl-la-mira-pic-21.jpg",
        "descripcion" => "Agua Dorada Beach Hotel en el corazón de Playa el Agua en la Isla de Margarita, con una privilegiada ubicación frente al mar. Resulta el lugar ideal para viajeros que desean disfrutar de unas vacaciones relajantes, resaltando toda la belleza natural de su entorno.",
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
            "Benji´s bar",
            "Kids club",
            "Salón de eventos"
        ]
    ]
];

//=============================
// OBTENER EL HOTEL SELECCIONADO
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
</head>
<body>

<header>
    <h1>JJM TRAVEL</h1>
    <nav>
        <ul>
            <li><a href="agencia.php">Inicio</a></li>
            <!-- EL BOTÓN DE RESERVA AQUÍ DEBE SER GENÉRICO SI NO HAY HOTEL SELECCIONADO, PERO LO DEJAMOS ASÍ POR AHORA -->
            <li><a href="registro.php">Reservar</a></li> 
            <li></li>
        </ul>
    </nav>
</header>

<section class="contenido">

    <h2 class="nombre-hotel-grande"><?php echo $hotel["nombre"]; ?></h2>

    <div class="slider-container">
        <!-- Contenido del Slider y la Galería -->
        <div class="image-slider">
            <?php foreach ($hotel["galeria"] as $img_src): ?>
                <img src="<?php echo $img_src; ?>" alt="Galería de <?php echo $hotel['nombre']; ?>">
            <?php endforeach; ?>
        </div>
         <div class="slider-nav">
             <!-- Los puntos de navegación del slider (no están implementados) -->
         </div>
    </div>

    <h3 style= "font-size: 3.5rem;">Descripción</h3>
    <p><?php echo $hotel["descripcion"]; ?></p>

    <h3 style= "font-size: 3.5rem;">Servicios incluidos</h3>
    <ul class="servicios">
        <?php foreach ($hotel["servicios"] as $servicio): ?>
            <li><?php echo $servicio; ?></li>
        <?php endforeach; ?>
    </ul>

    <!-- CORRECCIÓN CLAVE: El botón "Reservar ahora" debe pasar la clave del hotel a registro.php -->
    <a href="registro.php?hotel=<?php echo $seleccion; ?>" class="btn">Reservar ahora</a>

</section>

<footer>
    <p>© 2025 Agencia de Viajes Margarita</p>
</footer>

</body>
</html>