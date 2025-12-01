<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Agencia de Viajes Margarita</title>
    <link rel="stylesheet" href="estilos.css">
    <link rel="icon" href="Imagenes/Pagina Logo 2.png">
</head>
<body>

<header>
    <h1>
  <img src="Imagenes/logo-pagina.png" alt="Logo de JJM TRAVEL" width="40" style="vertical-align: middle; margin-right: 8px;">
  JJM TRAVEL
</h1>
    <nav>
        <ul>
            <li><a href="agencia.php">Inicio</a></li> 
            <li><a href="#paquetes">Paquetes</a></li>
            <li><a href="registro.php">Reservar</a></li>
            <li><a href="#contacto">Contacto</a></li>
        </ul>
    </nav>
</header>
 
<section class="hero">
    <h2>Explora Margarita con los mejores paquetes turísticos</h2>
    
</section>

<section class="contenido">
    <h3 style="font-size: 3.5rem" >Nuestros Hoteles Aliados</h3>

    <div class="hoteles">

        <div class="card">
            <img src="Imagenes/puntablanca.jpg" alt="">
            <h4>Sunsol Punta Blanca</h4>
            <p>Ubicado en la paradisíaca isla de Coche. Todo incluido, playa privada, piscinas y actividades.</p>
            <a href="hotel.php?hotel=puntablanca" class="btn">Ver información</a>
        </div>

        <div class="card">
            <img src="Imagenes/sunsol-ecoland-beach.jpg" alt="">
            <h4>Sunsol Ecoland</h4>
            <p>Hotel ecológico rodeado de naturaleza, ideal para familias y parejas. Servicio todo incluido.</p>
            <a href="hotel.php?hotel=ecoland" class="btn">Ver información</a>
        </div>

        <div class="card">
            <img src="Imagenes/Hesperia.jpg" alt="">
            <h4>Hotel Hesperia</h4>
            <p>Resort cinco estrellas junto al mar. Piscinas, restaurantes, shows y excelente atención.</p>
            <a href="hotel.php?hotel=hesperia" class="btn">Ver información</a>
        </div>

        <div class="card">
            <img src="Imagenes/lidotel-agua-dorada-beach-htl-la-mira-pic-21.jpg" alt="">
            <h4>Hotel Agua Dorada</h4>
            <p>Hotel moderno frente a Playa Parguito. Habitaciones premium y gastronomía de primera.</p>
            <a href="hotel.php?hotel=aguadorada" class="btn">Ver información</a>
        </div>

    </div>
</section>

<section id="paquetes" class="contenido paquetes-section">
    <h3>Paquetes Destacados</h3>

    <div class="paquetes-grid">
        
        <div class="card paquete-card">
            <img src="Imagenes/Promo Aventura en la isla1.jpg" alt="Aventura">
            <h4>Aventura en la Isla</h4>
            <p>3 Días / 2 Noches. Incluye alojamiento en Sunsol Ecoland, excursión a La Restinga y tour de snorkel en Cubagua. Traslados incluidos.</p>
            <p class="precio">Desde $199 p/p</p>
            
        </div>

        <div class="card paquete-card">
            <img  src="Imagenes/Promo Viaje - Lujo.jpg" alt="Relax">
            <h4>Relax de Lujo</h4>
            <p>4 Días / 3 Noches. Alojamiento en Hotel Hesperia, masaje en la playa y cena romántica. Ideal para parejas.</p>
            <p class="precio">Desde $350 p/p</p>
            
        </div>

        <div class="card paquete-card">
            <img src="Imagenes/paquete-familia.jpg" alt="Familiar">
            <h4>Diversión Familiar</h4>
            <p>5 Días / 4 Noches. Alojamiento en Sunsol Punta Blanca (Isla de Coche), con acceso a club de niños y parques acuáticos cercanos.</p>
            <p class="precio">Desde $290 p/p</p>
            
        </div>

    </div>
</section>

<section id="contacto" class="contenido contacto-section">
    <h3>Contáctanos</h3>
    <div class="contacto-info">
        <p>📍 Ubicación Principal Av. 4 de Mayo, Edif. JJM, Porlamar, Edo. Nueva Esparta.</p>
        <p>📞 Teléfono: +58 295 123 4567</p>
        <p>📧 Correo Electrónico: <a href="mailto:reservas@jjmtravel.com">jjmtravel.destinos@gmail.com</a></p>
        <p>⏰ Horario de Oficina: Lunes a Viernes (9:00am - 5:00pm)</p>
        <p></p>
    </div>
</section>

<footer>
    <p>© 2025 Agencia de Viajes Margarita - Todos los derechos reservados</p>
</footer>

</body>
</html>
