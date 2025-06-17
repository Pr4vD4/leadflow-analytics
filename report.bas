Option Explicit

' Константы для параметров форматирования
Const FONT_NAME = "Times New Roman"
Const MAIN_FONT_SIZE = 14
Const HEADER_FONT_SIZE = 16
Const TITLE_FONT_SIZE = 18
Const PARAGRAPH_SPACING = 1.5

' Основная процедура создания отчета
Sub CreateReport()
    ' Объявление переменных
    Dim objWord As Object
    Dim objDoc As Object
    Dim objSelection As Object

    ' Создание экземпляра Word и нового документа
    Set objWord = CreateObject("Word.Application")
    objWord.Visible = True
    Set objDoc = objWord.Documents.Add
    Set objSelection = objWord.Selection

    ' Настройка параметров страницы
    With objDoc.PageSetup
        .TopMargin = objWord.CentimetersToPoints(2)
        .BottomMargin = objWord.CentimetersToPoints(2)
        .LeftMargin = objWord.CentimetersToPoints(3)
        .RightMargin = objWord.CentimetersToPoints(1.5)
    End With

    ' Применение основного форматирования ко всему документу
    With objSelection
        .Font.Name = FONT_NAME
        .Font.Size = MAIN_FONT_SIZE
        .ParagraphFormat.LineSpacingRule = 1 ' wdLineSpace1pt5
        .ParagraphFormat.LineSpacing = PARAGRAPH_SPACING * 12
        .ParagraphFormat.SpaceAfter = 0
        .ParagraphFormat.SpaceBefore = 0
    End With

    ' Создание титульного листа
    CreateTitlePage objDoc, objSelection

    ' Создание структуры отчета
    InsertPageBreak objSelection
    CreateTableOfContents objDoc, objSelection

    ' Создание основных разделов отчета
    InsertPageBreak objSelection
    CreateIntroduction objDoc, objSelection

    InsertPageBreak objSelection
    CreateEnterpriseAnalysis objDoc, objSelection

    InsertPageBreak objSelection
    CreateTaskImplementation objDoc, objSelection

    InsertPageBreak objSelection
    CreateConclusion objDoc, objSelection

    InsertPageBreak objSelection
    CreateReferences objDoc, objSelection

    ' Обновление оглавления
    objDoc.TablesOfContents(1).Update

    ' Установка курсора в начало документа
    objSelection.HomeKey 6 ' wdStory

    MsgBox "Отчет успешно создан!", vbInformation
End Sub

' Процедура создания титульного листа
Sub CreateTitlePage(objDoc As Object, objSelection As Object)
    ' Настройка форматирования для титульного листа
    With objSelection
        .ParagraphFormat.Alignment = 1 ' wdAlignParagraphCenter

        ' Добавление верхнего текста
        .Font.Size = MAIN_FONT_SIZE
        .TypeText "МИНИСТЕРСТВО НАУКИ И ВЫСШЕГО ОБРАЗОВАНИЯ"
        .TypeParagraph
        .TypeText "РОССИЙСКОЙ ФЕДЕРАЦИИ"
        .TypeParagraph
        .TypeParagraph
        .TypeText "НАЗВАНИЕ ОБРАЗОВАТЕЛЬНОГО УЧРЕЖДЕНИЯ"
        .TypeParagraph
        .TypeParagraph
        .TypeText "Факультет / Кафедра"
        .TypeParagraph
        .TypeParagraph
        .TypeParagraph

        ' Название отчета
        .Font.Size = TITLE_FONT_SIZE
        .Font.Bold = True
        .TypeText "ОТЧЕТ"
        .TypeParagraph
        .TypeText "О ПРОХОЖДЕНИИ ПРОИЗВОДСТВЕННОЙ ПРАКТИКИ"
        .TypeParagraph
        .TypeParagraph
        .TypeText "Проект: «LeadFlow Analytics»"
        .Font.Bold = False
        .TypeParagraph
        .TypeParagraph
        .TypeParagraph
        .TypeParagraph

        ' Информация об авторе и руководителе
        .Font.Size = MAIN_FONT_SIZE
        .ParagraphFormat.Alignment = 3 ' wdAlignParagraphRight
        .TypeText "Выполнил: Студент группы ХХХ"
        .TypeParagraph
        .TypeText "ФИО студента"
        .TypeParagraph
        .TypeParagraph
        .TypeText "Руководитель практики:"
        .TypeParagraph
        .TypeText "ФИО руководителя"
        .TypeParagraph
        .TypeParagraph
        .TypeParagraph
        .TypeParagraph

        ' Место и год
        .ParagraphFormat.Alignment = 1 ' wdAlignParagraphCenter
        .TypeText "Город — 20ХХ"
    End With
End Sub

' Процедура создания оглавления
Sub CreateTableOfContents(objDoc As Object, objSelection As Object)
    ' Заголовок оглавления
    With objSelection
        .Font.Size = HEADER_FONT_SIZE
        .Font.Bold = True
        .ParagraphFormat.Alignment = 1 ' wdAlignParagraphCenter
        .TypeText "СОДЕРЖАНИЕ"
        .TypeParagraph
        .TypeParagraph
        .Font.Bold = False
    End With

    ' Вставка оглавления
    objDoc.TablesOfContents.Add objSelection.Range, True, 1, 3
End Sub

' Процедура создания введения
Sub CreateIntroduction(objDoc As Object, objSelection As Object)
    ' Заголовок раздела
    FormatSectionHeader objSelection, "ВВЕДЕНИЕ"

    ' Содержимое введения
    With objSelection
        .ParagraphFormat.Alignment = 0 ' wdAlignParagraphLeft

        ' Абзац 1
        .TypeText "Данный отчет представляет результаты производственной практики, проведенной в компании, занимающейся разработкой программного обеспечения. Целью практики было участие в разработке информационной системы «LeadFlow Analytics» — сервиса для сбора, анализа и автоматизации обработки клиентских заявок с интеграцией в CRM-системы и использованием искусственного интеллекта."
        .TypeParagraph
        .TypeParagraph

        ' Дополнительное описание проекта
        .TypeText "Проект «LeadFlow Analytics» нацелен на решение одной из ключевых проблем современного бизнеса — эффективного управления входящими клиентскими заявками. Разрабатываемая система представляет собой комплексное решение, которое позволяет автоматизировать сбор заявок из различных источников, проводить их аналитическую обработку с применением технологий искусственного интеллекта и предоставлять инструменты для управления взаимоотношениями с клиентами. Проект находится на стадии создания MVP (минимально жизнеспособного продукта), что позволяет сосредоточиться на разработке ключевого функционала и быстром выходе на рынок."
        .TypeParagraph
        .TypeParagraph

        ' Абзац 2
        .TypeText "В ходе практики решались следующие задачи:"
        .TypeParagraph
        .TypeText Chr(149) & " Анализ требований к проекту LeadFlow Analytics;"
        .TypeParagraph
        .TypeText Chr(149) & " Изучение архитектуры и технологий, используемых при разработке системы;"
        .TypeParagraph
        .TypeText Chr(149) & " Участие в разработке и тестировании отдельных модулей системы;"
        .TypeParagraph
        .TypeText Chr(149) & " Изучение процессов взаимодействия компонентов системы между собой;"
        .TypeParagraph
        .TypeText Chr(149) & " Анализ внедрения технологий искусственного интеллекта в бизнес-процессы;"
        .TypeParagraph
        .TypeText Chr(149) & " Исследование методов организации мультитенантной архитектуры приложения."
        .TypeParagraph
        .TypeParagraph

        ' Абзац 3
        .TypeText "Практика проходила в рамках проекта по разработке MVP системы LeadFlow Analytics. В процессе практики были закреплены теоретические знания, полученные в ходе обучения, а также приобретены практические навыки работы с современными технологиями разработки веб-приложений, такими как PHP, Laravel, MySQL, JavaScript, и интеграцией искусственного интеллекта."
        .TypeParagraph

        ' Добавляем новый абзац о значимости проекта
        .TypeParagraph
        .TypeText "Проект имеет высокую практическую значимость, поскольку направлен на повышение эффективности работы с клиентскими заявками за счет автоматизации рутинных операций и предоставления инструментов для анализа данных. Уникальность решения заключается в интеграции технологий искусственного интеллекта для обработки естественного языка, что позволяет автоматически категоризировать заявки, оценивать их релевантность и генерировать краткие резюме содержания. Это значительно сокращает время обработки заявок и повышает качество обслуживания клиентов."
        .TypeParagraph
    End With
End Sub

' Процедура создания раздела анализа деятельности предприятия
Sub CreateEnterpriseAnalysis(objDoc As Object, objSelection As Object)
    ' Заголовок раздела
    FormatSectionHeader objSelection, "РАЗДЕЛ 1. АНАЛИЗ ДЕЯТЕЛЬНОСТИ ПРЕДПРИЯТИЯ"

    ' 1.1 Общая информация о предприятии
    FormatSubsectionHeader objSelection, "1.1 Общая информация о предприятии"

    With objSelection
        .TypeText "Компания специализируется на разработке программного обеспечения для автоматизации бизнес-процессов и является разработчиком проекта LeadFlow Analytics. Основные направления деятельности компании включают:"
        .TypeParagraph
        .TypeText Chr(149) & " Разработку CRM-систем и сервисов для управления взаимоотношениями с клиентами;"
        .TypeParagraph
        .TypeText Chr(149) & " Создание и внедрение инструментов бизнес-аналитики;"
        .TypeParagraph
        .TypeText Chr(149) & " Интеграцию решений с искусственным интеллектом для автоматизации рутинных операций;"
        .TypeParagraph
        .TypeText Chr(149) & " Консультационные услуги в области цифровой трансформации бизнеса."
        .TypeParagraph
        .TypeParagraph
    End With

    ' 1.2 Организационная структура компании
    FormatSubsectionHeader objSelection, "1.2 Организационная структура компании"

    With objSelection
        .TypeText "Компания имеет проектно-ориентированную организационную структуру, включающую следующие основные отделы:"
        .TypeParagraph
        .TypeText Chr(149) & " Отдел разработки — занимается непосредственно созданием программных продуктов;"
        .TypeParagraph
        .TypeText Chr(149) & " Отдел тестирования — обеспечивает качество выпускаемого ПО;"
        .TypeParagraph
        .TypeText Chr(149) & " Отдел аналитики — отвечает за сбор и анализ требований, проектирование систем;"
        .TypeParagraph
        .TypeText Chr(149) & " Отдел внедрения — работает с клиентами по вопросам настройки и интеграции решений;"
        .TypeParagraph
        .TypeText Chr(149) & " Отдел поддержки — обеспечивает техническую поддержку пользователей."
        .TypeParagraph
        .TypeParagraph
    End With

    ' 1.3 Анализ технологического стека компании
    FormatSubsectionHeader objSelection, "1.3 Анализ технологического стека компании"

    With objSelection
        .TypeText "Компания использует современный технологический стек для разработки программных продуктов:"
        .TypeParagraph
        .TypeText Chr(149) & " Серверная часть: PHP 8+, фреймворк Laravel 10, MySQL/PostgreSQL;"
        .TypeParagraph
        .TypeText Chr(149) & " Клиентская часть: Laravel Blade, Livewire, JavaScript, Chart.js/ApexCharts;"
        .TypeParagraph
        .TypeText Chr(149) & " Инфраструктура: Docker, Git, CI/CD, облачные платформы;"
        .TypeParagraph
        .TypeText Chr(149) & " Интеграции с AI: OpenAI API, Ollama для локальных моделей."
        .TypeParagraph
        .TypeParagraph
    End With

    ' 1.4 Основные бизнес-процессы
    FormatSubsectionHeader objSelection, "1.4 Основные бизнес-процессы"

    With objSelection
        .TypeText "В компании действуют следующие основные бизнес-процессы:"
        .TypeParagraph
        .TypeText Chr(149) & " Процесс разработки ПО — от сбора требований до релиза продукта;"
        .TypeParagraph
        .TypeText Chr(149) & " Процесс взаимодействия с клиентами — получение и обработка запросов;"
        .TypeParagraph
        .TypeText Chr(149) & " Процесс внедрения решений — настройка и интеграция систем у клиентов;"
        .TypeParagraph
        .TypeText Chr(149) & " Процесс технической поддержки — обеспечение бесперебойной работы ПО."
        .TypeParagraph
        .TypeParagraph
    End With

    ' 1.5 Краткая характеристика проекта LeadFlow Analytics
    FormatSubsectionHeader objSelection, "1.5 Краткая характеристика проекта LeadFlow Analytics"

    With objSelection
        .TypeText "LeadFlow Analytics — это комплексный сервис, нацеленный на оптимизацию процесса обработки клиентских заявок с использованием искусственного интеллекта. Проект находится на стадии разработки MVP и включает следующие ключевые компоненты:"
        .TypeParagraph
        .TypeText Chr(149) & " Интеграционный модуль для приема заявок с различных источников;"
        .TypeParagraph
        .TypeText Chr(149) & " Аналитическая платформа для визуализации и анализа данных;"
        .TypeParagraph
        .TypeText Chr(149) & " Мини-CRM для управления заявками и коммуникациями с клиентами;"
        .TypeParagraph
        .TypeText Chr(149) & " Модуль ИИ-аналитики для автоматической обработки и анализа заявок;"
        .TypeParagraph
        .TypeText Chr(149) & " Интеграция с внешними CRM-системами, в частности с Bitrix24."
        .TypeParagraph
        .TypeParagraph

        ' Более подробное описание интеграционного модуля
        .TypeText "Интеграционный модуль представляет собой REST API для приема заявок с сайтов клиентов. API поддерживает формат данных JSON с обязательными полями source (источник заявки) и контактными данными (email или phone). Система обеспечивает безопасность через API-ключи, уникальные для каждого клиента. Модуль реализует принцип мультитенантности, изолируя данные разных компаний через поле company_id в базе данных."
        .TypeParagraph
        .TypeParagraph

        ' Более подробное описание аналитической платформы
        .TypeText "Аналитическая платформа включает дашборд с визуализацией ключевых метрик: общее количество заявок за период, конверсия (доля завершенных заявок), распределение по источникам и средняя оценка релевантности. Система предоставляет инструменты фильтрации и группировки данных по различным параметрам (дата, статус, теги) и поддерживает экспорт в CSV-формат для дальнейшей обработки. Для визуализации используются интерактивные графики на основе библиотеки Chart.js, а динамическое обновление данных реализовано с помощью Laravel Livewire."
        .TypeParagraph
        .TypeParagraph

        ' Более подробное описание мини-CRM
        .TypeText "Мини-CRM обеспечивает управление заявками через систему статусов («Новая», «В работе», «Завершена», «Архив»), поддерживает теги для категоризации, загрузку файлов (с ограничением по размеру и типу) и отправку уведомлений в Telegram при изменении статуса заявки. Система реализует контроль доступа, позволяя менеджерам видеть заявки только своей компании. Интерфейс CRM построен с использованием Livewire-компонентов, что обеспечивает динамическое взаимодействие без перезагрузки страницы."
        .TypeParagraph
        .TypeParagraph

        ' Более подробное описание модуля ИИ-аналитики
        .TypeText "Модуль ИИ-аналитики является инновационной частью системы и предоставляет функции автоматической категоризации заявок, суммаризации текста и оценки релевантности. Для обработки естественного языка используются технологии Ollama API и локально развернутые модели машинного обучения. Обработка заявок происходит асинхронно с использованием Laravel Queues, что позволяет не блокировать основной поток работы приложения. Результаты аналитики сохраняются в базе данных и доступны через интерфейс CRM и дашборд."
        .TypeParagraph
        .TypeParagraph

        ' Более подробное описание интеграции с внешними CRM
        .TypeText "Интеграция с Bitrix24 реализована через REST API и позволяет автоматически передавать данные о заявках во внешнюю CRM-систему. Система поддерживает двунаправленную синхронизацию: при создании или изменении заявки в LeadFlow Analytics данные отправляются в Bitrix24, а при обновлении статуса в Bitrix24 изменения отражаются в LeadFlow Analytics. Это обеспечивает единое информационное пространство для всех сотрудников компании независимо от используемого инструмента."
        .TypeParagraph
        .TypeParagraph
    End With
End Sub

' Процедура создания раздела реализации заданий
Sub CreateTaskImplementation(objDoc As Object, objSelection As Object)
    ' Заголовок раздела
    FormatSectionHeader objSelection, "РАЗДЕЛ 2. РЕАЛИЗАЦИЯ ЗАДАНИЙ, ПОЛУЧЕННЫХ ОТ РУКОВОДИТЕЛЯ ПРЕДПРИЯТИЯ"

    ' 2.1 Изучение архитектуры проекта LeadFlow Analytics
    FormatSubsectionHeader objSelection, "2.1 Изучение архитектуры проекта LeadFlow Analytics"

    With objSelection
        .TypeText "В ходе практики было проведено изучение архитектуры проекта LeadFlow Analytics. Система разработана на базе PHP-фреймворка Laravel 10 и использует архитектурный паттерн MVC (Model-View-Controller)."
        .TypeParagraph
        .TypeParagraph

        .TypeText "Основные компоненты архитектуры системы включают:"
        .TypeParagraph
        .TypeText Chr(149) & " Модели данных — представляют сущности системы и их взаимосвязи;"
        .TypeParagraph
        .TypeText Chr(149) & " Контроллеры — обрабатывают запросы пользователей и управляют бизнес-логикой;"
        .TypeParagraph
        .TypeText Chr(149) & " Представления — отвечают за визуализацию данных для конечных пользователей;"
        .TypeParagraph
        .TypeText Chr(149) & " Сервисы — содержат бизнес-логику и обеспечивают взаимодействие с внешними API;"
        .TypeParagraph
        .TypeText Chr(149) & " Репозитории — инкапсулируют логику работы с базой данных;"
        .TypeParagraph
        .TypeText Chr(149) & " Middleware — обрабатывают запросы перед их передачей в контроллеры."
        .TypeParagraph
        .TypeParagraph

        .TypeText "Система построена с учетом принципов мультитенантности, что позволяет изолировать данные каждой компании-клиента. Это реализовано через поле company_id в ключевых таблицах базы данных и соответствующие фильтры в запросах."
        .TypeParagraph
        .TypeParagraph
    End With

    ' 2.2 Анализ структуры базы данных
    FormatSubsectionHeader objSelection, "2.2 Анализ структуры базы данных"

    With objSelection
        .TypeText "В рамках практики был проведен анализ структуры базы данных проекта. База данных построена на основе MySQL и включает следующие основные таблицы:"
        .TypeParagraph
        .TypeText Chr(149) & " companies — хранит информацию о компаниях-клиентах системы;"
        .TypeParagraph
        .TypeText Chr(149) & " users — содержит данные пользователей системы с привязкой к компаниям;"
        .TypeParagraph
        .TypeText Chr(149) & " leads — основная таблица для хранения заявок клиентов;"
        .TypeParagraph
        .TypeText Chr(149) & " lead_files — хранит файлы, прикрепленные к заявкам;"
        .TypeParagraph
        .TypeText Chr(149) & " tags — содержит теги для категоризации заявок;"
        .TypeParagraph
        .TypeText Chr(149) & " lead_tag — реализует связь многие-ко-многим между заявками и тегами;"
        .TypeParagraph
        .TypeText Chr(149) & " lead_events — записи о событиях, происходящих с заявками;"
        .TypeParagraph
        .TypeText Chr(149) & " lead_comments — комментарии к заявкам;"
        .TypeParagraph
        .TypeText Chr(149) & " lead_metrics — агрегированные метрики по заявкам;"
        .TypeParagraph
        .TypeText Chr(149) & " lead_analytics — результаты аналитической обработки заявок с помощью ИИ."
        .TypeParagraph
        .TypeParagraph

        .TypeText "База данных спроектирована с учетом требований к производительности и оптимизирована для быстрого доступа к данным через соответствующие индексы. Миграции базы данных управляются средствами Laravel."
        .TypeParagraph
        .TypeParagraph
    End With

    ' 2.3 Изучение интеграционного модуля
    FormatSubsectionHeader objSelection, "2.3 Изучение интеграционного модуля"

    With objSelection
        .TypeText "В рамках практики было проведено изучение интеграционного модуля системы LeadFlow Analytics, который обеспечивает прием заявок через REST API."
        .TypeParagraph
        .TypeParagraph

        .TypeText "Основные компоненты интеграционного модуля:"
        .TypeParagraph
        .TypeText Chr(149) & " Контроллер API (LeadController) — обрабатывает входящие запросы на создание заявок;"
        .TypeParagraph
        .TypeText Chr(149) & " Middleware для проверки API-ключей — обеспечивает безопасность API и идентификацию клиентов;"
        .TypeParagraph
        .TypeText Chr(149) & " Валидаторы — выполняют проверку входящих данных на соответствие требованиям;"
        .TypeParagraph
        .TypeText Chr(149) & " Документация API — реализована с использованием Swagger/OpenAPI."
        .TypeParagraph
        .TypeParagraph

        .TypeText "API поддерживает передачу заявок в формате JSON и включает следующие обязательные поля: source, а также email или phone. Дополнительно могут быть переданы поля name, message и custom_fields для нестандартных данных."
        .TypeParagraph
        .TypeParagraph

        ' Добавляем пример кода контроллера API
        .TypeText "Ниже приведен фрагмент кода контроллера API для обработки заявок:"
        .TypeParagraph
        .Font.Name = "Courier New"
        .Font.Size = 10
        .TypeText "public function store(Request $request)" & vbCrLf
        .TypeText "{" & vbCrLf
        .TypeText "    try {" & vbCrLf
        .TypeText "        // Validate the request" & vbCrLf
        .TypeText "        $validator = Validator::make($request->all(), [" & vbCrLf
        .TypeText "            'source' => 'required|string|max:255'," & vbCrLf
        .TypeText "            'name' => 'nullable|string|max:255'," & vbCrLf
        .TypeText "            'email' => 'nullable|email|required_without:phone'," & vbCrLf
        .TypeText "            'phone' => 'nullable|string|max:20|required_without:email'," & vbCrLf
        .TypeText "            'message' => 'nullable|string'," & vbCrLf
        .TypeText "            'custom_fields' => 'nullable|array'," & vbCrLf
        .TypeText "        ]);" & vbCrLf & vbCrLf

        .TypeText "        if ($validator->fails()) {" & vbCrLf
        .TypeText "            return response()->json([" & vbCrLf
        .TypeText "                'message' => 'Validation failed'," & vbCrLf
        .TypeText "                'errors' => $validator->errors()," & vbCrLf
        .TypeText "                'status' => 'error'" & vbCrLf
        .TypeText "            ], 400);" & vbCrLf
        .TypeText "        }" & vbCrLf & vbCrLf

        .TypeText "        // Get company from request attributes (set by middleware)" & vbCrLf
        .TypeText "        $company = $request->attributes->get('company');" & vbCrLf & vbCrLf

        .TypeText "        // Create lead" & vbCrLf
        .TypeText "        $lead = new Lead();" & vbCrLf
        .TypeText "        $lead->company_id = $company->id;" & vbCrLf
        .TypeText "        $lead->source = $request->input('source');" & vbCrLf
        .TypeText "        $lead->name = $request->input('name');" & vbCrLf
        .TypeText "        $lead->email = $request->input('email');" & vbCrLf
        .TypeText "        $lead->phone = $request->input('phone');" & vbCrLf
        .TypeText "        $lead->message = $request->input('message');" & vbCrLf
        .TypeText "        $lead->custom_fields = $request->input('custom_fields');" & vbCrLf
        .TypeText "        $lead->save();" & vbCrLf & vbCrLf

        .TypeText "        return response()->json([" & vbCrLf
        .TypeText "            'message' => 'Lead created successfully'," & vbCrLf
        .TypeText "            'lead_id' => $lead->id," & vbCrLf
        .TypeText "            'status' => 'success'" & vbCrLf
        .TypeText "        ], 201);" & vbCrLf
        .TypeText "    } catch (\Exception $e) {" & vbCrLf
        .TypeText "        Log::error('Error creating lead: ' . $e->getMessage());" & vbCrLf & vbCrLf

        .TypeText "        return response()->json([" & vbCrLf
        .TypeText "            'message' => 'Error creating lead'," & vbCrLf
        .TypeText "            'status' => 'error'" & vbCrLf
        .TypeText "        ], 500);" & vbCrLf
        .TypeText "    }" & vbCrLf
        .TypeText "}"
        .Font.Name = FONT_NAME
        .Font.Size = MAIN_FONT_SIZE
        .TypeParagraph
        .TypeParagraph

        .TypeText "Данный метод контроллера отвечает за создание новой заявки. Он проверяет входящие данные, создает запись в базе данных и возвращает результат операции в формате JSON. Важной особенностью является проверка наличия как минимум одного из контактных полей (email или phone) и привязка заявки к компании клиента через поле company_id."
        .TypeParagraph
        .TypeParagraph
    End With

    ' 2.4 Анализ модуля ИИ-аналитики
    FormatSubsectionHeader objSelection, "2.4 Анализ модуля ИИ-аналитики"

    With objSelection
        .TypeText "Одним из ключевых компонентов системы LeadFlow Analytics является модуль ИИ-аналитики, который обеспечивает автоматическую обработку заявок с использованием технологий искусственного интеллекта."
        .TypeParagraph
        .TypeParagraph

        .TypeText "Основные компоненты модуля ИИ-аналитики:"
        .TypeParagraph
        .TypeText Chr(149) & " LeadRelevanceAnalyzer — сервис для анализа релевантности заявок с оценкой по шкале от 1 до 10;"
        .TypeParagraph
        .TypeText Chr(149) & " LeadAnalyticsService — сервис для категоризации заявок и генерации суммаризации текста;"
        .TypeParagraph
        .TypeText Chr(149) & " OllamaClient — клиент для взаимодействия с моделями ИИ через Ollama API;"
        .TypeParagraph
        .TypeText Chr(149) & " Система асинхронной обработки — использует Laravel Queues для обработки заявок в фоновом режиме."
        .TypeParagraph
        .TypeParagraph

        .TypeText "Для анализа заявок используются промпты, специально разработанные для определения категории заявки, оценки её релевантности и создания краткого резюме содержания. Результаты анализа сохраняются в таблице lead_analytics и используются для дальнейшей работы с заявками."
        .TypeParagraph
        .TypeParagraph

        ' Добавляем пример промпта для анализа релевантности
        .TypeText "Ниже приведен пример промпта, используемого для анализа релевантности заявок:"
        .TypeParagraph
        .Font.Name = "Courier New"
        .Font.Size = 10

        ' Разбиваем длинный промпт на несколько строк и переменных для решения проблемы "Too many line continuations"
        Dim promptLine1, promptLine2, promptLine3, promptLine4, promptLine5 As String
        Dim promptLine6, promptLine7, promptLine8, promptLine9, promptLine10 As String
        Dim promptLine11, promptLine12, promptLine13, promptLine14, promptLine15 As String
        Dim promptLine16, promptLine17, promptLine18, promptLine19, promptLine20 As String

        ' Блок 1
        promptLine1 = "Оцени релевантность заявки от клиента по шкале от 1 до 10, где:"
        promptLine2 = "1-3: низкая релевантность (спам, нерелевантные запросы)"
        promptLine3 = "4-7: средняя релевантность (общие вопросы, требующие уточнения)"
        promptLine4 = "8: хорошая релевантность (конкретный запрос с базовыми деталями)"
        promptLine5 = "9-10: высокая релевантность (детализированные запросы с явной готовностью к сотрудничеству)"

        ' Блок 2
        promptLine6 = "ДАННЫЕ ЗАЯВКИ:"
        promptLine7 = "Имя клиента: {$name}"
        promptLine8 = "Email: {$email}"
        promptLine9 = "Телефон: {$phone}"
        promptLine10 = "Источник: {$source}"
        promptLine11 = "{$tagsInfo}"
        promptLine12 = "Категория: {$category}"

        ' Блок 3
        promptLine13 = "Сообщение клиента:"
        promptLine14 = """{$message}"""
        promptLine15 = "КРИТЕРИИ ОЦЕНКИ РЕЛЕВАНТНОСТИ:"
        promptLine16 = "1. Конкретность запроса (общие фразы = ниже, конкретные детали = выше)"
        promptLine17 = "2. Полнота контактных данных (больше контактов = выше оценка)"
        promptLine18 = "3. Соответствие тематике бизнеса (насколько запрос соответствует услугам компании)"
        promptLine19 = "4. Наличие признаков реального интереса (а не спама или холодного обращения)"
        promptLine20 = "5. Потенциал для конверсии в продажу или длительное сотрудничество"

        ' Блок 4
        Dim resultLine1, resultLine2, resultLine3 As String
        resultLine1 = "Верни результат только в формате JSON с полями:"
        resultLine2 = "- score: числовая оценка релевантности от 1 до 10 (целое число)"
        resultLine3 = "- explanation: краткое объяснение оценки (1-2 предложения, предпочтительно до 20 слов)"

        ' Выводим промпт построчно
        .TypeText promptLine1 & vbCrLf
        .TypeText promptLine2 & vbCrLf
        .TypeText promptLine3 & vbCrLf
        .TypeText promptLine4 & vbCrLf
        .TypeText promptLine5 & vbCrLf & vbCrLf

        .TypeText promptLine6 & vbCrLf
        .TypeText promptLine7 & vbCrLf
        .TypeText promptLine8 & vbCrLf
        .TypeText promptLine9 & vbCrLf
        .TypeText promptLine10 & vbCrLf
        .TypeText promptLine11 & vbCrLf
        .TypeText promptLine12 & vbCrLf & vbCrLf

        .TypeText promptLine13 & vbCrLf
        .TypeText promptLine14 & vbCrLf & vbCrLf
        .TypeText promptLine15 & vbCrLf
        .TypeText promptLine16 & vbCrLf
        .TypeText promptLine17 & vbCrLf
        .TypeText promptLine18 & vbCrLf
        .TypeText promptLine19 & vbCrLf
        .TypeText promptLine20 & vbCrLf & vbCrLf

        .TypeText resultLine1 & vbCrLf
        .TypeText resultLine2 & vbCrLf
        .TypeText resultLine3

        .Font.Name = FONT_NAME
        .Font.Size = MAIN_FONT_SIZE
        .TypeParagraph
        .TypeParagraph

        ' Добавляем пример кода анализатора релевантности
        .TypeText "Рассмотрим фрагмент кода класса LeadRelevanceAnalyzer, который отвечает за анализ релевантности заявок:"
        .TypeParagraph
        .Font.Name = "Courier New"
        .Font.Size = 10

        ' Выводим код по частям без длинных конструкций с символами продолжения
        .TypeText "public function analyzeLead(Lead $lead, ?string $model = null): ?array" & vbCrLf
        .TypeText "{" & vbCrLf
        .TypeText "    // Кэшируем результаты по ID заявки" & vbCrLf
        .TypeText "    $cacheKey = 'lead_relevance_' . $lead->id;" & vbCrLf & vbCrLf

        .TypeText "    return Cache::remember($cacheKey, $this->cacheMinutes * 60, function () use ($lead, $model) {" & vbCrLf
        .TypeText "        // Проверяем доступность Ollama API" & vbCrLf
        .TypeText "        if (!$this->ollamaClient->isAvailable()) {" & vbCrLf
        .TypeText "            Log::warning('Ollama API недоступен при анализе релевантности заявки', [" & vbCrLf
        .TypeText "                'lead_id' => $lead->id" & vbCrLf
        .TypeText "            ]);" & vbCrLf
        .TypeText "            return null;" & vbCrLf
        .TypeText "        }" & vbCrLf & vbCrLf

        .TypeText "        $prompt = $this->buildPrompt($lead);" & vbCrLf & vbCrLf

        .TypeText "        try {" & vbCrLf
        .TypeText "            $result = $this->ollamaClient->generateJson($prompt, $model);" & vbCrLf & vbCrLf

        .TypeText "            if (!$result || !isset($result['score']) || !isset($result['explanation'])) {" & vbCrLf
        .TypeText "                Log::warning('Не удалось получить оценку релевантности заявки');" & vbCrLf
        .TypeText "                return null;" & vbCrLf
        .TypeText "            }" & vbCrLf & vbCrLf

        .TypeText "            $score = (int)$result['score'];" & vbCrLf
        .TypeText "            $explanation = $result['explanation'];" & vbCrLf & vbCrLf

        .TypeText "            // Проверяем, что оценка находится в диапазоне от 1 до 10" & vbCrLf
        .TypeText "            if ($score < 1 || $score > 10) {" & vbCrLf
        .TypeText "                $score = max(1, min(10, $score)); // Ограничиваем значение диапазоном 1-10" & vbCrLf
        .TypeText "            }" & vbCrLf & vbCrLf

        .TypeText "            return [" & vbCrLf
        .TypeText "                'score' => $score," & vbCrLf
        .TypeText "                'explanation' => $explanation" & vbCrLf
        .TypeText "            ];" & vbCrLf
        .TypeText "        } catch (\Exception $e) {" & vbCrLf
        .TypeText "            Log::error('Ошибка при анализе релевантности заявки: ' . $e->getMessage());" & vbCrLf
        .TypeText "            return null;" & vbCrLf
        .TypeText "        }" & vbCrLf
        .TypeText "    });" & vbCrLf
        .TypeText "}"
        .Font.Name = FONT_NAME
        .Font.Size = MAIN_FONT_SIZE
        .TypeParagraph
        .TypeParagraph

        .TypeText "Данный метод анализирует заявку и возвращает оценку релевантности от 1 до 10 вместе с кратким объяснением этой оценки. Важными особенностями реализации являются кэширование результатов для оптимизации производительности, обработка ошибок и проверка корректности полученных результатов. Метод использует клиент Ollama для взаимодействия с локальными моделями машинного обучения."
        .TypeParagraph
        .TypeParagraph
    End With

    ' 2.5 Изучение дашборда аналитики
    FormatSubsectionHeader objSelection, "2.5 Изучение дашборда аналитики"

    With objSelection
        .TypeText "В ходе практики был изучен дашборд аналитики — один из ключевых компонентов системы LeadFlow Analytics, предоставляющий визуализацию метрик и аналитических данных."
        .TypeParagraph
        .TypeParagraph

        .TypeText "Основные функциональные возможности дашборда аналитики:"
        .TypeParagraph
        .TypeText Chr(149) & " Отображение общего количества заявок за период;"
        .TypeParagraph
        .TypeText Chr(149) & " Визуализация конверсии — доли завершенных заявок;"
        .TypeParagraph
        .TypeText Chr(149) & " Распределение заявок по источникам;"
        .TypeParagraph
        .TypeText Chr(149) & " Отображение средней оценки релевантности заявок;"
        .TypeParagraph
        .TypeText Chr(149) & " Графики динамики поступления заявок с возможностью фильтрации по периоду и источнику;"
        .TypeParagraph
        .TypeText Chr(149) & " Экспорт данных в формате CSV."
        .TypeParagraph
        .TypeParagraph

        .TypeText "Дашборд реализован с использованием Laravel Livewire для динамического обновления данных без перезагрузки страницы и Chart.js для визуализации графиков и диаграмм. Данные для дашборда формируются на основе информации из таблицы lead_metrics, которая содержит агрегированные метрики по различным периодам."
        .TypeParagraph
        .TypeParagraph

        ' Добавляем пример кода получения метрик для дашборда
        .TypeText "Рассмотрим пример кода из контроллера дашборда, который отвечает за получение агрегированных метрик:"
        .TypeParagraph
        .Font.Name = "Courier New"
        .Font.Size = 10

        ' Выводим код по частям без длинных конструкций с символами продолжения
        .TypeText "private function getOrCalculateMetrics($companyId, $periodType, $startDate, $endDate, $source)" & vbCrLf
        .TypeText "{" & vbCrLf
        .TypeText "    // Преобразуем 'all' в null для поиска метрик по всем источникам" & vbCrLf
        .TypeText "    $sourceFilter = $source === 'all' ? null : $source;" & vbCrLf & vbCrLf

        .TypeText "    // Пытаемся найти уже рассчитанные метрики" & vbCrLf
        .TypeText "    $cachedMetric = LeadMetric::forCompany($companyId)" & vbCrLf
        .TypeText "        ->forPeriod($periodType, $startDate->toDateString(), $sourceFilter)" & vbCrLf
        .TypeText "        ->where('calculated_at', '>', now()->subHours(1)) // Не старше 1 часа" & vbCrLf
        .TypeText "        ->first();" & vbCrLf & vbCrLf

        .TypeText "    // Если есть актуальные метрики, используем их" & vbCrLf
        .TypeText "    if ($cachedMetric) {" & vbCrLf
        .TypeText "        return [" & vbCrLf
        .TypeText "            'total_leads' => $cachedMetric->total_leads," & vbCrLf
        .TypeText "            'conversion_rate' => $cachedMetric->conversion_rate," & vbCrLf
        .TypeText "            'avg_relevance_score' => $cachedMetric->avg_relevance_score," & vbCrLf
        .TypeText "            'avg_response_time' => $cachedMetric->avg_response_time," & vbCrLf
        .TypeText "            'source_distribution' => $cachedMetric->source_distribution," & vbCrLf
        .TypeText "        ];" & vbCrLf
        .TypeText "    }" & vbCrLf & vbCrLf

        .TypeText "    // Иначе рассчитываем и сохраняем новые метрики" & vbCrLf
        .TypeText "    $metric = LeadMetric::calculateMetrics(" & vbCrLf
        .TypeText "        $companyId," & vbCrLf
        .TypeText "        $periodType," & vbCrLf
        .TypeText "        $startDate->toDateString()," & vbCrLf
        .TypeText "        $endDate->toDateString()," & vbCrLf
        .TypeText "        $sourceFilter" & vbCrLf
        .TypeText "    );" & vbCrLf & vbCrLf

        .TypeText "    return [" & vbCrLf
        .TypeText "        'total_leads' => $metric->total_leads," & vbCrLf
        .TypeText "        'conversion_rate' => $metric->conversion_rate," & vbCrLf
        .TypeText "        'avg_relevance_score' => $metric->avg_relevance_score," & vbCrLf
        .TypeText "        'avg_response_time' => $metric->avg_response_time," & vbCrLf
        .TypeText "        'source_distribution' => $metric->source_distribution," & vbCrLf
        .TypeText "    ];" & vbCrLf
        .TypeText "}"
        .Font.Name = FONT_NAME
        .Font.Size = MAIN_FONT_SIZE
        .TypeParagraph
        .TypeParagraph

        .TypeText "Данный метод использует систему кэширования метрик для повышения производительности дашборда. Сначала метод пытается найти уже рассчитанные метрики, которые не старше одного часа. Если такие метрики не найдены, выполняется перерасчет и сохранение новых метрик в базе данных. Такой подход позволяет существенно снизить нагрузку на базу данных при интенсивном использовании дашборда несколькими пользователями."
        .TypeParagraph
        .TypeParagraph
    End With

    ' 2.6 Технологии и инструменты, использованные в проекте
    FormatSubsectionHeader objSelection, "2.6 Технологии и инструменты, использованные в проекте"

    With objSelection
        .TypeText "В ходе разработки проекта LeadFlow Analytics используются следующие технологии и инструменты:"
        .TypeParagraph
        .TypeParagraph

        .TypeText "Серверная часть:"
        .TypeParagraph
        .TypeText Chr(149) & " PHP 8+ — основной язык программирования, обеспечивающий высокую производительность и современные возможности, такие как типизация, атрибуты и улучшенная работа с ошибками;"
        .TypeParagraph
        .TypeText Chr(149) & " Laravel 10 — PHP-фреймворк для разработки веб-приложений, предоставляющий элегантный синтаксис и инструменты для задач, которые используются в большинстве веб-проектов, такие как аутентификация, маршрутизация, сессии и кэширование;"
        .TypeParagraph
        .TypeText Chr(149) & " MySQL — реляционная система управления базами данных, обеспечивающая надежное хранение данных и высокую производительность запросов;"
        .TypeParagraph
        .TypeText Chr(149) & " Laravel Queues — система очередей для асинхронной обработки задач, позволяющая отложить выполнение ресурсоемких операций, таких как обработка заявок с помощью ИИ, анализ данных и отправка уведомлений;"
        .TypeParagraph
        .TypeText Chr(149) & " Laravel Sanctum — современная и легковесная система аутентификации API, обеспечивающая безопасный доступ к API через токены и защиту от несанкционированного доступа;"
        .TypeParagraph
        .TypeText Chr(149) & " Laravel Telescope — инструмент для отладки и мониторинга, предоставляющий подробную информацию о запросах, исключениях, SQL-запросах и других аспектах работы приложения."
        .TypeParagraph
        .TypeParagraph

        .TypeText "Клиентская часть:"
        .TypeParagraph
        .TypeText Chr(149) & " Laravel Blade — шаблонизатор для создания представлений, обеспечивающий наследование шаблонов, компоненты и директивы, что упрощает создание многократно используемых элементов интерфейса;"
        .TypeParagraph
        .TypeText Chr(149) & " Livewire — фреймворк для создания динамических компонентов без написания JavaScript-кода, позволяющий создавать интерактивные интерфейсы с сохранением всей бизнес-логики на стороне сервера;"
        .TypeParagraph
        .TypeText Chr(149) & " JavaScript — для расширенных интерактивных элементов интерфейса, обеспечивающих плавные анимации, валидацию форм и асинхронные запросы к серверу;"
        .TypeParagraph
        .TypeText Chr(149) & " Chart.js — библиотека для создания отзывчивых и интерактивных графиков и диаграмм, поддерживающая различные типы визуализации данных: линейные графики, столбчатые и круговые диаграммы;"
        .TypeParagraph
        .TypeText Chr(149) & " Alpine.js — легковесный JavaScript-фреймворк для манипуляций DOM, предоставляющий реактивность и декларативный синтаксис для создания интерактивных компонентов с минимальным объемом кода;"
        .TypeParagraph
        .TypeText Chr(149) & " Tailwind CSS — утилитарный CSS-фреймворк, обеспечивающий низкоуровневые классы для построения уникальных интерфейсов без необходимости писать собственные стили."
        .TypeParagraph
        .TypeParagraph

        .TypeText "Интеграции с внешними сервисами:"
        .TypeParagraph
        .TypeText Chr(149) & " Ollama API — для работы с локальными моделями ИИ, обеспечивающий низкую задержку и конфиденциальность данных при выполнении ИИ-аналитики;"
        .TypeParagraph
        .TypeText Chr(149) & " Telegram Bot API — для отправки уведомлений о новых заявках и изменениях их статуса, что позволяет оперативно реагировать на входящие запросы;"
        .TypeParagraph
        .TypeText Chr(149) & " Bitrix24 REST API — для интеграции с CRM-системой Bitrix24, обеспечивающей двунаправленную синхронизацию данных о заявках и контактах."
        .TypeParagraph
        .TypeParagraph

        .TypeText "Инструменты разработки:"
        .TypeParagraph
        .TypeText Chr(149) & " Git — распределенная система контроля версий, обеспечивающая надежное управление изменениями в кодовой базе и командную работу над проектом;"
        .TypeParagraph
        .TypeText Chr(149) & " Docker — платформа для контейнеризации приложения, обеспечивающая идентичное окружение разработки и продакшена, а также упрощающая развертывание и масштабирование;"
        .TypeParagraph
        .TypeText Chr(149) & " Composer — менеджер зависимостей для PHP, автоматизирующий установку и обновление библиотек и компонентов, используемых в проекте;"
        .TypeParagraph
        .TypeText Chr(149) & " npm — менеджер пакетов для JavaScript, управляющий фронтенд-зависимостями и скриптами сборки;"
        .TypeParagraph
        .TypeText Chr(149) & " PHPUnit — фреймворк для модульного тестирования PHP-кода, обеспечивающий надежность и стабильность приложения."
        .TypeParagraph
        .TypeParagraph

        .TypeText "Использование современных технологий и инструментов в проекте LeadFlow Analytics обеспечивает его высокую производительность, масштабируемость, безопасность и удобство разработки. Выбор Laravel в качестве основного фреймворка обусловлен его зрелостью, обширной экосистемой и активным сообществом разработчиков, что гарантирует долгосрочную поддержку и развитие проекта."
        .TypeParagraph
        .TypeParagraph
    End With

    ' 2.7 Анализ моделей данных и их взаимосвязей
    FormatSubsectionHeader objSelection, "2.7 Анализ моделей данных и их взаимосвязей"

    With objSelection
        .TypeText "В рамках практики был проведен детальный анализ моделей данных проекта LeadFlow Analytics и их взаимосвязей. Система построена на основе реляционной модели данных с использованием ORM (Object-Relational Mapping) фреймворка Laravel Eloquent."
        .TypeParagraph
        .TypeParagraph

        .TypeText "Основные модели данных и их взаимосвязи:"
        .TypeParagraph
        .TypeParagraph

        ' Примеры классов с измененным форматом вывода кода
        .Font.Size = 10

        ' 1. Класс Company
        .TypeText "class Company extends Model" & vbCrLf
        .TypeText "{" & vbCrLf
        .TypeText "    // Отношения" & vbCrLf
        .TypeText "    public function users() { return $this->hasMany(User::class); }" & vbCrLf
        .TypeText "    public function leads() { return $this->hasMany(Lead::class); }" & vbCrLf
        .TypeText "    public function tags() { return $this->hasMany(Tag::class); }" & vbCrLf
        .TypeText "    // ..."
        .Font.Name = FONT_NAME
        .Font.Size = MAIN_FONT_SIZE
        .TypeParagraph
        .TypeParagraph

        .TypeText "2. User — пользователь системы (менеджер, администратор):"
        .TypeParagraph
        .Font.Name = "Courier New"
        .Font.Size = 10

        ' 2. Класс User
        .TypeText "class User extends Authenticatable" & vbCrLf
        .TypeText "{" & vbCrLf
        .TypeText "    // Отношения" & vbCrLf
        .TypeText "    public function company() { return $this->belongsTo(Company::class); }" & vbCrLf
        .TypeText "    // ..."
        .Font.Name = FONT_NAME
        .Font.Size = MAIN_FONT_SIZE
        .TypeParagraph
        .TypeParagraph

        .TypeText "3. Lead — основная сущность, представляющая клиентскую заявку:"
        .TypeParagraph
        .Font.Name = "Courier New"
        .Font.Size = 10

        ' 3. Класс Lead
        .TypeText "class Lead extends Model" & vbCrLf
        .TypeText "{" & vbCrLf
        .TypeText "    // Атрибуты, доступные для массового заполнения" & vbCrLf
        .TypeText "    protected $fillable = [" & vbCrLf
        .TypeText "        'company_id', 'source', 'name', 'email', 'phone', 'message'," & vbCrLf
        .TypeText "        'custom_fields', 'status', 'category', 'summary'," & vbCrLf
        .TypeText "        'generated_response', 'relevance_score'," & vbCrLf
        .TypeText "        // ..." & vbCrLf
        .TypeText "    ];" & vbCrLf & vbCrLf

        .TypeText "    // Отношения" & vbCrLf
        .TypeText "    public function company() { return $this->belongsTo(Company::class); }" & vbCrLf
        .TypeText "    public function tags() { return $this->belongsToMany(Tag::class); }" & vbCrLf
        .TypeText "    public function files() { return $this->hasMany(LeadFile::class); }" & vbCrLf
        .TypeText "    public function comments() { return $this->hasMany(LeadComment::class); }" & vbCrLf
        .TypeText "    public function events() { return $this->hasMany(LeadEvent::class); }" & vbCrLf
        .TypeText "    public function analytics() { return $this->hasOne(LeadAnalytics::class); }" & vbCrLf
        .TypeText "    // ..."
        .Font.Name = FONT_NAME
        .Font.Size = MAIN_FONT_SIZE
        .TypeParagraph
        .TypeParagraph

        .TypeText "4. Tag — тег для категоризации заявок:"
        .TypeParagraph
        .Font.Name = "Courier New"
        .Font.Size = 10

        ' 4. Класс Tag
        .TypeText "class Tag extends Model" & vbCrLf
        .TypeText "{" & vbCrLf
        .TypeText "    // Отношения" & vbCrLf
        .TypeText "    public function company() { return $this->belongsTo(Company::class); }" & vbCrLf
        .TypeText "    public function leads() { return $this->belongsToMany(Lead::class); }" & vbCrLf
        .TypeText "}"
        .Font.Name = FONT_NAME
        .Font.Size = MAIN_FONT_SIZE
        .TypeParagraph
        .TypeParagraph

        .TypeText "5. LeadFile — файл, прикрепленный к заявке:"
        .TypeParagraph
        .Font.Name = "Courier New"
        .Font.Size = 10

        ' 5. Класс LeadFile
        .TypeText "class LeadFile extends Model" & vbCrLf
        .TypeText "{" & vbCrLf
        .TypeText "    // Отношения" & vbCrLf
        .TypeText "    public function lead() { return $this->belongsTo(Lead::class); }" & vbCrLf
        .TypeText "}"
        .Font.Name = FONT_NAME
        .Font.Size = MAIN_FONT_SIZE
        .TypeParagraph
        .TypeParagraph

        .TypeText "6. LeadComment — комментарий к заявке:"
        .TypeParagraph
        .Font.Name = "Courier New"
        .Font.Size = 10

        ' 6. Класс LeadComment
        .TypeText "class LeadComment extends Model" & vbCrLf
        .TypeText "{" & vbCrLf
        .TypeText "    // Отношения" & vbCrLf
        .TypeText "    public function lead() { return $this->belongsTo(Lead::class); }" & vbCrLf
        .TypeText "    public function user() { return $this->belongsTo(User::class); }" & vbCrLf
        .TypeText "}"
        .Font.Name = FONT_NAME
        .Font.Size = MAIN_FONT_SIZE
        .TypeParagraph
        .TypeParagraph

        .TypeText "7. LeadEvent — событие, связанное с заявкой (изменение статуса и т.д.):"
        .TypeParagraph
        .Font.Name = "Courier New"
        .Font.Size = 10

        ' 7. Класс LeadEvent
        .TypeText "class LeadEvent extends Model" & vbCrLf
        .TypeText "{" & vbCrLf
        .TypeText "    // Отношения" & vbCrLf
        .TypeText "    public function lead() { return $this->belongsTo(Lead::class); }" & vbCrLf
        .TypeText "    public function user() { return $this->belongsTo(User::class); }" & vbCrLf
        .TypeText "}"
        .Font.Name = FONT_NAME
        .Font.Size = MAIN_FONT_SIZE
        .TypeParagraph
        .TypeParagraph

        .TypeText "8. LeadAnalytics — результаты ИИ-аналитики заявки:"
        .TypeParagraph
        .Font.Name = "Courier New"
        .Font.Size = 10

        ' 8. Класс LeadAnalytics
        .TypeText "class LeadAnalytics extends Model" & vbCrLf
        .TypeText "{" & vbCrLf
        .TypeText "    // Константы статусов обработки" & vbCrLf
        .TypeText "    const STATUS_PENDING = 'pending';" & vbCrLf
        .TypeText "    const STATUS_PROCESSING = 'processing';" & vbCrLf
        .TypeText "    const STATUS_COMPLETED = 'completed';" & vbCrLf
        .TypeText "    const STATUS_FAILED = 'failed';" & vbCrLf & vbCrLf

        .TypeText "    // Отношения" & vbCrLf
        .TypeText "    public function lead() { return $this->belongsTo(Lead::class); }" & vbCrLf
        .TypeText "}"
        .Font.Name = FONT_NAME
        .Font.Size = MAIN_FONT_SIZE
        .TypeParagraph
        .TypeParagraph

        .TypeText "9. LeadMetric — агрегированные метрики по заявкам:"
        .TypeParagraph
        .Font.Name = "Courier New"
        .Font.Size = 10

        ' 9. Класс LeadMetric
        .TypeText "class LeadMetric extends Model" & vbCrLf
        .TypeText "{" & vbCrLf
        .TypeText "    // Отношения" & vbCrLf
        .TypeText "    public function company() { return $this->belongsTo(Company::class); }" & vbCrLf & vbCrLf

        .TypeText "    // Метод для расчета метрик" & vbCrLf
        .TypeText "    public static function calculateMetrics($companyId, $periodType, $periodStart, $periodEnd, $source = null)" & vbCrLf
        .TypeText "    {" & vbCrLf
        .TypeText "        // Логика расчета метрик..." & vbCrLf
        .TypeText "    }" & vbCrLf
        .TypeText "}"
        .Font.Name = FONT_NAME
        .Font.Size = MAIN_FONT_SIZE
        .TypeParagraph
        .TypeParagraph

        .TypeText "Анализ моделей данных позволяет выделить следующие особенности архитектуры системы:"
        .TypeParagraph
        .TypeParagraph

        .TypeText Chr(149) & " Мультитенантность — все основные сущности (Lead, Tag, User) привязаны к компании через поле company_id, что обеспечивает изоляцию данных разных клиентов системы;"
        .TypeParagraph
        .TypeText Chr(149) & " Детальное логирование — система отслеживает все события, связанные с заявками, через модель LeadEvent, что позволяет контролировать историю изменений и действий пользователей;"
        .TypeParagraph
        .TypeText Chr(149) & " Расширяемость — модель Lead содержит поле custom_fields для хранения нестандартных данных в формате JSON, что позволяет адаптировать систему под требования конкретного клиента без изменения схемы базы данных;"
        .TypeParagraph
        .TypeText Chr(149) & " Аналитика — система накапливает и агрегирует метрики по заявкам через модель LeadMetric, что обеспечивает быстрый доступ к статистическим данным без необходимости выполнять тяжелые запросы к основной таблице заявок;"
        .TypeParagraph
        .TypeText Chr(149) & " Интеграция с ИИ — модель LeadAnalytics хранит результаты обработки заявок искусственным интеллектом, включая категоризацию, оценку релевантности и суммаризацию текста."
        .TypeParagraph
        .TypeParagraph

        .TypeText "Такая организация моделей данных обеспечивает гибкость, масштабируемость и производительность системы, а также позволяет эффективно реализовать все необходимые бизнес-процессы, связанные с обработкой и анализом клиентских заявок."
        .TypeParagraph
        .TypeParagraph
    End With
End Sub

' Процедура создания заключения
Sub CreateConclusion(objDoc As Object, objSelection As Object)
    ' Заголовок раздела
    FormatSectionHeader objSelection, "ЗАКЛЮЧЕНИЕ"

    With objSelection
        .TypeText "В ходе прохождения производственной практики был проведен анализ проекта LeadFlow Analytics — комплексной системы для сбора, анализа и автоматизации обработки клиентских заявок. Практика позволила ознакомиться с современными подходами к разработке веб-приложений и использованию технологий искусственного интеллекта для автоматизации бизнес-процессов."
        .TypeParagraph
        .TypeParagraph

        .TypeText "Основные результаты практики:"
        .TypeParagraph
        .TypeText Chr(149) & " Изучена архитектура веб-приложения на основе фреймворка Laravel и паттерна MVC, что позволило понять принципы организации кода в современных веб-приложениях и преимущества этого архитектурного подхода;"
        .TypeParagraph
        .TypeText Chr(149) & " Проанализирована структура базы данных проекта и принципы организации данных, включая подходы к мультитенантности и изоляции данных разных клиентов системы;"
        .TypeParagraph
        .TypeText Chr(149) & " Рассмотрены подходы к интеграции с внешними системами через REST API, что является важным аспектом современной веб-разработки в условиях построения распределенных и микросервисных архитектур;"
        .TypeParagraph
        .TypeText Chr(149) & " Изучены методы применения технологий ИИ для автоматизации обработки текстовых данных, что является одним из наиболее перспективных направлений в области цифровой трансформации бизнеса;"
        .TypeParagraph
        .TypeText Chr(149) & " Исследованы подходы к визуализации данных и построению интерактивных дашбордов с использованием современных JavaScript-библиотек;"
        .TypeParagraph
        .TypeText Chr(149) & " Получены навыки работы с современными инструментами разработки веб-приложений, включая системы контроля версий, менеджеры зависимостей и инструменты для контейнеризации."
        .TypeParagraph
        .TypeParagraph

        .TypeText "Практическая значимость проекта LeadFlow Analytics заключается в следующем:"
        .TypeParagraph
        .TypeText Chr(149) & " Автоматизация процесса обработки клиентских заявок позволяет существенно снизить трудозатраты менеджеров и повысить эффективность их работы;"
        .TypeParagraph
        .TypeText Chr(149) & " Интеграция искусственного интеллекта для категоризации заявок и оценки их релевантности позволяет приоритизировать обработку и сосредоточиться на наиболее перспективных клиентах;"
        .TypeParagraph
        .TypeText Chr(149) & " Аналитический дашборд предоставляет руководству компании инструменты для анализа эффективности работы с клиентами и принятия обоснованных управленческих решений;"
        .TypeParagraph
        .TypeText Chr(149) & " Интеграция с внешними CRM-системами обеспечивает целостность данных и единое информационное пространство для всех сотрудников компании."
        .TypeParagraph
        .TypeParagraph

        .TypeText "Проект LeadFlow Analytics демонстрирует современный подход к разработке программного обеспечения, основанный на следующих принципах:"
        .TypeParagraph
        .TypeText Chr(149) & " Итеративная разработка с фокусом на MVP — создание минимально жизнеспособного продукта с ключевым функционалом, что позволяет быстро выйти на рынок и получить обратную связь от пользователей;"
        .TypeParagraph
        .TypeText Chr(149) & " Модульная архитектура — система разбита на отдельные компоненты (интеграционный модуль, модуль ИИ-аналитики, мини-CRM, дашборд), что упрощает разработку, тестирование и сопровождение;"
        .TypeParagraph
        .TypeText Chr(149) & " Ориентация на облачные технологии — система спроектирована с учетом развертывания в облачной инфраструктуре, что обеспечивает масштабируемость и отказоустойчивость;"
        .TypeParagraph
        .TypeText Chr(149) & " Интеграция технологий искусственного интеллекта — использование современных моделей обработки естественного языка для автоматизации рутинных операций;"
        .TypeParagraph
        .TypeText Chr(149) & " Акцент на безопасность и изоляцию данных — система реализует принципы мультитенантности и контроля доступа к данным."
        .TypeParagraph
        .TypeParagraph

        .TypeText "Практика способствовала закреплению теоретических знаний, полученных в ходе обучения, и приобретению практических навыков в области веб-разработки. Особенно ценным было знакомство с методами интеграции искусственного интеллекта в бизнес-процессы, что является одним из наиболее востребованных направлений в современной разработке ПО."
        .TypeParagraph
        .TypeParagraph

        .TypeText "Полученные в ходе практики знания и навыки будут полезны в дальнейшей профессиональной деятельности и позволят более эффективно применять современные технологии для решения практических задач в области разработки программного обеспечения."
        .TypeParagraph
        .TypeParagraph

        .TypeText "Подводя итог, можно отметить, что проект LeadFlow Analytics представляет собой пример успешного применения современных технологий для решения реальных бизнес-задач. Производственная практика позволила глубоко погрузиться в процесс разработки и получить ценный опыт, который будет востребован на современном рынке труда в сфере информационных технологий."
        .TypeParagraph
    End With
End Sub

' Процедура создания списка использованных источников
Sub CreateReferences(objDoc As Object, objSelection As Object)
    ' Заголовок раздела
    FormatSectionHeader objSelection, "СПИСОК ИСПОЛЬЗОВАННЫХ ИСТОЧНИКОВ"

    With objSelection
        .TypeText "1. Документация PHP [Электронный ресурс]. — Режим доступа: https://www.php.net/docs.php"
        .TypeParagraph
        .TypeText "2. Документация Laravel [Электронный ресурс]. — Режим доступа: https://laravel.com/docs"
        .TypeParagraph
        .TypeText "3. Документация MySQL [Электронный ресурс]. — Режим доступа: https://dev.mysql.com/doc/"
        .TypeParagraph
        .TypeText "4. Документация Livewire [Электронный ресурс]. — Режим доступа: https://laravel-livewire.com/docs"
        .TypeParagraph
        .TypeText "5. Документация Chart.js [Электронный ресурс]. — Режим доступа: https://www.chartjs.org/docs/"
        .TypeParagraph
        .TypeText "6. Документация Ollama API [Электронный ресурс]. — Режим доступа: https://ollama.ai/api-reference"
        .TypeParagraph
        .TypeText "7. Документация Bitrix24 REST API [Электронный ресурс]. — Режим доступа: https://dev.1c-bitrix.ru/rest_help/"
        .TypeParagraph
        .TypeText "8. Мэтт Стоб. Laravel: Полное руководство. — М.: Альфа-книга, 2021. — 536 с."
        .TypeParagraph
        .TypeText "9. Тейлор Отвелл. Laravel: Up & Running, 2nd Edition. — O'Reilly Media, 2020. — 558 с."
        .TypeParagraph
        .TypeText "10. ГОСТ 7.32-2017. Система стандартов по информации, библиотечному и издательскому делу. Отчет о научно-исследовательской работе. Структура и правила оформления [Электронный ресурс]. — Режим доступа: https://docs.cntd.ru/document/1200157208"
        .TypeParagraph
    End With
End Sub

' Вспомогательная процедура для форматирования заголовков разделов
Sub FormatSectionHeader(objSelection As Object, headerText As String)
    With objSelection
        .Font.Size = HEADER_FONT_SIZE
        .Font.Bold = True
        .ParagraphFormat.Alignment = 1 ' wdAlignParagraphCenter
        .TypeText headerText
        .TypeParagraph
        .TypeParagraph
        .Font.Size = MAIN_FONT_SIZE
        .Font.Bold = False
        .ParagraphFormat.Alignment = 0 ' wdAlignParagraphLeft
    End With
End Sub

' Вспомогательная процедура для форматирования заголовков подразделов
Sub FormatSubsectionHeader(objSelection As Object, headerText As String)
    With objSelection
        .Font.Size = MAIN_FONT_SIZE
        .Font.Bold = True
        .ParagraphFormat.Alignment = 0 ' wdAlignParagraphLeft
        .TypeText headerText
        .TypeParagraph
        .Font.Bold = False
    End With
End Sub

' Вспомогательная процедура для вставки разрыва страницы
Sub InsertPageBreak(objSelection As Object)
    objSelection.InsertBreak 7 ' wdPageBreak
End Sub
