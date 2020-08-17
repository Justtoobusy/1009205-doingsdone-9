<section class="content__side">
    <h2 class="content__side-heading">Проекты</h2>

    <nav class="main-navigation">
        <ul class="main-navigation__list">
            <?php foreach ($projects as $project) { ?>
                <li class="main-navigation__list-item <?=$project['id'] === (int)$category_id ? 'main-navigation__list-item--active' : ''; ?>
">
                    <a class="main-navigation__list-item-link"
                       href="?category_id=<?= htmlspecialchars($project['id']); ?>"><?= htmlspecialchars($project['title']) ?> </a>
                    <span class="main-navigation__list-item-count">
                                <?php echo htmlspecialchars($project['task_count']); ?>
                            </span>
                </li>

            <?php } ?>
        </ul>
    </nav>

    <a class="button button--transparent button--plus content__side-button"
       href="project.php" target="project_add">Добавить проект</a>
</section>
<main class="content__main">
    <h2 class="content__main-heading">Список задач</h2>

    <form class="search-form" action="index.php" method="get" autocomplete="off">
        <?php $search = isset($_GET['task_search']) ? $_GET['task_search'] : '';
        ?>
        <input class="search-form__input" type="text" name="task_search" value="<?= $search; ?>"
               placeholder="Поиск по задачам">

        <input class="search-form__submit" type="submit" name="" value="Искать">
    </form>

    <div class="tasks-controls">
        <nav class="tasks-switch">
            <a href="/"
               class="tasks-switch__item <?= (!isset($_GET['filter'])) ? 'tasks-switch__item--active' : ''; ?>">Все
                задачи</a>
            <a href="/?filter=today"
               class="tasks-switch__item <?= (isset ($_GET['filter']) && $_GET['filter'] === 'today') ? 'tasks-switch__item--active' : ''; ?>">Повестка
                дня</a>
            <a href="/?filter=tomorrow"
               class="tasks-switch__item <?= (isset ($_GET['filter']) && $_GET['filter'] === 'tomorrow') ? 'tasks-switch__item--active' : ''; ?>">Завтра</a>
            <a href="/?filter=overdue"
               class="tasks-switch__item <?= (isset ($_GET['filter']) && $_GET['filter'] === 'overdue') ? 'tasks-switch__item--active' : ''; ?>">Просроченные</a>
        </nav>
        <label class="checkbox">
            <input
                class="checkbox__input visually-hidden show_completed" <?= (int)$show_completed_tasks === 1 ? 'checked' : ''; ?>
                type="checkbox"
            >
            <span class="checkbox__text ">Показывать выполненные</span>
        </label>
    </div>

    <table class="tasks">
        <?php foreach ($tasks as $task) { ?>
            <?php if ($task['is_done'] === 0 || (int)$show_completed_tasks === 1) { ?>

                <tr class="tasks__item task <?= ($task['is_done'] === 1) ? 'task--completed' : ''; ?>
                        <?= is_important($task['deadline']) ? 'task--important' : ''; ?>"
                >
                    <td class="task__select">
                        <label class="checkbox task__checkbox">
                            <input class="checkbox__input visually-hidden task__checkbox" type="checkbox"
                                   value="<?= htmlspecialchars($task['id'])?>" <?= ($task['is_done']) ? 'checked' : ''; ?>>
                            <span class="checkbox__text"><?= htmlspecialchars($task['title']); ?></span>
                        </label>
                    </td>

                    <td class="task__file">
                        <?php if ($task['file_attachement'] !== '') { ?>
                            <a class="download-link" target="_blank"
                               href="/uploads/<?= htmlspecialchars($task['file_attachement']); ?>"><?= htmlspecialchars($task['file_attachement']); ?></a>
                        <?php } ?>
                    </td>

                    <td class="task__date"><?= htmlspecialchars($task['deadline']); ?> </td>
                </tr>
            <?php } ?>
        <?php } ?>
        <?php if (empty($tasks) && !empty($_GET['task_search'])) { ?>
            <tr>
                <a>Ничего не найдено по вашему запросу</a>
            </tr>
        <?php } ?>
    </table>
</main>
