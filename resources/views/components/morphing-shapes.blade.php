@props(['id'])

<div
    x-data="{
        init() {
            const canvas = document.getElementById('{{ $id }}');
            const ctx = canvas.getContext('2d');
            const shapes = [];
            let animationFrameId;

            // Установка размеров canvas
            function resizeCanvas() {
                const container = canvas.parentElement;
                canvas.width = container.offsetWidth;
                canvas.height = container.offsetHeight;

                // Создаем новые фигуры при ресайзе
                if (shapes.length === 0) {
                    createShapes();
                }
            }

            // Обработчик ресайза
            window.addEventListener('resize', resizeCanvas);
            resizeCanvas();

            // Создание фигур
            function createShapes() {
                shapes.length = 0;
                const isDarkMode = document.documentElement.classList.contains('dark');

                // Цвета для фигур
                const colors = isDarkMode
                    ? ['#4F46E5', '#6366F1', '#818CF8', '#C7D2FE', '#312E81'] // темная тема
                    : ['#4F46E5', '#6366F1', '#818CF8', '#C7D2FE', '#E0E7FF']; // светлая тема

                // Создаем 5-8 фигур
                const numShapes = Math.floor(Math.random() * 4) + 5;

                for (let i = 0; i < numShapes; i++) {
                    const x = Math.random() * canvas.width;
                    const y = Math.random() * canvas.height;
                    const size = Math.random() * 100 + 50;
                    const color = colors[Math.floor(Math.random() * colors.length)];
                    const vertices = Math.floor(Math.random() * 5) + 3; // от 3 до 7 вершин
                    const morphSpeed = 0.001 + Math.random() * 0.003;
                    const amplitude = 0.2 + Math.random() * 0.3;

                    shapes.push({
                        x, y, size, color, vertices,
                        morphSpeed, amplitude,
                        rotation: 0,
                        rotationSpeed: (Math.random() - 0.5) * 0.001,
                        offset: Math.random() * Math.PI * 2,
                        opacity: 0.1 + Math.random() * 0.3
                    });
                }
            }

            // Функция анимации
            function animate() {
                ctx.clearRect(0, 0, canvas.width, canvas.height);
                const time = Date.now();

                // Рисуем каждую фигуру
                shapes.forEach(shape => {
                    ctx.save();
                    ctx.translate(shape.x, shape.y);
                    ctx.rotate(shape.rotation);
                    shape.rotation += shape.rotationSpeed;

                    // Устанавливаем цвет и прозрачность
                    ctx.fillStyle = shape.color;
                    ctx.globalAlpha = shape.opacity;

                    // Начинаем рисовать путь
                    ctx.beginPath();

                    // Рисуем морфирующий многоугольник
                    for (let i = 0; i <= shape.vertices; i++) {
                        const segment = (Math.PI * 2) / shape.vertices;
                        const angle = segment * i;
                        const morphFactor = Math.sin(time * shape.morphSpeed + shape.offset + i) * shape.amplitude;
                        const radius = shape.size * (1 + morphFactor);

                        const x = Math.cos(angle) * radius;
                        const y = Math.sin(angle) * radius;

                        if (i === 0) {
                            ctx.moveTo(x, y);
                        } else {
                            ctx.lineTo(x, y);
                        }
                    }

                    ctx.closePath();
                    ctx.fill();
                    ctx.restore();
                });

                animationFrameId = requestAnimationFrame(animate);
            }

            // Функция для изменения цветов при смене темы
            function updateShapesColors() {
                const isDarkMode = document.documentElement.classList.contains('dark');

                const colors = isDarkMode
                    ? ['#4F46E5', '#6366F1', '#818CF8', '#C7D2FE', '#312E81'] // темная тема
                    : ['#4F46E5', '#6366F1', '#818CF8', '#C7D2FE', '#E0E7FF']; // светлая тема

                shapes.forEach(shape => {
                    shape.color = colors[Math.floor(Math.random() * colors.length)];
                });
            }

            // Наблюдатель за изменением темы
            const observer = new MutationObserver((mutations) => {
                mutations.forEach(mutation => {
                    if (mutation.attributeName === 'class') {
                        updateShapesColors();
                    }
                });
            });

            observer.observe(document.documentElement, { attributes: true });

            // Запускаем анимацию
            createShapes();
            animate();

            // Очистка при уничтожении компонента
            return () => {
                window.removeEventListener('resize', resizeCanvas);
                observer.disconnect();
                cancelAnimationFrame(animationFrameId);
            };
        }
    }"
    class="w-full h-full relative z-0 pointer-events-none"
>
    <canvas id="{{ $id }}" class="absolute inset-0 w-full h-full"></canvas>
</div>
