@props(['id'])

<div id="{{ $id }}" class="w-full h-full relative z-0 overflow-hidden">
    <svg class="absolute inset-0 w-full h-full cursor-pointer" preserveAspectRatio="xMidYMid slice" viewBox="0 0 1000 1000">
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

            <!-- Градиент для сверхвысокого приоритета -->
            <linearGradient id="urgent-gradient-{{ $id }}" x1="0%" y1="0%" x2="100%" y2="100%">
                <stop offset="0%" stop-color="rgba(239, 68, 68, 0.7)" /> <!-- red-500 -->
                <stop offset="100%" stop-color="rgba(220, 38, 38, 0.9)" /> <!-- red-600 -->
            </linearGradient>

            <!-- Градиент для обработанных данных -->
            <linearGradient id="processed-gradient-{{ $id }}" x1="0%" y1="0%" x2="100%" y2="100%">
                <stop offset="0%" stop-color="rgba(34, 197, 94, 0.6)" /> <!-- green-500 -->
                <stop offset="100%" stop-color="rgba(22, 163, 74, 0.8)" /> <!-- green-600 -->
            </linearGradient>

            <!-- Свечение для узлов -->
            <filter id="glow-{{ $id }}" x="-50%" y="-50%" width="200%" height="200%">
                <feGaussianBlur stdDeviation="5" result="blur" />
                <feComposite in="SourceGraphic" in2="blur" operator="over" />
            </filter>

            <!-- Усиленное свечение для новых узлов -->
            <filter id="strong-glow-{{ $id }}" x="-50%" y="-50%" width="200%" height="200%">
                <feGaussianBlur stdDeviation="8" result="blur" />
                <feComposite in="SourceGraphic" in2="blur" operator="over" />
            </filter>

            <!-- Фильтр для движущихся частиц -->
            <filter id="particle-blur-{{ $id }}" x="-50%" y="-50%" width="200%" height="200%">
                <feGaussianBlur in="SourceGraphic" stdDeviation="1" />
            </filter>

            <!-- Фильтр для волны активности -->
            <filter id="wave-blur-{{ $id }}">
                <feGaussianBlur in="SourceGraphic" stdDeviation="3" />
            </filter>

            <!-- Анимация пульсации -->
            <radialGradient id="pulse-gradient-{{ $id }}" cx="50%" cy="50%" r="50%" fx="50%" fy="50%">
                <stop offset="0%" stop-color="rgba(79, 70, 229, 0.7)" stop-opacity="0.8" />
                <stop offset="100%" stop-color="rgba(79, 70, 229, 0)" stop-opacity="0" />
            </radialGradient>
        </defs>

        <!-- Основные слои -->
        <g class="wave-effects"></g>
        <g class="grid-lines"></g>
        <g class="data-nodes"></g>
        <g class="connections"></g>
        <g class="data-particles"></g>
        <g class="pulse-effects"></g>
    </svg>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const containerId = '{{ $id }}';
    const container = document.getElementById(containerId);
    if (!container) return;

    const svg = container.querySelector('svg');
    const waveEffects = svg.querySelector('.wave-effects');
    const gridLines = svg.querySelector('.grid-lines');
    const dataNodes = svg.querySelector('.data-nodes');
    const connections = svg.querySelector('.connections');
    const dataParticles = svg.querySelector('.data-particles');
    const pulseEffects = svg.querySelector('.pulse-effects');

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
            initialCount: 8,
            maxCount: 15,
            minSize: 5,
            maxSize: 12,
            // Категории узлов (имитируют разные типы заявок)
            types: [
                { name: 'regular', probability: 0.7 }, // обычные заявки
                { name: 'urgent', probability: 0.15 }, // срочные заявки
                { name: 'processed', probability: 0.15 } // обработанные заявки
            ],
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
            },
            // Динамика узлов
            dynamics: {
                addInterval: { min: 5000, max: 8000 }, // интервал добавления новых узлов
                removeInterval: { min: 10000, max: 15000 }, // интервал удаления старых узлов
                processInterval: { min: 5000, max: 10000 } // интервал обработки узлов
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
        },
        // Волны активности
        waves: {
            interval: { min: 8000, max: 15000 }, // интервал генерации новых волн
            duration: { min: 3, max: 5 }, // продолжительность анимации волны
            size: { min: 50, max: 150 } // размер волны
        },
        // Эффекты пульсации
        pulse: {
            size: { min: 20, max: 40 }, // размер эффекта пульсации
            duration: { min: 1.5, max: 2.5 } // продолжительность эффекта
        },
        // Настройки для интерактивных элементов при наведении курсора
        interactive: {
            enabled: true,                  // Включение/отключение интерактивных эффектов
            maxCursorNodes: 3,              // Максимальное количество узлов, создаваемых при наведении
            cursorNodeRadius: 80,           // Радиус вокруг курсора для создания узлов
            nodeLifetime: { min: 7, max: 12 }, // Время жизни созданных узлов (в секундах) - увеличено
            throttleInterval: 200,          // Интервал троттлинга для событий мыши (в мс)
            cursorNodeSize: { min: 3, max: 8 }, // Размер узлов, создаваемых при наведении
            gridDistortion: {
                enabled: true,              // Включение/отключение искажения сетки
                radius: 180,                // Радиус искажения сетки - увеличен
                strength: 60,               // Сила искажения - увеличена
                fadeRadius: 250,            // Радиус затухания эффекта - увеличен
                duration: 0.5,              // Длительность анимации искажения (в секундах)
                lensEffect: true            // Включение эффекта линзы
            }
        }
    };

    // Получаем цвета в зависимости от темы
    const nodeColors = isDarkMode ? config.nodes.colors.dark : config.nodes.colors.light;
    const particleColors = isDarkMode ? config.particles.colors.dark : config.particles.colors.light;

    // Хранение созданных элементов
    const nodes = [];
    const particleTimelines = [];
    const waveTimelines = [];
    const cursorNodes = []; // Для хранения интерактивных узлов, созданных при наведении курсора

    // Таймеры для динамики узлов
    let addNodeTimer, removeNodeTimer, processNodeTimer, waveTimer, lastCursorMove;

    // Для отслеживания позиции курсора
    let cursorX = 0, cursorY = 0;
    let isMouseInside = false;

    // Координаты искаженной сетки
    let distortedGridCoords = [];
    let originalGridCoords = [];
    let gridDistortionEnabled = false;

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

    // Функция для выбора случайного элемента из массива с учетом весов
    function getRandomWeightedItem(items, weightProp) {
        const totalWeight = items.reduce((sum, item) => sum + item[weightProp], 0);
        let random = Math.random() * totalWeight;

        for (const item of items) {
            random -= item[weightProp];
            if (random <= 0) {
                return item;
            }
        }

        return items[0]; // Если что-то пошло не так, вернем первый элемент
    }

    // Создание фоновой сетки
    function createGrid() {
        // Очищаем предыдущую сетку
        while (gridLines.firstChild) {
            gridLines.removeChild(gridLines.firstChild);
        }

        // Высчитываем шаг сетки
        const stepX = viewBoxWidth / config.grid.cols;
        const stepY = viewBoxHeight / config.grid.rows;

        // Сохраняем оригинальные координаты сетки для искажения
        originalGridCoords = [];

        // Создаем линии сетки
        for (let i = 1; i < config.grid.cols; i++) {
            const x = stepX * i;

            // Сохраняем координаты вертикальных линий
            originalGridCoords.push({
                type: 'vertical',
                index: i,
                x1: x,
                y1: 0,
                x2: x,
                y2: viewBoxHeight
            });

            const line = createSvgElement('line', {
                x1: x,
                y1: 0,
                x2: x,
                y2: viewBoxHeight,
                stroke: isDarkMode ? 'white' : 'black',
                'stroke-width': 0.5,
                opacity: config.grid.opacity,
                'data-grid-line': 'vertical',
                'data-index': i
            });
            gridLines.appendChild(line);
        }

        for (let i = 1; i < config.grid.rows; i++) {
            const y = stepY * i;

            // Сохраняем координаты горизонтальных линий
            originalGridCoords.push({
                type: 'horizontal',
                index: i,
                x1: 0,
                y1: y,
                x2: viewBoxWidth,
                y2: y
            });

            const line = createSvgElement('line', {
                x1: 0,
                y1: y,
                x2: viewBoxWidth,
                y2: y,
                stroke: isDarkMode ? 'white' : 'black',
                'stroke-width': 0.5,
                opacity: config.grid.opacity,
                'data-grid-line': 'horizontal',
                'data-index': i
            });
            gridLines.appendChild(line);
        }

        // Копируем оригинальные координаты для использования в искажении
        distortedGridCoords = JSON.parse(JSON.stringify(originalGridCoords));

        console.log(`Создана сетка: ${gridLines.children.length} линий`);
    }

    // Функция для искажения сетки
    function distortGrid(cursorX, cursorY, strength = config.interactive.gridDistortion.strength) {
        if (!config.interactive.gridDistortion.enabled || !isMouseInside) return;

        const radius = config.interactive.gridDistortion.radius;
        const fadeRadius = config.interactive.gridDistortion.fadeRadius;
        const lensEffect = config.interactive.gridDistortion.lensEffect;

        // Обновляем искаженные координаты
        for (let i = 0; i < originalGridCoords.length; i++) {
            const orig = originalGridCoords[i];
            const dist = distortedGridCoords[i];

            if (orig.type === 'vertical') {
                // Новый подход к искажению для вертикальных линий
                const x = orig.x1; // Горизонтальная позиция линии

                // Для каждой точки по высоте линии
                for (let y = 0; y <= viewBoxHeight; y += viewBoxHeight / 20) {
                    // Расстояние от точки до курсора
                    const dx = x - cursorX;
                    const dy = y - cursorY;
                    const distance = Math.sqrt(dx * dx + dy * dy);

                    // Если точка в зоне воздействия
                    if (distance < fadeRadius) {
                        // Коэффициент искажения с плавным затуханием по краям
                        let factor;

                        if (lensEffect) {
                            // Эффект линзы/выпуклости - максимальное искажение на границе радиуса,
                            // минимальное в центре и за пределами fadeRadius
                            if (distance < radius) {
                                // Внутри радиуса создаем эффект выпуклости
                                // Формула создает куполообразную форму с 0 в центре,
                                // возрастающую к границе radius
                                factor = (distance / radius) * (1 - Math.pow(distance / radius, 2));
                                factor = factor * 2.5; // Усиливаем эффект
                            } else {
                                // За пределами основного радиуса - затухание
                                factor = Math.max(0, 1 - (distance - radius) / (fadeRadius - radius));
                                factor = factor * 0.5; // Ослабляем эффект на границе
                            }
                        } else {
                            // Обычное линейное затухание
                            factor = Math.max(0, 1 - distance / fadeRadius);
                        }

                        // Финальная сила искажения
                        const distortion = strength * factor;

                        // Направление искажения (к курсору или от него)
                        const angle = Math.atan2(dy, dx);

                        // Искажение для X-координаты
                        let distX = Math.cos(angle) * distortion;

                        // Применяем искажение к крайним точкам линии
                        if (y === 0) {
                            dist.x1 = orig.x1 - distX;
                        } else if (y === viewBoxHeight) {
                            dist.x2 = orig.x2 - distX;
                        }
                    } else {
                        // Точка вне зоны воздействия - возвращаем оригинальные координаты
                        if (y === 0) {
                            dist.x1 = orig.x1;
                        } else if (y === viewBoxHeight) {
                            dist.x2 = orig.x2;
                        }
                    }
                }
            } else if (orig.type === 'horizontal') {
                // Подобный подход для горизонтальных линий
                const y = orig.y1; // Вертикальная позиция линии

                // Для каждой точки по ширине линии
                for (let x = 0; x <= viewBoxWidth; x += viewBoxWidth / 20) {
                    // Расстояние от точки до курсора
                    const dx = x - cursorX;
                    const dy = y - cursorY;
                    const distance = Math.sqrt(dx * dx + dy * dy);

                    // Если точка в зоне воздействия
                    if (distance < fadeRadius) {
                        // Коэффициент искажения с плавным затуханием по краям
                        let factor;

                        if (lensEffect) {
                            // Эффект линзы/выпуклости
                            if (distance < radius) {
                                factor = (distance / radius) * (1 - Math.pow(distance / radius, 2));
                                factor = factor * 2.5; // Усиливаем эффект
                            } else {
                                factor = Math.max(0, 1 - (distance - radius) / (fadeRadius - radius));
                                factor = factor * 0.5; // Ослабляем эффект на границе
                            }
                        } else {
                            // Обычное линейное затухание
                            factor = Math.max(0, 1 - distance / fadeRadius);
                        }

                        // Финальная сила искажения
                        const distortion = strength * factor;

                        // Направление искажения
                        const angle = Math.atan2(dy, dx);

                        // Искажение для Y-координаты
                        let distY = Math.sin(angle) * distortion;

                        // Применяем искажение к крайним точкам
                        if (x === 0) {
                            dist.y1 = orig.y1 - distY;
                        } else if (x === viewBoxWidth) {
                            dist.y2 = orig.y2 - distY;
                        }
                    } else {
                        // Возвращаем оригинальные координаты
                        if (x === 0) {
                            dist.y1 = orig.y1;
                        } else if (x === viewBoxWidth) {
                            dist.y2 = orig.y2;
                        }
                    }
                }
            }

            // Применяем искаженные координаты к элементам
            const lineElement = gridLines.querySelector(`[data-grid-line="${orig.type}"][data-index="${orig.index}"]`);
            if (lineElement) {
                gsap.to(lineElement, {
                    attr: {
                        x1: dist.x1,
                        y1: dist.y1,
                        x2: dist.x2,
                        y2: dist.y2
                    },
                    duration: config.interactive.gridDistortion.duration,
                    ease: "power2.out"
                });
            }
        }
    }

    // Функция для восстановления сетки
    function resetGrid() {
        // Возвращаем все линии к исходному положению
        for (let i = 0; i < originalGridCoords.length; i++) {
            const orig = originalGridCoords[i];
            const lineElement = gridLines.querySelector(`[data-grid-line="${orig.type}"][data-index="${orig.index}"]`);

            if (lineElement) {
                gsap.to(lineElement, {
                    attr: {
                        x1: orig.x1,
                        y1: orig.y1,
                        x2: orig.x2,
                        y2: orig.y2
                    },
                    duration: config.interactive.gridDistortion.duration,
                    ease: "power2.out"
                });
            }
        }

        // Сбрасываем искаженные координаты
        distortedGridCoords = JSON.parse(JSON.stringify(originalGridCoords));
    }

    // Создание узлов данных
    function createNodes(count) {
        for (let i = 0; i < count; i++) {
            createNode();
        }
    }

    // Функция создания одного узла
    function createNode(isNewNode = false) {
        if (nodes.length >= config.nodes.maxCount) return;

        // Случайные координаты с отступами от краев
        const x = 100 + Math.random() * (viewBoxWidth - 200);
        const y = 100 + Math.random() * (viewBoxHeight - 200);
        const size = config.nodes.minSize + Math.random() * (config.nodes.maxSize - config.nodes.minSize);

        // Выбираем случайный тип узла
        const nodeType = getRandomWeightedItem(config.nodes.types, 'probability');

        // Выбираем цвет и фильтр в зависимости от типа
        let fill, filter;

        switch(nodeType.name) {
            case 'urgent':
                fill = `url(#urgent-gradient-${containerId})`;
                filter = isNewNode ? `url(#strong-glow-${containerId})` : `url(#glow-${containerId})`;
                break;
            case 'processed':
                fill = `url(#processed-gradient-${containerId})`;
                filter = `url(#glow-${containerId})`;
                break;
            default: // regular
                const colorData = nodeColors[Math.floor(Math.random() * nodeColors.length)];
                fill = colorData.color;
                filter = isNewNode ? `url(#strong-glow-${containerId})` : `url(#glow-${containerId})`;
        }

        // Создаем узел с начальной прозрачностью 0 и размером 0 если это новый узел
        const initialOpacity = isNewNode ? 0 : (nodeType.name === 'regular' ? 0.8 : 0.9);
        const initialSize = isNewNode ? 0 : size;

        const node = createSvgElement('circle', {
            cx: x,
            cy: y,
            r: initialSize,
            fill: fill,
            opacity: initialOpacity,
            filter: filter,
            'data-type': nodeType.name
        });

        dataNodes.appendChild(node);

        // Сохраняем информацию об узле
        const nodeObj = {
            element: node,
            x: x,
            y: y,
            size: size,
            type: nodeType.name,
            createdAt: Date.now(),
            // Анимация пульсации (в зависимости от типа)
            timeline: gsap.timeline({
                repeat: -1,
                yoyo: true
            })
        };

        // Настраиваем анимацию пульсации в зависимости от типа узла
        switch(nodeType.name) {
            case 'urgent':
                nodeObj.timeline.to(node, {
                    r: size * 1.2,
                    opacity: 0.95,
                    duration: 1 + Math.random(),
                    ease: "sine.inOut"
                });
                break;
            case 'processed':
                nodeObj.timeline.to(node, {
                    r: size * 1.05,
                    opacity: 0.85,
                    duration: 2 + Math.random() * 2,
                    ease: "sine.inOut"
                });
                break;
            default: // regular
                nodeObj.timeline.to(node, {
                    r: size * (0.8 + Math.random() * 0.4),
                    opacity: 0.75 + Math.random() * 0.2,
                    duration: 2 + Math.random() * 3,
                    ease: "sine.inOut"
                });
        }

        nodes.push(nodeObj);

        // Если это новый узел, используем requestAnimationFrame для асинхронного обновления соединений
        // Это предотвратит блокировку основного потока и паузы в анимации
        if (isNewNode && nodes.length > 1) {
            // Сначала сделаем анимацию появления, не блокируя основной поток
            gsap.to(node, {
                r: size,
                opacity: nodeType.name === 'regular' ? 0.8 : 0.9,
                duration: 1.2,
                ease: "elastic.out(1, 0.5)", // Эффект упругого появления
                onComplete: () => {
                    // Добавляем эффект пульсации при появлении
                    createPulseEffect(x, y);

                    // Используем requestAnimationFrame для асинхронного обновления соединений
                    // после завершения анимации появления
                    requestAnimationFrame(() => {
                        updateConnectionsOptimized(nodeObj);
                    });
                }
            });
        } else if (isNewNode) {
            // Анимируем появление узла, даже если это первый узел
            gsap.to(node, {
                r: size,
                opacity: nodeType.name === 'regular' ? 0.8 : 0.9,
                duration: 1.2,
                ease: "elastic.out(1, 0.5)",
                onComplete: () => {
                    createPulseEffect(x, y);
                }
            });
        }

        return nodeObj;
    }

    // Оптимизированная функция обновления соединений, которая работает только с новым узлом
    function updateConnectionsOptimized(newNode) {
        // Создаем соединения только между новым узлом и существующими узлами
        // Это гораздо эффективнее, чем пересчитывать все соединения
        const newConnections = {};
        const nodeIndex = nodes.indexOf(newNode);

        if (nodeIndex === -1) return; // На всякий случай проверяем, что узел существует в массиве

        // Проверяем соединения только между новым узлом и другими узлами
        for (let i = 0; i < nodes.length; i++) {
            if (i === nodeIndex) continue; // Пропускаем сам узел

            const otherNode = nodes[i];

            // Проверка валидности узла
            if (!otherNode || !otherNode.x) continue;

            // Вычисляем расстояние между узлами
            const dx = newNode.x - otherNode.x;
            const dy = newNode.y - otherNode.y;
            const distance = Math.sqrt(dx * dx + dy * dy);

            // Ключ для этого соединения (всегда используем меньший индекс первым)
            const connectionKey = nodeIndex < i ? `${nodeIndex}-${i}` : `${i}-${nodeIndex}`;

            // Если узлы достаточно близко, создаем соединение
            if (distance < config.connections.maxDistance) {
                // Создаем новое соединение
                const thickness = config.connections.thickness.max -
                                 (distance / config.connections.maxDistance) *
                                 (config.connections.thickness.max - config.connections.thickness.min);
                const lineOpacity = config.connections.opacity * (1 - distance / config.connections.maxDistance);

                // Убедимся, что nodeIndex и i корректно определены
                const sourceIndex = nodeIndex < i ? nodeIndex : i;
                const targetIndex = nodeIndex < i ? i : nodeIndex;

                const line = createSvgElement('line', {
                    x1: nodes[sourceIndex].x,
                    y1: nodes[sourceIndex].y,
                    x2: nodes[targetIndex].x,
                    y2: nodes[targetIndex].y,
                    stroke: `url(#line-gradient-${containerId})`,
                    'stroke-width': thickness,
                    opacity: lineOpacity,
                    'data-node-a': sourceIndex,
                    'data-node-b': targetIndex,
                    'data-connection-key': connectionKey
                });

                connections.appendChild(line);

                // Отмечаем соединение как новое
                newConnections[connectionKey] = {
                    element: line,
                    distance: distance
                };
            }
        }

        // Асинхронно создаем частицы только для новых соединений
        if (Object.keys(newConnections).length > 0) {
            requestAnimationFrame(() => {
                updateParticlesForNewConnections(newConnections);
            });
        }
    }

    // Удаление случайного узла
    function removeRandomNode() {
        if (nodes.length <= config.nodes.initialCount) return;

        // Выбираем случайный узел, не срочный
        const regularNodes = nodes.filter(node => node.type !== 'urgent');
        if (regularNodes.length === 0) return;

        const randomIndex = Math.floor(Math.random() * regularNodes.length);
        const nodeToRemove = regularNodes[randomIndex];
        const nodeIndex = nodes.indexOf(nodeToRemove);

        // Анимация исчезновения
        gsap.to(nodeToRemove.element, {
            opacity: 0,
            r: nodeToRemove.size * 0.5,
            duration: 1,
            ease: "power2.in",
            onComplete: () => {
                // Удаляем элемент из DOM
                if (nodeToRemove.element.parentNode === dataNodes) {
                    dataNodes.removeChild(nodeToRemove.element);
                }

                // Удаляем из массива
                if (nodeIndex !== -1) {
                    nodes.splice(nodeIndex, 1);
                }

                // Обновляем соединения асинхронно
                requestAnimationFrame(() => {
                    updateConnections();
                });
            }
        });
    }

    // Обработка случайного узла (изменение типа)
    function processRandomNode() {
        // Ищем узлы, которые можно обработать (не processed)
        const unprocessedNodes = nodes.filter(node => node.type !== 'processed');
        if (unprocessedNodes.length === 0) return;

        const randomIndex = Math.floor(Math.random() * unprocessedNodes.length);
        const nodeToProcess = unprocessedNodes[randomIndex];

        // Меняем тип на processed
        nodeToProcess.type = 'processed';
        nodeToProcess.element.setAttribute('data-type', 'processed');

        // Анимация обработки
        gsap.to(nodeToProcess.element, {
            fill: `url(#processed-gradient-${containerId})`,
            duration: 1,
            ease: "power2.inOut"
        });

        // Обновляем анимацию пульсации
        nodeToProcess.timeline.clear();
        nodeToProcess.timeline.to(nodeToProcess.element, {
            r: nodeToProcess.size * 1.05,
            opacity: 0.85,
            duration: 2 + Math.random() * 2,
            ease: "sine.inOut",
            repeat: -1,
            yoyo: true
        });

        // Добавляем эффект волны при обработке
        createWaveEffect(nodeToProcess.x, nodeToProcess.y);
    }

    // Функция для обновления только частиц для новых соединений
    function updateParticlesForNewConnections(newConnections) {
        // Создаем новые частицы только для новых соединений
        for (const key in newConnections) {
            const connection = newConnections[key].element;
            const x1 = parseFloat(connection.getAttribute('x1'));
            const y1 = parseFloat(connection.getAttribute('y1'));
            const x2 = parseFloat(connection.getAttribute('x2'));
            const y2 = parseFloat(connection.getAttribute('y2'));
            const distance = newConnections[key].distance;

            // Получаем индексы узлов
            const nodeAIndex = parseInt(connection.getAttribute('data-node-a'));
            const nodeBIndex = parseInt(connection.getAttribute('data-node-b'));

            // Проверяем, что индексы узлов корректны
            if (isNaN(nodeAIndex) || isNaN(nodeBIndex) ||
                nodeAIndex >= nodes.length || nodeBIndex >= nodes.length) {
                continue;
            }

            // Определяем, есть ли среди них срочные или обработанные
            const hasUrgent = nodes[nodeAIndex].type === 'urgent' || nodes[nodeBIndex].type === 'urgent';
            const hasProcessed = nodes[nodeAIndex].type === 'processed' || nodes[nodeBIndex].type === 'processed';

            // Определяем количество частиц на этом пути (в зависимости от длины)
            const particlesCount = Math.max(1, Math.floor(distance / 100));

            // Создаем по одной частице за раз, используя микрозадержки, чтобы не блокировать основной поток
            const createParticlesSequentially = (index) => {
                if (index >= particlesCount + 1) return;

                // Выбираем цвет в зависимости от типов соединенных узлов
                let colorData;

                if (hasUrgent) {
                    // Срочные частицы (красноватые)
                    colorData = { color: '#EF4444', opacity: 0.8 };
                } else if (hasProcessed) {
                    // Обработанные частицы (зеленоватые)
                    colorData = { color: '#22C55E', opacity: 0.7 };
                } else {
                    // Обычные частицы
                    colorData = particleColors[Math.floor(Math.random() * particleColors.length)];
                }

                const size = config.particles.minSize + Math.random() * (config.particles.maxSize - config.particles.minSize);

                // Создаем частицу (начальное положение - в начале линии)
                const particle = createSvgElement('circle', {
                    cx: x1,
                    cy: y1,
                    r: 0, // Начальный размер 0 для анимации появления
                    fill: colorData.color,
                    opacity: 0, // Начальная прозрачность 0 для анимации появления
                    filter: `url(#particle-blur-${containerId})`,
                    'data-connection': key
                });

                dataParticles.appendChild(particle);

                // Анимируем появление частицы
                gsap.to(particle, {
                    r: size,
                    opacity: colorData.opacity,
                    duration: 0.6,
                    ease: "power2.out",
                    delay: Math.random() * 0.3
                });

                // Скорость движения (срочные быстрее)
                const speedMultiplier = hasUrgent ? 1.5 : (hasProcessed ? 1.2 : 1);
                const speed = (config.particles.speed.min + Math.random() * (config.particles.speed.max - config.particles.speed.min)) * speedMultiplier;
                const duration = distance / speed;

                // Задержка старта для распределения частиц по линии
                const startDelay = (duration / particlesCount) * index;

                // Создаем простую GSAP-анимацию для частицы
                const timeline = gsap.timeline({
                    repeat: -1,
                    delay: startDelay % duration // Циклическая задержка
                });

                // Сохраняем ссылку на элемент частицы
                timeline.data = { particleElement: particle };

                // Анимация движения от начала к концу и обратно
                timeline.to(particle, {
                    cx: x2,
                    cy: y2,
                    duration: duration,
                    ease: "none"
                });

                timeline.to(particle, {
                    cx: x1,
                    cy: y1,
                    duration: duration,
                    ease: "none"
                });

                particleTimelines.push(timeline);

                // Создаем следующую частицу с небольшой задержкой
                setTimeout(() => {
                    createParticlesSequentially(index + 1);
                }, 10); // Маленькая задержка, чтобы не блокировать основной поток
            };

            // Начинаем создавать частицы последовательно
            createParticlesSequentially(0);
        }
    }

    // Создание эффекта пульсации
    function createPulseEffect(x, y) {
        const size = config.pulse.size.min + Math.random() * (config.pulse.size.max - config.pulse.size.min);
        const duration = config.pulse.duration.min + Math.random() * (config.pulse.duration.max - config.pulse.duration.min);

        const pulse = createSvgElement('circle', {
            cx: x,
            cy: y,
            r: 0, // Начинаем с нулевого размера
            fill: `url(#pulse-gradient-${containerId})`,
            opacity: 0, // Начинаем с нулевой прозрачности
            filter: `url(#glow-${containerId})`
        });

        pulseEffects.appendChild(pulse);

        // Улучшенная анимация пульсации и исчезновения
        const timeline = gsap.timeline({
            onComplete: () => {
                pulse.remove();
            }
        });

        // Сначала быстро увеличиваем размер и прозрачность
        timeline.to(pulse, {
            r: size * 0.5,
            opacity: 0.8,
            duration: duration * 0.3,
            ease: "power2.out"
        });

        // Затем медленно увеличиваем и снижаем прозрачность
        timeline.to(pulse, {
            r: size,
            opacity: 0,
            duration: duration * 0.7,
            ease: "power2.in"
        });
    }

    // Создание эффекта волны активности
    function createWaveEffect(x, y) {
        const size = config.waves.size.min + Math.random() * (config.waves.size.max - config.waves.size.min);
        const duration = config.waves.duration.min + Math.random() * (config.waves.duration.max - config.waves.duration.min);

        const wave = createSvgElement('circle', {
            cx: x,
            cy: y,
            r: 0, // Начинаем с нулевого размера
            stroke: isDarkMode ? 'rgba(129, 140, 248, 0.4)' : 'rgba(79, 70, 229, 0.3)',
            'stroke-width': 2,
            fill: 'none',
            opacity: 0, // Начинаем с нулевой прозрачности
            filter: `url(#wave-blur-${containerId})`
        });

        waveEffects.appendChild(wave);

        // Улучшенная анимация расширения и исчезновения с двумя стадиями
        const timeline = gsap.timeline({
            onComplete: () => {
                waveEffects.removeChild(wave);
                const index = waveTimelines.indexOf(timeline);
                if (index !== -1) {
                    waveTimelines.splice(index, 1);
                }
            }
        });

        // Первая стадия: появление и начальное расширение
        timeline.to(wave, {
            r: size * 0.4,
            opacity: 0.8,
            duration: duration * 0.3,
            ease: "power2.out"
        });

        // Вторая стадия: дальнейшее расширение и исчезновение
        timeline.to(wave, {
            r: size,
            opacity: 0,
            'stroke-width': 0.5,
            duration: duration * 0.7,
            ease: "power1.in"
        });

        waveTimelines.push(timeline);
    }

    // Создание сильной волны активности (независимо от узла)
    function createRandomWave() {
        const x = Math.random() * viewBoxWidth;
        const y = Math.random() * viewBoxHeight;

        createWaveEffect(x, y);

        // Планируем следующую волну
        scheduleNextWave();
    }

    // Планирование следующей волны
    function scheduleNextWave() {
        const delay = config.waves.interval.min + Math.random() * (config.waves.interval.max - config.waves.interval.min);
        waveTimer = setTimeout(createRandomWave, delay);
    }

    // Создание соединений между узлами (с сохранением существующих соединений)
    function createConnections() {
        // Сохраняем существующие соединения для сравнения
        const existingConnections = {};
        for (let i = 0; i < connections.children.length; i++) {
            const connection = connections.children[i];
            const nodeAIndex = parseInt(connection.getAttribute('data-node-a'));
            const nodeBIndex = parseInt(connection.getAttribute('data-node-b'));
            const key = `${nodeAIndex}-${nodeBIndex}`;
            existingConnections[key] = {
                element: connection,
                x1: parseFloat(connection.getAttribute('x1')),
                y1: parseFloat(connection.getAttribute('y1')),
                x2: parseFloat(connection.getAttribute('x2')),
                y2: parseFloat(connection.getAttribute('y2'))
            };
        }

        // Для определения новых и сохраненных соединений
        const newConnections = {};
        const keptConnections = {};
        const removedConnections = {...existingConnections}; // Копия для отслеживания удаленных

        // Проверка наличия узлов
        if (nodes.length === 0) {
            console.log("Не найдено узлов для создания соединений");
            return;
        }

        console.log(`Найдено ${nodes.length} узлов для создания соединений`);

        // Для каждой пары узлов проверяем расстояние и создаем/обновляем соединения
        for (let i = 0; i < nodes.length; i++) {
            for (let j = i + 1; j < nodes.length; j++) {
                const nodeA = nodes[i];
                const nodeB = nodes[j];

                // Проверка валидности узлов
                if (!nodeA || !nodeB || !nodeA.x || !nodeB.x) {
                    console.log(`Невалидная пара узлов: узел A (${i}): ${Boolean(nodeA)}, узел B (${j}): ${Boolean(nodeB)}`);
                    continue;
                }

                // Вычисляем расстояние между узлами
                const dx = nodeA.x - nodeB.x;
                const dy = nodeA.y - nodeB.y;
                const distance = Math.sqrt(dx * dx + dy * dy);

                // Ключ для этого соединения
                const connectionKey = `${i}-${j}`;

                // Если узлы достаточно близко, создаем/обновляем соединение
                if (distance < config.connections.maxDistance) {
                    // Проверяем, существует ли уже такое соединение
                    if (existingConnections[connectionKey]) {
                        // Если существует, обновляем координаты
                        const existingConnection = existingConnections[connectionKey].element;

                        existingConnection.setAttribute('x1', nodeA.x);
                        existingConnection.setAttribute('y1', nodeA.y);
                        existingConnection.setAttribute('x2', nodeB.x);
                        existingConnection.setAttribute('y2', nodeB.y);

                        // Толщина и прозрачность обновляются
                        const thickness = config.connections.thickness.max -
                                         (distance / config.connections.maxDistance) *
                                         (config.connections.thickness.max - config.connections.thickness.min);
                        const lineOpacity = config.connections.opacity * (1 - distance / config.connections.maxDistance);

                        existingConnection.setAttribute('stroke-width', thickness);
                        existingConnection.setAttribute('opacity', lineOpacity);

                        // Отмечаем соединение как сохраненное
                        keptConnections[connectionKey] = {
                            element: existingConnection,
                            updated: true
                        };

                        // Удаляем из списка на удаление
                        delete removedConnections[connectionKey];
                    } else {
                        // Создаем новое соединение
                        const thickness = config.connections.thickness.max -
                                         (distance / config.connections.maxDistance) *
                                         (config.connections.thickness.max - config.connections.thickness.min);
                        const lineOpacity = config.connections.opacity * (1 - distance / config.connections.maxDistance);

                        const line = createSvgElement('line', {
                            x1: nodeA.x,
                            y1: nodeA.y,
                            x2: nodeB.x,
                            y2: nodeB.y,
                            stroke: `url(#line-gradient-${containerId})`,
                            'stroke-width': thickness,
                            opacity: lineOpacity,
                            'data-node-a': i,
                            'data-node-b': j,
                            'data-connection-key': connectionKey
                        });

                        connections.appendChild(line);

                        // Отмечаем соединение как новое
                        newConnections[connectionKey] = {
                            element: line,
                            distance: distance
                        };
                    }
                }
            }
        }

        // Удаляем соединения, которые больше не нужны
        for (const key in removedConnections) {
            const connection = removedConnections[key].element;
            connection.remove();
        }

        console.log(`Обновлено соединений: сохранено ${Object.keys(keptConnections).length}, создано ${Object.keys(newConnections).length}, удалено ${Object.keys(removedConnections).length}`);

        // Теперь обновляем только необходимые частицы
        updateParticles(newConnections, keptConnections, removedConnections);
    }

    // Обновление только необходимых частиц (новые, удаленные)
    function updateParticles(newConnections, keptConnections, removedConnections) {
        // Сохраняем существующие частицы для каждого соединения
        const existingParticlesByConnection = {};
        for (let i = 0; i < dataParticles.children.length; i++) {
            const particle = dataParticles.children[i];
            const connectionKey = particle.getAttribute('data-connection');
            if (connectionKey) {
                if (!existingParticlesByConnection[connectionKey]) {
                    existingParticlesByConnection[connectionKey] = [];
                }
                existingParticlesByConnection[connectionKey].push(particle);
            }
        }

        // Список частиц для удаления
        const particlesToRemove = [];

        // 1. Удаляем частицы для соединений, которые больше не существуют
        for (const key in removedConnections) {
            if (existingParticlesByConnection[key]) {
                existingParticlesByConnection[key].forEach(particle => {
                    particlesToRemove.push(particle);

                    // Находим и останавливаем таймлайн этой частицы
                    const timelineIndex = particleTimelines.findIndex(
                        tl => tl.data && tl.data.particleElement === particle
                    );
                    if (timelineIndex !== -1) {
                        particleTimelines[timelineIndex].kill();
                        particleTimelines.splice(timelineIndex, 1);
                    }
                });

                // Удаляем запись о частицах этого соединения
                delete existingParticlesByConnection[key];
            }
        }

        // Анимируем исчезновение ненужных частиц
        if (particlesToRemove.length > 0) {
            gsap.to(particlesToRemove, {
                opacity: 0,
                scale: 0.5,
                duration: 0.6,
                ease: "power2.out",
                stagger: 0.03,
                onComplete: () => {
                    // Удаляем частицы из DOM после анимации
                    particlesToRemove.forEach(particle => {
                        if (particle.parentNode === dataParticles) {
                            dataParticles.removeChild(particle);
                        }
                    });
                }
            });
        }

        // 2. Создаем новые частицы только для новых соединений
        for (const key in newConnections) {
            const connection = newConnections[key].element;
            const x1 = parseFloat(connection.getAttribute('x1'));
            const y1 = parseFloat(connection.getAttribute('y1'));
            const x2 = parseFloat(connection.getAttribute('x2'));
            const y2 = parseFloat(connection.getAttribute('y2'));
            const distance = newConnections[key].distance;

            // Получаем индексы узлов
            const nodeAIndex = parseInt(connection.getAttribute('data-node-a'));
            const nodeBIndex = parseInt(connection.getAttribute('data-node-b'));

            // Проверяем, что индексы узлов корректны
            if (isNaN(nodeAIndex) || isNaN(nodeBIndex) ||
                nodeAIndex >= nodes.length || nodeBIndex >= nodes.length) {
                continue;
            }

            // Определяем, есть ли среди них срочные или обработанные
            const hasUrgent = nodes[nodeAIndex].type === 'urgent' || nodes[nodeBIndex].type === 'urgent';
            const hasProcessed = nodes[nodeAIndex].type === 'processed' || nodes[nodeBIndex].type === 'processed';

            // Определяем количество частиц на этом пути (в зависимости от длины)
            const particlesCount = Math.max(1, Math.floor(distance / 100));

            // Создаем нужное количество частиц для нового соединения
            for (let j = 0; j < particlesCount + 1; j++) {
                // Выбираем цвет в зависимости от типов соединенных узлов
                let colorData;

                if (hasUrgent) {
                    // Срочные частицы (красноватые)
                    colorData = { color: '#EF4444', opacity: 0.8 };
                } else if (hasProcessed) {
                    // Обработанные частицы (зеленоватые)
                    colorData = { color: '#22C55E', opacity: 0.7 };
                } else {
                    // Обычные частицы
                    colorData = particleColors[Math.floor(Math.random() * particleColors.length)];
                }

                const size = config.particles.minSize + Math.random() * (config.particles.maxSize - config.particles.minSize);

                // Создаем частицу (начальное положение - в начале линии)
                const particle = createSvgElement('circle', {
                    cx: x1,
                    cy: y1,
                    r: 0, // Начальный размер 0 для анимации появления
                    fill: colorData.color,
                    opacity: 0, // Начальная прозрачность 0 для анимации появления
                    filter: `url(#particle-blur-${containerId})`,
                    'data-connection': key
                });

                dataParticles.appendChild(particle);

                // Анимируем появление частицы
                gsap.to(particle, {
                    r: size,
                    opacity: colorData.opacity,
                    duration: 0.6,
                    ease: "power2.out",
                    delay: Math.random() * 0.3
                });

                // Скорость движения (срочные быстрее)
                const speedMultiplier = hasUrgent ? 1.5 : (hasProcessed ? 1.2 : 1);
                const speed = (config.particles.speed.min + Math.random() * (config.particles.speed.max - config.particles.speed.min)) * speedMultiplier;
                const duration = distance / speed;

                // Задержка старта для распределения частиц по линии
                const startDelay = (duration / particlesCount) * j;

                // Создаем простую GSAP-анимацию для частицы
                const timeline = gsap.timeline({
                    repeat: -1,
                    delay: startDelay % duration // Циклическая задержка
                });

                // Сохраняем ссылку на элемент частицы
                timeline.data = { particleElement: particle };

                // Анимация движения от начала к концу и обратно
                timeline.to(particle, {
                    cx: x2,
                    cy: y2,
                    duration: duration,
                    ease: "none"
                });

                timeline.to(particle, {
                    cx: x1,
                    cy: y1,
                    duration: duration,
                    ease: "none"
                });

                particleTimelines.push(timeline);
            }
        }

        // 3. Обновляем позиции для сохраненных соединений
        for (const key in keptConnections) {
            const connection = keptConnections[key].element;
            const x1 = parseFloat(connection.getAttribute('x1'));
            const y1 = parseFloat(connection.getAttribute('y1'));
            const x2 = parseFloat(connection.getAttribute('x2'));
            const y2 = parseFloat(connection.getAttribute('y2'));

            // Получаем существующие частицы для этого соединения
            const particles = existingParticlesByConnection[key] || [];

            // Не создаем новые частицы, а просто обновляем анимацию существующих
            particles.forEach(particle => {
                // Находим таймлайн для этой частицы
                const timelineIndex = particleTimelines.findIndex(
                    tl => tl.data && tl.data.particleElement === particle
                );

                if (timelineIndex !== -1) {
                    // Останавливаем старую анимацию
                    particleTimelines[timelineIndex].kill();

                    // Расчет нового движения
                    const dx = x2 - x1;
                    const dy = y2 - y1;
                    const distance = Math.sqrt(dx * dx + dy * dy);

                    // Получаем индексы узлов
                    const nodeAIndex = parseInt(connection.getAttribute('data-node-a'));
                    const nodeBIndex = parseInt(connection.getAttribute('data-node-b'));

                    // Определяем, есть ли среди них срочные или обработанные
                    const hasUrgent = nodes[nodeAIndex].type === 'urgent' || nodes[nodeBIndex].type === 'urgent';
                    const hasProcessed = nodes[nodeAIndex].type === 'processed' || nodes[nodeBIndex].type === 'processed';

                    // Скорость движения
                    const speedMultiplier = hasUrgent ? 1.5 : (hasProcessed ? 1.2 : 1);
                    const speed = (config.particles.speed.min + Math.random() * (config.particles.speed.max - config.particles.speed.min)) * speedMultiplier;
                    const duration = distance / speed;

                    // Создаем новую анимацию для частицы
                    const newTimeline = gsap.timeline({
                        repeat: -1,
                        delay: Math.random() * duration // Случайная задержка
                    });

                    // Сохраняем ссылку на элемент частицы
                    newTimeline.data = { particleElement: particle };

                    // Текущее положение частицы
                    const currentX = parseFloat(particle.getAttribute('cx'));
                    const currentY = parseFloat(particle.getAttribute('cy'));

                    // Новая анимация с учетом текущего положения
                    // Сначала завершаем текущий путь
                    if (Math.abs(currentX - x1) < Math.abs(currentX - x2)) {
                        // Ближе к началу - движемся к концу
                        newTimeline.to(particle, {
                            cx: x2,
                            cy: y2,
                            duration: duration * (Math.abs(currentX - x2) / Math.abs(x2 - x1)),
                            ease: "none"
                        });

                        newTimeline.to(particle, {
                            cx: x1,
                            cy: y1,
                            duration: duration,
                            ease: "none"
                        });
                    } else {
                        // Ближе к концу - движемся к началу
                        newTimeline.to(particle, {
                            cx: x1,
                            cy: y1,
                            duration: duration * (Math.abs(currentX - x1) / Math.abs(x2 - x1)),
                            ease: "none"
                        });

                        newTimeline.to(particle, {
                            cx: x2,
                            cy: y2,
                            duration: duration,
                            ease: "none"
                        });
                    }

                    // Добавляем полный цикл после первоначального движения
                    newTimeline.to(particle, {
                        cx: x1,
                        cy: y1,
                        duration: duration,
                        ease: "none"
                    });

                    // Заменяем таймлайн
                    particleTimelines[timelineIndex] = newTimeline;
                }
            });
        }

        console.log(`Обновлено частиц: ${dataParticles.children.length}`);
    }

    // Создание движущихся частиц (первоначальное)
    function createParticles() {
        // Очищаем все предыдущие частицы и таймлайны
        while (dataParticles.firstChild) {
            dataParticles.removeChild(dataParticles.firstChild);
        }

        // Останавливаем все таймлайны
        particleTimelines.forEach(timeline => timeline.kill());
        particleTimelines.length = 0;

        // Если нет соединений, выходим
        if (connections.children.length === 0) {
            console.log("Соединения отсутствуют - частицы не созданы");
            return;
        }

        console.log(`Обнаружено ${connections.children.length} соединений - создаем частицы`);

        // Создадим новые соединения для передачи в updateParticles
        const newConnections = {};

        // Для каждого соединения подготовим данные
        for (let i = 0; i < connections.children.length; i++) {
            const connection = connections.children[i];
            const nodeAIndex = parseInt(connection.getAttribute('data-node-a'));
            const nodeBIndex = parseInt(connection.getAttribute('data-node-b'));
            const key = `${nodeAIndex}-${nodeBIndex}`;

            // Устанавливаем ключ соединения для последующего отслеживания
            connection.setAttribute('data-connection-key', key);

            const x1 = parseFloat(connection.getAttribute('x1'));
            const y1 = parseFloat(connection.getAttribute('y1'));
            const x2 = parseFloat(connection.getAttribute('x2'));
            const y2 = parseFloat(connection.getAttribute('y2'));
            const dx = x2 - x1;
            const dy = y2 - y1;
            const distance = Math.sqrt(dx * dx + dy * dy);

            newConnections[key] = {
                element: connection,
                distance: distance
            };
        }

        // Вызываем обновление частиц (все будут новыми)
        updateParticles(newConnections, {}, {});
    }

    // Обновление соединений (вызывается при изменении набора узлов)
    function updateConnections() {
        // Для большинства операций, кроме добавления нового узла, используем существующую логику
        createConnections();
    }

    // Планирование добавления нового узла
    function scheduleNodeAddition() {
        const delay = config.nodes.dynamics.addInterval.min + Math.random() * (config.nodes.dynamics.addInterval.max - config.nodes.dynamics.addInterval.min);
        addNodeTimer = setTimeout(() => {
            createNode(true); // создаем новый узел с анимацией
            scheduleNodeAddition(); // планируем следующее добавление
        }, delay);
    }

    // Планирование удаления узла
    function scheduleNodeRemoval() {
        const delay = config.nodes.dynamics.removeInterval.min + Math.random() * (config.nodes.dynamics.removeInterval.max - config.nodes.dynamics.removeInterval.min);
        removeNodeTimer = setTimeout(() => {
            removeRandomNode();
            scheduleNodeRemoval(); // планируем следующее удаление
        }, delay);
    }

    // Планирование обработки узла
    function scheduleNodeProcessing() {
        const delay = config.nodes.dynamics.processInterval.min + Math.random() * (config.nodes.dynamics.processInterval.max - config.nodes.dynamics.processInterval.min);
        processNodeTimer = setTimeout(() => {
            processRandomNode();
            scheduleNodeProcessing(); // планируем следующую обработку
        }, delay);
    }

    // Обновление темы цветов при изменении theme mode
    function updateTheme() {
        const newDarkMode = document.documentElement.classList.contains('dark');

        if (isDarkMode !== newDarkMode) {
            console.log(`Изменение темы: ${newDarkMode ? 'тёмная' : 'светлая'}`);
            // Обновляем состояние
            isDarkMode = newDarkMode;

            // Обновляем цвета в зависимости от темы
            nodeColors = isDarkMode ? config.nodes.colors.dark : config.nodes.colors.light;
            particleColors = isDarkMode ? config.particles.colors.dark : config.particles.colors.light;

            // Обновляем прозрачность соединений
            config.connections.opacity = isDarkMode ? 0.25 : 0.2;

            // Обновляем внешний вид сетки
            for (let i = 0; i < gridLines.children.length; i++) {
                const line = gridLines.children[i];
                line.setAttribute('stroke', isDarkMode ? 'white' : 'black');
                line.setAttribute('opacity', config.grid.opacity);
            }

            // Обновляем соединения
            for (let i = 0; i < connections.children.length; i++) {
                const connection = connections.children[i];
                // Прозрачность линии также обратно пропорциональна расстоянию
                const x1 = parseFloat(connection.getAttribute('x1'));
                const y1 = parseFloat(connection.getAttribute('y1'));
                const x2 = parseFloat(connection.getAttribute('x2'));
                const y2 = parseFloat(connection.getAttribute('y2'));
                const dx = x2 - x1;
                const dy = y2 - y1;
                const distance = Math.sqrt(dx * dx + dy * dy);
                const lineOpacity = config.connections.opacity * (1 - distance / config.connections.maxDistance);
                connection.setAttribute('opacity', lineOpacity);
            }

            console.log('Тема обновлена, перезагружаем частицы');
            // Пересоздаем частицы с новыми цветами
            createParticles();
        }
    }

    // Создание интерактивных узлов при движении курсора
    function createCursorNode(x, y, isVisible = true) {
        // Проверяем, не превышено ли максимальное количество узлов, созданных курсором
        if (cursorNodes.length >= config.interactive.maxCursorNodes) {
            // Если превышено, удаляем самый старый интерактивный узел
            const oldestNode = cursorNodes.shift();
            if (oldestNode.element && oldestNode.element.parentNode) {
                fadeOutAndRemoveNode(oldestNode);
            }
        }

        // Добавляем случайное смещение вокруг курсора
        const angle = Math.random() * Math.PI * 2;
        const distance = Math.random() * config.interactive.cursorNodeRadius;
        const nodeX = x + Math.cos(angle) * distance;
        const nodeY = y + Math.sin(angle) * distance;

        // Случайный размер узла
        const size = config.interactive.cursorNodeSize.min +
                     Math.random() * (config.interactive.cursorNodeSize.max - config.interactive.cursorNodeSize.min);

        // Случайный тип узла (для курсора чаще создаем "обработанные" узлы)
        const nodeTypes = [
            { name: 'regular', probability: 0.4 },
            { name: 'urgent', probability: 0.2 },
            { name: 'processed', probability: 0.4 }
        ];
        const nodeType = getRandomWeightedItem(nodeTypes, 'probability');

        // Определяем заливку и фильтр узла
        let fill, filter;
        switch(nodeType.name) {
            case 'urgent':
                fill = `url(#urgent-gradient-${containerId})`;
                filter = `url(#strong-glow-${containerId})`;
                break;
            case 'processed':
                fill = `url(#processed-gradient-${containerId})`;
                filter = `url(#glow-${containerId})`;
                break;
            default: // regular
                const colorData = nodeColors[Math.floor(Math.random() * nodeColors.length)];
                fill = colorData.color;
                filter = `url(#glow-${containerId})`;
        }

        // Создаем узел
        const node = createSvgElement('circle', {
            cx: nodeX,
            cy: nodeY,
            r: 0, // Начинаем с нулевого размера для анимации появления
            fill: fill,
            opacity: 0, // Начинаем с нулевой прозрачности
            filter: filter,
            'data-type': nodeType.name,
            'data-cursor-node': 'true' // Метка, что это узел, созданный курсором
        });

        dataNodes.appendChild(node);

        // Анимируем появление узла
        gsap.to(node, {
            r: size,
            opacity: nodeType.name === 'regular' ? 0.8 : 0.9,
            duration: 0.6,
            ease: "power2.out"
        });

        // Настраиваем пульсацию узла
        const timeline = gsap.timeline({
            repeat: -1,
            yoyo: true
        });

        // В зависимости от типа узла настраиваем анимацию
        switch(nodeType.name) {
            case 'urgent':
                timeline.to(node, {
                    r: size * 1.2,
                    opacity: 0.95,
                    duration: 1 + Math.random(),
                    ease: "sine.inOut"
                });
                break;
            case 'processed':
                timeline.to(node, {
                    r: size * 1.05,
                    opacity: 0.85,
                    duration: 2 + Math.random() * 2,
                    ease: "sine.inOut"
                });
                break;
            default: // regular
                timeline.to(node, {
                    r: size * (0.8 + Math.random() * 0.4),
                    opacity: 0.75 + Math.random() * 0.2,
                    duration: 2 + Math.random() * 3,
                    ease: "sine.inOut"
                });
        }

        // Создаем информацию об узле
        const nodeObj = {
            element: node,
            x: nodeX,
            y: nodeY,
            size: size,
            type: nodeType.name,
            createdAt: Date.now(),
            timeline: timeline,
            // Время жизни узла в мс (случайное из настроек)
            lifetime: (config.interactive.nodeLifetime.min +
                      Math.random() * (config.interactive.nodeLifetime.max - config.interactive.nodeLifetime.min)) * 1000
        };

        // Добавляем узел в массив курсорных узлов
        cursorNodes.push(nodeObj);

        // Если узел видимый, обновляем соединения
        if (isVisible) {
            // Асинхронно обновляем соединения, чтобы не блокировать основной поток
            requestAnimationFrame(() => {
                createCursorConnections(nodeObj);
            });

            // Запускаем таймер для автоматического удаления узла
            setTimeout(() => {
                fadeOutAndRemoveNode(nodeObj);
            }, nodeObj.lifetime);
        }

        return nodeObj;
    }

    // Плавное исчезновение и удаление узла
    function fadeOutAndRemoveNode(nodeObj) {
        // Проверяем, существует ли еще узел
        if (!nodeObj || !nodeObj.element || !nodeObj.element.parentNode) return;

        // Находим индекс узла в массиве
        const nodeIndex = cursorNodes.indexOf(nodeObj);
        if (nodeIndex !== -1) {
            cursorNodes.splice(nodeIndex, 1);
        }

        // Останавливаем таймлайн
        if (nodeObj.timeline) {
            nodeObj.timeline.kill();
        }

        // Анимируем исчезновение
        gsap.to(nodeObj.element, {
            r: 0,
            opacity: 0,
            duration: 0.6,
            ease: "power2.in",
            onComplete: () => {
                // Удаляем элемент из DOM
                if (nodeObj.element.parentNode === dataNodes) {
                    dataNodes.removeChild(nodeObj.element);
                }

                // Обновляем соединения после удаления узла
                requestAnimationFrame(() => {
                    updateConnections();
                });
            }
        });
    }

    // Создание соединений для узла, созданного курсором
    function createCursorConnections(cursorNode) {
        // Создаем соединения между курсорным узлом и обычными узлами
        for (let i = 0; i < nodes.length; i++) {
            const otherNode = nodes[i];

            // Проверка валидности узла
            if (!otherNode || !otherNode.x) continue;

            // Вычисляем расстояние между узлами
            const dx = cursorNode.x - otherNode.x;
            const dy = cursorNode.y - otherNode.y;
            const distance = Math.sqrt(dx * dx + dy * dy);

            // Если узлы достаточно близко, создаем соединение
            if (distance < config.connections.maxDistance) {
                // Определяем, есть ли среди них срочные или обработанные
                const hasUrgent = cursorNode.type === 'urgent' || otherNode.type === 'urgent';
                const hasProcessed = cursorNode.type === 'processed' || otherNode.type === 'processed';

                // Толщина и прозрачность линии обратно пропорциональны расстоянию
                const thickness = config.connections.thickness.max -
                                (distance / config.connections.maxDistance) *
                                (config.connections.thickness.max - config.connections.thickness.min);
                const lineOpacity = config.connections.opacity * (1 - distance / config.connections.maxDistance);

                // Создаем соединение
                const line = createSvgElement('line', {
                    x1: cursorNode.x,
                    y1: cursorNode.y,
                    x2: otherNode.x,
                    y2: otherNode.y,
                    stroke: `url(#line-gradient-${containerId})`,
                    'stroke-width': thickness,
                    opacity: 0, // Начинаем с нулевой прозрачности
                    'data-cursor-connection': 'true' // Метка, что это соединение от курсора
                });

                connections.appendChild(line);

                // Анимируем появление соединения
                gsap.to(line, {
                    opacity: lineOpacity,
                    duration: 0.3,
                    ease: "power2.out"
                });

                // Создаем движущиеся частицы для соединения
                const particlesCount = Math.max(1, Math.floor(distance / 150)); // Меньше частиц для соединений курсора

                for (let j = 0; j < particlesCount; j++) {
                    // Выбираем цвет в зависимости от типов соединенных узлов
                    let colorData;

                    if (hasUrgent) {
                        colorData = { color: '#EF4444', opacity: 0.8 };
                    } else if (hasProcessed) {
                        colorData = { color: '#22C55E', opacity: 0.7 };
                    } else {
                        colorData = particleColors[Math.floor(Math.random() * particleColors.length)];
                    }

                    const size = config.particles.minSize + Math.random() * (config.particles.maxSize - config.particles.minSize);

                    // Создаем частицу
                    const particle = createSvgElement('circle', {
                        cx: cursorNode.x,
                        cy: cursorNode.y,
                        r: 0, // Начальный размер 0 для анимации появления
                        fill: colorData.color,
                        opacity: 0, // Начальная прозрачность 0 для анимации появления
                        filter: `url(#particle-blur-${containerId})`,
                        'data-cursor-particle': 'true' // Метка, что это частица от курсора
                    });

                    dataParticles.appendChild(particle);

                    // Анимируем появление частицы
                    gsap.to(particle, {
                        r: size,
                        opacity: colorData.opacity,
                        duration: 0.4,
                        ease: "power2.out",
                        delay: Math.random() * 0.2
                    });

                    // Скорость движения
                    const speedMultiplier = hasUrgent ? 1.5 : (hasProcessed ? 1.2 : 1);
                    const speed = (config.particles.speed.min + Math.random() * (config.particles.speed.max - config.particles.speed.min)) * speedMultiplier;
                    const duration = distance / speed;

                    // Создаем анимацию движения частицы
                    const timeline = gsap.timeline({
                        repeat: -1,
                        delay: (duration / particlesCount) * j % duration // Циклическая задержка
                    });

                    // Сохраняем связь с курсорным узлом
                    timeline.data = {
                        particleElement: particle,
                        cursorNodeElement: cursorNode.element
                    };

                    // Анимация движения
                    timeline.to(particle, {
                        cx: otherNode.x,
                        cy: otherNode.y,
                        duration: duration,
                        ease: "none"
                    });

                    timeline.to(particle, {
                        cx: cursorNode.x,
                        cy: cursorNode.y,
                        duration: duration,
                        ease: "none"
                    });

                    particleTimelines.push(timeline);

                    // Когда курсорный узел удаляется, нужно также удалить его частицы
                    // Это будет обработано в функции очистки
                }
            }
        }

        // Также создаем соединения между курсорными узлами
        for (let i = 0; i < cursorNodes.length; i++) {
            const otherCursorNode = cursorNodes[i];

            // Пропускаем сам себя
            if (otherCursorNode === cursorNode) continue;

            // Проверка валидности узла
            if (!otherCursorNode || !otherCursorNode.x) continue;

            // Вычисляем расстояние между узлами
            const dx = cursorNode.x - otherCursorNode.x;
            const dy = cursorNode.y - otherCursorNode.y;
            const distance = Math.sqrt(dx * dx + dy * dy);

            // Если узлы достаточно близко, создаем соединение
            if (distance < config.connections.maxDistance) {
                // Толщина и прозрачность линии обратно пропорциональны расстоянию
                const thickness = config.connections.thickness.max -
                                (distance / config.connections.maxDistance) *
                                (config.connections.thickness.max - config.connections.thickness.min);
                const lineOpacity = config.connections.opacity * (1 - distance / config.connections.maxDistance);

                // Создаем соединение
                const line = createSvgElement('line', {
                    x1: cursorNode.x,
                    y1: cursorNode.y,
                    x2: otherCursorNode.x,
                    y2: otherCursorNode.y,
                    stroke: `url(#line-gradient-${containerId})`,
                    'stroke-width': thickness,
                    opacity: 0, // Начинаем с нулевой прозрачности
                    'data-cursor-connection': 'true' // Метка, что это соединение от курсора
                });

                connections.appendChild(line);

                // Анимируем появление соединения
                gsap.to(line, {
                    opacity: lineOpacity,
                    duration: 0.3,
                    ease: "power2.out"
                });
            }
        }
    }

    // Очистка соединений и частиц от удаленных курсорных узлов
    function cleanupCursorElements() {
        // Удаляем соединения от курсора
        const cursorConnections = connections.querySelectorAll('[data-cursor-connection="true"]');
        cursorConnections.forEach(connection => {
            gsap.to(connection, {
                opacity: 0,
                duration: 0.3,
                ease: "power2.in",
                onComplete: () => {
                    if (connection.parentNode === connections) {
                        connections.removeChild(connection);
                    }
                }
            });
        });

        // Удаляем частицы от курсора
        const cursorParticles = dataParticles.querySelectorAll('[data-cursor-particle="true"]');
        cursorParticles.forEach(particle => {
            gsap.to(particle, {
                opacity: 0,
                r: 0,
                duration: 0.3,
                ease: "power2.in",
                onComplete: () => {
                    if (particle.parentNode === dataParticles) {
                        dataParticles.removeChild(particle);
                    }
                }
            });
        });

        // Останавливаем таймлайны частиц от курсора
        for (let i = particleTimelines.length - 1; i >= 0; i--) {
            const timeline = particleTimelines[i];
            if (timeline.data && timeline.data.cursorNodeElement) {
                timeline.kill();
                particleTimelines.splice(i, 1);
            }
        }
    }

    // Обработчик движения мыши для создания интерактивных узлов
    function handleMouseMove(event) {
        if (!config.interactive.enabled) return;

        // Применяем троттлинг для уменьшения нагрузки
        const now = Date.now();
        if (lastCursorMove && now - lastCursorMove < config.interactive.throttleInterval) {
            // Даже при троттлинге обновляем позицию курсора для искажения сетки
            updateCursorPosition(event);
            return;
        }
        lastCursorMove = now;

        // Обновляем позицию курсора
        updateCursorPosition(event);

        // Создаем новый узел рядом с курсором
        createCursorNode(cursorX, cursorY);

        // Применяем искажение сетки
        if (config.interactive.gridDistortion.enabled) {
            distortGrid(cursorX, cursorY);
        }
    }

    // Обновление позиции курсора
    function updateCursorPosition(event) {
        const svgRect = svg.getBoundingClientRect();
        cursorX = (event.clientX - svgRect.left) / svgRect.width * viewBoxWidth;
        cursorY = (event.clientY - svgRect.top) / svgRect.height * viewBoxHeight;
    }

    // Обработчик входа мыши в область SVG
    function handleMouseEnter(event) {
        if (!config.interactive.enabled) return;

        isMouseInside = true;

        // Обновляем курсор для начального положения
        updateCursorPosition(event);

        // Создаем начальные узлы при входе курсора
        for (let i = 0; i < Math.min(2, config.interactive.maxCursorNodes); i++) {
            setTimeout(() => {
                if (isMouseInside) { // Проверяем, что курсор все еще внутри
                    createCursorNode(cursorX, cursorY);
                }
            }, i * 300); // Создаем с небольшой задержкой для эффекта появления
        }

        // Активируем искажение сетки
        if (config.interactive.gridDistortion.enabled) {
            gridDistortionEnabled = true;
            distortGrid(cursorX, cursorY);
        }
    }

    // Обработчик выхода мыши из области SVG
    function handleMouseLeave(event) {
        if (!config.interactive.enabled) return;

        isMouseInside = false;

        // Очищаем все элементы, связанные с курсором
        cursorNodes.forEach(node => {
            fadeOutAndRemoveNode(node);
        });
        cursorNodes.length = 0;

        // Очищаем соединения и частицы
        cleanupCursorElements();

        // Сбрасываем искажение сетки
        if (config.interactive.gridDistortion.enabled) {
            gridDistortionEnabled = false;
            resetGrid();
        }
    }

    // Инициализация анимации
    function initAnimation() {
        createGrid();
        createNodes(config.nodes.initialCount);
        createConnections(); // Теперь createConnections вызывает updateParticles

        // Дополнительный явный вызов создания частиц уже не нужен
        // Но оставим проверку для подстраховки
        setTimeout(() => {
            if (dataParticles.children.length === 0) {
                console.log('Экстренное создание частиц - они не были созданы');
                createParticles();
            }
        }, 500);

        // Запускаем динамику узлов
        scheduleNodeAddition();
        scheduleNodeRemoval();
        scheduleNodeProcessing();

        // Запускаем волны активности
        scheduleNextWave();

        // Отслеживаем изменение темы
        const observer = new MutationObserver((mutations) => {
            mutations.forEach(mutation => {
                if (mutation.attributeName === 'class') {
                    updateTheme();
                }
            });
        });

        observer.observe(document.documentElement, { attributes: true });

        // Добавляем интерактивные обработчики событий курсора
        if (config.interactive.enabled) {
            // Удаляем класс pointer-events-none с контейнера
            container.classList.remove('pointer-events-none');

            // Добавляем обработчики событий
            svg.addEventListener('mousemove', handleMouseMove);
            svg.addEventListener('mouseenter', handleMouseEnter);
            svg.addEventListener('mouseleave', handleMouseLeave);

            console.log('Интерактивность включена: обработчики событий курсора добавлены');
        }
    }

    // Очистка при уничтожении компонента
    function cleanupAnimation() {
        // Очищаем все таймеры
        clearTimeout(addNodeTimer);
        clearTimeout(removeNodeTimer);
        clearTimeout(processNodeTimer);
        clearTimeout(waveTimer);

        // Останавливаем все анимации
        gsap.killTweensOf(dataNodes.children);
        gsap.killTweensOf(connections.children);
        gsap.killTweensOf(dataParticles.children);
        gsap.killTweensOf(waveEffects.children);
        gsap.killTweensOf(pulseEffects.children);

        // Останавливаем все таймлайны
        nodes.forEach(node => {
            if (node.timeline) {
                node.timeline.kill();
            }
        });

        particleTimelines.forEach(timeline => {
            timeline.kill();
        });

        waveTimelines.forEach(timeline => {
            timeline.kill();
        });

        // Удаляем обработчики событий курсора
        if (config.interactive.enabled) {
            svg.removeEventListener('mousemove', handleMouseMove);
            svg.removeEventListener('mouseenter', handleMouseEnter);
            svg.removeEventListener('mouseleave', handleMouseLeave);
        }
    }

    // Запускаем инициализацию
    initAnimation();

    // Обработчик изменения размера окна
    window.addEventListener('resize', () => {
        // При изменении размера окна можно пересоздать анимацию или настроить масштабирование
        // Для SVG с viewBox это происходит автоматически
    });

    // Очистка ресурсов при уничтожении компонента
    return () => {
        cleanupAnimation();
    };
});
</script>
