@props(['id'])

<div id="{{ $id }}" class="w-full h-full relative z-0 pointer-events-none overflow-hidden">
    <svg class="absolute inset-0 w-full h-full" preserveAspectRatio="xMidYMid slice" viewBox="0 0 1000 1000">
        <defs>
            <!-- Градиенты для линий и узлов -->
            <linearGradient id="line-gradient-{{ $id }}" x1="0%" y1="0%" x2="100%" y2="0%">
                <stop offset="0%" stop-color="rgba(79, 70, 229, 0.1)" /> <!-- primary-500 с низкой прозрачностью -->
                <stop offset="100%" stop-color="rgba(99, 102, 241, 0.4)" /> <!-- indigo-500 -->
            </linearGradient>

            <linearGradient id="node-gradient-{{ $id }}" x1="0%" y1="0%" x2="100%" y2="100%">
                <stop offset="0%" stop-color="rgba(129, 140, 248, 0.6)" /> <!-- indigo-400 -->
                <stop offset="100%" stop-color="rgba(79, 70, 229, 0.8)" /> <!-- primary-600 -->
            </linearGradient>

            <!-- Свечение для узлов -->
            <filter id="glow-{{ $id }}" x="-50%" y="-50%" width="200%" height="200%">
                <feGaussianBlur stdDeviation="5" result="blur" />
                <feComposite in="SourceGraphic" in2="blur" operator="over" />
            </filter>

            <!-- Фильтр для движущихся частиц -->
            <filter id="particle-blur-{{ $id }}" x="-50%" y="-50%" width="200%" height="200%">
                <feGaussianBlur in="SourceGraphic" stdDeviation="1" />
            </filter>
        </defs>

        <!-- Основные слои -->
        <g class="grid-lines"></g>
        <g class="data-nodes"></g>
        <g class="connections"></g>
        <g class="data-particles"></g>
    </svg>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const containerId = '{{ $id }}';
    const container = document.getElementById(containerId);
    if (!container) return;

    const svg = container.querySelector('svg');
    const gridLines = svg.querySelector('.grid-lines');
    const dataNodes = svg.querySelector('.data-nodes');
    const connections = svg.querySelector('.connections');
    const dataParticles = svg.querySelector('.data-particles');

    const isDarkMode = document.documentElement.classList.contains('dark');

    // Конфигурация
    const config = {
        // Сетка
        grid: {
            rows: 8,
            cols: 10,
            opacity: isDarkMode ? 0.15 : 0.08
        },
        // Узлы (представляют точки данных)
        nodes: {
            count: 12,
            minSize: 5,
            maxSize: 12,
            colors: {
                light: [
                    { color: '#4F46E5', opacity: 0.8 }, // primary-600
                    { color: '#6366F1', opacity: 0.8 }, // indigo-500
                    { color: '#818CF8', opacity: 0.7 }, // indigo-400
                    { color: '#C7D2FE', opacity: 0.8 }  // indigo-200
                ],
                dark: [
                    { color: '#4F46E5', opacity: 0.9 }, // primary-600
                    { color: '#6366F1', opacity: 0.9 }, // indigo-500
                    { color: '#818CF8', opacity: 0.8 }, // indigo-400
                    { color: '#C7D2FE', opacity: 0.7 }  // indigo-200
                ]
            }
        },
        // Соединения между узлами
        connections: {
            maxDistance: 250,
            thickness: { min: 1, max: 2.5 },
            opacity: isDarkMode ? 0.25 : 0.2
        },
        // Движущиеся частицы (поток данных)
        particles: {
            count: 30,
            minSize: 1.5,
            maxSize: 3.5,
            speed: { min: 15, max: 40 },
            colors: {
                light: [
                    { color: '#4F46E5', opacity: 0.7 }, // primary-600
                    { color: '#6366F1', opacity: 0.8 }, // indigo-500
                    { color: '#818CF8', opacity: 0.6 }  // indigo-400
                ],
                dark: [
                    { color: '#4F46E5', opacity: 0.9 }, // primary-600
                    { color: '#6366F1', opacity: 0.9 }, // indigo-500
                    { color: '#818CF8', opacity: 0.7 }  // indigo-400
                ]
            }
        }
    };

    // Получаем цвета в зависимости от темы
    const nodeColors = isDarkMode ? config.nodes.colors.dark : config.nodes.colors.light;
    const particleColors = isDarkMode ? config.particles.colors.dark : config.particles.colors.light;

    // Хранение созданных элементов
    const nodes = [];
    const particleTimelines = [];

    // Размеры холста
    const viewBoxWidth = 1000;
    const viewBoxHeight = 1000;

    // Функция для создания SVG элемента
    function createSvgElement(type, attributes = {}) {
        const element = document.createElementNS('http://www.w3.org/2000/svg', type);
        for (const [key, value] of Object.entries(attributes)) {
            element.setAttribute(key, value);
        }
        return element;
    }

    // Создание фоновой сетки
    function createGrid() {
        const { rows, cols, opacity } = config.grid;
        const gridColor = isDarkMode ? 'rgba(255, 255, 255, ' + opacity + ')' : 'rgba(0, 0, 0, ' + opacity + ')';

        // Вертикальные линии
        const xStep = viewBoxWidth / cols;
        for (let i = 1; i < cols; i++) {
            const x = xStep * i;
            const line = createSvgElement('line', {
                x1: x,
                y1: 0,
                x2: x,
                y2: viewBoxHeight,
                stroke: gridColor,
                'stroke-width': '1',
                'stroke-dasharray': '5, 15'
            });
            gridLines.appendChild(line);
        }

        // Горизонтальные линии
        const yStep = viewBoxHeight / rows;
        for (let i = 1; i < rows; i++) {
            const y = yStep * i;
            const line = createSvgElement('line', {
                x1: 0,
                y1: y,
                x2: viewBoxWidth,
                y2: y,
                stroke: gridColor,
                'stroke-width': '1',
                'stroke-dasharray': '5, 15'
            });
            gridLines.appendChild(line);
        }
    }

    // Создание узлов данных
    function createNodes() {
        for (let i = 0; i < config.nodes.count; i++) {
            // Случайные координаты с отступами от краев
            const x = 100 + Math.random() * (viewBoxWidth - 200);
            const y = 100 + Math.random() * (viewBoxHeight - 200);
            const size = config.nodes.minSize + Math.random() * (config.nodes.maxSize - config.nodes.minSize);

            // Выбираем случайный цвет из набора
            const colorData = nodeColors[Math.floor(Math.random() * nodeColors.length)];

            // Создаем узел
            const node = createSvgElement('circle', {
                cx: x,
                cy: y,
                r: size,
                fill: colorData.color,
                opacity: colorData.opacity,
                filter: `url(#glow-${containerId})`
            });

            dataNodes.appendChild(node);

            // Сохраняем информацию об узле
            nodes.push({
                element: node,
                x: x,
                y: y,
                size: size,
                // Анимация пульсации
                timeline: gsap.timeline({
                    repeat: -1,
                    yoyo: true
                }).to(node, {
                    r: size * (0.8 + Math.random() * 0.4),
                    opacity: colorData.opacity * (0.8 + Math.random() * 0.4),
                    duration: 2 + Math.random() * 3,
                    ease: "sine.inOut"
                })
            });
        }
    }

    // Создание соединений между узлами
    function createConnections() {
        // Для каждой пары узлов
        for (let i = 0; i < nodes.length; i++) {
            for (let j = i + 1; j < nodes.length; j++) {
                const nodeA = nodes[i];
                const nodeB = nodes[j];

                // Вычисляем расстояние между узлами
                const dx = nodeA.x - nodeB.x;
                const dy = nodeA.y - nodeB.y;
                const distance = Math.sqrt(dx * dx + dy * dy);

                // Если узлы достаточно близко, создаем соединение
                if (distance < config.connections.maxDistance) {
                    // Толщина линии обратно пропорциональна расстоянию
                    const thickness = config.connections.thickness.max -
                                     (distance / config.connections.maxDistance) *
                                     (config.connections.thickness.max - config.connections.thickness.min);

                    // Прозрачность линии также обратно пропорциональна расстоянию
                    const lineOpacity = config.connections.opacity * (1 - distance / config.connections.maxDistance);

                    // Создаем линию
                    const line = createSvgElement('line', {
                        x1: nodeA.x,
                        y1: nodeA.y,
                        x2: nodeB.x,
                        y2: nodeB.y,
                        stroke: `url(#line-gradient-${containerId})`,
                        'stroke-width': thickness,
                        opacity: lineOpacity
                    });

                    connections.appendChild(line);
                }
            }
        }
    }

    // Создание движущихся частиц (поток данных)
    function createParticles() {
        // Для каждой пары соединенных узлов создаем частицы
        for (let i = 0; i < connections.children.length; i++) {
            const connection = connections.children[i];
            const x1 = parseFloat(connection.getAttribute('x1'));
            const y1 = parseFloat(connection.getAttribute('y1'));
            const x2 = parseFloat(connection.getAttribute('x2'));
            const y2 = parseFloat(connection.getAttribute('y2'));

            // Определяем количество частиц на этом пути (в зависимости от длины)
            const dx = x2 - x1;
            const dy = y2 - y1;
            const distance = Math.sqrt(dx * dx + dy * dy);
            const particlesCount = Math.max(1, Math.floor(distance / 100) + 1);

            for (let j = 0; j < particlesCount; j++) {
                // Выбираем случайный цвет
                const colorData = particleColors[Math.floor(Math.random() * particleColors.length)];
                const size = config.particles.minSize + Math.random() * (config.particles.maxSize - config.particles.minSize);

                // Создаем частицу
                const particle = createSvgElement('circle', {
                    cx: x1,
                    cy: y1,
                    r: size,
                    fill: colorData.color,
                    opacity: colorData.opacity,
                    filter: `url(#particle-blur-${containerId})`
                });

                dataParticles.appendChild(particle);

                // Скорость движения
                const speed = config.particles.speed.min + Math.random() * (config.particles.speed.max - config.particles.speed.min);
                const duration = distance / speed;

                // Анимация движения частицы от одного узла к другому
                const timeline = gsap.timeline({
                    repeat: -1,
                    onComplete: () => timeline.restart()
                });

                // Случайное начальное положение на пути
                const randomStart = Math.random();

                // Начинаем с рассчитанной точки на линии
                gsap.set(particle, {
                    cx: x1 + dx * randomStart,
                    cy: y1 + dy * randomStart
                });

                // Дальше анимируем до конца линии
                timeline.to(particle, {
                    cx: x2,
                    cy: y2,
                    duration: duration * (1 - randomStart),
                    ease: "none"
                });

                // Затем анимируем от начала до той же точки
                timeline.to(particle, {
                    cx: x1,
                    cy: y1,
                    duration: duration,
                    ease: "none"
                });

                // Анимируем от начала до случайной точки
                timeline.to(particle, {
                    cx: x1 + dx * randomStart,
                    cy: y1 + dy * randomStart,
                    duration: duration * randomStart,
                    ease: "none"
                });

                particleTimelines.push(timeline);
            }
        }
    }

    // Обработчик изменения темы
    function updateTheme() {
        const newIsDarkMode = document.documentElement.classList.contains('dark');
        const newNodeColors = newIsDarkMode ? config.nodes.colors.dark : config.nodes.colors.light;
        const newParticleColors = newIsDarkMode ? config.particles.colors.dark : config.particles.colors.light;

        // Обновляем цвет сетки
        const gridColor = newIsDarkMode ?
            `rgba(255, 255, 255, ${config.grid.opacity})` :
            `rgba(0, 0, 0, ${config.grid.opacity})`;

        gridLines.querySelectorAll('line').forEach(line => {
            line.setAttribute('stroke', gridColor);
        });

        // Обновляем цвета узлов
        nodes.forEach(node => {
            const newColorData = newNodeColors[Math.floor(Math.random() * newNodeColors.length)];
            gsap.to(node.element, {
                fill: newColorData.color,
                opacity: newColorData.opacity,
                duration: 1.5,
                ease: "power2.inOut"
            });
        });

        // Обновляем цвета частиц
        Array.from(dataParticles.children).forEach(particle => {
            const newColorData = newParticleColors[Math.floor(Math.random() * newParticleColors.length)];
            gsap.to(particle, {
                fill: newColorData.color,
                opacity: newColorData.opacity,
                duration: 1.5,
                ease: "power2.inOut"
            });
        });
    }

    // Инициализация анимации
    function initAnimation() {
        createGrid();
        createNodes();
        createConnections();
        createParticles();

        // Отслеживаем изменение темы
        const observer = new MutationObserver((mutations) => {
            mutations.forEach(mutation => {
                if (mutation.attributeName === 'class') {
                    updateTheme();
                }
            });
        });

        observer.observe(document.documentElement, { attributes: true });
    }

    // Запускаем инициализацию
    initAnimation();

    // Обработчик изменения размера окна
    window.addEventListener('resize', () => {
        // При изменении размера окна можно пересоздать анимацию или настроить масштабирование
        // Для SVG с viewBox это происходит автоматически
    });
});
</script>
