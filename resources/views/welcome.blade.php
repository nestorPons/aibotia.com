<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AIBOT - Chatbot para WhatsApp</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="icon" href="favicon.ico" type="image/x-icon" />

    <link rel="stylesheet" href="css/main.css">

</head>

<body class="bg-gray-100">

    <!-- Hero Section -->
    <header class="bg-transparent text-white text-center py-20">
        <img src="img/logo.webp" alt="AIBOT Logo" class="mx-auto w-32 mb-4">
        <h1 class="text-4xl font-bold">AIBOT - Automatiza tu atención al cliente en WhatsApp</h1>
        <p class="mt-2 text-lg">Responde preguntas, agenda citas y mucho más.</p>
        <a href="https://chat.whatsapp.com/EeZlbPsT7KWBjo8sWRQpBe" class="mt-4 inline-block bg-white text-gray-500 font-semibold px-6 py-3 rounded-full shadow-md">¡Probar Ahora!</a>
    </header>

    <!-- ¿Qué es AIBOT? -->
    <section id="aibot" class="bg-gray-100 py-16 px-6 h-50">
        <div class="max-w-6xl mx-auto text-center">
            <h2 class="flex items-center justify-center gap-3 text-4xl font-bold text-gray-800 mb-4"><x-aibot-icon name="robot" class="w-10 h-10 text-green-600" /> ¿Qué es <span class="text-green-600">AIBOT</span>?</h2>
            <p class="text-gray-600 text-lg mb-8 max-w-3xl mx-auto">
                AIBOT es un chatbot inteligente diseñado para empresas que reciben muchas consultas repetitivas por
                <strong>WhatsApp</strong>. Responde automáticamente preguntas sobre ubicación, servicios, horarios y más.
                Además, puede <span class="text-green-500 font-semibold">gestionar citas, enviar recordatorios</span> y
                derivar consultas a un operador humano.
            </p>

            <div class="grid md:grid-cols-3 gap-6">
                <div class="bg-white shadow-md rounded-lg p-6">
                    <h3 class="flex items-center gap-2 text-xl font-semibold text-gray-800 mb-3"><x-aibot-icon name="chat" class="w-6 h-6 text-green-600" /> Responde Consultas</h3>
                    <p class="text-gray-600">Automatiza respuestas sobre ubicación, horarios, servicios y más.</p>
                </div>

                <div class="bg-white shadow-md rounded-lg p-6">
                    <h3 class="flex items-center gap-2 text-xl font-semibold text-gray-800 mb-3"><x-aibot-icon name="calendar" class="w-6 h-6 text-green-600" /> Agenda Citas</h3>
                    <p class="text-gray-600">Permite a los clientes reservar citas sin necesidad de un operador.</p>
                </div>

                <div class="bg-white shadow-md rounded-lg p-6">
                    <h3 class="flex items-center gap-2 text-xl font-semibold text-gray-800 mb-3"><x-aibot-icon name="bell" class="w-6 h-6 text-green-600" /> Envía Recordatorios</h3>
                    <p class="text-gray-600">Reduce ausencias con alertas automáticas a los clientes.</p>
                </div>
            </div>

            <a href="#contacto" class="mt-8 inline-flex items-center justify-center gap-2 bg-green-600 text-white py-3 px-6 rounded-lg text-lg font-semibold hover:bg-green-700 transition">
                <x-aibot-icon name="rocket" class="w-5 h-5" /> Integrar AIBOT en mi empresa
            </a>
        </div>
    </section>

    <!-- Planes y Precios -->
    <section class="bg-transparent flex items-center py-2">
        <div class="max-w-6xl mx-auto py-12 px-6">
            <h2 class="text-3xl font-bold text-center text-white mb-2">Plan de Servicio Único</h2>
            <div class="bg-white shadow-lg rounded-lg p-6 text-center">
                <h3 class="text-2xl font-semibold text-green-600">Instalación Personalizada del Sistema Básica</h3>
                <p class="mt-2 text-gray-700">Incluye la configuración del servidor AWS EC2 o local, configuración del asistente OPENAI, carga de datos.</p>
                <p class="text-2xl font-bold mt-4">Desde 90€</p>
                <p class="mt-2 text-gray-600">*El precio puede variar según la complejidad de la personalización requerida.</p>
            </div>
            <h2 class="text-3xl font-bold text-center text-white mb-2">Plan de Servicio Único</h2>
            <div class="bg-white shadow-lg rounded-lg p-6 text-center mt-12">
                <h3 class="mt-2 text-gray-700">Cuota mensual</h3>
                <p class="text-2xl font-bold mt-4">50€</p>
                <p class="text-2xl font-bold mt-4">+ Gastos por uso API OpenAI</p>
                <a href="https://openai.com/es-ES/api/pricing/" class="mt-4">https://openai.com/es-ES/api/pricing/</a>
                <p class="mt-2 text-gray-600">*Precios sin iva incluido</p>
            </div>
            <p class="text-center mt-6 text-white">Personalización adicional: 25€/hora</p>
        </div>
    </section>

    <!-- Ventajas del Plan de Tarifa Única -->
    <section class="bg-white flex items-center">
        <div class="max-w-6xl mx-auto py-12 px-6">
            <h2 class="text-3xl font-bold text-center text-gray-800 mb-10">Ventajas del Plan de Tarifa Única</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                <div class="bg-white shadow-lg rounded-lg p-6">
                    <h3 class="text-xl font-semibold text-gray-800 mb-2">Titularidad</h3>
                    <p class="text-gray-600">El sistema es <b>tuyo</b>, proporcionando total control y propiedad.</p>
                </div>
                <div class="bg-white shadow-lg rounded-lg p-6">
                    <h3 class="text-xl font-semibold text-gray-800 mb-2">Adaptación</h3>
                    <p class="text-gray-600">El sistema se adapta a tu negocio, no tu negocio al sistema.</p>
                </div>
                <div class="bg-white shadow-lg rounded-lg p-6">
                    <h3 class="text-xl font-semibold text-gray-800 mb-2">Control Total</h3>
                    <p class="text-gray-600">Gestiona tu sistema de manera autónoma sin depender de servicios externos para el procesamiento de datos.</p>
                </div>
                <div class="bg-white shadow-lg rounded-lg p-6">
                    <h3 class="text-xl font-semibold text-gray-800 mb-2">Personalización Profunda</h3>
                    <p class="text-gray-600">Adapta el sistema para incluir <b>tus bases de datos</b>, modelos de <b>IA en local</b>, y la integración con otras aplicaciones empresariales existentes.</p>
                </div>
                <div class="bg-white shadow-lg rounded-lg p-6">
                    <h3 class="text-xl font-semibold text-gray-800 mb-2">Privacidad y Seguridad Mejoradas</h3>
                    <p class="text-gray-600">Los datos se mantienen en tu infraestructura, sin cederlos a terceros.</p>
                </div>
                <div class="bg-white shadow-lg rounded-lg p-6">
                    <h3 class="text-xl font-semibold text-gray-800 mb-2">Costo Efectivo</h3>
                    <p class="text-gray-600">Un pago único sin tarifas mensuales recurrentes, ideal para controlar mejor los gastos a largo plazo.</p>
                </div>
            </div>
        </div>
    </section>


    <!-- Resumen del Sistema AIBOT para WhatsApp -->
    <section class="bg-transparent">
        <div class="max-w-6xl mx-auto py-12 px-6">

            <h2 class="text-3xl font-bold text-center text-white mb-4">Resumen del Sistema AIBOT para WhatsApp</h2>
            <p class="text-center text-white mb-6">AIBOT es una solución revolucionaria diseñada para mejorar la comunicación entre empresas y clientes mediante WhatsApp. Este sistema de chatbot utiliza la tecnología de inteligencia artificial de OpenAI para responder automáticamente a consultas comunes, gestionar citas y enviar recordatorios, todo integrado directamente en WhatsApp.</p>

            <div class="text-center">
                <h3 class="text-xl font-semibold text-white mb-3">Características Principales</h3>
                <ul class="list-disc text-white mx-auto text-left max-w-2xl">
                    <li><strong>Integración Perfecta:</strong> Se adapta a la infraestructura existente, sin necesidad de ajustes complejos.</li>
                    <li><strong>Control Total y Propiedad:</strong> El sistema, desarrollado completamente en código propio, asegura que toda la instalación y los datos permanezcan bajo tu control.</li>
                    <li><strong>Flexibilidad:</strong> A medida que tu empresa crece, el sistema es escalable y personalizable para atender nuevas necesidades.</li>
                </ul>
            </div>

        </div>
    </section>
    <!-- Contacto -->
    <section id="contacto" class="bg-white py-12 px-6 ">
        <div class="max-w-6xl mx-auto py-12 px-6">
            <h2 class="text-2xl font-bold text-center text-gray-800 mb-4">Contacto</h2>
            <p class="text-center text-gray-600 mb-6">Envíanos un mensaje y te responderemos lo antes posible.</p>

            <form action="../../contact.php" method="POST" class="space-y-4">
                <input type="text" name="telefono" style="display:none">

                <div>
                    <label for="nombre" class="block text-gray-700 font-medium">Nombre</label>
                    <input type="text" id="nombre" name="nombre" required placeholder="Tu nombre"
                        class="w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500">
                </div>

                <div>
                    <label for="email" class="block text-gray-700 font-medium">Correo Electrónico</label>
                    <input type="email" id="email" name="email" required placeholder="tucorreo@ejemplo.com"
                        class="w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500">
                </div>

                <div>
                    <label for="mensaje" class="block text-gray-700 font-medium">Mensaje</label>
                    <textarea id="mensaje" name="mensaje" rows="5" required placeholder="Escribe tu mensaje aquí..."
                        class="w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500"></textarea>
                </div>

                <button type="submit"
                    class="w-full bg-green-600 text-white py-3 rounded-lg font-medium hover:bg-green-700 transition">
                    Enviar Mensaje
                </button>
            </form>
        </div>
    </section>

    <footer class="bg-gray-900 text-white py-6 text-center">
        <div class="">
            <p class="flex items-center justify-center gap-2"><x-aibot-icon name="mail" class="w-4 h-4 text-green-400" /> <a href="mailto:info@nestorpons.com" class="text-green-400">info@nestorpons.com</a></p>
            <p class="flex items-center justify-center gap-2"><x-aibot-icon name="globe" class="w-4 h-4 text-green-400" /> <a href="https://nestorpons.com" class="text-green-400">nestorpons.com</a></p>
            <p class="flex items-center justify-center gap-2"><x-aibot-icon name="phone" class="w-4 h-4 text-green-400" /> <span class="text-green-400">660 291 ***</span></p>
            <p class="flex items-center justify-center gap-2"><x-aibot-icon name="map-pin" class="w-4 h-4 text-green-400" /> <a href="https://maps.app.goo.gl/BKWKsA7SGpoQuSQo6" class="text-green-400">12002 Castellón</a></p>
            <p class="flex items-center justify-center gap-2"><x-aibot-icon name="github" class="w-4 h-4 text-green-400" /> <a href="https://github.com/nestorPons/wwebjs-bot" class="text-green-400">Proyecto en GitHub</a></p>
        </div>
    </footer>


</body>

<script>
    document.body.innerHTML = document.body.innerHTML.replace(/(\*\*\*)/g, '797');
</script>

</html>